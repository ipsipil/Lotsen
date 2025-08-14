<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Shift;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Notifications\BookingConfirmed;
use App\Notifications\BookingCancelled;

class DashboardController extends Controller
{

    public function index(Request $request)
    {
        $user = auth()->user();
        $shifts = Shift::orderBy('id')->get();
        return view('dashboard', compact('user','shifts'));
    }

    public function book(Request $request)
    {
        $request->validate([
            'shift_id' => 'required|exists:shifts,id',
            'date'     => 'required|date'
        ]);

        $date = Carbon::parse($request->date);

        if ($date->isWeekend()) {
            return response('Wochenenden sind nicht buchbar.', 422);
        }

        $isHoliday = Holiday::whereDate('start_date', '<=', $date->toDateString())
            ->whereDate('end_date', '>=', $date->toDateString())
            ->exists();
        if ($isHoliday) {
            return response('Ferien sind nicht buchbar.', 422);
        }

        $exists = Booking::where('shift_id', $request->shift_id)
            ->whereDate('date', $date->toDateString())
            ->exists();
        if ($exists) {
            return response('Diese Schicht ist an diesem Tag bereits belegt.', 422);
        }

        $booking = Booking::create([
            'user_id'  => auth()->id(),
            'shift_id' => $request->shift_id,
            'date'     => $date->toDateString()
        ]);

        $user = auth()->user();
        if ($user->email) $user->notify(new BookingConfirmed($booking));

        if (env('ADMIN_EMAIL')) {
            Mail::raw(
                $user->name.' hat gebucht: '.$booking->date.' | '.$booking->shift->name,
                function($message) use ($user) {
                    $message->to(env('ADMIN_EMAIL'))
                            ->subject('[Lotsen] Neue Buchung');
                }
            );
        }

        return response('Buchung erfolgreich', 201);
    }

    public function delete($guid, $id)
    {
        $booking = Booking::with('shift','user')->findOrFail($id);
        if ($booking->user_id !== auth()->id()) abort(403, 'Keine Berechtigung');

        $user = auth()->user();
        $booking->delete();

        if ($user->email) $user->notify(new BookingCancelled($booking));

        if (env('ADMIN_EMAIL')) {
            Mail::raw(
                $user->name.' hat storniert: '.$booking->date.' | '.$booking->shift->name,
                function($message) {
                    $message->to(env('ADMIN_EMAIL'))
                            ->subject('[Lotsen] Buchung storniert');
                }
            );
        }

        return back()->with('success', 'Buchung gelöscht');
    }
}
