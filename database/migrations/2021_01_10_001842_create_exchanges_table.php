<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateExchangesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
	    if(Schema::hasTable('exchanges')){
	        return;
        }

		Schema::create('exchanges', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->float('echange_rates', 10, 5);
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('exchanges');
	}

}
