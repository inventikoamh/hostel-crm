<?php
/**
 * Laravel Hostel CRM Deployment Script - Styled Web Interface
 *
 * This script provides a modern web interface for deploying Laravel applications.
 * Access this file through your web browser to deploy your application.
 *
 * Usage: Visit http://yourdomain.com/deploy.php in your browser
 */

// Set execution time limit
set_time_limit(300); // 5 minutes

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
if (isset($_POST['action'])) {
    header('Content-Type: application/json');

    switch ($_POST['action']) {
        case 'check_requirements':
$phpVersion = phpversion();
$requiredExtensions = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath', 'fileinfo'];
$missingExtensions = [];

foreach ($requiredExtensions as $extension) {
    if (!extension_loaded($extension)) {
        $missingExtensions[] = $extension;
    }
}

            echo json_encode([
                'php_version' => $phpVersion,
                'php_ok' => version_compare($phpVersion, '8.1.0', '>='),
                'extensions_ok' => empty($missingExtensions),
                'missing_extensions' => $missingExtensions,
                'composer_available' => commandExists('composer')
            ]);
            exit;

        case 'deploy':
            $steps = [];
            $overall_success = true;
            $run_migrations = isset($_POST['run_migrations']) && $_POST['run_migrations'] === 'true';

            // Step 1: Install Composer dependencies
if (commandExists('composer')) {
                $result = runCommand('composer install --no-dev --optimize-autoloader', 'Installing production dependencies');
                $steps[] = [
                    'name' => 'Install Dependencies',
                    'success' => $result['success'],
                    'output' => $result['output']
                ];
                if (!$result['success']) {
                    $overall_success = false;
    }
} else {
                $steps[] = [
                    'name' => 'Install Dependencies',
                    'success' => false,
                    'output' => 'Composer not found'
                ];
                $overall_success = false;
            }

            // Step 2: Set permissions
            $directories = ['storage', 'storage/app', 'storage/framework', 'storage/framework/cache', 'storage/framework/sessions', 'storage/framework/views', 'storage/logs', 'bootstrap/cache'];
            $permission_success = true;
foreach ($directories as $dir) {
    if (is_dir($dir)) {
                    $result = runCommand("chmod -R 775 $dir", "Setting permissions for $dir");
                    if (!$result['success']) {
                        $permission_success = false;
                    }
    }
}
            $steps[] = [
                'name' => 'Set Permissions',
                'success' => $permission_success,
                'output' => $permission_success ? 'All permissions set successfully' : 'Some permissions failed'
            ];

            // Step 3: Environment setup
            $env_success = true;
if (!file_exists('.env')) {
    if (file_exists('.env.example')) {
                    $env_success = copy('.env.example', '.env');
        } else {
                    $env_success = false;
                }
            }
            $steps[] = [
                'name' => 'Environment Setup',
                'success' => $env_success,
                'output' => $env_success ? '.env file ready' : 'Failed to create .env file'
            ];

            // Step 4: Generate app key
$envContent = file_get_contents('.env');
if (strpos($envContent, 'APP_KEY=') === false || strpos($envContent, 'APP_KEY=base64:') === false) {
                $result = runCommand('php artisan key:generate', 'Generating application key');
                $steps[] = [
                    'name' => 'Generate App Key',
                    'success' => $result['success'],
                    'output' => $result['output']
                ];
} else {
                $steps[] = [
                    'name' => 'Generate App Key',
                    'success' => true,
                    'output' => 'App key already exists'
                ];
            }

            // Step 5: Clear and cache
            $cache_commands = [
                'php artisan config:clear' => 'Clear Config Cache',
                'php artisan config:cache' => 'Cache Configuration',
                'php artisan route:clear' => 'Clear Route Cache',
                'php artisan route:cache' => 'Cache Routes',
                'php artisan view:clear' => 'Clear View Cache',
                'php artisan view:cache' => 'Cache Views',
                'php artisan event:clear' => 'Clear Event Cache',
                'php artisan event:cache' => 'Cache Events'
            ];

            $cache_success = true;
            foreach ($cache_commands as $command => $description) {
                $result = runCommand($command, $description);
                if (!$result['success']) {
                    $cache_success = false;
                }
            }

            $steps[] = [
                'name' => 'Cache Optimization',
                'success' => $cache_success,
                'output' => $cache_success ? 'All caches optimized successfully' : 'Some cache operations failed'
            ];

            // Step 6: Create storage link
            $result = runCommand('php artisan storage:link', 'Creating symbolic link for storage');
            $steps[] = [
                'name' => 'Create Storage Link',
                'success' => $result['success'],
                'output' => $result['output']
            ];

            // Step 7: Run migrations (optional)
            if ($run_migrations) {
                $result = runCommand('php artisan migrate --force', 'Running database migrations');
                $steps[] = [
                    'name' => 'Database Migrations',
                    'success' => $result['success'],
                    'output' => $result['output']
                ];
                if (!$result['success']) {
                    $overall_success = false;
                }
            }

            // Step 8: Final optimization
            $result = runCommand('php artisan optimize', 'Optimizing application');
            $steps[] = [
                'name' => 'Final Optimization',
                'success' => $result['success'],
                'output' => $result['output']
            ];

            echo json_encode([
                'success' => $overall_success,
                'steps' => $steps
            ]);
            exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Hostel CRM - Deployment</title>
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
        .pulse-animation {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
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
        .loading-dots::after {
            content: '';
            animation: dots 1.5s infinite;
        }
        @keyframes dots {
            0%, 20% { content: ''; }
            40% { content: '.'; }
            60% { content: '..'; }
            80%, 100% { content: '...'; }
        }
        .floating-nav {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
        .floating-nav-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        .floating-nav-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(0,0,0,0.2);
        }
        .floating-menu {
            position: absolute;
            bottom: 70px;
            right: 0;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            padding: 10px;
            min-width: 200px;
            display: none;
        }
        .floating-menu.show {
            display: block;
            animation: slideUp 0.3s ease;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .floating-menu a {
            display: block;
            padding: 8px 12px;
            color: #374151;
            text-decoration: none;
            border-radius: 6px;
            transition: background 0.2s;
            font-size: 14px;
        }
        .floating-menu a:hover {
            background: #f3f4f6;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="gradient-bg text-white py-8">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl font-bold mb-2">
                <i class="fas fa-rocket mr-3"></i>
                Laravel Hostel CRM
            </h1>
            <p class="text-xl opacity-90">Deployment Dashboard</p>

            <!-- Breadcrumb Navigation -->
            <nav class="mt-4 flex justify-center">
                <div class="flex items-center space-x-2 text-sm opacity-80">
                    <a href="deploy.php" class="hover:opacity-100 transition duration-200">
                        <i class="fas fa-home mr-1"></i>Deploy
                    </a>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <a href="deploy-web.php" class="hover:opacity-100 transition duration-200">
                        <i class="fas fa-tools mr-1"></i>Advanced
                    </a>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <a href="clear-cache.php" class="hover:opacity-100 transition duration-200">
                        <i class="fas fa-broom mr-1"></i>Maintenance
                    </a>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <a href="DEPLOY_README.md" class="hover:opacity-100 transition duration-200">
                        <i class="fas fa-book mr-1"></i>Docs
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
                Deployment Tools Navigation
            </h2>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Main Deployment Tools -->
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-4 rounded-lg border border-blue-200">
                    <h3 class="font-bold text-blue-800 mb-2">
                        <i class="fas fa-rocket mr-2"></i>
                        Main Deployment
                    </h3>
                    <div class="space-y-2">
                        <a href="deploy.php" class="block text-blue-600 hover:text-blue-800 text-sm font-medium">
                            <i class="fas fa-globe mr-1"></i> Web Deploy (Current)
                        </a>
                        <a href="deploy-cli.php" class="block text-blue-600 hover:text-blue-800 text-sm font-medium">
                            <i class="fas fa-terminal mr-1"></i> Command Line Deploy
                        </a>
                        <a href="deploy-web.php" class="block text-blue-600 hover:text-blue-800 text-sm font-medium">
                            <i class="fas fa-tools mr-1"></i> Advanced Deploy Interface
                        </a>
                    </div>
                </div>

                <!-- Maintenance Tools -->
                <div class="bg-gradient-to-r from-green-50 to-green-100 p-4 rounded-lg border border-green-200">
                    <h3 class="font-bold text-green-800 mb-2">
                        <i class="fas fa-wrench mr-2"></i>
                        Maintenance
                    </h3>
                    <div class="space-y-2">
                        <a href="clear-cache.php" class="block text-green-600 hover:text-green-800 text-sm font-medium">
                            <i class="fas fa-broom mr-1"></i> Clear All Caches
                        </a>
                        <a href="optimize.php" class="block text-green-600 hover:text-green-800 text-sm font-medium">
                            <i class="fas fa-tachometer-alt mr-1"></i> Optimize Application
                        </a>
                        <a href="maintenance.php" class="block text-green-600 hover:text-green-800 text-sm font-medium">
                            <i class="fas fa-cog mr-1"></i> Maintenance Mode
                        </a>
                    </div>
                </div>

                <!-- Database Tools -->
                <div class="bg-gradient-to-r from-purple-50 to-purple-100 p-4 rounded-lg border border-purple-200">
                    <h3 class="font-bold text-purple-800 mb-2">
                        <i class="fas fa-database mr-2"></i>
                        Database
                    </h3>
                    <div class="space-y-2">
                        <a href="run-migrations.php" class="block text-purple-600 hover:text-purple-800 text-sm font-medium">
                            <i class="fas fa-sync mr-1"></i> Run Migrations
                        </a>
                        <a href="run-migrations.php?seed=1" class="block text-purple-600 hover:text-purple-800 text-sm font-medium">
                            <i class="fas fa-seedling mr-1"></i> Migrations + Seeders
                        </a>
                        <a href="backup-database.php" class="block text-purple-600 hover:text-purple-800 text-sm font-medium">
                            <i class="fas fa-save mr-1"></i> Backup Database
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h3 class="font-bold text-gray-700 mb-3">
                    <i class="fas fa-bolt mr-2"></i>
                    Quick Actions
                </h3>
                <div class="flex flex-wrap gap-2">
                    <a href="deploy-web.php" class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition duration-200">
                        <i class="fas fa-tools mr-2"></i>
                        Advanced Interface
                    </a>
                    <a href="clear-cache.php" class="inline-flex items-center px-3 py-2 bg-yellow-600 text-white text-sm font-medium rounded-md hover:bg-yellow-700 transition duration-200">
                        <i class="fas fa-broom mr-2"></i>
                        Clear Caches
                    </a>
                    <a href="optimize.php" class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition duration-200">
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        Optimize
                    </a>
                    <a href="DEPLOY_README.md" class="inline-flex items-center px-3 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 transition duration-200">
                        <i class="fas fa-book mr-2"></i>
                        Documentation
                    </a>
                </div>
            </div>
        </div>

        <!-- System Requirements Check -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8 card-hover">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">
                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                System Requirements
            </h2>
            <div id="requirements-status" class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="font-medium">Checking requirements...</span>
                    <i class="fas fa-spinner fa-spin text-blue-500"></i>
                </div>
            </div>
        </div>

        <!-- Deployment Section -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8 card-hover">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">
                <i class="fas fa-cogs text-blue-500 mr-2"></i>
                Deployment Process
            </h2>

            <div class="mb-6">
                <p class="text-gray-600 mb-4">
                    Click the button below to start the deployment process. This will:
                </p>
                <ul class="list-disc list-inside text-gray-600 space-y-1 mb-4">
                    <li>Install/update Composer dependencies</li>
                    <li>Set proper file permissions</li>
                    <li>Configure environment settings</li>
                    <li>Generate application key</li>
                    <li>Optimize caches and routes</li>
                    <li>Create storage links</li>
                </ul>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <label class="flex items-center">
                        <input type="checkbox" id="run-migrations" class="mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <span class="text-sm text-gray-700">
                            <strong>Run database migrations</strong> (Make sure your database is configured in .env first)
                        </span>
                    </label>
                </div>
            </div>

            <button id="deploy-btn" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition duration-300 flex items-center justify-center">
                <i class="fas fa-play mr-2"></i>
                Start Deployment
            </button>
        </div>

        <!-- Deployment Progress -->
        <div id="deployment-progress" class="bg-white rounded-lg shadow-lg p-6 mb-8 hidden">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">
                <i class="fas fa-tasks text-purple-500 mr-2"></i>
                Deployment Progress
            </h2>

            <div class="mb-4">
                <div class="flex justify-between text-sm text-gray-600 mb-1">
                    <span>Progress</span>
                    <span id="progress-text">0%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div id="progress-bar" class="bg-blue-600 h-2 rounded-full progress-bar" style="width: 0%"></div>
                </div>
            </div>

            <div id="deployment-steps" class="space-y-3">
                <!-- Steps will be populated here -->
            </div>
        </div>

        <!-- Important Information -->
        <div class="bg-white rounded-lg shadow-lg p-6 card-hover">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">
                <i class="fas fa-info-circle text-yellow-500 mr-2"></i>
                Important Information
            </h2>

            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-bold text-gray-700 mb-2">Post-Deployment Checklist:</h3>
                    <ul class="list-disc list-inside text-gray-600 space-y-1">
                        <li>Configure your .env file with database credentials</li>
                        <li>Run database migrations: <code class="bg-gray-100 px-2 py-1 rounded">php artisan migrate</code></li>
                        <li>Set up your web server to point to the 'public' directory</li>
                        <li>Verify file permissions (755 for directories, 644 for files)</li>
                        <li>Check storage/logs/laravel.log for any issues</li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-bold text-gray-700 mb-2">Useful Commands:</h3>
                    <ul class="list-disc list-inside text-gray-600 space-y-1">
                        <li><code class="bg-gray-100 px-2 py-1 rounded">php artisan migrate</code> - Run migrations</li>
                        <li><code class="bg-gray-100 px-2 py-1 rounded">php artisan db:seed</code> - Run seeders</li>
                        <li><code class="bg-gray-100 px-2 py-1 rounded">php artisan optimize</code> - Optimize app</li>
                        <li><code class="bg-gray-100 px-2 py-1 rounded">php artisan cache:clear</code> - Clear caches</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-6">
                <div>
                    <h3 class="font-bold text-lg mb-3">
                        <i class="fas fa-info-circle mr-2"></i>
                        About This Tool
                    </h3>
                    <p class="text-gray-300 text-sm mb-3">
                        This deployment tool automates the setup process for Laravel applications on shared hosting environments.
                    </p>
                    <div class="space-y-1">
                        <a href="DEPLOY_README.md" class="block text-gray-300 hover:text-white text-sm transition duration-200">
                            <i class="fas fa-book mr-1"></i> Documentation
                        </a>
                        <a href="deploy-cli.php" class="block text-gray-300 hover:text-white text-sm transition duration-200">
                            <i class="fas fa-terminal mr-1"></i> CLI Version
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="font-bold text-lg mb-3">
                        <i class="fas fa-tools mr-2"></i>
                        Deployment Tools
                    </h3>
                    <div class="space-y-1">
                        <a href="deploy-web.php" class="block text-gray-300 hover:text-white text-sm transition duration-200">
                            <i class="fas fa-cogs mr-1"></i> Advanced Interface
                        </a>
                        <a href="clear-cache.php" class="block text-gray-300 hover:text-white text-sm transition duration-200">
                            <i class="fas fa-broom mr-1"></i> Clear Caches
                        </a>
                        <a href="optimize.php" class="block text-gray-300 hover:text-white text-sm transition duration-200">
                            <i class="fas fa-tachometer-alt mr-1"></i> Optimize App
                        </a>
                        <a href="maintenance.php" class="block text-gray-300 hover:text-white text-sm transition duration-200">
                            <i class="fas fa-wrench mr-1"></i> Maintenance Mode
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="font-bold text-lg mb-3">
                        <i class="fas fa-database mr-2"></i>
                        Database Tools
                    </h3>
                    <div class="space-y-1">
                        <a href="run-migrations.php" class="block text-gray-300 hover:text-white text-sm transition duration-200">
                            <i class="fas fa-sync mr-1"></i> Run Migrations
                        </a>
                        <a href="run-migrations.php?seed=1" class="block text-gray-300 hover:text-white text-sm transition duration-200">
                            <i class="fas fa-seedling mr-1"></i> With Seeders
                        </a>
                        <a href="backup-database.php" class="block text-gray-300 hover:text-white text-sm transition duration-200">
                            <i class="fas fa-save mr-1"></i> Backup Database
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="font-bold text-lg mb-3">
                        <i class="fas fa-shield-alt mr-2"></i>
                        Security & Help
                    </h3>
                    <p class="text-gray-300 text-sm mb-3">
                        Remember to delete deployment files after successful setup for security.
                    </p>
                    <div class="space-y-1">
                        <a href="https://laravel.com/docs" target="_blank" class="block text-gray-300 hover:text-white text-sm transition duration-200">
                            <i class="fas fa-external-link-alt mr-1"></i> Laravel Docs
                        </a>
                        <a href="https://github.com" target="_blank" class="block text-gray-300 hover:text-white text-sm transition duration-200">
                            <i class="fab fa-github mr-1"></i> GitHub
                        </a>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-700 mt-8 pt-6 text-center">
                <div class="flex flex-wrap justify-center items-center gap-4 mb-4">
                    <span class="text-gray-400 text-sm">Quick Access:</span>
                    <a href="deploy-web.php" class="text-gray-400 hover:text-white text-sm transition duration-200">Advanced</a>
                    <span class="text-gray-600">•</span>
                    <a href="clear-cache.php" class="text-gray-400 hover:text-white text-sm transition duration-200">Cache</a>
                    <span class="text-gray-600">•</span>
                    <a href="optimize.php" class="text-gray-400 hover:text-white text-sm transition duration-200">Optimize</a>
                    <span class="text-gray-600">•</span>
                    <a href="run-migrations.php" class="text-gray-400 hover:text-white text-sm transition duration-200">Migrations</a>
                </div>
                <p class="text-gray-400 text-sm">
                    Laravel Hostel CRM Deployment Tool - Built with ❤️ for easy deployment
                </p>
            </div>
        </div>
    </footer>

    <!-- Floating Navigation -->
    <div class="floating-nav">
        <div class="floating-menu" id="floating-menu">
            <a href="deploy-web.php">
                <i class="fas fa-tools mr-2"></i>Advanced Interface
            </a>
            <a href="clear-cache.php">
                <i class="fas fa-broom mr-2"></i>Clear Caches
            </a>
            <a href="optimize.php">
                <i class="fas fa-tachometer-alt mr-2"></i>Optimize
            </a>
            <a href="run-migrations.php">
                <i class="fas fa-sync mr-2"></i>Migrations
            </a>
            <a href="backup-database.php">
                <i class="fas fa-save mr-2"></i>Backup DB
            </a>
            <a href="maintenance.php">
                <i class="fas fa-wrench mr-2"></i>Maintenance
            </a>
            <a href="DEPLOY_README.md">
                <i class="fas fa-book mr-2"></i>Documentation
            </a>
        </div>
        <button class="floating-nav-btn" onclick="toggleFloatingMenu()">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <script>
        // Check system requirements on page load
        document.addEventListener('DOMContentLoaded', function() {
            checkRequirements();
        });

        // Floating navigation menu
        function toggleFloatingMenu() {
            const menu = document.getElementById('floating-menu');
            menu.classList.toggle('show');
        }

        // Close floating menu when clicking outside
        document.addEventListener('click', function(event) {
            const floatingNav = document.querySelector('.floating-nav');
            const menu = document.getElementById('floating-menu');

            if (!floatingNav.contains(event.target)) {
                menu.classList.remove('show');
            }
        });

        function checkRequirements() {
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=check_requirements'
            })
            .then(response => response.json())
            .then(data => {
                displayRequirements(data);
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('requirements-status').innerHTML =
                    '<div class="p-3 bg-red-50 border border-red-200 rounded-lg text-red-700">Error checking requirements</div>';
            });
        }

        function displayRequirements(data) {
            const container = document.getElementById('requirements-status');
            container.innerHTML = '';

            // PHP Version
            const phpStatus = data.php_ok ? 'success' : 'warning';
            const phpIcon = data.php_ok ? 'fa-check-circle text-green-500' : 'fa-exclamation-triangle text-yellow-500';
            container.innerHTML += `
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="font-medium">PHP Version (${data.php_version})</span>
                    <i class="fas ${phpIcon}"></i>
                </div>
            `;

            // Extensions
            const extStatus = data.extensions_ok ? 'success' : 'error';
            const extIcon = data.extensions_ok ? 'fa-check-circle text-green-500' : 'fa-times-circle text-red-500';
            const extText = data.extensions_ok ? 'All required extensions available' : `Missing: ${data.missing_extensions.join(', ')}`;
            container.innerHTML += `
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="font-medium">${extText}</span>
                    <i class="fas ${extIcon}"></i>
                </div>
            `;

            // Composer
            const composerIcon = data.composer_available ? 'fa-check-circle text-green-500' : 'fa-exclamation-triangle text-yellow-500';
            const composerText = data.composer_available ? 'Composer available' : 'Composer not found';
            container.innerHTML += `
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="font-medium">${composerText}</span>
                    <i class="fas ${composerIcon}"></i>
                </div>
            `;
        }

        // Handle deployment
        document.getElementById('deploy-btn').addEventListener('click', function() {
            const btn = this;
            const progressDiv = document.getElementById('deployment-progress');
            const stepsDiv = document.getElementById('deployment-steps');

            // Disable button and show progress
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Deploying...';
            progressDiv.classList.remove('hidden');
            stepsDiv.innerHTML = '';

            // Start deployment
            const runMigrations = document.getElementById('run-migrations').checked;
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=deploy&run_migrations=${runMigrations}`
            })
            .then(response => response.json())
            .then(data => {
                displayDeploymentResults(data);

                // Re-enable button
                btn.disabled = false;
                if (data.success) {
                    btn.innerHTML = '<i class="fas fa-check mr-2"></i>Deployment Complete';
                    btn.className = 'w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition duration-300 flex items-center justify-center';
} else {
                    btn.innerHTML = '<i class="fas fa-redo mr-2"></i>Retry Deployment';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                stepsDiv.innerHTML = '<div class="p-3 bg-red-50 border border-red-200 rounded-lg text-red-700">Deployment failed due to an error</div>';

                // Re-enable button
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-redo mr-2"></i>Retry Deployment';
            });
        });

        function displayDeploymentResults(data) {
            const stepsDiv = document.getElementById('deployment-steps');
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
