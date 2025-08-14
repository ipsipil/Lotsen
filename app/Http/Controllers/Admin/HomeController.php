<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function loginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate(['key' => 'required']);
        if (hash_equals((string) env('ADMIN_KEY'), (string) $request->key)) {
            $request->session()->put('admin_ok', true);
            return redirect()->to('/admin')->with('ok', 'Erfolgreich angemeldet.');
        }
        return back()->withErrors(['key' => 'Ungültiger Admin-Key']);
    }

    public function logout(Request $request)
    {
        $request->session()->forget('admin_ok');
        return redirect()->to('/admin/login')->with('ok', 'Abgemeldet.');
    }

    public function index()
    {
        return view('admin.dashboard');
    }
}
