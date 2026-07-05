# LendingSystem - Advanced Financial Management System

LendingSystem is a high-performance, high-volume financial management system built with Laravel. It is engineered for scalability, featuring database sharding, read/write splitting, query result streaming, and multi-tier caching.

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
| **Loan Lookup** | < 50 ms | **0.29 ms** | ✅ PASS |
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

### 2. Environment Configuration
Ensure your `.env` file contains the necessary AI provider keys for full functionality:
```env
# AI Provider Configuration
GEMINI_API_KEY=your_gemini_key
OPENAI_API_KEY=your_openai_key
HF_API_KEY=your_huggingface_key

# Local AI (Ollama) Configuration
OLLAMA_ENABLED=true
OLLAMA_BASE_URL=http://localhost:11434
OLLAMA_MODEL=llama3.2

# AI Fallback Settings
AI_DEFAULT_PROVIDER=gemini
AI_FALLBACK_ENABLED=true
```

### 3. High-Performance Database Setup
The system uses MySQL Partitioning and specialized indexes for 1M+ record scalability.
```bash
# Run migrations with partitioning and optimized indexes
php artisan migrate:fresh

# Run the 1 Million Loan Product Challenge Seeder
php artisan db:seed --class=MillionLoanProductSeeder
```

---

## 🛠 Advanced Features

### ⚖️ Horizontal Scaling (Database Sharding)
Implemented modulo-based routing (`id % 4`) via the `Shardable` trait. This allows the application to distribute data across 4 database shards automatically.

### 🔄 Asynchronous Cache Warmup
To ensure high availability, the `WarmCategoryCache` job pre-loads popular categories into the cache. This is scheduled daily and runs automatically after mass seeding.

### 📡 Read/Write Splitting
Configured in `config/database.php` to offload heavy reporting queries to read replicas, ensuring the primary database remains responsive for write operations.

### 🛡 Security & AI Intelligence
- **Multi-Provider Fallback**: Intelligent routing that automatically switches between Gemini (Primary), OpenAI, and Ollama (Local) to ensure 100% uptime for AI services.
- **Dedicated AI Queueing**: Heavy workloads (Fraud Detection, Content Generation) are offloaded to a specialized `ai-tasks` background queue to maintain UI responsiveness.
- **Cost & Usage Tracking**: Real-time monitoring of token consumption and estimated costs across all AI providers.
- **Automated Security Auditing**: Scheduled batch processing that scans loans for risk every 30 minutes via the Laravel Scheduler.
- **Intelligent Tiered Throttling**: Uses Redis to enforce limits based on user roles (Guest, Standard, Premium, Admin).
- **Audit Logging**: Comprehensive monitoring of sensitive operations with SHA-256 integrity verification.

### 📄 Professional Document Generation
- **PDF Export**: Integrated PDF generation for official loan invoices and transaction receipts using DomPDF. Features custom CSS-driven professional layouts.

---

## 📊 Monitoring & Administration

### 1. AI Usage Dashboard
Navigate to `/admin/ai-usage` to view real-time consumption statistics, provider breakdowns, and consumption history.

### 2. Queue Management
To process background AI tasks, use the specialized worker:
```bash
php artisan queue:work --queue=ai-tasks
```

### 3. Automated Scheduling
The system automates maintenance and audits. To run the scheduler locally:
```bash
php artisan schedule:work
```

---

## 🧪 Testing Suite
```bash
# Run all scalability tests
php artisan test --testsuite=Feature
```
