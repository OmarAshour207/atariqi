## pre requirements:

- php 8.0 at least
- composer installed
- mysql 5.7 and create new database manually

## Make app working

- Clone the repo using `git clone https://github.com/OmarAshour207/atariqi.git`.
- Run `cd /atariqi` then `composer install`.
- Make copy from .env.example file called .env .
- Make database and put database name,username and password in .env file.
- Run `php artisan migrate --seed`.
- Congrats.
