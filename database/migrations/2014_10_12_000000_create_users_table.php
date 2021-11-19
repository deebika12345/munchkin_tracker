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
            $table->id();
            $table->string('name');
            $table->string('student_name')->nullable();
            $table->string('user_type')->nullable();
            $table->string('phone_number')->unique();
            $table->string('permanent_latitude')->nullable();
            $table->string('permanent_longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('password');
            $table->string('driver_id')->nullable();
            $table->dateTime('arriving_time')->nullable();
            $table->boolean('is_dismissal')->default(false);
            $table->longText('dismissal_note')->nullable();
            $table->string('standard')->nullable();
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
        Schema::dropIfExists('users');
    }
}
