<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function csv(Request $r): StreamedResponse
    {
        $from     = $r->query('from');
        $to       = $r->query('to');
        $shift_id = $r->query('shift_id');

        $query = Booking::with(['user','shift'])->orderBy('date')->orderBy('shift_id');
        if ($from) $query->whereDate('date', '>=', $from);
        if ($to)   $query->whereDate('date', '<=', $to);
        if ($shift_id) $query->where('shift_id', $shift_id);

        $filename = 'buchungen_'.($from ?? 'alle').'_'.($to ?? 'alle').'.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        return response()->stream(function() use ($query) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Datum','Schicht','Name','E-Mail','Angelegt am']);

            $query->chunk(500, function($rows) use ($out) {
                foreach ($rows as $b) {
                    fputcsv($out, [
                        $b->date,
                        $b->shift?->name,
                        $b->user?->name,
                        $b->user?->email,
                        optional($b->created_at)->toDateTimeString(),
                    ]);
                }
            });
            fclose($out);
        }, 200, $headers);
    }

    public function userCsv(Request $r): StreamedResponse
    {
        $guid = $r->query('guid');
        abort_unless($guid, 400, 'guid erforderlich');

        $user = User::where('guid', $guid)->firstOrFail();

        $query = Booking::with(['shift'])
            ->where('user_id', $user->id)
            ->orderBy('date')
            ->orderBy('shift_id');

        $filename = 'buchungen_'.$user->name.'_'.now()->format('Ymd_His').'.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        return response()->stream(function() use ($query, $user) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Name', $user->name]);
            fputcsv($out, ['GUID', $user->guid]);
            fputcsv($out, []);
            fputcsv($out, ['Datum','Schicht','Erstellt am']);

            $query->chunk(500, function($rows) use ($out) {
                foreach ($rows as $b) {
                    fputcsv($out, [
                        $b->date,
                        $b->shift?->name,
                        optional($b->created_at)->toDateTimeString(),
                    ]);
                }
            });
            fclose($out);
        }, 200, $headers);
    }

    public function weekData(Carbon $monday): array
    {
        $sunday = (clone $monday)->endOfWeek(Carbon::SUNDAY);

        $days = collect();
        for ($d = 0; $d < 7; $d++) {
            $days->push((clone $monday)->addDays($d));
        }

        $shifts = Shift::orderBy('id')->get();
        $bookings = Booking::with(['user','shift'])
            ->whereBetween('date', [$monday->toDateString(), $sunday->toDateString()])
            ->get()
            ->groupBy(fn($b) => $b->date.'#'.$b->shift_id);

        return compact('monday','sunday','days','shifts','bookings');
    }

    public function weekPdf(Request $r)
    {
        $anyDate = $r->query('week') ? Carbon::parse($r->query('week')) : Carbon::today();
        $monday  = (clone $anyDate)->startOfWeek(Carbon::MONDAY);

        $data = $this->weekData($monday);

        $pdf = app('dompdf.wrapper')->loadView('admin.export.week', $data)->setPaper('A4', 'landscape');
        $filename = 'wochenuebersicht_'.$monday->toDateString().'.pdf';
        return $pdf->download($filename);
    }
}
