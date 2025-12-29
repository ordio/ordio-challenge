# Coding Challenge: Payroll Calculation

## Task

Create a service that calculates payroll for employees based on their shifts.

## Requirements

### Data Source

Shift data is retrieved from the Ordio API:
- **Endpoint:** `GET /api/shifts/december`
- **Period:** December 2025

### Pay Rules

| Rule                           | Amount             |
|--------------------------------|--------------------|
| Base hourly rate               | 15.00 EUR          |
| Night surcharge (20:00 - 00:00)| 17.00 EUR / hour   |
| Holiday surcharge              | 150% of hourly rate|

### Calculation Logic

1. **Regular working hours:** All hours are compensated at the base hourly rate (15 EUR).

2. **Night surcharge:** For work between 20:00 and 00:00, a surcharge of 2 EUR per hour is added (on top of the base hourly rate).

3. **Holiday surcharge:** On public holidays, the hourly rate is multiplied by 150% (22.50 EUR instead of 15 EUR).

### Public Holidays December 2025

- 25.12.2025 - Christmas Day
- 26.12.2025 - Boxing Day

## Expected Output

The service should calculate the following values per employee:

- Total working hours
- Regular working hours
- Night hours (20:00 - 00:00)
- Holiday hours
- Total pay (broken down by base pay, night surcharge, holiday surcharge)

## Example Calculation

**Shift:** 18:00 - 02:00 (8 hours) on a regular day

| Time Period   | Hours | Calculation                |
|---------------|-------|----------------------------|
| 18:00 - 20:00 | 2h    | 2 × 15 EUR = 30 EUR        |
| 20:00 - 00:00 | 4h    | 4 × (15 + 2) EUR = 68 EUR  |
| 00:00 - 02:00 | 2h    | 2 × 15 EUR = 30 EUR        |
| **Total**     | **8h**| **128 EUR**                |

**Shift:** 10:00 - 18:00 (8 hours) on 25.12.2025

| Time Period   | Hours | Calculation                     |
|---------------|-------|---------------------------------|
| 10:00 - 18:00 | 8h    | 8 × 15 EUR × 150% = 180 EUR     |
| **Total**     | **8h**| **180 EUR**                     |

## Technical Notes

- The project is based on Symfony 7.4
- PHP 8.2+ required
- API authentication is done via `ORDIO_API_KEY` in `.env`

## Setup

```bash
composer install
```

## Good luck!
