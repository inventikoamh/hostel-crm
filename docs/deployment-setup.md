# Deployment & Setup Guide

## Overview

This guide provides comprehensive instructions for setting up, configuring, and deploying the Hostel CRM system. It covers development setup, production deployment, and maintenance procedures.

## Table of Contents

1. [System Requirements](#system-requirements)
2. [Development Setup](#development-setup)
3. [Production Deployment](#production-deployment)
4. [Configuration](#configuration)
5. [Database Setup](#database-setup)
6. [Security Configuration](#security-configuration)
7. [Performance Optimization](#performance-optimization)
8. [Monitoring & Maintenance](#monitoring--maintenance)
9. [Troubleshooting](#troubleshooting)
10. [Backup & Recovery](#backup--recovery)

## System Requirements

### Minimum Requirements
- **PHP**: 8.3 or higher
- **Composer**: 2.0 or higher
- **Node.js**: 18.0 or higher
- **NPM**: 8.0 or higher
- **Database**: MySQL 8.0, PostgreSQL 13, or SQLite 3
- **Web Server**: Apache 2.4 or Nginx 1.18
- **Memory**: 512MB RAM minimum
- **Storage**: 1GB free space minimum

### Recommended Requirements
- **PHP**: 8.3 with OPcache enabled
- **Composer**: Latest version
- **Node.js**: 20.0 LTS
- **NPM**: Latest version
- **Database**: MySQL 8.0 with InnoDB
- **Web Server**: Nginx 1.20 with PHP-FPM
- **Memory**: 2GB RAM or higher
- **Storage**: 10GB free space
- **SSL Certificate**: Let's Encrypt or commercial certificate

### PHP Extensions
Required PHP extensions:
```bash
# Core extensions
php-mbstring
php-xml
php-curl
php-zip
php-gd
php-mysql
php-pdo
php-tokenizer
php-fileinfo
php-openssl
php-json
php-bcmath
php-ctype
php-dom
php-filter
php-hash
php-iconv
php-intl
php-pcre
php-reflection
php-session
php-simplexml
php-spl
php-standard
php-xmlreader
php-xmlwriter

# Optional but recommended
php-opcache
php-redis
php-memcached
php-imagick
```

## Development Setup

### 1. Clone Repository
```bash
# Clone the repository
git clone https://github.com/your-username/hostel-crm.git
cd hostel-crm

# Checkout to development branch
git checkout develop
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Database Setup
```bash
# Create database (MySQL example)
mysql -u root -p
CREATE DATABASE hostel_crm;
CREATE USER 'hostel_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON hostel_crm.* TO 'hostel_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Run migrations
php artisan migrate

# Seed database with sample data (includes hostels and users)
php artisan db:seed

# Or seed specific modules
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=HostelSeeder
```

### 5. Build Assets
```bash
# Build development assets
npm run dev

# Or build production assets
npm run build
```

### 6. Start Development Server
```bash
# Start Laravel development server
php artisan serve

# In another terminal, start Vite dev server
npm run dev
```

### 7. Access Application
- **URL**: http://localhost:8000
- **Login**: admin@hostelcrm.com
- **Password**: password123

## Production Deployment

### 1. Server Preparation
```bash
# Update system packages
sudo apt update && sudo apt upgrade -y

# Install required packages
sudo apt install -y nginx mysql-server php8.3-fpm php8.3-mysql php8.3-xml php8.3-mbstring php8.3-curl php8.3-zip php8.3-gd php8.3-bcmath php8.3-intl php8.3-opcache

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js and NPM
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt-get install -y nodejs
```

### 2. Application Deployment
```bash
# Create application directory
sudo mkdir -p /var/www/hostel-crm
sudo chown -R www-data:www-data /var/www/hostel-crm

# Clone repository
cd /var/www/hostel-crm
sudo -u www-data git clone https://github.com/your-username/hostel-crm.git .

# Install dependencies
sudo -u www-data composer install --optimize-autoloader --no-dev
sudo -u www-data npm install
sudo -u www-data npm run build
```

### 3. Web Server Configuration

#### Nginx Configuration
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/hostel-crm/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

#### Apache Configuration
```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /var/www/hostel-crm/public

    <Directory /var/www/hostel-crm/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/hostel-crm_error.log
    CustomLog ${APACHE_LOG_DIR}/hostel-crm_access.log combined
</VirtualHost>
```

### 4. SSL Configuration
```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx

# Obtain SSL certificate
sudo certbot --nginx -d your-domain.com -d www.your-domain.com

# Auto-renewal
sudo crontab -e
# Add: 0 12 * * * /usr/bin/certbot renew --quiet
```

### 5. Database Configuration
```bash
# Secure MySQL installation
sudo mysql_secure_installation

# Create database and user
mysql -u root -p
CREATE DATABASE hostel_crm_production;
CREATE USER 'hostel_prod'@'localhost' IDENTIFIED BY 'very_secure_password';
GRANT ALL PRIVILEGES ON hostel_crm_production.* TO 'hostel_prod'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 6. Application Configuration
```bash
# Set proper permissions
sudo chown -R www-data:www-data /var/www/hostel-crm
sudo chmod -R 755 /var/www/hostel-crm
sudo chmod -R 775 /var/www/hostel-crm/storage
sudo chmod -R 775 /var/www/hostel-crm/bootstrap/cache

# Configure environment
sudo -u www-data cp .env.example .env
sudo -u www-data php artisan key:generate
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
```

## Configuration

### Environment Variables
```env
# Application
APP_NAME="Hostel CRM"
APP_ENV=production
APP_KEY=base64:your-generated-key
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hostel_crm_production
DB_USERNAME=hostel_prod
DB_PASSWORD=very_secure_password

# Cache
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="${APP_NAME}"

# Logging
LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error
```

### PHP Configuration
```ini
; /etc/php/8.3/fpm/php.ini
memory_limit = 256M
upload_max_filesize = 20M
post_max_size = 20M
max_execution_time = 300
max_input_vars = 3000

; OPcache settings
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
opcache.revalidate_freq=2
opcache.fast_shutdown=1
```

### Nginx PHP-FPM Configuration
```ini
; /etc/php/8.3/fpm/pool.d/www.conf
pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests = 500
```

## Database Setup

### Migration Commands
```bash
# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Reset database
php artisan migrate:reset

# Fresh migration with seeding
php artisan migrate:fresh --seed
```

### Seeding Commands
```bash
# Run all seeders (includes UserSeeder and HostelSeeder)
php artisan db:seed

# Run specific seeders
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=HostelSeeder

# Fresh seed (reset database and run all seeders)
php artisan migrate:fresh --seed
```

### Available Seeders
- **UserSeeder**: Creates admin user for login
- **HostelSeeder**: Creates 6 demo hostels with realistic data

### Database Optimization
```sql
-- Optimize tables
OPTIMIZE TABLE users, hostels, tenants;

-- Analyze tables
ANALYZE TABLE users, hostels, tenants;

-- Check table status
SHOW TABLE STATUS;
```

## Security Configuration

### File Permissions
```bash
# Set proper permissions
sudo chown -R www-data:www-data /var/www/hostel-crm
sudo chmod -R 755 /var/www/hostel-crm
sudo chmod -R 775 /var/www/hostel-crm/storage
sudo chmod -R 775 /var/www/hostel-crm/bootstrap/cache

# Secure sensitive files
sudo chmod 600 /var/www/hostel-crm/.env
sudo chmod 600 /var/www/hostel-crm/storage/logs/*.log
```

### Firewall Configuration
```bash
# Configure UFW firewall
sudo ufw enable
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw deny 3306/tcp
sudo ufw deny 6379/tcp
```

### SSL/TLS Configuration
```nginx
# Strong SSL configuration
ssl_protocols TLSv1.2 TLSv1.3;
ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384;
ssl_prefer_server_ciphers off;
ssl_session_cache shared:SSL:10m;
ssl_session_timeout 10m;
ssl_stapling on;
ssl_stapling_verify on;
```

## Performance Optimization

### Laravel Optimization
```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev
```

### Database Optimization
```sql
-- Add indexes for better performance
CREATE INDEX idx_hostels_status ON hostels(status);
CREATE INDEX idx_tenants_status ON tenants(status);
CREATE INDEX idx_tenants_hostel_id ON tenants(hostel_id);
CREATE INDEX idx_users_email ON users(email);
```

### Redis Configuration
```bash
# Install Redis
sudo apt install redis-server

# Configure Redis
sudo nano /etc/redis/redis.conf

# Restart Redis
sudo systemctl restart redis-server
sudo systemctl enable redis-server
```

### OPcache Configuration
```ini
; OPcache settings for production
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.revalidate_freq=0
opcache.validate_timestamps=0
opcache.save_comments=0
opcache.fast_shutdown=1
```

## Monitoring & Maintenance

### Log Monitoring
```bash
# Laravel logs
tail -f /var/www/hostel-crm/storage/logs/laravel.log

# Nginx logs
tail -f /var/log/nginx/access.log
tail -f /var/log/nginx/error.log

# PHP-FPM logs
tail -f /var/log/php8.3-fpm.log
```

### System Monitoring
```bash
# Install monitoring tools
sudo apt install htop iotop nethogs

# Monitor system resources
htop
iotop
nethogs

# Check disk usage
df -h
du -sh /var/www/hostel-crm/*
```

### Automated Tasks
```bash
# Create cron jobs
sudo crontab -e

# Add Laravel scheduler
* * * * * cd /var/www/hostel-crm && php artisan schedule:run >> /dev/null 2>&1

# Add log rotation
0 0 * * * /usr/sbin/logrotate /etc/logrotate.d/laravel

# Add backup tasks
0 2 * * * /var/www/hostel-crm/scripts/backup.sh
```

### Health Checks
```bash
# Create health check script
#!/bin/bash
# /var/www/hostel-crm/scripts/health-check.sh

# Check if application is responding
curl -f http://localhost/health || exit 1

# Check database connection
php artisan tinker --execute="DB::connection()->getPdo();" || exit 1

# Check disk space
df / | awk 'NR==2 {if($5 > 90) exit 1}'

# Check memory usage
free | awk 'NR==2 {if($3/$2 > 0.9) exit 1}'
```

## Troubleshooting

### Common Issues

#### Application Not Loading
1. Check web server configuration
2. Verify PHP-FPM is running
3. Check file permissions
4. Verify .env configuration
5. Check Laravel logs

#### Database Connection Issues
1. Verify database credentials
2. Check database server status
3. Verify network connectivity
4. Check database permissions
5. Review database logs

#### Performance Issues
1. Check server resources
2. Review application logs
3. Optimize database queries
4. Enable OPcache
5. Configure Redis caching

#### SSL Certificate Issues
1. Verify certificate validity
2. Check certificate chain
3. Verify domain configuration
4. Check SSL configuration
5. Test SSL rating

### Debug Commands
```bash
# Check Laravel configuration
php artisan config:show

# Check routes
php artisan route:list

# Check database connection
php artisan tinker
>>> DB::connection()->getPdo();

# Check cache status
php artisan cache:table
php artisan cache:clear

# Check queue status
php artisan queue:work --once
```

### Log Analysis
```bash
# Search for errors
grep -i error /var/www/hostel-crm/storage/logs/laravel.log

# Search for specific issues
grep -i "database" /var/www/hostel-crm/storage/logs/laravel.log

# Monitor real-time logs
tail -f /var/www/hostel-crm/storage/logs/laravel.log | grep -i error
```

## Backup & Recovery

### Database Backup
```bash
# Create backup script
#!/bin/bash
# /var/www/hostel-crm/scripts/backup-db.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/hostel-crm"
DB_NAME="hostel_crm_production"
DB_USER="hostel_prod"
DB_PASS="very_secure_password"

mkdir -p $BACKUP_DIR

mysqldump -u$DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/db_backup_$DATE.sql

# Compress backup
gzip $BACKUP_DIR/db_backup_$DATE.sql

# Remove old backups (keep 30 days)
find $BACKUP_DIR -name "db_backup_*.sql.gz" -mtime +30 -delete
```

### File Backup
```bash
# Create file backup script
#!/bin/bash
# /var/www/hostel-crm/scripts/backup-files.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/hostel-crm"
APP_DIR="/var/www/hostel-crm"

mkdir -p $BACKUP_DIR

# Backup application files (excluding vendor and node_modules)
tar -czf $BACKUP_DIR/files_backup_$DATE.tar.gz \
    --exclude='vendor' \
    --exclude='node_modules' \
    --exclude='.git' \
    --exclude='storage/logs' \
    -C $APP_DIR .

# Remove old backups (keep 7 days)
find $BACKUP_DIR -name "files_backup_*.tar.gz" -mtime +7 -delete
```

### Recovery Procedures
```bash
# Database recovery
gunzip /var/backups/hostel-crm/db_backup_20240120_120000.sql.gz
mysql -u$DB_USER -p$DB_PASS $DB_NAME < /var/backups/hostel-crm/db_backup_20240120_120000.sql

# File recovery
tar -xzf /var/backups/hostel-crm/files_backup_20240120_120000.tar.gz -C /var/www/hostel-crm/

# Restart services
sudo systemctl restart nginx
sudo systemctl restart php8.3-fpm
```

### Automated Backup
```bash
# Add to crontab
sudo crontab -e

# Daily database backup at 2 AM
0 2 * * * /var/www/hostel-crm/scripts/backup-db.sh

# Weekly file backup on Sunday at 3 AM
0 3 * * 0 /var/www/hostel-crm/scripts/backup-files.sh
```

## Maintenance Schedule

### Daily Tasks
- Monitor application logs
- Check system resources
- Verify backup completion
- Review error logs

### Weekly Tasks
- Update system packages
- Clean old log files
- Optimize database tables
- Review security logs

### Monthly Tasks
- Update application dependencies
- Review and rotate SSL certificates
- Performance analysis
- Security audit

### Quarterly Tasks
- Full system backup
- Disaster recovery testing
- Performance optimization review
- Security updates

## Support & Resources

### Documentation
- [Laravel Documentation](https://laravel.com/docs)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Nginx Documentation](https://nginx.org/en/docs/)
- [MySQL Documentation](https://dev.mysql.com/doc/)

### Community Support
- [Laravel Community](https://laracasts.com/)
- [Stack Overflow](https://stackoverflow.com/questions/tagged/laravel)
- [GitHub Issues](https://github.com/your-username/hostel-crm/issues)

### Professional Support
- Laravel Consulting Services
- System Administration Services
- Custom Development Services
