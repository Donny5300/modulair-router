<?php namespace Donny5300\ModulairRouter\Middleware;

use Closure;
use Donny5300\ModulairRouter\Exceptions\ControllerNotFoundException;
use Donny5300\ModulairRouter\Exceptions\DatabaseRouteNotFoundException;
use Donny5300\ModulairRouter\Exceptions\DevelopmentModeException;
use Donny5300\ModulairRouter\Router;


class DevelopmentMode extends BaseMiddleWare
{
	/**
	 * @param         $request
	 * @param Closure $next
	 * @param null    $guard
	 * @return mixed
	 * @throws DevelopmentModeException
	 */
	public function handle( $request, Closure $next, $guard = null )
	{
		if( !$this->config['debug'] )
		{
			throw new DevelopmentModeException( 'This action is only available when debug mode is off!' );
		}

		return $next( $request );
	}
}