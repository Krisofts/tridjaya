<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {

            /*
            |--------------------------------------
            | OUTCOME
            |--------------------------------------
            */
            $table->string('outcome')
                ->nullable()
                ->after('status');

            /*
            |--------------------------------------
            | NOTES
            |--------------------------------------
            */
            $table->text('notes')
                ->nullable()
                ->after('description');

            /*
            |--------------------------------------
            | REMINDER
            |--------------------------------------
            */
            $table->dateTime('reminder_at')
                ->nullable()
                ->after('due_date');

            /*
            |--------------------------------------
            | COMPLETED BY
            |--------------------------------------
            */
            $table->foreignId('completed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->after('created_by');

            /*
            |--------------------------------------
            | PARENT TASK (SUB TASK)
            |--------------------------------------
            */
            $table->foreignId('parent_task_id')
                ->nullable()
                ->constrained('tasks')
                ->nullOnDelete()
                ->after('id');

            /*
            |--------------------------------------
            | METADATA (JSON FLEXIBLE FIELD)
            |--------------------------------------
            */
            $table->json('metadata')
                ->nullable()
                ->after('reminder_at');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {

            $table->dropForeign(['completed_by']);
            $table->dropForeign(['parent_task_id']);

            $table->dropColumn([
                'outcome',
                'notes',
                'reminder_at',
                'completed_by',
                'parent_task_id',
                'metadata',
            ]);
        });
    }
};