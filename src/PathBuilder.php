<?php namespace Donny5300\ModulairRouter;

use Donny5300\ModulairRouter\Models\AppController;
use Donny5300\ModulairRouter\Models\AppMethod;
use Donny5300\ModulairRouter\Models\AppModule;
use Donny5300\ModulairRouter\Models\AppNamespace;


/**
 * Class TreeBuilder
 *
 * @package Donny5300\Routing
 */
class PathBuilder
{
	/**
	 * TreeBuilder constructor.
	 */
	public function __construct()
	{
		$this->namespace  = app( AppNamespace::class );
		$this->module     = app( AppModule::class );
		$this->controller = app( AppController::class );
		$this->method     = app( AppMethod::class );
		$this->cache      = app( 'cache' );
	}

	/**
	 *
	 */
	public function clearCachedCollection()
	{
		$this->cache->forget( 'system.router_tree' );

	}

	/**
	 * @param $collection
	 */
	public function setCachedCollection( $collection )
	{
		return $this->cache->forever( 'system_router_tree', $collection );
	}

	/**
	 * Gets all the routes from the cache
	 *
	 * @return mixed
	 */
	public function getCachedCollection()
	{
		if( $this->cache->has( 'system_router_tree' ) )
		{
			return $this->cache->get( 'system_router_tree' );
		}

		return $this->build();
	}

	/**
	 * @return mixed
	 */
	public function build()
	{
		$results = $this->namespace->with( [
			'modules.controllers.methods'
		] )->get();

		$this->setCachedCollection( $results );

		return $results;

	}

	/**
	 * @param $namespaces
	 * @return array
	 */
	public function renderRouteList( $namespaces )
	{
		$routes = [ ];

		foreach( $namespaces as $namespace )
		{
			$routes[$namespace['title']] = $this->extractOptions( $namespace );
			foreach( $namespace['modules'] as $module )
			{
				$routes[$namespace['title'] . '.' . $module['title']] = $this->extractOptions( $module );
				foreach( $module['controllers'] as $controller )
				{
					$routes[$namespace['title'] . '.' . $module['title'] . '.' . $controller['title']] = $this->extractOptions( $controller );
					foreach( $controller['methods'] as $action )
					{
						$routes[$namespace['title'] . '.' . $module['title'] . '.' . $controller['title'] . '.' . $action['title']] = $this->extractOptions( $action );
					}
				}
			}
		}

		return $routes;
	}

	/**
	 * Extract all the routes from the Live environment
	 *
	 * @param null $url
	 * @return array
	 */
	public function getFromEnvironment( $url = null )
	{
		$contents = file_get_contents( 'http://playground.dev/system/modulair-system' );

		return $this->renderRouteList( json_decode( $contents, true ) );

	}

	/**
	 * Gets the extra options
	 *
	 * @param $item
	 * @return array
	 */
	private function extractOptions( $item )
	{
		$options = [
			'is_deleted'     => !is_null( $item['deleted_at'] ),
			'is_online'      => ( $item['is_online'] ),
			'developer_only' => ( $item['developer_only'] ),
			'description'    => $item['description'],
		];

		if( array_key_exists( 'method', $item ) )
		{
			$options['method'] = $item['method'];
		}

		if( array_key_exists( 'is_visible', $item ) )
		{
			$options['is_visible'] = ( $item['is_visible'] );
		}

		return $options;
	}
}