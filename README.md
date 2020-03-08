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

Copy the `.env` to `.env.local`. Edit `.env.local` by specifying the [database url](https://symfony.com/doc/current/doctrine.html#configuring-the-database).

```bash
  # Drop database if previously installed by
  # ./bin/console doctrine:schema:drop

  # Create database
  ./bin/console doctrine:schema:create

Generate the security keys:

```bash
    mkdir -p config/jwt
    openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
    openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout
```

Edit the JWT_PASSPHRASE and CORS_ALLOW_ORIGIN variables in `.env.local` file to suit your case.

See: [LexikJWTAuthenticationBundle](https://github.com/lexik/LexikJWTAuthenticationBundle).

## Run the server

```bash
    symfony server:start
```

## Load fixtures

Write fixtures files in the fixtures/ folder.

```bash
  php bin/console hautelook:fixtures:load
```

See: [AliceBundle](https://github.com/hautelook/AliceBundle) and [alice fixtures](https://github.com/nelmio/alice).

See [Faker](https://github.com/fzaninotto/Faker) for fixtures data types.

## License

This bundle is under the MIT license.  
For the whole copyright, see the [LICENSE](LICENSE) file distributed with this source code.
