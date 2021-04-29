<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParserSitemapsQueueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parser_sitemaps_queue', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('sitemap_url');
            $table->unsignedInteger('process_id')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parser_sitemaps_queue');
    }
}
