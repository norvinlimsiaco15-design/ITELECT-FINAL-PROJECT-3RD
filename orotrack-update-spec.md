# OroTrack: Update Spec for Existing System

Source: ACES Tagum College project proposal (ITElec3), Oct 2026.

Update the **existing** Laravel codebase. Do **not** rebuild from scratch. Keep Laravel, MySQL, and AdminLTE.

## Naming / Branding

- System name: **OroTrack: A Construction Soil and Rock Inventory and Material Management System**
- Client: Oro Construction Soil and Rock, Tagum City, Davao del Norte
- Standardize on **OroTrack** (not "Oro Contrack") in UI titles, headers, login, footer, reports, and README.

## User Roles

| Role | Responsibilities |
| --- | --- |
| Inventory Personnel | Maintain material records; record stock-in, stock-out, adjustments |
| Administrator | Manage user accounts and system settings |
| Management | Review inventory reports |

## Required Features

1. Material management / classification (name, description, unit, quantity, min stock, unit price; categories: soil, sand, gravel, crushed rock)
2. Inventory monitoring from recorded transactions
3. Stock-in / stock-out / adjustments with validation and automatic balance updates
4. Low-stock notifications when quantity <= minimum stock level
5. Searchable/filterable transaction history
6. Inventory reports including total inventory value (quantity × unit price)
7. User accounts and authentication (admin manages users)

## Out of Scope

- Financial accounting, payroll, employee management
- GPS / delivery vehicle tracking
- Automatic volume/weight measurement
- Independent physical stock verification
