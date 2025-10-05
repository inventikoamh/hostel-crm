<?php
/**
 * Laravel Database Backup Utility - Styled Web Interface
 *
 * This script provides a modern web interface for creating database backups.
 * Access this file through your web browser to backup your database.
 *
 * Usage: Visit http://yourdomain.com/backup-database.php in your browser
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

// Check if .env file exists
if (!file_exists('.env')) {
    die("❌ Error: .env file not found. Please create it from .env.example and configure your database settings.");
}

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

// Function to get database configuration
function getDatabaseConfig() {
    $envContent = file_get_contents('.env');
    $config = [];

    $lines = explode("\n", $envContent);
    foreach ($lines as $line) {
        if (strpos($line, 'DB_') === 0) {
            $parts = explode('=', $line, 2);
            if (count($parts) === 2) {
                $key = trim($parts[0]);
                $value = trim($parts[1]);
                $config[$key] = $value;
            }
        }
    }

    return $config;
}

// Handle AJAX requests
if (isset($_POST['action']) && $_POST['action'] === 'backup_database') {
    header('Content-Type: application/json');

    $steps = [];
    $overall_success = true;
    $backup_name = $_POST['backup_name'] ?? 'backup-' . date('Y-m-d-H-i-s');
    $compress = isset($_POST['compress']) && $_POST['compress'] === 'true';

    // Step 1: Check database configuration
    $dbConfig = getDatabaseConfig();
$dbHost = $dbConfig['DB_HOST'] ?? 'localhost';
$dbPort = $dbConfig['DB_PORT'] ?? '3306';
    $dbDatabase = $dbConfig['DB_DATABASE'] ?? '';
    $dbUsername = $dbConfig['DB_USERNAME'] ?? '';
    $dbPassword = $dbConfig['DB_PASSWORD'] ?? '';

    if (empty($dbDatabase) || empty($dbUsername)) {
        $steps[] = [
            'name' => 'Database Configuration Check',
            'success' => false,
            'output' => 'Database configuration incomplete. Please check your .env file.'
        ];
        $overall_success = false;
    } else {
        $steps[] = [
            'name' => 'Database Configuration Check',
            'success' => true,
            'output' => "Database: $dbDatabase, Host: $dbHost:$dbPort"
        ];
    }

    // Step 2: Create backup directory
    if ($overall_success) {
$backupDir = 'storage/backups';
if (!is_dir($backupDir)) {
            if (mkdir($backupDir, 0755, true)) {
                $steps[] = [
                    'name' => 'Create Backup Directory',
                    'success' => true,
                    'output' => "Created directory: $backupDir"
                ];
            } else {
                $steps[] = [
                    'name' => 'Create Backup Directory',
                    'success' => false,
                    'output' => "Failed to create directory: $backupDir"
                ];
                $overall_success = false;
            }
        } else {
            $steps[] = [
                'name' => 'Create Backup Directory',
                'success' => true,
                'output' => "Directory exists: $backupDir"
            ];
        }
    }

    // Step 3: Create database backup
    if ($overall_success) {
        $backupFile = "storage/backups/$backup_name.sql";
        $mysqldumpCommand = "mysqldump -h $dbHost -P $dbPort -u $dbUsername -p$dbPassword $dbDatabase > $backupFile";

        $result = runCommand($mysqldumpCommand, 'Creating database backup');
        $steps[] = [
            'name' => 'Database Backup',
            'success' => $result['success'],
            'output' => $result['success'] ? "Backup created: $backupFile" : $result['output']
        ];
        if (!$result['success']) $overall_success = false;
    }

    // Step 4: Compress backup if requested
    if ($overall_success && $compress) {
        $backupFile = "storage/backups/$backup_name.sql";
        $compressedFile = "storage/backups/$backup_name.sql.gz";

    if (file_exists($backupFile)) {
            $result = runCommand("gzip -c $backupFile > $compressedFile", 'Compressing backup');
            if ($result['success'] && file_exists($compressedFile)) {
                // Remove original file after compression
            unlink($backupFile);
                $steps[] = [
                    'name' => 'Compress Backup',
                    'success' => true,
                    'output' => "Compressed backup: $compressedFile"
                ];
        } else {
                $steps[] = [
                    'name' => 'Compress Backup',
                    'success' => false,
                    'output' => 'Compression failed'
                ];
            }
        }
    }

    // Step 5: Verify backup
    if ($overall_success) {
        $finalFile = $compress ? "storage/backups/$backup_name.sql.gz" : "storage/backups/$backup_name.sql";
        if (file_exists($finalFile)) {
            $fileSize = filesize($finalFile);
            $fileSizeFormatted = formatBytes($fileSize);
            $steps[] = [
                'name' => 'Verify Backup',
                'success' => true,
                'output' => "Backup verified: $finalFile ($fileSizeFormatted)"
            ];
        } else {
            $steps[] = [
                'name' => 'Verify Backup',
                'success' => false,
                'output' => 'Backup file not found'
            ];
            $overall_success = false;
        }
    }

    echo json_encode([
        'success' => $overall_success,
        'steps' => $steps,
        'backup_file' => $overall_success ? ($compress ? "$backup_name.sql.gz" : "$backup_name.sql") : null
    ]);
    exit;
}

// Function to format bytes
function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }

    return round($bytes, $precision) . ' ' . $units[$i];
}

// Get existing backups
function getExistingBackups() {
    $backupDir = 'storage/backups';
    $backups = [];

    if (is_dir($backupDir)) {
        $files = scandir($backupDir);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..' && (strpos($file, '.sql') !== false || strpos($file, '.gz') !== false)) {
                $filePath = "$backupDir/$file";
                $backups[] = [
                    'name' => $file,
                    'size' => formatBytes(filesize($filePath)),
                    'date' => date('Y-m-d H:i:s', filemtime($filePath)),
                    'path' => $filePath
                ];
            }
        }
    }

    // Sort by date (newest first)
    usort($backups, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });

    return $backups;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Hostel CRM - Database Backup</title>
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
                <i class="fas fa-save mr-3"></i>
                Laravel Hostel CRM
            </h1>
            <p class="text-xl opacity-90">Database Backup</p>

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
                <a href="run-migrations.php" class="inline-flex items-center px-3 py-2 bg-purple-600 text-white text-sm font-medium rounded-md hover:bg-purple-700 transition duration-200">
                    <i class="fas fa-sync mr-2"></i>Migrations
                </a>
                <a href="clear-cache.php" class="inline-flex items-center px-3 py-2 bg-yellow-600 text-white text-sm font-medium rounded-md hover:bg-yellow-700 transition duration-200">
                    <i class="fas fa-broom mr-2"></i>Clear Cache
                </a>
                <a href="optimize.php" class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-white text-sm font-medium rounded-md hover:bg-green-700 transition duration-200">
                    <i class="fas fa-tachometer-alt mr-2"></i>Optimize
                </a>
                <a href="maintenance.php" class="inline-flex items-center px-3 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 transition duration-200">
                    <i class="fas fa-wrench mr-2"></i>Maintenance
                </a>
            </div>
        </div>

        <!-- Backup Creation Section -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8 card-hover">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">
                <i class="fas fa-save text-red-500 mr-2"></i>
                Create Database Backup
            </h2>

            <div class="mb-6">
                <p class="text-gray-600 mb-4">
                    Create a backup of your database. This will export all tables and data to a SQL file.
                </p>

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label for="backup-name" class="block text-sm font-medium text-gray-700 mb-2">
                            Backup Name
                        </label>
                        <input type="text" id="backup-name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                               value="<?php echo 'backup-' . date('Y-m-d-H-i-s'); ?>" placeholder="Enter backup name">
                    </div>

                    <div class="flex items-center">
                        <label class="flex items-center">
                            <input type="checkbox" id="compress-backup" class="mr-3 h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded" checked>
                            <span class="text-sm text-gray-700">
                                <strong>Compress backup</strong> (saves disk space)
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            <button id="create-backup-btn" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg transition duration-300 flex items-center justify-center">
                <i class="fas fa-save mr-2"></i>
                Create Backup
            </button>
        </div>

        <!-- Backup Progress -->
        <div id="backup-progress" class="bg-white rounded-lg shadow-lg p-6 mb-8 hidden">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">
                <i class="fas fa-tasks text-red-500 mr-2"></i>
                Backup Progress
            </h2>

            <div class="mb-4">
                <div class="flex justify-between text-sm text-gray-600 mb-1">
                    <span>Progress</span>
                    <span id="progress-text">0%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div id="progress-bar" class="bg-red-600 h-2 rounded-full progress-bar" style="width: 0%"></div>
                </div>
            </div>

            <div id="backup-steps" class="space-y-3">
                <!-- Steps will be populated here -->
            </div>
        </div>

        <!-- Existing Backups -->
        <div class="bg-white rounded-lg shadow-lg p-6 card-hover">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">
                <i class="fas fa-history text-blue-500 mr-2"></i>
                Existing Backups
            </h2>

            <?php $existingBackups = getExistingBackups(); ?>
            <?php if (empty($existingBackups)): ?>
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-database text-4xl mb-4"></i>
                    <p>No backups found. Create your first backup above.</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($existingBackups as $backup): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <i class="fas fa-file-archive mr-2"></i>
                                    <?php echo htmlspecialchars($backup['name']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo $backup['size']; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo $backup['date']; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="<?php echo $backup['path']; ?>" download class="text-red-600 hover:text-red-900 mr-3">
                                        <i class="fas fa-download mr-1"></i>Download
                                    </a>
                                    <button onclick="deleteBackup('<?php echo $backup['name']; ?>')" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash mr-1"></i>Delete
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
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
                        <a href="run-migrations.php" class="block text-gray-300 hover:text-white text-sm transition duration-200">
                            <i class="fas fa-sync mr-1"></i> Migrations
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
                        <a href="run-migrations.php" class="block text-gray-300 hover:text-white text-sm transition duration-200">
                            <i class="fas fa-sync mr-1"></i> Run Migrations
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
                        Regular backups are essential for data protection. Store backups securely and test restoration procedures.
                    </p>
                </div>
            </div>

            <div class="border-t border-gray-700 mt-6 pt-6">
                <p class="text-gray-400 text-sm">
                    Laravel Hostel CRM Backup Tool - Built with ❤️ for data protection
                </p>
            </div>
        </div>
    </footer>

    <script>
        // Handle backup creation
        document.getElementById('create-backup-btn').addEventListener('click', function() {
            const btn = this;
            const progressDiv = document.getElementById('backup-progress');
            const stepsDiv = document.getElementById('backup-steps');
            const backupName = document.getElementById('backup-name').value;
            const compress = document.getElementById('compress-backup').checked;

            if (!backupName.trim()) {
                alert('Please enter a backup name');
                return;
            }

            // Disable button and show progress
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating Backup...';
            progressDiv.classList.remove('hidden');
            stepsDiv.innerHTML = '';

            // Start backup
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=backup_database&backup_name=${encodeURIComponent(backupName)}&compress=${compress}`
            })
            .then(response => response.json())
            .then(data => {
                displayBackupResults(data);

                // Re-enable button
                btn.disabled = false;
                if (data.success) {
                    btn.innerHTML = '<i class="fas fa-check mr-2"></i>Backup Created';
                    btn.className = 'w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition duration-300 flex items-center justify-center';
                    // Reload page to show new backup
                    setTimeout(() => location.reload(), 2000);
                } else {
                    btn.innerHTML = '<i class="fas fa-redo mr-2"></i>Retry Backup';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                stepsDiv.innerHTML = '<div class="p-3 bg-red-50 border border-red-200 rounded-lg text-red-700">Backup failed due to an error</div>';

                // Re-enable button
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-redo mr-2"></i>Retry Backup';
            });
        });

        function displayBackupResults(data) {
            const stepsDiv = document.getElementById('backup-steps');
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

        function deleteBackup(filename) {
            if (confirm(`Are you sure you want to delete ${filename}?`)) {
                // This would need to be implemented with a separate endpoint
                alert('Delete functionality would be implemented here');
            }
        }
    </script>
</body>
</html>
