<?php namespace Donny5300\ModulairRouter\Controllers\Exceptions;

use App\Http\Controllers\Controller;
use Donny5300\ModulairRouter\Controllers\System\BaseController;
use Donny5300\ModulairRouter\Models;
use Donny5300\ModulairRouter\TemplateBuilder;
use Illuminate\Console\AppNamespaceDetectorTrait;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;

/**
 * Class AppControllersController
 *
 * @package Donny5300\Routing\Controllers
 */
class ControllerNotFoundExceptionController extends BaseController
{
	use AppNamespaceDetectorTrait;
	/**
	 * @var Filesystem
	 */
	protected $fs;

	protected $viewPath = 'exceptions';

	/**
	 * ExceptionController constructor.
	 *
	 * @param Filesystem $fs
	 */
	public function __construct( Filesystem $fs )
	{
		$this->fs             = $fs;
		$this->config         = config( 'modulair-router' );
		$this->controllerPath = $this->config['controller_path'];
		$this->builder        = new TemplateBuilder( $this->config );
	}

	/**
	 * @param $message
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 * @throws \Exception
	 * @throws \Throwable
	 */
	public function missingController( $message )
	{
		$arguments  = $message->getArguments();
		$namespace  = studly_case( $arguments['namespace'] );
		$controller = studly_case( $arguments['controller'] );
		$module     = studly_case( $arguments['module'] );
		$fileExists = $this->fileExists( $namespace, $module, $controller );

		$view = $this->output( 'controller_not_found', compact( 'module', 'message', 'fileExists', 'namespace', 'controller' ) )->render();

		return response( $view );
	}

	/**
	 * @param Request $request
	 * @return mixed
	 * @throws \Exception
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
	public function createMissingController( Request $request )
	{
		$namespace  = $request->namespace;
		$controller = $request->controller;
		$module     = $request->module;

		//Get controllers folder
		$controllersDir = $this->getMainControllerPath();

		//Get controller path for new controller
		$newControllerDir = $this->getControllerPath( $namespace, $module );

		//Get controller namespace
		$controllerNamespace = $this->getMainControllerNamespace();

		//Get namespace for the new controller
		$newControllerNamespace = $this->getControllerNamespace( $namespace, $module );

		//Get the file
		$file = $this->getFileLocation( $namespace, $module, $controller );

		$controllerClass = studly_case( $controller . 'Controller' );

		//Check if file exists
		$fileExists = $this->fileExists( $file );

		//Check if is dir
		if( !$this->fs->isDirectory( $newControllerDir ) )
		{
			$this->fs->makeDirectory( $newControllerDir, 0755, true, true );

		}

		//If file doesnt exist, create the file
		if( !$fileExists )
		{
			$template = $this->builder
				->setNamespace( $newControllerNamespace )
				->setTemplate( $this->fs->get( $this->config['templates']['controller'] ) )
				->setClassName( studly_case( $controllerClass ) )
				->setUses( Request::class )
				->buildController();

			//Store the file
			$this->fs->put( $file, $template );

			//Call the created controller

			return app( $newControllerNamespace . '\\' . $controllerClass )->index();
		}

		throw new \Exception( 'File exists! I don\'t want to override it!' );
	}

	/**
	 * @param $file
	 * @return bool
	 */
	public function fileExists( $file )
	{
		return $this->fs->exists( $file );
	}

	/**
	 * @param $namespace
	 * @param $module
	 * @param $controller
	 * @return string
	 */
	public function getFileLocation( $namespace, $module, $controller )
	{
		return $this->getMainControllerPath() . '/' . $namespace . '/' . $module . '/' . studly_case( $controller ) . 'Controller.php';
	}

	/**
	 * @return string
	 */
	public function getMainControllerPath()
	{
		return app_path( $this->config['controller_path'] );
	}

	/**
	 * @param $namespace
	 * @param $module
	 * @return string
	 */
	public function getControllerPath( $namespace, $module )
	{
		return $this->getMainControllerPath() . '/' . $namespace . '/' . $module;
	}

	/**
	 * @return mixed
	 */
	public function getNamespace()
	{
		return $this->config['template_builder']['app_namespace'];
	}

	/**
	 * @return mixed
	 */
	public function getMainControllerNamespace()
	{
		$namespace = $this->getAppNamespace() . $this->config['controller_path'];

		return str_replace( '/', '\\', $namespace );
	}

	/**
	 * @param $namespace
	 * @param $module
	 * @return string
	 */
	public function getControllerNamespace( $namespace, $module )
	{
		return $this->getMainControllerNamespace() . '\\' . $namespace . '\\' . $module;
	}

}