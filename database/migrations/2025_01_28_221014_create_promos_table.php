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
        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('text');
            $table->string('status');
            $table->string('url');
            $table->integer('view_counts');
            $table->decimal('cpm', total: 10, places: 2);
            $table->decimal('amount', total: 10, places: 2);
            $table->string('button_text');
            $table->foreignId('company_id')->index()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promos');
    }
};
