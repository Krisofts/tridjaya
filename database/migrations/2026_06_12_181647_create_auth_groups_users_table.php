<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth_groups_users', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('group', 100);

            $table->timestamps();

            $table->index('group');

            $table->unique([
                'user_id',
                'group',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_groups_users');
    }
};