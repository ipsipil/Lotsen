<?php
namespace App\Http\Middleware;

use Closure;

class AdminSecret {
    public function handle($request, Closure $next) {
        if (session('admin_ok') === true) return $next($request);
        $keyExpected = env('ADMIN_KEY');
        $provided = $request->header('X-ADMIN-KEY') ?? $request->query('key');
        if ($keyExpected && $provided && hash_equals($keyExpected, $provided)) {
            session(['admin_ok' => true]);
            return $next($request);
        }
        if ($request->expectsJson() || $request->wantsJson()) abort(403, 'Admin-Schlüssel fehlt/ungültig');
        return redirect()->to('/admin/login');
    }
}
