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
        // Enhance lists table
        Schema::table('lists', function (Blueprint $table) {
            $table->string('category')->default('general')->after('is_public'); // List Categories/Tags
            $table->string('sort_by')->default('date_added')->after('category'); // Sorting Options
            $table->string('sort_direction')->default('desc')->after('sort_by'); // Sorting Direction
            $table->unsignedBigInteger('cloned_from')->nullable()->after('sort_direction'); // Duplicate Lists - reference to original
            $table->boolean('allow_collaboration')->default(false)->after('cloned_from'); // List Collaboration
            $table->boolean('allow_comments')->default(true)->after('allow_collaboration'); // List Comments
            $table->integer('followers_count')->default(0)->after('allow_comments'); // List Following
            $table->integer('comments_count')->default(0)->after('followers_count'); // List Comments count
            
            $table->foreign('cloned_from')->references('id')->on('lists')->onDelete('set null');
        });

        // Add order field to list_items for custom sorting
        Schema::table('list_items', function (Blueprint $table) {
            $table->integer('sort_order')->default(0)->after('product_id'); // Manual sorting
        });

        // Create list_collaborators table (reusing existing structure pattern)
        Schema::create('list_collaborators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('list_id')->constrained('lists')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('permission', ['view', 'edit', 'admin'])->default('edit');
            $table->timestamp('invited_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();
            
            $table->unique(['list_id', 'user_id']);
        });

        // Create list_followers table
        Schema::create('list_followers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('list_id')->constrained('lists')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['list_id', 'user_id']);
        });

        // Create list_comments table
        Schema::create('list_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('list_id')->constrained('lists')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('list_comments')->onDelete('cascade'); // For threaded replies
            $table->text('content');
            $table->integer('likes_count')->default(0);
            $table->timestamps();
        });

        // Create list_comment_likes table
        Schema::create('list_comment_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comment_id')->constrained('list_comments')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['comment_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('list_comment_likes');
        Schema::dropIfExists('list_comments');
        Schema::dropIfExists('list_followers');
        Schema::dropIfExists('list_collaborators');
        
        Schema::table('list_items', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
        
        Schema::table('lists', function (Blueprint $table) {
            $table->dropForeign(['cloned_from']);
            $table->dropColumn([
                'category',
                'sort_by',
                'sort_direction',
                'cloned_from',
                'allow_collaboration',
                'allow_comments',
                'followers_count',
                'comments_count'
            ]);
        });
    }
};
