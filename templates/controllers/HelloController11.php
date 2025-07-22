<?php

//lll
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use View;

class HelloController extends Controller
{

    public static function middleware(): array
    {
        // Runs on all actions.  You could also limit it with only:/except:
        return [
            'auth.wp',
            // new RouteMiddleware('auth.wp', only: ['list_users', 'list_posts']),
        ];
    }

    public function __construct()
    {
        

        $public_path = base_path();
        $app_route   = explode("/", $public_path);
        $app_route   = "/".$app_route[array_key_last($app_route)];

        View::share(compact('app_route'));
    }

    public function wordpress_plus_laravel_examples()
    {
        return view('wordpress_plus_laravel_examples');
    }

    public function list_users(Request $request)
    {
        $users = $this->fetchFromWp($request, 'users');
        return view('list_users', compact('users'));
    }

    public function list_posts(Request $request)
    {
        $posts = $this->fetchFromWp($request, 'posts');
        return view('list_posts', compact('posts'));
    }

    public function list_products(Request $request)
    {
        $products = $this->fetchFromWp($request, 'products');
        return view('list_products', compact('products'));
    }

    public function list_orders(Request $request)
    {
        $orders = $this->fetchFromWp($request, 'orders');
        return view('list_orders', compact('orders'));
    }

    protected function fetchFromWp(Request $request, string $resource): array
    {
        $cookie = $request->header('Cookie', '');
        $wpUrl  = rtrim(env('WP_URL'), '/');
        $url    = "{$wpUrl}/wp-json/pete/v1/{$resource}";

        $response = Http::withHeaders([
            'Cookie' => $cookie,
        ])->get($url);

        if (! $response->successful()) {
            $loginUrl = "{$wpUrl}/wp-login.php?redirect_to=" . urlencode(url()->full());
            Redirect::away($loginUrl)->send();
            exit; // asegurarnos de no seguir ejecutando
        }

        return $response->json();
    }
}
