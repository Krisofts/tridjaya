<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('leads', function (Blueprint $table) {
        $table->string('interest')->nullable()->after('status');
    });
}

public function down(): void
{
    Schema::table('leads', function (Blueprint $table) {
        $table->dropColumn('interest');
    });
}
};
