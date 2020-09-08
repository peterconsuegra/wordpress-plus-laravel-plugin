<?php


namespace Pete\WordPressPlusLaravel\Http;

use Pete\WordPressPlusLaravel\Todo;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Site;
use Input;
use Illuminate\Http\Request;
use App\PeteOption;
use Validator;
use Illuminate\Support\Facades\Redirect;
use View;
use Log;

class WordPressPlusLaravelController extends Controller
{
	
	public function __construct(Request $request){
	    
	    $this->middleware('auth');
		$dashboard_url = env("DASHBOARD_URL");
		$viewsw = "/wordpress_plus_laravel";
		
		//DEBUGING PARAMS
		$debug = env('DEBUG');
		if($debug == "active"){
			$inputs = $request->all();
			Log::info($inputs);
		}
		
		$system_vars = parent::__construct();
		$pete_options = $system_vars["pete_options"];
		$sidebar_options = $system_vars["sidebar_options"];
		$current_user = Auth::user(); 
		View::share(compact('dashboard_url','viewsw','pete_options','system_vars','sidebar_options','current_user'));
		   
	}
  	
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
		$sites = $user->my_sites()->where("app_name","WordPressPlusLaravel")->orWhere('app_name', 'WordPress+Laravel')->whereNull('deleted_at')->paginate(10);
		
		$success = Input::get('success');
		$site_id = Input::get('site_id');
		return view('wordpress-plus-laravel-plugin::index', compact('sites','success','site_id','viewsw'));
	}
	
	public function store(Request $request)
	{
		$pete_options = new PeteOption();
		$user = Auth::user();
		$fields_to_validator = $request->all();
		
		$site = new Site();
		$site->output = "";
		$site->user_id = $user->id;
		$site->app_name = "WordPress+Laravel";
		$site->action_name = $request->input("action_name");
		$site->to_clone_project_id = $request->input("to_clone_project_id");
		$site->name = $request->input("name");
		$site->to_import_project = $request->input("to_import_project");
		$site->user_id = $user->id;
		$site->url = $request->input("url");
		$site->big_file_route = $request->input("big_file_route");
		$site->laravel_version = $request->input("selected_version");	
		
		$site->wordpress_laravel_target_id = $request->input("wordpress_laravel_target");
	  	$site->wordpress_laravel_git_branch = $request->input("wordpress_laravel_git_branch");
	  	$site->wordpress_laravel_git = $request->input("wordpress_laravel_git");
		$site->wordpress_laravel_name = $request->input("wordpress_laravel_name");
	  		
		if($site->action_name == "new_wordpress_laravel"){
			
	    	$validator = Validator::make($fields_to_validator, [
		   	 'name' =>  array('required', 'regex:/^[a-zA-Z0-9-_]+$/','unique:sites'),
			 'wordpress_laravel_name' =>  array('required'),
			 "wordpress_laravel_target" =>  array('required'),
			 
	    	 ]);
			 
		}else if($site->action_name == "import_wordpress_laravel"){
			
	    	$validator = Validator::make($fields_to_validator, [
		   	 'name' =>  array('required', 'regex:/^[a-zA-Z0-9-_]+$/','unique:sites'),
			 'wordpress_laravel_git' =>  array('required'),
			 'wordpress_laravel_git_branch' =>  array('required'),
			 'wordpress_laravel_name' =>  array('required'),
			  "wordpress_laravel_target" =>  array('required'),
	    	 ]);
		
		}
		
     	if ($validator->fails()) {
			
			if(($site->action_name == "new_wordpress_laravel") || ($site->action_name == "import_wordpress_laravel")){
	        	return redirect('sites/wordpress_plus_laravel')
	        		->withErrors($validator)
	        			->withInput();
			}
     	 }
		 
		 $site->wordpress_laravel();
		
		return Redirect::to('/wordpress_plus_laravel'.'/'.$site->id .'/edit' .'?success=' . 'true');
		//return Redirect::to('/wordpress_plus_laravel?success=true');
	}
	
	public function edit($id)
	{
		$viewsw = "/wordpress_plus_laravel";
		$site = Site::findOrFail($id);
		$success = Input::get('success');
		return view('wordpress-plus-laravel-plugin::edit', compact('site','success','viewsw'));
	}
	
	
	
}
