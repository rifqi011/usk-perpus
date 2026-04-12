# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Planned
- Laravel Breeze integration
- Custom member authentication views
- Public catalog & book detail pages
- Member dashboard & profile
- Filament admin panel resources
- Dashboard widgets & statistics
- Email notifications
- Report generation
- Feature & unit tests

## [0.7.0] - 2026-04-10

### Added - Foundation Complete (70%)

#### Database Layer
- 18 database migrations with proper foreign keys and indexes
- 4 seeders (Role, SuperAdmin, LoanRule, Database)
- Soft delete strategy for master data
- Immutable transaction tables

#### Domain Layer
- 18 Eloquent models with complete relationships
- 11 PHP 8.1+ backed enums with label() and color() methods
- 4 authorization policies (Admin, MemberProfile, Loan, Book)
- 2 custom middleware (EnsureUserIsAdmin, EnsureUserIsMember)
- 5 authorization gates
- AuthServiceProvider configuration

#### Application Layer
- 6 single-purpose action classes:
  - ApproveRegistrationAction
  - RejectRegistrationAction
  - CreateLoanAction
  - ProcessReturnAction
  - CalculateFineAction
  - CreateReservationAction
- 3 orchestration service classes:
  - LoanService
  - FineService
  - ReservationService
- Helper functions file with 10+ utility functions

#### Documentation
- ARCHITECTURE.md - Complete architecture documentation
- README.md - Main project documentation
- PROGRESS.md - Development progress tracking
- IMPLEMENTATION_GUIDE.md - Detailed implementation guide
- PROJECT_SUMMARY.md - Project summary and statistics
- CHANGELOG.md - This file
- INSTALL.sh - Installation script

#### Configuration
- .env.example with library-specific settings
- bootstrap/app.php with middleware aliases
- bootstrap/providers.php with service providers
- composer.json with autoload configuration

### Technical Details

#### Models Created
1. User - Main user account (admin & member)
2. Role - Admin roles (superadmin, admin)
3. AdminProfile - Admin profile data
4. MemberProfile - Member profile data
5. Registration - Member registration requests
6. LoanRule - Loan and fine rules
7. Category - Book categories
8. Author - Book authors
9. Publisher - Book publishers
10. Shelf - Book shelves/locations
11. Book - Book master data
12. BookCopy - Physical book copies
13. Loan - Loan transaction header
14. LoanDetail - Loan transaction details
15. Fine - Fine/penalty records
16. Reservation - Book reservations

#### Enums Created
1. AccountType - admin, member
2. ActiveStatus - active, inactive
3. MembershipStatus - pending, active, suspended, inactive
4. RegistrationStatus - pending, approved, rejected
5. LoanStatus - borrowed, returned, partially_returned, overdue, lost
6. CopyStatus - available, borrowed, reserved, maintenance, lost, discarded
7. BookCondition - new, good, fair, minor_damage, major_damage, lost
8. FineType - late_return, minor_damage, major_damage, lost_book, other
9. FineStatus - unpaid, paid, waived
10. ReservationStatus - waiting, ready, cancelled, fulfilled, expired
11. Gender - L (Laki-laki), P (Perempuan)

#### Business Logic Implemented
- Complete loan creation with validation
- Complete return processing with fine calculation
- Automatic fine generation based on rules
- Member registration approval workflow
- Book reservation system
- Stock management automation

### Database Schema
- Total tables: 18
- Tables with soft delete: 13
- Tables without soft delete: 5 (transactions & pivots)
- Foreign key constraints: 25+
- Indexes: 40+

### Code Statistics
- Total files created: 70+
- Total lines of code: ~5,000+
- PHP files: 60+
- Documentation files: 6
- Configuration files: 4

### Architecture Highlights
- Clean Architecture principles
- Service-Action pattern for business logic
- Policy-based authorization
- Enum-based status management
- Immutable transaction pattern
- Comprehensive audit trail

## [0.1.0] - 2026-04-10

### Added
- Initial project setup
- Laravel 12 installation
- Basic project structure

---

## Version History

- **0.7.0** - Foundation Complete (70%)
- **0.1.0** - Initial Setup

## Next Milestone: 1.0.0 (MVP)

### Required for 1.0.0
- [ ] Complete authentication system
- [ ] Public/member pages
- [ ] Filament admin panel
- [ ] All CRUD operations
- [ ] Basic reporting
- [ ] Feature tests
- [ ] Production deployment

### Estimated Timeline
- Week 1-2: Authentication & Public Pages
- Week 3-4: Filament Admin Panel
- Week 5: Testing & Bug Fixes
- Week 6: Production Deployment

---

**Maintained by**: Development Team
**Last Updated**: 2026-04-10
