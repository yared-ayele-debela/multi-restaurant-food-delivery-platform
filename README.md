# Multi-Restaurant Food Delivery Platform

A production-ready, full-featured multi-restaurant food delivery web application with intelligent features including AI chatbot ordering, real-time tracking, loyalty gamification, and advanced analytics.

## 🏗️ System Architecture

### Backend Stack
- **Framework**: Laravel 11
- **Database**: MySQL 8.0+
- **Cache/Queue**: Redis
- **Authentication**: Laravel Sanctum (JWT tokens)
- **WebSockets**: Laravel Reverb
- **Payments**: Stripe Integration
- **Architecture**: Repository + Service Pattern (Clean Architecture)
- **API Versioning**: `api/v1/*`
- **Task Scheduling**: Laravel Scheduler (Cron)
- **Queue Workers**: Laravel Horizon + Redis

### Frontend Stack
- **Framework**: React 18+ (Vite)
- **Styling**: TailwindCSS 3
- **State Management**: Redux Toolkit
- **HTTP Client**: Axios (with interceptors)
- **Routing**: React Router v6
- **Real-Time**: Socket.io (via Laravel Reverb)
- **Maps**: Leaflet (OpenStreetMap) + Heatmap plugin
- **Charts**: Recharts (analytics dashboards)

---

## 👥 User Roles

| Role | Description |
|------|-------------|
| **Admin** | Platform super administrator — manages users, restaurants, drivers, commissions, analytics |
| **Restaurant Owner** | Manages restaurant(s), branches, menus, orders, staff, and earnings |
| **Restaurant Staff** | Handles order processing with owner-assigned permissions |
| **Customer** | Browses restaurants, places orders, tracks deliveries, earns loyalty points |
| **Delivery Driver** | Accepts deliveries, updates live location, manages availability and earnings |

---

## 📦 Modules Overview

| # | Module | Description |
|---|--------|-------------|
| 1 | **User Management** | RBAC, profiles, addresses, activity logs |
| 2 | **Restaurant Management** | CRUD, approval workflow, commission, hours, images |
| 3 | **Multi-Branch Management** | Branch CRUD, delivery radius, nearest branch assignment |
| 4 | **Menu Management** | Categories, products, sizes, add-ons, stock |
| 5 | **Order Management** | Cart, checkout, status flow, commission/tax calculation |
| 6 | **Delivery Management** | Driver approval, auto-assignment (Haversine), live tracking |
| 7 | **Wallet System** | Ledger-based wallets, commission deduction, withdrawal requests |
| 8 | **Coupon Engine** | Multi-type coupons, usage tracking, validation rules |
| 9 | **Loyalty & Gamification** | Points, levels (Bronze→Platinum), cashback, rewards |
| 10 | **Recommendation Engine** | Personalized scoring (frequency, cuisine, distance, rating) |
| 11 | **Performance Scoring** | Daily restaurant scoring — delivery time, cancellations, ratings |
| 12 | **Scheduled Orders** | Future orders, queued jobs, pre-notification |
| 13 | **AI Chatbot** | Natural language ordering via modular AI service |
| 14 | **Real-Time Heat Map** | Order density, peak zones, driver density on Leaflet |

---

## 📁 Project Structure

```
food-delivery/
├── backend/                          # Laravel 11 API
│   ├── app/
│   │   ├── Enums/                    # Status enums, role enums
│   │   ├── Events/                   # Real-time events
│   │   ├── Exceptions/               # Custom exceptions
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   └── Api/
│   │   │   │       └── V1/           # Versioned API controllers
│   │   │   │           ├── AuthController.php
│   │   │   │           ├── UserController.php
│   │   │   │           ├── RestaurantController.php
│   │   │   │           ├── BranchController.php
│   │   │   │           ├── CategoryController.php
│   │   │   │           ├── ProductController.php
│   │   │   │           ├── OrderController.php
│   │   │   │           ├── DeliveryController.php
│   │   │   │           ├── DriverController.php
│   │   │   │           ├── WalletController.php
│   │   │   │           ├── CouponController.php
│   │   │   │           ├── LoyaltyController.php
│   │   │   │           ├── RecommendationController.php
│   │   │   │           ├── PerformanceController.php
│   │   │   │           ├── ScheduledOrderController.php
│   │   │   │           ├── ChatbotController.php
│   │   │   │           └── HeatMapController.php
│   │   │   ├── Middleware/
│   │   │   │   ├── RoleMiddleware.php
│   │   │   │   ├── RestaurantOwnerMiddleware.php
│   │   │   │   └── DriverMiddleware.php
│   │   │   ├── Requests/             # Form Request Validation
│   │   │   └── Resources/            # API Resources
│   │   ├── Jobs/                     # Queue jobs
│   │   │   ├── ProcessScheduledOrder.php
│   │   │   ├── CalculatePerformanceScore.php
│   │   │   ├── AssignDriverJob.php
│   │   │   └── ProcessLoyaltyPoints.php
│   │   ├── Listeners/                # Event listeners
│   │   ├── Models/                   # Eloquent models
│   │   ├── Notifications/            # Notification classes
│   │   ├── Observers/                # Model observers
│   │   ├── Policies/                 # Authorization policies
│   │   ├── Providers/
│   │   │   └── RepositoryServiceProvider.php
│   │   ├── Repositories/
│   │   │   ├── Contracts/            # Repository interfaces
│   │   │   └── Eloquent/             # Repository implementations
│   │   └── Services/                 # Business logic services
│   │       ├── UserService.php
│   │       ├── RestaurantService.php
│   │       ├── BranchService.php
│   │       ├── ProductService.php
│   │       ├── OrderService.php
│   │       ├── DeliveryService.php
│   │       ├── DriverAssignmentService.php
│   │       ├── DistanceCalculationService.php
│   │       ├── EarningsCalculationService.php
│   │       ├── WalletService.php
│   │       ├── CouponValidationService.php
│   │       ├── CouponApplyService.php
│   │       ├── LoyaltyService.php
│   │       ├── RecommendationService.php
│   │       ├── PerformanceScoreService.php
│   │       ├── ScheduledOrderService.php
│   │       ├── ChatbotService.php
│   │       ├── HeatMapService.php
│   │       └── StripePaymentService.php
│   ├── config/
│   ├── database/
│   │   ├── factories/
│   │   ├── migrations/
│   │   └── seeders/
│   ├── routes/
│   │   ├── api.php                   # Main API routes (versioned)
│   │   ├── channels.php              # WebSocket channels
│   │   └── console.php               # Scheduled commands
│   ├── .env.example
│   ├── composer.json
│   └── phpunit.xml
│
├── frontend/                         # React + Vite
│   ├── public/
│   ├── src/
│   │   ├── api/                      # Axios instances & API calls
│   │   │   ├── axiosClient.js
│   │   │   ├── authApi.js
│   │   │   ├── restaurantApi.js
│   │   │   ├── orderApi.js
│   │   │   ├── driverApi.js
│   │   │   └── walletApi.js
│   │   ├── app/
│   │   │   └── store.js              # Redux store configuration
│   │   ├── components/
│   │   │   ├── common/               # Reusable UI components
│   │   │   ├── forms/                # Form components
│   │   │   ├── layout/               # Header, Sidebar, Footer
│   │   │   ├── maps/                 # Leaflet map components
│   │   │   └── charts/               # Recharts components
│   │   ├── features/                 # Redux slices by feature
│   │   │   ├── auth/
│   │   │   ├── restaurants/
│   │   │   ├── orders/
│   │   │   ├── delivery/
│   │   │   ├── wallet/
│   │   │   ├── coupons/
│   │   │   ├── loyalty/
│   │   │   └── chatbot/
│   │   ├── hooks/                    # Custom React hooks
│   │   ├── pages/
│   │   │   ├── admin/                # Admin dashboard pages
│   │   │   ├── restaurant/           # Restaurant owner pages
│   │   │   ├── customer/             # Customer-facing pages
│   │   │   ├── driver/               # Driver pages
│   │   │   └── auth/                 # Login, Register
│   │   ├── routes/                   # Route definitions & guards
│   │   ├── services/                 # WebSocket, geolocation
│   │   ├── utils/                    # Helpers, constants
│   │   ├── App.jsx
│   │   ├── main.jsx
│   │   └── index.css
│   ├── .env.example
│   ├── package.json
│   ├── tailwind.config.js
│   ├── postcss.config.js
│   └── vite.config.js
│
├── docker/
│   ├── nginx/
│   │   └── default.conf
│   ├── php/
│   │   └── Dockerfile
│   └── node/
│       └── Dockerfile
├── docker-compose.yml
│
├── docs/
│   ├── ARCHITECTURE.md
│   ├── DATABASE_SCHEMA.md
│   ├── IMPLEMENTATION_GUIDE.md
│   ├── IMPLEMENTATION_GUIDE_PART2.md
│   ├── API_DOCUMENTATION.md
│   └── FRONTEND_GUIDE.md
│
└── README.md
```

---

## 🚀 Quick Start

### Prerequisites
- PHP 8.2+
- Composer 2.x
- Node.js 18+
- MySQL 8.0+
- Redis 7+
- Docker & Docker Compose (optional)

### Backend Setup

```bash
# Create Laravel project
composer create-project laravel/laravel backend

cd backend

# Install required packages
composer require laravel/sanctum
composer require laravel/reverb
composer require stripe/stripe-php
composer require intervention/image
composer require predis/predis

# Configure environment
cp .env.example .env
php artisan key:generate

# Update .env with your credentials
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=food_delivery
DB_USERNAME=root
DB_PASSWORD=your_password

REDIS_HOST=127.0.0.1
REDIS_PORT=6379

STRIPE_KEY=pk_test_xxx
STRIPE_SECRET=sk_test_xxx
STRIPE_WEBHOOK_SECRET=whsec_xxx

REVERB_APP_ID=your_app_id
REVERB_APP_KEY=your_app_key
REVERB_APP_SECRET=your_app_secret

# Install Sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# Install Reverb
php artisan install:broadcasting
php artisan reverb:install

# Run migrations and seeders
php artisan migrate --seed

# Start the server
php artisan serve

# Start WebSocket server (separate terminal)
php artisan reverb:start

# Start queue worker (separate terminal)
php artisan queue:work redis

# Start scheduler (separate terminal)
php artisan schedule:work
```

### Frontend Setup

```bash
# Create React app
npm create vite@latest frontend -- --template react

cd frontend

# Install dependencies
npm install
npm install axios react-router-dom
npm install @reduxjs/toolkit react-redux
npm install socket.io-client
npm install leaflet react-leaflet
npm install leaflet.heat
npm install recharts
npm install react-hot-toast
npm install @heroicons/react
npm install dayjs

# Install dev dependencies
npm install -D tailwindcss postcss autoprefixer
npx tailwindcss init -p

# Configure environment
cp .env.example .env
# VITE_API_URL=http://localhost:8000/api/v1
# VITE_WS_HOST=localhost
# VITE_WS_PORT=8080

# Start development server
npm run dev
```

### Docker Setup (Recommended for Production)

```bash
# Clone repository
git clone <repo-url> food-delivery
cd food-delivery

# Copy environment files
cp backend/.env.example backend/.env
cp frontend/.env.example frontend/.env

# Start all services
docker-compose up -d

# Install backend dependencies
docker-compose exec app composer install

# Generate key and run migrations
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate --seed

# Install frontend dependencies
docker-compose exec node npm install
docker-compose exec node npm run build
```

---

## 🔧 Environment Variables

### Backend (.env)

| Variable | Description | Example |
|----------|-------------|---------|
| `DB_DATABASE` | MySQL database name | `food_delivery` |
| `DB_USERNAME` | MySQL username | `root` |
| `DB_PASSWORD` | MySQL password | `secret` |
| `REDIS_HOST` | Redis host | `127.0.0.1` |
| `REDIS_PORT` | Redis port | `6379` |
| `STRIPE_KEY` | Stripe publishable key | `pk_test_...` |
| `STRIPE_SECRET` | Stripe secret key | `sk_test_...` |
| `STRIPE_WEBHOOK_SECRET` | Stripe webhook signing secret | `whsec_...` |
| `REVERB_APP_ID` | Reverb application ID | `12345` |
| `REVERB_APP_KEY` | Reverb key | `app-key` |
| `REVERB_APP_SECRET` | Reverb secret | `app-secret` |
| `AI_SERVICE_PROVIDER` | AI chatbot provider | `openai` |
| `AI_SERVICE_API_KEY` | AI provider API key | `sk-...` |
| `DELIVERY_FEE_BASE` | Base delivery fee | `2.50` |
| `DELIVERY_FEE_PER_KM` | Fee per kilometer | `0.50` |
| `DEFAULT_TAX_RATE` | Tax rate (percentage) | `10` |
| `LOYALTY_POINTS_PER_DOLLAR` | Loyalty points per dollar spent | `10` |

### Frontend (.env)

| Variable | Description | Example |
|----------|-------------|---------|
| `VITE_API_URL` | Backend API base URL | `http://localhost:8000/api/v1` |
| `VITE_WS_HOST` | WebSocket host | `localhost` |
| `VITE_WS_PORT` | WebSocket port | `8080` |
| `VITE_STRIPE_KEY` | Stripe publishable key | `pk_test_...` |
| `VITE_MAP_TILE_URL` | Map tile server URL | `https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png` |

---

## 🗄️ Database Overview

The application uses **35+ tables** organized across 14 modules:

### Core Tables
- `users`, `roles`, `role_user`, `permissions`, `permission_role`
- `user_addresses`, `activity_logs`

### Restaurant Tables
- `restaurants`, `restaurant_images`, `restaurant_hours`
- `restaurant_branches`

### Menu Tables
- `categories`, `products`, `product_sizes`, `product_addons`, `product_stock`

### Order Tables
- `orders`, `order_items`, `order_status_history`

### Delivery Tables
- `drivers`, `driver_locations`, `deliveries`

### Financial Tables
- `wallets`, `wallet_transactions`, `withdrawal_requests`

### Marketing Tables
- `coupons`, `coupon_usages`

### Loyalty Tables
- `loyalty_points`, `loyalty_transactions`, `loyalty_levels`

### Analytics Tables
- `restaurant_performance_scores`

See [DATABASE_SCHEMA.md](docs/DATABASE_SCHEMA.md) for complete schema documentation.

---

## 🔌 API Endpoints Summary

### Authentication
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/v1/auth/register` | Register new user |
| POST | `/api/v1/auth/login` | Login and get token |
| POST | `/api/v1/auth/logout` | Revoke token |
| GET | `/api/v1/auth/me` | Get authenticated user |

### Users (Admin)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/users` | List all users (filterable) |
| PUT | `/api/v1/users/{id}/role` | Assign role |
| PUT | `/api/v1/users/{id}/status` | Activate/suspend |
| POST | `/api/v1/users/{id}/reset-password` | Reset password |

### Restaurants
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/restaurants` | List restaurants |
| POST | `/api/v1/restaurants` | Create restaurant |
| GET | `/api/v1/restaurants/{id}` | Restaurant details |
| PUT | `/api/v1/restaurants/{id}` | Update restaurant |
| PUT | `/api/v1/restaurants/{id}/approve` | Admin approve |
| GET | `/api/v1/restaurants/recommended` | AI recommendations |

### Orders
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/v1/orders` | Place order |
| GET | `/api/v1/orders/{id}` | Order details |
| PUT | `/api/v1/orders/{id}/status` | Update status |
| POST | `/api/v1/orders/{id}/cancel` | Cancel order |

### Full API documentation: [API_DOCUMENTATION.md](docs/API_DOCUMENTATION.md)

---

## 🧪 Testing

### Backend Tests
```bash
cd backend

# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage
```

### Frontend Tests
```bash
cd frontend

# Run tests
npm run test

# Run with coverage
npm run test -- --coverage
```

---

## 📚 Documentation

| Document | Description |
|----------|-------------|
| [ARCHITECTURE.md](docs/ARCHITECTURE.md) | System architecture, patterns, and design decisions |
| [DATABASE_SCHEMA.md](docs/DATABASE_SCHEMA.md) | Complete database schema with all migrations |
| [IMPLEMENTATION_GUIDE.md](docs/IMPLEMENTATION_GUIDE.md) | Step-by-step backend implementation (Modules 1-7) |
| [IMPLEMENTATION_GUIDE_PART2.md](docs/IMPLEMENTATION_GUIDE_PART2.md) | Advanced features implementation (Modules 8-14) |
| [API_DOCUMENTATION.md](docs/API_DOCUMENTATION.md) | Full API reference with examples |
| [FRONTEND_GUIDE.md](docs/FRONTEND_GUIDE.md) | React architecture and component guide |

---

## 🚢 Deployment

### Production Checklist

- [ ] Set `APP_ENV=production` and `APP_DEBUG=false`
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`
- [ ] Run `php artisan optimize`
- [ ] Set up Redis for sessions, cache, and queues
- [ ] Configure Stripe webhook endpoint
- [ ] Set up SSL certificates
- [ ] Configure CORS properly
- [ ] Set up Laravel Horizon for queue monitoring
- [ ] Set up log rotation
- [ ] Configure rate limiting
- [ ] Run `npm run build` for frontend
- [ ] Set up CDN for static assets
- [ ] Configure database backups

### Docker Production

```bash
docker-compose -f docker-compose.prod.yml up -d
```

---

## 📄 License

This project is proprietary software. All rights reserved.
# multi--restaurant-food-delivery-platform-
