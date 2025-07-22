<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WPAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $cookieHeader = $request->header('Cookie', '');
        $wpSite = env('WP_URL');
        //$wpSite   = config('services.wp.url');
        $endpoint = "{$wpSite}/wp-json/pete/v1/is-logged-in";
        $loginUrl = "{$wpSite}/wp-login.php?redirect_to=" . urlencode(url()->full());

        $response = Http::withHeaders([
            'Cookie' => $cookieHeader,
        ])->get($endpoint);

        if (! $response->ok()) {
            //abort(502, 'Cannot reach WordPress for auth check.');
            return redirect()->away($loginUrl);
        }

        $wp = $response->json();

        if (empty($wp['logged_in'])) {
            return redirect()->away($loginUrl);
        }

        $request->attributes->set('wp_user', $wp);

        return $next($request);
    }
}
