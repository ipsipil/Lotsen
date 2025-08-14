<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('shift_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->timestamps();
        });
        // Unique: pro Tag und Schicht nur 1 Buchung
        Schema::table('bookings', function (Blueprint $table) {
            $table->unique(['shift_id','date']);
        });
    }
    public function down(): void { Schema::dropIfExists('bookings'); }
};
