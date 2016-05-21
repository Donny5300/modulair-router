<?php namespace Donny5300\ModulairRouter\Models;

use Donny5300\ModulairRouter\PathBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaseModel extends Model
{

	use SoftDeletes;
	
	private static $methods = [ 'created', 'updated', 'deleted' ];

	public static function boot()
	{
		parent::boot();

		foreach( self::$methods as $method )
		{
			self::$method( function ()
			{
				( new PathBuilder() )
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