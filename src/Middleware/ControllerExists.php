<?php namespace Donny5300\ModulairRouter\Middleware;

use Closure;
use Donny5300\ModulairRouter\Exceptions\ControllerNotFoundException;
use Donny5300\ModulairRouter\Exceptions\DatabaseRouteNotFoundException;
use Donny5300\ModulairRouter\Router;

/**
 * Class ControllerExists
 *
 * @package Donny5300\Routing\Middleware
 *
 * @todo add as middleware
 */
class ControllerExists extends BaseMiddleWare
{
	/**
	 * @param         $request
	 * @param Closure $next
	 * @param null    $guard
	 * @return mixed
	 * @throws ControllerNotFoundException
	 */
	public function handle( $request, Closure $next, $guard = null )
	{
		$namespace      = ucfirst( camel_case( $this->namespace ) );
		$module         = ucfirst( camel_case( $this->module ) );
		$controller     = ucfirst( camel_case( $this->controller ) );
		$appNamespace   = $this->config['template_builder']['app_namespace'];
		$controllerPath = str_replace( '/', '\\', $this->config['controller_path'] );
		$class          = $appNamespace . '\\' . $controllerPath . '\\' . $namespace . '\\' . $module . '\\' . $controller . 'Controller';

		if( !class_exists( $class ) )
		{
			throw new ControllerNotFoundException( $class, [
				'namespace'  => $namespace,
				'module'     => $module,
				'controller' => $controller
			] );
		}

		return $next( $request );
	}
}