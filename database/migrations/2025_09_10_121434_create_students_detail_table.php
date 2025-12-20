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
        Schema::create('students_detail', function (Blueprint $table) {
            $table->id();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('student_name');
            $table->string('f_name');
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable();
            $table->string('contact', 15)->nullable();
            $table->string('email_id')->unique();
            $table->string('college_name')->nullable();
            $table->string('duration')->nullable();
            $table->string('technology')->nullable();
            $table->string('session')->nullable();
            $table->decimal('total_fees', 10, 2)->default(0);
            $table->decimal('reg_fees', 10, 2)->default(0);
            $table->decimal('pending_fees', 10, 2)->default(0);
            $table->string('department')->nullable();
            $table->date('join_date')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->integer('email_count_confirmation')->default(0);
            $table->integer('email_count_certificate')->default(0);
            $table->string('batch_assign')->nullable();
            $table->decimal('reg_due_amount', 10, 2)->default(0);
            $table->string('reference')->nullable();
            $table->date('due_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students_detail');
    }
};
