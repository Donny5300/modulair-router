<?php
	namespace Donny5300\ModulairRouter\Models;

	use Illuminate\Database\Eloquent\Model;

	/**
	 * Class AppMethod
	 *
	 * @package Donny5300\Routing\Models
	 */
	class AppMethod extends BaseModel
	{
		/**
		 * @var array
		 */
		protected $fillable = [ 'uuid', 'controller_id', 'title' ];

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
			$item->controller_id  = $request->controller_id;
			$item->is_online      = $request->is_online;
			$item->developer_only = $request->developer_only;
			$item->is_visible     = $request->is_visible;
			$item->request_method = $request->request_method;

			if( $item->save() )
			{
				return $item;
			}

			return false;
		}

		/**
		 * @param $request
		 * @param $controllerId
		 * @return mixed
		 */
		public function storeFromException( $request, $controllerId )
		{
			$item = $this->firstOrNew( [ 'controller_id' => $controllerId, 'title' => $request->action ] );

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
		public function controller()
		{
			return $this->belongsTo( AppController::class );
		}
	}