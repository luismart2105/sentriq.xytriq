<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotes', function (Blueprint $table): void {
            $table->string('signing_token', 64)->nullable()->unique()->after('signature_path');
            $table->string('client_signer_name')->nullable()->after('signing_token');
            $table->string('client_signature_path')->nullable()->after('client_signer_name');
            $table->timestamp('signed_at')->nullable()->after('client_signature_path');
            $table->string('signed_ip', 45)->nullable()->after('signed_at');
            $table->text('signed_user_agent')->nullable()->after('signed_ip');
        });
    }

    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table): void {
            $table->dropUnique(['signing_token']);
            $table->dropColumn(['signing_token', 'client_signer_name', 'client_signature_path', 'signed_at', 'signed_ip', 'signed_user_agent']);
        });
    }
};
