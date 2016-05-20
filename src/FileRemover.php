<?php namespace Donny5300\ModulairRouter;

use Illuminate\Filesystem\Filesystem;

class FileRemover
{
	private $namespace;

	public function __construct( $object )
	{
		$this->fs     = new Filesystem();
		$this->config = config( 'modulair-router' );
		$this->object = $object;
	}

	public function deleteNamespace()
	{
		$dir = app_path( $this->config['controller_path'] . '/' . $this->object->title );

		if( $this->fs->deleteDirectory( $dir ) )
		{
			return true;
		}

		return false;
	}

	public function deleteModule()
	{
		$namespace = $this->object->appNamespace->title;

		$dir = app_path( $this->config['controller_path'] . '/' . $namespace . '/' . $this->object->title );

		if( $this->fs->deleteDirectory( $dir ) )
		{
			return true;
		}

		return false;
	}

	public function deleteController()
	{
		$module    = $this->object->module->title;
		$namespace = $this->object->module->appNamespace->title;
		$fileName  = $this->object->title . 'Controller.php';
		$file      = $this->getBasePath() . $namespace . '/' . $module . '/' . $fileName;

		if( $this->fs->isFile( $file ) )
		{
			return $this->fs->delete( $file );
		}

		return false;
	}

	public function setNamespace( $namespace )
	{
		$this->namespace = $namespace;
	}

	public function getBasePath()
	{
		return app_path( $this->config['controller_path'] . '/' );
	}
}
