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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_name');
            $table->text('description');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable()->after('start_date');
            $table->enum('status', ['Active', 'Archived', 'Completed'])->default('Active')->after('end_date');
            //$table->foreignId('team_id')->nullable()->constrained();
            $table->foreignId('client_id')->nullable()->constrained();
            $table->softDeletes(); // adds deleted_at column
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
       
    }
};
