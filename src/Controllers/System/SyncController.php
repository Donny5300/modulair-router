<?php namespace Donny5300\ModulairRouter\Controllers\System;

use Donny5300\ModulairRouter\Models;
use Donny5300\ModulairRouter\Models\AppNamespace;
use Donny5300\ModulairRouter\PathBuilder;
use Donny5300\ModulairRouter\RouteSynchroniser;
use Illuminate\Http\Request;
use Symfony\Component\Finder\Exception\AccessDeniedException;

/**
 * Class SyncController
 *
 * @package Donny5300\ModulairRouter\Controllers\System
 */
class SyncController extends BaseController
{

	/**
	 * @var string
	 */
	public $viewPath = 'system';

	/**
	 * SyncController constructor.
	 *
	 * @param AppNamespace $namespace
	 */
	public function __construct( AppNamespace $namespace )
	{
		parent::__construct();
		$this->namespace   = $namespace;
		$this->pathBuilder = new PathBuilder;
	}

	/**
	 * @param Request $request
	 * @return \Illuminate\Database\Eloquent\Collection|static[]
	 */
	public function index( Request $request )
	{
		$items = $this->namespace
			->withTrashed()
			->with( [ 'modules'              => function ( $q )
			{
				$q->withTrashed();
			}, 'modules.controllers'         => function ( $q )
			{
				$q->withTrashed();

			}, 'modules.controllers.methods' => function ( $q )
			{
				$q->withTrashed();
			}
			] )
			->get(  );


		return $items;
	}

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function sync()
	{
		return $this->output( 'sync', compact( 'devRoutes', 'envRoutes', 'routes' ) );
	}

	public function dev()
	{
		return $this->output( 'dev', compact( 'devRoutes', 'envRoutes', 'routes' ) );
	}

	public function live()
	{
		$data = $this->namespace
			->with( [ 'modules'              => function ( $q )
			{
				$q->withTrashed();
			}, 'modules.controllers'         => function ( $q )
			{
				$q->withTrashed();

			}, 'modules.controllers.methods' => function ( $q )
			{
				$q->withTrashed();
			}
			] )
			->withTrashed()
			->get();

		$devRoutes  = $this->pathBuilder->renderRouteList( $data );
		$liveRoutes = $this->pathBuilder->getFromEnvironment();

		return $this->output( 'live', compact( 'liveRoutes', 'devRoutes' ) );
	}

	public function doLiveSync( Request $request )
	{
		$syncer  = new RouteSynchroniser();
		$routes  = $request->get( 'route', [ ] );
		$deletes = $request->get( 'is_deleted', [ ] );

		foreach( $routes as $key => $route )
		{
			$syncer
				->setIsDeleted( $deletes, $key )
				->syncRoute( $route );
		}


	}
}