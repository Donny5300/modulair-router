<?php namespace Donny5300\ModulairRouter\Models;

/**
 * Class AppNamespace
 *
 * @package Donny5300\Routing\Models
 */
class AppNamespace extends BaseModel
{
	/**
	 * @var array
	 */
	protected $fillable = [ 'uuid', 'title' ];

	/**
	 * @param      $request
	 * @param null $uuid
	 * @return bool
	 */
	public function storeOrUpdate( $request, $uuid = null )
	{
		$item = $this->firstOrNew( [ 'uuid' => $uuid ] );

		$item->title          = $request->title;
		$item->description    = $request->get( 'description', $item->description );
		$item->is_online      = $request->get( 'is_online', 1 );
		$item->developer_only = $request->get( 'developer_only', 0 );

		if( $item->save() )
		{
			return $item;
		}

		return false;
	}

	/**
	 * @param $request
	 * @return mixed
	 */
	public function storeFromException( $request )
	{
		$item = $this->firstOrNew( [ 'title' => $request->namespace ] );

		$item->uuid = generate_uuid();

		return $item->save() ? $item : false;
	}

	/**
	 * @param $value
	 * @return string
	 */
	public function setUuidAttribute( $value )
	{
		return $this->attributes['uuid'] = generate_uuid( $value );
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function modules()
	{
		return $this->hasMany( AppModule::class, 'namespace_id', 'id' );
	}

	/**
	 * @param $uuid
	 * @return object
	 */
	public function deleteWithRelations( $uuid )
	{
		$item  = $this->where( 'uuid', $uuid )
			->with( 'modules.controllers.methods' )
			->first();
		$clone = $item->replicate();

		$item->modules()->each( function ( $module )
		{
			$module->controllers()->each( function ( $controller )
			{
				$controller->methods()->each( function ( $method )
				{
					$method->delete();
				} );

				$controller->delete();
			} );

			$module->delete();

		} );

		$item->delete();

		return $clone;
	}
}