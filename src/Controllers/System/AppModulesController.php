<?php namespace Donny5300\ModulairRouter\Controllers\System;

use Donny5300\ModulairRouter\FileRemover;
use Donny5300\ModulairRouter\Models;
use Donny5300\ModulairRouter\Models\AppModule;
use Donny5300\ModulairRouter\Models\AppNamespace;
use Donny5300\ModulairRouter\Requests\StoreModuleRequest;
use Donny5300\ModulairRouter\Requests\UpdateModuleRequest;

/**
 * Class AppModulesController
 *
 * @package Donny5300\Routing\Controllers
 */
class AppModulesController extends BaseController
{
	/**
	 * @var
	 */
	protected $appModule;

	protected $viewPath = 'modules';

	/**
	 * AppModulesController constructor.
	 *
	 * @param AppModule    $module
	 * @param AppNamespace $namespace
	 */
	public function __construct( AppModule $module, AppNamespace $namespace )
	{
		parent::__construct();
		$this->dataModel      = $module;
		$this->namespaceModel = $namespace;
	}

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function index()
	{
		$modules = $this->dataModel->with( 'appNamespace' )->get();

		return $this->output( 'index', compact( 'modules' ) );
	}

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function create()
	{
		$namespaces = $this->namespaceModel->lists( 'title', 'id' );

		return $this->output( 'create', compact( 'namespaces' ) );
	}

	/**
	 * @param StoreModuleRequest $request
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function store( StoreModuleRequest $request )
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
		$item       = $this->dataModel->where( 'uuid', $uuid )->firstOrFail();
		$namespaces = $this->namespaceModel->lists( 'title', 'id' );

		return $this->output( 'edit', compact( 'item', 'namespaces' ) );
	}

	/**
	 * @param UpdateModuleRequest $request
	 * @param                     $uuid
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function update( UpdateModuleRequest $request, $uuid )
	{
		if( $this->dataModel->storeOrUpdate( $request, $uuid ) )
		{
			return $this->backToIndex();
		}

		return $this->backWithFailed();
	}

	/**
	 * @param $object
	 * @return bool
	 */
	public function deleteFiles( $object )
	{
		return ( new FileRemover( $object ) )->deleteModule();
	}
}