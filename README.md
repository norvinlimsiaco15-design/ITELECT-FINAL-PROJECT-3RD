<<<<<<< HEAD
# OroTrack: A Construction Soil and Rock Inventory and Material Management System

Centralized web system for **Oro Construction Soil and Rock**, Tagum City, Davao del Norte, to record, monitor, and manage construction materials (soil, sand, gravel, crushed rock): quantities, prices, stock movement, and inventory value.

This project is adapted from an existing Laravel + MySQL + AdminLTE application. The original appointment-booking UI is retained in the codebase but is not the primary workflow.

## Roles

- **Administrator** — user accounts and system settings
- **Inventory Personnel** — material records; stock-in, stock-out, adjustments
- **Management** — inventory reports

## Features

- Material management and classification
- Inventory monitoring from recorded quantities
- Stock-in / stock-out / adjustments with validation (no negative quantities; no stock-out beyond available stock)
- Low-stock alerts when quantity is at or below the configured minimum
- Searchable transaction history
- Reports: valuation (quantity × unit price), stock levels, low-stock list, transaction records

The system relies on user data entry and regular stocktaking. It does not independently verify physical stock.

## Installation

1. Install dependencies: `composer install`
2. Copy `.env.example` to `.env` and set MySQL credentials
3. `php artisan key:generate`
4. `php artisan migrate`
5. `php artisan db:seed`
6. `php artisan serve`

Open http://localhost:8000 and sign in.

## Demo accounts

| Role | Email | Password |
| --- | --- | --- |
| Administrator | admin@example.com | admin123 |
| Inventory Personnel | inventory@example.com | inventory123 |
| Management | management@example.com | management123 |

Demo accounts are created only on a fresh user table (`php artisan db:seed` when no users exist).
=======
# ITELECT-FINAL-PROJECT-3RD
SCHOOL PROJECT ONLY 
>>>>>>> 9442e9d305682fff5c3db507e74f094542a7f03f
