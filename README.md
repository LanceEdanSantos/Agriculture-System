# Department of Agriculture (DOA) Inventory System

A comprehensive inventory management system built with Laravel, Filament, and Shield for the Department of Agriculture. This system manages inventory items, purchase requests, and user roles with proper permissions and multi-tenancy support.

## Features

### 🏢 **Multi-Role System**
- **Administrators**: Full access to all features
- **DOA Staff**: Manage inventory and purchase requests
- **Farmers**: View-only access to inventory and purchase requests

### 📦 **Inventory Management**
- Complete inventory item management
- Stock tracking with low stock alerts
- Expiration date monitoring
- Category-based organization
- Unit cost and total value calculations
- JSON import functionality for bulk data

### 📋 **Purchase Request System**
- Comprehensive purchase request forms
- Multi-section item management
- Approval workflow
- Status tracking (Draft, Pending, Approved, Rejected, Completed)
- Personnel assignment and tracking

### 🔐 **Security & Permissions**
- Filament Shield integration
- Role-based access control
- Granular permissions for each resource
- Secure user management

### 📊 **Dashboard & Analytics**
- Real-time inventory statistics
- Low stock alerts
- Expired item tracking
- Purchase request status overview
- Total inventory value calculation

## Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd agriculture-system
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Build assets**
   ```bash
   npm run build
   ```

## Default Users

The system comes with three default users:

| Role | Email | Password | Access Level |
|------|-------|----------|--------------|
| Administrator | admin@doa.gov.ph | password | Full access |
| DOA Staff | staff@doa.gov.ph | password | Inventory & Purchase management | - wala pa
| Farmer | farmer@example.com | password | View-only access | - wala pa (API Json Driven -> mobile app (React Native -> Javascript Framework (Android Application/IOS Applications)))

## Usage

### Accessing the System
- Navigate to `/admin` to access the Filament admin panel
- Login with any of the default credentials above

### Inventory Management
1. **View Inventory**: Navigate to "Inventory Management" → "Inventory Items"
2. **Add Items**: Click "Create Inventory Item" or use the JSON import feature
3. **Adjust Stock**: Use the "Adjust Stock" action on individual items
4. **Monitor**: Use filters to view low stock, expired items, etc.

### Purchase Requests
1. **Create Request**: Navigate to "Purchase Management" → "Purchase Requests"
2. **Add Items**: Use the repeater to add multiple items with categories
3. **Approve/Reject**: Use the action buttons to change request status
4. **Track**: Monitor request status through the dashboard

### JSON Import
The system supports importing inventory items from JSON format:

1. Navigate to "Inventory Management" → "Inventory Items"
2. Click "Import from JSON" button
3. Paste your JSON data in the format:
   ```json
   {
     "purchase_request": {
       "sections": [
         {
           "category": "Assorted Vegetable Seeds",
           "items": [
             {
               "description": "Okra - Smooth Green (1 kg. Per pack or can)",
               "unit": "packs/cans",
               "unit_cost": 2400.00
             }
           ]
         }
       ]
     }
   }
   ```

## Database Structure

### Core Tables
- `users` - User accounts with roles
- `inventory_items` - Inventory items with stock tracking
- `purchase_requests` - Purchase request forms
- `purchase_request_items` - Individual items in purchase requests
- `permissions` - Shield permissions
- `roles` - Shield roles
- `role_has_permissions` - Role-permission relationships
- `model_has_roles` - User-role relationships
- `model_has_permissions` - User-permission relationships

### Key Features
- **Multi-tenancy ready**: Shield supports tenant scoping
- **Audit trail**: All changes are tracked
- **Soft deletes**: Data is preserved when deleted
- **Real-time updates**: Dashboard updates automatically

## Customization

### Adding New Roles
1. Create the role in the database seeder
2. Assign appropriate permissions
3. Update the User model if needed

### Adding New Inventory Categories
1. Update the options in `InventoryItemResource`
2. Add to the purchase request form if needed
3. Update any related business logic

### Custom Permissions
1. Generate new permissions: `php artisan shield:generate --resource=YourResource`
2. Assign to roles in the seeder
3. Update policies if needed

## Security Features

- **Role-based access control** with Filament Shield
- **Permission-based authorization** for all resources
- **Secure password handling** with Laravel's built-in hashing
- **CSRF protection** on all forms
- **Session management** with proper security headers

## Support

For technical support or feature requests, please contact the development team.

## License

This project is licensed under the MIT License. 
