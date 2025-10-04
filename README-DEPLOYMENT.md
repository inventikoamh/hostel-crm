# Laravel Hostel CRM - Deployment Guide

This guide will help you deploy your Laravel Hostel CRM application to shared hosting.

## 📁 Deployment Files

The following utility scripts are included for easy deployment and maintenance:

### 🚀 Main Deployment Script
- **`deploy.php`** - Complete deployment automation script

### 🛠️ Maintenance Utilities
- **`clear-cache.php`** - Clear all Laravel caches
- **`run-migrations.php`** - Run database migrations with optional seeders
- **`optimize.php`** - Optimize Laravel for production
- **`maintenance.php`** - Manage maintenance mode
- **`backup-database.php`** - Create database backups

## 🚀 Quick Deployment Steps

### 1. Upload Your Code
Upload all your Laravel project files to your shared hosting account.

### 2. Set Document Root
Configure your domain to point to the `public` directory of your Laravel application.

### 3. Run Deployment Script
```bash
php deploy.php
```

This script will:
- ✅ Check PHP version and extensions
- ✅ Install Composer dependencies
- ✅ Set proper file permissions
- ✅ Create .env file from .env.example
- ✅ Generate application key
- ✅ Clear and cache configurations
- ✅ Run database migrations (optional)
- ✅ Create storage symbolic link
- ✅ Optimize for production

### 4. Configure Environment
Edit your `.env` file with your database credentials and other settings:

```env
APP_NAME="Hostel CRM"
APP_ENV=production
APP_KEY=base64:your-generated-key
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

## 🛠️ Maintenance Commands

### Clear All Caches
```bash
php clear-cache.php
```

### Run Database Migrations
```bash
# Run pending migrations only
php run-migrations.php

# Run migrations with seeders
php run-migrations.php --seed

# Fresh migration (DANGEROUS - drops all data)
php run-migrations.php --fresh

# Fresh migration with seeders
php run-migrations.php --fresh-seed
```

### Optimize Application
```bash
php optimize.php
```

### Maintenance Mode
```bash
# Enable maintenance mode
php maintenance.php on

# Disable maintenance mode
php maintenance.php off

# Check maintenance mode status
php maintenance.php status
```

### Database Backup
```bash
# Create backup with default name
php backup-database.php

# Create backup with custom name
php backup-database.php my-backup-name
```

## 📋 Pre-Deployment Checklist

### Server Requirements
- [ ] PHP 8.1 or higher
- [ ] MySQL 5.7+ or MariaDB 10.2+
- [ ] Required PHP extensions: PDO, PDO_MySQL, MBstring, OpenSSL, Tokenizer, XML, Ctype, JSON, BCMath, Fileinfo
- [ ] Composer (optional, but recommended)
- [ ] MySQL client tools (for database backups)

### File Permissions
Ensure these directories are writable (755 or 775):
- [ ] `storage/`
- [ ] `storage/app/`
- [ ] `storage/framework/`
- [ ] `storage/framework/cache/`
- [ ] `storage/framework/sessions/`
- [ ] `storage/framework/views/`
- [ ] `storage/logs/`
- [ ] `bootstrap/cache/`

### Configuration
- [ ] `.env` file configured with production settings
- [ ] Database credentials are correct
- [ ] Mail settings configured
- [ ] `APP_DEBUG=false` for production
- [ ] `APP_URL` set to your domain

## 🔧 Troubleshooting

### Common Issues

#### 1. "artisan file not found"
- Make sure you're running the scripts from the Laravel project root directory
- Ensure all files were uploaded correctly

#### 2. "Database connection failed"
- Check your database credentials in `.env`
- Ensure your database server is running
- Verify database user has proper permissions

#### 3. "Permission denied" errors
- Set proper file permissions: `chmod -R 755 .` for files, `chmod -R 775 storage bootstrap/cache`
- Contact your hosting provider if you can't set permissions

#### 4. "Class not found" errors
- Run `composer install --no-dev --optimize-autoloader`
- Clear caches: `php clear-cache.php`

#### 5. "Storage link failed"
- Ensure `storage` directory is writable
- Run `php artisan storage:link` manually

### Log Files
Check these files for error details:
- `storage/logs/laravel.log` - Application logs
- Web server error logs (location varies by hosting provider)

## 🔒 Security Considerations

### Production Security
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Use strong database passwords
- [ ] Enable HTTPS/SSL
- [ ] Set secure file permissions
- [ ] Regular security updates
- [ ] Database backups

### File Permissions
```bash
# Secure file permissions
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod -R 775 storage bootstrap/cache
```

## 📞 Support

If you encounter issues during deployment:

1. Check the Laravel logs in `storage/logs/laravel.log`
2. Verify all requirements are met
3. Test database connectivity
4. Check file permissions
5. Contact your hosting provider for server-specific issues

## 🎉 Post-Deployment

After successful deployment:

1. **Test the application** - Visit your domain and test all functionality
2. **Create admin user** - Use the seeder or create manually
3. **Configure mail settings** - Test email functionality
4. **Set up backups** - Schedule regular database backups
5. **Monitor logs** - Check for any errors or issues
6. **Performance optimization** - Consider using a CDN for static assets

Your Laravel Hostel CRM should now be live and accessible! 🚀
