<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSubcategoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('subcategories', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('name')->nullable();
			$table->string('link')->nullable();
			$table->integer('category_id')->nullable()->index('subcategories_category_id_foreign');
			$table->boolean('new')->nullable();
			$table->string('alias', 191)->nullable()->index();
			$table->integer('page_id')->unsigned()->nullable();
			$table->timestamps();
			$table->boolean('visible')->default(1);
			$table->string('ikea_id', 191)->nullable()->index();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('subcategories');
	}

}
