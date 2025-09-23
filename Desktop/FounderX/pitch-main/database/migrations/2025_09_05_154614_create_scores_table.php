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
        Schema::create('scores', function (Blueprint $table) {
            $table->id();
            $table->integer('market_score');
            $table->integer('competition_score');
            $table->integer('problem_score');
            $table->integer('business_model_score');
            $table->integer('solution_score');
            $table->integer('GTM_strategy_score');
            $table->integer('traction_score');
            $table->integer('team_score');
            $table->integer('product_tech_stack_score');
            $table->integer('traction_results_score');
            $table->integer('financials_investment_score');
            $table->integer('overall_score');
            $table->foreignId('pitch_id')->constrained();
            $table->foreignId('pitch_text_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scores');
    }
};
