<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCmsDirectoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cms__directories', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('fullpath', 191)->unique('fullpath_indx');
			$table->string('parent_directory', 191);
			$table->string('description', 191);
			$table->string('description_uk', 191)->nullable();
			$table->string('description_en', 191)->nullable();
			$table->timestamps();
			$table->boolean('transparent')->default(0);
			$table->boolean('special')->default(0);
			$table->boolean('show_siblings')->default(0);
			$table->string('host', 120)->default('ikeamania.by');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cms__directories');
	}

}
