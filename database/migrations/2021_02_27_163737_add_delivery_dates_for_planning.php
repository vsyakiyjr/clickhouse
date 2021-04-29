<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeliveryDatesForPlanning extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deliverance_days', function (Blueprint $table) {
            $table->date('minsk_date_from')->nullable();
            $table->date('minsk_date_to')->nullable();
            $table->date('country_date_from')->nullable();
            $table->date('country_date_to')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deliverance_days', function (Blueprint $table) {
            $table->dropColumn('minsk_date_from');
            $table->dropColumn('minsk_date_to');
            $table->dropColumn('country_date_from');
            $table->dropColumn('country_date_to');
        });
    }
}
