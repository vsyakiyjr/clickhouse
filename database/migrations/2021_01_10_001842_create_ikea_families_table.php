<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateIkeaFamiliesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if(Schema::hasTable('ikea_families')){
            return;
        }

		Schema::create('ikea_families', function(Blueprint $table)
		{
			$table->increments('id');
			$table->date('start_date');
			$table->date('finish_date');
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
        if(!Schema::hasTable('ikea_families')){
            return;
        }
		Schema::drop('ikea_families');
	}

}
