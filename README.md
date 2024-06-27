
# Shopify Demo Project


## Requirements

Before you begin, ensure you have met the following requirements:

- PHP >= 8.2
- Composer
- Docker if you want to use Laravel Sail
- MySQL if you want to use the ```artisan serve``` command

# Installation

Follow these steps to install Laravel:

### Clone the Repository

```
git clone git@github.com:jorshhh/shopify-domaine.git
cd shopify-domaine 
```

### Install composer project dependencies

```
composer install
```

## If you are using Docker with Laravel Sail

### 1. Copy the .env file

This .env example has all the values setup for us to use docker. The only thing that needs to be replaced is the ```SHOPIFY_STORE``` and ```SHOPIFY_TOKEN``` values. This will create a database for our project which is super convenient.

```
 cp .env.example.sail .env
```

### 2. Running the Docker container

```
./vendor/bin/sail up -d
```
This might take a while if it's the first time you're running the command.

We also need to run the database migration the first time we install the app

```
./vendor/bin/sail artisan migrate
```
App will be served at http://0.0.0.0

### 3. Stopping the app

To stop the app, run
```
./vendor/bin/sail down
```


## If you are using the serve command

### 1. Copy the .env file

This .env example has all the values setup for us to use the ```artisan serve``` command to test our app. This will serve the http requests but we still need to setup our MySQL database. Please create a database and update the necessary credentials.

Additionally, you will need to replace  the ```SHOPIFY_STORE``` and ```SHOPIFY_TOKEN``` values.

```
 cp .env.example .env
```

### 2. Run the migrations and generate the app key
```
 php artisan migrate && php artisan key:generate
```

### 3. Serve the app
```
 php artisan serve
```
App will be served at http://0.0.0.0

