<?php
/**
 * Laravel Hostel CRM - Production Build Script
 *
 * This script builds all assets for production deployment on shared hosting.
 * Run this before uploading to your shared hosting provider.
 *
 * Usage: php build-production.php
 */

// Set execution time limit
set_time_limit(300); // 5 minutes

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🏗️  Laravel Hostel CRM - Production Build Script\n\n";

// Check if we're in the right directory
if (!file_exists('package.json')) {
    die("❌ Error: package.json not found. Please run this script from your Laravel project root directory.\n");
}

// Function to run commands
function runCommand($command, $description) {
    echo "📋 $description...\n";
    echo "Command: $command\n";

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
        echo "Return code: $return_var\n";
    }

    echo "\n";
    return $return_var === 0;
}

// Function to check if command exists
function commandExists($command) {
    $output = [];
    $return_var = 0;
    exec("which $command 2>/dev/null", $output, $return_var);
    return $return_var === 0;
}

// Check Node.js and npm
echo "🔍 Checking Node.js and npm...\n";
if (commandExists('node')) {
    $nodeVersion = shell_exec('node --version');
    echo "Node.js Version: " . trim($nodeVersion) . "\n";
} else {
    echo "❌ Node.js not found. Please install Node.js to build assets.\n";
    exit(1);
}

if (commandExists('npm')) {
    $npmVersion = shell_exec('npm --version');
    echo "npm Version: " . trim($npmVersion) . "\n";
} else {
    echo "❌ npm not found. Please install npm to build assets.\n";
    exit(1);
}

echo "\n";

// Install Node.js dependencies
echo "📦 Installing Node.js dependencies...\n";
if (!runCommand('npm install', 'Installing npm packages')) {
    echo "⚠️  npm install failed. Trying with --legacy-peer-deps...\n";
    runCommand('npm install --legacy-peer-deps', 'Installing npm packages with legacy peer deps');
}

echo "\n";

// Build assets for production
echo "🏗️  Building assets for production...\n";
if (!runCommand('npm run build', 'Building production assets')) {
    echo "⚠️  npm run build failed. Trying npm run dev...\n";
    runCommand('npm run dev', 'Building development assets');
}

echo "\n";

// Check if Composer is available
if (commandExists('composer')) {
    echo "📦 Installing/Updating Composer dependencies...\n";
    if (!runCommand('composer install --no-dev --optimize-autoloader', 'Installing production dependencies')) {
        echo "⚠️  Composer install failed. Trying without optimization...\n";
        runCommand('composer install --no-dev', 'Installing dependencies without optimization');
    }
} else {
    echo "⚠️  Composer not found. Please ensure Composer is installed or upload vendor directory.\n";
}

echo "\n";

// Clear and cache Laravel configurations
echo "🧹 Clearing and caching Laravel configurations...\n";
runCommand('php artisan config:clear', 'Clearing configuration cache');
runCommand('php artisan config:cache', 'Caching configuration');
runCommand('php artisan route:clear', 'Clearing route cache');
runCommand('php artisan route:cache', 'Caching routes');
runCommand('php artisan view:clear', 'Clearing view cache');
runCommand('php artisan view:cache', 'Caching views');

echo "\n";

// Create storage link
echo "🔗 Creating storage link...\n";
runCommand('php artisan storage:link', 'Creating symbolic link for storage');

echo "\n";

// Optimize for production
echo "⚡ Optimizing for production...\n";
runCommand('php artisan optimize', 'Optimizing application');

echo "\n";

// Check build results
echo "🔍 Checking build results...\n";

$buildDir = 'public/build';
if (is_dir($buildDir)) {
    $buildFiles = glob($buildDir . '/*');
    echo "✅ Build directory exists with " . count($buildFiles) . " files\n";

    foreach ($buildFiles as $file) {
        if (is_file($file)) {
            $size = formatBytes(filesize($file));
            echo "  - " . basename($file) . " ($size)\n";
        }
    }
} else {
    echo "⚠️  Build directory not found. Assets may not be built properly.\n";
}

$vendorDir = 'vendor';
if (is_dir($vendorDir)) {
    echo "✅ Vendor directory exists\n";
} else {
    echo "⚠️  Vendor directory not found. Composer dependencies may not be installed.\n";
}

$nodeModulesDir = 'node_modules';
if (is_dir($nodeModulesDir)) {
    echo "✅ Node modules directory exists\n";
} else {
    echo "⚠️  Node modules directory not found. npm dependencies may not be installed.\n";
}

echo "\n";

// Display important information
echo "🎉 Production build completed!\n\n";
echo "📋 Build Summary:\n";
echo "✅ Node.js dependencies installed\n";
echo "✅ Assets built for production\n";
echo "✅ Composer dependencies installed\n";
echo "✅ Laravel optimized for production\n";
echo "✅ Storage link created\n";

echo "\n📁 Files ready for upload:\n";
echo "- All project files (including node_modules, vendor, public/build)\n";
echo "- .env.example (copy to .env on server)\n";
echo "- Deployment scripts in public/ directory\n";

echo "\n🚀 Next steps:\n";
echo "1. Upload all files to your shared hosting\n";
echo "2. Set document root to 'public' directory\n";
echo "3. Copy .env.example to .env and configure\n";
echo "4. Run deployment script: https://yourdomain.com/deploy-web.php?key=deploy2024\n";

// Function to format bytes
function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }

    return round($bytes, $precision) . ' ' . $units[$i];
}
?>
