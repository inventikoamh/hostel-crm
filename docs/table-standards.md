# Data Table Standards - Hostel CRM

## Overview
This document defines the standard structure and implementation for all data tables in the Hostel CRM system to ensure consistency across the application.

## Table Component Structure

### Basic Usage
```php
<x-data-table
    title="Table Title"
    add-button-text="Add New Item"
    add-button-url="{{ route('items.create') }}"
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

### Required Props
- `title` - Table title (string)
- `columns` - Column definitions (array)
- `data` - Table data (array)

### Optional Props
- `addButtonText` - Text for add button (string, default: "Add New")
- `addButtonUrl` - URL for add button (string, default: "#")
- `actions` - Show action column (boolean, default: true)
- `searchable` - Enable search functionality (boolean, default: true)
- `exportable` - Show export button (boolean, default: false)
- `filters` - Filter configuration (array, default: [])
- `bulkActions` - Bulk action configuration (array, default: [])
- `pagination` - Pagination data (array, default: null)

## Column Configuration

### Standard Column Structure
```php
$columns = [
    [
        'key' => 'column_key',
        'label' => 'Column Label',
        'component' => 'components.component-name', // Optional
        'sortable' => true, // Optional
        'width' => 'w-32' // Optional - Recommended for mobile scrolling
    ]
];
```

### Recommended Column Widths
- **Checkbox Column:** `w-12` (48px) - For bulk actions
- **Text Columns:** `w-20` to `w-32` (80px to 128px)
- **Wide Content:** `w-64` (256px) - For names, descriptions
- **Actions Column:** `w-24` (96px) - For action buttons

### Column Types
1. **Text Column** - Simple text display
2. **Component Column** - Custom component rendering
3. **Status Column** - Uses status-badge component
4. **Date Column** - Formatted date display
5. **Currency Column** - Formatted currency display

### Standard Column Keys
- `id` - Primary key (always include)
- `name` - Item name
- `email` - Email address
- `phone` - Phone number
- `status` - Status (active/inactive/pending)
- `created_at` - Creation date
- `updated_at` - Last update date

## Data Structure

### Required Data Fields
Every table row must include:
```php
[
    'id' => $item['id'], // Required for bulk actions
    'view_url' => route('items.show', $item['id']),
    'edit_url' => route('items.edit', $item['id']),
    'delete_url' => route('items.destroy', $item['id'])
]
```

### Component Data Structure
For columns using components:
```php
// For tenant-info component
'tenant_info' => ['tenant' => $tenant]

// For status-badge component
'status' => $item['status'] // Direct value

// For custom components
'custom_field' => ['data' => $item['custom_field']]
```

## Filter Configuration

### Filter Types
1. **Select Filter** - Dropdown selection
2. **Date Filter** - Date picker
3. **Range Filter** - Min/Max number inputs
4. **Text Filter** - Text input

### Filter Structure
```php
$filters = [
    [
        'key' => 'status',
        'label' => 'Status',
        'type' => 'select',
        'options' => [
            ['value' => 'active', 'label' => 'Active'],
            ['value' => 'inactive', 'label' => 'Inactive']
        ]
    ],
    [
        'key' => 'amount',
        'label' => 'Amount',
        'type' => 'range'
    ],
    [
        'key' => 'date',
        'label' => 'Date',
        'type' => 'date'
    ]
];
```

## Bulk Actions Configuration

### Standard Bulk Actions
```php
$bulkActions = [
    [
        'key' => 'activate',
        'label' => 'Activate',
        'icon' => 'fas fa-check',
        'class' => 'bg-green-100 hover:bg-green-200 text-green-700'
    ],
    [
        'key' => 'deactivate',
        'label' => 'Deactivate',
        'icon' => 'fas fa-times',
        'class' => 'bg-red-100 hover:bg-red-200 text-red-700'
    ],
    [
        'key' => 'export',
        'label' => 'Export',
        'icon' => 'fas fa-download',
        'class' => 'bg-blue-100 hover:bg-blue-200 text-blue-700'
    ],
    [
        'key' => 'delete',
        'label' => 'Delete',
        'icon' => 'fas fa-trash',
        'class' => 'bg-red-100 hover:bg-red-200 text-red-700'
    ]
];
```

### Bulk Action Colors
- **Green** - Positive actions (activate, approve)
- **Red** - Destructive actions (delete, deactivate)
- **Blue** - Neutral actions (export, view)
- **Yellow** - Warning actions (suspend, hold)

## Pagination Structure

### Pagination Data
```php
$pagination = [
    'from' => 1,
    'to' => 25,
    'total' => 100,
    'current_page' => 1,
    'per_page' => 25,
    'links' => [
        ['url' => null, 'label' => '&laquo; Previous', 'active' => false],
        ['url' => '#', 'label' => '1', 'active' => true],
        ['url' => '#', 'label' => '2', 'active' => false],
        ['url' => '#', 'label' => 'Next &raquo;', 'active' => false]
    ]
];
```

## Component Standards

### Required Components
1. **tenant-info** - For displaying tenant information
2. **status-badge** - For status indicators
3. **data-table** - Main table component

### Component Props
All components must follow this pattern:
```php
@props(['propName' => 'defaultValue'])
```

## Styling Standards

### CSS Classes
- Use Tailwind CSS classes
- Follow the design system colors
- Use CSS variables for theme support
- Maintain responsive design

### Table Borders
- **Complete Borders**: All table cells have borders
- **Border Collapse**: Use `border-collapse` for clean appearance
- **Theme Support**: Use `var(--border-color)` for theme-aware borders
- **Consistent Styling**: All cells use `border border-gray-200`

### Checkbox Styling
- **Size**: `w-4 h-4` (16px × 16px) for better visibility
- **Centering**: Use `text-center` for perfect alignment
- **Column Width**: `w-12` (48px) for adequate space
- **Padding**: Only vertical padding (`py-3`/`py-4`)

### Row Selection Highlighting
- **Selected Rows**: Blue background with left border
- **Light Theme**: `rgba(59, 130, 246, 0.1)` background
- **Dark Theme**: `rgba(59, 130, 246, 0.2)` background
- **Border**: 3px solid blue left border

### Color Scheme
- **Primary**: Blue (#3b82f6)
- **Success**: Green (#16a34a)
- **Warning**: Yellow (#d97706)
- **Danger**: Red (#dc2626)
- **Info**: Blue (#0ea5e9)

## JavaScript Standards

### Required Functions
- `deleteItem(url)` - Delete single item
- `bulkAction(action)` - Handle bulk actions
- `toggleFilterOffcanvas()` - Toggle filter panel
- `applyFilters()` - Apply filter changes
- `clearFilters()` - Clear all filters
- `clearSelection()` - Clear bulk selection
- `updateOverlayTheme()` - Update overlay for theme changes
- `updateBulkActions()` - Show/hide bulk action bar
- `updateSelectAllState()` - Update select all checkbox state

### Event Handling
- Use event delegation for dynamic content
- Implement proper error handling
- Show loading states for async operations
- Provide user feedback for all actions
- Handle theme change events for overlay updates
- Manage checkbox state changes for row highlighting

## Mobile Responsiveness

### Breakpoints
- **Mobile**: < 640px
- **Tablet**: 640px - 1024px
- **Desktop**: > 1024px

### Mobile Table Strategy
- **Horizontal Scrolling**: Table scrolls horizontally on all devices
- **Minimum Width**: Set `min-width: 800px` for proper scrolling
- **No Card Layout**: Consistent table experience across devices
- **Touch-Friendly**: Larger checkboxes and buttons for mobile

### Mobile Layout Optimization
- **Two-Row Layout**: Compact search/filter controls to save vertical space
- **Top Row**: Search input (flex-1) + Filter button (40px × 40px)
- **Bottom Row**: Per page selector + Total rows count (justified between)
- **Space Efficiency**: 50% less vertical space than previous stacked layout

### Button Sizing Standards
- **Filter Button**: `w-10 h-10` (40px × 40px) - Perfect square
- **Export Button**: `w-10 h-10` (40px × 40px) - Perfect square
- **Add Button**: `w-16 h-10` (64px × 40px) - Wider for content
- **All Buttons**: Same height (`h-10`) for perfect alignment
- **Desktop**: `sm:w-auto sm:h-auto` for auto-sizing

### Mobile Adaptations
- **Compact Controls**: Smaller text (`text-xs`) and padding (`py-1.5`)
- **Icon-Only Buttons**: Mobile buttons show only icons, desktop shows text
- **Flexible Search**: Search input takes available space next to filter
- **Total Rows Display**: Shows count (e.g., "25 total") for context
- **Touch-Friendly**: All interactive elements meet minimum 40px touch target

## Accessibility Standards

### Required Features
- Keyboard navigation support
- Screen reader compatibility
- Proper ARIA labels
- Focus management
- Color contrast compliance

### ARIA Labels
```html
<button aria-label="Delete item" title="Delete item">
    <i class="fas fa-trash"></i>
</button>
```

## Performance Standards

### Optimization
- Implement virtual scrolling for large datasets
- Use pagination for data > 100 items
- Lazy load images and heavy content
- Debounce search inputs
- Cache filter results

### Loading States
- Show skeleton loaders during data fetch
- Display progress indicators for bulk operations
- Implement error boundaries for failed requests

## Testing Standards

### Required Tests
- Unit tests for all JavaScript functions
- Integration tests for table interactions
- Accessibility tests for keyboard navigation
- Performance tests for large datasets
- Visual regression tests for UI consistency

## Filter Offcanvas Standards

### Overlay Styling
- **Ultra-Transparent**: 2% opacity for minimal intrusion
- **Theme Support**: Different colors for light/dark modes
- **Blur Effect**: 2px backdrop blur for subtle morphing
- **Light Theme**: `rgba(0, 0, 0, 0.02)` with blur
- **Dark Theme**: `rgba(255, 255, 255, 0.02)` with blur

### Offcanvas Behavior
- **Slide Animation**: Smooth slide from right
- **Click Outside**: Close on overlay click
- **Theme Updates**: Automatic overlay color changes
- **Responsive**: Full height on all devices

## Layout Standards

### Header Section
- **Title**: Left-aligned with responsive typography
- **Action Buttons**: Right-aligned with consistent sizing
- **Button Hierarchy**: Export (secondary) + Add (primary)

### Search and Controls Section
- **Two-Row Layout**: Optimized for mobile space efficiency
- **Top Row**: Search input + Filter button (side by side)
- **Bottom Row**: Per page selector + Total rows count
- **Responsive Behavior**: Maintains layout across all screen sizes

### Button Consistency
- **Mobile Dimensions**: All buttons use consistent sizing
- **Icon Alignment**: Perfect centering with `flex items-center justify-center`
- **Touch Targets**: Minimum 40px × 40px for mobile accessibility
- **Visual Harmony**: Same border radius, transitions, and hover effects

### Space Optimization
- **Vertical Space**: Reduced by 50% with two-row layout
- **Horizontal Space**: Efficient use of available width
- **Content Priority**: Most important controls (search, filter) on top row
- **Information Display**: Secondary info (per page, totals) on bottom row

## Examples

### Complete Table Implementation
```php
// In controller
public function index()
{
    $tenants = collect([
        [
            'id' => 1,
            'tenant_info' => ['tenant' => ['name' => 'John Doe', 'email' => 'john@example.com']],
            'room' => 'A101',
            'rent_amount' => '$500',
            'status' => 'active',
            'move_in_date' => '2024-01-15',
            'view_url' => route('tenants.show', 1),
            'edit_url' => route('tenants.edit', 1),
            'delete_url' => route('tenants.destroy', 1)
        ]
    ]);
    
    $columns = [
        ['key' => 'tenant_info', 'label' => 'Tenant', 'component' => 'components.tenant-info', 'width' => 'w-64'],
        ['key' => 'room', 'label' => 'Room', 'width' => 'w-20'],
        ['key' => 'rent_amount', 'label' => 'Rent', 'width' => 'w-24'],
        ['key' => 'status', 'label' => 'Status', 'component' => 'components.status-badge', 'width' => 'w-24'],
        ['key' => 'move_in_date', 'label' => 'Move-in Date', 'width' => 'w-32']
    ];
    
    $filters = [
        ['key' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => [
            ['value' => 'active', 'label' => 'Active'],
            ['value' => 'inactive', 'label' => 'Inactive']
        ]]
    ];
    
    $bulkActions = [
        ['key' => 'activate', 'label' => 'Activate', 'icon' => 'fas fa-check'],
        ['key' => 'deactivate', 'label' => 'Deactivate', 'icon' => 'fas fa-times']
    ];
    
    $pagination = [
        'from' => 1, 'to' => 25, 'total' => 100,
        'current_page' => 1, 'per_page' => 25
    ];
    
    return view('tenants.index', compact('tenants', 'columns', 'filters', 'bulkActions', 'pagination'));
}

// In view
<x-data-table
    title="All Tenants"
    add-button-text="Add Tenant"
    add-button-url="{{ route('tenants.create') }}"
    :columns="$columns"
    :data="$tenants"
    :actions="true"
    :searchable="true"
    :exportable="true"
    :filters="$filters"
    :bulk-actions="$bulkActions"
    :pagination="$pagination"
/>
```

### Column Configuration Example
```php
$columns = [
    [
        'key' => 'tenant_info',
        'label' => 'Tenant',
        'component' => 'components.tenant-info',
        'width' => 'w-64'
    ],
    [
        'key' => 'room',
        'label' => 'Room',
        'width' => 'w-20'
    ],
    [
        'key' => 'status',
        'label' => 'Status',
        'component' => 'components.status-badge',
        'width' => 'w-24'
    ]
];
```

### Notification System Tables

#### Notifications Index Table
```php
// In NotificationController
public function index(Request $request)
{
    $notifications = Notification::with('notifiable')->latest()->paginate(15);
    
    $data = $notifications->map(function ($notification) {
        return [
            'id' => $notification->id,
            'type' => $notification->type_display,
            'title' => $notification->title ?? $notification->data['subject'] ?? 'No Title',
            'recipient_email' => $notification->recipient_email,
            'status' => $notification->status,
            'created_at' => $notification->created_at->format('M j, Y g:i A'),
            'sent_at' => $notification->sent_at ? $notification->sent_at->format('M j, Y g:i A') : 'Not sent',
            'retry_count' => $notification->retry_count ?? 0,
        ];
    })->toArray();

    $columns = [
        ['key' => 'id', 'label' => 'ID', 'sortable' => true],
        ['key' => 'type', 'label' => 'Type', 'sortable' => true],
        ['key' => 'title', 'label' => 'Title', 'sortable' => true],
        ['key' => 'recipient_email', 'label' => 'Recipient', 'sortable' => true],
        ['key' => 'status', 'label' => 'Status', 'sortable' => true, 'component' => 'components.status-badge'],
        ['key' => 'created_at', 'label' => 'Created', 'sortable' => true],
        ['key' => 'sent_at', 'label' => 'Sent At', 'sortable' => true],
        ['key' => 'retry_count', 'label' => 'Retries', 'sortable' => true],
    ];

    $filters = [
        [
            'key' => 'type',
            'label' => 'Type',
            'type' => 'select',
            'options' => [
                ['value' => '', 'label' => 'All'],
                ['value' => 'tenant_added', 'label' => 'Tenant Added'],
                ['value' => 'enquiry_received_admin', 'label' => 'Enquiry Received (Admin)'],
                ['value' => 'invoice_created', 'label' => 'Invoice Created'],
                ['value' => 'payment_received', 'label' => 'Payment Received'],
            ],
            'value' => $request->type
        ],
        [
            'key' => 'status',
            'label' => 'Status',
            'type' => 'select',
            'options' => [
                ['value' => '', 'label' => 'All'],
                ['value' => 'pending', 'label' => 'Pending'],
                ['value' => 'sent', 'label' => 'Sent'],
                ['value' => 'failed', 'label' => 'Failed'],
                ['value' => 'scheduled', 'label' => 'Scheduled']
            ],
            'value' => $request->status
        ],
    ];

    return view('notifications.index', compact('data', 'columns', 'filters', 'notifications'));
}
```

#### Notification Settings Table
```php
// In NotificationController settings method
public function settings(Request $request)
{
    $settings = NotificationSetting::latest()->paginate(10);
    
    $data = $settings->map(function ($setting) {
        return [
            'id' => $setting->id,
            'notification_type' => $setting->notification_type,
            'name' => $setting->name,
            'enabled' => $setting->enabled,
            'recipient_type' => $setting->recipient_type,
            'recipient_email' => $setting->recipient_email,
            'send_immediately' => $setting->send_immediately,
            'delay_minutes' => $setting->delay_minutes,
            'created_at' => $setting->created_at->format('M j, Y H:i'),
        ];
    })->toArray();

    $columns = [
        ['key' => 'id', 'label' => '#'],
        ['key' => 'name', 'label' => 'Name'],
        ['key' => 'notification_type', 'label' => 'Type'],
        ['key' => 'enabled', 'label' => 'Enabled', 'component' => 'components.status-badge'],
        ['key' => 'recipient_type', 'label' => 'Recipient Type'],
        ['key' => 'recipient_email', 'label' => 'Recipient Email'],
        ['key' => 'send_immediately', 'label' => 'Immediate', 'component' => 'components.status-badge'],
        ['key' => 'delay_minutes', 'label' => 'Delay (min)'],
        ['key' => 'created_at', 'label' => 'Created At'],
    ];

    return view('notifications.settings', compact('data', 'columns', 'settings'));
}
```

### Button Layout Standards
```html
<!-- Header Action Buttons -->
<div class="flex items-center gap-2">
    <!-- Export Button: Perfect Square -->
    <button class="w-10 h-10 sm:w-auto sm:h-auto">
        <i class="fas fa-download sm:mr-2"></i>
        <span class="hidden sm:inline">Export</span>
    </button>
    
    <!-- Add Button: Wider for Content -->
    <a class="w-16 h-10 sm:w-auto sm:h-auto">
        <i class="fas fa-plus sm:mr-2"></i>
        <span class="hidden sm:inline">Add Tenant</span>
        <span class="sm:hidden">Add</span>
    </a>
</div>

<!-- Search and Filter Row -->
<div class="flex items-center gap-2">
    <!-- Search: Flexible Width -->
    <div class="flex-1">
        <input class="w-full pl-10 pr-4 py-2 text-sm" />
    </div>
    
    <!-- Filter: Fixed Square -->
    <button class="w-10 h-10 flex-shrink-0">
        <i class="fas fa-filter text-sm"></i>
    </button>
</div>

<!-- Per Page and Total Row -->
<div class="flex items-center justify-between">
    <div class="flex items-center gap-2">
        <label class="text-xs">Show:</label>
        <select class="px-2 py-1.5 text-xs min-w-12">...</select>
    </div>
    <div class="text-xs">25 total</div>
</div>
```

## Recent Updates (2024)

### Financial Module Integration
- **Invoice System**: Added comprehensive invoicing with PDF generation
- **Payment System**: Multi-method payment processing with verification
- **Amenity Usage**: Daily attendance-style usage tracking
- **Bulk Operations**: Enhanced bulk actions for all financial modules
- **Status Management**: Improved status badges and workflow indicators

### Notification System Integration
- **Notification Management**: Complete notification tracking and management
- **Settings Configuration**: Configurable notification preferences and templates
- **Email Templates**: Responsive email templates with dynamic content
- **Delivery Tracking**: Real-time notification status monitoring
- **Retry Mechanism**: Automatic retry for failed notifications
- **Component Integration**: Status badges and action buttons for notifications

### Enhanced Features
- **PDF Actions**: View, download, and email PDF invoices
- **Usage Analytics**: Interactive charts and reporting capabilities
- **Verification System**: Multi-level payment verification workflow
- **Export Functionality**: CSV export for all major data tables
- **Mobile Optimization**: Improved responsive design for all tables

### Component Improvements
- **Data Table**: Enhanced with better mobile responsiveness
- **Status Badges**: Extended support for financial statuses
- **Action Buttons**: Improved button consistency and mobile layout
- **Filter System**: Advanced filtering with date ranges and multi-select
- **Search Enhancement**: Debounced search with better performance

## Maintenance

### Regular Updates
- Review and update standards quarterly
- Test new features against existing standards
- Update documentation for any changes
- Ensure backward compatibility

### Version Control
- Tag major standard changes
- Maintain changelog for standards
- Document breaking changes
- Provide migration guides
