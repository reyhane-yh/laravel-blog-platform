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
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
        });

        // post_tags table
        Schema::create('post_tags', function (Blueprint $table) {
            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('tag_id');

            $table->foreign('post_id')
                ->references('id')->on('posts')->onDelete('cascade');

            $table->foreign('tag_id'
            )->references('id')->on('tags')->onDelete('cascade');

            $table->primary(['post_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_tags');
        Schema::dropIfExists('tags');
    }
};
