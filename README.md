# PageTurner - Advanced Book Management System

PageTurner is a high-performance, high-volume book management system built with Laravel. It is engineered for scalability, featuring database sharding, read/write splitting, query result streaming, and multi-tier caching.

## 💻 Hardware Specifications (Benchmarking Environment)
The following benchmarks were achieved on the following hardware configuration:
- **Device**: LAPTOP-QNUA32HG
- **Processor**: 12th Gen Intel(R) Core(TM) i5-1235U (1.30 GHz, 10 Cores)
- **RAM**: 8.00 GB DDR4
- **Storage**: 477 GB NVMe SSD
- **System Type**: 64-bit OS, x64-based processor

## 📊 Performance Benchmarks (Activity 6)
All performance targets were successfully met and validated through iterative testing.

| Metric | Target | Achieved | Status |
| :--- | :--- | :--- | :--- |
| **1M Record Seeding** | < 10 Minutes | **~420 Seconds** | ✅ PASS |
| **ISBN Lookup** | < 50 ms | **0.29 ms** | ✅ PASS |
| **Catalog Listing** | < 100 ms | **0.81 ms** | ✅ PASS |
| **Category Filter** | < 150 ms | **1.11 ms** | ✅ PASS |
| **Full-Text Search** | < 300 ms | **2.75 ms** | ✅ PASS |
| **Cached API Response** | < 10 ms | **3.50 ms** | ✅ PASS |
| **Concurrent Requests** | 50 Requests | **0 Errors** | ✅ PASS |

---

## 🚀 Getting Started

### 1. Installation
```bash
# Install dependencies
composer install
npm install
npm run build

# Setup environment
copy .env.example .env
php artisan key:generate
```

### 2. High-Performance Database Setup
The system uses MySQL Partitioning and specialized indexes for 1M+ record scalability.
```bash
# Run migrations with partitioning and optimized indexes
php artisan migrate:fresh

# Run the 1 Million Book Challenge Seeder
php artisan db:seed --class=MassBookSeeder
```

### 3. Performance Validation Commands
Use these commands to verify the system meets the required benchmarks:
```bash
# 1. Comprehensive Query Benchmarking
php artisan benchmark:books --iterations=100

# 2. Load & Cache Validation
php artisan test tests/Performance/BookCatalogLoadTest.php

# 3. Database Integrity & Seeding Performance
php artisan test tests/Feature/SeedingPerformanceTest.php

# 4. Cache Logic & Invalidation Verification
php artisan test tests/Feature/CacheValidationTest.php
```

---

## 🛠 Advanced Features

### ⚖️ Horizontal Scaling (Database Sharding)
Implemented modulo-based routing (`id % 4`) via the `Shardable` trait. This allows the application to distribute data across 4 database shards automatically.

### 🔄 Asynchronous Cache Warmup
To ensure high availability, the `WarmCategoryCache` job pre-loads popular categories into the cache. This is scheduled daily and runs automatically after mass seeding.

### 📡 Read/Write Splitting
Configured in `config/database.php` to offload heavy reporting queries to read replicas, ensuring the primary database remains responsive for write operations.

### 🛡 Security & Rate Limiting
- **Intelligent Tiered Throttling**: Uses Redis to enforce limits based on user roles (Guest, Standard, Premium, Admin).
- **Audit Logging**: Comprehensive monitoring of sensitive operations with SHA-256 integrity verification.

---

## 🧪 Testing Suite
```bash
# Run all scalability tests
php artisan test --testsuite=Feature
php artisan test --testsuite=Performance
```
