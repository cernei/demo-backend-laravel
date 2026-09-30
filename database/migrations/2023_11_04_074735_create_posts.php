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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->foreignId('category_id');
            $table->foreignId('user_id');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
        DB::statement("
            ALTER TABLE posts
            ADD COLUMN content_search tsvector
            GENERATED ALWAYS AS (
                to_tsvector('english', coalesce(content, ''))
            ) STORED
        ");

        DB::statement("
            CREATE INDEX posts_content_search_idx
            ON posts
            USING GIN (content_search)
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
        DB::statement("
            DROP INDEX IF EXISTS posts_content_search_idx
        ");

        DB::statement("
            ALTER TABLE posts
            DROP COLUMN IF EXISTS content_search
        ");
    }
};
