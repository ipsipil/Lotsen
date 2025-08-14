<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('shifts', function (Blueprint $table) {
            $table->string('color', 7)->nullable()->after('name'); // z.B. #007bff
        });
    }
    public function down(): void {
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
};
