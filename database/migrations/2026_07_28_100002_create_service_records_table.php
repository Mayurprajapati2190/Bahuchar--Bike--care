<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bike_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('service_date');
            $table->timestamp('completed_at')->nullable();
            $table->date('next_service_due_at')->nullable();
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->text('work_done')->nullable();
            $table->string('status', 20)->default('in_progress');
            $table->timestamp('confirmation_sms_sent_at')->nullable();
            $table->timestamp('reminder_sms_sent_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('next_service_due_at');
            $table->index('service_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_records');
    }
};
