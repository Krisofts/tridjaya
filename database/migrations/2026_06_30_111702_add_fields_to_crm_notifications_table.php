<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crm_notifications', function (Blueprint $table) {

            // Penerima
            $table->foreignId('user_id')
                ->after('id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Tipe
            $table->string('type', 50)->after('user_id');

            // Konten
            $table->string('title')->after('type');
            $table->text('message')->nullable()->after('title');

            // Relasi opsional
            $table->foreignId('lead_id')
                ->nullable()
                ->after('message')
                ->constrained('crm_leads')
                ->cascadeOnDelete();

            $table->foreignId('task_id')
                ->nullable()
                ->after('lead_id')
                ->constrained('crm_tasks')
                ->cascadeOnDelete();

            // URL tujuan
            $table->string('action_url')->nullable()->after('task_id');

            // Status baca
            $table->boolean('is_read')->default(false)->after('action_url');
            $table->timestamp('read_at')->nullable()->after('is_read');

            // Waktu reminder
            $table->timestamp('remind_at')->nullable()->after('read_at');

            // Indexes
            $table->index(['user_id', 'is_read']);
            $table->index(['user_id', 'created_at']);
            $table->index('remind_at');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::table('crm_notifications', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['lead_id']);
            $table->dropForeign(['task_id']);
            $table->dropIndex(['user_id', 'is_read']);
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropIndex(['remind_at']);
            $table->dropIndex(['type']);
            $table->dropColumn([
                'user_id', 'type', 'title', 'message',
                'lead_id', 'task_id', 'action_url',
                'is_read', 'read_at', 'remind_at',
            ]);
        });
    }
};