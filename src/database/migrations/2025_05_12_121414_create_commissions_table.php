<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->decimal('min_amount', 10, 3);
            $table->decimal('max_amount', 10, 3)->nullable();
            $table->integer('rate');
            $table->integer('min_fee')->default(50000)->nullable();
            $table->bigInteger('max_fee')->default(5000000)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};
