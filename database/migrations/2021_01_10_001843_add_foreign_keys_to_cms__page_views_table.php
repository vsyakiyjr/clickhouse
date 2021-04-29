<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCmsPageViewsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cms__page_views', function(Blueprint $table)
		{
			$table->foreign('page_id')->references('id')->on('cms__pages')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('cms__page_views', function(Blueprint $table)
		{
			$table->dropForeign('cms__page_views_page_id_foreign');
		});
	}

}
