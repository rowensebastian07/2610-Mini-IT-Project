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

            /** * Category: Using string instead of enum for flexibility.
             * This allows us to add new Enum cases in PHP without needing to 
             * run a database migration every time.
             */
            $table->string('category')->default(ClubCategory::ART->value); 
            $table->string('profile_picture')->nullable();
            $table->string('email')->nullable();
            $table->string('banner_image')->nullable();

            // Ownership Logic:
            $table->foreignId('owner_id')
                  ->nullable() // nullable(): Allows the club to exist even if the owner is gone.
                  ->constrained('users') // constrained('users'): Links this to the 'id' on the 'users' table.
                  ->nullOnDelete(); // nullOnDelete(): If the student deletes their account, the club 
            $table->softDeletes(); // Allows "restoring" a club if deleted by mistake
            $table->timestamps();  // Automatically creates created_at and updated_at
            // remains active but the owner_id becomes NULL.
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