<?php namespace Donny5300\ModulairRouter\Controllers;

use App\Http\Controllers\Controller;

use Donny5300\ModulairRouter\Models;

use Donny5300\ModulairRouter\Models\AppController;
use Donny5300\ModulairRouter\TreeBuilder;
use Illuminate\Http\Request;

/**
 * Class AppControllersController
 *
 * @package Donny5300\Routing\Controllers
 */
class RoutesController extends Controller
{

	public function __construct( Models\AppNamespace $namespace, Models\AppModule $module, AppController $controller, Models\AppMethod $method )
	{
		$this->namespaceModel  = $namespace;
		$this->moduleModel     = $module;
		$this->controllerModel = $controller;
		$this->methodModel     = $method;
	}

	public function show( Request $request )
	{

		$tree = ( new TreeBuilder() )->getCachedCollection();

		return view( 'd5300.router::partials.mapping', compact( 'tree' ) );

	}
}