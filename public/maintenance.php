<?php
/**
 * Laravel Maintenance Mode Utility - Styled Web Interface
 *
 * This script provides a modern web interface for managing Laravel's maintenance mode.
 * Access this file through your web browser to manage maintenance mode.
 *
 * Usage: Visit http://yourdomain.com/maintenance.php in your browser
 */

// Set execution time limit
set_time_limit(30);

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

// Function to check maintenance mode status
function getMaintenanceStatus() {
    $downFile = 'storage/framework/down';
    return file_exists($downFile);
}

// Handle AJAX requests
if (isset($_POST['action'])) {
    header('Content-Type: application/json');

    $action = $_POST['action'];
    $result = null;

    switch ($action) {
    case 'enable':
            $result = runCommand('php artisan down', 'Enabling maintenance mode');
        break;

    case 'disable':
            $result = runCommand('php artisan up', 'Disabling maintenance mode');
        break;

    case 'status':
            $isDown = getMaintenanceStatus();
            $result = [
                'success' => true,
                'output' => $isDown ? 'Maintenance mode is ENABLED' : 'Maintenance mode is DISABLED',
                'is_down' => $isDown
            ];
            break;

        case 'enable_with_message':
            $message = $_POST['message'] ?? 'We are currently performing maintenance. Please check back later.';
            $result = runCommand("php artisan down --message=\"$message\"", 'Enabling maintenance mode with custom message');
            break;

        case 'enable_with_retry':
            $retry = $_POST['retry'] ?? 60;
            $result = runCommand("php artisan down --retry=$retry", 'Enabling maintenance mode with retry time');
            break;
    }

    echo json_encode($result);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Hostel CRM - Maintenance Mode</title>
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
        .maintenance-active {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="gradient-bg text-white py-8" id="header">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl font-bold mb-2">
                <i class="fas fa-wrench mr-3"></i>
                Laravel Hostel CRM
            </h1>
            <p class="text-xl opacity-90">Maintenance Mode Management</p>

            <!-- Breadcrumb Navigation -->
            <nav class="mt-4 flex justify-center">
                <div class="flex items-center space-x-2 text-sm opacity-80">
                    <a href="deploy.php" class="hover:opacity-100 transition duration-200">
                        <i class="fas fa-home mr-1"></i>Deploy
                    </a>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <a href="maintenance.php" class="hover:opacity-100 transition duration-200">
                        <i class="fas fa-wrench mr-1"></i>Maintenance
                    </a>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <a href="clear-cache.php" class="hover:opacity-100 transition duration-200">
                        <i class="fas fa-broom mr-1"></i>Clear Cache
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
                <a href="optimize.php" class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition duration-200">
                    <i class="fas fa-tachometer-alt mr-2"></i>Optimize
                </a>
                <a href="run-migrations.php" class="inline-flex items-center px-3 py-2 bg-purple-600 text-white text-sm font-medium rounded-md hover:bg-purple-700 transition duration-200">
                    <i class="fas fa-sync mr-2"></i>Migrations
                </a>
                <a href="backup-database.php" class="inline-flex items-center px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 transition duration-200">
                    <i class="fas fa-save mr-2"></i>Backup
                </a>
            </div>
        </div>

        <!-- Maintenance Status -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8 card-hover">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">
                <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                Current Status
            </h2>

            <div id="status-display" class="text-center py-8">
                <div class="flex items-center justify-center mb-4">
                    <i class="fas fa-spinner fa-spin text-4xl text-blue-500"></i>
                </div>
                <p class="text-gray-600">Checking maintenance mode status...</p>
            </div>
        </div>

        <!-- Maintenance Controls -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8 card-hover">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">
                <i class="fas fa-cogs text-orange-500 mr-2"></i>
                Maintenance Controls
            </h2>

            <div class="grid md:grid-cols-2 gap-6">
                <!-- Basic Controls -->
                <div>
                    <h3 class="font-bold text-gray-700 mb-3">Basic Controls</h3>
                    <div class="space-y-3">
                        <button id="enable-maintenance" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300 flex items-center justify-center">
                            <i class="fas fa-toggle-on mr-2"></i>
                            Enable Maintenance Mode
                        </button>

                        <button id="disable-maintenance" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300 flex items-center justify-center">
                            <i class="fas fa-toggle-off mr-2"></i>
                            Disable Maintenance Mode
                        </button>

                        <button id="check-status" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300 flex items-center justify-center">
                            <i class="fas fa-sync mr-2"></i>
                            Check Status
                        </button>
                    </div>
                </div>

                <!-- Advanced Controls -->
                <div>
                    <h3 class="font-bold text-gray-700 mb-3">Advanced Options</h3>
                    <div class="space-y-3">
                        <div>
                            <label for="custom-message" class="block text-sm font-medium text-gray-700 mb-1">
                                Custom Message
                            </label>
                            <input type="text" id="custom-message" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"
                                   placeholder="Enter custom maintenance message">
                            <button id="enable-with-message" class="w-full mt-2 bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300 flex items-center justify-center">
                                <i class="fas fa-comment mr-2"></i>
                                Enable with Message
                            </button>
                        </div>

                        <div>
                            <label for="retry-time" class="block text-sm font-medium text-gray-700 mb-1">
                                Retry Time (seconds)
                            </label>
                            <input type="number" id="retry-time" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"
                                   value="60" min="1" max="3600">
                            <button id="enable-with-retry" class="w-full mt-2 bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300 flex items-center justify-center">
                                <i class="fas fa-clock mr-2"></i>
                                Enable with Retry
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Maintenance Information -->
        <div class="bg-white rounded-lg shadow-lg p-6 card-hover">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">
                <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                Maintenance Mode Information
            </h2>

            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-bold text-gray-700 mb-2">What is Maintenance Mode?</h3>
                    <ul class="list-disc list-inside text-gray-600 space-y-1">
                        <li>Temporarily disables your application for visitors</li>
                        <li>Shows a maintenance page instead of your app</li>
                        <li>Allows you to perform updates safely</li>
                        <li>Prevents users from accessing incomplete features</li>
                        <li>Essential for production deployments</li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-bold text-gray-700 mb-2">When to Use:</h3>
                    <ul class="list-disc list-inside text-gray-600 space-y-1">
                        <li>Before running database migrations</li>
                        <li>During application updates</li>
                        <li>When fixing critical bugs</li>
                        <li>During scheduled maintenance</li>
                        <li>Before major deployments</li>
                    </ul>
                </div>
            </div>

            <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <h4 class="font-bold text-yellow-800 mb-2">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Important Notes
                </h4>
                <ul class="text-yellow-700 text-sm space-y-1">
                    <li>• Remember to disable maintenance mode after completing your work</li>
                    <li>• Custom messages help inform users about the maintenance</li>
                    <li>• Retry time tells browsers when to check back</li>
                    <li>• Maintenance mode affects all visitors except bypass tokens</li>
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
                        Always enable maintenance mode before making changes to production systems.
                    </p>
                </div>
            </div>

            <div class="border-t border-gray-700 mt-6 pt-6">
                <p class="text-gray-400 text-sm">
                    Laravel Hostel CRM Maintenance Tool - Built with ❤️ for safe deployments
                </p>
            </div>
        </div>
    </footer>

    <script>
        // Check status on page load
        document.addEventListener('DOMContentLoaded', function() {
            checkMaintenanceStatus();
        });

        function checkMaintenanceStatus() {
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=status'
            })
            .then(response => response.json())
            .then(data => {
                displayStatus(data);
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('status-display').innerHTML =
                    '<div class="text-red-600"><i class="fas fa-exclamation-triangle mr-2"></i>Error checking status</div>';
            });
        }

        function displayStatus(data) {
            const statusDiv = document.getElementById('status-display');
            const header = document.getElementById('header');

            if (data.is_down) {
                statusDiv.innerHTML = `
                    <div class="text-center">
                        <div class="flex items-center justify-center mb-4">
                            <i class="fas fa-exclamation-triangle text-4xl text-orange-500"></i>
                        </div>
                        <h3 class="text-xl font-bold text-orange-600 mb-2">Maintenance Mode ENABLED</h3>
                        <p class="text-gray-600">Your application is currently in maintenance mode.</p>
                        <p class="text-sm text-gray-500 mt-2">${data.output}</p>
                    </div>
                `;
                header.className = 'maintenance-active text-white py-8';
            } else {
                statusDiv.innerHTML = `
                    <div class="text-center">
                        <div class="flex items-center justify-center mb-4">
                            <i class="fas fa-check-circle text-4xl text-green-500"></i>
                        </div>
                        <h3 class="text-xl font-bold text-green-600 mb-2">Maintenance Mode DISABLED</h3>
                        <p class="text-gray-600">Your application is running normally.</p>
                        <p class="text-sm text-gray-500 mt-2">${data.output}</p>
                    </div>
                `;
                header.className = 'gradient-bg text-white py-8';
            }
        }

        // Event listeners for buttons
        document.getElementById('enable-maintenance').addEventListener('click', function() {
            performAction('enable', 'Enabling maintenance mode...');
        });

        document.getElementById('disable-maintenance').addEventListener('click', function() {
            performAction('disable', 'Disabling maintenance mode...');
        });

        document.getElementById('check-status').addEventListener('click', function() {
            checkMaintenanceStatus();
        });

        document.getElementById('enable-with-message').addEventListener('click', function() {
            const message = document.getElementById('custom-message').value;
            if (!message.trim()) {
                alert('Please enter a custom message');
                return;
            }
            performAction('enable_with_message', 'Enabling maintenance mode with custom message...', { message: message });
        });

        document.getElementById('enable-with-retry').addEventListener('click', function() {
            const retry = document.getElementById('retry-time').value;
            performAction('enable_with_retry', 'Enabling maintenance mode with retry time...', { retry: retry });
        });

        function performAction(action, loadingText, extraData = {}) {
            const formData = new FormData();
            formData.append('action', action);

            for (const [key, value] of Object.entries(extraData)) {
                formData.append(key, value);
            }

            // Show loading state
            const statusDiv = document.getElementById('status-display');
            statusDiv.innerHTML = `
                <div class="text-center">
                    <div class="flex items-center justify-center mb-4">
                        <i class="fas fa-spinner fa-spin text-4xl text-blue-500"></i>
                    </div>
                    <p class="text-gray-600">${loadingText}</p>
                </div>
            `;

            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Refresh status after action
                    setTimeout(() => {
                        checkMaintenanceStatus();
                    }, 1000);
                } else {
                    statusDiv.innerHTML = `
                        <div class="text-center text-red-600">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Error: ${data.output}
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                statusDiv.innerHTML = `
                    <div class="text-center text-red-600">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Error performing action
                    </div>
                `;
            });
        }
    </script>
</body>
</html>
