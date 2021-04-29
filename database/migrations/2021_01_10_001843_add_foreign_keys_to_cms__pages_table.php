<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCmsPagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cms__pages', function(Blueprint $table)
		{
			$table->foreign('parent_directory', 'cms__pages_ibfk_2')->references('fullpath')->on('cms__directories')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('parent_directory_id', 'cms__pages_ibfk_3')->references('id')->on('cms__directories')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('parent_page_id', 'cms__pages_ibfk_4')->references('id')->on('cms__pages')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('cms__pages', function(Blueprint $table)
		{
			$table->dropForeign('cms__pages_ibfk_2');
			$table->dropForeign('cms__pages_ibfk_3');
			$table->dropForeign('cms__pages_ibfk_4');
		});
	}

}
