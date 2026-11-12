<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dass_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('question_number')->unique();
            $table->text('text');
            $table->string('dimension', 20);
            $table->timestamps();

            $table->index('dimension');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dass_questions');
    }
};