<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feedback', function (Blueprint $table) {
          
            $table->text('content')->nullable()->after('pdf_path');
            $table->json('ai_response')->nullable()->after('content');
            $table->timestamp('analysis_date')->nullable()->after('ai_response');
            
           
            $table->string('pdf_path')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('feedback', function (Blueprint $table) {
            $table->dropColumn(['content', 'ai_response', 'analysis_date']);
            $table->string('pdf_path')->nullable(false)->change();
        });
    }
};