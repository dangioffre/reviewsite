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
        Schema::table('list_collaborators', function (Blueprint $table) {
            // Remove the old permission column
            $table->dropColumn('permission');
            
            // Add granular permission columns
            $table->boolean('can_add_games')->default(true)->after('user_id');
            $table->boolean('can_delete_games')->default(true)->after('can_add_games');
            $table->boolean('can_rename_list')->default(false)->after('can_delete_games');
            $table->boolean('can_manage_users')->default(false)->after('can_rename_list');
            $table->boolean('can_change_privacy')->default(false)->after('can_manage_users');
            $table->boolean('can_change_category')->default(false)->after('can_change_privacy');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('list_collaborators', function (Blueprint $table) {
            // Remove granular permission columns
            $table->dropColumn([
                'can_add_games',
                'can_delete_games', 
                'can_rename_list',
                'can_manage_users',
                'can_change_privacy',
                'can_change_category'
            ]);
            
            // Add back the old permission column
            $table->enum('permission', ['view', 'edit', 'admin'])->default('edit')->after('user_id');
        });
    }
};
