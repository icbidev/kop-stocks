# 📦 Stock Management System

A simple and effective stock management system designed to help businesses manage their **products**, **inventory**, and **stock levels** with ease.

This system allows users to:

- Track and manage product stock
- Monitor inventory levels
- Perform stock-in and stock-out operations
- Generate reports for product movement

---

## 🛠️ Technologies Used

- **V**ue.js 3 (Frontend)
- **I**nertia.js (SPA routing)
- **L**aravel 11 (Backend API)
- **T**ailwind CSS (Styling)
- Vite (Build tool)
- MySQL (Database)
- Git for version control

---

## 📁 Features

- ✅ Product creation and editing
- ✅ Inventory tracking
- ✅ Stock In/Stock Out recording
- ✅ Alerts for low stock levels
- ✅ User authentication (admin/staff roles)
- ✅ Activity logs and reports

---

## 🚀 Production Deployment Guide

> Assumes you're deploying to a Linux-based VPS with Apache/Nginx and MySQL installed.

### ✅ Step 1: Clone the repository

```bash
git clone git@github.com:yourusername/kop-stocks.git
cd kop-stocks
```

### ✅ Step 2: Install dependencies (VILT-specific)

```bash
composer install
npm install
npm run build
```

> Since you're using Vite (default in Laravel 11), `npm run build` will compile your Vue components and Tailwind CSS.

### ✅ Step 3: Set environment variables

Copy the `.env` file and configure your database and production settings:

```bash
cp .env.example .env
```

Edit `.env`:

```env
APP_NAME="Stock Management"
APP_ENV=production
APP_KEY=base64:...

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_user
DB_PASSWORD=your_password

VITE_APP_NAME="Stock Management"
```

### ✅ Step 4: Generate application key

```bash
php artisan key:generate
```

### ✅ Step 5: Run database migrations

```bash
php artisan migrate --seed
```

> `--seed` is optional if you have initial data.

### ✅ Step 6: Set permissions

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### ✅ Step 7: Set up your web server

#### For Nginx (example):

```nginx
server {
    listen 80;
    server_name yourdomain.com;

    root /var/www/kop-stocks/public;

    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.1-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

Restart Nginx:

```bash
sudo systemctl restart nginx
```

---

## 🔐 Optional: Use HTTPS (SSL)

Use [Let's Encrypt](https://letsencrypt.org/) with Certbot:

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com
```

---

## ✅ Final Steps

- Make sure `.env` has `APP_ENV=production` and `APP_DEBUG=false`
- Set up a queue or cron job if necessary:
  ```bash
  php artisan queue:work
  ```

---

## 👨‍💼 Contributing

1. Fork the repo
2. Create your branch (`git checkout -b feature-name`)
3. Commit your changes
4. Push to the branch
5. Open a Pull Request

---

## 📄 License

MIT License © 2025 ICBI DEV

