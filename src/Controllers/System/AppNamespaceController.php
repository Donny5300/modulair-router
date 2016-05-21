<?php namespace Donny5300\ModulairRouter\Controllers\System;

use Donny5300\ModulairRouter\FileRemover;
use Donny5300\ModulairRouter\Models;
use Donny5300\ModulairRouter\Models\AppNamespace;
use Donny5300\ModulairRouter\Requests\StoreNamespaceRequest;
use Donny5300\ModulairRouter\Requests\UpdateNamespaceRequest;
use League\Flysystem\File;

/**
 * Class AppNamespaceController
 *
 * @package Donny5300\Routing\Controllers\System
 */
class AppNamespaceController extends BaseController
{
	protected $viewPath = 'namespaces';

	/**
	 * AppNamespaceController constructor.
	 *
	 * @param AppNamespace $namespace
	 */
	public function __construct( AppNamespace $namespace )
	{
		parent::__construct();
		$this->dataModel = $namespace;
	}

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function index()
	{
		$namespaces = $this->dataModel->with( 'modules' )->get();

		return $this->output( 'index', compact( 'namespaces' ) );
	}

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function create()
	{
		return $this->output( 'create' );
	}

	/**
	 * @param StoreNamespaceRequest $request
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function store( StoreNamespaceRequest $request )
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
		$item = $this->dataModel->where( 'uuid', $uuid )->firstOrFail();

		return $this->output( 'edit', compact( 'item' ) );
	}

	/**
	 * @param UpdateNamespaceRequest $request
	 * @param                        $uuid
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function update( UpdateNamespaceRequest $request, $uuid )
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
		return ( new FileRemover( $item, File::class ) )->deleteNamespace();
	}
}