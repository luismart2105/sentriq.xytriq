<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kits', function (Blueprint $table) {
            $table->string('pricing_type', 16)->default('fixed')->after('service_slug');
            $table->decimal('price', 10, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        if (DB::table('kits')->whereNull('price')->exists()) {
            throw new RuntimeException('No se puede revertir mientras existan opciones que requieren cotización sin precio.');
        }

        Schema::table('kits', function (Blueprint $table) {
            $table->dropColumn('pricing_type');
            $table->decimal('price', 10, 2)->nullable(false)->change();
        });
    }
};
