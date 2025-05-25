<?php


namespace Pete\WordPressPlusLaravel\Http;

use Pete\WordPressPlusLaravel\Todo;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Site;
use Illuminate\Http\Request;
use App\PeteOption;
use Validator;
use Illuminate\Support\Facades\Redirect;
use View;
use Log;
use DB;

class WordPressPlusLaravelController extends Controller
{
	
	public function __construct(Request $request){
	    
	    $this->middleware('auth');
		$dashboard_url = env("PETE_DASHBOARD_URL");
		$viewsw = "/wordpress_plus_laravel";
		
		//DEBUGING PARAMS
		$debug = env('PETE_DEBUG');
		if($debug == "active"){
			$inputs = $request->all();
			Log::info($inputs);
		}
		
		$system_vars = parent::__construct();
		$pete_options = $system_vars["pete_options"];
		$sidebar_options = $system_vars["sidebar_options"];
		$os_distribution = $system_vars["os_distribution"];
		
		View::share(compact('dashboard_url','viewsw','pete_options','system_vars','sidebar_options','os_distribution'));
		   
	}
  	
	public function create(Request $request){
		
		$num = substr(PHP_VERSION, 0, 3);
		$float_version = (float)$num;
		
		if($float_version < 7.1){
        	return redirect('sites/create')->withErrors("The PHP version must be >= 7.1 to activate WordPress+Laravel functionality.");
		}
		$current_user = Auth::user(); 
		$viewsw = "/wordpress_plus_laravel";
		return view("wordpress-plus-laravel-plugin::create",compact('float_version','viewsw','current_user'));
	}
	
	
	public function index(Request $request)
	{
		$user = Auth::user();
		$viewsw = "/wordpress_plus_laravel";
		
		$sites = DB::select("select id, url, name, app_name, action_name, laravel_version, integration_type, wordpress_laravel_url from sites where app_name='WordPress+Laravel' and deleted_at is NULL ORDER BY created_at DESC");
		
		$tab_index = "index";
		$current_user = Auth::user(); 
		$success = $request->input('success');
		$site_id = $request->input('site_id');
		return view('wordpress-plus-laravel-plugin::index', compact('sites','success','site_id','viewsw','tab_index','current_user'));
	}
	
	public function trash(Request $request){
		
		$user = Auth::user();
		$sites = DB::select("select id, url, name, app_name, action_name, laravel_version from sites where app_name='WordPress+Laravel' and deleted_at is NOT NULL ORDER BY created_at DESC");
		$viewsw = "/wordpress_plus_laravel";
		$tab_index = "trash";
		$success = $request->input('success');
		$site_id = $request->input('site_id');
		$current_user = Auth::user(); 
		return view('wordpress-plus-laravel-plugin::trash', compact('sites','success','site_id','viewsw','tab_index','current_user'));
	}
	
	public function store(Request $request)
	{
		Log::info("wordpressLaravel check0a");
		$pete_options = new PeteOption();
		$user = Auth::user();
		$fields_to_validator = $request->all();
		
		Log::info("ACTION NAME:");
		Log::info($request->input("action_name"));
		
		$site = new Site();
		$site->output = "";
		$site->user_id = $user->id;
		$site->app_name = "WordPress+Laravel";
		$site->action_name = $request->input("action_name");
		$site->user_id = $user->id;
		$site->laravel_version = $request->input("selected_version");
		$site->wordpress_laravel_target_id = $request->input("wordpress_laravel_target");
	  	$site->wordpress_laravel_git_branch = $request->input("wordpress_laravel_git_branch");
	  	$site->wordpress_laravel_git = $request->input("wordpress_laravel_git");
		$site->wordpress_laravel_name = $request->input("wordpress_laravel_name");
		$site->name = $site->wordpress_laravel_name;
		$site->integration_type = $request->input("integration_type");
		
		Log::info("wordpressLaravel check0");
		
		if(isset($site->wordpress_laravel_target_id))
		  $site->set_wordpress_laravel_url($site->wordpress_laravel_target_id);
		
	  	$fields_to_validator["url"] = $site->url;
		$fields_to_validator["name"] = $site->name;
		$fields_to_validator["laravel_version"] = $site->laravel_version;
		
		Log::info("wordpressLaravel check1");
		$phpVersion = phpversion(); 
		if ($site->laravel_version == "10.*"){
			
			$phpVersion = phpversion(); // ejemplo: "8.0.30"
			$phpVersionParts = explode('.', $phpVersion);
			$major = (int) ($phpVersionParts[0] ?? 0);
			$minor = (int) ($phpVersionParts[1] ?? 0);

			if ($major < 8 || ($major === 8 && $minor < 1)) {
				return redirect('wordpress_plus_laravel/create')
					->withErrors("Unable to create WordPress + Laravel 10 integration with PHP version minor than 8.1")
					->withInput();
			}
		}
		
		//inside_wordpress validations
		$forbidden_names=["cache","ozone-speed","wp-admin","wp-content","wp-includes"];
		
		if($request->input("integration_type") == "inside_wordpress"){
			
			if(in_array($request->input("action_name"), $forbidden_names)){
				 
				 return redirect('wordpress_plus_laravel/create')->withErrors("Forbidden project name");
			}
		
		}
		
		if($site->action_name == "new_wordpress_laravel"){
			
			//CHECK PHP VERSIONS
			//$php_version = floatval(phpversion());
			
	    	$validator = Validator::make($fields_to_validator, [
		   	 'name' =>  array('required', 'regex:/^[a-zA-Z0-9-_]+$/','unique:sites'),
			 'wordpress_laravel_name' =>  array('required', 'regex:/^[a-zA-Z0-9-_]+$/'),
			 'integration_type' =>  array('required'),
			 "wordpress_laravel_target" =>  array('required'),
			 "laravel_version" =>  array('required'),
			 'url' => 'required|unique:sites'
	    	 ]);
			 
		}else if($site->action_name == "import_wordpress_laravel"){
			
	    	$validator = Validator::make($fields_to_validator, [
		   	 'name' =>  array('required', 'regex:/^[a-zA-Z0-9-_]+$/','unique:sites'),
			 'wordpress_laravel_git' =>  array('required'),
			 'integration_type' =>  array('required'),
			 'wordpress_laravel_git_branch' =>  array('required'),
			 'wordpress_laravel_name' =>  array('required'),
			  "wordpress_laravel_target" =>  array('required'),
			  'url' => 'required|unique:sites'
	    	 ]);
		
		}
		
     	if ($validator->fails()) {
			Log::info("wordpressLaravel check3");
			if(($site->action_name == "new_wordpress_laravel") || ($site->action_name == "import_wordpress_laravel")){
	        	return redirect('/wordpress_plus_laravel/create')
	        		->withErrors($validator)
	        			->withInput();
			}
     	 }
		 
		 $site->wordpress_laravel();
		
		
		return Redirect::to('/wordpress_plus_laravel'.'/'.$site->id .'/edit' .'?success=' . 'true');
		
	}
	
	public function edit(Request $request,$id)
	{
		$viewsw = "/wordpress_plus_laravel";
		$site = Site::findOrFail($id);
		$success = $request->input('success');
		$current_user = Auth::user(); 
		
		$pete_options = new PeteOption();
	    $app_root = $pete_options->get_meta_value('app_root');
		
		$web_server_error_file = "$app_root/wwwlog/$site->name/error.log";
		$web_server_error_content = @file_get_contents("$app_root/wwwlog/$site->name/error.log");
		$web_server_access_file = "$app_root/wwwlog/$site->name/access.log";
		$web_server_access_content = @file_get_contents("$app_root/wwwlog/$site->name/access.log");
		
		$target_site = Site::findOrFail($site->wordpress_laravel_target_id);
		
		return view('wordpress-plus-laravel-plugin::edit', compact('site','success','viewsw','current_user','web_server_error_file','web_server_error_content','web_server_access_file','web_server_access_content','target_site'));
	}
	
	
	public function destroy(Request $request)
	{
		$user = Auth::user();
		$site = Site::findOrFail($request->input("site_id"));
		
		if($user->is_owner_and_admin($site)){
			$site->delete_wordpress_laravel();
			$site->delete();
			$debug = env('PETE_DEBUG');
			if($debug == "active"){
				Log::info('Ouput deleteDebug' . $site->output);
			}
			
		}
		
		return Redirect::to('/wordpress_plus_laravel?success=true');
	}
	
    public function force_delete(Request $request){
	   
	    $user = Auth::user();
 		$site = Site::onlyTrashed()->findOrFail($request->input("site_id"));	
 		
		if($user->is_owner_and_admin($site)){
			$site->force_delete_wordpress();
			$site->forceDelete();
		}
		
 		return Redirect::to('wordpress_plus_laravel/trash');
	
    }
	
	public function restore(Request $request){
		$site = Site::withTrashed()->findOrFail($request->input('id'));
		$site->restore();
		$site->restore_wordpress_laravel();
		
		return Redirect::to('/wordpress_plus_laravel?success=true');
	}

	public function wl_generate_ssl(Request $request){

		Log::info("enter in generate_ssl");
		$pete_options = new PeteOption();

		if($pete_options->get_meta_value('environment') != "production"){
			$result = ['error' => true, 'message'=>'This feature is only avaliable in production environment'];
			return response()->json($result);
		}else{
			$current_user = Auth::user();
			$request_array = $request->all();
			$site = Site::findOrFail($request->input('site_id'));
			$site->ssl = true;
			$site->save();
			$site->generate_ssl($current_user->email);
			return response()->json($request_array);
		}

	}
	
}
