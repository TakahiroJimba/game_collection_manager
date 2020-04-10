<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoginAdminUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('login_admin_users', function (Blueprint $table) {
            $table->integer('admin_id')->unsigned()->index();
            $table->string('session_id');
            $table->timestamp('expiration_date');
            $table->timestamps();
            $table->foreign('admin_id')->references('id')->on('admin_users')->onDelete('cascade'); //外部キー参照
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('login_admin_users');
    }
}
