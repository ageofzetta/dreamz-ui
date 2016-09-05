el KARNALO SE DESESPERA MUCHO.
:(

# Installation


**VM**

If you're going to clone this repo and plan to contribute to it first you need to setup a proper Development Enviroment.

A LEMP Stack with PHP 5.6+ is needed to run this project, if you do not know how to setup this i suggest to use [Laravel Homestead](https://www.youtube.com/watch?v=tbPGuikTzKk).


**Clone the repo**

```
git clone git@github.com:fabricadesoluciones/dreamz.git dreamz
```

**Install Composer**

Composer is a tool for dependency management in PHP. It allows you to declare the libraries your project depends on and it will manage (install/update) them for you.

Go [install it](https://getcomposer.org/doc/00-intro.md) if you haven't already.


**Install Composer packages**

```
composer install
```

**Run migrations**

```
php artisan migrate
```

**Run DB seeding**

```
php db:seed
```


[Passwords!](database/seeds/UserTableSeeder.php)

