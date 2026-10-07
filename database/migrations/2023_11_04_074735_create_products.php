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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->foreignId('category_id');
            $table->foreignId('admin_id');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
        DB::statement("
            ALTER TABLE products
            ADD COLUMN description_search tsvector
            GENERATED ALWAYS AS (
                to_tsvector('english', coalesce(description, ''))
            ) STORED
        ");

        DB::statement("
            CREATE INDEX products_description_search_idx
            ON products
            USING GIN (description_search)
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            DROP INDEX IF EXISTS products_description_search_idx
        ");

        DB::statement("
            ALTER TABLE products
            DROP COLUMN IF EXISTS description_search
        ");
        Schema::dropIfExists('products');
    }
};
