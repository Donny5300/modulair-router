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
			->get();


		return $items;
	}

	/**
	 * @param Request $request
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function sync( Request $request )
	{
		if( $request->get( 'path' ) || $this->config['merge_config']['default_path'] )
		{
			$syncPath = $request->get( 'path', $this->config['merge_config']['default_path'] );

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
			$liveRoutes = $this->pathBuilder->getFromEnvironment( $syncPath );
		}

		return $this->output( 'sync', compact( 'liveRoutes', 'devRoutes' ) );

	}

	/**
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function doSync( Request $request )
	{
		$routes  = $request->get( 'route', [ ] );
		$deletes = $request->get( 'is_deleted', [ ] );

		foreach( $routes as $key => $route )
		{
			( new RouteSynchroniser() )
				->setIsDeleted( $deletes, $key )
				->syncRoute( $route );
		}

		return redirect()->back()->with( 'message', 'Syncing complete!' );


	}
}