<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');

            $table->string('username')->unique()->nullable();
            $table->string('phone')->unique()->nullable();
            //$table->string('country_code')->default("+86");
            $table->string('email')->unique();
            $table->string('password');
            $table->text('intro')->nullable();
            $table->text('avatar_url')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
