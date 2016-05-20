<?php namespace Donny5300\ModulairRouter\Controllers\Exceptions\Database;

use Donny5300\ModulairRouter\Controllers\System\BaseController;
use Donny5300\ModulairRouter\Models\AppController;
use Donny5300\ModulairRouter\Models\AppMethod;
use Donny5300\ModulairRouter\Models\AppModule;
use Donny5300\ModulairRouter\Models\AppNamespace;
use Illuminate\Http\Request;

class RouteException extends BaseController
{

	/**
	 * @var string
	 */
	public $viewPath = 'exceptions';

	/**
	 * RouteException constructor.
	 *
	 * @param AppNamespace  $namespace
	 * @param AppModule     $module
	 * @param AppController $controller
	 * @param AppMethod     $method
	 */
	public function __construct( AppNamespace $namespace, AppModule $module, AppController $controller, AppMethod $method )
	{
		parent::__construct();

		$this->namespace  = $namespace;
		$this->module     = $module;
		$this->controller = $controller;
		$this->method     = $method;
	}

	/**
	 * @param $exception
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	public function missingRoute( $exception )
	{
		$routeParams = $exception->getArguments();
		$namespace   = $routeParams['namespace'];
		$controller  = $routeParams['controller'];
		$module      = $routeParams['module'];
		$method      = $routeParams['action'];

		$hasNamespace = $this->namespace
			->where( 'title', $routeParams['namespace'] )
			->first();

		if( $hasNamespace )
		{
			$hasModule = $this->module
				->where( 'namespace_id', $hasNamespace->id )
				->where( 'title', $routeParams['module'] )
				->first();
		}

		if( isset( $hasModule ) )
		{
			$hasController = $this->controller
				->where( 'module_id', $hasModule->id )
				->where( 'title', $routeParams['controller'] )
				->first();
		}

		if( isset( $hasController ) )
		{
			$hasMethod = $this->method
				->where( 'controller_id', $hasController->id ?? false )
				->where( 'title', $routeParams['action'] )
				->first();
		}

		$output = $this->output( 'route_not_found', compact( 'hasNamespace', 'hasModule', 'hasController', 'hasMethod', 'exception', 'routeParams', 'namespace', 'module', 'controller', 'method' ) );

		return response( $output );
	}

	/**
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function storeRoute( Request $request )
	{
		$namespace  = $this->namespace->storeFromException( $request );
		$module     = $this->module->storeFromException( $request, $namespace->id );
		$controller = $this->controller->storeFromException( $request, $module->id );
		$action     = $this->method->storeFromException( $request, $controller->id );

		return redirect()->to( $namespace->title . '/' . $module->title . '/' . $controller->title . '/' . $action->title );
	}

}
