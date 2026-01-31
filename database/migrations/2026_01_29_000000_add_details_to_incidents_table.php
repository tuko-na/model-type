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
            if (!Schema::hasColumn('incidents', 'details')) {
                $table->json('details')->nullable()->after('description');
            }
            if (!Schema::hasColumn('incidents', 'severity')) {
                $table->string('severity')->nullable()->after('incident_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            if (Schema::hasColumn('incidents', 'details')) {
                $table->dropColumn('details');
            }
            if (Schema::hasColumn('incidents', 'severity')) {
                $table->dropColumn('severity');
            }
        });
    }
};
