<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pages', function(Blueprint $table)
		{
			$table->integer('id')->primary();
			$table->string('alias')->index();
			$table->string('title')->nullable();
			$table->string('description')->nullable();
			$table->text('text')->nullable();
			$table->boolean('visible');
			$table->string('head')->nullable();
			$table->string('seo_title', 191)->nullable();
			$table->string('seo_description', 191)->nullable();
			$table->string('seo_keywords', 191)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pages');
	}

}
