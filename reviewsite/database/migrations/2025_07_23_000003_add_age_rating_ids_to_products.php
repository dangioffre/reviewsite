<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('esrb_rating_id')->nullable()->constrained('age_ratings')->nullOnDelete();
            $table->foreignId('pegi_rating_id')->nullable()->constrained('age_ratings')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['esrb_rating_id']);
            $table->dropForeign(['pegi_rating_id']);
            $table->dropColumn(['esrb_rating_id', 'pegi_rating_id']);
        });
    }
}; 