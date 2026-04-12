<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\User;

class BookPolicy
{
    /**
     * Semua user bisa view books (public)
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Semua user bisa view book detail (public)
     */
    public function view(?User $user, Book $book): bool
    {
        return true;
    }

    /**
     * Hanya admin yang bisa create book
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Hanya admin yang bisa update book
     */
    public function update(User $user, Book $book): bool
    {
        return $user->isAdmin();
    }

    /**
     * Hanya admin yang bisa delete book
     */
    public function delete(User $user, Book $book): bool
    {
        return $user->isAdmin();
    }
}
