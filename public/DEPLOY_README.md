# Laravel Hostel CRM - Deployment Guide

## Overview

This project includes two deployment options for easy setup on shared hosting:

1. **Web Interface** (`deploy.php`) - Modern, user-friendly web interface
2. **Command Line** (`deploy-cli.php`) - Traditional command-line deployment

## Web Interface Deployment (Recommended)

### Access the Deploy Page
1. Upload your Laravel project files to your web server
2. Navigate to `http://yourdomain.com/deploy.php` in your browser
3. The page will automatically check system requirements

### Features
- ✅ **System Requirements Check** - Automatically verifies PHP version and extensions
- 🎨 **Modern UI** - Clean, responsive interface with real-time progress
- ⚙️ **Automated Deployment** - Handles all deployment steps automatically
- 📊 **Progress Tracking** - Visual progress bar and step-by-step status
- 🗄️ **Optional Migrations** - Choose whether to run database migrations
- 📱 **Mobile Friendly** - Works on all devices

### Deployment Steps
The web interface will automatically:
1. Install/update Composer dependencies
2. Set proper file permissions
3. Configure environment settings
4. Generate application key
5. Optimize caches and routes
6. Create storage links
7. Run database migrations (optional)
8. Final optimization

## Command Line Deployment

### Usage
```bash
php deploy-cli.php
```

### Features
- Interactive prompts for migrations and seeders
- Detailed console output
- Step-by-step progress display
- Error handling and reporting

## Pre-Deployment Checklist

Before running the deployment:

1. **Upload Files** - Ensure all Laravel project files are uploaded
2. **Database Setup** - Create your database and get credentials
3. **Environment File** - The deployment will create `.env` from `.env.example`
4. **File Permissions** - The deployment will set proper permissions automatically

## Post-Deployment Steps

After successful deployment:

1. **Configure .env** - Update database credentials and other settings
2. **Run Migrations** - If not done during deployment: `php artisan migrate`
3. **Run Seeders** - If needed: `php artisan db:seed`
4. **Web Server** - Ensure your domain points to the `public` directory
5. **Security** - Delete `deploy.php` and `deploy-cli.php` files

## Troubleshooting

### Common Issues

**Permission Errors**
- Ensure the web server has write access to `storage` and `bootstrap/cache`
- The deployment script will attempt to set permissions automatically

**Composer Not Found**
- Upload the `vendor` directory manually
- Or contact your hosting provider to install Composer

**Database Connection Issues**
- Verify database credentials in `.env`
- Ensure the database server is accessible
- Check if the database exists

**Route Caching Errors**
- This has been fixed in the latest version
- If you encounter issues, run: `php artisan route:clear`

### Useful Commands

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Run migrations
php artisan migrate

# Run seeders
php artisan db:seed

# Optimize application
php artisan optimize

# Check application status
php artisan about
```

## Security Notes

- **Delete deployment files** after successful deployment
- **Secure .env file** - Never commit it to version control
- **Set proper permissions** - 755 for directories, 644 for files
- **Use HTTPS** - Always use SSL certificates in production

## Support

If you encounter issues:
1. Check the `storage/logs/laravel.log` file
2. Verify all system requirements are met
3. Contact your hosting provider for server-specific issues
4. Review Laravel documentation for application-specific problems

---

**Laravel Hostel CRM** - Built with ❤️ for easy deployment
