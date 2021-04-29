<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToProductsSubcategoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('products_subcategories', function(Blueprint $table)
		{
			$table->foreign('product_id', 'products_subcategories_table_product_id_foreign')->references('id')->on('products')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('subcategory_id', 'products_subcategories_table_subcategory_id_foreign')->references('id')->on('subcategories')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('products_subcategories', function(Blueprint $table)
		{
			$table->dropForeign('products_subcategories_table_product_id_foreign');
			$table->dropForeign('products_subcategories_table_subcategory_id_foreign');
		});
	}

}
