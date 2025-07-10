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
        Schema::create('actionplans', function (Blueprint $table) {
            $table->id();
            //FK to plants table
            $table->foreignId('plant_id')->constrained('plants')->onDelete('cascade');
            //FK to users table for the lider of the action plan
            $table->foreignId('leader_id')->constrained('users')->onDelete('cascade');
            //FK to users table for the user who is assigned to the action plan
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            //Fk to users table for the user who created the action plan
            $table->foreignId('created_id')->constrained('users')->onDelete('cascade');
            
            //aperture date
            $table->date('date_start')->nullable();
            //closure date
            $table->date('date_close')->nullable();
            //description of the action plan
            $table->text('description')->nullable();
            $table->text('indicator')->nullable();
            //status of the action plan
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            //priority of the action plan
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');

            //coments from other areas
            $table->text('comments')->nullable();
            //observations from the leader
            $table->text('observations')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actionplans');
    }
};
