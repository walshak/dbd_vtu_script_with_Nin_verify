<?php $__env->startSection('title', 'Reports & Analytics'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-6 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Reports & Analytics</h1>
                    <p class="text-indigo-100 text-lg">Generate comprehensive business reports</p>
                </div>
                <div>
                    <i class="fas fa-chart-pie text-5xl opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Available Reports</p>
                    <p class="text-2xl font-bold text-gray-900">16</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-file-alt text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Export Formats</p>
                    <p class="text-2xl font-bold text-gray-900">3</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <i class="fas fa-download text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Categories</p>
                    <p class="text-2xl font-bold text-gray-900">4</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <i class="fas fa-layer-group text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Quick Export</p>
                    <p class="text-sm font-medium text-green-600 mt-1">CSV, Excel, PDF</p>
                </div>
                <div class="bg-orange-100 rounded-full p-3">
                    <i class="fas fa-rocket text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <button onclick="openReportModal('transaction_reports')"
                class="bg-gradient-to-br from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-xl p-6 shadow-lg transition-all duration-300 transform hover:scale-105 text-left">
            <div class="flex items-center justify-between mb-3">
                <i class="fas fa-chart-line text-3xl"></i>
                <span class="bg-blue-400 bg-opacity-30 text-white text-xs px-3 py-1 rounded-full">Popular</span>
            </div>
            <h3 class="text-lg font-semibold">Transaction Reports</h3>
            <p class="text-blue-100 text-sm mt-2">Sales & transaction analysis</p>
        </button>

        <button onclick="openReportModal('user_reports')"
                class="bg-gradient-to-br from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-xl p-6 shadow-lg transition-all duration-300 transform hover:scale-105 text-left">
            <div class="flex items-center justify-between mb-3">
                <i class="fas fa-users text-3xl"></i>
            </div>
            <h3 class="text-lg font-semibold">User Reports</h3>
            <p class="text-green-100 text-sm mt-2">User analytics & KYC status</p>
        </button>

        <button onclick="openReportModal('financial_reports')"
                class="bg-gradient-to-br from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white rounded-xl p-6 shadow-lg transition-all duration-300 transform hover:scale-105 text-left">
            <div class="flex items-center justify-between mb-3">
                <i class="fas fa-dollar-sign text-3xl"></i>
            </div>
            <h3 class="text-lg font-semibold">Financial Reports</h3>
            <p class="text-purple-100 text-sm mt-2">Revenue & profit analysis</p>
        </button>

        <button onclick="openReportModal('operational_reports')"
                class="bg-gradient-to-br from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-xl p-6 shadow-lg transition-all duration-300 transform hover:scale-105 text-left">
            <div class="flex items-center justify-between mb-3">
                <i class="fas fa-cogs text-3xl"></i>
            </div>
            <h3 class="text-lg font-semibold">Operational Reports</h3>
            <p class="text-orange-100 text-sm mt-2">System health & performance</p>
        </button>
    </div>

    <!-- Available Reports Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <?php $__currentLoopData = $availableReports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 p-6 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="bg-white rounded-lg p-3 shadow-md">
                        <i class="<?php echo e($category['icon']); ?> text-2xl text-indigo-600"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-xl font-semibold text-gray-900"><?php echo e($category['title']); ?></h3>
                        <p class="text-gray-600 text-sm"><?php echo e($category['description']); ?></p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <ul class="space-y-3">
                    <?php $__currentLoopData = $category['reports']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reportKey => $reportName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li>
                        <button onclick="generateReport('<?php echo e($key); ?>', '<?php echo e($reportKey); ?>')"
                                class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-indigo-50 transition-colors duration-200 group">
                            <div class="flex items-center">
                                <i class="fas fa-file-alt text-indigo-400 mr-3 group-hover:text-indigo-600"></i>
                                <span class="text-gray-700 group-hover:text-gray-900 font-medium"><?php echo e($reportName); ?></span>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400 group-hover:text-indigo-600"></i>
                        </button>
                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <!-- Export Formats Info -->
    <div class="bg-white rounded-xl shadow-lg p-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">
            <i class="fas fa-info-circle text-indigo-600 mr-2"></i>Export Formats
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                <div class="flex items-center mb-4">
                    <div class="bg-green-100 rounded-lg p-3">
                        <i class="fas fa-file-csv text-green-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="font-semibold text-gray-900">CSV Format</h3>
                        <p class="text-sm text-gray-600">Comma-separated values</p>
                    </div>
                </div>
                <p class="text-gray-600 text-sm">Best for data analysis in spreadsheet applications. Compatible with Excel, Google Sheets, and databases.</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                <div class="flex items-center mb-4">
                    <div class="bg-blue-100 rounded-lg p-3">
                        <i class="fas fa-file-excel text-blue-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="font-semibold text-gray-900">Excel Format</h3>
                        <p class="text-sm text-gray-600">XML Spreadsheet</p>
                    </div>
                </div>
                <p class="text-gray-600 text-sm">Native Excel format with formatting support. Opens directly in Microsoft Excel with styled headers.</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                <div class="flex items-center mb-4">
                    <div class="bg-red-100 rounded-lg p-3">
                        <i class="fas fa-file-pdf text-red-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="font-semibold text-gray-900">PDF Format</h3>
                        <p class="text-sm text-gray-600">Printable HTML Report</p>
                    </div>
                </div>
                <p class="text-gray-600 text-sm">Beautiful formatted report that can be printed or saved as PDF using your browser's print function.</p>
            </div>
        </div>
    </div>
</div>

<!-- Report Generation Modal -->
<div id="reportModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900 bg-opacity-50">
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
        <div class="relative bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="sticky top-0 flex items-center justify-between p-6 border-b border-gray-200 bg-white rounded-t-xl">
                <h3 id="reportModalTitle" class="text-xl font-semibold text-gray-900">Generate Report</h3>
                <button type="button" onclick="closeReportModal()"
                        class="text-gray-400 hover:text-gray-600 transition-colors p-2 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="reportForm" onsubmit="submitReport(event)">
                <?php echo csrf_field(); ?>
                <div class="p-6 space-y-6">
                    <!-- Report Type -->
                    <input type="hidden" id="report_category" name="report_category">
                    <input type="hidden" id="report_type" name="report_type">

                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2">
                            <i class="fas fa-file-alt text-indigo-600 mr-2"></i>Report Type
                        </label>
                        <select id="report_select" name="report_select"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                            <option value="">Select a report type...</option>
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">
                                <i class="fas fa-calendar-alt text-indigo-600 mr-2"></i>From Date
                            </label>
                            <input type="date" name="date_from" id="date_from"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">
                                <i class="fas fa-calendar-check text-indigo-600 mr-2"></i>To Date
                            </label>
                            <input type="date" name="date_to" id="date_to"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                        </div>
                    </div>

                    <!-- Quick Date Ranges -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm font-semibold text-gray-700 mb-3">Quick Date Ranges:</p>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" onclick="setDateRange('today')"
                                    class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-indigo-50 hover:border-indigo-300 transition-colors">
                                Today
                            </button>
                            <button type="button" onclick="setDateRange('yesterday')"
                                    class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-indigo-50 hover:border-indigo-300 transition-colors">
                                Yesterday
                            </button>
                            <button type="button" onclick="setDateRange('this_week')"
                                    class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-indigo-50 hover:border-indigo-300 transition-colors">
                                This Week
                            </button>
                            <button type="button" onclick="setDateRange('last_week')"
                                    class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-indigo-50 hover:border-indigo-300 transition-colors">
                                Last Week
                            </button>
                            <button type="button" onclick="setDateRange('this_month')"
                                    class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-indigo-50 hover:border-indigo-300 transition-colors">
                                This Month
                            </button>
                            <button type="button" onclick="setDateRange('last_month')"
                                    class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-indigo-50 hover:border-indigo-300 transition-colors">
                                Last Month
                            </button>
                            <button type="button" onclick="setDateRange('last_90_days')"
                                    class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-indigo-50 hover:border-indigo-300 transition-colors">
                                Last 90 Days
                            </button>
                            <button type="button" onclick="setDateRange('this_year')"
                                    class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-indigo-50 hover:border-indigo-300 transition-colors">
                                This Year
                            </button>
                        </div>
                    </div>

                    <!-- Export Format -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-3">
                            <i class="fas fa-file-export text-indigo-600 mr-2"></i>Export Format
                        </label>
                        <div class="grid grid-cols-3 gap-4">
                            <label class="relative flex items-center justify-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-indigo-500 transition-colors group">
                                <input type="radio" name="format" value="csv" class="sr-only peer" checked>
                                <div class="text-center peer-checked:text-indigo-600 group-hover:text-indigo-500">
                                    <i class="fas fa-file-csv text-2xl mb-2"></i>
                                    <p class="text-sm font-medium">CSV</p>
                                    <p class="text-xs text-gray-500 mt-1">Spreadsheet</p>
                                </div>
                                <div class="absolute inset-0 border-2 border-indigo-600 rounded-lg opacity-0 peer-checked:opacity-100"></div>
                            </label>
                            <label class="relative flex items-center justify-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-indigo-500 transition-colors group">
                                <input type="radio" name="format" value="excel" class="sr-only peer">
                                <div class="text-center peer-checked:text-indigo-600 group-hover:text-indigo-500">
                                    <i class="fas fa-file-excel text-2xl mb-2"></i>
                                    <p class="text-sm font-medium">Excel</p>
                                    <p class="text-xs text-gray-500 mt-1">Formatted</p>
                                </div>
                                <div class="absolute inset-0 border-2 border-indigo-600 rounded-lg opacity-0 peer-checked:opacity-100"></div>
                            </label>
                            <label class="relative flex items-center justify-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-indigo-500 transition-colors group">
                                <input type="radio" name="format" value="pdf" class="sr-only peer">
                                <div class="text-center peer-checked:text-indigo-600 group-hover:text-indigo-500">
                                    <i class="fas fa-file-pdf text-2xl mb-2"></i>
                                    <p class="text-sm font-medium">PDF</p>
                                    <p class="text-xs text-gray-500 mt-1">Printable</p>
                                </div>
                                <div class="absolute inset-0 border-2 border-indigo-600 rounded-lg opacity-0 peer-checked:opacity-100"></div>
                            </label>
                        </div>
                    </div>

                    <!-- Info Message -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-3"></i>
                            <div class="text-sm text-blue-700">
                                <p class="font-medium mb-1">Export Information</p>
                                <ul class="list-disc list-inside space-y-1 text-blue-600">
                                    <li>CSV files open in Excel with full data compatibility</li>
                                    <li>Excel format includes styled headers and summary</li>
                                    <li>PDF opens in browser - use Print to save as PDF file</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="sticky bottom-0 flex items-center justify-end gap-3 p-6 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                    <button type="button" onclick="closeReportModal()"
                            class="px-6 py-2.5 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit" id="submitBtn"
                            class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors flex items-center">
                        <i class="fas fa-download mr-2"></i>Generate Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="fixed inset-0 z-[60] hidden bg-gray-900 bg-opacity-75 flex items-center justify-center">
    <div class="bg-white rounded-xl p-8 text-center shadow-2xl">
        <div class="animate-spin rounded-full h-16 w-16 border-4 border-indigo-600 border-t-transparent mx-auto mb-4"></div>
        <h3 class="text-lg font-semibold text-gray-900 mb-2">Generating Report</h3>
        <p class="text-gray-600">Please wait while we prepare your report...</p>
    </div>
</div>

<!-- Success Toast -->
<div id="successToast" class="fixed bottom-4 right-4 z-[70] hidden transform transition-transform duration-300 translate-y-full">
    <div class="bg-green-600 text-white px-6 py-4 rounded-lg shadow-lg flex items-center">
        <i class="fas fa-check-circle mr-3 text-xl"></i>
        <div>
            <p class="font-semibold">Report Generated!</p>
            <p class="text-sm opacity-90">Your download should start automatically.</p>
        </div>
        <button onclick="hideToast('successToast')" class="ml-4 text-white hover:text-green-200">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<!-- Error Toast -->
<div id="errorToast" class="fixed bottom-4 right-4 z-[70] hidden transform transition-transform duration-300 translate-y-full">
    <div class="bg-red-600 text-white px-6 py-4 rounded-lg shadow-lg flex items-center">
        <i class="fas fa-exclamation-circle mr-3 text-xl"></i>
        <div>
            <p class="font-semibold">Generation Failed</p>
            <p id="errorMessage" class="text-sm opacity-90">An error occurred.</p>
        </div>
        <button onclick="hideToast('errorToast')" class="ml-4 text-white hover:text-red-200">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
const availableReports = <?php echo json_encode($availableReports, 15, 512) ?>;

function openReportModal(category) {
    document.getElementById('report_category').value = category;
    document.getElementById('reportModalTitle').textContent = availableReports[category].title;

    // Populate report select
    const select = document.getElementById('report_select');
    select.innerHTML = '<option value="">Select a report type...</option>';

    Object.entries(availableReports[category].reports).forEach(([key, name]) => {
        const option = document.createElement('option');
        option.value = key;
        option.textContent = name;
        select.appendChild(option);
    });

    // Set default dates (last 30 days)
    setDateRange('this_month');

    document.getElementById('reportModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function generateReport(category, reportKey) {
    openReportModal(category);
    setTimeout(() => {
        document.getElementById('report_select').value = reportKey;
    }, 100);
}

function closeReportModal() {
    document.getElementById('reportModal').classList.add('hidden');
    document.body.style.overflow = '';
    document.getElementById('reportForm').reset();
}

function setDateRange(range) {
    const today = new Date();
    const dateFrom = document.getElementById('date_from');
    const dateTo = document.getElementById('date_to');

    const formatDate = (date) => date.toISOString().split('T')[0];

    switch(range) {
        case 'today':
            dateFrom.value = formatDate(today);
            dateTo.value = formatDate(today);
            break;
        case 'yesterday':
            const yesterday = new Date(today);
            yesterday.setDate(yesterday.getDate() - 1);
            dateFrom.value = formatDate(yesterday);
            dateTo.value = formatDate(yesterday);
            break;
        case 'this_week':
            const weekStart = new Date(today);
            weekStart.setDate(today.getDate() - today.getDay());
            dateFrom.value = formatDate(weekStart);
            dateTo.value = formatDate(today);
            break;
        case 'last_week':
            const lastWeekEnd = new Date(today);
            lastWeekEnd.setDate(today.getDate() - today.getDay() - 1);
            const lastWeekStart = new Date(lastWeekEnd);
            lastWeekStart.setDate(lastWeekEnd.getDate() - 6);
            dateFrom.value = formatDate(lastWeekStart);
            dateTo.value = formatDate(lastWeekEnd);
            break;
        case 'this_month':
            const monthStart = new Date(today.getFullYear(), today.getMonth(), 1);
            dateFrom.value = formatDate(monthStart);
            dateTo.value = formatDate(today);
            break;
        case 'last_month':
            const lastMonthStart = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            const lastMonthEnd = new Date(today.getFullYear(), today.getMonth(), 0);
            dateFrom.value = formatDate(lastMonthStart);
            dateTo.value = formatDate(lastMonthEnd);
            break;
        case 'last_90_days':
            const ninetyDaysAgo = new Date(today);
            ninetyDaysAgo.setDate(today.getDate() - 90);
            dateFrom.value = formatDate(ninetyDaysAgo);
            dateTo.value = formatDate(today);
            break;
        case 'this_year':
            const yearStart = new Date(today.getFullYear(), 0, 1);
            dateFrom.value = formatDate(yearStart);
            dateTo.value = formatDate(today);
            break;
    }
}

function showLoading() {
    document.getElementById('loadingOverlay').classList.remove('hidden');
    document.getElementById('loadingOverlay').classList.add('flex');
}

function hideLoading() {
    document.getElementById('loadingOverlay').classList.add('hidden');
    document.getElementById('loadingOverlay').classList.remove('flex');
}

function showToast(id) {
    const toast = document.getElementById(id);
    toast.classList.remove('hidden', 'translate-y-full');
    toast.classList.add('translate-y-0');
    setTimeout(() => hideToast(id), 5000);
}

function hideToast(id) {
    const toast = document.getElementById(id);
    toast.classList.add('translate-y-full');
    setTimeout(() => toast.classList.add('hidden'), 300);
}

function submitReport(event) {
    event.preventDefault();

    const form = event.target;
    const formData = new FormData(form);
    const category = document.getElementById('report_category').value;
    const reportType = document.getElementById('report_select').value;
    const format = formData.get('format');

    if (!reportType) {
        alert('Please select a report type');
        return;
    }

    // Show loading state
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Generating...';
    submitBtn.disabled = true;
    showLoading();

    // Determine route based on category
    let route = '';
    switch(category) {
        case 'transaction_reports':
            route = '<?php echo e(route("reports.transaction")); ?>';
            break;
        case 'user_reports':
            route = '<?php echo e(route("reports.user")); ?>';
            break;
        case 'financial_reports':
            route = '<?php echo e(route("reports.financial")); ?>';
            break;
        case 'operational_reports':
            route = '<?php echo e(route("reports.operational")); ?>';
            break;
    }

    formData.append('report_type', reportType);

    fetch(route, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw new Error(err.error || 'Report generation failed'); });
        }

        // Get content type and filename from headers
        const contentType = response.headers.get('Content-Type');
        const disposition = response.headers.get('Content-Disposition');
        let filename = `report_${reportType}_${Date.now()}`;

        if (disposition) {
            const filenameMatch = disposition.match(/filename="?([^"]+)"?/);
            if (filenameMatch) {
                filename = filenameMatch[1];
            }
        }

        return response.blob().then(blob => ({ blob, filename, contentType }));
    })
    .then(({ blob, filename, contentType }) => {
        // Handle PDF (HTML) format - open in new window
        if (contentType && contentType.includes('text/html')) {
            const url = window.URL.createObjectURL(blob);
            window.open(url, '_blank');
            setTimeout(() => window.URL.revokeObjectURL(url), 60000);
        } else {
            // Download file for CSV/Excel
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            a.remove();
        }

        closeReportModal();
        showToast('successToast');
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('errorMessage').textContent = error.message;
        showToast('errorToast');
    })
    .finally(() => {
        hideLoading();
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Close modal on background click
document.getElementById('reportModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeReportModal();
    }
});

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeReportModal();
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\HARDMOTIONS\Documents\work\dbd_vtu_script_with_Nin_verify\laravel_version\resources\views/admin/reports/index.blade.php ENDPATH**/ ?>