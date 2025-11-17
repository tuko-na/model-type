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
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropColumn(['status', 'category', 'severity']);
            $table->string('incident_type');
            $table->string('resolution_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropColumn(['incident_type', 'resolution_type']);
            $table->string('status');
            $table->string('category')->nullable();
            $table->string('severity')->nullable();
        });
    }
};
