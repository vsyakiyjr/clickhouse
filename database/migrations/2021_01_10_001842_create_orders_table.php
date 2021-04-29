<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrdersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('orders', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('order_num');
			$table->string('email');
			$table->string('phone');
			$table->string('name', 191)->nullable();
			$table->string('user_id', 45)->index();
			$table->date('delivery_date')->index();
			$table->string('delivery_city')->index();
			$table->integer('delivery_type')->index();
			$table->text('delivery_address')->nullable();
			$table->text('delivery_house')->nullable();
			$table->string('delivery_stairs', 191)->nullable();
			$table->string('delivery_apartment', 191)->nullable();
			$table->boolean('delivery_assemble')->default(0);
			$table->boolean('delivery_lifting')->default(0);
			$table->integer('delivery_cost');
			$table->integer('total');
			$table->timestamps();
			$table->string('vendor_code', 191)->nullable();
			$table->float('exchange', 16, 4);
			$table->integer('delivery_floor')->nullable();
			$table->float('commission')->nullable();
			$table->text('comment', 65535)->nullable();
			$table->text('price_byn', 65535)->nullable();
			$table->integer('fix_commission')->default(0);
			$table->float('without_commission', 20);
			$table->text('cart_details')->nullable();
			$table->string('promo_code', 191)->nullable();
			$table->string('promo_discount', 191)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('orders');
	}

}
