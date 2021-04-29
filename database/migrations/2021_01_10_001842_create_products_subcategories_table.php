<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductsSubcategoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('products_subcategories', function(Blueprint $table)
		{
			$table->integer('product_id');
			$table->integer('subcategory_id')->index('products_subcategories_table_subcategory_id_foreign');
			$table->primary(['product_id','subcategory_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('products_subcategories');
	}

}
