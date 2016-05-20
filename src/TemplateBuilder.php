<?php namespace Donny5300\ModulairRouter;

use Donny5300\ModulairRouter\Exceptions\ControllerBuilderClassNotFoundException;
use Illuminate\Contracts\Filesystem\Filesystem;

/**
 * Class TemplateBuilder
 *
 * @package Donny5300\Routing
 */
class TemplateBuilder
{
	/**
	 * @var
	 */
	private $controller;
	/**
	 * @var
	 */
	private $namespace;
	/**
	 * @var
	 */
	private $template;
	/**
	 * @var
	 */
	private $baseController;
	/**
	 * @var
	 */
	private $className;
	/**
	 * @var
	 */
	private $appNamespace;
	/**
	 * @var array
	 */
	private $classUses = [ ];
	/**
	 * @var
	 */
	private $classValidation;
	private $module;

	/**
	 * TemplateBuilder constructor.
	 *
	 * @param $config
	 */
	public function __construct( $config )
	{
		$this->fs     = app( Filesystem::class );
		$this->config = $config;
	}

	/**
	 *
	 */
	public function buildController()
	{


		$replace = $this->setReplaceAbleArray();
		$uses    = $this->getUses();

		foreach( $replace as $replaceKey => $replaceValue )
		{
			$this->template = str_replace( '$$' . $replaceKey . '$$', $replaceValue, $this->template );
		}

		$this->template = str_replace( '$$uses$$', implode( PHP_EOL, $uses ), $this->template );

		return $this->template;

	}

	/**
	 * @param $uses
	 * @return $this
	 */
	public function setUses( $uses )
	{
		if( is_array( $uses ) )
		{
			$this->classUses = array_merge( $this->classUses, $uses );
		}
		else
		{
			$this->classUses[] = $uses;
		}

		return $this;
	}

	/**
	 * @param $namespace
	 * @return $this
	 */
	public function setNamespace( $namespace )
	{
		$this->namespace = $namespace;

		return $this;
	}

	/**
	 * @param $template
	 * @return $this
	 */
	public function setTemplate( $template )
	{
		$this->template = $template;

		return $this;
	}

	/**
	 * @param $baseController
	 * @return $this
	 */
	public function setBaseController( $baseController )
	{
		$this->baseController = $baseController;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getBaseController()
	{

		if( $this->baseController )
		{
			return $this->baseController;
		}

		return $this->config['template_builder']['base_controller'];
	}

	/**
	 * @param $className
	 * @return $this
	 */
	public function setClassName( $className )
	{
		$this->className = $className;

		return $this;
	}


	/**
	 * @return array
	 */
	public function setReplaceAbleArray()
	{
		$data = [
			'namespace'       => $this->namespace . ';',
			'base_controller' => $this->getBaseControllerName(),
			'class_name'      => $this->className
		];

		return $data;
	}


	/**
	 * @return string
	 */
	public function mergeNamespaces()
	{
		return $this->namespace;
	}

	/**
	 * @return mixed
	 */
	public function getBaseControllerName()
	{
		$basecontroller = explode( '\\', $this->getBaseController() );

		return last( $basecontroller );
	}

	/**
	 * @return array
	 * @throws ControllerBuilderClassNotFoundException
	 */
	public function getUses()
	{
		$classes = array_merge( $this->classUses, [ $this->getBaseController() ] );

		foreach( $classes as &$class )
		{
			if( $this->getClassValidation() )
			{
				if( !class_exists( $class ) )
				{
					throw new ControllerBuilderClassNotFoundException( 'Class [' . $class . '] not found!' );
				}
			}
			$class = 'use ' . $class . ';';
		}

		return $classes;

	}

	/**
	 * @param $classValidation
	 * @return $this
	 */
	public function setClassValidation( $classValidation )
	{
		$this->classValidation = $classValidation;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getClassValidation()
	{
		if( $this->classValidation )
		{
			return $this->classValidation;
		}

		return $this->config['template_builder']['validate_classes'];
	}

}