<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kits', function (Blueprint $table) {
            $table->string('price_label', 80)->nullable();
            $table->text('conditions')->nullable();
            $table->string('image_path')->nullable();
            $table->string('image_caption')->nullable();
            $table->string('cabinet_image_path')->nullable();
            $table->string('cabinet_image_caption')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('kits', function (Blueprint $table) {
            $table->dropColumn(['price_label', 'conditions', 'image_path', 'image_caption', 'cabinet_image_path', 'cabinet_image_caption']);
        });
    }
};
