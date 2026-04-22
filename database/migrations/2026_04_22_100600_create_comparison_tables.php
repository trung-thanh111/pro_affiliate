<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('post_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('posts')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('sort_order')->default(0);
            $table->string('column_title')->nullable();
            $table->string('column_subtitle')->nullable();
            $table->string('column_image')->nullable();
            $table->string('cta_text')->nullable();
            $table->string('cta_url')->nullable();
            $table->string('badge_text')->nullable();
            $table->boolean('is_highlight')->default(false);
            $table->timestamps();
            $table->unique(['post_id', 'product_id']);
        });

        Schema::create('comparison_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('posts')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('comparison_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comparison_section_id')->constrained('comparison_sections')->cascadeOnDelete();
            $table->string('label');
            $table->string('slug')->nullable();
            $table->string('row_type')->default('text');
            $table->string('unit')->nullable();
            $table->string('tooltip')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });

        Schema::create('comparison_cells', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comparison_row_id')->constrained('comparison_rows')->cascadeOnDelete();
            $table->foreignId('post_product_id')->constrained('post_products')->cascadeOnDelete();
            $table->text('value_text')->nullable();
            $table->text('value_html')->nullable();
            $table->string('value_type')->default('text');
            $table->boolean('is_highlight')->default(false);
            $table->string('note')->nullable();
            $table->string('icon')->nullable();
            $table->string('link_url')->nullable();
            $table->timestamps();
            $table->unique(['comparison_row_id', 'post_product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comparison_cells');
        Schema::dropIfExists('comparison_rows');
        Schema::dropIfExists('comparison_sections');
        Schema::dropIfExists('post_products');
    }
};
