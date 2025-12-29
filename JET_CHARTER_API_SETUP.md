# Jet Charter API - Quick Deployment Guide

## ✅ What's Been Set Up

- ✅ Sanctum authentication installed
- ✅ User model updated with phone, profile_picture, preferred_location
- ✅ API routes configured at `/api/v1/*`
- ✅ AuthController with register, login, update profile, logout
- ✅ Form validation requests
- ✅ Migrations ready

## 🚀 Deployment Steps (Forge)

### 1. **SSH into your Forge server:**

```bash
ssh forge@your-server-ip
cd /home/forge/your-site-name
```

### 2. **Pull latest code:**

```bash
git pull origin main
```

### 3. **Install dependencies:**

```bash
composer install --no-dev --optimize-autoloader
```

### 4. **Run migrations:**

```bash
php artisan migrate
```

### 5. **Create storage link:**

```bash
php artisan storage:link
```

### 6. **Set permissions:**

```bash
chmod -R 775 storage
chown -R forge:www-data storage
```

### 7. **Clear and cache config:**

```bash
php artisan config:clear
php artisan config:cache
php artisan route:cache
```

### 8. **Restart PHP-FPM:**

```bash
sudo service php8.2-fpm reload
```

## 📝 Environment Variables

No additional environment variables needed! The API uses your existing database.

## 🧪 Test the API

### Register a user:

```bash
curl -X POST https://your-domain.com/api/v1/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+1234567890",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

### Login:

```bash
curl -X POST https://your-domain.com/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

Save the `token` from the response, then:

### Get current user:

```bash
curl -X GET https://your-domain.com/api/v1/me \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Update profile:

```bash
curl -X PUT https://your-domain.com/api/v1/profile \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -F "name=John Updated" \
  -F "phone=+1987654321" \
  -F "preferred_location=New York" \
  -F "profile_picture=@/path/to/image.jpg"
```

## 📱 API Endpoints Summary

| Method | Endpoint | Auth Required | Description |
|--------|----------|---------------|-------------|
| POST | `/api/v1/register` | No | Register new user |
| POST | `/api/v1/login` | No | Login user |
| GET | `/api/v1/me` | Yes | Get current user |
| PUT | `/api/v1/profile` | Yes | Update profile |
| POST | `/api/v1/logout` | Yes | Logout user |

## 🔒 Security Notes

- Tokens never expire by default (configure in `config/sanctum.php` if needed)
- Passwords are hashed using bcrypt
- Profile images are stored in `storage/app/public/profile-pictures`
- Images are validated (jpeg, png, jpg, gif, max 5MB)

## 📄 Full Documentation

See `API_DOCUMENTATION.md` for complete API docs with mobile app examples.

