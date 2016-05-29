<?php namespace Donny5300\ModulairRouter\Middleware;

use Donny5300\ModulairRouter\Models\AppMethod;
use Illuminate\Http\Request;

/**
 * Class BaseMiddleWare
 *
 * @property bool|string guid
 * @package Donny5300\Routing\Middleware
 */
abstract class BaseMiddleWare
{
	/**
	 * @var
	 */
	public $namespace;
	/**
	 * @var
	 */
	public $module;
	/**
	 * @var
	 */
	public $controller;
	/**
	 * @var
	 */
	public $action = 'index';

	public $uuid;

	/**
	 * @var
	 */
	public $config;

	/**
	 * BaseMiddleWare constructor.
	 *
	 * @param Request   $request
	 * @param AppMethod $method
	 */
	public function __construct( Request $request, AppMethod $method )
	{
		$this->setSegments( $request->getRequestUri() );
		$this->methodModel = $method;
		$this->config      = config( 'modulair-router' );
	}

	/**
	 * @param $uri
	 */
	public function setSegments( $uri )
	{
		$request = app( 'request' );

		$this->namespace  = $request->segment( 1 );
		$this->module     = $request->segment( 2 );
		$this->controller = $request->segment( 3, 'index' );
		$this->action     = $this->guidOrAction( $request );
	}

	/**
	 * @param $request
	 * @return mixed
	 */
	public function guidOrAction( $request )
	{
		$segment = $request->segment( 4, 'index' );

		if( is_uuid( $segment ) )
		{
			$this->uuid = $segment;

			return $request->segment( 5, 'index' );
		}

		return $segment;

	}
}