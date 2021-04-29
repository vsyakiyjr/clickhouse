<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCmsPagesHistoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cms__pages_history', function(Blueprint $table)
		{
			$table->foreign('parent_directory_id', 'cms__pages_history_ibfk_2')->references('id')->on('cms__directories')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('page_id', 'cms__pages_history_ibfk_4')->references('id')->on('cms__pages')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('cms__pages_history', function(Blueprint $table)
		{
			$table->dropForeign('cms__pages_history_ibfk_2');
			$table->dropForeign('cms__pages_history_ibfk_4');
		});
	}

}
