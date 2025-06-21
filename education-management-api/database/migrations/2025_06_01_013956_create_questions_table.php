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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->text('text');
            $table->text('content');
            $table->string('chapter')->nullable();
            $table->enum('type', ['multiple_choice', 'true_false', 'short_answer', 'programming', 'essay']);
            $table->string('difficulty')->default('medium');
            $table->integer('points')->default(1);
            $table->foreignId('category_id')->nullable()->constrained('question_categories')->onDelete('set null');
            $table->foreignId('created_by')->constrained('users');
            $table->boolean('is_active')->default(true);
            $table->text('explanation')->nullable();
            $table->integer('time_limit')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
