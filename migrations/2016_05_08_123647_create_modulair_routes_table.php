<?php

	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Database\Migrations\Migration;

	class CreateModulairRoutesTable extends Migration
	{
		/**
		 * Run the migrations.
		 *
		 * @return void
		 */
		public function up()
		{
			Schema::create( 'app_namespaces', function ( Blueprint $table )
			{
				$table->increments( 'id' );
				$table->uuid( 'uuid' );
				$table->string( 'title' );
				$table->text( 'description' );
				$table->boolean( 'is_online' );
				$table->boolean( 'developer_only' )->default( false );
				$table->softDeletes();
				$table->timestamps();
			} );

			Schema::create( 'app_modules', function ( Blueprint $table )
			{
				$table->increments( 'id' );
				$table->uuid( 'uuid' );
				$table->string( 'title' );
				$table->text( 'description' )->nullable();
				$table->integer( 'namespace_id' );
				$table->boolean( 'is_online' );
				$table->boolean( 'developer_only' )->default( false );
				$table->softDeletes();
				$table->timestamps();
			} );

			Schema::create( 'app_controllers', function ( Blueprint $table )
			{
				$table->increments( 'id' );
				$table->uuid( 'uuid' );
				$table->string( 'title' );
				$table->text( 'description' )->nullable();
				$table->integer( 'module_id' );
				$table->boolean( 'is_online' );
				$table->boolean( 'developer_only' )->default( false );
				$table->softDeletes();
				$table->timestamps();
			} );

			Schema::create( 'app_methods', function ( Blueprint $table )
			{
				$table->increments( 'id' );
				$table->uuid( 'uuid' );
				$table->string( 'title' );
				$table->text( 'description' )->nullable();
				$table->integer( 'controller_id' );
				$table->boolean( 'is_online' );
				$table->boolean( 'developer_only' );
				$table->boolean( 'is_visible' );
				$table->string( 'request_method' );
				$table->softDeletes();
				$table->timestamps();
			} );
		}

		/**
		 * Reverse the migrations.
		 *
		 * @return void
		 */
		public function down()
		{
			Schema::drop( 'app_namespaces' );
			Schema::drop( 'app_controllers' );
			Schema::drop( 'app_modules' );
			Schema::drop( 'app_methods' );
		}
	}
