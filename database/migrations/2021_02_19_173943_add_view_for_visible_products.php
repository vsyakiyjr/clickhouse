<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddViewForVisibleProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::getPdo()->exec(/** @lang MySQL */"
            create view categories_subcategories_visible as
              select
                cs.category_id,
                subcategory_id
            from categories_subcategories as cs
            inner join subcategories s on cs.subcategory_id = s.id
            and s.visible = 1
        ");

        DB::getPdo()->exec(/** @lang MySQL */"
            create view products_categories_visible as
            select
              p.product_id  AS product_id,
              s.category_id AS category_id
            from products_subcategories p
            inner join subcategories s on p.subcategory_id = s.id
            inner join categories c on s.category_id = c.id
            where c.visible = 1 and s.visible = 1
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::getPdo()->exec("drop view products_categories_visible");
        DB::getPdo()->exec("drop view categories_subcategories_visible");
    }
}
