<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->string('customer_number', 100);
            $table->string('name', 255);
            $table->enum('type', ['route', 'phone', 'priority']);
            $table->string('street', 255);
            $table->string('house_number', 15);
            $table->string('postal_code', 15);
            $table->string('city', 255);
            $table->string('logo_url', 255)->nullable();
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
        Schema::dropIfExists('shops');
    }
};
