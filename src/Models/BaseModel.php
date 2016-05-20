<?php namespace Donny5300\ModulairRouter\Models;

use Donny5300\ModulairRouter\TreeBuilder;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{

	private static $methods = [ 'created', 'updated', 'deleted' ];

	public static function boot()
	{
		parent::boot();

		foreach( self::$methods as $method )
		{
			self::$method( function ()
			{
				( new TreeBuilder() )
					->build();
			} );
		}
	}

	public function scopeUuid( $q, $value )
	{
		return $q->where( 'uuid', $value );
	}

	public function setTitleAttribute( $value )
	{
		return $this->attributes['title'] = str_replace( ' ', '-', strtolower( $value ) );
	}
}