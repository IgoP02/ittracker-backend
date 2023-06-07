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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId("department_id");
            $table->foreignId("issue_id");
            $table->tinyInteger("priority");
            $table->string("status", 1);
            $table->text("description");
            $table->timestamps();

            $table->foreign("department_id")->references("id")->on("departments");
            $table->foreign("issue_id")->references("id")->on("issues");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
