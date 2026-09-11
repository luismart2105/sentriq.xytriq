<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotes', function (Blueprint $table): void {
            $table->unsignedSmallInteger('equipment_warranty_duration')->default(1)->after('installation_amount');
            $table->string('equipment_warranty_unit', 10)->default('years')->after('equipment_warranty_duration');
            $table->unsignedSmallInteger('installation_warranty_duration')->default(2)->after('equipment_warranty');
            $table->string('installation_warranty_unit', 10)->default('years')->after('installation_warranty_duration');
        });
    }

    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table): void {
            $table->dropColumn([
                'equipment_warranty_duration', 'equipment_warranty_unit',
                'installation_warranty_duration', 'installation_warranty_unit',
            ]);
        });
    }
};
