<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_record_id')->nullable()->constrained()->nullOnDelete();
            $table->string('phone', 15);
            $table->string('type', 30);
            $table->string('provider_message_id')->nullable();
            $table->string('status', 20)->default('pending');
            $table->text('body');
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index('type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_messages');
    }
};
