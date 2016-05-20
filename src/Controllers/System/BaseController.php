<?php namespace Donny5300\ModulairRouter\Controllers\System;

use App\Http\Controllers\Controller;
use Donny5300\ModulairRouter\FileRemover;
use Donny5300\ModulairRouter\Models;
use League\Flysystem\Filesystem;

/**
 * Class AppControllersController
 *
 * @package Donny5300\Routing\Controllers
 */
class BaseController extends Controller
{
	/**
	 * @var string
	 */
	protected $baseLink = 'd5300.router::';

	/**
	 * @var
	 */
	protected $viewPath;

	/**
	 * @var
	 */
	protected $dataModel;

	/**
	 * BaseController constructor.
	 */
	public function __construct()
	{
		$this->config = config( 'modulair-router' );
	}

	/**
	 * @param bool|false $view
	 * @param array      $data
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function output( $view = false, $data = [ ] )
	{
		$config = $this->config;

		return view( $this->buildPath( $view ), array_merge( $config, $data ) );
	}

	/**
	 * @param $view
	 * @return string
	 */
	public function buildPath( $view )
	{
		if( $config = $this->config['system_views'][$this->viewPath] )
		{
			return $config . '.' . $view;
		}

		return $this->baseLink . $this->viewPath . '.' . $view;
	}

	/**
	 * @return string
	 */
	public function getBaseLink()
	{
		return $this->baseLink;
	}

	/**
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function backWithFailed()
	{
		return redirect()->back()->with( 'message', 'Could not create item' );
	}

	/**
	 * @param null $message
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function backToIndex( $message = null )
	{
		$basePath = $this->config['system_routes']['base_path'];
		$path     = $this->config['system_routes'][$this->viewPath];
		$message  = is_null( $message ) ? 'Item saved' : 'Item deleted';

		return redirect()->to( $basePath . '/' . $path )->with( 'message', $message );
	}

	/**
	 * @param $uuid
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function destroy( $uuid )
	{
		$item = $this->dataModel->deleteWithRelations( $uuid );
		$this->deleteFiles( $item );

		return $this->backToIndex( 'deleted' );
	}
}