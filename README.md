# Disclaimer

The code is presented in а demonstration purposes. I can't guarantee it's even work, or work correctly, or free of any security vulnerabilities. Use it or run it on your own risk.

# What is it?

It's a 99% copy of commercial service ActiveDNS.net, running from 2011 until 2017. It might be still operational, but a bit outdated.

# How to get up and running

## Easy way with Docker

Assume that git and docker is already installed on your computer. Be sure that no one is using your local port `8080`.

Type in your command line:
```bash
git clone https://github.com/evc54/activedns.git app
cd app
docker-compose up
```

Building docker containers may take a while. After it finish and start-up script will do the job, it says like `Migrated up successfully` that's means the database is ready to go.

Open `http://localhost:8000/` in your browser and log in as `admin@activedns.net` with password `123456`. Or sign up for new account.

## Hard way with Bash

Get a web server with PHP **v5.6** module installed ([apache](https://www.php.net/manual/en/book.apache.php) or [nginx + php-fpm](https://www.php.net/manual/en/install.unix.nginx.php)) and MySQL or MariaDB.

Additionally you must install git and PHP composer.

Clone the app with git into your web directory:
```bash
git clone https://github.com/evc54/activedns.git /var/www
```

Make `public/assets` and `protected` directories to be writable by the web server process user.

Note, that app's web root is inside `public` directory. Go to nginx sites configuration directory and create (or update) site config:
```
server {
  server_name _;
  listen 80 default_server;
  root /var/www/public;

  location / {
    index index.php;
    try_files $uri $uri/ /index.php?$args;
  }

  location ~ \.php$ {
    fastcgi_pass unix:/run/php/php5.6-fpm.sock;
    fastcgi_index index.php;
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    fastcgi_param PATH_INFO $fastcgi_path_info;
  }
```

Create database and configure access to it in file `/var/www/protected/config/db.local.php`. Required to set `<hostname>`, `<database>`, `<username>` and `<password>`.
```php
  'connectionString'      => 'mysql:host=<hostname>;dbname=<database>',
  'username'              => '<username>',
  'password'              => '<password>',
  'charset'               => 'utf8',
  'tablePrefix'           => 'ad',
  'schemaCachingDuration' => 3600,
```

Next, install libraries with PHP composer:
```bash
cd /var/www
composer install
```

After composer's job done, process database migrations in the same directory:
```bash
php console.php migrate --interactive=0
```

If everything went well, your new site is ready to serve requests.

# Bad news

I completely lost my NSD config file, so if you really planned to use this app, you have to write it by your own.
