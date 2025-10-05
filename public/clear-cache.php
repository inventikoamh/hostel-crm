<?php
/**
 * Laravel Cache Clearing Utility - Styled Web Interface
 *
 * This script provides a modern web interface for clearing Laravel caches.
 * Access this file through your web browser to clear caches.
 *
 * Usage: Visit http://yourdomain.com/clear-cache.php in your browser
 */

// Set execution time limit
set_time_limit(60);

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if we're in the right directory
if (!file_exists('../artisan')) {
    die("❌ Error: artisan file not found. Please run this script from your Laravel project root directory.");
}

// Change to parent directory (Laravel root)
chdir('..');

// Function to run commands
function runCommand($command, $description) {
    $output = [];
    $return_var = 0;
    exec($command . ' 2>&1', $output, $return_var);

    return [
        'success' => $return_var === 0,
        'output' => implode("\n", $output),
        'return_code' => $return_var
    ];
}

// Handle AJAX requests
if (isset($_POST['action']) && $_POST['action'] === 'clear_cache') {
    header('Content-Type: application/json');

    $steps = [];
    $overall_success = true;

    // Step 1: Clear application cache
    $result = runCommand('php artisan cache:clear', 'Clearing application cache');
    $steps[] = [
        'name' => 'Application Cache',
        'success' => $result['success'],
        'output' => $result['output']
    ];
    if (!$result['success']) $overall_success = false;

    // Step 2: Clear configuration cache
    $result = runCommand('php artisan config:clear', 'Clearing configuration cache');
    $steps[] = [
        'name' => 'Configuration Cache',
        'success' => $result['success'],
        'output' => $result['output']
    ];
    if (!$result['success']) $overall_success = false;

    // Step 3: Clear route cache
    $result = runCommand('php artisan route:clear', 'Clearing route cache');
    $steps[] = [
        'name' => 'Route Cache',
        'success' => $result['success'],
        'output' => $result['output']
    ];
    if (!$result['success']) $overall_success = false;

    // Step 4: Clear view cache
    $result = runCommand('php artisan view:clear', 'Clearing view cache');
    $steps[] = [
        'name' => 'View Cache',
        'success' => $result['success'],
        'output' => $result['output']
    ];
    if (!$result['success']) $overall_success = false;

    // Step 5: Clear event cache
    $result = runCommand('php artisan event:clear', 'Clearing event cache');
    $steps[] = [
        'name' => 'Event Cache',
        'success' => $result['success'],
        'output' => $result['output']
    ];
    if (!$result['success']) $overall_success = false;

    // Step 6: Clear compiled services
    $result = runCommand('php artisan clear-compiled', 'Clearing compiled services');
    $steps[] = [
        'name' => 'Compiled Services',
        'success' => $result['success'],
        'output' => $result['output']
    ];
    if (!$result['success']) $overall_success = false;

    // Step 7: Clear OPcache
    $opcache_available = function_exists('opcache_reset');
    $opcache_success = false;
    if ($opcache_available) {
        $opcache_success = opcache_reset();
    }
    $steps[] = [
        'name' => 'OPcache',
        'success' => $opcache_success,
        'output' => $opcache_available ? ($opcache_success ? 'OPcache cleared successfully' : 'OPcache reset failed') : 'OPcache not available'
    ];

    echo json_encode([
        'success' => $overall_success,
        'steps' => $steps
    ]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Hostel CRM - Cache Clearing</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .progress-bar {
            transition: width 0.3s ease;
        }
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .success-glow {
            box-shadow: 0 0 20px rgba(34, 197, 94, 0.3);
        }
        .error-glow {
            box-shadow: 0 0 20px rgba(239, 68, 68, 0.3);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="gradient-bg text-white py-8">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl font-bold mb-2">
                <i class="fas fa-broom mr-3"></i>
                Laravel Hostel CRM
            </h1>
            <p class="text-xl opacity-90">Cache Clearing Utility</p>

            <!-- Breadcrumb Navigation -->
            <nav class="mt-4 flex justify-center">
                <div class="flex items-center space-x-2 text-sm opacity-80">
                    <a href="deploy.php" class="hover:opacity-100 transition duration-200">
                        <i class="fas fa-home mr-1"></i>Deploy
                    </a>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <a href="clear-cache.php" class="hover:opacity-100 transition duration-200">
                        <i class="fas fa-broom mr-1"></i>Clear Cache
                    </a>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <a href="optimize.php" class="hover:opacity-100 transition duration-200">
                        <i class="fas fa-tachometer-alt mr-1"></i>Optimize
                    </a>
                </div>
            </nav>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <!-- Navigation Section -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8 card-hover">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">
                <i class="fas fa-compass text-indigo-500 mr-2"></i>
                Quick Navigation
            </h2>

            <div class="flex flex-wrap gap-2">
                <a href="deploy.php" class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition duration-200">
                    <i class="fas fa-rocket mr-2"></i>Deploy
                </a>
                <a href="deploy-web.php" class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition duration-200">
                    <i class="fas fa-tools mr-2"></i>Advanced
                </a>
                <a href="optimize.php" class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition duration-200">
                    <i class="fas fa-tachometer-alt mr-2"></i>Optimize
                </a>
                <a href="run-migrations.php" class="inline-flex items-center px-3 py-2 bg-purple-600 text-white text-sm font-medium rounded-md hover:bg-purple-700 transition duration-200">
                    <i class="fas fa-sync mr-2"></i>Migrations
                </a>
                <a href="maintenance.php" class="inline-flex items-center px-3 py-2 bg-yellow-600 text-white text-sm font-medium rounded-md hover:bg-yellow-700 transition duration-200">
                    <i class="fas fa-wrench mr-2"></i>Maintenance
                </a>
            </div>
        </div>

        <!-- Cache Clearing Section -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8 card-hover">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">
                <i class="fas fa-broom text-yellow-500 mr-2"></i>
                Cache Clearing Process
            </h2>

            <div class="mb-6">
                <p class="text-gray-600 mb-4">
                    Click the button below to clear all Laravel caches. This will:
                </p>
                <ul class="list-disc list-inside text-gray-600 space-y-1">
                    <li>Clear application cache</li>
                    <li>Clear configuration cache</li>
                    <li>Clear route cache</li>
                    <li>Clear view cache</li>
                    <li>Clear event cache</li>
                    <li>Clear compiled services</li>
                    <li>Clear OPcache (if available)</li>
                </ul>
            </div>

            <button id="clear-cache-btn" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-3 px-6 rounded-lg transition duration-300 flex items-center justify-center">
                <i class="fas fa-broom mr-2"></i>
                Clear All Caches
            </button>
        </div>

        <!-- Cache Clearing Progress -->
        <div id="cache-progress" class="bg-white rounded-lg shadow-lg p-6 mb-8 hidden">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">
                <i class="fas fa-tasks text-purple-500 mr-2"></i>
                Cache Clearing Progress
            </h2>

            <div class="mb-4">
                <div class="flex justify-between text-sm text-gray-600 mb-1">
                    <span>Progress</span>
                    <span id="progress-text">0%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div id="progress-bar" class="bg-yellow-600 h-2 rounded-full progress-bar" style="width: 0%"></div>
                </div>
            </div>

            <div id="cache-steps" class="space-y-3">
                <!-- Steps will be populated here -->
            </div>
        </div>

        <!-- Information Section -->
        <div class="bg-white rounded-lg shadow-lg p-6 card-hover">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">
                <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                Cache Information
            </h2>

            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-bold text-gray-700 mb-2">What Gets Cleared:</h3>
                    <ul class="list-disc list-inside text-gray-600 space-y-1">
                        <li><strong>Application Cache</strong> - Stored application data</li>
                        <li><strong>Configuration Cache</strong> - Cached configuration files</li>
                        <li><strong>Route Cache</strong> - Cached route definitions</li>
                        <li><strong>View Cache</strong> - Compiled Blade templates</li>
                        <li><strong>Event Cache</strong> - Cached event listeners</li>
                        <li><strong>Compiled Services</strong> - Service container cache</li>
                        <li><strong>OPcache</strong> - PHP opcode cache</li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-bold text-gray-700 mb-2">When to Clear Caches:</h3>
                    <ul class="list-disc list-inside text-gray-600 space-y-1">
                        <li>After updating configuration files</li>
                        <li>After modifying routes</li>
                        <li>After updating Blade templates</li>
                        <li>When experiencing caching issues</li>
                        <li>After code deployments</li>
                        <li>For troubleshooting purposes</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="container mx-auto px-4 text-center">
            <div class="grid md:grid-cols-3 gap-6">
                <div>
                    <h3 class="font-bold text-lg mb-2">
                        <i class="fas fa-tools mr-2"></i>
                        Related Tools
                    </h3>
                    <div class="space-y-1">
                        <a href="deploy.php" class="block text-gray-300 hover:text-white text-sm transition duration-200">
                            <i class="fas fa-rocket mr-1"></i> Main Deploy
                        </a>
                        <a href="optimize.php" class="block text-gray-300 hover:text-white text-sm transition duration-200">
                            <i class="fas fa-tachometer-alt mr-1"></i> Optimize
                        </a>
                        <a href="deploy-web.php" class="block text-gray-300 hover:text-white text-sm transition duration-200">
                            <i class="fas fa-cogs mr-1"></i> Advanced
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="font-bold text-lg mb-2">
                        <i class="fas fa-database mr-2"></i>
                        Database Tools
                    </h3>
                    <div class="space-y-1">
                        <a href="run-migrations.php" class="block text-gray-300 hover:text-white text-sm transition duration-200">
                            <i class="fas fa-sync mr-1"></i> Migrations
                        </a>
                        <a href="backup-database.php" class="block text-gray-300 hover:text-white text-sm transition duration-200">
                            <i class="fas fa-save mr-1"></i> Backup
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="font-bold text-lg mb-2">
                        <i class="fas fa-shield-alt mr-2"></i>
                        Security Note
                    </h3>
                    <p class="text-gray-300 text-sm">
                        Remember to delete deployment files after successful setup for security.
                    </p>
                </div>
            </div>

            <div class="border-t border-gray-700 mt-6 pt-6">
                <p class="text-gray-400 text-sm">
                    Laravel Hostel CRM Cache Clearing Tool - Built with ❤️ for easy maintenance
                </p>
            </div>
        </div>
    </footer>

    <script>
        // Handle cache clearing
        document.getElementById('clear-cache-btn').addEventListener('click', function() {
            const btn = this;
            const progressDiv = document.getElementById('cache-progress');
            const stepsDiv = document.getElementById('cache-steps');

            // Disable button and show progress
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Clearing Caches...';
            progressDiv.classList.remove('hidden');
            stepsDiv.innerHTML = '';

            // Start cache clearing
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=clear_cache'
            })
            .then(response => response.json())
            .then(data => {
                displayCacheResults(data);

                // Re-enable button
                btn.disabled = false;
                if (data.success) {
                    btn.innerHTML = '<i class="fas fa-check mr-2"></i>Caches Cleared Successfully';
                    btn.className = 'w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition duration-300 flex items-center justify-center';
                } else {
                    btn.innerHTML = '<i class="fas fa-redo mr-2"></i>Retry Cache Clearing';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                stepsDiv.innerHTML = '<div class="p-3 bg-red-50 border border-red-200 rounded-lg text-red-700">Cache clearing failed due to an error</div>';

                // Re-enable button
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-redo mr-2"></i>Retry Cache Clearing';
            });
        });

        function displayCacheResults(data) {
            const stepsDiv = document.getElementById('cache-steps');
            const progressBar = document.getElementById('progress-bar');
            const progressText = document.getElementById('progress-text');

            let completedSteps = 0;
            const totalSteps = data.steps.length;

            data.steps.forEach((step, index) => {
                const stepDiv = document.createElement('div');
                stepDiv.className = `flex items-center justify-between p-3 rounded-lg ${
                    step.success ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'
                }`;

                const icon = step.success ? 'fa-check-circle text-green-500' : 'fa-times-circle text-red-500';

                stepDiv.innerHTML = `
                    <div class="flex items-center">
                        <i class="fas ${icon} mr-3"></i>
                        <span class="font-medium">${step.name}</span>
                    </div>
                    <div class="text-sm text-gray-600 max-w-md text-right">
                        ${step.output}
                    </div>
                `;

                stepsDiv.appendChild(stepDiv);

                if (step.success) {
                    completedSteps++;
                }

                // Update progress
                const progress = Math.round((completedSteps / totalSteps) * 100);
                progressBar.style.width = progress + '%';
                progressText.textContent = progress + '%';
            });
        }
    </script>
</body>
</html>
