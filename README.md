# COCO API

Container load business application backend.

# Requirements

- [Composer](https://getcomposer.org)/[Symfony](https://symfony.com) enabled system.
- PHP >= 7.4.2
- MySQL/MariaDB

## Installation

```bash
    git clone https://github.com/ywemay/coco-api.git

    cd coco-api

    composer install
```

## Set up

Copy the `.env` to `.env.local`. Edit `.env.local` setting up database by specifying the [database url](https://symfony.com/doc/current/doctrine.html#configuring-the-database).

Edit the `JWT_PASSPHRASE` and `CORS_ALLOW_ORIGIN` variables in `.env.local` file to suit your case.

Run the setup script:

```bash
  ./bin/setup
```
The setup script will create local and test databases, make migrations for both databases,
load fixtures and generate security keys.

For security the system uses: [LexikJWTAuthenticationBundle](https://github.com/lexik/LexikJWTAuthenticationBundle).

## Run the server

```bash
    symfony server:start
```

## Fixtures

Fixtures feature populates database with demo/test data.
See fixtures/dummy.yml for default declared records.
One may append his own fixture files in the fixtures/ folder.

```bash
  ./bin/db -f
  # for test environment:
  ./bin/db -f --env test
```

See: [AliceBundle](https://github.com/hautelook/AliceBundle) and [alice fixtures](https://github.com/nelmio/alice).

See [Faker](https://github.com/fzaninotto/Faker) for fixtures data types.

## Working with databases

Drop, create, migrate and load fixtures shortcut for both local and test environments:

```bash
  ./bin/db -a
```

For details on script check:
```bash
  ./bin/db --help
```
## Testing

Run all tests from `tests` directory:

```bash
  ./bin/phpunit
```

Run specific test:

```bash
  ./bin/phpunit tests/UsersTest.php --filter List
```

## License

This bundle is under the MIT license.  
For the whole copyright, see the [LICENSE](LICENSE) file distributed with this source code.
