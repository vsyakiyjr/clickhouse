<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCategoriesSubcategoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('categories_subcategories', function(Blueprint $table)
		{
			$table->integer('category_id');
			$table->integer('subcategory_id')->index('categories_subcategories_subcategory_id_foreign');
			$table->primary(['category_id','subcategory_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('categories_subcategories');
	}

}
