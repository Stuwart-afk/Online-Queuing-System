<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queue_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('student_id')->nullable()->constrained('students')->onDelete('set null');
            $table->foreignUuid('transaction_request_id')->nullable()->constrained('transaction_requests')->onDelete('set null');
            $table->string('name');
            $table->string('tracking_number');
            $table->string('device_id')->nullable();
            $table->string('platform')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('status')->default('holding'); // holding, active, serving, completed, held
            $table->string('assigned_teller')->nullable();
            $table->date('queue_date');
            $table->uuid('access_token')->unique();
             $table->integer('last_notified_position')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queue_tickets');
    }
};