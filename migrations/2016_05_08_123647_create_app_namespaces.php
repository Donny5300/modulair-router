<?php

	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Database\Migrations\Migration;

	class CreateAppNamespaces extends Migration
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
				$table->string( 'namespace' );
				$table->text( 'description' );
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
		}
	}
