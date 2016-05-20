<?php namespace Donny5300\ModulairRouter\Exceptions;

use Exception;

/**
 * Class ControllerNotFoundException
 *
 * @package Donny5300\Routing\Exceptions
 */
class ControllerNotFoundException extends Exception
{

	/**
	 * ControllerNotFoundException constructor.
	 *
	 * @param string $message
	 * @param array  $arguments
	 */
	public function __construct( $message, $arguments = [ ] )
	{
		parent::__construct( $message, 0, null );

		$this->arguments = $arguments;
	}

	/**
	 * @return array
	 */
	public function getArguments()
	{
		return $this->arguments;
	}
}