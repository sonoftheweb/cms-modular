<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('stripe_plan_identifier');
            $table->string('nickname');
            $table->string('object');
            $table->boolean('active');
            $table->string('currency');
            $table->string('interval');
            $table->integer('interval_count');
            $table->integer('livemode');
            $table->json('metadata');
            $table->string('product_id');
            $table->json('tiers');
            $table->integer('trial_period_days')->default(14);
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
        Schema::dropIfExists('plans');
    }
}
