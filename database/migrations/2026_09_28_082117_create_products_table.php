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
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('product_name');
            $table->unsignedBigInteger('branchs_id')->index('products_branchs_id_foreign');
            $table->double('purchasingـprice')->unsigned()->default(0);
            $table->double('sale_price')->unsigned()->default(0);
            $table->double('numberofpice')->nullable();
            $table->string('Status', 50);
            $table->unsignedBigInteger('user_id')->index('products_user_id_foreign');
            $table->softDeletes();
            $table->timestamps();
            $table->string('Product_Location')->nullable();
            $table->string('Product_Code');
            $table->double('Added_Value', 8, 2)->default(0);
            $table->bigInteger('numberـofـsales')->default(0);
            $table->string('name_en')->default('Name_En');
            $table->string('notes')->default('لا توجد ملاحظات');
            $table->string('unit')->default('piece');
            $table->integer('minmum_quantity_stock_alart')->default(10);
            $table->bigInteger('main_product')->default(0);
            $table->text('refnumber')->nullable();
            $table->double('opening_blance')->default(0);
            $table->double('average_cost')->default(0);
            $table->double('Wholesale_price')->default(0);
            $table->text('photo')->nullable();
            $table->bigInteger('products_mix')->default(0);
            $table->bigInteger('product_group')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
