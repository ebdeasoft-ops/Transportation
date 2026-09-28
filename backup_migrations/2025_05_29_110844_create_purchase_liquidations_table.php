<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_liquidations', function (Blueprint $table) {
               $table->id();
            $table->bigInteger( 'branchs_id' )->unsigned();
            $table->bigInteger( 'user_id' )->unsigned();
            $table->unsignedDouble  ('price_filtering')->default(0);
            $table->string('note_detaials', 1000);
            $table->string('attachments', 1000);
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
        Schema::dropIfExists('purchase_liquidations');
    }
};
