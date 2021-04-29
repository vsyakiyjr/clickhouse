<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShowProductsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('show_products', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('vendor_code')->index();
			$table->string('place', 191)->default('header')->index();
			$table->timestamps();
			$table->integer('show_order');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('show_products');
	}

}
