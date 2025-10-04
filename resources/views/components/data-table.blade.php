@props([
    'title' => 'Data Table',
    'addButtonText' => 'Add New',
    'addButtonUrl' => '#',
    'columns' => [],
    'data' => [],
    'actions' => true,
    'pagination' => null,
    'filters' => [],
    'bulkActions' => [],
    'searchable' => true,
    'exportable' => false
])

<div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
    <div class="p-4 sm:p-6 border-b border-gray-100" style="border-color: var(--border-color);">
        <!-- Header Row -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
            <h2 class="text-lg sm:text-xl font-semibold" style="color: var(--text-primary);">{{ $title }}</h2>
            <div class="flex items-center gap-2">
                @if($exportable)
                    <button onclick="exportData()" class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center text-sm w-10 h-10 sm:w-auto sm:h-auto">
                        <i class="fas fa-download sm:mr-2"></i>
                        <span class="hidden sm:inline">Export</span>
                    </button>
                @endif
                @if(!empty($addButtonText) && !empty($addButtonUrl))
                    <a href="{{ $addButtonUrl }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center text-sm w-16 h-10 sm:w-auto sm:h-auto">
                        <i class="fas fa-plus sm:mr-2"></i>
                        <span class="hidden sm:inline">{{ $addButtonText }}</span>
                        <span class="sm:hidden">Add</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Search and Controls Row -->
        <div class="flex flex-col gap-3">
            <!-- Top Row: Search and Filter -->
            <div class="flex items-center gap-2">
                <!-- Search -->
                @if($searchable)
                    <div class="flex-1">
                        <div class="relative">
                            <input type="text" id="tableSearch" placeholder="Search..."
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                        </div>
                    </div>
                @endif

                <!-- Filter Button -->
                @if(!empty($filters))
                    <button onclick="toggleFilterOffcanvas()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors duration-200 flex items-center justify-center relative w-10 h-10 flex-shrink-0" title="Filters">
                        <i class="fas fa-filter text-sm"></i>
                        <span id="filterCount" class="absolute -top-1 -right-1 bg-blue-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center hidden">0</span>
                    </button>
                @endif
            </div>

            <!-- Bottom Row: Per Page and Total Rows -->
            <div class="flex items-center justify-between">
                <!-- Show Per Page -->
                <div class="flex items-center gap-2">
                    <label class="text-xs text-gray-600" style="color: var(--text-secondary);">Show:</label>
                    <select id="perPageSelect" class="border border-gray-300 rounded-lg px-2 py-1.5 text-xs focus:ring-2 focus:ring-blue-500 focus:border-transparent min-w-12"
                            style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        <option value="10">10</option>
                        <option value="25" selected>25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>

                <!-- Total Rows Info -->
                <div class="text-xs text-gray-600" style="color: var(--text-secondary);">
                    <span id="totalRows">{{ count($data) }}</span> total
                </div>
            </div>
        </div>

        <!-- Bulk Actions (Hidden by default) -->
        <div id="bulkActions" class="hidden mt-4 p-3 sm:p-4 bg-blue-50 rounded-lg border border-blue-200" style="background-color: var(--hover-bg); border-color: var(--border-color);">
            <div class="flex flex-col gap-3 sm:gap-4">
                <!-- Selection Info -->
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium" style="color: var(--text-primary);">
                        <span id="selectedCount">0</span> items selected
                    </span>
                    <button onclick="clearSelection()" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
                        <i class="fas fa-times"></i>
                        <span class="hidden sm:inline">Clear</span>
                    </button>
                </div>

                <!-- Action Controls -->
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                    <div class="flex-1">
                        <label class="block text-xs font-medium mb-1 sm:hidden" style="color: var(--text-secondary);">Action</label>
                        <select id="bulkActionSelect" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                            <option value="">Select action...</option>
                            @foreach($bulkActions as $action)
                                <option value="{{ $action['key'] }}" data-icon="{{ $action['icon'] ?? 'fas fa-cog' }}">
                                    {{ $action['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button onclick="executeBulkAction()"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition-colors duration-200 flex items-center justify-center gap-2 sm:w-auto"
                            id="executeBulkBtn" disabled>
                        <i class="fas fa-play text-xs"></i>
                        <span class="hidden sm:inline">Execute</span>
                        <span class="sm:hidden">Go</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Table (Desktop and Mobile) -->
    <div class="overflow-x-auto rounded-lg border border-gray-200" style="border-color: var(--border-color);">
        <table class="w-full border-collapse" style="min-width: 1000px; border-color: var(--border-color);">
            <thead class="bg-gray-50" style="background-color: var(--hover-bg);">
                <tr>
                    @if(!empty($bulkActions))
                        <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-16 border-r border-gray-200" style="color: var(--text-secondary); border-color: var(--border-color);">
                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4">
                        </th>
                    @endif
                    @foreach($columns as $column)
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider {{ $column['width'] ?? '' }} border-r border-gray-200" style="color: var(--text-secondary); border-color: var(--border-color);">
                            {{ $column['label'] }}
                        </th>
                    @endforeach
                    @if($actions)
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24" style="color: var(--text-secondary);">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200" style="border-color: var(--border-color);">
                @foreach($data as $index => $row)
                    <tr class="hover:bg-gray-50 transition-colors duration-200" style="background-color: var(--bg-primary);" data-row-id="{{ $row['id'] ?? $index }}">
                        @if(!empty($bulkActions))
                            <td class="px-3 py-4 text-center w-16 border-r border-gray-200" style="border-color: var(--border-color);">
                                <input type="checkbox" class="row-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4" value="{{ $row['id'] ?? $index }}">
                            </td>
                        @endif
                        @foreach($columns as $column)
                            <td class="px-3 py-4 whitespace-nowrap {{ $column['width'] ?? '' }} border-r border-gray-200" style="border-color: var(--border-color);">
                                @if(isset($column['component']))
                                    @if(is_array($row[$column['key']]))
                                        @include($column['component'], $row[$column['key']])
                                    @else
                                        @include($column['component'], ['status' => $row[$column['key']]])
                                    @endif
                                @else
                                    <div class="text-sm font-medium" style="color: var(--text-primary);">
                                        @if(isset($column['html']) && $column['html'])
                                            {!! $row[$column['key']] ?? '' !!}
                                        @else
                                            {{ $row[$column['key']] ?? '' }}
                                        @endif
                                    </div>
                                @endif
                            </td>
                        @endforeach
                        @if($actions)
                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium w-24">
                                <div class="flex items-center space-x-2">
                                    @if(isset($row['view_url']))
                                        <a href="{{ $row['view_url'] }}" class="text-blue-600 hover:text-blue-900 transition-colors duration-200" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endif
                                    @if(isset($row['pdf_url']))
                                        <a href="{{ $row['pdf_url'] }}" target="_blank" class="text-green-600 hover:text-green-900 transition-colors duration-200" title="View PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    @endif
                                    @if(isset($row['pdf_download_url']))
                                        <a href="{{ $row['pdf_download_url'] }}" class="text-purple-600 hover:text-purple-900 transition-colors duration-200" title="Download PDF">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    @endif
                                    @if(isset($row['edit_url']))
                                        <a href="{{ $row['edit_url'] }}" class="text-orange-600 hover:text-orange-900 transition-colors duration-200" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                    @if(isset($row['delete_url']))
                                        <button onclick="deleteItem('{{ $row['delete_url'] }}')" class="text-red-600 hover:text-red-900 transition-colors duration-200" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>


    <!-- Pagination -->
    @if($pagination)
        <div class="px-6 py-4 border-t border-gray-200" style="border-color: var(--border-color);">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="text-sm text-gray-700" style="color: var(--text-secondary);">
                    Showing {{ $pagination['from'] ?? 1 }} to {{ $pagination['to'] ?? count($data) }} of {{ $pagination['total'] ?? count($data) }} results
                </div>
                <div class="flex items-center gap-2">
                    @if(isset($pagination['links']))
                        @foreach($pagination['links'] as $link)
                            @if($link['url'])
                                <a href="{{ $link['url'] }}"
                                   class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200 {{ $link['active'] ? 'bg-blue-500 text-white border-blue-500' : 'text-gray-700' }}"
                                   style="background-color: {{ $link['active'] ? 'var(--primary-color, #3b82f6)' : 'var(--bg-secondary)' }}; border-color: var(--border-color); color: {{ $link['active'] ? 'white' : 'var(--text-primary)' }};">
                                    {!! $link['label'] !!}
                                </a>
                            @else
                                <span class="px-3 py-2 text-sm border border-gray-300 rounded-lg text-gray-400"
                                      style="border-color: var(--border-color);">
                                    {!! $link['label'] !!}
                                </span>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Filter Offcanvas -->
@if(!empty($filters))
    <div id="filterOffcanvas" class="fixed inset-y-0 right-0 w-80 bg-white shadow-xl transform translate-x-full transition-transform duration-300 ease-in-out z-50"
         style="background-color: var(--card-bg);">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold" style="color: var(--text-primary);">Filters</h3>
                <button onclick="toggleFilterOffcanvas()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form id="filterForm" class="space-y-4">
                @foreach($filters as $filter)
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                            {{ $filter['label'] }}
                        </label>
                        @if($filter['type'] === 'select')
                            <select name="{{ $filter['key'] }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                                <option value="">All {{ $filter['label'] }}</option>
                                @foreach($filter['options'] as $option)
                                    <option value="{{ $option['value'] }}" {{ (isset($filter['value']) && $filter['value'] == $option['value']) ? 'selected' : '' }}>{{ $option['label'] }}</option>
                                @endforeach
                            </select>
                        @elseif($filter['type'] === 'date')
                            <input type="date" name="{{ $filter['key'] }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        @elseif($filter['type'] === 'range')
                            <div class="flex items-center gap-2">
                                <input type="number" name="{{ $filter['key'] }}_min" placeholder="Min"
                                       class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                                <span class="text-gray-500">to</span>
                                <input type="number" name="{{ $filter['key'] }}_max" placeholder="Max"
                                       class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                            </div>
                        @endif
                    </div>
                @endforeach

                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="applyFilters()"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                        Apply Filters
                    </button>
                    <button type="button" onclick="clearFilters()"
                            class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition-colors duration-200">
                        Clear
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Offcanvas Overlay -->
    <div id="filterOverlay" class="fixed inset-0 z-40 hidden" style="background-color: rgba(0, 0, 0, 0.02); backdrop-filter: blur(2px);" onclick="toggleFilterOffcanvas()"></div>
@endif

@push('scripts')
<script>
// Table functionality
let selectedItems = new Set();
let currentFilters = {};

// Add CSS for selected row highlighting
const style = document.createElement('style');
style.textContent = `
    .row-selected {
        background-color: rgba(59, 130, 246, 0.1) !important;
        border-left: 3px solid #3b82f6;
    }
    [data-theme="dark"] .row-selected {
        background-color: rgba(59, 130, 246, 0.2) !important;
    }
`;
document.head.appendChild(style);

// Delete item function
function deleteItem(url) {
    if (confirm('Are you sure you want to delete this item?')) {
        // Implement delete functionality
        console.log('Delete:', url);
    }
}

// Search functionality
document.getElementById('tableSearch')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

// Select all functionality
document.getElementById('selectAll')?.addEventListener('change', function(e) {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = e.target.checked;
        const row = checkbox.closest('tr');
        if (e.target.checked) {
            selectedItems.add(checkbox.value);
            row.classList.add('row-selected');
        } else {
            selectedItems.delete(checkbox.value);
            row.classList.remove('row-selected');
        }
    });
    updateBulkActions();
});

// Individual checkbox functionality
document.querySelectorAll('.row-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function(e) {
        const row = e.target.closest('tr');
        if (e.target.checked) {
            selectedItems.add(e.target.value);
            row.classList.add('row-selected');
        } else {
            selectedItems.delete(e.target.value);
            row.classList.remove('row-selected');
        }
        updateSelectAllState();
        updateBulkActions();
    });
});

// Update select all checkbox state
function updateSelectAllState() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.row-checkbox');
    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');

    if (selectAll) {
        selectAll.checked = checkedBoxes.length === checkboxes.length;
        selectAll.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < checkboxes.length;
    }
}

// Update bulk actions visibility
function updateBulkActions() {
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');

    if (bulkActions && selectedCount) {
        if (selectedItems.size > 0) {
            bulkActions.classList.remove('hidden');
            selectedCount.textContent = selectedItems.size;
        } else {
            bulkActions.classList.add('hidden');
        }
    }

    updateExecuteButton();
}

// Clear selection
function clearSelection() {
    selectedItems.clear();
    document.querySelectorAll('.row-checkbox').forEach(checkbox => {
        checkbox.checked = false;
        const row = checkbox.closest('tr');
        row.classList.remove('row-selected');
    });
    document.getElementById('selectAll').checked = false;

    // Reset bulk action dropdown
    const selectElement = document.getElementById('bulkActionSelect');
    if (selectElement) {
        selectElement.value = '';
    }

    updateBulkActions();
    updateExecuteButton();
}

// Execute bulk action
function executeBulkAction() {
    const selectElement = document.getElementById('bulkActionSelect');
    const action = selectElement.value;

    if (!action) {
        alert('Please select an action first');
        return;
    }

    if (selectedItems.size === 0) {
        alert('Please select items first');
        return;
    }

    const selectedIds = Array.from(selectedItems);

    switch(action) {
        case 'delete':
            if (confirm(`Are you sure you want to delete ${selectedIds.length} items?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("admin.tenant-profile-requests.bulk-action") }}';

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'bulk_action';
                actionInput.value = action;
                form.appendChild(actionInput);

                selectedIds.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'selected_ids[]';
                    input.value = id;
                    form.appendChild(input);
                });

                document.body.appendChild(form);
                form.submit();
            }
            break;
        case 'export':
            console.log('Bulk export:', selectedIds);
            // Implement bulk export
            break;
        case 'activate':
            console.log('Bulk activate:', selectedIds);
            // Implement bulk activate
            clearSelection();
            break;
        case 'deactivate':
            console.log('Bulk deactivate:', selectedIds);
            // Implement bulk deactivate
            clearSelection();
            break;
        case 'approve':
        case 'reject':
            // Handle profile update request bulk actions
            if (confirm(`Are you sure you want to ${action} ${selectedIds.length} request(s)?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("admin.tenant-profile-requests.bulk-action") }}';

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'bulk_action';
                actionInput.value = action;
                form.appendChild(actionInput);

                selectedIds.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'selected_ids[]';
                    input.value = id;
                    form.appendChild(input);
                });

                document.body.appendChild(form);
                form.submit();
            }
            break;
        default:
            console.log('Bulk action:', action, selectedIds);
    }

    // Reset dropdown
    selectElement.value = '';
    updateExecuteButton();
}

// Update execute button state
function updateExecuteButton() {
    const selectElement = document.getElementById('bulkActionSelect');
    const executeBtn = document.getElementById('executeBulkBtn');

    if (selectElement && executeBtn) {
        const hasSelection = selectedItems.size > 0;
        const hasAction = selectElement.value !== '';

        executeBtn.disabled = !hasSelection || !hasAction;

        if (hasSelection && hasAction) {
            executeBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            executeBtn.classList.add('hover:bg-blue-700');
        } else {
            executeBtn.classList.add('opacity-50', 'cursor-not-allowed');
            executeBtn.classList.remove('hover:bg-blue-700');
        }
    }
}

// Filter offcanvas functionality
function toggleFilterOffcanvas() {
    const offcanvas = document.getElementById('filterOffcanvas');
    const overlay = document.getElementById('filterOverlay');

    if (offcanvas && overlay) {
        const isOpen = !offcanvas.classList.contains('translate-x-full');

        if (isOpen) {
            offcanvas.classList.add('translate-x-full');
            overlay.classList.add('hidden');
        } else {
            offcanvas.classList.remove('translate-x-full');
            overlay.classList.remove('hidden');
        }
    }
}

// Apply filters
function applyFilters() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    const filters = {};

    for (let [key, value] of formData.entries()) {
        if (value) {
            filters[key] = value;
        }
    }

    currentFilters = filters;
    updateFilterCount();

    // Apply filters to table
    console.log('Applying filters:', filters);
    // Implement filter logic here

    toggleFilterOffcanvas();
}

// Clear filters
function clearFilters() {
    const form = document.getElementById('filterForm');
    form.reset();
    currentFilters = {};
    updateFilterCount();

    // Clear filters from table
    console.log('Clearing filters');
    // Implement clear filter logic here
}

// Update filter count badge
function updateFilterCount() {
    const filterCount = document.getElementById('filterCount');
    const activeFilters = Object.keys(currentFilters).length;

    if (filterCount) {
        if (activeFilters > 0) {
            filterCount.textContent = activeFilters;
            filterCount.classList.remove('hidden');
        } else {
            filterCount.classList.add('hidden');
        }
    }
}

// Per page functionality
document.getElementById('perPageSelect')?.addEventListener('change', function(e) {
    const perPage = e.target.value;
    console.log('Per page changed to:', perPage);
    // Implement per page logic here
    // This would typically reload the page with new per_page parameter
});

// Export functionality
function exportData() {
    console.log('Exporting data');
    // Implement export functionality here
}

// Update overlay theme
function updateOverlayTheme() {
    const overlay = document.getElementById('filterOverlay');
    if (overlay) {
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        if (isDark) {
            overlay.style.backgroundColor = 'rgba(255, 255, 255, 0.02)';
        } else {
            overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.02)';
        }
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateBulkActions();
    updateFilterCount();
    updateOverlayTheme();
    updateExecuteButton();

    // Add event listener for bulk action dropdown
    const bulkActionSelect = document.getElementById('bulkActionSelect');
    if (bulkActionSelect) {
        bulkActionSelect.addEventListener('change', updateExecuteButton);
    }
});

// Listen for theme changes
document.addEventListener('themeChanged', function() {
    updateOverlayTheme();
});
</script>
@endpush
