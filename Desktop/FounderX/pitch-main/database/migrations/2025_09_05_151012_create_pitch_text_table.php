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
        Schema::create('pitch_texts', function (Blueprint $table) {
            $table->id();
            $table->text('text');
            $table->enum("status",['draft', 'submitted', 'scored']);
            $table->foreignId('user_id')->constrained();
            $table->foreignId('field_id')->constrained();
            $table->foreignId('stage_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pitch_text');
    }
};
