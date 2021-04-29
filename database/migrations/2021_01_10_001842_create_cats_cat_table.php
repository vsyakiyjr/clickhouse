<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCatsCatTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cats_cat', function(Blueprint $table)
		{
			$table->integer('parent_id');
			$table->integer('cat_id');
			$table->unique(['parent_id','cat_id'], 'uniq_cats_cat');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cats_cat');
	}

}
