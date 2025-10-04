@extends('layouts.app')

@section('title', 'Usage Reports')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold mb-1" style="color: var(--text-primary);">Usage Reports</h1>
            <p class="text-sm" style="color: var(--text-secondary);">Analyze amenity usage patterns and generate reports</p>
        </div>
        <a href="{{ route('amenity-usage.index') }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium rounded-lg transition-colors duration-200">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Records
        </a>
    </div>

    <!-- Debug Info -->

    <!-- Report Filters -->
    <div class="bg-white rounded-xl shadow-sm border p-6 mb-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
        <form id="reportFilters" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label for="report_type" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Report Type</label>
                <select id="report_type" name="report_type" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                    <option value="monthly">Monthly Summary</option>
                    <option value="daily">Daily Usage</option>
                    <option value="tenant">By Tenant</option>
                    <option value="amenity">By Amenity</option>
                </select>
            </div>
            <div>
                <label for="start_date" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Start Date</label>
                <input type="date" id="start_date" name="start_date" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       value="{{ now()->startOfMonth()->format('Y-m-d') }}"
                       style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">End Date</label>
                <input type="date" id="end_date" name="end_date" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       value="{{ now()->format('Y-m-d') }}"
                       style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
            </div>
            <div>
                <button type="button" onclick="generateReport()" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-chart-bar mr-2"></i>
                    Generate Report
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6" id="summaryCards" style="display: none;">
        <div class="rounded-lg shadow-lg p-4" style="background-color: var(--card-bg); border: 1px solid var(--border-color);">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="bg-blue-100 rounded-lg p-3">
                        <i class="fas fa-calendar-check text-blue-600 text-lg"></i>
                    </div>
                </div>
                <div class="flex-grow ml-3">
                    <div class="text-sm" style="color: var(--text-secondary);">Total Usage Days</div>
                    <div class="text-2xl font-bold" style="color: var(--text-primary);" id="totalUsageDays">-</div>
                </div>
            </div>
        </div>
        <div class="rounded-lg shadow-lg p-4" style="background-color: var(--card-bg); border: 1px solid var(--border-color);">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="bg-green-100 rounded-lg p-3">
                        <i class="fas fa-rupee-sign text-green-600 text-lg"></i>
                    </div>
                </div>
                <div class="flex-grow ml-3">
                    <div class="text-sm" style="color: var(--text-secondary);">Total Amount</div>
                    <div class="text-2xl font-bold" style="color: var(--text-primary);" id="totalAmount">-</div>
                </div>
            </div>
        </div>
        <div class="rounded-lg shadow-lg p-4" style="background-color: var(--card-bg); border: 1px solid var(--border-color);">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="bg-blue-100 rounded-lg p-3">
                        <i class="fas fa-users text-blue-600 text-lg"></i>
                    </div>
                </div>
                <div class="flex-grow ml-3">
                    <div class="text-sm" style="color: var(--text-secondary);">Active Tenants</div>
                    <div class="text-2xl font-bold" style="color: var(--text-primary);" id="activeTenants">-</div>
                </div>
            </div>
        </div>
        <div class="rounded-lg shadow-lg p-4" style="background-color: var(--card-bg); border: 1px solid var(--border-color);">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="bg-yellow-100 rounded-lg p-3">
                        <i class="fas fa-chart-line text-yellow-600 text-lg"></i>
                    </div>
                </div>
                <div class="flex-grow ml-3">
                    <div class="text-sm" style="color: var(--text-secondary);">Avg Daily Usage</div>
                    <div class="text-2xl font-bold" style="color: var(--text-primary);" id="avgDailyUsage">-</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Container -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-4" id="chartContainer" style="background-color: var(--card-bg); border-color: var(--border-color); display: none;">
        <h5 class="mb-4" style="color: var(--text-primary);" id="chartTitle">Usage Chart</h5>
        <canvas id="usageChart" width="400" height="200"></canvas>
    </div>

    <!-- Report Table -->
    <div class="rounded-xl shadow-sm border" id="reportTable" style="background-color: var(--card-bg); border-color: var(--border-color); display: none;">
        <div class="p-6 border-b" style="border-color: var(--border-color);">
            <div class="flex justify-between items-center">
                <h5 class="text-lg font-semibold mb-0" style="color: var(--text-primary);" id="tableTitle">Report Data</h5>
                <button type="button" onclick="exportReport()" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition-colors duration-200 flex items-center">
                    <i class="fas fa-download mr-2"></i>
                    Export CSV
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full" id="reportDataTable">
                <thead class="text-left" style="background-color: var(--bg-secondary);">
                    <!-- Dynamic headers will be inserted here -->
                </thead>
                <tbody id="reportTableBody" class="divide-y" style="border-color: var(--border-color);">
                    <!-- Dynamic data will be inserted here -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Loading State -->
    <div class="text-center py-12" id="loadingState" style="display: none;">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        <p class="mt-4" style="color: var(--text-secondary);">Generating report...</p>
    </div>

    <!-- No Data State -->
    <div class="rounded-lg shadow-lg p-12 text-center" id="noDataState" style="background-color: var(--card-bg); border: 1px solid var(--border-color); display: none;">
        <i class="fas fa-chart-bar text-6xl mb-4" style="color: var(--text-secondary);"></i>
        <h3 class="text-xl font-semibold mb-2" style="color: var(--text-primary);">No Data Available</h3>
        <p style="color: var(--text-secondary);">No usage records found for the selected criteria. Try adjusting your filters.</p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let usageChart = null;

async function generateReport() {
    const reportType = document.getElementById('report_type').value;
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;

    if (!startDate || !endDate) {
        alert('Please select both start and end dates');
        return;
    }

    if (new Date(startDate) > new Date(endDate)) {
        alert('Start date cannot be after end date');
        return;
    }

    // Show loading state
    showLoadingState();

    try {
        const url = `{{ route('amenity-usage.reports') }}?report_type=${reportType}&start_date=${startDate}&end_date=${endDate}`;

        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            console.error('CSRF token not found');
            alert('CSRF token not found. Please refresh the page.');
            hideLoadingState();
            return;
        }

        const response = await fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken.getAttribute('content')
            }
        });

        const data = await response.json();

        if (data.success) {
            displayReport(data.data, reportType);
        } else {
            showNoDataState();
        }
    } catch (error) {
        showNoDataState();
    }
}

function showLoadingState() {
    document.getElementById('summaryCards').style.display = 'none';
    document.getElementById('chartContainer').style.display = 'none';
    document.getElementById('reportTable').style.display = 'none';
    document.getElementById('noDataState').style.display = 'none';
    document.getElementById('loadingState').style.display = 'block';
}

function showNoDataState() {
    document.getElementById('summaryCards').style.display = 'none';
    document.getElementById('chartContainer').style.display = 'none';
    document.getElementById('reportTable').style.display = 'none';
    document.getElementById('loadingState').style.display = 'none';
    document.getElementById('noDataState').style.display = 'block';
}

function displayReport(data, reportType) {
    document.getElementById('loadingState').style.display = 'none';

    if (!data.records || data.records.length === 0) {
        showNoDataState();
        return;
    }

    // Update summary cards
    document.getElementById('totalUsageDays').textContent = data.summary.total_usage_days || 0;
    document.getElementById('totalAmount').textContent = '₹' + (parseFloat(data.summary.total_amount) || 0).toFixed(2);
    document.getElementById('activeTenants').textContent = data.summary.active_tenants || 0;
    document.getElementById('avgDailyUsage').textContent = (parseFloat(data.summary.avg_daily_usage) || 0).toFixed(1);
    document.getElementById('summaryCards').style.display = 'grid';

    // Display chart
    displayChart(data.chart_data, reportType);

    // Display table
    displayTable(data.records, reportType);
}

function displayChart(chartData, reportType) {
    const ctx = document.getElementById('usageChart').getContext('2d');

    // Destroy existing chart
    if (usageChart) {
        usageChart.destroy();
    }

    const chartTitle = getChartTitle(reportType);
    document.getElementById('chartTitle').textContent = chartTitle;

    usageChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'Usage Amount (₹)',
                data: chartData.amounts,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.1,
                fill: true
            }, {
                label: 'Usage Count',
                data: chartData.counts,
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                tension: 0.1,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Amount (₹)'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Count'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            }
        }
    });

    document.getElementById('chartContainer').style.display = 'block';
}

function displayTable(records, reportType) {
    const headers = getTableHeaders(reportType);
    const tableTitle = getTableTitle(reportType);

    document.getElementById('tableTitle').textContent = tableTitle;

    // Create table headers
    const thead = document.querySelector('#reportDataTable thead');
    thead.innerHTML = '<tr>' + headers.map(header => `<th class="px-6 py-3 text-xs font-medium uppercase tracking-wider" style="color: var(--text-primary);">${header}</th>`).join('') + '</tr>';

    // Create table body
    const tbody = document.getElementById('reportTableBody');
    tbody.innerHTML = records.map(record => {
        return '<tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">' + getTableRow(record, reportType).join('') + '</tr>';
    }).join('');

    document.getElementById('reportTable').style.display = 'block';
}

function getChartTitle(reportType) {
    const titles = {
        'monthly': 'Monthly Usage Trends',
        'daily': 'Daily Usage Pattern',
        'tenant': 'Usage by Tenant',
        'amenity': 'Usage by Amenity'
    };
    return titles[reportType] || 'Usage Chart';
}

function getTableTitle(reportType) {
    const titles = {
        'monthly': 'Monthly Usage Summary',
        'daily': 'Daily Usage Details',
        'tenant': 'Tenant Usage Report',
        'amenity': 'Amenity Usage Report'
    };
    return titles[reportType] || 'Report Data';
}

function getTableHeaders(reportType) {
    const headers = {
        'monthly': ['Month', 'Usage Days', 'Total Amount', 'Active Tenants', 'Avg Daily'],
        'daily': ['Date', 'Usage Count', 'Total Amount', 'Active Tenants', 'Top Amenity'],
        'tenant': ['Tenant', 'Usage Days', 'Total Amount', 'Avg Per Day', 'Top Amenity'],
        'amenity': ['Amenity', 'Usage Days', 'Total Amount', 'Active Tenants', 'Avg Per Day']
    };
    return headers[reportType] || ['Data'];
}

function getTableRow(record, reportType) {
    const cellClass = "px-6 py-4 whitespace-nowrap text-sm";
    switch(reportType) {
        case 'monthly':
            return [
                `<td class="${cellClass}" style="color: var(--text-primary);">${record.period}</td>`,
                `<td class="${cellClass}" style="color: var(--text-primary);">${record.usage_days}</td>`,
                `<td class="${cellClass} font-medium" style="color: var(--text-primary);">₹${(parseFloat(record.total_amount) || 0).toFixed(2)}</td>`,
                `<td class="${cellClass}" style="color: var(--text-primary);">${record.active_tenants}</td>`,
                `<td class="${cellClass}" style="color: var(--text-primary);">₹${(parseFloat(record.avg_daily) || 0).toFixed(2)}</td>`
            ];
        case 'daily':
            return [
                `<td class="${cellClass}" style="color: var(--text-primary);">${record.date}</td>`,
                `<td class="${cellClass}" style="color: var(--text-primary);">${record.usage_count}</td>`,
                `<td class="${cellClass} font-medium" style="color: var(--text-primary);">₹${(parseFloat(record.total_amount) || 0).toFixed(2)}</td>`,
                `<td class="${cellClass}" style="color: var(--text-primary);">${record.active_tenants}</td>`,
                `<td class="${cellClass}" style="color: var(--text-primary);">${record.top_amenity}</td>`
            ];
        case 'tenant':
            return [
                `<td class="${cellClass} font-medium" style="color: var(--text-primary);">${record.tenant_name}</td>`,
                `<td class="${cellClass}" style="color: var(--text-primary);">${record.usage_days}</td>`,
                `<td class="${cellClass} font-medium" style="color: var(--text-primary);">₹${(parseFloat(record.total_amount) || 0).toFixed(2)}</td>`,
                `<td class="${cellClass}" style="color: var(--text-primary);">₹${(parseFloat(record.avg_per_day) || 0).toFixed(2)}</td>`,
                `<td class="${cellClass}" style="color: var(--text-primary);">${record.top_amenity}</td>`
            ];
        case 'amenity':
            return [
                `<td class="${cellClass} font-medium" style="color: var(--text-primary);">${record.amenity_name}</td>`,
                `<td class="${cellClass}" style="color: var(--text-primary);">${record.usage_days}</td>`,
                `<td class="${cellClass} font-medium" style="color: var(--text-primary);">₹${(parseFloat(record.total_amount) || 0).toFixed(2)}</td>`,
                `<td class="${cellClass}" style="color: var(--text-primary);">${record.active_tenants}</td>`,
                `<td class="${cellClass}" style="color: var(--text-primary);">₹${(parseFloat(record.avg_per_day) || 0).toFixed(2)}</td>`
            ];
        default:
            return [`<td class="${cellClass}" style="color: var(--text-primary);">${JSON.stringify(record)}</td>`];
    }
}

function exportReport() {
    const reportType = document.getElementById('report_type').value;
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;

    const url = `{{ route('amenity-usage.export') }}?report_type=${reportType}&start_date=${startDate}&end_date=${endDate}`;
    window.open(url, '_blank');
}


// Initialize with current month report
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate monthly report on page load
    setTimeout(generateReport, 500);
});
</script>
@endsection
