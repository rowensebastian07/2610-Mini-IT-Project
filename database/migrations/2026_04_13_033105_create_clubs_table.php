<?php

use App\Enums\ClubCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clubs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('category', array_column(ClubCategory::cases(), 'value'))
                  ->default(ClubCategory::STUDENT->value);
             $table->foreignId('owner_id')
                    ->nullable() // 1. Must be nullable to allow the user to disappear
                    ->constrained('users')
                    ->nullOnDelete(); // 2. Sets owner_id to NULL instead of deleting the club // nullOnDelete() make sure the club doesn't die if the owner delete his account
            $table->softDeletes();  // softDeletes also in Models\Club to avoid club owner accidentally delete the 
                                    // club including the post and member
            $table->timestamps();
            $table->string('profile_picture')->default('images/1.png');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clubs');
    }
};

