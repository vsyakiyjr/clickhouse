<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCmsPagesHistoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cms__pages_history', function(Blueprint $table)
		{
			$table->string('revision', 120);
			$table->integer('page_id')->unsigned()->index('page_id');
			$table->string('alias', 191);
			$table->string('parent_directory', 191)->index('parent_directory');
			$table->integer('parent_directory_id')->unsigned()->index('parent_directory_id');
			$table->string('title', 191);
			$table->string('description', 500);
			$table->text('keywords', 65535)->nullable();
			$table->string('breadcrumbs_title', 191)->nullable();
			$table->text('content', 65535)->nullable();
			$table->boolean('enabled')->default(1);
			$table->string('image_path', 191)->nullable();
			$table->boolean('priority')->default(1);
			$table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->integer('updated_by_user_id')->unsigned()->index('updated_by_user_id');
			$table->decimal('sitemap_priority', 5)->nullable();
			$table->boolean('force_noindex')->default(0);
			$table->boolean('include_to_index')->default(1);
			$table->primary(['revision','page_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cms__pages_history');
	}

}
