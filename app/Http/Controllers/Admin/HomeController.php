<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shift; // <-- hinzufügen

class HomeController extends Controller
{
    public function loginForm() { return view('admin.login'); }

    public function login(Request $request) {
        $request->validate(['key' => 'required']);
        if (hash_equals((string)env('ADMIN_KEY'), (string)$request->key)) {
            $request->session()->put('admin_ok', true);
            return redirect('/admin')->with('ok', 'Erfolgreich angemeldet.');
        }
        return back()->withErrors(['key' => 'Ungültiger Admin-Key']);
    }

    public function logout(Request $request) {
        $request->session()->forget('admin_ok');
        return redirect('/admin/login')->with('ok', 'Abgemeldet.');
    }

    public function index() {
        $shifts = Shift::orderBy('id')->get();   // <-- Schichten laden
        return view('admin.dashboard', compact('shifts'));
    }
}
