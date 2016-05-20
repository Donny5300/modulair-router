<?php namespace Donny5300\ModulairRouter\Controllers\System;

use Donny5300\ModulairRouter\FileRemover;
use Donny5300\ModulairRouter\Models\AppController;
use Donny5300\ModulairRouter\Models\AppModule;
use Donny5300\ModulairRouter\Requests\StoreControllerRequest;
use Donny5300\ModulairRouter\Requests\UpdateControllerRequest;

/**
 * Class AppControllersController
 *
 * @package Donny5300\Routing\Controllers
 */
class AppControllersController extends BaseController
{
	protected $viewPath = 'controllers';

	/**
	 * AppControllersController constructor.
	 *
	 * @param AppController $controller
	 * @param AppModule     $module
	 */
	public function __construct( AppController $controller, AppModule $module )
	{
		parent::__construct();
		$this->dataModel = $controller;
		$this->moduleModel     = $module;
	}

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function index()
	{
		$controllers = $this->dataModel->with( 'module' )->get();

		return $this->output( 'index', compact( 'controllers' ) );
	}

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function create()
	{
		$modules = $this->moduleModel->lists( 'title', 'id' );

		return $this->output( 'create', compact( 'controllers', 'modules' ) );
	}

	/**
	 * @param StoreControllerRequest $request
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function store( StoreControllerRequest $request )
	{
		if( $this->dataModel->storeOrUpdate( $request ) )
		{
			return $this->backToIndex();
		}

		return $this->backWithFailed();
	}

	/**
	 * @param $uuid
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function edit( $uuid )
	{
		$item    = $this->dataModel->where( 'uuid', $uuid )->firstOrFail();
		$modules = $this->moduleModel->lists( 'title', 'id' );

		return $this->output( 'edit', compact( 'item', 'modules' ) );
	}

	/**
	 * @param UpdateControllerRequest $request
	 * @param                         $uuid
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function update( UpdateControllerRequest $request, $uuid )
	{
		if( $this->dataModel->storeOrUpdate( $request, $uuid ) )
		{
			return $this->backToIndex();
		}

		return $this->backWithFailed();
	}

	/**
	 * @param $item
	 * @return bool
	 */
	public function deleteFiles( $item )
	{
		return ( new FileRemover( $item) )->deleteController();
	}
}