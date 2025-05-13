# Gold trading system implementation

## project description

this is an implementation of an order book buy sell trading platform

## Setup

- clone the repo and run the following commands

```sh
cd src
cp .env.example .env
cp .env.testing.example .env
cd ../
docker compose up -d --build
docker exec -it tlyn-php composer install
docker exec -it tlyn-php php artisan migrate --seed
```

## Postman link

https://app.getpostman.com/join-team?invite_code=a9c9f5ef6363343aaf4234445f8128437329ce2e1b96deb392a05150589955a4&target_code=dc957015838fa4d88769549928dce658

## How to test the application in PostMan

1. Login the User via the "User/Login" request and set the token global variable
2. Place orders or view your user orders

## Project description based on task

I created an order book system and implemented core required things to consider such as:

- race condition checking via transactions and DB locks
- match buy and sell orders efficiently to ensure database performance
- after a user places and order a matching job will be dispatched every second as a cronjob to make trades

##### User Authentication and Authorization

- Authentication is scaffolded with sanctum that is built-in laravel
- Caching is done by redis.


