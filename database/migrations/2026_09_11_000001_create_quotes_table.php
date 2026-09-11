<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table): void {
            $table->id();
            $table->string('number')->unique();
            $table->string('client_name');
            $table->date('quote_date');
            $table->unsignedSmallInteger('validity_days')->default(15);
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('items');
            $table->decimal('installation_amount', 12, 2)->default(0);
            $table->text('equipment_warranty')->nullable();
            $table->text('installation_warranty')->nullable();
            $table->text('installation_scope')->nullable();
            $table->text('project_considerations')->nullable();
            $table->string('project_manager')->nullable();
            $table->string('status')->default('draft')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
