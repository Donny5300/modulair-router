<?php namespace Donny5300\ModulairRouter;

use Donny5300\ModulairRouter\Models\AppController;
use Donny5300\ModulairRouter\Models\AppMethod;
use Donny5300\ModulairRouter\Models\AppModule;
use Donny5300\ModulairRouter\Models\AppNamespace;
use Illuminate\Filesystem\Filesystem;
use stdClass;

/**
 * Class RouteSynchroniser
 *
 * @package Donny5300\ModulairRouter
 */
class RouteSynchroniser
{
	/**
	 * @var
	 */
	private $route;
	/**
	 * @var
	 */
	private $deleted;

	/**
	 * RouteSynchroniser constructor.
	 */
	public function __construct()
	{
		$this->namespace  = app( AppNamespace::class );
		$this->module     = app( AppModule::class );
		$this->controller = app( AppController::class );
		$this->method     = app( AppMethod::class );
	}

	/**
	 * @param $route
	 * @return $this
	 */
	public function syncRoute( $route )
	{
		$chunks = $this->stripRoute( $route );

		foreach( $chunks as $key => $value )
		{
			switch( $key )
			{
				case 0:
					$this->namespace = $this->createOrDeleteNamespace( $value );
					break;
				case 1:
					$this->module = $this->createOrDeleteModule( $value, $this->namespace->id );
					break;

				case 2:
					$this->controller = $this->createOrDeleteController( $value, $this->module->id );
					break;
				case 3:
					$this->createOrDeleteAction( $value, $this->controller->id );
					break;
			}
		}

		dd( 'File: ' . __FILE__, 'Line: ' . __LINE__, $chunks );

		return $this;

	}

	/**
	 * @param $route
	 * @return array
	 */
	public function stripRoute( $route )
	{
		return explode( '.', $route );
	}

	/**
	 * @param $deleted
	 * @param $key
	 * @return $this
	 */
	public function setIsDeleted( $deleted, $key )
	{
		if( array_key_exists( $key, $deleted ) )
		{
			$this->deleted = $deleted[$key] ? true : false;

			return $this;
		}

		unset( $this->deleted );

		return $this;
	}

	/**
	 * @param $title
	 * @return bool
	 */
	public function createOrDeleteNamespace( $title )
	{

		if( isset( $this->deleted ) )
		{
			if( $this->deleted )
			{
				return $this->deleteNamespace( $title );
			}

			return $this->restoreNamespace( $title );
		}

		$item            = new stdClass();
		$item->namespace = $title;

		return $this->namespace->storeFromException( $item );
	}

	/**
	 * @param $title
	 * @return bool
	 */
	private function deleteNamespace( $title )
	{
		$item = $this->namespace->withTrashed()->where( 'title', $title )->first();

		if( $item->delete() )
		{
			
			return $item;
		}

		return false;
	}

	/**
	 * @param $namespace
	 * @return bool
	 */
	public function restoreNamespace( $namespace )
	{
		$item = $this->namespace->where( 'title', $namespace )->withTrashed()->first();

		if( $item->restore() )
		{
			return $item;
		}

		return false;
	}


	/**
	 * @param $module
	 * @param $namespaceId
	 * @return bool|stdClass
	 */
	private function createOrDeleteModule( $module, $namespaceId )
	{
		if( isset( $this->deleted ) )
		{
			if( $this->deleted )
			{
				return $this->deleteModule( $module );
			}

			return $this->restoreModule( $module, $namespaceId );
		}

		$item        = new stdClass();
		$item->title = $module;

		return $this->module->storeFromException( $item );
	}


	/**
	 * @param $module
	 * @param $namespaceId
	 * @return bool
	 */
	public function restoreModule( $module, $namespaceId )
	{
		$item = $this->module
			->where( 'title', $module )
			->where( 'namespace_id', $namespaceId )
			->withTrashed()
			->first();

		if( $item->restore() )
		{
			return $item;
		}

		return false;
	}

	/**
	 * @param $title
	 * @return bool
	 */
	private function deleteModule( $title )
	{
		$item = $this->module->withTrashed()->where( 'title', $title )->first();

		if( $item->delete() )
		{
			return $item;
		}

		return false;
	}

	/**
	 * @param $controller
	 * @param $namespaceId
	 * @return bool|stdClass
	 */
	private function createOrDeleteController( $controller, $namespaceId )
	{
		if( isset( $this->deleted ) )
		{
			if( $this->deleted )
			{
				return $this->deleteController( $controller );
			}

			return $this->restoreController( $controller, $namespaceId );
		}

		$item        = new stdClass();
		$item->title = $controller;

		return $this->controller->storeFromException( $item );
	}


	/**
	 * @param $controller
	 * @param $moduleId
	 * @return bool
	 */
	public function restoreController( $controller, $moduleId )
	{
		$item = $this->controller
			->where( 'title', $controller )
			->where( 'module_id', $moduleId )
			->withTrashed()
			->first();

		if( $item->restore() )
		{
			return $item;
		}

		return false;
	}

	/**
	 * @param $title
	 * @return bool
	 */
	private function deleteController( $title )
	{
		$item = $this->controller->withTrashed()->where( 'title', $title )->first();

		if( $item->delete() )
		{
			return $item;
		}

		return false;
	}

	/**
	 * @param $action
	 * @param $namespaceId
	 * @return bool|stdClass
	 */
	private function createOrDeleteAction( $action, $namespaceId )
	{
		if( isset( $this->deleted ) )
		{
			if( $this->deleted )
			{
				return $this->deleteAction( $action );
			}

			return $this->restoreAction( $action, $namespaceId );
		}

		$item        = new stdClass();
		$item->title = $action;

		return $this->method->storeFromException( $item );
	}


	/**
	 * @param $action
	 * @param $moduleId
	 * @return bool
	 */
	public function restoreAction( $action, $moduleId )
	{
		$item = $this->method
			->where( 'title', $action )
			->where( 'module_id', $moduleId )
			->withTrashed()
			->first();

		if( $item->restore() )
		{
			return $item;
		}

		return false;
	}

	/**
	 * @param $title
	 * @return bool
	 */
	private function deleteAction( $title )
	{
		$item = $this->method->withTrashed()->where( 'title', $title )->first();

		if( $item->delete() )
		{
			return $item;
		}

		return false;
	}


}
