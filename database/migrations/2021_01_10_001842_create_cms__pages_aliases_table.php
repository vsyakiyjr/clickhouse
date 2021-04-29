<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCmsPagesAliasesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cms__pages_aliases', function(Blueprint $table)
		{
			$table->string('alias', 191)->primary();
			$table->integer('page_id')->unsigned()->index('page_indx');
			$table->string('match_type', 20)->index('match_type');
			$table->softDeletes();
			$table->text('comment', 65535)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cms__pages_aliases');
	}

}
