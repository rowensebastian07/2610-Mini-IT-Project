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

            $table->string('category') ->default(ClubCategory::ART->value); 

            $table->string('profile_picture')->nullable();

            $table->foreignId('owner_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();
            $table->string('profile_picture')->default('images/1.png');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clubs');
    }
};
