<?php namespace Donny5300\ModulairRouter;

use Donny5300\ModulairRouter\Middleware\DevelopmentMode;
use Illuminate\Routing\Router as IlluminateRouter;
use Donny5300\ModulairRouter\Middleware\CheckMissingRoute;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * @var
	 */
	private $config;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$configPath = __DIR__ . '/../config/modulair-router.php';
		$this->mergeConfigFrom( $configPath, 'modulair-router' );

		//dyamic-router


		include __DIR__ . '/Helpers.php';
		include __DIR__ . '/routes.php';
	}

	/**
	 * @param IlluminateRouter $router
	 */
	public function boot( IlluminateRouter $router )
	{
		$this->config = config( 'modulair-router' );

		$assetsPath = __DIR__ . '/../assets';
		$migration  = __DIR__ . '/../migrations';
		$configPath = __DIR__ . '/../config/modulair-router.php';

		$this->publishes( [ $configPath => $this->getConfigPath() ], 'config' );
		$this->publishes( [ $assetsPath => $this->getAssetsPath() ], 'assets' );
		$this->publishes( [ $migration => $this->getMigrationsPath() ], 'migrations' );

		$this->loadViewsFrom( __DIR__ . '/Resources/Views/', 'd5300.router' );

		$router->middlewareGroup( 'CheckMissingRoutes', [
			CheckMissingRoute::class,
		] );

		$router->middleware( 'routeDevelopmentMode', DevelopmentMode::class );
	}

	/**
	 * Get the active router.
	 *
	 * @return Router
	 */
	protected function getRouter()
	{
		return $this->app['router'];
	}

	/**
	 * @return string
	 */
	public function getMigrationsPath()
	{
		return $this->app->databasePath() . '/migrations';
	}

	/**
	 * Get the config path
	 *
	 * @return string
	 */
	protected function getConfigPath()
	{
		return config_path( 'routing.php' );
	}

	/**
	 * @return string
	 */
	protected function getAssetsPath()
	{
		return public_path( 'assets' );
	}

	/**
	 * Publish the config file
	 *
	 * @param  string $configPath
	 */
	protected function publishConfig( $configPath )
	{
		$this->publishes( [ $configPath => config_path( 'routing.php' ) ], 'config' );
	}

	/**
	 * @param $middleware
	 */
	protected function registerMiddleware( $middleware )
	{
		$kernel = $this->app['Illuminate\Contracts\Http\Kernel'];
		$kernel->pushMiddleware( $middleware );
	}
}
