<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            // Kolom ini sudah ada di $fillable App\Models\ActivityLog dan dipakai oleh
            // User::createApiToken()/revokeAllTokens(), tapi tabelnya sendiri tidak
            // pernah punya kolom ini — insert lewat method tersebut akan gagal
            // (Unknown column 'description') begitu dipakai.
            $table->text('description')->nullable()->after('new_data');
        });
    }

    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};
