<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organizer_id');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->enum('type', ['webinar', 'workshop', 'parenting', 'live_class', 'zoom_meeting', 'seminar']);
            $table->string('image')->nullable();
            $table->enum('category', ['education', 'parenting', 'technology', 'business', 'health', 'other']);
            $table->string('speaker')->nullable();
            $table->string('speaker_bio')->nullable();
            $table->string('speaker_photo')->nullable();
            $table->string('location')->nullable(); // untuk offline event
            $table->string('zoom_link')->nullable(); // untuk online event
            $table->string('meeting_id')->nullable();
            $table->string('passcode')->nullable();
            $table->timestamp('start_time');
            $table->timestamp('end_time')->nullable();
            $table->integer('max_participants')->nullable();
            $table->enum('price_type', ['free', 'paid'])->default('free');
            $table->decimal('price', 12, 2)->default(0);
            $table->enum('status', ['draft', 'published', 'cancelled', 'completed'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->integer('total_registrations')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('organizer_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('slug');
            $table->index('status');
            $table->index('start_time');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};