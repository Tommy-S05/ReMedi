<?php

declare(strict_types=1);

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
        Schema::create('resource_shares', function (Blueprint $table) {
            $table->id();

            // Who shares the resource
            $table->foreignId('owner_user_id')->constrained('users')->cascadeOnDelete();

            // Who is shared with (can be null until the invitation is accepted)
            $table->foreignId('shared_with_user_id')->nullable()->constrained('users')->cascadeOnDelete();

            // Email to which the invitation was sent
            $table->string('shared_with_email');

            // Columns for polymorphic relationship (what is being shared)
            $table->morphs('shareable'); // This creates shareable_id (unsignedBigInteger) and shareable_type (string)

            $table->string('status')->default('pending');
            $table->string('token')->unique();
            $table->timestamp('expires_at')->nullable()->index();

            $table->index(['status', 'expires_at']);
            $table->index(['shared_with_email', 'status']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_shares');
    }
};
