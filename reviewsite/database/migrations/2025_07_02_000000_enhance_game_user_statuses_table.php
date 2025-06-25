<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('game_user_statuses', function (Blueprint $table) {
            // Enhanced status tracking
            $table->string('completion_status')->nullable()->after('played');
            $table->integer('hours_played')->nullable()->after('completion_status');
            $table->integer('completion_percentage')->nullable()->after('hours_played');
            $table->date('started_date')->nullable()->after('completion_percentage');
            $table->date('completed_date')->nullable()->after('started_date');
            $table->text('notes')->nullable()->after('completed_date');
            $table->integer('rating')->nullable()->after('notes');
            $table->boolean('is_favorite')->default(false)->after('rating');
            $table->string('platform_played')->nullable()->after('is_favorite');
            $table->integer('times_replayed')->default(0)->after('platform_played');
            $table->json('achievements')->nullable()->after('times_replayed');
            $table->string('difficulty_played')->nullable()->after('achievements');
            $table->boolean('dropped')->default(false)->after('difficulty_played');
            $table->date('dropped_date')->nullable()->after('dropped');
            $table->text('drop_reason')->nullable()->after('dropped_date');
        });
    }

    public function down()
    {
        Schema::table('game_user_statuses', function (Blueprint $table) {
            $table->dropColumn([
                'completion_status',
                'hours_played',
                'completion_percentage',
                'started_date',
                'completed_date',
                'notes',
                'rating',
                'is_favorite',
                'platform_played',
                'times_replayed',
                'achievements',
                'difficulty_played',
                'dropped',
                'dropped_date',
                'drop_reason'
            ]);
        });
    }
}; 