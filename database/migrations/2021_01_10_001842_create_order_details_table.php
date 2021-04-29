<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrderDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('order_details', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
			$table->integer('order_id')->index('order_details_order_id_foreign');
			$table->decimal('price', 30);
			$table->integer('qty');
			$table->integer('product_id')->nullable()->index('order_details_product_id_foreign');
			$table->string('vendor_code', 191);
			$table->text('options')->nullable();
			$table->text('model')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('order_details');
	}

}
