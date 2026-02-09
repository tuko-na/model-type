<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // category を genre_id と genre_name に変更
            $table->string('genre_id')->nullable()->after('category');
            $table->string('genre_name')->nullable()->after('genre_id');
            
            // 楽天URL（出典リンク - 必須表示）
            $table->string('rakuten_url')->nullable()->after('genre_name');
        });

        // 既存の category データを genre_name に移行
        DB::table('products')
            ->whereNotNull('category')
            ->update(['genre_name' => DB::raw('category')]);

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('category')->nullable()->after('manufacturer');
        });

        // genre_name を category に戻す
        DB::table('products')
            ->whereNotNull('genre_name')
            ->update(['category' => DB::raw('genre_name')]);

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['genre_id', 'genre_name', 'rakuten_url']);
        });
    }
};
