# PageTurner - Advanced Book Management System

PageTurner is a high-volume book management system built with Laravel, featuring robust data portability, automated backups, comprehensive auditing, and tiered rate limiting.

## 🚀 Getting Started

Follow these steps to set up the project on your local machine.

### 1. Prerequisites
Ensure you have the following installed:
- PHP 8.2+
- Composer
- Node.js & NPM
- SQLite (or your preferred database)

### 2. Installation

#### Backend Setup
```bash
# Clone the repository
git clone <repository-url>
cd WebSoft_Activity_6

# Install PHP dependencies
composer install

# Environment configuration
copy .env.example .env
php artisan key:generate
```

#### Frontend Setup
```bash
# Install NPM dependencies
npm install

# Build assets
npm run build
# OR for development
npm run dev
```

### 3. Database Configuration & Seeding
```bash
# Run migrations
php artisan migrate

# Seed the database with test data (includes Admin, Users, Books, and Monitoring data)
php artisan db:seed
```

### 4. Running the Application
```bash
# Start the local development server
php artisan serve
```

---

## 🛠 Advanced Features & Management

### 📥 Data Portability (Import/Export)
The system supports bulk operations for books and users.
- **Queue Worker**: To process large imports (like the 10k records CSV) in the background:
  ```bash
  php artisan queue:work
  ```
- **Import Logs**: Detailed logs and error reports are available in the Admin dashboard.

### 🛡 Audit & Security
- **Tamper-Proof Logs**: All data changes are logged with SHA-256 checksums.
- **Rate Limiting**: Tiered limits are enforced per role.
  - Guest: 30 req/min
  - Standard: 60 req/min
  - Premium: 300 req/min
  - Admin: 1000 req/min

### 💾 Backup Management
Backups are automated but can be managed manually.
- **List Backups**:
  ```bash
  php artisan backup:list
  ```
- **Run Manual Backup**:
  ```bash
  php artisan backup:run
  ```
- **Schedule Monitoring**:
  ```bash
  php artisan schedule:list
  ```

### ⏰ Scheduled Tasks
To simulate the production scheduler locally:
```bash
php artisan schedule:work
```
Scheduled tasks include:
- **Daily Backups**: 02:00 AM
- **Weekly Full Backups**: Sundays 03:00 AM
- **Log Rotation**: Weekly
- **System Health Checks**: Daily 09:00 AM

---

## 🧪 Testing
Run the suite of feature tests to verify system integrity:
```bash
# Run all tests
php artisan test

# Specific Feature Tests
php artisan test tests/Feature/AuditComplianceTest.php
php artisan test tests/Feature/RateLimitingTest.php
php artisan test tests/Feature/DatabaseOptimizationTest.php
```

