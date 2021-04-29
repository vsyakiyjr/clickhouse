<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('products', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('name');
			$table->string('vendor_code')->unique();
			$table->string('type');
			$table->text('description');
			$table->string('color')->nullable();
			$table->string('height')->nullable();
			$table->string('width')->nullable();
			$table->string('length')->nullable();
			$table->string('weight')->nullable();
			$table->string('depth')->nullable();
			$table->string('packaging')->nullable();
			$table->string('photo');
			$table->text('gallery')->nullable();
			$table->boolean('fixed_price');
			$table->integer('price');
			$table->string('family_price')->nullable();
			$table->string('family_offers_start')->nullable();
			$table->string('family_offers_end')->nullable();
			$table->float('discount', 11, 5)->default(0.00000);
			$table->boolean('visible');
			$table->integer('available')->nullable();
			$table->integer('quantity_controll')->nullable();
			$table->integer('quantity')->nullable()->default(0);
			$table->boolean('new')->nullable();
			$table->boolean('popular')->default(0);
			$table->string('link');
			$table->string('mod_group')->nullable()->index();
			$table->date('updated_at');
			$table->date('created_at');
			$table->string('benefit')->nullable();
			$table->string('good_to_know')->nullable();
			$table->string('sold_separately')->nullable();
			$table->string('cust_materials')->nullable();
			$table->text('attachments')->nullable();
			$table->text('pkg_info')->nullable();
			$table->text('additional_products')->nullable();
			$table->string('size', 191)->nullable();
			$table->integer('qty_orders')->nullable()->default(0);
			$table->boolean('price_family')->default(0);
			$table->integer('ikea_family_id')->unsigned()->nullable();
			$table->text('possible_attributes')->nullable();
			$table->text('attributes')->nullable();
			$table->text('sizes_original')->nullable();
			$table->integer('priority')->unsigned()->default(0);
			$table->string('name_fuzzy', 191)->nullable();
			$table->string('type_fuzzy', 191)->nullable();
			$table->text('description_fuzzy')->nullable();
			$table->string('cat_ikea_id', 50)->nullable()->index();
			$table->dateTime('parsed_at')->nullable();
			$table->boolean('parsed_at_this_run')->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('products');
	}

}
