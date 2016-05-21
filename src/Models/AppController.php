<?php
	namespace Donny5300\ModulairRouter\Models;

	use Illuminate\Database\Eloquent\Model;

	/**
	 * Class AppController
	 *
	 * @package Donny5300\Routing\Models
	 */
	class AppController extends BaseModel
	{
		/**
		 * @var array
		 */
		protected $fillable = [ 'uuid', 'module_id', 'title' ];


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
			$item->module_id      = $request->module_id;
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
		 * @param $moduleId
		 * @return mixed
		 */
		public function storeFromException( $request, $moduleId )
		{
			$item = $this->firstOrNew( [ 'module_id' => $moduleId, 'title' => $request->controller ] );

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
		public function module()
		{
			return $this->belongsTo( AppModule::class );
		}

		/**
		 * @return \Illuminate\Database\Eloquent\Relations\HasMany
		 */
		public function methods()
		{
			return $this->hasMany( AppMethod::class, 'controller_id' );
		}

		/**
		 * @param $uuid
		 */
		public function deleteWithRelations( $uuid )
		{
			$item  = $this->uuid( $uuid )->with( 'methods', 'module.appNamespace' )->first();
			$clone = $item->replicate();

			$item->methods()->delete();
			$item->delete();

			return $clone;
		}
	}