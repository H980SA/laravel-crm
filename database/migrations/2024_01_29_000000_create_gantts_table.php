<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGanttsTable extends Migration
{
    public function up()
    {
        Schema::create('gantts', function (Blueprint $table) {
            $table->id();
            $table->string('text');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('duration')->nullable();
            $table->decimal('progress', 5, 2)->default(0);
            $table->enum('priority', ['Baja', 'Media', 'Alta'])->default('Media');  // Mejor usar enum
            $table->boolean('is_parent')->default(false);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedInteger('lead_id')->nullable();
            $table->timestamps();

            // Relación de claves foráneas
            $table->foreign('parent_id')->references('id')->on('gantts')->onDelete('cascade');
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('set null');
            
            // Índices adicionales para claves foráneas
            $table->index('parent_id');
            $table->index('lead_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('gantts');
    }
}
