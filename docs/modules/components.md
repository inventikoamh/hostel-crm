# Component System

## Overview

The Component System is the foundation of the Hostel CRM's UI consistency and reusability. It provides a comprehensive library of reusable Blade components that ensure consistent design, behavior, and functionality across the entire application.

## Architecture

### Component-Based Design
The system follows a component-based architecture where UI elements are broken down into reusable, self-contained components that can be composed together to build complex interfaces.

### Benefits
- **Consistency**: Uniform look and feel across the application
- **Reusability**: Write once, use everywhere
- **Maintainability**: Centralized component logic
- **Scalability**: Easy to add new components
- **Testing**: Isolated component testing
- **Documentation**: Self-documenting components

## File Structure

```
resources/views/components/
├── layout/
│   ├── app.blade.php              # Master layout
│   ├── sidebar.blade.php          # Navigation sidebar
│   └── header.blade.php           # Page header
├── data/
│   ├── data-table.blade.php       # Advanced data table
│   ├── stats-card.blade.php       # Statistics display
│   └── status-badge.blade.php     # Status indicators
├── forms/
│   ├── input.blade.php            # Form inputs
│   ├── select.blade.php           # Select dropdowns
│   └── textarea.blade.php         # Text areas
├── ui/
│   ├── button.blade.php           # Buttons
│   ├── card.blade.php             # Cards
│   └── modal.blade.php            # Modals
└── business/
    ├── tenant-info.blade.php      # Tenant information
    └── hostel-info.blade.php      # Hostel information
```

## Layout Components

### Master Layout (`x-layout`)

#### Purpose
Provides the main application structure with sidebar, header, and content area.

#### Props
```php
@props([
    'title' => 'Hostel CRM',
    'showSidebar' => true,
    'showHeader' => true
])
```

#### Structure
```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="flex h-screen">
        @if($showSidebar)
            <x-sidebar />
        @endif
        
        <div class="flex-1 flex flex-col">
            @if($showHeader)
                <x-sticky-header />
            @endif
            
            <main id="mainContent" class="flex-1 p-4 sm:p-6 lg:p-8 min-h-screen pt-20 transition-all duration-300">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
```

#### Usage
```html
<x-layout title="Dashboard">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Dashboard content -->
    </div>
</x-layout>
```

### Sidebar Component (`x-sidebar`)

#### Purpose
Provides navigation sidebar with collapsible functionality and theme support.

#### Features
- **Collapsible**: Desktop sidebar can be collapsed/expanded
- **Mobile Responsive**: Overlay on mobile devices
- **Active States**: Highlights current page
- **Theme Support**: Light and dark mode compatibility
- **User Profile**: User information and dropdown

#### Structure
```html
<aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-gray-600 to-gray-800 transform transition-transform duration-300 ease-in-out">
    <!-- Sidebar Header -->
    <div class="flex items-center p-4 border-b border-white border-opacity-10 h-16">
        <button onclick="toggleSidebar()" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white bg-opacity-30 hover:bg-opacity-40 transition-all duration-200 mr-3">
            <i class="fas fa-chevron-left text-sm text-gray-800 font-bold"></i>
        </button>
        <div class="w-10 h-10 bg-white bg-opacity-40 rounded-lg flex items-center justify-center mr-3">
            <i class="fas fa-building text-xl text-gray-800 font-bold"></i>
        </div>
        <h1 class="text-xl font-bold text-white">Hostel CRM</h1>
    </div>
    
    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto custom-scrollbar">
        <div class="space-y-2">
            <a href="{{ route('dashboard') }}" class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }} flex items-center px-4 py-3 rounded-lg">
                <i class="fas fa-tachometer-alt mr-3"></i>
                Dashboard
            </a>
            <!-- More navigation items -->
        </div>
    </nav>
    
    <!-- User Profile -->
    <div class="border-t border-white border-opacity-10 p-4">
        <!-- User profile dropdown -->
    </div>
</aside>
```

#### Usage
```html
<x-sidebar />
```

### Header Component (`x-header`)

#### Purpose
Provides page headers with title, subtitle, and action buttons.

#### Props
```php
@props([
    'title' => 'Page Title',
    'subtitle' => 'Page subtitle',
    'showBackButton' => false,
    'backUrl' => '#'
])
```

#### Structure
```html
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            @if($showBackButton)
                <a href="{{ $backUrl }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-2">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back
                </a>
            @endif
            <h1 class="text-2xl sm:text-3xl font-bold" style="color: var(--text-primary);">{{ $title }}</h1>
            @if($subtitle)
                <p class="text-sm sm:text-base mt-1" style="color: var(--text-secondary);">{{ $subtitle }}</p>
            @endif
        </div>
    </div>
</div>
```

#### Usage
```html
<x-header 
    title="Hostels" 
    subtitle="Manage all hostels in your system"
    :show-back-button="false"
/>
```

## Data Components

### Data Table Component (`x-data-table`)

#### Purpose
Provides a comprehensive data table with search, filtering, pagination, and bulk actions.

#### Props
```php
@props([
    'title' => 'Data Table',
    'columns' => [],
    'data' => [],
    'actions' => true,
    'searchable' => true,
    'exportable' => false,
    'filters' => [],
    'bulkActions' => [],
    'pagination' => null,
    'addButtonText' => 'Add New',
    'addButtonUrl' => '#'
])
```

#### Features
- **Search**: Real-time search across data
- **Filtering**: Advanced filter options with offcanvas
- **Pagination**: Configurable pagination
- **Bulk Actions**: Select and perform actions on multiple items
- **Export**: Export data in various formats
- **Responsive**: Mobile-friendly horizontal scrolling
- **Theme Support**: Light and dark mode compatibility

#### Column Configuration
```php
$columns = [
    [
        'key' => 'name',
        'label' => 'Name',
        'width' => 'w-48',
        'sortable' => true,
        'component' => 'components.name-component'
    ],
    [
        'key' => 'status',
        'label' => 'Status',
        'width' => 'w-24',
        'component' => 'components.status-badge'
    ]
];
```

#### Usage
```html
<x-data-table
    title="All Hostels"
    add-button-text="Add Hostel"
    add-button-url="{{ route('hostels.create') }}"
    :columns="$columns"
    :data="$hostels"
    :actions="true"
    :searchable="true"
    :exportable="true"
    :filters="$filters"
    :bulk-actions="$bulkActions"
    :pagination="$pagination"
/>
```

### Stats Card Component (`x-stats-card`)

#### Purpose
Displays statistics in a visually appealing card format.

#### Props
```php
@props([
    'title' => 'Title',
    'value' => '0',
    'subtitle' => 'Subtitle',
    'icon' => 'fas fa-chart-bar',
    'color' => 'blue'
])
```

#### Color Themes
- **Blue**: Primary metrics
- **Green**: Success indicators
- **Purple**: Capacity metrics
- **Orange**: Performance metrics
- **Red**: Alerts
- **Yellow**: Warnings

#### Structure
```html
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
    <div class="flex items-center">
        <div class="flex-shrink-0">
            <div class="w-8 h-8 sm:w-12 sm:h-12 bg-{{ $color }}-100 rounded-lg flex items-center justify-center">
                <i class="{{ $icon }} text-{{ $color }}-600 text-sm sm:text-lg"></i>
            </div>
        </div>
        <div class="ml-4 sm:ml-5 w-0 flex-1">
            <dl>
                <dt class="text-sm font-medium truncate" style="color: var(--text-secondary);">{{ $title }}</dt>
                <dd class="flex items-baseline">
                    <div class="text-lg sm:text-2xl font-semibold" style="color: var(--text-primary);">{{ $value }}</div>
                    <div class="ml-2 text-sm font-medium" style="color: var(--text-secondary);">{{ $subtitle }}</div>
                </dd>
            </dl>
        </div>
    </div>
</div>
```

#### Usage
```html
<x-stats-card
    title="Total Hostels"
    value="4"
    subtitle="All registered hostels"
    icon="fas fa-building"
    color="blue"
/>
```

### Status Badge Component (`x-status-badge`)

#### Purpose
Displays status indicators with appropriate colors and icons.

#### Props
```php
@props(['status' => 'inactive'])
```

#### Status Configuration
```php
$statusConfig = [
    'active' => [
        'class' => 'bg-green-100 text-green-800',
        'icon' => 'fas fa-check-circle'
    ],
    'inactive' => [
        'class' => 'bg-red-100 text-red-800',
        'icon' => 'fas fa-times-circle'
    ],
    'pending' => [
        'class' => 'bg-yellow-100 text-yellow-800',
        'icon' => 'fas fa-clock'
    ],
    'maintenance' => [
        'class' => 'bg-orange-100 text-orange-800',
        'icon' => 'fas fa-tools'
    ]
];
```

#### Structure
```html
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $config['class'] }}">
    <i class="{{ $config['icon'] }} mr-1"></i>
    {{ ucfirst($status) }}
</span>
```

#### Usage
```html
<x-status-badge :status="$hostel['status']" />
```

## Business Components

### Tenant Info Component (`x-tenant-info`)

#### Purpose
Displays tenant information in a compact format for data tables.

#### Props
```php
@props(['tenant' => []])
```

#### Structure
```html
<div class="flex items-center">
    <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mr-3">
        <i class="fas fa-user text-gray-600"></i>
    </div>
    <div>
        <div class="text-sm font-medium" style="color: var(--text-primary);">{{ $tenant['name'] }}</div>
        <div class="text-sm" style="color: var(--text-secondary);">{{ $tenant['email'] }}</div>
    </div>
</div>
```

#### Usage
```html
<x-tenant-info :tenant="['name' => 'John Doe', 'email' => 'john@email.com']" />
```

## Form Components

### Input Component (`x-input`)

#### Purpose
Provides consistent form input styling and behavior.

#### Props
```php
@props([
    'type' => 'text',
    'name' => '',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'error' => null
])
```

#### Structure
```html
<div class="mb-4">
    <label for="{{ $name }}" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">
        {{ ucfirst($name) }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    <input 
        type="{{ $type }}" 
        id="{{ $name }}" 
        name="{{ $name }}" 
        value="{{ $value }}"
        placeholder="{{ $placeholder }}"
        @if($required) required @endif
        @if($disabled) disabled @endif
        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error($name) border-red-500 @enderror"
        style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
    >
    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
```

#### Usage
```html
<x-input 
    name="email" 
    type="email" 
    placeholder="Enter email address"
    :required="true"
/>
```

### Select Component (`x-select`)

#### Purpose
Provides consistent select dropdown styling and behavior.

#### Props
```php
@props([
    'name' => '',
    'options' => [],
    'value' => '',
    'placeholder' => 'Select an option',
    'required' => false,
    'disabled' => false,
    'error' => null
])
```

#### Structure
```html
<div class="mb-4">
    <label for="{{ $name }}" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">
        {{ ucfirst($name) }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    <select 
        id="{{ $name }}" 
        name="{{ $name }}"
        @if($required) required @endif
        @if($disabled) disabled @endif
        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error($name) border-red-500 @enderror"
        style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
    >
        <option value="">{{ $placeholder }}</option>
        @foreach($options as $option)
            <option value="{{ $option['value'] }}" {{ $value == $option['value'] ? 'selected' : '' }}>
                {{ $option['label'] }}
            </option>
        @endforeach
    </select>
    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
```

#### Usage
```html
<x-select 
    name="status" 
    :options="[
        ['value' => 'active', 'label' => 'Active'],
        ['value' => 'inactive', 'label' => 'Inactive']
    ]"
    placeholder="Select status"
    :required="true"
/>
```

## UI Components

### Button Component (`x-button`)

#### Purpose
Provides consistent button styling and behavior.

#### Props
```php
@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'disabled' => false,
    'loading' => false
])
```

#### Variants
- **Primary**: Blue background
- **Secondary**: Gray background
- **Success**: Green background
- **Danger**: Red background
- **Warning**: Yellow background
- **Info**: Light blue background

#### Sizes
- **Sm**: Small button
- **Md**: Medium button (default)
- **Lg**: Large button

#### Structure
```html
<button 
    type="{{ $type }}"
    @if($disabled) disabled @endif
    class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200 {{ $variantClasses }} {{ $sizeClasses }}"
>
    @if($loading)
        <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    @endif
    {{ $slot }}
</button>
```

#### Usage
```html
<x-button type="submit" variant="primary" size="lg">
    Save Changes
</x-button>
```

### Card Component (`x-card`)

#### Purpose
Provides consistent card styling for content containers.

#### Props
```php
@props([
    'title' => null,
    'subtitle' => null,
    'padding' => 'p-6',
    'shadow' => 'shadow-sm'
])
```

#### Structure
```html
<div class="bg-white rounded-xl {{ $shadow }} border border-gray-100 {{ $padding }}" style="background-color: var(--card-bg); border-color: var(--border-color);">
    @if($title || $subtitle)
        <div class="mb-4">
            @if($title)
                <h3 class="text-lg font-semibold" style="color: var(--text-primary);">{{ $title }}</h3>
            @endif
            @if($subtitle)
                <p class="text-sm" style="color: var(--text-secondary);">{{ $subtitle }}</p>
            @endif
        </div>
    @endif
    {{ $slot }}
</div>
```

#### Usage
```html
<x-card title="Hostel Information" subtitle="Basic details about the hostel">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Card content -->
    </div>
</x-card>
```

## JavaScript Integration

### Component JavaScript
Each component can include its own JavaScript functionality:

```javascript
// Data table component JavaScript
function toggleFilterOffcanvas() {
    const offcanvas = document.getElementById('filterOffcanvas');
    const overlay = document.getElementById('filterOverlay');
    
    offcanvas.classList.toggle('translate-x-full');
    overlay.classList.toggle('hidden');
}

function performSearch() {
    const searchTerm = document.getElementById('tableSearch').value;
    // Implement search logic
}

function executeBulkAction() {
    const selectedIds = getSelectedIds();
    const action = document.getElementById('bulkActionSelect').value;
    // Implement bulk action logic
}
```

### Global JavaScript
Common JavaScript functionality shared across components:

```javascript
// Theme management
function toggleTheme() {
    const body = document.body;
    const themeIcon = document.getElementById('themeIcon');
    
    if (body.classList.contains('dark')) {
        body.classList.remove('dark');
        themeIcon.classList.remove('fa-sun');
        themeIcon.classList.add('fa-moon');
        localStorage.setItem('theme', 'light');
    } else {
        body.classList.add('dark');
        themeIcon.classList.remove('fa-moon');
        themeIcon.classList.add('fa-sun');
        localStorage.setItem('theme', 'dark');
    }
}

// Sidebar management
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const isMobile = window.innerWidth < 1024;
    
    if (isMobile) {
        sidebar.classList.toggle('show');
        overlay.classList.toggle('show');
    } else {
        sidebar.classList.toggle('sidebar-collapsed');
        mainContent.classList.toggle('main-content-collapsed');
    }
}
```

## Styling System

### CSS Variables
Components use CSS variables for consistent theming:

```css
:root {
    --primary-color: #3b82f6;
    --secondary-color: #6b7280;
    --success-color: #10b981;
    --danger-color: #ef4444;
    --warning-color: #f59e0b;
    --info-color: #06b6d4;
    
    --bg-primary: #ffffff;
    --bg-secondary: #f9fafb;
    --text-primary: #111827;
    --text-secondary: #6b7280;
    --border-color: #e5e7eb;
    --hover-bg: #f9fafb;
    --card-bg: #ffffff;
}

.dark {
    --bg-primary: #1f2937;
    --bg-secondary: #374151;
    --text-primary: #f9fafb;
    --text-secondary: #d1d5db;
    --border-color: #4b5563;
    --hover-bg: #374151;
    --card-bg: #1f2937;
}
```

### Tailwind Classes
Components use Tailwind CSS classes for styling:

```html
<!-- Responsive classes -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">

<!-- Theme-aware classes -->
<div class="bg-white" style="background-color: var(--card-bg);">

<!-- Interactive classes -->
<button class="hover:bg-blue-700 transition-colors duration-200">
```

## Best Practices

### Component Design
1. **Single Responsibility**: Each component should have one clear purpose
2. **Props Interface**: Define clear prop interfaces
3. **Default Values**: Provide sensible defaults
4. **Documentation**: Document component usage and props
5. **Testing**: Write tests for component behavior

### Naming Conventions
1. **Component Names**: Use kebab-case (e.g., `x-data-table`)
2. **Prop Names**: Use camelCase (e.g., `showBackButton`)
3. **CSS Classes**: Use Tailwind conventions
4. **JavaScript Functions**: Use camelCase

### Performance
1. **Lazy Loading**: Load components as needed
2. **Caching**: Cache rendered components
3. **Optimization**: Optimize component rendering
4. **Bundle Size**: Keep component size minimal

### Accessibility
1. **ARIA Labels**: Provide proper ARIA labels
2. **Keyboard Navigation**: Support keyboard navigation
3. **Screen Readers**: Ensure screen reader compatibility
4. **Color Contrast**: Maintain proper color contrast

## Testing

### Component Testing
```php
// Test component rendering
public function test_stats_card_component_renders_correctly()
{
    $component = new StatsCard('Total Hostels', '4', 'All registered hostels', 'fas fa-building', 'blue');
    
    $view = $component->render();
    
    $this->assertStringContainsString('Total Hostels', $view);
    $this->assertStringContainsString('4', $view);
}
```

### JavaScript Testing
```javascript
// Test component JavaScript
describe('Data Table Component', function() {
    it('should toggle filter offcanvas', function() {
        // Test filter toggle functionality
    });
    
    it('should perform search', function() {
        // Test search functionality
    });
});
```

## Future Enhancements

### Planned Components
- **Modal Component**: Reusable modal dialogs
- **Toast Component**: Notification toasts
- **Loading Component**: Loading states and spinners
- **Chart Component**: Data visualization
- **Calendar Component**: Calendar and date picker
- **File Upload Component**: File upload functionality

### Performance Improvements
- **Component Caching**: Cache rendered components
- **Lazy Loading**: Load components on demand
- **Bundle Optimization**: Optimize component bundles
- **Tree Shaking**: Remove unused component code

### Advanced Features
- **Component Composition**: Compose complex components
- **Slot System**: Advanced slot functionality
- **Event System**: Component event handling
- **State Management**: Component state management
- **Animation System**: Component animations
- **Internationalization**: Multi-language support
