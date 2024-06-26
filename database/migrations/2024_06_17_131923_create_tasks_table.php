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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('sonumb');
            $table->string('site_name');
            $table->string('site_id');
            $table->string('operator');
            $table->string('tower_type')->nullable();
            $table->string('regency');
            $table->date('inviting_date')->nullable();
            $table->date('atp_date')->nullable();
            $table->string('file')->nullable();
            $table->string('note')->nullable();
            $table->string('status')->default('invitation');
            $table->bigInteger('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
