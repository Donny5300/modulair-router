<?php namespace Donny5300\ModulairRouter\Middleware;

use Donny5300\ModulairRouter\Models\AppMethod;
use Illuminate\Http\Request;

/**
 * Class BaseMiddleWare
 *
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
		$segments         = explode( '/', $uri );
		$filteredSegments = array_values( array_filter( $segments ) );

		$this->namespace  = snake_case( $filteredSegments[0] ) ?? false;
		$this->module     = snake_case( $filteredSegments[1] ) ?? false;
		$this->controller = $this->filter( $filteredSegments[2] ?? false );


		if( array_key_exists( 3, $filteredSegments ) )
		{
			$action = !is_uuid( $filteredSegments[3] ) ? $filteredSegments[3] : $filteredSegments[4];

			$this->action = $this->filter( $action );
		}
	}

	/**
	 * @param $value
	 * @return bool|string
	 */
	public function filter( $value )
	{
		if( $value )
		{
			return snake_case( strtok( $value, "?" ) );
		}

		return false;
	}
}