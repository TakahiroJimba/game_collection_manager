<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id')->index();
            $table->string('mail_address')->unique()->index();
            $table->string('name')->unique();
            $table->string('password');
            $table->timestamp('last_playing_time')->nullable();
            $table->timestamp('lock_at')->nullable();
            $table->integer('lock_count')->default(0);
            $table->integer('login_failure_num')->default(0);
            $table->boolean('ban')->default(false);
            $table->timestamps();
            $table->softDeletes();      // deleted_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
