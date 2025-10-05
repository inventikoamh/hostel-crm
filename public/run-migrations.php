<?php
/**
 * Laravel Migration Runner - Styled Web Interface
 *
 * This script provides a modern web interface for running database migrations.
 * Access this file through your web browser to run migrations.
 *
 * Usage: Visit http://yourdomain.com/run-migrations.php in your browser
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

// Debug: Log all POST data
error_log("POST data received: " . print_r($_POST, true));

// Simple test endpoint
if (isset($_POST['action']) && $_POST['action'] === 'test') {
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Test successful', 'post_data' => $_POST]);
    exit;
}

// Handle AJAX requests
if (isset($_POST['action']) && $_POST['action'] === 'run_migrations') {
    header('Content-Type: application/json');

    $steps = [];
    $overall_success = true;
    $migration_type = $_POST['migration_type'] ?? 'normal';
    $run_seeders = isset($_POST['run_seeders']) && ($_POST['run_seeders'] === 'true' || $_POST['run_seeders'] === 'on');

    // Debug: Log received data
    error_log("Migration request received - Type: $migration_type, Seeders: " . ($run_seeders ? 'true' : 'false'));

    // Step 1: Check database connection
    $result = runCommand('php artisan migrate:status', 'Checking database connection and migration status');
    $steps[] = [
        'name' => 'Database Connection Check',
        'success' => $result['success'],
        'output' => $result['success'] ? 'Database connection successful' : 'Database connection failed: ' . $result['output']
    ];
    if (!$result['success']) $overall_success = false;

    // Step 2: Run migrations based on type
    if ($overall_success) {
        $migration_command = 'php artisan migrate --force';

        switch ($migration_type) {
            case 'fresh':
                $migration_command = 'php artisan migrate:fresh --force';
                break;
            case 'fresh_seed':
                $migration_command = 'php artisan migrate:fresh --seed --force';
                break;
            case 'rollback':
                $migration_command = 'php artisan migrate:rollback --force';
                break;
            case 'reset':
                $migration_command = 'php artisan migrate:reset --force';
                break;
        }

        $result = runCommand($migration_command, 'Running database migrations');
        $steps[] = [
            'name' => 'Database Migrations',
            'success' => $result['success'],
            'output' => $result['output']
        ];
        if (!$result['success']) $overall_success = false;
    }

    // Step 3: Run seeders if requested and migrations were successful
    if ($overall_success && $run_seeders && $migration_type !== 'fresh_seed') {
        $result = runCommand('php artisan db:seed --force', 'Running database seeders');
        $steps[] = [
            'name' => 'Database Seeders',
            'success' => $result['success'],
            'output' => $result['output']
        ];
        if (!$result['success']) $overall_success = false;
    }

    // Step 4: Show final migration status
    if ($overall_success) {
        $result = runCommand('php artisan migrate:status', 'Final migration status');
        $steps[] = [
            'name' => 'Migration Status',
            'success' => $result['success'],
            'output' => $result['success'] ? 'All migrations completed successfully' : 'Error checking final status'
        ];
    }

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
    <title>Laravel Hostel CRM - Database Migrations</title>
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
                <i class="fas fa-database mr-3"></i>
                Laravel Hostel CRM
            </h1>
            <p class="text-xl opacity-90">Database Migrations</p>

            <!-- Breadcrumb Navigation -->
            <nav class="mt-4 flex justify-center">
                <div class="flex items-center space-x-2 text-sm opacity-80">
                    <a href="deploy.php" class="hover:opacity-100 transition duration-200">
                        <i class="fas fa-home mr-1"></i>Deploy
                    </a>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <a href="run-migrations.php" class="hover:opacity-100 transition duration-200">
                        <i class="fas fa-sync mr-1"></i>Migrations
                    </a>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <a href="backup-database.php" class="hover:opacity-100 transition duration-200">
                        <i class="fas fa-save mr-1"></i>Backup
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
                <a href="backup-database.php" class="inline-flex items-center px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 transition duration-200">
                    <i class="fas fa-save mr-2"></i>Backup
                </a>
                <a href="maintenance.php" class="inline-flex items-center px-3 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 transition duration-200">
                    <i class="fas fa-wrench mr-2"></i>Maintenance
                </a>
            </div>
        </div>

        <!-- Migration Options -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8 card-hover">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">
                <i class="fas fa-sync text-purple-500 mr-2"></i>
                Migration Options
            </h2>

            <div class="mb-6">
                <p class="text-gray-600 mb-4">
                    Choose the type of migration you want to run:
                </p>

                <div class="grid md:grid-cols-2 gap-4">
                    <div class="space-y-3">
                        <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="migration_type" value="normal" class="mr-3" checked>
                            <div>
                                <div class="font-medium text-gray-800">Normal Migrations</div>
                                <div class="text-sm text-gray-600">Run pending migrations only</div>
                            </div>
                        </label>

                        <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="migration_type" value="fresh" class="mr-3">
                            <div>
                                <div class="font-medium text-gray-800">Fresh Migrations</div>
                                <div class="text-sm text-gray-600">Drop all tables and re-run migrations</div>
                            </div>
                        </label>

                        <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="migration_type" value="fresh_seed" class="mr-3">
                            <div>
                                <div class="font-medium text-gray-800">Fresh + Seeders</div>
                                <div class="text-sm text-gray-600">Fresh migrations with database seeding</div>
                            </div>
                        </label>
                    </div>

                    <div class="space-y-3">
                        <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="migration_type" value="rollback" class="mr-3">
                            <div>
                                <div class="font-medium text-gray-800">Rollback</div>
                                <div class="text-sm text-gray-600">Rollback the last batch of migrations</div>
                            </div>
                        </label>

                        <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="migration_type" value="reset" class="mr-3">
                            <div>
                                <div class="font-medium text-gray-800">Reset</div>
                                <div class="text-sm text-gray-600">Rollback all migrations</div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" id="run-seeders" class="mr-3 h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                    <span class="text-sm text-gray-700">
                        <strong>Run database seeders</strong> (Only applies to normal migrations)
                    </span>
                </label>
            </div>

            <button id="test-ajax-btn" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300 flex items-center justify-center mb-2">
                <i class="fas fa-flask mr-2"></i>
                Test AJAX Connection
            </button>

            <button id="run-migrations-btn" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg transition duration-300 flex items-center justify-center">
                <i class="fas fa-sync mr-2"></i>
                Run Migrations
            </button>
        </div>

        <!-- Migration Progress -->
        <div id="migration-progress" class="bg-white rounded-lg shadow-lg p-6 mb-8 hidden">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">
                <i class="fas fa-tasks text-purple-500 mr-2"></i>
                Migration Progress
            </h2>

            <div class="mb-4">
                <div class="flex justify-between text-sm text-gray-600 mb-1">
                    <span>Progress</span>
                    <span id="progress-text">0%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div id="progress-bar" class="bg-purple-600 h-2 rounded-full progress-bar" style="width: 0%"></div>
                </div>
            </div>

            <div id="migration-steps" class="space-y-3">
                <!-- Steps will be populated here -->
            </div>
        </div>

        <!-- Migration Information -->
        <div class="bg-white rounded-lg shadow-lg p-6 card-hover">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">
                <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                Migration Information
            </h2>

            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-bold text-gray-700 mb-2">Migration Types:</h3>
                    <ul class="list-disc list-inside text-gray-600 space-y-1">
                        <li><strong>Normal</strong> - Run only pending migrations</li>
                        <li><strong>Fresh</strong> - Drop all tables and re-run migrations</li>
                        <li><strong>Fresh + Seeders</strong> - Fresh migrations with seeding</li>
                        <li><strong>Rollback</strong> - Undo the last batch of migrations</li>
                        <li><strong>Reset</strong> - Undo all migrations</li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-bold text-gray-700 mb-2">Important Notes:</h3>
                    <ul class="list-disc list-inside text-gray-600 space-y-1">
                        <li>Make sure your database is configured in .env</li>
                        <li>Fresh migrations will delete all existing data</li>
                        <li>Always backup your database before running migrations</li>
                        <li>Test migrations in a development environment first</li>
                        <li>Seeders populate the database with sample data</li>
                    </ul>
                </div>
            </div>

            <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <h4 class="font-bold text-red-800 mb-2">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Warning
                </h4>
                <p class="text-red-700 text-sm">
                    <strong>Fresh migrations and reset operations will permanently delete all data!</strong>
                    Make sure you have a backup before proceeding with these operations.
                </p>
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
                        <a href="backup-database.php" class="block text-gray-300 hover:text-white text-sm transition duration-200">
                            <i class="fas fa-save mr-1"></i> Backup Database
                        </a>
                        <a href="deploy-web.php" class="block text-gray-300 hover:text-white text-sm transition duration-200">
                            <i class="fas fa-cogs mr-1"></i> Advanced
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="font-bold text-lg mb-2">
                        <i class="fas fa-database mr-2"></i>
                        Database Operations
                    </h3>
                    <div class="space-y-1">
                        <a href="backup-database.php" class="block text-gray-300 hover:text-white text-sm transition duration-200">
                            <i class="fas fa-save mr-1"></i> Create Backup
                        </a>
                        <a href="clear-cache.php" class="block text-gray-300 hover:text-white text-sm transition duration-200">
                            <i class="fas fa-broom mr-1"></i> Clear Cache
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="font-bold text-lg mb-2">
                        <i class="fas fa-shield-alt mr-2"></i>
                        Security Note
                    </h3>
                    <p class="text-gray-300 text-sm">
                        Always backup your database before running migrations, especially fresh or reset operations.
                    </p>
                </div>
            </div>

            <div class="border-t border-gray-700 mt-6 pt-6">
                <p class="text-gray-400 text-sm">
                    Laravel Hostel CRM Migration Tool - Built with ❤️ for safe database management
                </p>
            </div>
        </div>
    </footer>

    <script>
        // Test AJAX connection
        document.getElementById('test-ajax-btn').addEventListener('click', function() {
            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Testing...';

            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=test&test_data=hello'
            })
            .then(response => response.json())
            .then(data => {
                alert('AJAX Test Result: ' + JSON.stringify(data, null, 2));
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-flask mr-2"></i>Test AJAX Connection';
            })
            .catch(error => {
                alert('AJAX Test Failed: ' + error.message);
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-flask mr-2"></i>Test AJAX Connection';
            });
        });

        // Handle migration execution
        document.getElementById('run-migrations-btn').addEventListener('click', function() {
            const btn = this;
            const progressDiv = document.getElementById('migration-progress');
            const stepsDiv = document.getElementById('migration-steps');

            // Get selected migration type
            const migrationType = document.querySelector('input[name="migration_type"]:checked').value;
            const runSeeders = document.getElementById('run-seeders').checked;

            // Disable button and show progress
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Running Migrations...';
            progressDiv.classList.remove('hidden');
            stepsDiv.innerHTML = '';

            // Start migration
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=run_migrations&migration_type=${migrationType}&run_seeders=${runSeeders}`
            })
            .then(response => response.json())
            .then(data => {
                displayMigrationResults(data);

                // Re-enable button
                btn.disabled = false;
                if (data.success) {
                    btn.innerHTML = '<i class="fas fa-check mr-2"></i>Migrations Complete';
                    btn.className = 'w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition duration-300 flex items-center justify-center';
} else {
                    btn.innerHTML = '<i class="fas fa-redo mr-2"></i>Retry Migrations';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                stepsDiv.innerHTML = '<div class="p-3 bg-red-50 border border-red-200 rounded-lg text-red-700">Migration failed due to an error</div>';

                // Re-enable button
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-redo mr-2"></i>Retry Migrations';
            });
        });

        function displayMigrationResults(data) {
            const stepsDiv = document.getElementById('migration-steps');
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
