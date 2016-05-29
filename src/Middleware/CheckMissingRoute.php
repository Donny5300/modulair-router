<?php namespace Donny5300\ModulairRouter\Middleware;

use Closure;
use Donny5300\ModulairRouter\Exceptions\DatabaseRouteNotFoundException;

class CheckMissingRoute extends BaseMiddleWare
{
	/**
	 * @param         $request
	 * @param Closure $next
	 * @param null    $guard
	 * @return mixed
	 * @throws DatabaseRouteNotFoundException
	 */
	public function handle( $request, Closure $next, $guard = null )
	{

		if( $request->get( 'middleware' ) && env('APP_DEBUG', false))
		{
			return $next( $request );
		}

		$result = $this->methodModel
				->where( 'title', studly_case( $this->action ) )
				->whereHas( 'controller', function ( $q )
				{
					$q->whereTitle( $this->controller );
				} )
				->whereHas( 'controller.module', function ( $q )
				{
					$q->whereTitle( $this->module );
				} )
				->whereHas( 'controller.module.appNamespace', function ( $q )
				{
					$q->whereTitle( $this->namespace );
				} )
				->first();

		/**
		 * @todo sync with live environment on vice versa
		 */
		if( !$result )
		{
			throw new DatabaseRouteNotFoundException(
					'Route [' . $this->namespace . '/' . $this->module . '/' . $this->controller . '/' . $this->action . '] not found!'
					, [
							'namespace'  => $this->namespace,
							'module'     => $this->module,
							'controller' => $this->controller,
							'action'     => $this->action,
					]
			);
		}


		return $next( $request );
	}
}