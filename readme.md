# COMING SOON #
This package is in development at the moment. I will expand the documentation soon!

## How to install ##

> composer require donny5300/modulair-router dev-master

### To use the defaults of the package, please install mcrowe/twigbridge ###

Add the Service provider
> Donny5300\ModulairRouter\ServiceProvider::class,

Add the router singleton to /project/path/bootstrap/app.php

> $app->singleton('router', Donny5300\ModulairRouter\Router::class);

Add the dynamic resource bindings to your routes.php
> $router->dynamicResourceBindings();

###### Optional ######
Add the exceptions to your Exceptions/Handler.php
```
if( $e instanceof ControllerNotFoundException )
{
	return app( ControllerNotFoundExceptionController::class )->missingController( $e );
}

if( $e instanceof ActionNotFoundException )
{
	return app( ActionNotFoundExceptionController::class )->missingAction( $e );
}

if( $e instanceof ViewNotFoundException )
{
	return app( ViewNotFoundExceptionController::class )->missingView( $e );
}

if( $e instanceof DatabaseRouteNotFoundException )
{
	return app( RouteException::class )->missingRoute( $e );
}
```

### Using custom views ###
To change the for a i.e. the /system/namespaces/, change the system_views in your config. The `user` key will now direct to user.management.(methodName)


## Exceptions ##
For easy development, i've added some exceptions:
* ViewNotFound
* ActionNotFound
* ControllerNotFound
* DatabaseRouteNotFound

#### ViewNotFound ####
When a view is not found, you will be redirected to the ViewNotFoundController, where you can create a view with just 1 click. The path will always be: namespace/module/controller/action

#### ActionNotFound ####
Just throws a exception to notify you that the method is not found in the ClassController

#### ControllerNotFound ####
A exception that notifies you about the missing controller. There can be 2 scenario's up here: File doesnt exist or the namesapce/class is invalid. Both will be answered by the view.

#### DatabaseRouteNotFound ####
To make sure that all namespaces, modules, controllers and actions are registered, a exception will be thrown when one of those aren't available. You will be redirected to the RouteException (controller) (need to rename it) and there will be a proposal for you.

## Syncing from 1 environment to another ##
It can be annoying to do the same thing twice when creating a application. This method gives you the oppertunity to sync 1 environment with another. Enter the URL with the routes that are available (by default /system/modulair-routes) and the system will display all missing or deleted routes. Select wich items you wish to restore and voila, your done. Notice: you can set a default sync link in your modulair-router.php config

## Clean up ##
All namespaces, modules, controllers and actions, can be stored once. They will be removed with soft delete and when you sync with another environment that has the item, it will be restored. But sometimes you want to make a fresh start and remove all the soft deletes. Or just restore all of it. Hit the clean-up button and the packages will do the rest.

# ONE IMPORTANT THING #
Some actions should not happen on a production environment. Think about creating a view, or just allow a route to be created or a ControllerClass. Set the debug in your config to false, to prevent actions that can harm your application. When debug is `false`, a DeveloperMode exception will be thrown.

# Questions #
For questions: please create a issue and add the question label. Thank you