<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCmsPagesAliasesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cms__pages_aliases', function(Blueprint $table)
		{
			$table->foreign('page_id', 'cms__pages_aliases_ibfk_2')->references('id')->on('cms__pages')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('cms__pages_aliases', function(Blueprint $table)
		{
			$table->dropForeign('cms__pages_aliases_ibfk_2');
		});
	}

}
