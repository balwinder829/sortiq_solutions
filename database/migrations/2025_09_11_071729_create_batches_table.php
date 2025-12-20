<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_name');
            $table->string('session_name');  // Can also reference a sessions table if you have one
            $table->time('start_time');
            $table->time('end_time');
            $table->string('department');
            $table->string('batch_assign');
            $table->string('class_assign');
            $table->string('duration'); // store as string or integer
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
