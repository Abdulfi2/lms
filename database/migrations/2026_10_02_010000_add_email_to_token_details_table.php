<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('token_details', function (Blueprint $table) {
            $table->string('email')->nullable()->after('model_token');
        });
    }

    public function down(): void
    {
        Schema::table('token_details', function (Blueprint $table) {
            $table->dropColumn('email');
        });
    }
};
