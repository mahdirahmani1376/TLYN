<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table
                ->foreignId('user_id')
                ->index()
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table
                ->foreignId('trade_id')
                ->index()
                ->nullable()
                ->references('id')
                ->on('trades');

            $table->string('type');
            $table->string('description')->nullable();

            $table->decimal('amount', 10, 3);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
