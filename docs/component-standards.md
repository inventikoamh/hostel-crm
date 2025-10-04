# Component Standards - Hostel CRM

## Overview
This document defines the standard structure and implementation for all reusable components in the Hostel CRM system.

## Component Structure

### File Organization
```
resources/views/components/
├── sidebar.blade.php
├── header.blade.php
├── stats-card.blade.php
├── data-table.blade.php
├── tenant-info.blade.php
├── status-badge.blade.php
├── form-input.blade.php
├── form-select.blade.php
├── form-textarea.blade.php
├── button.blade.php
├── modal.blade.php
└── alert.blade.php
```

### Component Template Structure
```php
@props(['prop1' => 'defaultValue', 'prop2' => null])

<!-- Component HTML -->
<div class="component-class">
    <!-- Component content -->
</div>

@push('scripts')
<script>
// Component-specific JavaScript
</script>
@endpush
```

## Core Components

### 1. Sidebar Component
**File:** `components/sidebar.blade.php`

**Purpose:** Main navigation sidebar

**Props:** None (uses global state)

**Features:**
- Fixed positioning
- Collapsible behavior
- Mobile overlay
- User profile dropdown
- Active state management
- Theme support

**Usage:**
```php
@include('components.sidebar')
```

### 2. Header Component
**File:** `components/header.blade.php`

**Purpose:** Page header with title and optional back button

**Props:**
- `title` (string) - Page title
- `subtitle` (string) - Page subtitle
- `showBackButton` (boolean) - Show back button
- `backUrl` (string) - Back button URL

**Usage:**
```php
@include('components.header', [
    'title' => 'Dashboard',
    'subtitle' => 'Welcome to Hostel CRM',
    'showBackButton' => true,
    'backUrl' => route('dashboard')
])
```

### 3. Stats Card Component
**File:** `components/stats-card.blade.php`

**Purpose:** Display statistics with icons and values

**Props:**
- `icon` (string) - FontAwesome icon class
- `iconColor` (string) - Icon color (hex)
- `iconBg` (string) - Icon background color (rgba)
- `title` (string) - Card title
- `value` (string|number) - Main value
- `subtitle` (string) - Subtitle text
- `subtitleIcon` (string) - Subtitle icon class

**Usage:**
```php
<x-stats-card
    icon="fas fa-users"
    icon-color="#2563eb"
    icon-bg="rgba(59, 130, 246, 0.1)"
    title="Total Tenants"
    :value="$stats['total_tenants']"
    subtitle="All tenants"
    subtitle-icon="fas fa-home"
/>
```

### 4. Data Table Component
**File:** `components/data-table.blade.php`

**Purpose:** Advanced data table with filtering, pagination, and bulk actions

**Props:**
- `title` (string) - Table title
- `addButtonText` (string) - Add button text
- `addButtonUrl` (string) - Add button URL
- `columns` (array) - Column definitions
- `data` (array) - Table data
- `actions` (boolean) - Show actions column
- `searchable` (boolean) - Enable search
- `exportable` (boolean) - Show export button
- `filters` (array) - Filter configuration
- `bulkActions` (array) - Bulk action configuration
- `pagination` (array) - Pagination data

**Usage:**
```php
<x-data-table
    title="All Tenants"
    add-button-text="Add Tenant"
    add-button-url="{{ route('tenants.create') }}"
    :columns="$columns"
    :data="$tableData"
    :actions="true"
    :searchable="true"
    :exportable="true"
    :filters="$filters"
    :bulk-actions="$bulkActions"
    :pagination="$pagination"
/>
```

### 5. Tenant Info Component
**File:** `components/tenant-info.blade.php`

**Purpose:** Display tenant avatar, name, and email with proper asset handling

**Props:**
- `name` (string) - Tenant's full name
- `email` (string) - Tenant's email address
- `avatar` (string) - Avatar file path (optional)

**Features:**
- Automatic asset URL generation for avatars
- Fallback to user icon when no avatar is provided
- Responsive design with proper sizing
- Theme support with CSS variables

**Usage:**
```php
<x-tenant-info 
    :name="$tenant['name']" 
    :email="$tenant['email']" 
    :avatar="$tenant['avatar']" 
/>
```

**Example Output:**
```html
<div class="flex items-center">
    <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mr-3">
        <img src="/storage/avatars/filename.jpg" alt="John Doe" class="w-10 h-10 rounded-full object-cover">
    </div>
    <div>
        <div class="text-sm font-medium">John Doe</div>
        <div class="text-sm text-gray-500">john@example.com</div>
    </div>
</div>
```

### 6. Status Badge Component
**File:** `components/status-badge.blade.php`

**Purpose:** Display status with color-coded badges for various status types

**Props:**
- `status` (string|boolean) - Status value (active/pending/inactive/sent/failed/true/false)

**Supported Status Types:**
- **Notification Statuses**: pending, sent, failed, scheduled, cancelled
- **General Statuses**: active, inactive, verified, unverified
- **Payment Statuses**: paid, unpaid, overdue, completed, processing
- **Boolean Values**: true/false (displays as Yes/No)
- **Numeric Values**: 1/0 (displays as Yes/No)

**Color Coding:**
- 🟢 Green: sent, active, verified, paid, completed, true
- 🟡 Yellow: pending, unverified, false
- 🔴 Red: failed, unpaid, overdue, false
- 🔵 Blue: scheduled, processing
- ⚪ Gray: cancelled, inactive, draft, false

**Usage:**
```php
<x-status-badge :status="$item['status']" />
```

**Example Output:**
```html
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
    Sent
</span>
```

## Form Components

### 1. Form Input Component
**File:** `components/form-input.blade.php`

**Purpose:** Standardized form input field

**Props:**
- `name` (string) - Input name
- `label` (string) - Input label
- `type` (string) - Input type (default: text)
- `value` (string) - Input value
- `placeholder` (string) - Placeholder text
- `required` (boolean) - Required field
- `error` (string) - Error message

**Usage:**
```php
<x-form-input
    name="name"
    label="Full Name"
    type="text"
    :value="old('name')"
    placeholder="Enter full name"
    :required="true"
    :error="$errors->first('name')"
/>
```

### 2. Form Select Component
**File:** `components/form-select.blade.php`

**Purpose:** Standardized form select dropdown

**Props:**
- `name` (string) - Select name
- `label` (string) - Select label
- `options` (array) - Select options
- `value` (string) - Selected value
- `placeholder` (string) - Placeholder text
- `required` (boolean) - Required field
- `error` (string) - Error message

**Usage:**
```php
<x-form-select
    name="status"
    label="Status"
    :options="[
        ['value' => 'active', 'label' => 'Active'],
        ['value' => 'inactive', 'label' => 'Inactive']
    ]"
    :value="old('status')"
    placeholder="Select status"
    :required="true"
    :error="$errors->first('status')"
/>
```

### 3. Form Textarea Component
**File:** `components/form-textarea.blade.php`

**Purpose:** Standardized form textarea

**Props:**
- `name` (string) - Textarea name
- `label` (string) - Textarea label
- `value` (string) - Textarea value
- `placeholder` (string) - Placeholder text
- `rows` (number) - Number of rows
- `required` (boolean) - Required field
- `error` (string) - Error message

**Usage:**
```php
<x-form-textarea
    name="description"
    label="Description"
    :value="old('description')"
    placeholder="Enter description"
    :rows="4"
    :required="false"
    :error="$errors->first('description')"
/>
```

## UI Components

### 1. Button Component
**File:** `components/button.blade.php`

**Purpose:** Standardized button component

**Props:**
- `type` (string) - Button type (primary/secondary/danger)
- `size` (string) - Button size (sm/md/lg)
- `icon` (string) - FontAwesome icon class
- `disabled` (boolean) - Disabled state
- `onclick` (string) - Click handler

**Usage:**
```php
<x-button
    type="primary"
    size="md"
    icon="fas fa-save"
    onclick="submitForm()"
>
    Save Changes
</x-button>
```

### 2. Modal Component
**File:** `components/modal.blade.php`

**Purpose:** Standardized modal dialog

**Props:**
- `id` (string) - Modal ID
- `title` (string) - Modal title
- `size` (string) - Modal size (sm/md/lg/xl)
- `closable` (boolean) - Show close button

**Usage:**
```php
<x-modal
    id="confirmModal"
    title="Confirm Action"
    size="md"
    :closable="true"
>
    <p>Are you sure you want to perform this action?</p>
    
    <div class="flex justify-end space-x-3 mt-6">
        <x-button type="secondary" onclick="closeModal('confirmModal')">
            Cancel
        </x-button>
        <x-button type="danger" onclick="confirmAction()">
            Confirm
        </x-button>
    </div>
</x-modal>
```

### 3. Alert Component
**File:** `components/alert.blade.php`

**Purpose:** Display alert messages

**Props:**
- `type` (string) - Alert type (success/warning/error/info)
- `title` (string) - Alert title
- `message` (string) - Alert message
- `dismissible` (boolean) - Show dismiss button

**Usage:**
```php
<x-alert
    type="success"
    title="Success!"
    message="The operation completed successfully."
    :dismissible="true"
/>
```

## Component Standards

### Naming Conventions
- Use kebab-case for file names
- Use descriptive names that indicate purpose
- Prefix with component type when applicable (form-, ui-, etc.)

### Props Standards
- Always provide default values
- Use type hints in comments
- Group related props together
- Use boolean props for simple toggles

### Styling Standards
- Use Tailwind CSS classes
- Support theme switching with CSS variables
- Maintain responsive design
- Follow consistent spacing patterns

### JavaScript Standards
- Use vanilla JavaScript (no frameworks)
- Implement proper event handling
- Provide error handling
- Use modern ES6+ features

### Accessibility Standards
- Include proper ARIA labels
- Support keyboard navigation
- Maintain color contrast
- Provide screen reader support

## Component Development Guidelines

### 1. Planning
- Define component purpose and scope
- Identify required props and their types
- Plan responsive behavior
- Consider accessibility requirements

### 2. Implementation
- Start with basic HTML structure
- Add Tailwind CSS classes
- Implement JavaScript functionality
- Add accessibility features

### 3. Testing
- Test with different prop combinations
- Verify responsive behavior
- Check accessibility compliance
- Test theme switching

### 4. Documentation
- Document all props and their types
- Provide usage examples
- Include accessibility notes
- Document any limitations

## Component Examples

### Custom Component Template
```php
@props([
    'title' => 'Default Title',
    'subtitle' => null,
    'variant' => 'default',
    'size' => 'md'
])

@php
    $sizeClasses = [
        'sm' => 'text-sm p-3',
        'md' => 'text-base p-4',
        'lg' => 'text-lg p-6'
    ];
    
    $variantClasses = [
        'default' => 'bg-white border border-gray-200',
        'primary' => 'bg-blue-50 border border-blue-200',
        'success' => 'bg-green-50 border border-green-200'
    ];
@endphp

<div class="rounded-lg shadow-sm {{ $sizeClasses[$size] }} {{ $variantClasses[$variant] }}"
     style="background-color: var(--card-bg); border-color: var(--border-color);">
    <h3 class="font-semibold mb-2" style="color: var(--text-primary);">
        {{ $title }}
    </h3>
    
    @if($subtitle)
        <p class="text-sm" style="color: var(--text-secondary);">
            {{ $subtitle }}
        </p>
    @endif
    
    {{ $slot }}
</div>
```

### Component Usage
```php
<x-custom-component
    title="Custom Title"
    subtitle="This is a subtitle"
    variant="primary"
    size="lg"
>
    <p>Custom content goes here</p>
</x-custom-component>
```

## Maintenance

### Regular Updates
- Review component standards quarterly
- Update components for new requirements
- Maintain backward compatibility
- Document changes and migrations

### Version Control
- Tag component releases
- Maintain changelog
- Document breaking changes
- Provide migration guides

### Testing
- Unit test all components
- Integration test component interactions
- Visual regression test UI changes
- Accessibility test compliance
