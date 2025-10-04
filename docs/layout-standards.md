# Layout Standards - Hostel CRM

## Overview
This document defines the standard layout structure and implementation for all pages in the Hostel CRM system to ensure consistency across the application.

## Master Layout Structure

### Layout Hierarchy
```
layouts/
├── app.blade.php (Master layout)
├── auth.blade.php (Authentication layout)
└── guest.blade.php (Public layout)
```

### Master Layout Components
```
components/
├── sidebar.blade.php (Navigation sidebar)
├── header.blade.php (Page header)
├── stats-card.blade.php (Statistics cards)
├── data-table.blade.php (Data tables)
├── tenant-info.blade.php (Tenant information)
└── status-badge.blade.php (Status indicators)
```

## Page Structure Standards

### Standard Page Layout
```php
@extends('layouts.app')

@section('title', 'Page Title - Hostel CRM')

@php
    $title = 'Page Title';
    $subtitle = 'Page description or subtitle';
    $showBackButton = false; // Optional
    $backUrl = null; // Optional
@endphp

@section('content')
    <!-- Page content here -->
@endsection
```

### Required Variables
Every page must define:
- `$title` - Page title for header
- `$subtitle` - Page description
- Optional: `$showBackButton` and `$backUrl` for navigation

## Layout Components

### Sidebar Component
```php
@include('components.sidebar')
```

**Features:**
- Fixed positioning
- Collapsible on desktop
- Mobile overlay
- User profile dropdown
- Navigation items with active states
- Theme support

### Header Component
```php
@include('components.header', [
    'title' => $title ?? 'Dashboard',
    'subtitle' => $subtitle ?? 'Welcome to Hostel CRM',
    'showBackButton' => $showBackButton ?? false,
    'backUrl' => $backUrl ?? null
])
```

**Features:**
- Page title and subtitle
- Optional back button
- Responsive design
- Theme support

### Sticky Header
```php
<!-- Automatically included in master layout -->
<div class="sticky-header">
    <!-- Menu toggle, page title, theme toggle -->
</div>
```

**Features:**
- Fixed positioning
- Menu toggle button
- Page title display
- Theme toggle button
- Responsive behavior

## Content Structure Standards

### Page Sections
1. **Stats Cards** (Optional)
2. **Main Content**
3. **Data Tables** (If applicable)
4. **Forms** (If applicable)
5. **Modals** (If applicable)

### Stats Cards Section
```php
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6 sm:mb-8">
    <x-stats-card
        icon="fas fa-users"
        icon-color="#2563eb"
        icon-bg="rgba(59, 130, 246, 0.1)"
        title="Total Items"
        :value="$stats['total']"
        subtitle="All items"
        subtitle-icon="fas fa-home"
    />
    <!-- More stats cards -->
</div>
```

### Main Content Section
```php
<div class="space-y-6">
    <!-- Content blocks -->
</div>
```

## Responsive Design Standards

### Breakpoints
- **Mobile**: < 640px
- **Tablet**: 640px - 1024px
- **Desktop**: > 1024px

### Grid System
```php
<!-- Mobile: 1 column, Tablet: 2 columns, Desktop: 4 columns -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">

<!-- Mobile: 1 column, Desktop: 2 columns -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
```

### Spacing Standards
- **Small spacing**: `gap-3 sm:gap-4`
- **Medium spacing**: `gap-4 sm:gap-6`
- **Large spacing**: `gap-6 sm:gap-8`
- **Section spacing**: `mb-6 sm:mb-8`

## Theme Support

### CSS Variables
```css
:root {
    --bg-primary: #f9fafb;
    --bg-secondary: #ffffff;
    --text-primary: #1f2937;
    --text-secondary: #6b7280;
    --border-color: #e5e7eb;
    --card-bg: #ffffff;
    --hover-bg: #f3f4f6;
}

[data-theme="dark"] {
    --bg-primary: #111827;
    --bg-secondary: #1f2937;
    --text-primary: #f9fafb;
    --text-secondary: #d1d5db;
    --border-color: #374151;
    --card-bg: #1f2937;
    --hover-bg: #374151;
}
```

### Theme Implementation
```php
<!-- Use CSS variables for theme support -->
<div style="background-color: var(--card-bg); color: var(--text-primary);">
    Content
</div>
```

## Component Standards

### Stats Card Component
```php
<x-stats-card
    icon="fas fa-icon"
    icon-color="#color"
    icon-bg="rgba(r, g, b, 0.1)"
    title="Card Title"
    :value="$value"
    subtitle="Subtitle"
    subtitle-icon="fas fa-icon"
/>
```

### Data Table Component
```php
<x-data-table
    title="Table Title"
    add-button-text="Add New"
    add-button-url="#"
    :columns="$columns"
    :data="$data"
    :actions="true"
    :searchable="true"
    :exportable="true"
    :filters="$filters"
    :bulk-actions="$bulkActions"
    :pagination="$pagination"
/>
```

## Navigation Standards

### Sidebar Navigation
```php
<a href="{{ route('dashboard') }}" 
   class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }} flex items-center px-4 py-3 rounded-lg">
    <i class="fas fa-tachometer-alt mr-3"></i>
    Dashboard
</a>
```

### Breadcrumb Navigation
```php
<!-- Optional breadcrumb component -->
<nav class="flex items-center space-x-2 text-sm text-gray-500 mb-4">
    <a href="{{ route('dashboard') }}" class="hover:text-gray-700">Dashboard</a>
    <i class="fas fa-chevron-right text-xs"></i>
    <span class="text-gray-900">Current Page</span>
</nav>
```

## Form Standards

### Form Layout
```php
<form class="space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                Field Label
            </label>
            <input type="text" 
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
        </div>
    </div>
    
    <div class="flex justify-end space-x-3">
        <button type="button" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
            Cancel
        </button>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Save
        </button>
    </div>
</form>
```

## Modal Standards

### Modal Structure
```php
<!-- Modal Trigger -->
<button onclick="openModal('modalId')" class="bg-blue-600 text-white px-4 py-2 rounded-lg">
    Open Modal
</button>

<!-- Modal -->
<div id="modalId" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full" style="background-color: var(--card-bg);">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold" style="color: var(--text-primary);">Modal Title</h3>
                    <button onclick="closeModal('modalId')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <!-- Modal content -->
            </div>
        </div>
    </div>
</div>
```

## JavaScript Standards

### Required Functions
```javascript
// Theme management
function toggleTheme() { /* ... */ }
function initializeTheme() { /* ... */ }

// Sidebar management
function toggleSidebar() { /* ... */ }
function initializeSidebar() { /* ... */ }

// Modal management
function openModal(modalId) { /* ... */ }
function closeModal(modalId) { /* ... */ }

// Form handling
function submitForm(formId) { /* ... */ }
function resetForm(formId) { /* ... */ }
```

### Event Handling
```javascript
// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeTheme();
    initializeSidebar();
    // Other initializations
});

// Handle window resize
window.addEventListener('resize', function() {
    initializeSidebar();
});
```

## Accessibility Standards

### Required Features
- Keyboard navigation support
- Screen reader compatibility
- Proper ARIA labels
- Focus management
- Color contrast compliance

### ARIA Implementation
```html
<button aria-label="Toggle sidebar" title="Toggle sidebar">
    <i class="fas fa-bars"></i>
</button>

<div role="dialog" aria-labelledby="modal-title" aria-hidden="true">
    <h3 id="modal-title">Modal Title</h3>
</div>
```

## Performance Standards

### Optimization
- Lazy load images and heavy content
- Use CSS variables for theme switching
- Minimize JavaScript bundle size
- Implement proper caching strategies
- Use efficient DOM manipulation

### Loading States
```php
<!-- Loading skeleton -->
<div class="animate-pulse">
    <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
    <div class="h-4 bg-gray-200 rounded w-1/2"></div>
</div>
```

## Error Handling

### Error Pages
```php
<!-- 404 Error -->
@extends('layouts.app')

@section('title', 'Page Not Found - Hostel CRM')

@section('content')
    <div class="text-center py-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">404</h1>
        <p class="text-gray-600 mb-8">Page not found</p>
        <a href="{{ route('dashboard') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg">
            Go to Dashboard
        </a>
    </div>
@endsection
```

## Security Standards

### CSRF Protection
```php
<form method="POST" action="{{ route('items.store') }}">
    @csrf
    <!-- Form fields -->
</form>
```

### Input Validation
```php
// In controller
$request->validate([
    'name' => 'required|string|max:255',
    'email' => 'required|email|unique:users',
]);
```

## Testing Standards

### Required Tests
- Layout rendering tests
- Responsive design tests
- Theme switching tests
- Accessibility tests
- Performance tests

### Test Structure
```php
// Feature test example
public function test_dashboard_layout_renders_correctly()
{
    $response = $this->get('/dashboard');
    
    $response->assertStatus(200);
    $response->assertSee('Dashboard');
    $response->assertSee('Welcome to Hostel CRM');
}
```

## Maintenance

### Regular Updates
- Review layout standards quarterly
- Test new features against existing standards
- Update documentation for any changes
- Ensure backward compatibility

### Version Control
- Tag major layout changes
- Maintain changelog for standards
- Document breaking changes
- Provide migration guides

## Examples

### Complete Page Implementation
```php
@extends('layouts.app')

@section('title', 'Tenants - Hostel CRM')

@php
    $title = 'Tenants';
    $subtitle = 'Manage all your hostel tenants and their information.';
@endphp

@section('content')
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6 sm:mb-8">
        <x-stats-card
            icon="fas fa-users"
            icon-color="#2563eb"
            icon-bg="rgba(59, 130, 246, 0.1)"
            title="Total Tenants"
            :value="$stats['total_tenants']"
            subtitle="All tenants"
            subtitle-icon="fas fa-home"
        />
    </div>

    <!-- Data Table -->
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
@endsection
```
