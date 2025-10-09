# Hostel CRM System Documentation

## Overview

The Hostel CRM (Customer Relationship Management) system is a comprehensive web application built with Laravel 12 and modern frontend technologies. It provides a complete solution for managing hostels, tenants, rooms, and related operations.

## Table of Contents

1. [System Overview](#system-overview)
2. [Architecture](#architecture)
3. [Technology Stack](#technology-stack)
4. [Module Documentation](#module-documentation)
5. [Component System](#component-system)
6. [API & Routes](#api--routes)
7. [Database Schema](#database-schema)
8. [Setup & Installation](#setup--installation)
9. [Development Guidelines](#development-guidelines)
10. [Deployment](#deployment)
11. [Changelog](#changelog)

## System Overview

### Purpose
The Hostel CRM system is designed to streamline hostel management operations including:
- Hostel registration and management
- Tenant management and tracking
- Room allocation and monitoring
- Payment tracking and billing
- Maintenance scheduling
- Reporting and analytics

### Key Features
- **Multi-hostel Management**: Manage multiple hostels from a single interface
- **Tenant Management**: Complete tenant lifecycle management with billing cycles and advanced bed assignment system
- **Room & Bed Management**: Track room availability, bed allocation, and occupancy
- **Availability System**: Comprehensive room and bed availability checking based on lease dates with date overlap detection
- **Visual Map System**: Interactive floor-wise room and bed visualization
- **Financial Management**: Complete invoicing, payment processing, and billing automation
- **Amenity Services**: Paid services management with usage tracking and billing
- **Usage Tracking**: Daily attendance-style recording for amenity usage
- **PDF Generation**: Professional invoices with automated email delivery
- **Payment Processing**: Multi-method payment support with verification system
- **Enquiry Management**: Public enquiry forms and admin management
- **Notification System**: Automated email notifications with configurable templates
- **Configuration Management**: Amenities and system settings
- **User Management**: Complete user administration with role-based access control (RBAC)
- **Role & Permission System**: Granular permission management with module organization
- **Tenant Profile Update Requests**: Admin approval workflow for tenant profile changes with change detection
- **Reporting & Analytics**: Comprehensive reports with charts and export capabilities
- **Database Integration**: Full database connectivity with Eloquent ORM
- **Demo Data**: Comprehensive seeder with realistic sample data
- **Responsive Design**: Mobile-friendly interface for all devices
- **Theme Support**: Light and dark mode support with global scrollbar styling
- **Component Architecture**: Reusable Blade components for consistency
- **Advanced Data Tables**: Search, filters, pagination, and bulk actions

## Architecture

### MVC Pattern
The system follows Laravel's Model-View-Controller (MVC) architecture:

```
app/
├── Http/Controllers/     # Controllers handle business logic
├── Models/              # Models represent data structures
├── Providers/           # Service providers for dependency injection
└── ...

resources/
├── views/               # Blade templates for UI
│   ├── components/      # Reusable UI components
│   ├── layouts/         # Layout templates
│   └── modules/         # Module-specific views
└── css/                 # Stylesheets

database/
├── migrations/          # Database schema definitions
├── seeders/            # Database seeding
└── factories/          # Model factories for testing
```

### Component-Based Architecture
The system uses a component-based approach for UI consistency:
- **Layout Components**: Master layout with sidebar and header
- **Data Components**: Reusable data display components
- **Form Components**: Standardized form elements
- **UI Components**: Buttons, cards, badges, etc.

## Technology Stack

### Backend
- **Laravel 12**: PHP framework for web applications
- **PHP 8.3+**: Server-side programming language
- **MySQL/SQLite**: Database management system
- **Eloquent ORM**: Object-relational mapping

### Frontend
- **Blade Templates**: Server-side templating engine
- **Tailwind CSS 4**: Utility-first CSS framework
- **JavaScript (ES6+)**: Client-side scripting
- **Font Awesome**: Icon library
- **Vite**: Build tool and development server

### Development Tools
- **Composer**: PHP dependency manager
- **NPM**: Node.js package manager
- **Git**: Version control system
- **Artisan**: Laravel command-line interface

## Module Documentation

### Core Modules
1. **[Authentication Module](modules/authentication.md)** - User login, logout, and session management
2. **[Dashboard Module](modules/dashboard.md)** - Main dashboard with overview and statistics
3. **[Hostel Module](modules/hostel.md)** - Hostel management with full database integration
4. **[Tenant Module](modules/tenant.md)** - Complete tenant management with billing cycles
5. **[Tenant Profile Update Requests](modules/tenant-profile-update-requests.md)** - Admin approval workflow for tenant profile changes
6. **[Room Module](modules/room.md)** - Room and bed management with occupancy tracking
7. **[Availability Module](modules/availability.md)** - Room and bed availability checking system
8. **[Map Module](modules/map.md)** - Visual floor-wise room and bed mapping
9. **[Enquiry Module](modules/enquiry.md)** - Public enquiry forms and admin management
10. **[Component System](modules/components.md)** - Reusable UI components

### Financial Modules
11. **[Invoice System](modules/invoice.md)** - Comprehensive invoicing with PDF generation and email delivery
12. **[Payment System](modules/payment.md)** - Multi-method payment processing and tracking
13. **[Paid Amenities](modules/paid-amenities.md)** - Additional services management and tenant subscriptions
14. **[Amenity Usage Tracking](modules/amenity-usage.md)** - Daily usage tracking with attendance-style recording
15. **[Usage Correction Requests](modules/usage-correction-requests.md)** - Tenant correction request system with admin approval workflow
16. **[Billing System](billing-cycle-system.md)** - Automated billing cycles and payment tracking

### Configuration Modules
- **Amenities Management** - Configurable amenities for hostels
- **Notification System** - Email notifications with configurable templates and settings
- **System Settings** - Global system configuration and SMTP settings

### User Management Modules
17. **[User Management](modules/user-management.md)** - Complete user administration with role-based access control (RBAC)
    - **User Management** - User CRUD operations with status management and avatar uploads
    - **Role Management** - Role creation, assignment, and permission management
    - **Permission Management** - Granular permission system with module organization
    - **Access Control** - Middleware-based permission checking and route protection

### Completed Features
- **PDF Generation** - Professional invoice PDFs with company branding
- **Email Integration** - Automated invoice and receipt delivery
- **Usage Analytics** - Comprehensive reporting with charts and exports
- **Billing Automation** - Automated monthly invoice generation
- **Payment Verification** - Multi-level payment verification system
- **Bulk Operations** - Mass operations for efficient management

## Component System

The system uses a comprehensive component library for consistent UI:

### Layout Components
- `x-layout` - Master layout wrapper
- `x-sidebar` - Navigation sidebar
- `x-header` - Page header with title and actions

### Data Components
- `x-data-table` - Advanced data table with search, filters, and pagination
- `x-stats-card` - Statistics display cards
- `x-status-badge` - Status indicators

### Form Components
- Form inputs with consistent styling
- Validation error display
- File upload components

## API & Routes

### Route Structure
```
/                       # Welcome page
/login                  # Authentication
/dashboard              # Main dashboard
/users                  # User management with RBAC
/roles                  # Role management
/permissions            # Permission management
/hostels                # Hostel management
/tenants                # Tenant management with billing
/rooms                  # Room and bed management
/availability           # Room and bed availability checking
/map                    # Visual hostel mapping
/enquiries              # Enquiry management
/invoices               # Invoice management with PDF generation
/payments               # Payment processing and tracking
/paid-amenities         # Paid services management
/tenant-amenities       # Tenant service subscriptions
/amenity-usage          # Daily usage tracking and attendance
/config/amenities       # Amenities configuration
/config/smtp-settings   # Email configuration
/contact                # Public enquiry form
```

### RESTful Routes
All modules follow RESTful conventions:
- `GET /resource` - List resources
- `GET /resource/create` - Show create form
- `POST /resource` - Store new resource
- `GET /resource/{id}` - Show specific resource
- `GET /resource/{id}/edit` - Show edit form
- `PUT/PATCH /resource/{id}` - Update resource
- `DELETE /resource/{id}` - Delete resource

## Database Schema

### Core Tables
- `users` - System users and authentication with role management
- `roles` - User roles with permission assignments
- `permissions` - System permissions organized by modules
- `user_roles` - Many-to-many relationship between users and roles
- `role_permissions` - Many-to-many relationship between roles and permissions
- `hostels` - Hostel information and details with amenities
- `tenant_profiles` - Tenant information with billing cycle data
- `rooms` - Room details and allocation
- `beds` - Individual bed management and assignment
- `enquiries` - Public enquiry submissions
- `amenities` - Configurable hostel amenities

### Financial Tables
- `invoices` - Invoice management with PDF generation
- `invoice_items` - Itemized billing details
- `payments` - Payment processing and tracking
- `paid_amenities` - Additional paid services
- `tenant_amenities` - Tenant service subscriptions
- `tenant_amenity_usage` - Daily usage tracking records

### Relationships
- Users can have multiple roles and inherit permissions through roles
- Roles can have multiple permissions and be assigned to multiple users
- Permissions are organized by modules and can be assigned to multiple roles
- Users can manage multiple hostels and process payments
- Hostels have multiple rooms with beds and offer paid amenities
- Tenant profiles are linked to users, beds, and amenity subscriptions
- Rooms belong to hostels and contain multiple beds
- Beds can be assigned to tenants with billing information
- Enquiries are linked to specific hostels
- Invoices are generated for tenants with itemized billing
- Payments are linked to invoices and tenants
- Usage records track daily amenity consumption

## Setup & Installation

### Prerequisites
- PHP 8.3 or higher
- Composer
- Node.js and NPM
- MySQL or SQLite
- Git

### Installation Steps
1. Clone the repository
2. Install PHP dependencies: `composer install`
3. Install Node.js dependencies: `npm install`
4. Copy environment file: `cp .env.example .env`
5. Generate application key: `php artisan key:generate`
6. Configure database in `.env`
7. Run migrations: `php artisan migrate`
8. Seed database: `php artisan db:seed`
9. Build assets: `npm run build`
10. Start development server: `php artisan serve`

## Development Guidelines

### Code Standards
- Follow PSR-12 coding standards
- Use meaningful variable and function names
- Write comprehensive comments
- Implement proper error handling
- Follow Laravel conventions

### Component Development
- Create reusable components
- Use consistent prop naming
- Implement proper validation
- Follow accessibility guidelines
- Test on multiple devices

### Database Design
- Use proper indexing
- Implement foreign key constraints
- Follow normalization principles
- Use migrations for schema changes
- Implement proper seeding

## Deployment

### Production Requirements
- PHP 8.3+ with required extensions
- Web server (Apache/Nginx)
- Database server (MySQL/PostgreSQL)
- SSL certificate
- Domain name

### Deployment Steps
1. Set up production server
2. Configure web server
3. Install dependencies
4. Set up database
5. Configure environment variables
6. Run migrations and seeders
7. Build production assets
8. Set up monitoring and backups

## Support & Maintenance

### Documentation Updates
- Keep documentation current with code changes
- Update API documentation for new endpoints
- Maintain component documentation
- Update setup instructions as needed

### Version Control
- Use semantic versioning
- Maintain changelog
- Tag releases appropriately
- Document breaking changes

## Contributing

### Development Workflow
1. Create feature branch
2. Implement changes
3. Write tests
4. Update documentation
5. Submit pull request
6. Code review process
7. Merge to main branch

### Testing
- Write unit tests for models
- Write feature tests for controllers
- Test UI components
- Perform integration testing
- Test on multiple browsers and devices

## Changelog

For a comprehensive list of recent updates, improvements, and new features, please refer to the [Changelog](CHANGELOG.md).

### Recent Major Updates
- **Bed Assignment System Overhaul**: Advanced multi-tenant bed assignment with date-based availability
- **Enhanced Tenant Creation**: Conditional bed selection with improved user experience
- **Availability System**: Comprehensive room and bed availability checking
- **UI/UX Improvements**: Removed test buttons, added progressive disclosure, enhanced visual feedback
- **Status Management**: Real-time status updates for tenant amenities with AJAX functionality
- **Tenant Details Integration**: Comprehensive tenant information display in amenity management
- **Enhanced User Experience**: Toast notifications, loading states, and error handling

---

For detailed information about specific modules, please refer to the individual module documentation files in the `docs/modules/` directory.
