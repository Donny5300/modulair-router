<?php namespace Donny5300\ModulairRouter\Controllers\System;

use Donny5300\ModulairRouter\Models;
use Donny5300\ModulairRouter\Models\AppNamespace;
use Donny5300\ModulairRouter\PathBuilder;
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
				$q->withTrashed()
					->select( [ 'id', 'title', 'deleted_at', 'namespace_id' ] );
			}, 'modules.controllers'         => function ( $q )
			{
				$q->withTrashed()
					->select( [ 'id', 'title', 'deleted_at', 'module_id' ] );

			}, 'modules.controllers.methods' => function ( $q )
			{
				$q->withTrashed()
					->select( [ 'id', 'title', 'deleted_at', 'controller_id' ] );
			}
			] )
			->get( [ 'id', 'title', 'deleted_at' ] );

		return $items;
//		return ( new PathBuilder() )->renderRouteList( $items );
	}

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function sync()
	{
		$data = $this->namespace
			->with( [ 'modules'              => function ( $q )
			{
				$q->withTrashed()
					->select( [ 'id', 'title', 'deleted_at', 'namespace_id' ] );
			}, 'modules.controllers'         => function ( $q )
			{
				$q->withTrashed()
					->select( [ 'id', 'title', 'deleted_at', 'module_id' ] );

			}, 'modules.controllers.methods' => function ( $q )
			{
				$q->withTrashed()
					->select( [ 'id', 'title', 'deleted_at', 'controller_id' ] );
			}
			] )->withTrashed()->get();

		$devRoutes = $this->pathBuilder->renderRouteList( $data );
		$envRoutes = $this->pathBuilder->getFromEnvironment();

		return $this->output( 'sync', compact( 'devRoutes', 'envRoutes' ) );
	}
}