<?php


namespace Pete\WordPressPlusLaravel\Http;

use Pete\WordPressPlusLaravel\Todo;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Site;
use Input;

class WordPressPlusLaravelController extends Controller
{
  	
	public function create(){
		
		$num = substr(PHP_VERSION, 0, 3);
		$float_version = (float)$num;
		
		if($float_version < 7.1){
        	return redirect('sites/create')->withErrors("The PHP version must be >= 7.1 to activate WordPress Plus Laravel functionality.");
		}
		
		$viewsw = "/wordpress_plus_laravel";
		return view("wordpress-plus-laravel-plugin::create")->with('viewsw',$viewsw);
	}
	
	
	public function index()
	{
		$user = Auth::user();
		$viewsw = "/wordpress_plus_laravel";
		$sites = $user->my_sites()->where("app_name","WordPressPlusLaravel")->paginate(10);
		
		$success = Input::get('success');
		$site_id = Input::get('site_id');
		return view('wordpress-plus-laravel-plugin::index', compact('sites','success','site_id','viewsw'));
	}
	
	
}
