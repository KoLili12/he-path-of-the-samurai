# Python Telemetry Generator

Modern replacement for Pascal legacy service.

## Features

- **Typed CSV Generation**: Proper data types (timestamp, numeric, boolean, text)
- **PostgreSQL Integration**: Direct database inserts with parameterized queries
- **Error Handling**: Robust error handling and logging
- **Docker Support**: Containerized with health checks

## Data Types

The generator produces properly typed telemetry data:

| Field | Type | Description | Example |
|-------|------|-------------|---------|
| recorded_at | TIMESTAMPTZ | Exact datetime with timezone | 2025-12-09T22:30:15+00:00 |
| voltage | NUMERIC(5,2) | Voltage reading (2 decimals) | 8.45 |
| temp | NUMERIC(5,2) | Temperature (2 decimals) | -12.30 |
| operational | BOOLEAN | System operational status | TRUE/FALSE |
| source_file | TEXT | CSV filename | telemetry_20251209_223015.csv |
| status | TEXT | Status code | OK, WARNING, ERROR, OFFLINE |

## CSV Format

Generated CSV files have the following format:

```csv
recorded_at,voltage,temp,operational,source_file,status
2025-12-09T22:30:15.123456,8.45,-12.30,TRUE,telemetry_20251209_223015.csv,OK
2025-12-09T22:30:15.234567,9.12,45.67,TRUE,telemetry_20251209_223015.csv,WARNING
```

## Environment Variables

| Variable | Default | Description |
|----------|---------|-------------|
| CSV_OUT_DIR | /data/csv | CSV output directory |
| GEN_PERIOD_SEC | 300 | Generation interval (seconds) |
| PGHOST | db | PostgreSQL host |
| PGPORT | 5432 | PostgreSQL port |
| PGUSER | monouser | PostgreSQL user |
| PGPASSWORD | monopass | PostgreSQL password |
| PGDATABASE | monolith | PostgreSQL database |

## Database Schema

```sql
CREATE TABLE telemetry_legacy (
    id SERIAL PRIMARY KEY,
    recorded_at TIMESTAMPTZ NOT NULL,
    voltage NUMERIC(5,2) NOT NULL,
    temp NUMERIC(5,2) NOT NULL,
    operational BOOLEAN NOT NULL,
    source_file TEXT NOT NULL,
    status TEXT NOT NULL,
    created_at TIMESTAMPTZ DEFAULT NOW()
);
```

## Running

### With Docker
```bash
docker-compose up python_telemetry
```

### Standalone
```bash
python3 telemetry_generator.py
```

## Logging

Logs are written to stdout in the format:
```
2025-12-09 22:30:15 - INFO - CSV file created: /data/csv/telemetry_20251209_223015.csv
2025-12-09 22:30:15 - INFO - Inserted 5 rows into database
2025-12-09 22:30:15 - INFO - ✓ Successfully generated 5 telemetry records
```

## Improvements over Pascal Legacy

1. **Type Safety**: Proper data types (timestamp, numeric, boolean, text)
2. **Modern Language**: Python 3.11 with type hints
3. **Better Error Handling**: Comprehensive exception handling
4. **SQL Injection Protection**: Parameterized queries
5. **Logging**: Structured logging to stdout/stderr
6. **Containerization**: Docker support with health checks
7. **Code Quality**: Clean, readable, maintainable code
