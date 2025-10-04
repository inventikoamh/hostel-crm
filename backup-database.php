<?php
/**
 * Laravel Database Backup Utility
 *
 * This script creates a backup of your Laravel database.
 *
 * Usage: php backup-database.php [backup-name]
 */

// Set execution time limit
set_time_limit(300); // 5 minutes

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "💾 Laravel Database Backup Utility\n\n";

// Check if we're in the right directory
if (!file_exists('artisan')) {
    die("❌ Error: artisan file not found. Please run this script from your Laravel project root directory.\n");
}

// Check if .env file exists
if (!file_exists('.env')) {
    die("❌ Error: .env file not found. Please create it from .env.example and configure your database settings.\n");
}

// Parse command line arguments
$args = $argv ?? [];
$backupName = $args[1] ?? 'backup-' . date('Y-m-d-H-i-s');

// Function to run commands
function runCommand($command, $description) {
    echo "📋 $description...\n";

    $output = [];
    $return_var = 0;

    exec($command . ' 2>&1', $output, $return_var);

    if ($return_var === 0) {
        echo "✅ Success!\n";
        if (!empty($output)) {
            echo "Output: " . implode("\n", $output) . "\n";
        }
    } else {
        echo "❌ Error occurred!\n";
        echo "Output: " . implode("\n", $output) . "\n";
    }

    echo "\n";
    return $return_var === 0;
}

// Function to get database configuration from .env
function getDatabaseConfig() {
    $envContent = file_get_contents('.env');
    $config = [];

    $lines = explode("\n", $envContent);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && !empty(trim($line))) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            if (in_array($key, ['DB_CONNECTION', 'DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'])) {
                $config[$key] = $value;
            }
        }
    }

    return $config;
}

// Get database configuration
echo "🔍 Reading database configuration...\n";
$dbConfig = getDatabaseConfig();

if (empty($dbConfig['DB_DATABASE'])) {
    die("❌ Error: Database name not found in .env file.\n");
}

$dbHost = $dbConfig['DB_HOST'] ?? 'localhost';
$dbPort = $dbConfig['DB_PORT'] ?? '3306';
$dbName = $dbConfig['DB_DATABASE'];
$dbUser = $dbConfig['DB_USERNAME'] ?? '';
$dbPass = $dbConfig['DB_PASSWORD'] ?? '';

echo "Database: $dbName\n";
echo "Host: $dbHost:$dbPort\n";
echo "User: $dbUser\n\n";

// Create backup directory if it doesn't exist
$backupDir = 'storage/backups';
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
    echo "📁 Created backup directory: $backupDir\n";
}

// Check if mysqldump is available
$mysqldumpPath = '';
$possiblePaths = [
    'mysqldump',
    '/usr/bin/mysqldump',
    '/usr/local/bin/mysqldump',
    '/opt/lampp/bin/mysqldump',
    '/Applications/XAMPP/bin/mysqldump'
];

foreach ($possiblePaths as $path) {
    $output = [];
    $return_var = 0;
    exec("which $path 2>/dev/null", $output, $return_var);
    if ($return_var === 0) {
        $mysqldumpPath = $path;
        break;
    }
}

if (empty($mysqldumpPath)) {
    echo "❌ Error: mysqldump not found. Please ensure MySQL client tools are installed.\n";
    echo "You can also use Laravel's built-in backup functionality.\n";
    exit(1);
}

echo "✅ Found mysqldump at: $mysqldumpPath\n\n";

// Create backup filename
$backupFile = "$backupDir/$backupName.sql";
$backupFileGz = "$backupFile.gz";

// Build mysqldump command
$command = "$mysqldumpPath";
$command .= " --host=$dbHost";
$command .= " --port=$dbPort";
$command .= " --user=$dbUser";
if (!empty($dbPass)) {
    $command .= " --password=$dbPass";
}
$command .= " --single-transaction";
$command .= " --routines";
$command .= " --triggers";
$command .= " --add-drop-table";
$command .= " --add-locks";
$command .= " --create-options";
$command .= " --disable-keys";
$command .= " --extended-insert";
$command .= " --quick";
$command .= " --set-charset";
$command .= " $dbName > $backupFile";

echo "🔄 Creating database backup...\n";
echo "Backup file: $backupFile\n";

// Run mysqldump
$output = [];
$return_var = 0;
exec($command . ' 2>&1', $output, $return_var);

if ($return_var === 0) {
    echo "✅ Database backup created successfully!\n";

    // Check file size
    if (file_exists($backupFile)) {
        $fileSize = filesize($backupFile);
        $fileSizeFormatted = formatBytes($fileSize);
        echo "📊 Backup size: $fileSizeFormatted\n";

        // Compress the backup
        echo "🗜️  Compressing backup...\n";
        $output = [];
        $return_var = 0;
        exec("gzip -c $backupFile > $backupFileGz 2>&1", $output, $return_var);

        if ($return_var === 0 && file_exists($backupFileGz)) {
            $compressedSize = filesize($backupFileGz);
            $compressedSizeFormatted = formatBytes($compressedSize);
            echo "✅ Backup compressed successfully!\n";
            echo "📊 Compressed size: $compressedSizeFormatted\n";

            // Remove uncompressed file
            unlink($backupFile);
            echo "🗑️  Removed uncompressed backup file.\n";

            $finalBackupFile = $backupFileGz;
        } else {
            echo "⚠️  Compression failed, keeping uncompressed backup.\n";
            $finalBackupFile = $backupFile;
        }

        echo "\n🎉 Backup completed successfully!\n";
        echo "📁 Backup location: $finalBackupFile\n";

        // List recent backups
        echo "\n📋 Recent backups:\n";
        $backups = glob("$backupDir/*.sql*");
        rsort($backups);
        $recentBackups = array_slice($backups, 0, 5);

        foreach ($recentBackups as $backup) {
            $size = formatBytes(filesize($backup));
            $date = date('Y-m-d H:i:s', filemtime($backup));
            $filename = basename($backup);
            echo "- $filename ($size) - $date\n";
        }

    } else {
        echo "❌ Backup file was not created.\n";
    }
} else {
    echo "❌ Database backup failed!\n";
    echo "Output: " . implode("\n", $output) . "\n";
}

// Function to format bytes
function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }

    return round($bytes, $precision) . ' ' . $units[$i];
}

echo "\n💡 Tips:\n";
echo "1. Store backups in a secure location outside your web directory\n";
echo "2. Set up automated backups using cron jobs\n";
echo "3. Test your backups regularly by restoring them\n";
echo "4. Consider using Laravel's backup packages for more advanced features\n";
?>
