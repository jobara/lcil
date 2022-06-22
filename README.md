# Legal Capacity Inclusion Lens

| main  | dev |
| ----- | --- |
| [![code coverage badge for main branch](https://codecov.io/gh/fluid-project/lcil/branch/main/graph/badge.svg?token=XZ7R5ISIBR)](https://codecov.io/gh/fluid-project/lcil) | [![code coverage badge for dev branch](https://codecov.io/gh/fluid-project/lcil/branch/dev/graph/badge.svg?token=XZ7R5ISIBR)](https://app.codecov.io/gh/fluid-project/lcil/branch/dev)  |

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

Using [Laravel Sail](https://laravel.com/docs/9.x/sail) provides a container with a development environment. For
example, deploying and configuring the database and serving the application. Sail is already included as a dev
dependency, but you'll likely want to
[configure a `sail` bash alias](https://laravel.com/docs/9.x/sail#configuring-a-bash-alias), as is assumed in the
examples below. For Windows users, Sail is supported via [WSL2](https://docs.microsoft.com/en-us/windows/wsl/about).

1. If you change `DB_USERNAME`, you'll need to add the file
   `docker/provision/mysql/init/02_perms_test_db.sql` with the following contents where `{username}` is replaced with
   the value assigned to `DB_USERNAME`.

   ```sql
   GRANT ALL ON `lcil_test`.* TO '{username}'@'%';
   ```

2. Launch with Laravel Sail

   ```bash
   # Will be served at the location specified in the .env file
   # By default http://localhost
   sail up -d
   ```

3. Generate an application key

   ```bash
   sail artisan key:generate
   ```

4. Run the migrations and seed the database

   ```bash
   sail artisan migrate:refresh --seed
   ```

5. Run the migrations for the test database

   ```bash
   sail artisan migrate:fresh --database=mysql-test
   ```

6. When you need to stop the application

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

3. Ensure that a database called `lcil_test` is also created in your local database. This is used for running the tests.
   If you prefer to use a different database you'll need to add a .env.testing file with the modified DB_DATABASE name.
   You'll also need to add DB_DATABASE_TEST with the new database name to your main .env file to setup the database for
   running the migrations as mentioned below.

4. Run the migrations and seed the database

   ```bash
   php artisan migrate:refresh --seed
   ```

5. Run the migrations for the test database

   ```bash
   php artisan migrate:fresh --database=mysql-test
   ```

6. Serve the application. If using [Laravel Valet](https://laravel.com/docs/9.x/valet), this step shouldn't be
   necessary.

   ```bash
   php artisan serve

   # use ctrl-c to terminate the server
   ```

### Localization

When entering text into the templates and etc, use translatable strings. The text can be retrieved using [default
translation strings](https://laravel.com/docs/9.x/localization#using-translation-strings-as-keys) and passed to the
`__()` helper function. For example:

```php
__('String to be translated')
```

Parameters can be passed in and interpolated into the translation string by using tokens starting with `:` and passing
an associative array as the second argument with the value to substitute.

```php
__('Hello :name!', ['name' => 'World'])
// returns Hello World!
```

There is also support for [pluralization](https://laravel.com/docs/9.x/localization#pluralization) of strings.

These default translation strings need to be collected and added to the language JSON file(s) in the `lang` directory.
To do this automatically run the following CLI command:

```bash
# if using sail
sail artisan localize

# when running locally
php artisan localize
```

This will extract the strings and collect them for the default language specified in `app.locale`. If you'd like to
extract strings to localize in other languages pass in a comma separated list of language codes at the end.

```bash
# if using sail
sail artisan localize en,fr

# when running locally
php artisan localize en,fr
```

_**NOTE:** If you would like to use [short keys](https://laravel.com/docs/9.x/localization#using-short-keys) for
specifying language strings, you may need to update the [localizator.php](./config/localizator.php) file to also
extract those._

For more details on localization please see the Laravel [Localization](https://laravel.com/docs/9.x/localization) docs
and [Localizator README](https://github.com/amiranagram/localizator/blob/0.x/README.md).

### Rebuilding Assets

[Laravel Mix](https://laravel.com/docs/9.x/mix) is used for compiling assets such as JavaScript and CSS. The
configuration can be found in [webpack.mix.js](./webpack.mix.js). If you change any of the assets you'll need to
trigger mix to rebuild. (See: [Running Mix](https://laravel.com/docs/9.x/mix#running-mix))

### Testing

Tests are written using PHPUnit and Laravel's testing supports. For more information see
[Testing: Getting Started](https://laravel.com/docs/9.x/testing).

Use the following command to run the full test suite:

```bash
# if using sail
sail artisan test

# when running locally
php artisan test
```

Use the `--testsuite` flag to only run a particular test suite:

```bash
# if using sail
sail artisan test --testsuite=Feature

# when running locally
php artisan test --testsuite=Feature
```

Use the `--filter` flag to only run a particular test or testcase:

```bash
# if using sail
sail artisan test --filter ExampleTest
sail artisan test --filter ExampleTest::test_that_true_is_true

# when running locally
php artisan test --filter ExampleTest
php artisan test --filter ExampleTest::test_that_true_is_true
```

For PEST tests, use the `--group` flag to only run a group of tests.

```bash
# if using sail
sail artisan test --group=groupOne,groupeTwo

# when running locally
php artisan test --group=groupOne,groupTwo
```

For PEST tests, use [`->only()`](https://pestphp.com/docs/skipping-tests#running-single-test) to run a single test.
However, not that this applies globally to all tests. If you run a specific test file using `--filter` and `only()` is
specified in a different file, no tests will be found to run.

```php
test('test case', function () {})->only();
```

You can also get a test coverage report by using the `--coverage` flag and can also specify a minimum with the `--min`
flag. For example:

```bash
# if using sail
sail artisan test --coverage --min=80.3

# when running locally
php artisan test --coverage --min=80.3
```

If using sail, in the `.env` file set `SAIL_XDEBUG_MODE=develop,debug,coverage` to enable the coverage reporting.

_**NOTE:** Coverage reporting requires [Xdebug](https://xdebug.org/) or [PCOV](https://pecl.php.net/package/pcov).
see: [Test coverage with Xdebug](https://laracasts.com/series/whats-new-in-laravel-9/episodes/7)_

### Linting

Static analysis of PHP files is done using [Larastan](https://github.com/nunomaduro/larastan). The default configuration
is provided in the [`phpstan.neon.dist`](./phpstan.neon.dist) file. If you'd like to use a different local configuration
or perhaps modified configuration in CI, a `phpstan.neon` file can be used to supersede the default config file.
`phpstan.neon` has been added to the [`.gitignore`](./.gitignore) file and is excluded from version control.

Additionally styling fixes can be addressed using [PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) by
running the provided `format` composer script.

To perform the analysis run:

```bash
# if using sail
sail composer lint
sail composer format

# when running locally
php composer lint
php composer format
```

Linting of JavaScript, SCSS, MD and other files is handled by [fluid-lint-all](https://www.npmjs.com/package/fluid-lint-all).
Configuration is contained within the [`.fluidlintallrc.json`](./fluidlintallrc.json), [`.eslintignore`](./.eslintignore),
[`.eslintrc.json`](./.eslintrc.json) and [`.stylelintrc.json`](./.stylelintrc.json) files.

To run linting:

```bash
# if using sail
sail npm run lint

# when running locally
npm run lint
```

## Production

The production setup is likely similar to the local development setup but with the environment variables adjusted and
web server configured as needed.

### Setting up the environment

In the `.env` file a couple additional variables need to be adjusted to switch to the production environment.

* `APP_ENV=production`: sets the applications running environment to production
* `APP_DEBUG=false`: disables the full debugging to prevent sensitive information in error messages.

There are some "constant" database entries that should be populated into the production database. On the initial run

### Migrating and Seeding

1. Run the database migrations

   ```bash
   php artisan migrate
   ```

2. Seed the constant values into the database

   ```bash
   php artisan db:seed --class=ConstantsSeeder
   ```

## Helpful tools

* [Clockwork](https://underground.works/clockwork/): php dev tools in the browser
* [Sequel Ace](https://github.com/Sequel-Ace/Sequel-Ace): Mac database management application
* [Laravel Valet](https://laravel.com/docs/9.x/valet): development environment for macOS
