<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Admin\ExportController;

class SendNextWeekRoster extends Command
{
    protected $signature = 'lotsen:send-next-week';
    protected $description = 'Erzeugt die Wochenübersicht für die nächste Woche und sendet sie per E-Mail an die Koordination.';

    public function handle()
    {
        $adminEmail = env('ADMIN_EMAIL');
        if (!$adminEmail) {
            $this->warn('ADMIN_EMAIL nicht gesetzt – Abbruch.');
            return Command::FAILURE;
        }

        $today  = Carbon::today();
        $monday = (clone $today)->addWeek()->startOfWeek(Carbon::MONDAY);

        $export = app(ExportController::class);
        $data   = $export->weekData($monday);

        $pdf = app('dompdf.wrapper')->loadView('admin.export.week', $data)->setPaper('A4', 'landscape');
        $filename = 'wochenuebersicht_'.$monday->toDateString().'.pdf';
        $binary   = $pdf->output();

        Mail::raw(
            'Anbei die Schülerlotsen-Wochenübersicht für '.$data['monday']->format('d.m.Y').' – '.$data['sunday']->format('d.m.Y'),
            function($message) use ($adminEmail, $binary, $filename) {
                $message->to($adminEmail)->subject('[Lotsen] Wochenübersicht '.$filename)->attachData($binary, $filename, ['mime' => 'application/pdf']);
            }
        );

        $this->info('Wochenübersicht an '.$adminEmail.' verschickt: '.$filename);
        return Command::SUCCESS;
    }
}
