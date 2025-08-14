<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shift;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Früh','Mittel','Spät'] as $n) {
            Shift::firstOrCreate(['name' => $n]);
        }
    }
}
