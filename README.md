# Yentu

[![Build Status](https://github.com/ntentan/yentu/actions/workflows/tests.yml/badge.svg)](https://github.com/ntentan/yentu/actions/workflows/tests.yml)
[![Latest Stable Version](https://poser.pugx.org/ntentan/yentu/v/stable.svg)](https://packagist.org/packages/ntentan/yentu)
[![Total Downloads](https://poser.pugx.org/ntentan/yentu/downloads.svg)](https://packagist.org/packages/ntentan/yentu)
[![License](https://poser.pugx.org/ntentan/yentu/license.svg)](https://packagist.org/packages/ntentan/yentu)

A framework-independent, PHP-based database migration tool. Yentu allows you to track and manage changes to your database schema using your current version control system.

## Features

- **Fluent Migration DSL**: Express schema definitions in readable, declarative PHP code (`begin()`, `table()`, `column()`, etc.).
- **Multiple Database Support**: First-class support for **PostgreSQL**, **MySQL**, and **SQLite**.
- **Schema Reverse-Engineering**: Import existing database schemas directly into versioned migrations.
- **Transactional & Reversible**: Automated reversions and session-based rollbacks (`yentu rollback`).
- **Flexible Migration Execution**: Dry-runs (`--dry`), query dumping (`--dump-queries`), and decoupled foreign-key constraints (`--no-foreign-keys`, `--only-foreign-keys`).
- **VCS Friendly**: Migration files are self-contained and tracked cleanly in Git.

## Requirements

- PHP >= 8.3
- PDO extension with the appropriate driver:
  - `pdo_pgsql` for PostgreSQL
  - `pdo_mysql` for MySQL
  - `pdo_sqlite` for SQLite

## Installation

Install Yentu via Composer into your project:

```bash
composer require ntentan/yentu
```

Or install it globally:

```bash
composer global require ntentan/yentu
```

## Getting Started

### 1. Initialize Yentu

In your project root, run the initialization command:

```bash
vendor/bin/yentu init
```

You can initialize interactively:

```bash
vendor/bin/yentu init --interactive
```

Or pass connection flags directly:

```bash
# PostgreSQL
vendor/bin/yentu init -d postgresql -h localhost -u postgres -p secret -n my_database

# MySQL
vendor/bin/yentu init -d mysql -h localhost -u root -p secret -n my_database

# SQLite (file-based)
vendor/bin/yentu init -d sqlite -f my_database.sqlite3

# Using a connection DSN
vendor/bin/yentu init -s "pgsql:host=localhost;dbname=my_database;user=postgres;password=secret"
```

This creates the following directory structure:

```
yentu/
├── config/
│   └── yentu.ini
└── migrations/
```

### 2. Configuration (`yentu/config/yentu.ini`)

The configuration file specifies your database connection details:

```ini
[db]
driver = postgresql
host = localhost
port = 5432
dbname = my_database
user = postgres
password = secret
```

Alternatively, you can provide a connection DSN:

```ini
[db]
dsn = "pgsql:host=localhost;dbname=my_database;user=postgres;password=secret"
```

### 3. Create a Migration

Generate a new timestamped migration:

```bash
vendor/bin/yentu create create_users_table
```

This creates a new file in `yentu/migrations/YYYYMMDDHHIISS_create_users_table.php`.

### 4. Write Your Migration

Use Yentu's fluent interface to describe your schema:

```php
<?php

begin()
    ->table('users')
        ->column('id')->type('integer')->nulls(false)->autoIncrement()
        ->column('username')->type('string')->length(64)->nulls(false)
        ->column('email')->type('string')->length(255)->nulls(false)
        ->column('created_at')->type('timestamp')->nulls(false)
        ->primaryKey('id')
        ->unique('username')
        ->unique('email')
    ->table('posts')
        ->column('id')->type('integer')->nulls(false)->autoIncrement()
        ->column('user_id')->type('integer')->nulls(false)
        ->column('title')->type('string')->length(255)->nulls(false)
        ->column('body')->type('text')->nulls(true)
        ->primaryKey('id')
        ->foreignKey('user_id')->references('users')->columns('id')->onDelete('cascade')
->end();
```

### 5. Run Migrations

Apply pending migrations to your database:

```bash
vendor/bin/yentu migrate
```

To inspect queries without executing them:

```bash
vendor/bin/yentu migrate --dry --dump-queries
```

### 6. Roll Back Migrations

Roll back the last executed migration session:

```bash
vendor/bin/yentu rollback
```

### 7. Check Migration Status

Check which migrations have been applied and which are pending:

```bash
vendor/bin/yentu status

# Detailed view of operations performed per migration:
vendor/bin/yentu status --details
```

## Importing an Existing Database

If you have an existing database, Yentu can reverse-engineer it into an initial migration:

```bash
vendor/bin/yentu import
```

Use `--skip-defaults` (`-d`) if you do not want to import default column values.

## Migration DSL Reference

### Tables & Columns

```php
begin()
    ->table('articles')
        ->column('title')->type('string')->length(200)->nulls(false)
        ->column('views')->type('integer')->defaultValue(0)
        ->column('published')->type('boolean')->defaultValue(false)
        ->column('content')->type('text')
        ->column('rating')->type('double')
->end();
```

### Supported Column Types

- `integer`
- `string`
- `text`
- `boolean`
- `timestamp`
- `date`
- `double`
- `blob`

### Keys & Constraints

```php
begin()
    ->table('orders')
        ->primaryKey('order_id')
        ->index('customer_id')
        ->unique('order_number')
        ->foreignKey('customer_id')
            ->references('customers')
            ->columns('id')
            ->onDelete('cascade')
            ->onUpdate('cascade')
->end();
```

### Schema & Table References

For modifying existing tables or working across PostgreSQL schemas:

```php
// Modify an existing table
reftable('users')
    ->column('avatar_url')->type('string')->nulls(true);

// Work within a specific PostgreSQL schema
begin()
    ->schema('billing')
        ->table('invoices')
            ->column('id')->type('integer')->primaryKey()
->end();
```

## CLI Command Reference

| Command | Description | Common Options |
| :--- | :--- | :--- |
| `yentu init` | Initialize Yentu directories and config | `-i`, `--driver`, `-h`, `-u`, `-p`, `-n`, `-s` (dsn), `-c` |
| `yentu create <name>` | Create a new timestamped migration | |
| `yentu migrate` | Run pending migrations | `--dry`, `--dump-queries`, `--no-foreign-keys`, `--only-foreign-keys` |
| `yentu rollback` | Roll back the most recent migration session | |
| `yentu status` | Show status of migrations | `--details` |
| `yentu import` | Reverse-engineer an existing database | `-d` (`--skip-defaults`) |

### Global Options

- `-y`, `--home <path>`: Specify the Yentu working directory.
- `-c`, `--config-path <path>`: Specify custom configuration file path (default: `yentu/config/yentu.ini`).
- `-v`, `--verbose <level>`: Set verbosity (`high`, `mid`, `low`, `none`).
- `--debug`: Display full stack traces on error.

## Running Tests

Run the test suites using PHPUnit:

```bash
# Run unit tests
vendor/bin/phpunit --testsuite unit

# Run SQLite integration tests
vendor/bin/phpunit --configuration tests/config/sqlite.xml --testsuite integration

# Run PostgreSQL integration tests (requires PostgreSQL on 127.0.0.1:5432)
vendor/bin/phpunit --configuration tests/config/postgresql.xml --testsuite integration

# Run MySQL integration tests (requires MySQL on 127.0.0.1:3306)
vendor/bin/phpunit --configuration tests/config/mysql.xml --testsuite integration
```

## License

Yentu is open-source software licensed under the [MIT License](https://opensource.org/licenses/MIT).
