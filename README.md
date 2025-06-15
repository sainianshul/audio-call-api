# Laravel API Starter with Swagger Documentation

Build a backend in Laravel that allows two registered users to initiate and receive audio calls via token-based signaling (for WebRTC/Agora-like flow).

---

##  Features

- Laravel 11 API-ready structure
- Integrated Swagger (OpenAPI) documentation
- Dummy user data with seeder
- Token-based authentication compatible with Sanctum
- RESTful user search endpoint

---

##  Installation Guide

Follow the steps below to get the project up and running:

### 1. Clone the Repository

```bash
git clone https://github.com/sainianshul/audio-call-api.git
cd audio-call-api
```

### 2. Install Dependencies

Make sure you have PHP, Composer, and a database (MySQL) setup.

```bash
composer install
```

### 3. Copy `.env` File and Generate App Key

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure Database

Update your `.env` file with your database credentials:

```dotenv
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Run Migrations

```bash
php artisan migrate
```

### 6. Seed Dummy Users

This will create 10 dummy users. All users have the password: `password`.

```bash
php artisan db:seed --class=UsersSeeder
```

### 7. Start the Development Server

```bash
php artisan serve
```

---

## 📘 API Documentation

Swagger UI is already integrated and ready to use.

### 🔗 Access the API Docs

Visit the following URL in your browser:

```
http://localhost:8000/api/documentation
```

If you're using a different port or domain, update this value in `.env`:

```dotenv
L5_SWAGGER_CONST_HOST=http://localhost:8000
```

---

##  Authentication

This project uses Laravel Sanctum for API token authentication. To authorize in Swagger UI:

1. Click the 🔒 "Authorize" button.
2. Enter your token in this format:

```
Bearer your-token-here
```

---

##  Directory Structure Highlights

- `app/Http/Controllers/UserController.php` – Contains user listing and search endpoints.
- `app/Http/Controllers/CallController.php` – Contains call actions



---

##  Sample API Endpoints

| Method | Endpoint              | Description            | Auth Required |
|--------|-----------------------|------------------------|----------------|
| GET    | `/api/users`          | Get all users          | yes            |
| GET    | `/api/users/search?q=`| Search users by name   | yes             |

---

## 🛠 Tech Stack

- Laravel 11
- PHP 8+
- Sanctum (for auth)
- L5-Swagger (for docs)
- MySQL / PostgreSQL

---

##  License

This project is open-source and available under the [MIT License](LICENSE).

---



##  Contact

For any inquiries or support, please reach out to:


- GitHub: [@sainianshul](https://github.com/sainianshul)
