<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShoppingcartTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('shoppingcart', function(Blueprint $table)
		{
			$table->string('identifier', 191);
			$table->string('instance', 191);
			$table->text('content');
			$table->timestamps();
			$table->primary(['identifier','instance']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('shoppingcart');
	}

}
