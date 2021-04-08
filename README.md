# Soycue-API

Mon api pour une application de réseau social de vieille --in progress

## Installation instruction

1. run `git clone https://github.com/Math2512/my_api in your development environement`

2. run `composer update`

3. Check your .env to change :
```
    -DB_CONNECTION=mysql
    -DB_HOST=127.0.0.1
    -DB_PORT=3306
    -DB_DATABASE=test_soyhuce
    -DB_USERNAME=root
    -DB_PASSWORD=

```

4. run `php artisan migrate`


5. run `php artisan serve`

6. Don't forget to check your .env to change :) :
```
    AWS_ACCESS_KEY_ID=
    AWS_SECRET_ACCESS_KEY=
    AWS_DEFAULT_REGION=
    AWS_BUCKET=
    GOOGLE_CLIENT_ID=
    GOOGLE_CLIENT_SECRET=
    GOOGLE_REDIRECT=
```
