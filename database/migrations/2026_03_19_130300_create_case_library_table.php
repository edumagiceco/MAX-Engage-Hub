<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('case_library', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('industry')->nullable()->index();
            $table->string('department')->nullable()->index();
            $table->string('problem');
            $table->string('solution_type', 50)->index();
            $table->text('summary');
            $table->text('outcome')->nullable();
            $table->json('metrics_json')->nullable();
            $table->string('status', 30)->default('approved')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_library');
    }
};
