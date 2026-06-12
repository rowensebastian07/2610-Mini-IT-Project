<?php

use App\Enums\ClubCategory;
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
        Schema::create('clubs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();

            /**
             * Category: Using string instead of enum for flexibility.
             * This allows adding new Enum cases in PHP without needing
             * a database migration every time.
             */
            $table->string('category')->default(ClubCategory::ART->value);

            // Media & branding
            $table->string('profile_picture')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('theme')->default('Default');
            $table->string('committee_background')->nullable(); 
            $table->string('committee_theme')->default('white'); 
            
            // Contact & registration
            $table->string('email')->nullable();
            $table->string('instagram')->nullable();
            $table->string('website')->nullable();
            $table->string('registration_link')->nullable();
            $table->boolean('registration_open')->default(false);

            // Ownership logic
            $table->foreignId('owner_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // Club Verification
            $table->boolean('is_Verified')->default(false);

            // Soft delete + timestamps
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clubs');
    }
};
