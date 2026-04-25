# trading-app-backend
Secure Laravel API backend for trading system with Sanctum authentication, role-based access control, and wallet-ready structure.

---

## 📌 Features

- User Registration & Login (API)
- Token Authentication (Laravel Sanctum)
- Role-Based Access Control (Admin / User / Trader)
- Protected API Routes
- Ready for Wallet & Trading System Integration

---

## 🧱 Tech Stack

- Laravel 13
- MySQL
- Laravel Sanctum
- REST API

---

## ⚙️ Installation

```bash
git clone https://github.com/your-username/trading-app-backend.git
cd trading-app
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
