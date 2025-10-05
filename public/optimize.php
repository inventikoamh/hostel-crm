<?php
/**
 * Laravel Optimization Utility - Styled Web Interface
 *
 * This script provides a modern web interface for optimizing Laravel applications.
 * Access this file through your web browser to optimize your application.
 *
 * Usage: Visit http://yourdomain.com/optimize.php in your browser
 */

// Set execution time limit
set_time_limit(120);

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

// Function to check if command exists
function commandExists($command) {
    $output = [];
    $return_var = 0;
    exec("which $command 2>/dev/null", $output, $return_var);
    return $return_var === 0;
}

// Handle AJAX requests
if (isset($_POST['action']) && $_POST['action'] === 'optimize') {
    header('Content-Type: application/json');

    $steps = [];
    $overall_success = true;

    // Step 1: Clear existing caches
    $cache_commands = [
        'php artisan cache:clear' => 'Application Cache',
        'php artisan config:clear' => 'Configuration Cache',
        'php artisan route:clear' => 'Route Cache',
        'php artisan view:clear' => 'View Cache',
        'php artisan event:clear' => 'Event Cache'
    ];

    $cache_success = true;
    foreach ($cache_commands as $command => $name) {
        $result = runCommand($command, "Clearing $name");
        if (!$result['success']) {
            $cache_success = false;
        }
    }

    $steps[] = [
        'name' => 'Clear Existing Caches',
        'success' => $cache_success,
        'output' => $cache_success ? 'All caches cleared successfully' : 'Some cache clearing operations failed'
    ];
    if (!$cache_success) $overall_success = false;

    // Step 2: Cache configuration
    $result = runCommand('php artisan config:cache', 'Caching configuration files');
    $steps[] = [
        'name' => 'Cache Configuration',
        'success' => $result['success'],
        'output' => $result['output']
    ];
    if (!$result['success']) $overall_success = false;

    // Step 3: Cache routes
    $result = runCommand('php artisan route:cache', 'Caching routes');
    $steps[] = [
        'name' => 'Cache Routes',
        'success' => $result['success'],
        'output' => $result['output']
    ];
    if (!$result['success']) $overall_success = false;

    // Step 4: Cache views
    $result = runCommand('php artisan view:cache', 'Caching views');
    $steps[] = [
        'name' => 'Cache Views',
        'success' => $result['success'],
        'output' => $result['output']
    ];
    if (!$result['success']) $overall_success = false;

    // Step 5: Cache events
    $result = runCommand('php artisan event:cache', 'Caching events');
    $steps[] = [
        'name' => 'Cache Events',
        'success' => $result['success'],
        'output' => $result['output']
    ];
    if (!$result['success']) $overall_success = false;

    // Step 6: Optimize Composer autoloader
    $composer_available = commandExists('composer') || file_exists('composer.phar');
    $composer_success = true;
    if ($composer_available) {
        $result = runCommand('composer dump-autoload --optimize --no-dev', 'Optimizing Composer autoloader');
        $composer_success = $result['success'];
    }

    $steps[] = [
        'name' => 'Optimize Composer Autoloader',
        'success' => $composer_success,
        'output' => $composer_available ? ($composer_success ? 'Autoloader optimized successfully' : 'Autoloader optimization failed') : 'Composer not available'
    ];
    if (!$composer_success) $overall_success = false;

    // Step 7: Run Laravel optimization
    $result = runCommand('php artisan optimize', 'Optimizing Laravel application');
    $steps[] = [
        'name' => 'Laravel Optimization',
        'success' => $result['success'],
        'output' => $result['output']
    ];
    if (!$result['success']) $overall_success = false;

    // Step 8: Check file permissions
    $directories = ['storage', 'storage/app', 'storage/framework', 'storage/framework/cache', 'storage/framework/sessions', 'storage/framework/views', 'storage/logs', 'bootstrap/cache'];
    $permission_issues = [];

    foreach ($directories as $dir) {
        if (is_dir($dir) && !is_writable($dir)) {
            $permission_issues[] = $dir;
        }
    }

    $permission_success = empty($permission_issues);
    $steps[] = [
        'name' => 'File Permissions Check',
        'success' => $permission_success,
        'output' => $permission_success ? 'All directories are writable' : 'Some directories need permission fixes: ' . implode(', ', $permission_issues)
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
    <title>Laravel Hostel CRM - Optimization</title>
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
                <i class="fas fa-tachometer-alt mr-3"></i>
                Laravel Hostel CRM
            </h1>
            <p class="text-xl opacity-90">Performance Optimization</p>

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
                <a href="clear-cache.php" class="inline-flex items-center px-3 py-2 bg-yellow-600 text-white text-sm font-medium rounded-md hover:bg-yellow-700 transition duration-200">
                    <i class="fas fa-broom mr-2"></i>Clear Cache
                </a>
                <a href="deploy-web.php" class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition duration-200">
                    <i class="fas fa-tools mr-2"></i>Advanced
                </a>
                <a href="run-migrations.php" class="inline-flex items-center px-3 py-2 bg-purple-600 text-white text-sm font-medium rounded-md hover:bg-purple-700 transition duration-200">
                    <i class="fas fa-sync mr-2"></i>Migrations
                </a>
                <a href="maintenance.php" class="inline-flex items-center px-3 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 transition duration-200">
                    <i class="fas fa-wrench mr-2"></i>Maintenance
                </a>
            </div>
        </div>

        <!-- Optimization Section -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8 card-hover">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">
                <i class="fas fa-tachometer-alt text-green-500 mr-2"></i>
                Performance Optimization
            </h2>

            <div class="mb-6">
                <p class="text-gray-600 mb-4">
                    Click the button below to optimize your Laravel application for production. This will:
                </p>
                <ul class="list-disc list-inside text-gray-600 space-y-1">
                    <li>Clear existing caches</li>
                    <li>Cache configuration files</li>
                    <li>Cache routes for faster routing</li>
                    <li>Cache views for faster rendering</li>
                    <li>Cache events for better performance</li>
                    <li>Optimize Composer autoloader</li>
                    <li>Run Laravel optimization</li>
                    <li>Check file permissions</li>
                </ul>
            </div>

            <button id="optimize-btn" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition duration-300 flex items-center justify-center">
                <i class="fas fa-tachometer-alt mr-2"></i>
                Optimize Application
            </button>
        </div>

        <!-- Optimization Progress -->
        <div id="optimize-progress" class="bg-white rounded-lg shadow-lg p-6 mb-8 hidden">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">
                <i class="fas fa-tasks text-purple-500 mr-2"></i>
                Optimization Progress
            </h2>

            <div class="mb-4">
                <div class="flex justify-between text-sm text-gray-600 mb-1">
                    <span>Progress</span>
                    <span id="progress-text">0%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div id="progress-bar" class="bg-green-600 h-2 rounded-full progress-bar" style="width: 0%"></div>
                </div>
            </div>

            <div id="optimize-steps" class="space-y-3">
                <!-- Steps will be populated here -->
            </div>
        </div>

        <!-- Performance Tips -->
        <div class="bg-white rounded-lg shadow-lg p-6 card-hover">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">
                <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                Performance Tips
            </h2>

            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-bold text-gray-700 mb-2">Optimization Benefits:</h3>
                    <ul class="list-disc list-inside text-gray-600 space-y-1">
                        <li><strong>Faster Loading</strong> - Cached configurations and routes</li>
                        <li><strong>Reduced Memory Usage</strong> - Optimized autoloader</li>
                        <li><strong>Better Performance</strong> - Compiled views and events</li>
                        <li><strong>Production Ready</strong> - Optimized for live environments</li>
                        <li><strong>Improved SEO</strong> - Faster page load times</li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-bold text-gray-700 mb-2">When to Optimize:</h3>
                    <ul class="list-disc list-inside text-gray-600 space-y-1">
                        <li>Before going to production</li>
                        <li>After major code changes</li>
                        <li>When experiencing slow performance</li>
                        <li>After updating dependencies</li>
                        <li>Regular maintenance (weekly/monthly)</li>
                    </ul>
                </div>
            </div>

            <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <h4 class="font-bold text-blue-800 mb-2">
                    <i class="fas fa-info-circle mr-2"></i>
                    Important Notes
                </h4>
                <ul class="text-blue-700 text-sm space-y-1">
                    <li>• Run optimization after any configuration changes</li>
                    <li>• Clear caches when updating routes or views during development</li>
                    <li>• Monitor application logs in storage/logs/</li>
                    <li>• Consider using a CDN for static assets in production</li>
                </ul>
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
                        <a href="clear-cache.php" class="block text-gray-300 hover:text-white text-sm transition duration-200">
                            <i class="fas fa-broom mr-1"></i> Clear Cache
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
                    Laravel Hostel CRM Optimization Tool - Built with ❤️ for peak performance
                </p>
            </div>
        </div>
    </footer>

    <script>
        // Handle optimization
        document.getElementById('optimize-btn').addEventListener('click', function() {
            const btn = this;
            const progressDiv = document.getElementById('optimize-progress');
            const stepsDiv = document.getElementById('optimize-steps');

            // Disable button and show progress
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Optimizing...';
            progressDiv.classList.remove('hidden');
            stepsDiv.innerHTML = '';

            // Start optimization
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=optimize'
            })
            .then(response => response.json())
            .then(data => {
                displayOptimizeResults(data);

                // Re-enable button
                btn.disabled = false;
                if (data.success) {
                    btn.innerHTML = '<i class="fas fa-check mr-2"></i>Optimization Complete';
                    btn.className = 'w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition duration-300 flex items-center justify-center';
                } else {
                    btn.innerHTML = '<i class="fas fa-redo mr-2"></i>Retry Optimization';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                stepsDiv.innerHTML = '<div class="p-3 bg-red-50 border border-red-200 rounded-lg text-red-700">Optimization failed due to an error</div>';

                // Re-enable button
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-redo mr-2"></i>Retry Optimization';
            });
        });

        function displayOptimizeResults(data) {
            const stepsDiv = document.getElementById('optimize-steps');
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
