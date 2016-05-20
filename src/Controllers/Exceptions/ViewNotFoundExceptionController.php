<?php namespace Donny5300\ModulairRouter\Controllers\Exceptions;

use Donny5300\ModulairRouter\Controllers\System\BaseController;
use Donny5300\ModulairRouter\Models;
use Illuminate\Console\AppNamespaceDetectorTrait;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;

/**
 * Class AppControllersController
 *
 * @package Donny5300\Routing\Controllers
 */
class ViewNotFoundExceptionController extends BaseController
{
	use AppNamespaceDetectorTrait;
	/**
	 * @var Filesystem
	 */
	protected $fs;

	/**
	 * @var string
	 */
	public $viewPath = 'exceptions';

	/**
	 * ExceptionController constructor.
	 *
	 * @param Filesystem $fs
	 */
	public function __construct( Filesystem $fs )
	{
		parent::__construct();
		$this->fs     = $fs;
		$this->config = config( 'modulair-router' );
	}

	/**
	 * @param $message
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 * @throws \Exception
	 * @throws \Throwable
	 */
	public function missingView( $message )
	{
		$paths = config( 'view.paths' );

		$view = $this->output( compact( 'message', 'paths' ) )->render();

		return response( $view );
	}

	/**
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function createMissingView( Request $request )
	{
		$path              = config( 'view.paths.0' );
		$chunks            = explode( '.', $request->view );
		$file              = last( $chunks );
		$fileWithExtension = $file . $this->config['view_extension'];
		unset( $chunks[count( $chunks ) - 1] );
		$fullPath = $path . '/' . implode( '/', $chunks );
		$template = $this->getTemplate( $file );

		if( !$this->fs->isDirectory( $fullPath ) )
		{
			$this->fs->makeDirectory( $fullPath, 0755, true, true );
		}

		if( !$this->fs->isFile( $fullPath . '/' . $fileWithExtension ) )
		{
			$this->fs->put( $fullPath . '/' . $fileWithExtension, $template );
		}

		return redirect()->back();
	}

	/**
	 * @param $view
	 * @return string
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
	public function getTemplate( $view )
	{
		if( array_key_exists( $view, $this->config['templates']['view'] ) && ( $template = $this->config['templates']['view'][$view] ) )
		{
			return $this->fs->get( $template );
		}

		return '';
	}
}