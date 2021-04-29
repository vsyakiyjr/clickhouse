<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePromoCodesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('promo_codes', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->string('code', 191)->unique();
			$table->string('discount', 191);
			$table->date('valid_from')->nullable()->index();
			$table->date('valid_to')->nullable()->index();
			$table->text('comment', 65535)->nullable();
			$table->integer('usage_limit')->unsigned()->nullable()->index();
			$table->integer('usage_count')->unsigned()->default(0)->index();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('promo_codes');
	}

}
