<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToOrderDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('order_details', function(Blueprint $table)
		{
			$table->foreign('order_id')->references('id')->on('orders')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('product_id')->references('id')->on('products')->onUpdate('RESTRICT')->onDelete('SET NULL');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('order_details', function(Blueprint $table)
		{
			$table->dropForeign('order_details_order_id_foreign');
			$table->dropForeign('order_details_product_id_foreign');
		});
	}

}
