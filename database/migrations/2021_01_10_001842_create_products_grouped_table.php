<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductsGroupedTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('products_grouped', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->string('parent_vendor_code')->index();
			$table->string('grouped_vendor_code')->index();
			$table->unique(['parent_vendor_code','grouped_vendor_code'], 'grouped_combo');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('products_grouped');
	}

}
