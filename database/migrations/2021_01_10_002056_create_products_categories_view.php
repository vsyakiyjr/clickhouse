<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsCategoriesView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       DB::getPdo()->exec(/** @lang SQL */'
            create or replace view products_categories as
                select `p`.`product_id` AS `product_id`,
                  `s`.`category_id`     AS `category_id`
                from `ikea`.`products_subcategories` `p`
                join `ikea`.`subcategories` `s` on `p`.`subcategory_id` = `s`.`id`;
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::getPdo()->exec(/** @lang SQL */'
            drop view products_categories
        ');
    }
}
