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
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('mailgun_api_key')->nullable();
            $table->string('stripe_api_key')->nullable();
            $table->string('admin_email')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn('mailgun_api_key');
            $table->dropColumn('stripe_api_key');
            $table->dropColumn('admin_email');
        });
    }
}; 