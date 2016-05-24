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

	public $uuid;

	/**
	 * @var
	 */
	public $config;
	private $segments;

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
		$segments       = explode( '/', $uri );
		$this->segments = array_values( array_filter( $segments ) );

		$this->namespace  = $this->getSegment( 0 );
		$this->module     = $this->getSegment( 1 );
		$this->controller = $this->getSegment( 2 );
		$this->action     = $this->getSegment( 3 );
	}

	/**
	 * @param $value
	 * @return bool|string
	 */
	public function getSegment( $value )
	{
		if( array_key_exists( $value, $this->segments ) )
		{
			if( $value == 3 && is_uuid( $this->segments[$value] ) )
			{
				$this->uuid = $this->segments[$value];
				$value++;
			}

			return snake_case( strtok( $this->segments[$value], '?' ) );
		}

		return false;
	}
}