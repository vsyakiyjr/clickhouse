<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToProductsGroupedTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('products_grouped', function(Blueprint $table)
		{
			$table->foreign('parent_vendor_code')->references('vendor_code')->on('products')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('products_grouped', function(Blueprint $table)
		{
			$table->dropForeign('products_grouped_parent_vendor_code_foreign');
		});
	}

}
