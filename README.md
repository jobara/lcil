# Legal Capacity Inclusion Lens

| main  | dev |
| ----- | --- |
| [![code coverage badge for main branch](https://codecov.io/gh/fluid-project/lcil/branch/main/graph/badge.svg?token=XZ7R5ISIBR)](https://codecov.io/gh/fluid-project/lcil) | [![code coverage badge for dev branch](https://codecov.io/gh/fluid-project/lcil/branch/dev/graph/badge.svg?token=XZ7R5ISIBR)](https://app.codecov.io/gh/fluid-project/lcil/branch/dev)  |

## Overview

The Legal Capacity Inclusion Lens (LCIL) is built using the [Laravel](https://laravel.com) PHP framework and
[Hearth](https://github.com/fluid-project/hearth), a Laravel starter kit.

Deployments:

* [dev](https://lcil-dev.fluidproject.org)

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

7. To update JS and CSS dependencies run the vite build

   ```bash
   sail npm run build
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
   * `DB_DATABASE`: usually `lcil`; will likely need to create a new database in MySQL or MariaDB first
   * `DB_USERNAME`
   * `DB_PASSWORD`

3. If you need to create a database you can do so from the command line like:

    ```bash
    mysql -uroot -e "create database lcil;"
    ```

    You can also create the database from an application like [Sequel Ace](https://github.com/Sequel-Ace/Sequel-Ace).

    _**NOTE:** If the database is run through an external app like [DBngin](https://dbngin.com) the CLI command above
    may not work._

4. Ensure that a database called `lcil_test` is also created in your local database. This is used for running the tests.
   If you prefer to use a different database you'll need to add a .env.testing file with the modified DB_DATABASE name.
   You'll also need to add DB_DATABASE_TEST with the new database name to your main .env file to setup the database for
   running the migrations as mentioned below.

    ```bash
    mysql -uroot -e "create database lcil_test;"
    ```

5. Run the migrations and seed the database

   ```bash
   php artisan migrate:refresh --seed
   ```

6. Run the migrations for the test database

   ```bash
   php artisan migrate:fresh --database=mysql-test
   ```

7. Serve the application. If using [Laravel Valet](https://laravel.com/docs/9.x/valet), this step shouldn't be
   necessary.

   ```bash
   npm run dev

   # use ctrl-c to terminate the server
   ```

8. To update JS and CSS dependencies run the vite build

   ```bash
   npm run build
   ```

9. To debug JS or CSS run the `dev` npm script which will enable source maps

   ```bash
   npm run dev
   ```

10. For account creation the app requires a mail server. You can use [MailHog](https://github.com/mailhog/MailHog) to
   simulate email communication. This is already configured in Sail, but will need to be installed manually for local
   development. Once MailHog is installed update the `MAIL_HOST` variable in the `.env` file with the IP that MailHog
   bound SMTP to.

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

# composer script that will also remove old translations
php composer localize
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

[Vite](https://vitejs.dev), with [laravel-vite-plugin](https://www.npmjs.com/package/laravel-vite-plugin), is used for
compiling assets such as JavaScript and CSS. The configuration can be found in [vite.config.js](./vite.config.js). If
you change any of the assets you'll need to trigger a rebuild.

```bash
npm run build
```

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

_**NOTE:** If tests are running slow, you may want to run them in parallel using the `--parallel` flag. See:
[Memory leak on testing](https://github.com/laravel/framework/discussions/39255) for more discussion on potential
issues with tests._

A composer script has been included to make running tests locally easier; includes coverage reporting and running all
tests in parallel:

```bash
php composer coverage
```

### Linting

Static analysis of PHP files is done using [Larastan](https://github.com/nunomaduro/larastan). The default configuration
is provided in the [`phpstan.neon.dist`](./phpstan.neon.dist) file. If you'd like to use a different local configuration
or perhaps modified configuration in CI, a `phpstan.neon` file can be used to supersede the default config file.
`phpstan.neon` has been added to the [`.gitignore`](./.gitignore) file and is excluded from version control.

Additionally styling fixes can be addressed using [Laravel Pint](https://github.com/laravel/pint) by running the
provided `format` composer script.

To perform the analysis run:

```bash
# if using sail
sail composer lint
sail composer format

# when running locally
php composer lint
php composer format
```

You can pass in flags to the format script after `--`.

```bash

# verbose output
sail composer format -- -v
php composer format -- -v

# test output, doesn't change the files
sail composer format -- --test
php composer format -- --test
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

### Restricting Registration

The application uses [Laravel Fortify](https://laravel.com/docs/9.x/fortify) to manage authentication. The majority of
configuration options can be found in the fortify.php config file. In addition, the application provides an additional
layer of registration restriction by providing `registration` options in the settings.php config file.

* `settings.registration.restricted`: defines if registration restrictions should be enforced. Defaults to true. Can
  also be set with the `RESTRICT_REGISTRATION` environment variable.
* `settings.registration.allowlist`: An array of domain names to allow. E.g. `['example.com']`. If this value is
  removed, or an empty array, all domains will be permitted.
* `settings.registration.blocklist`: An array of domain names to block, takes precedence over allowlist.
  E.g `['example.com']`. An empty array, `[]`, can be used if no domains are blocked.

### Migrating and Seeding

1. Run the database migrations

   ```bash
   php artisan migrate
   ```

2. Seed the constant values into the database

   ```bash
   php artisan db:seed --class=ConstantsSeeder
   ```

## Setting and clearing caches

At times you may want to clear or reset caches, for example when updating a deployed site or making local development
changes to the .env file.

For more information see [Optimizing configuration loading documentation](https://laravel.com/docs/9.x/deployment#optimizing-configuration-loading)

_**Note:** If clearing the caches doesn't address the issue, the serve may need to be restarted._

### Application cache

To clear manually stored caches:

```bash
php artisan cache:clear
```

To clear a specific store:

```bash
php artisan cache:clear --store={name}
```

For more information see: [Cache documentation](https://laravel.com/docs/9.x/cache)

### Config cache

To clear the config cache. This may need to be done if you change any settings in a config or environment variable.

```bash
php artisan config:clear
```

You could also combine the config files into a single cache, which will also improve performance.

```bash
php artisan config:cache
```

### Event cache

You can cache even handling, which may improve performance.

```bash
php artisan event:cache
```

If you do so, you'll need to rerun the cache or clear it (see below) after making changes.

```bash
php artisan event:clear
```

### Google-Fonts

You can fetch Google Fonts and store them on disk (uses  spatie/laravel-google-fonts).

```bash
php artisan google-fonts:fetch
```

### Icon cache

You can discover icon sets and generate a manifest file (uses Blade UI kit)

```bash
php artisan icons:cache
```

You can also remove the blade icons manifest file.

```bash
php artisan icons:clear
```

### Route cache

Route caches are only for deployments.

```bash
php artisan route:cache
```

The route cache can be cleared, in particular if you cached the routes in your local setup.

```bash
php artisan route:clear
```

### View cache

The view cache is useful for deploys and pre-renders the views.

```bash
php artisan view:cache
```

However, it shouldn't be used for local development as your changes will not be reflected. In which case you may need to
clear the view cache.

```bash
php artisan view:clear
```

## Helpful tools

* [Clockwork](https://underground.works/clockwork/): php dev tools in the browser
* [Sequel Ace](https://github.com/Sequel-Ace/Sequel-Ace): Mac database management application
* [DBngin](https://dbngin.com): Database version management tool
* [Laravel Valet](https://laravel.com/docs/9.x/valet): development environment for macOS
