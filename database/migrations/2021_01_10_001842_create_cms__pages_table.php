<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCmsPagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cms__pages', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('alias', 191);
			$table->string('parent_directory', 191)->index('cms__pages_ibfk_2');
			$table->integer('parent_directory_id')->unsigned()->index('parent_directory_id');
			$table->string('host', 120)->default('ikeamania.by');
			$table->string('title', 191);
			$table->string('description', 500);
			$table->text('keywords')->nullable();
			$table->string('breadcrumbs_title', 191)->nullable();
			$table->text('content')->nullable();
			$table->boolean('enabled')->default(0);
			$table->boolean('editable')->default(1);
			$table->string('image_path', 191)->nullable();
			$table->string('image_path_original', 191)->nullable();
			$table->boolean('priority')->default(1);
			$table->timestamps();
			$table->decimal('sitemap_priority', 5)->nullable();
			$table->string('template_name', 100)->nullable();
			$table->text('text_preview', 65535)->nullable();
			$table->integer('parent_page_id')->unsigned()->nullable()->index('parent_page_id');
			$table->boolean('force_noindex')->default(0);
			$table->string('fullpath', 191)->nullable();
			$table->boolean('include_to_index')->default(1);
			$table->dateTime('news_date')->nullable();
			$table->bigInteger('view_count')->unsigned()->default(0);
			$table->unique(['alias','parent_directory'], 'page_full_path');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cms__pages');
	}

}
