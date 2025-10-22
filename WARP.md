# WARP.md

This file provides guidance to WARP (warp.dev) when working with code in this repository.

## Project Overview

Department of Agriculture (DOA) Inventory System - A Laravel 12 + Filament 3.3 application for managing inventory, purchase requests, and multi-role access control with Spatie Permissions and Filament Shield.

## Development Commands

### Setup & Installation
```powershell
# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed
```

### Development Server
```powershell
# Run all services (server, queue, vite) concurrently
composer dev

# Or run individually:
php artisan serve
php artisan queue:listen --tries=1
npm run dev
```

### Building Assets
```powershell
# Development build
npm run dev

# Production build
npm run build
```

### Testing
```powershell
# Run all tests
composer test

# Or directly:
php artisan test

# Run specific test suite
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature
```

### Code Quality
```powershell
# Format code with Laravel Pint
./vendor/bin/pint

# Specific files
./vendor/bin/pint path/to/file.php
```

### Filament Commands
```powershell
# Generate Shield permissions for a resource
php artisan shield:generate --resource=ResourceName

# Upgrade Filament (runs automatically after composer update)
php artisan filament:upgrade

# Clear Filament cache
php artisan filament:clear-cached-components
```

### Database Management
```powershell
# Fresh migration with seeding
php artisan migrate:fresh --seed

# Run specific seeder
php artisan db:seed --class=RolePermissionSeeder
```

## Architecture Overview

### Tech Stack
- **Backend**: Laravel 12 (PHP 8.3+)
- **Admin Panel**: Filament 3.3
- **Frontend**: Livewire, Volt, Flux
- **Authorization**: Spatie Laravel Permission + Filament Shield
- **Activity Logging**: Spatie Activity Log with rmsramos/activitylog Filament integration
- **Assets**: Vite + TailwindCSS 4.x

### Application Structure

#### Filament Resources (`app/Filament/Resources/`)
All admin panel CRUD interfaces are Filament resources:
- `InventoryItemResource` - Main inventory management
- `PurchaseRequestResource` - Purchase request forms with repeater items
- `CategoryResource`, `UnitResource`, `SupplierResource` - Master data
- `StockMovementResource` - Track inventory movements
- `FarmResource`, `ItemRequestResource` - Farm/farmer management

Each resource follows Filament conventions with nested Pages and RelationManagers directories.

#### Models (`app/Models/`)
Key models use traits for soft deletes and activity logging:

**InventoryItem**
- Tracks stock levels with low stock alerts (auto-notifies super_admins)
- Relationships: `category`, `supplier`, `unit`, `purchaseHistory`, `farms` (many-to-many)
- Methods: `isLowStock()`, `isExpired()`, `updateAverageUnitCost()`
- Attributes: computed properties for formatted costs and total values

**PurchaseRequest**
- Status workflow: draft → pending → approved → completed/rejected
- Has many `PurchaseRequestItem` with inventory item linking
- `approved_by` field is JSON array (KeyValue in Filament)

#### Role-Based Access Control
Three primary roles (defined in `RolePermissionSeeder`):
1. **super_admin** - Full access to all resources and user management
2. **doa_staff** - Can manage inventory and purchase requests (no delete)
3. **farmer** - View-only access to inventory and purchase requests

Permissions use Filament Shield conventions: `view_any_inventory::item`, `create_purchase::request`, etc.

Policies in `app/Policies/` enforce authorization for each resource.

#### Custom Filament Actions (`app/Filament/Actions/`)
- `ImportInventoryAction` - Imports inventory from JSON structure (purchase_request format)
- `ProcessPurchaseRequestAction` - Processes purchase request approval workflow

#### Routes
- `/admin` - Filament admin panel (requires authentication)
- `routes/api.php` - Sanctum API (currently minimal, planned for mobile app)
- `routes/farmer.php` - Farmer-specific Livewire routes for item requests
- Admin panel auth handled by Filament middleware

#### Database Schema
Core tables:
- `inventory_items` - Stock tracking with supplier, category, unit relationships
- `purchase_requests` + `purchase_request_items` - Purchase request forms
- `categories`, `units`, `suppliers` - Master data
- `stock_movements` - Audit trail for inventory changes
- `farms` + `farm_inventory_visibility` - Multi-tenancy for farmer access
- Spatie permission tables: `roles`, `permissions`, `model_has_roles`, etc.

#### Seeders (`database/seeders/`)
Run in order (defined in `DatabaseSeeder`):
1. `RolePermissionSeeder` - Creates roles, permissions, default users
2. `CategorySeeder`, `UnitSeeder`, `SupplierSeeder` - Master data
3. `InventoryItemSeeder` - Sample inventory
4. `ComprehensiveSeeder` - Additional test data

#### Widgets (`app/Filament/Widgets/`)
Dashboard widgets for inventory overview and trends charts. Registered in `AdminPanelProvider`.

### Key Design Patterns

**Filament Resource Pattern**: Each resource has `form()` and `table()` methods defining the UI. Forms use Section and Grid components for layout. Tables use filters, actions, and bulk actions.

**Relationships in Forms**: Use `->relationship()` with `->createOptionForm()` for inline creation. Purchase request items have live state management to auto-fill fields from selected inventory items.

**Activity Logging**: Models implement `LogsActivity` trait with `getActivitylogOptions()` defining logged fields. Accessible via `ActivityLogTimelineTableAction` in Filament tables.

**Stock Alerts**: InventoryItem model has `booted()` hook that sends database notifications to super_admins when stock falls below minimum.

**Soft Deletes**: Most models use `SoftDeletes` trait for data preservation.

### Configuration Notes

- Filament panel configured in `app/Providers/Filament/AdminPanelProvider.php`
- Uses database notifications (`->databaseNotifications()`)
- Primary color set to Amber
- Plugins: FilamentShield (RBAC), ActivitylogPlugin (audit logs)

### Testing

PHPUnit configured with in-memory SQLite for tests. Test environment uses array cache/session and disables features like Telescope/Pulse.

## Important Conventions

- Filament resources should use Shield permissions (`php artisan shield:generate`)
- All database changes should include corresponding policy updates
- Activity logging should be configured for sensitive models
- Use Filament's built-in form components for consistency
- Follow Laravel PSR-2 coding standards (enforced by Pint)
- Migrations should be reversible (`down()` method)

## Default Users

| Role | Email | Password |
|------|-------|----------|
| Administrator | admin@doa.gov.ph | password |
| DOA Staff | staff@doa.gov.ph | password |
| Farmer | farmer@example.com | password |

## Future Development Notes

- API routes prepared for mobile app (React Native) - currently using Sanctum authentication
- Farmer role intended for mobile app JSON-driven access
- Farm visibility pivot table suggests multi-tenancy features
