<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserRegistrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_registrations', function (Blueprint $table) {
            $table->integer('app_id')->unsigned()->index();
            $table->timestamp('date');
            $table->integer('count')->default(0);
            $table->timestamps();
            $table->primary(['app_id', 'date']);
            $table->foreign('app_id')->references('id')->on('app_info')->onDelete('cascade'); //外部キー参照
            $table->index(['app_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_registrations');
    }
}
