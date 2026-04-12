<?php

namespace App\Providers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\MemberProfile;
use App\Models\User;
use App\Policies\AdminPolicy;
use App\Policies\BookPolicy;
use App\Policies\LoanPolicy;
use App\Policies\MemberProfilePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Book::class => BookPolicy::class,
        Loan::class => LoanPolicy::class,
        MemberProfile::class => MemberProfilePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Gates for admin operations
        Gate::define('manage-admins', [AdminPolicy::class, 'manageAdmins']);
        Gate::define('manage-roles', [AdminPolicy::class, 'manageRoles']);
        Gate::define('approve-registration', [AdminPolicy::class, 'approveRegistration']);
        Gate::define('manage-loans', [AdminPolicy::class, 'manageLoans']);
        Gate::define('manage-master-data', [AdminPolicy::class, 'manageMasterData']);

        // Gate untuk cek apakah user adalah admin
        Gate::define('is-admin', function (User $user) {
            return $user->isAdmin();
        });

        // Gate untuk cek apakah user adalah member
        Gate::define('is-member', function (User $user) {
            return $user->isMember();
        });

        // Gate untuk cek apakah user adalah superadmin
        Gate::define('is-superadmin', function (User $user) {
            return $user->isSuperAdmin();
        });
    }
}
