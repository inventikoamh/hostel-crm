<?php
/**
 * Laravel Hostel CRM - Web Deployment Interface
 *
 * This provides a web-based interface for deployment and maintenance tasks.
 * Access this file through your browser: https://yourdomain.com/deploy-web.php
 *
 * SECURITY WARNING: Remove this file after deployment is complete!
 */

// Security check - only allow access from specific IPs or with a secret key
$allowedIPs = [
    '127.0.0.1',
    '::1',
    // Add your IP addresses here for security
];

$secretKey = 'deploy2024'; // Change this to a secure random string
$requireSecret = true; // Set to false if you want to allow all IPs

// Check IP access
if (!in_array($_SERVER['REMOTE_ADDR'], $allowedIPs) && $requireSecret) {
    if (!isset($_GET['key']) || $_GET['key'] !== $secretKey) {
        http_response_code(403);
        die('Access denied. Use: ' . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . '?key=' . $secretKey);
    }
}

// Set execution time limit
set_time_limit(300);

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Hostel CRM - Deployment Interface</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 2.5em;
            font-weight: 300;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.8;
        }
        .content {
            padding: 30px;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        .warning strong {
            color: #d63031;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .card {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .card h3 {
            margin: 0 0 15px 0;
            color: #2c3e50;
            font-size: 1.3em;
        }
        .card p {
            margin: 0 0 15px 0;
            color: #6c757d;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.2s;
            margin-right: 10px;
            margin-bottom: 10px;
        }
        .btn:hover {
            background: #0056b3;
        }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #1e7e34; }
        .btn-warning { background: #ffc107; color: #212529; }
        .btn-warning:hover { background: #e0a800; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .btn-info { background: #17a2b8; }
        .btn-info:hover { background: #138496; }
        .output {
            background: #2c3e50;
            color: #ecf0f1;
            padding: 20px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            white-space: pre-wrap;
            max-height: 400px;
            overflow-y: auto;
            margin-top: 20px;
            display: none;
        }
        .status {
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            font-weight: 500;
        }
        .status.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .status.info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            border-top: 1px solid #e9ecef;
        }
        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 2s linear infinite;
            margin: 0 auto 10px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 Laravel Hostel CRM</h1>
            <p>Deployment & Maintenance Interface</p>
        </div>

        <div class="content">
            <div class="warning">
                <strong>⚠️ Security Warning:</strong> This interface provides access to sensitive operations.
                Remove this file after deployment is complete for security reasons.
            </div>

            <div class="grid">
                <!-- Main Deployment -->
                <div class="card">
                    <h3>🚀 Complete Deployment</h3>
                    <p>Run the full deployment process including dependencies, configuration, and database setup.</p>
                    <button class="btn btn-success" onclick="runScript('deploy.php')">Deploy Application</button>
                </div>

                <!-- Cache Management -->
                <div class="card">
                    <h3>🧹 Cache Management</h3>
                    <p>Clear all Laravel caches including application, config, routes, views, and events.</p>
                    <button class="btn btn-warning" onclick="runScript('clear-cache.php')">Clear All Caches</button>
                </div>

                <!-- Database Migrations -->
                <div class="card">
                    <h3>🗄️ Database Migrations</h3>
                    <p>Run database migrations with optional seeders. Choose your migration type.</p>
                    <button class="btn btn-info" onclick="runScript('run-migrations.php')">Run Migrations</button>
                    <button class="btn btn-info" onclick="runScript('run-migrations.php?seed=1')">Run with Seeders</button>
                </div>

                <!-- Optimization -->
                <div class="card">
                    <h3>⚡ Performance Optimization</h3>
                    <p>Optimize your Laravel application for production with caching and optimization.</p>
                    <button class="btn btn-success" onclick="runScript('optimize.php')">Optimize Application</button>
                </div>

                <!-- Maintenance Mode -->
                <div class="card">
                    <h3>🔧 Maintenance Mode</h3>
                    <p>Enable or disable maintenance mode for your application.</p>
                    <button class="btn btn-warning" onclick="runScript('maintenance.php?action=on')">Enable Maintenance</button>
                    <button class="btn btn-success" onclick="runScript('maintenance.php?action=off')">Disable Maintenance</button>
                    <button class="btn btn-info" onclick="runScript('maintenance.php?action=status')">Check Status</button>
                </div>

                <!-- Database Backup -->
                <div class="card">
                    <h3>💾 Database Backup</h3>
                    <p>Create a backup of your database with automatic compression.</p>
                    <button class="btn btn-info" onclick="runScript('backup-database.php')">Create Backup</button>
                </div>
            </div>

            <div class="loading" id="loading">
                <div class="spinner"></div>
                <p>Processing your request...</p>
            </div>

            <div class="output" id="output"></div>
        </div>

        <div class="footer">
            <p><strong>Laravel Hostel CRM</strong> - Deployment Interface</p>
            <p>Remember to remove this file after deployment for security!</p>
        </div>
    </div>

    <script>
        function runScript(script) {
            const output = document.getElementById('output');
            const loading = document.getElementById('loading');

            // Show loading
            loading.style.display = 'block';
            output.style.display = 'none';
            output.textContent = '';

            // Create iframe for script execution
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.src = script;
            document.body.appendChild(iframe);

            // Simulate loading time and show output
            setTimeout(() => {
                loading.style.display = 'none';
                output.style.display = 'block';
                output.textContent = 'Script executed successfully!\n\nNote: For detailed output, run the script directly via command line or check the server logs.';
                document.body.removeChild(iframe);
            }, 2000);
        }

        // Check if we're in maintenance mode
        window.onload = function() {
            fetch('maintenance.php?action=status')
                .then(response => response.text())
                .then(data => {
                    if (data.includes('ENABLED')) {
                        document.body.insertAdjacentHTML('afterbegin',
                            '<div class="status error">⚠️ Maintenance mode is currently ENABLED</div>'
                        );
                    }
                })
                .catch(() => {
                    // Ignore errors
                });
        };
    </script>
</body>
</html>
