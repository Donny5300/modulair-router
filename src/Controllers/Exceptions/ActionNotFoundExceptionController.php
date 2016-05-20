<?php namespace Donny5300\ModulairRouter\Controllers\Exceptions;

use App\Http\Controllers\Controller;
use Donny5300\ModulairRouter\Models;
use Donny5300\ModulairRouter\Router;
use Illuminate\Console\AppNamespaceDetectorTrait;
use Illuminate\Filesystem\Filesystem;

/**
 * Class AppControllersController
 *
 * @package Donny5300\Routing\Controllers
 */
class ActionNotFoundExceptionController extends Controller
{
	use AppNamespaceDetectorTrait;
	/**
	 * @var Filesystem
	 */
	protected $fs;

	/**
	 * @var
	 */
	protected $controllerName;

	/**
	 * ExceptionController constructor.
	 *
	 * @param Filesystem $fs
	 */
	public function __construct( Filesystem $fs )
	{
		$this->fs     = $fs;
		$this->config = config( 'modulair-router' );
	}

	/**
	 * @param $message
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 * @throws \Exception
	 * @throws \Throwable
	 */
	public function missingAction( $message )
	{
		$view = view( 'd5300.router::exceptions.action_not_found', compact( 'message' ) )->render();

		return response( $view );
	}
}