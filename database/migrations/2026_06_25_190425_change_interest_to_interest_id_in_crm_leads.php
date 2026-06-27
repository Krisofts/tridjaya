<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crm_leads', function (Blueprint $table) {
            // Hapus kolom interest string lama
            if (Schema::hasColumn('crm_leads', 'interest')) {
                $table->dropColumn('interest');
            }

            // Tambah interest_id foreign key
            $table->foreignId('interest_id')
                ->nullable()
                ->after('notes')
                ->constrained('crm_interests')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('crm_leads', function (Blueprint $table) {
            $table->dropForeign(['interest_id']);
            $table->dropColumn('interest_id');
            $table->string('interest')->nullable()->after('notes');
        });
    }
};