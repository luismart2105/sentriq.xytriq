<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prospects', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->nullable();
            $table->string('business')->nullable();
            $table->string('phone', 40)->nullable()->index();
            $table->string('email')->nullable()->index();
            $table->string('service_interest')->nullable()->index();
            $table->string('municipality', 120)->nullable();
            $table->text('description')->nullable();
            $table->string('source', 40)->index();
            $table->string('campaign')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_term')->nullable();
            $table->string('utm_content')->nullable();
            $table->string('landing_page', 500)->nullable();
            $table->string('referrer', 500)->nullable();
            $table->string('stage', 40)->default('new')->index();
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('next_follow_up_at')->nullable()->index();
            $table->dateTime('last_contact_at')->nullable();
            $table->timestamps();
        });

        Schema::create('prospect_activities', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('prospect_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 40)->index();
            $table->text('summary');
            $table->string('old_stage', 40)->nullable();
            $table->string('new_stage', 40)->nullable()->index();
            $table->string('next_action')->nullable();
            $table->dateTime('next_action_at')->nullable();
            $table->dateTime('happened_at')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prospect_activities');
        Schema::dropIfExists('prospects');
    }
};
