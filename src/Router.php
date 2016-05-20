<?php namespace Donny5300\ModulairRouter;

use Donny5300\ModulairRouter\Exceptions\ActionNotFoundException;
use Donny5300\ModulairRouter\Exceptions\ControllerNotFoundException;
use Illuminate\Routing\Router as IlluminateRouter;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Container\Container;
use Illuminate\Routing\RouteDependencyResolverTrait;


/**
 * Class Router
 *
 * @package App\Extenders
 */
class Router extends IlluminateRouter
{
	use RouteDependencyResolverTrait;
	/**
	 * @var
	 */
	private $appNamespace;
	/**
	 * @var
	 */
	private $namespace;
	/**
	 * @var
	 */
	private $module;
	/**
	 * @var
	 */
	private $controller;
	/**
	 * @var
	 */
	private $action;
	/**
	 * @var
	 */
	private $guid;

	/**
	 * @var
	 */
	private $breadcrumbs;
	/**
	 * @var
	 */
	private $config;
	/**
	 * @var
	 */
	private $method;
	/**
	 * @var
	 */
	private $controllerClass;


	/**
	 * Router constructor.
	 *
	 * @param Dispatcher     $events
	 * @param Container|null $container
	 */
	public function __construct( Dispatcher $events, Container $container = null )
	{
		parent::__construct( $events, $container );
	}

	/**
	 *
	 */
	public function dynamicResourceBindings()
	{
		$this->config = config( 'modulair-router' );


		$this->any( '{namespace}/{module?}/{controller?}/{guidOrAction?}/{action?}/{extra?}/{extra2?}',

			function ( $namespace, $module = null, $controller = 'index', $guidOrAction = null, $action = 'index', $extra = null, $extra2 = null )
			{
				//Capture the current request
				$request = $this->getCurrentRequest();
				//Set the namespace
				$this->namespace = studly_case( $namespace );
				//Set the module
				$this->module = studly_case( $module );
				//Set the controller
				$this->controller = $controller;
				//Set controller class
				$this->controllerClass = studly_case( $controller ) . 'Controller';
				//Set the controller action
				$this->action = $this->getControllerMethod( $guidOrAction, camel_case( $action ) );
				//Set the request method
				$this->method = $request->method();

				//Set the class
				$class = $this->getControllerClass();


				// Is an action
				$this->setAction( $this->method, $action );

				$this->validateAction( $class, $this->action );

				if( $this->guid )
				{
					return call_user_func_array(
						[ $class, $action ], $this->resolveClassMethodDependencies( [ $guid, $extra, $extra2 ], $class, $action )
					);
				}

				return $this->callWithDependencies( $class, $action );
			}
		);

	}

	/**
	 * @return \Illuminate\Foundation\Application|mixed
	 * @throws ControllerNotFoundException
	 */
	private function getControllerClass()
	{
		$modulePath     = $this->namespace . '\\' . $this->module . '\\' . $this->controllerClass;
		$appNamespace   = $this->getAppNamespace();
		$controllerPath = $this->getControllerPath();
		$class          = str_replace( '/', '\\', $appNamespace . '/' . $controllerPath . '/' . $modulePath );

		if( !class_exists( $class ) )
		{
			throw new ControllerNotFoundException( $class, [
				'namespace'  => $this->namespace,
				'module'     => $this->module,
				'controller' => $this->controller
			] );
		}


		return app( $class );
	}

	/**
	 * @param $guidOrAction
	 * @param $action
	 * @return mixed
	 */
	public function getControllerMethod( $guidOrAction, $action )
	{
		if( is_uuid( $guidOrAction ) || is_null( $guidOrAction ) )
		{
			$this->guid = $guidOrAction;

			return $action;
		}

		return $guidOrAction;
	}

	/**
	 * @return mixed
	 */
	public function getAppNamespace()
	{
		return $this->config['template_builder']['app_namespace'];
	}

	/**
	 * @return mixed
	 */
	public function getControllerPath()
	{
		return $this->config['controller_path'];
	}

	/**
	 * @param $class
	 * @param $method
	 * @throws ActionNotFoundException
	 */
	public function validateAction( $class, $method )
	{
		if( !method_exists( $class, camel_case( $method ) ) )
		{
			throw new ActionNotFoundException( $method );
		}
	}

	/**
	 * @param $method
	 * @param $action
	 */
	public function setAction( $method, $action )
	{
		// POST create => store
		if( $method == 'POST' && $action == 'create' )
		{
			$this->action = 'store';
		}
		// PATCH edit => update
		else if( $method == 'PATCH' && $action == 'edit' )
		{
			$this->action = 'update';
		}
		elseif( $method == 'DELETE' )
		{
			$this->action = 'destroy';
		}
	}
}