<?php
	namespace Donny5300\ModulairRouter\Models;

	use Illuminate\Database\Eloquent\Model;

	/**
	 * Class AppModule
	 *
	 * @package Donny5300\Routing\Models
	 */
	class AppModule extends BaseModel
	{
		/**
		 * @var array
		 */
		protected $fillable = [ 'uuid', 'title', 'namespace_id' ];

		/**
		 * @param      $request
		 * @param null $uuid
		 * @return bool
		 */
		public function storeOrUpdate( $request, $uuid = null )
		{
			$item = $this->firstOrNew( [ 'uuid' => $uuid ] );

			$item->title          = $request->title;
			$item->description    = $request->description;
			$item->namespace_id   = $request->namespace_id;
			$item->is_online      = $request->is_online;
			$item->developer_only = $request->developer_only;

			if( $item->save() )
			{
				return $item;
			}

			return false;
		}

		/**
		 * @param $request
		 * @param $namespaceId
		 * @return mixed
		 */
		public function storeFromException( $request, $namespaceId )
		{
			$item = $this->firstOrNew( [ 'namespace_id' => $namespaceId, 'title' => $request->module ] );

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
		 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
		 */
		public function appNamespace()
		{
			return $this->belongsTo( AppNamespace::class, 'namespace_id', 'id' );
		}

		/**
		 * @return \Illuminate\Database\Eloquent\Relations\HasMany
		 */
		public function controllers()
		{
			return $this->hasMany( AppController::class, 'module_id' );
		}

		/**
		 * @param $uuid
		 * @return mixed
		 */
		public function deleteWithRelations( $uuid )
		{
			$object = $this->where( 'uuid', $uuid )->with( 'controllers.methods', 'appNamespace' )->first();

			$clone = $object->replicate();

			$object->controllers->each( function ( $controller )
			{
				$controller->methods->each( function ( $method )
				{
					$method->delete();
				} );

				$controller->delete();
			} );

			$object->delete();

			return $clone;

		}


	}