<?php namespace Donny5300\ModulairRouter\Exceptions;

use \Exception;

class BaseException extends Exception
{
	public function __construct( $message, $arguments = [ ] )
	{
		parent::__construct( $message, 0, null );

		$this->arguments = $arguments;
	}

	public function getArguments()
	{
		return $this->arguments;
	}
}