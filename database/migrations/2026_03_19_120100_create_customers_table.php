<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone', 30)->nullable();
            $table->string('company_name')->nullable();
            $table->string('job_title')->nullable();
            $table->boolean('consent_marketing')->default(false);
            $table->string('latest_source', 50)->nullable();
            $table->string('status', 30)->default('new')->index();
            $table->timestamps();

            $table->index('company_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
