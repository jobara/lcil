# Legal Capacity Inclusion Lens

## Overview

The Legal Capacity Inclusion Lens (LCIL) is built using the [Laravel](https://laravel.com) PHP framework and
[Hearth](https://github.com/fluid-project/hearth), a Laravel starter kit.

## Development setup

### Requirements

* [PHP 8.1](https://www.php.net)
* [Composer](https://getcomposer.org)
* Development in a container
  * [Docker](https://www.docker.com)
* Development locally
  * [Node.js](https://nodejs.org/en/)
  * [npm](https://www.npmjs.com)
  * [MySQL](https://www.mysql.com) or [MariaDB](https://mariadb.org)

### Installation

1. Clone the repository
2. Create the `.env` file in the project root.

   ```bash
   cp .env.example .env
   ```

3. Install the dependencies via Composer

   ```bash
   composer install
   ```

### Setup for developing in a container

Using [Laravel Sail](https://laravel.com/docs/8.x/sail) provides a container with a development environment. For
example, deploying and configuring the database and serving the application. Sail is already included as a dev
dependency, but you'll likely want to
[configure a `sail` bash alias](https://laravel.com/docs/8.x/sail#configuring-a-bash-alias), as is assumed in the
exmaples below. For Windows users, Sail is supported via [WSL2](https://docs.microsoft.com/en-us/windows/wsl/about).

1. Launch with Laravel Sail

   ```bash
   # Will be served at the location specificed in the .env file
   # By default http://localhost
   sail up -d
   ```

2. Generate an application key

   ```bash
   sail artisan key:generate
   ```

3. Run the database migrations

   ```bash
   sail artisan migrate
   ```

4. When you need to stop the application

   ```bash
   sail down
   ```

### Setup for developing locally

If you prefer to develop locally, you'll need to setup and configure the database manually and update the `.env`
file with the appropriate information for accessing it.

1. Generate an application key

   ```bash
   php artisan key:generate
   ```

2. In the `.env` file, ensure that the following have been set correctly to access your local database:
   * `DB_HOST`: usually `localhost` or `127.0.0.1`
   * `DB_PORT`: usually `3306`
   * `DB_DATABASE`: will likely need to create a new database in MySQL or MariaDB first
   * `DB_USERNAME`
   * `DB_PASSWORD`

3. Run the database migrations

   ```bash
   php artisan migrate
   ```

4. Serve the application

   ```bash
   php artisan serve

   # use ctrl-c to terminate the server
   ```

## Helpful tools

* [Clockwork](https://underground.works/clockwork/): php dev tools in the browser
* [Sequel Ace](https://github.com/Sequel-Ace/Sequel-Ace): Mac database management application
* [Laravel Valet](https://laravel.com/docs/8.x/valet): development environment for macOS
