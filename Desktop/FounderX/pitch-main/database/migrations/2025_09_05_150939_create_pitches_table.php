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
        Schema::create('pitches', function (Blueprint $table) {
            $table->id();
            $table->text('Problem');
            $table->text('Solution');
            $table->text('Market');
            $table->text('Product / Tech Stack');
            $table->text('Business Model');
            $table->text('Competition');
            $table->text('Market Strategy');
            $table->text('Traction /Results');
            $table->text('Team Info');
            $table->text('Financials & Investment Ask');
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
        Schema::dropIfExists('pitches');
    }
};
