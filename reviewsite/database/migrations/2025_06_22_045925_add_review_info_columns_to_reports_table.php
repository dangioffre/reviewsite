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
        Schema::table('reports', function (Blueprint $table) {
            $table->string('review_title')->nullable()->after('additional_info');
            $table->string('review_author_name')->nullable()->after('review_title');
            $table->string('product_name')->nullable()->after('review_author_name');
            $table->string('product_type')->nullable()->after('product_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn(['review_title', 'review_author_name', 'product_name', 'product_type']);
        });
    }
};
