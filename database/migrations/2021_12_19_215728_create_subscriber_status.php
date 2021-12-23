<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriberStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriber_status', function (Blueprint $table) {
            $table->id();
            $table->string('uid');
            $table->string('device_id')->unique();
            $table->boolean('statu')->nullable($value = false);
            $table->string('receipt')->nullable();
            $table->timestamp('start_date')->nullable()->default(null);
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
        Schema::dropIfExists('subscriber_status');
    }
}
