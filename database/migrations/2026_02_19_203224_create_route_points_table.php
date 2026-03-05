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
        Schema::create('route_points', function (Blueprint $table) {

            $table->id();

            $table->foreignId('route_id')
                ->constrained()
                ->onDelete('cascade');

            $table->string('name');

            $table->enum('type', [
                'start',
                'middle',
                'end'
            ]);

            $table->string('address')->nullable();

            $table->decimal('lat', 10, 8);
            $table->decimal('lng', 11, 8);

            $table->text('description')->nullable();

            $table->string('image')->nullable();

            $table->integer('order_index')->default(0);

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('route_points');
    }
};
