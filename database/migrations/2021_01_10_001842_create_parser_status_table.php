<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateParserStatusTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('parser_status', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('process_id')->index();
			$table->integer('subcategory_id')->index();
			$table->string('vendor_code', 191);
			$table->integer('total_subcategories')->default(0);
			$table->integer('processed_subcategories')->default(0);
			$table->integer('processed_products')->default(0);
			$table->integer('total_products')->default(0);
			$table->string('url', 500)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('parser_status');
	}

}
