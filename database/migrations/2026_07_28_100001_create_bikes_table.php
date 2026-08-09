<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bikes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('brand');
            $table->string('model')->nullable();
            $table->string('registration_number')->nullable();
            $table->timestamps();

            $table->index('registration_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bikes');
    }
};
