<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->index()
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->string('type');
            $table->string('status');
            $table->decimal('amount', 10, 3);
            $table->decimal('remaining_amount', 10, 3);
            $table->bigInteger('price');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
