<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;

class AdminCalendarController extends Controller
{
    public function events(Request $request)
    {
        $events = Booking::with('shift','user')->get()->map(function($b){
            return [
                'id'       => $b->id,
                'title'    => $b->shift->name.' – '.($b->user->name ?? '—'),
                'start'    => $b->date,
                'allDay'   => true,
                'color'    => $b->shift->color ?: '#6c757d',
                'user_id'  => $b->user_id,
                'shift_id' => $b->shift_id,
            ];
        });
        return response()->json($events);
    }

    public function book(Request $r)
    {
        $r->validate([
            'date'     => 'required|date',
            'shift_id' => 'required|exists:shifts,id',
            'user_id'  => 'required|exists:users,id',
        ]);

        $date = Carbon::parse($r->date)->toDateString();

        // Nur eine Buchung je Datum+Schicht
        if (Booking::where('shift_id',$r->shift_id)->whereDate('date',$date)->exists()) {
            return response('Diese Schicht ist an diesem Tag bereits belegt.', 422);
        }

        $b = Booking::create([
            'user_id'  => $r->user_id,
            'shift_id' => $r->shift_id,
            'date'     => $date
        ]);

        return response()->json(['ok' => true, 'id' => $b->id], 201);
    }

    public function delete($id)
    {
        Booking::findOrFail($id)->delete();
        return response()->json(['ok' => true]);
    }
}
