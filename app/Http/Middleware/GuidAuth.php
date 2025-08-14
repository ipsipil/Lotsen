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

        $user = User::where('guid', $guid)->first();
        if (!$user) abort(403, 'Ungültiger Link');

        auth()->login($user);
        return $next($request);
    }
}
