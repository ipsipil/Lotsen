<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;
use App\Notifications\UserInvited;

class UserAdminController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $r)
    {
        $r->validate([
            'name'  => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'send'  => 'nullable|boolean',
        ]);

        $user = User::firstOrCreate(
            ['name' => $r->name, 'email' => $r->email],
            ['guid' => (string) Str::uuid()]
        );
        if (!$user->guid) { $user->guid = (string) Str::uuid(); $user->save(); }

        if ($r->boolean('send') && $user->email) {
            $user->notify(new UserInvited($user));
        }

        return redirect()->to('/admin/users')->with('ok', 'Nutzer angelegt'.($r->boolean('send') && $user->email ? ' & Einladung gesendet.' : '.'));
    }

    public function resend($id)
    {
        $user = User::findOrFail($id);
        if (!$user->guid) { $user->guid = (string) Str::uuid(); $user->save(); }
        if ($user->email) {
            $user->notify(new UserInvited($user));
            return back()->with('ok', 'Einladung erneut gesendet an '.$user->email);
        }
        return back()->with('ok', 'Kein E‑Mail hinterlegt – Link manuell kopieren.');
    }

    public function import(Request $r)
    {
        $r->validate([
            'csv'   => 'required|file|mimes:csv,txt',
            'send'  => 'nullable|boolean',
        ]);
        $path = $r->file('csv')->getRealPath();
        $fh = fopen($path, 'r');
        $header = fgetcsv($fh);
        if (!$header) return back()->withErrors(['csv' => 'Leere CSV/kein Header. Erwartet: name,email']);

        $map = array_flip(array_map(fn($h)=>strtolower(trim($h)), $header));
        $created = 0; $mailed = 0;

        while (($row = fgetcsv($fh)) !== false) {
            $name  = $row[$map['name']]  ?? null;
            $email = $row[$map['email']] ?? null;
            if (!$name) continue;

            $user = User::firstOrCreate(
                ['name' => $name, 'email' => $email],
                ['guid' => (string) Str::uuid()]
            );
            if (!$user->guid) { $user->guid = (string) Str::uuid(); $user->save(); }
            $created++;

            if ($r->boolean('send') && $user->email) {
                $user->notify(new UserInvited($user));
                $mailed++;
            }
        }
        fclose($fh);

        return back()->with('ok', "Import fertig: {$created} Nutzer, {$mailed} Einladungen gesendet.");
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        // Optional: auch Buchungen löschen? Dann onDelete('cascade') ist an der bookings.user_id FK bereits aktiv.
        $user->delete();
        return back()->with('ok', 'Nutzer gelöscht.');
    }
}
