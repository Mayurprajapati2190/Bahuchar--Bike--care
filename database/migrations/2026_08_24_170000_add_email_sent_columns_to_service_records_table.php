<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_records', function (Blueprint $table) {
            $table->timestamp('confirmation_email_sent_at')->nullable()->after('confirmation_sms_sent_at');
            $table->timestamp('reminder_email_sent_at')->nullable()->after('reminder_sms_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('service_records', function (Blueprint $table) {
            $table->dropColumn(['confirmation_email_sent_at', 'reminder_email_sent_at']);
        });
    }
};
