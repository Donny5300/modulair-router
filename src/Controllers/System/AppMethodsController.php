<?php namespace Donny5300\ModulairRouter\Controllers\System;

use Donny5300\ModulairRouter\Models\AppController;
use Donny5300\ModulairRouter\Models\AppMethod;
use Donny5300\ModulairRouter\Requests\StoreMethodRequest;
use Donny5300\ModulairRouter\Requests\UpdateMethodRequest;

/**
 * Class AppControllersController
 *
 * @package Donny5300\Routing\Controllers
 */
class AppMethodsController extends BaseController
{
	/**
	 * @var string
	 */
	protected $viewPath = 'methods';

	/**
	 * AppMethodsController constructor.
	 *
	 * @param AppMethod     $method
	 * @param AppController $controller
	 */
	public function __construct( AppMethod $method, AppController $controller )
	{
		parent::__construct();
		$this->methodModel     = $method;
		$this->controllerModel = $controller;
	}

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function index()
	{
		$methods = $this->methodModel->with( 'controller' )->get();

		return $this->output( 'index', compact( 'methods' ) );
	}

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function create()
	{

		$requestMethods = $this->config['request_methods'];

		$controllers = $this->controllerModel->lists( 'title', 'id' );

		return $this->output( 'create', compact( 'controllers', 'requestMethods' ) );
	}

	/**
	 * @param StoreMethodRequest $request
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function store( StoreMethodRequest $request )
	{
		if( $this->methodModel->storeOrUpdate( $request ) )
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
		$item           = $this->methodModel->where( 'uuid', $uuid )->firstOrFail();
		$controllers    = $this->controllerModel->lists( 'title', 'id' );
		$requestMethods = $this->config['request_methods'];

		return $this->output( 'edit', compact( 'item', 'controllers', 'requestMethods' ) );
	}

	/**
	 * @param UpdateMethodRequest $request
	 * @param                     $uuid
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function update( UpdateMethodRequest $request, $uuid )
	{
		if( $this->methodModel->storeOrUpdate( $request, $uuid ) )
		{
			return $this->backToIndex();
		}

		return $this->backWithFailed();
	}

	/**
	 * @param $guid
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function destroy( $guid )
	{
		$this->methodModel->where( 'uuid', $guid )->delete();

		return $this->backWithFailed( 'deleted' );
	}
}