<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Holiday;

class CalendarController extends Controller
{
    public function events(Request $request)
    {
        $user = auth()->user();

        $events = \App\Models\Booking::with('shift','user')->get()->map(function($b) use ($user) {
            $title = $b->shift->name . ' – ' . ($b->user->name ?? '—');
            // Farbe: bevorzugt Schicht-Farbe; eigene Buchung optional mit Rand hervorheben
            $color = $b->shift->color ?: ($b->user_id === $user->id ? '#007bff' : '#dc3545');

            return [
                'id'       => $b->id,
                'title'    => $title,
                'start'    => $b->date,
                'allDay'   => true,
                'color'    => $color,
                'user_id'  => $b->user_id,
                'shift_id' => $b->shift_id,
            ];
        });

        return response()->json($events);
    }


    public function holidays(Request $request)
    {
        $events = Holiday::all()->map(function($h) {
            $endExclusive = \Carbon\Carbon::parse($h->end_date)->addDay()->toDateString();
            return [
                'title'   => $h->name,
                'start'   => $h->start_date,
                'end'     => $endExclusive,
                'display' => 'background',
                'overlap' => false,
            ];
        });
        return response()->json($events);
    }
}
