<?php namespace Donny5300\ModulairRouter\Controllers\System;

use Donny5300\ModulairRouter\Models;
use Donny5300\ModulairRouter\Models\AppController;
use Donny5300\ModulairRouter\Models\AppMethod;
use Donny5300\ModulairRouter\Models\AppModule;
use Donny5300\ModulairRouter\Models\AppNamespace;
use Donny5300\ModulairRouter\PathBuilder;
use Donny5300\ModulairRouter\RouteSynchroniser;
use Illuminate\Http\Request;

/**
 * Class SyncController
 *
 * @package Donny5300\ModulairRouter\Controllers\System
 */
class CleanUpController extends BaseController
{
	/**
	 * @var string
	 */
	public $viewPath = 'system';
	/**
	 * @var AppNamespace
	 */
	private $namespace;
	/**
	 * @var AppModule
	 */
	private $module;
	/**
	 * @var AppController
	 */
	private $controller;
	/**
	 * @var AppMethod
	 */
	private $method;

	/**
	 * SyncController constructor.
	 *
	 * @param AppNamespace  $namespace
	 * @param AppModule     $module
	 * @param AppController $controller
	 * @param AppMethod     $method
	 */
	public function __construct( AppNamespace $namespace, AppModule $module, AppController $controller, AppMethod $method )
	{
		parent::__construct();
		$this->namespace   = $namespace;
		$this->module      = $module;
		$this->controller  = $controller;
		$this->method      = $method;
		$this->pathBuilder = new PathBuilder;
	}

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function index()
	{
		return $this->output( 'clean_up', compact( 'namespaces', 'controllers', 'modules', 'methods' ) );
	}

	/**
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function deleteAll()
	{
		$this->namespace->onlyTrashed()->forceDelete();
		$this->module->onlyTrashed()->forceDelete();
		$this->controller->onlyTrashed()->forceDelete();
		$this->method->onlyTrashed()->forceDelete();

		return redirect()->back()->with( 'message', 'All trashed are deleted' );
	}

	/**
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function restoreAll()
	{
		$this->namespace->onlyTrashed()->restore();
		$this->module->onlyTrashed()->restore();
		$this->controller->onlyTrashed()->restore();
		$this->method->onlyTrashed()->restore();

		return redirect()->back()->with( 'message', 'All trashed are restored' );

	}


}