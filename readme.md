# COMING SOON #

## How to install ##

> composer require donny5300/modulair-router dev-master

Add the Service provider
> Donny5300\ModulairRouter\ServiceProvider::class,

Add the router singleton to /project/path/bootstrap/app.php

> $app->singleton('router', Donny5300\ModulairRouter\Router::class);

Add the dynamic resource bindings to your routes.php
> $router->dynamicResourceBindings();