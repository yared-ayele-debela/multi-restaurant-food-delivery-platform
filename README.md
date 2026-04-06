# Multi-Restaurant Food Delivery Platform

A full-stack food delivery platform built with Laravel and React for multi-restaurant operations.  
It supports customer ordering, restaurant owner management, and admin operations including commission tracking and ledger export.

## Tech Stack

### Backend
- Laravel 12 (PHP 8.2+)
- MySQL / MariaDB
- Laravel Sanctum authentication
- Spatie Laravel Permission (RBAC)
- Laravel Reverb (real-time support)
- Queue + scheduler support

### Frontend
- React 19 + Vite
- Tailwind CSS 4
- React Router
- Leaflet maps

## Key Features

### Customer
- Browse restaurants, categories, and products
- Product detail and quick add-to-cart
- Cart, checkout, and order flow
- User profile and address management
- Location-driven restaurant discovery
- Distance badges (for restaurants with coordinates)

### Restaurant Owner Panel
- Manage categories, products, sizes, add-ons, stock
- Manage orders and order statuses
- Manage branches and operating hours
- Restaurant profile settings
  - Main location via map (Leaflet + Geoapify)
  - Restaurant image management (up to 5 images)
  - Branch image upload and location selection

### Admin Panel
- Restaurant moderation and status management
- Driver and withdrawal management
- Commission settlement tracking
- Platform wallet commission collection
- Commission ledger with:
  - date filters
  - restaurant filter
  - CSV export for filtered results

## Recent Enhancements

- Platform commission now credited into a dedicated platform wallet on completed orders.
- Admin dashboard shows commission and platform wallet indicators.
- Added dedicated Admin Commission Ledger page and CSV export.
- Added location picker modal on frontend (current location or map selection).
- Added map area filtering for restaurants on listing page.
- Added distance badges on restaurant cards.

## Project Structure

```text
food-delivery/
├── backend/        # Laravel application
│   ├── app/
│   ├── config/
│   ├── database/
│   ├── resources/
│   ├── routes/
│   └── ...
├── frontend/       # React + Vite application
│   ├── src/
│   └── ...
└── README.md
```

## Prerequisites

- PHP 8.2+
- Composer 2+
- Node.js 18+
- MySQL (or compatible)

## Local Setup

## 1) Backend Setup

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
```

Update `.env` for database and app URL. Then run:

```bash
php artisan migrate
php artisan db:seed
php artisan storage:link
php artisan serve
```

Optional (for async jobs/events):

```bash
php artisan queue:work
php artisan schedule:work
php artisan reverb:start
```

## 2) Frontend Setup

```bash
cd frontend
npm install
```

Create frontend env file (if not present) and set:

```env
VITE_API_URL=http://127.0.0.1:8000
VITE_GEOAPIFY_API_KEY=your_geoapify_key
```

Then run:

```bash
npm run dev
```

## Environment Notes

### Backend (`backend/.env`)
- Configure DB credentials (`DB_*`)
- Configure Sanctum stateful domains when needed
- Important custom settings:
  - `ORDER_TAX_RATE`
  - `WALLET_CURRENCY`
  - `LOYALTY_POINTS_PER_DOLLAR`
  - `LOYALTY_POINTS_PER_CURRENCY_UNIT`

### Frontend
- `VITE_API_URL` should point to backend origin (not including `/api/v1`)
- `VITE_GEOAPIFY_API_KEY` enables location search/reverse geocoding

## Main Route Groups

- Web admin: `/admin/*`
- Web restaurant owner: `/restaurant/*`
- Public API: `/api/v1/*`

Examples:
- `/api/v1/restaurants`
- `/api/v1/categories`
- `/api/v1/settings`
- `/admin/commissions`
- `/admin/commissions/export/csv`

## Scripts

### Backend
- `composer dev` - run app server, queue, logs, and vite concurrently (project script)
- `php artisan test` - run backend tests

### Frontend
- `npm run dev` - start Vite dev server
- `npm run build` - production build
- `npm run preview` - preview build

## Commission Settlement Logic

- Order checkout stores:
  - `commission_rate`
  - `commission_amount`
  - `restaurant_earnings`
- On order completion:
  - restaurant wallet is credited with `restaurant_earnings`
  - platform wallet is credited with `commission_amount`
  - admin can view all platform commission transactions in commission ledger

## Security Notes

- Do not commit `.env` files.
- Use strong app keys and production-safe configs.
- Ensure file permissions and storage symlink are set correctly.

## Deployment Checklist (Quick)

- Set `APP_ENV=production` and `APP_DEBUG=false`
- Run:
  - `php artisan config:cache`
  - `php artisan route:cache`
  - `php artisan view:cache`
- Build frontend: `npm run build`
- Run queue worker and scheduler in production process manager
- Configure backups and monitoring

## License

Proprietary project. All rights reserved.
