<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateChargesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('charges', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
			$table->decimal('total_from', 20)->nullable();
			$table->decimal('total_to', 20)->nullable();
			$table->string('type', 191);
			$table->decimal('amount', 20);
			$table->index(['total_from','total_to']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('charges');
	}

}
