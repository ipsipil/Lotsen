<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'guid')) {
                $table->uuid('guid')->unique()->nullable();
            }
            if (Schema::hasColumn('users', 'password')) {
                $table->string('password')->nullable()->change();
            }
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'guid')) $table->dropColumn('guid');
        });
    }
};
