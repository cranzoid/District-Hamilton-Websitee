<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('section', 20)->default('food')->after('slug')->index();
        });

        Schema::create('daily_offers', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('day_of_week')->comment('0=Sunday ... 6=Saturday (Carbon::dayOfWeek)');
            $table->string('section', 20)->default('food');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('price_display', 64)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['day_of_week', 'section']);
            $table->index(['is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_offers');

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['section']);
            $table->dropColumn('section');
        });
    }
};
