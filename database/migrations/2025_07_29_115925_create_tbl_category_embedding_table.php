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
        Schema::create('tbl_category_embedding', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_category_id')->constrained('tbl_sub_category')->onDelete('cascade');
            $table->string('service')->nullable();
            $table->text('keywords')->nullable();
            $table->longText('embedding')->nullable();
            $table->string('row_hash')->unique();
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
        Schema::dropIfExists('tbl_category_embedding');
    }
};
