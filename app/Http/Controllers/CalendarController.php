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

        $events = Booking::with('shift','user')->get()->map(function($b) use ($user) {
            return [
                'id'       => $b->id,
                'title'    => $b->shift->name,
                'start'    => $b->date,
                'allDay'   => true,
                'color'    => $b->user_id === $user->id ? '#007bff' : '#dc3545',
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
