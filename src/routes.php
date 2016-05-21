<?php

	$config = config( 'modulair-router.system_routes' );

	Route::group( [ 'prefix' => $config['base_path'] ? $config['base_path'] : 'system', 'middleware' => 'web' ], function () use ( $config )
	{
		Route::group( [ 'prefix' => $config['namespaces'] ? $config['namespaces'] : 'namespaces' ], function ()
		{
			Route::get( '/', 'Donny5300\ModulairRouter\Controllers\System\AppNamespaceController@index' );
			Route::get( '/create', 'Donny5300\ModulairRouter\Controllers\System\AppNamespaceController@create' );
			Route::post( '/create', 'Donny5300\ModulairRouter\Controllers\System\AppNamespaceController@store' );

			Route::get( '/{uuid}/edit', 'Donny5300\ModulairRouter\Controllers\System\AppNamespaceController@edit' );
			Route::post( '/{uuid}/edit', 'Donny5300\ModulairRouter\Controllers\System\AppNamespaceController@update' );

			Route::post( '/{uuid}', 'Donny5300\ModulairRouter\Controllers\System\AppNamespaceController@destroy' );
		} );

		Route::group( [ 'prefix' => $config['controllers'] ? $config['controllers'] : 'controllers' ], function ()
		{
			Route::get( '/', 'Donny5300\ModulairRouter\Controllers\System\AppControllersController@index' );
			Route::get( '/create', 'Donny5300\ModulairRouter\Controllers\System\AppControllersController@create' );
			Route::post( '/create', 'Donny5300\ModulairRouter\Controllers\System\AppControllersController@store' );

			Route::get( '/{uuid}/edit', 'Donny5300\ModulairRouter\Controllers\System\AppControllersController@edit' );
			Route::post( '/{uuid}/edit', 'Donny5300\ModulairRouter\Controllers\System\AppControllersController@update' );

			Route::post( '/{uuid}', 'Donny5300\ModulairRouter\Controllers\System\AppControllersController@destroy' );
		} );

		Route::group( [ 'prefix' => $config['modules'] ? $config['modules'] : 'modules' ], function ()
		{
			Route::get( '/', 'Donny5300\ModulairRouter\Controllers\System\AppModulesController@index' );
			Route::get( '/create', 'Donny5300\ModulairRouter\Controllers\System\AppModulesController@create' );
			Route::post( '/create', 'Donny5300\ModulairRouter\Controllers\System\AppModulesController@store' );

			Route::get( '/{uuid}/edit', 'Donny5300\ModulairRouter\Controllers\System\AppModulesController@edit' );
			Route::post( '/{uuid}/edit', 'Donny5300\ModulairRouter\Controllers\System\AppModulesController@update' );

			Route::post( '/{uuid}', 'Donny5300\ModulairRouter\Controllers\System\AppModulesController@destroy' );
		} );

		Route::group( [ 'prefix' => $config['methods'] ? $config['methods'] : 'methods' ], function ()
		{
			Route::get( '/', 'Donny5300\ModulairRouter\Controllers\System\AppMethodsController@index' );
			Route::get( '/create', 'Donny5300\ModulairRouter\Controllers\System\AppMethodsController@create' );
			Route::post( '/create', 'Donny5300\ModulairRouter\Controllers\System\AppMethodsController@store' );

			Route::get( '/{uuid}/edit', 'Donny5300\ModulairRouter\Controllers\System\AppMethodsController@edit' );
			Route::post( '/{uuid}/edit', 'Donny5300\ModulairRouter\Controllers\System\AppMethodsController@update' );

			Route::post( '/{uuid}', 'Donny5300\ModulairRouter\Controllers\System\AppMethodsController@destroy' );
		} );

		Route::group( [ 'prefix' => 'exceptions', 'middleware' => 'routeDevelopmentMode' ], function ()
		{
			Route::post( 'controller/create', 'Donny5300\ModulairRouter\Controllers\Exceptions\ControllerNotFoundExceptionController@createMissingController' );
			Route::get( 'views/create', 'Donny5300\ModulairRouter\Controllers\Exceptions\ViewNotFoundExceptionController@createMissingView' );
			Route::post( 'missing-route/create', 'Donny5300\ModulairRouter\Controllers\Exceptions\Database\RouteException@storeRoute' );
		} );

		Route::get( 'modulair-system', 'Donny5300\ModulairRouter\Controllers\System\SyncController@index' );

		Route::get( 'sync', 'Donny5300\ModulairRouter\Controllers\System\SyncController@sync' );
		Route::get( 'sync/dev', 'Donny5300\ModulairRouter\Controllers\System\SyncController@dev' );
		Route::get( 'sync/live', 'Donny5300\ModulairRouter\Controllers\System\SyncController@live' );
		Route::post( 'sync/live', 'Donny5300\ModulairRouter\Controllers\System\SyncController@doLiveSync' );

		Route::get( 'mapping', 'Donny5300\ModulairRouter\Controllers\RoutesController@show' );
		Route::post( 'mapping', 'Donny5300\ModulairRouter\Controllers\RoutesController@show' );
		Route::get( 'css', 'Donny5300\ModulairRouter\Controllers\AssetController@css' );

	} );
