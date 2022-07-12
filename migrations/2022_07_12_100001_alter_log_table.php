<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::alter('logs', function (Blueprint $table) {
            $table->string('request_id')->index();
            $table->string('ip')->nullable();
            $table->string('route')->nullable();
            $table->string('uri')->nullable();
            $table->string('user_agent')->nullable();
            $table->text('payload_base64')->nullable();
            $table->float('request_start')->nullable();
            $table->float('request_duration')->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logs');
    }
}
