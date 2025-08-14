<?php
namespace App\Http\Middleware;

use Closure;
use App\Models\User;

class GuidAuth
{
    public function handle($request, Closure $next)
    {
        $guid = $request->route('guid');
        if (!$guid) abort(403, 'Kein GUID angegeben');

        $user = User::where('guid', $guid)->firstOrFail();

        // Nur wenn noch nicht (dieser) User eingeloggt ist
        if (!auth()->check() || auth()->id() !== $user->id) {
            auth()->login($user);

            // Wichtig: Session nur bei GET rotieren, damit CSRF stabil bleibt
            if ($request->isMethod('GET')) {
                $request->session()->regenerate(); // oder ->migrate(true)
            }
        }

        return $next($request);
    }
}
