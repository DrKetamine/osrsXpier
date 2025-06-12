<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActionsTable extends Migration
{
    public function up()
    {
        Schema::create('actions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('image')->nullable();
            $table->integer('level')->nullable();
            $table->integer('xp')->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('buy', 12, 2)->nullable();
            $table->decimal('sell', 12, 2)->nullable();
            $table->decimal('margin', 12, 2)->nullable();
            $table->decimal('margin_percent', 5, 2)->nullable();
            $table->boolean('members_only')->default(false);
            $table->timestamps();
        });

        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('action_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('image')->nullable();
            $table->integer('quantity')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ingredients');
        Schema::dropIfExists('actions');
    }
}
