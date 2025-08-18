<?php


namespace Pete\WordPressPlusLaravel\Http;

use Pete\WordPressPlusLaravel\Todo;
use Pete\WordPressPlusLaravel\Models\Site; 
use App\Http\Controllers\PeteController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\PeteOption;
use App\OServer;
use Validator;
use Illuminate\Support\Facades\Redirect;
use View;
use Log;
use DB;

class WordPressPlusLaravelController extends PeteController
{
	
	public function __construct(Request $request)
    {
		//Ensure system vars are loaded
        parent::__construct();          

        $this->middleware('auth');

        View::share([
            'dashboard_url' => env('PETE_DASHBOARD_URL'),
            'viewsw'        => '/wordpress_plus_laravel'
        ]);
    }
  	
	public function create(Request $request){
		
		$num = substr(PHP_VERSION, 0, 3);
		$float_version = (float)$num;
		
		if($float_version < 7.1){
        	return redirect('sites/create')->withErrors("The PHP version must be >= 7.1 to activate WordPress+Laravel functionality.");
		}

		$current_user = Auth::user(); 
		return view("wordpress-plus-laravel-plugin::create",compact('float_version','current_user'));
	}
	
	
	public function index(Request $request)
	{
		$current_user = Auth::user(); 
		if($current_user->admin){
			$sites = Site::orderBy('id', 'desc')->where("app_name","WordPress+Laravel")->paginate(50);
		}else{
			$sites = $user->my_sites()->where("app_name","WordPress+Laravel")->paginate(10);
		}
		return view('wordpress-plus-laravel-plugin::index', compact('sites','current_user'));
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
		$site->user_id = $user->id;
		$site->laravel_version = $request->input("selected_version");
		$site->wordpress_laravel_target_id = $request->input("wordpress_laravel_target");
	  	$site->wordpress_laravel_git_branch = $request->input("wordpress_laravel_git_branch");
	  	$site->wordpress_laravel_git = $request->input("wordpress_laravel_git");
		$site->wordpress_laravel_name = $request->input("wordpress_laravel_name");
		$site->name = $site->wordpress_laravel_name;
		$site->integration_type = $request->input("integration_type");
		
		if(isset($site->wordpress_laravel_target_id))
		  $site->set_wordpress_laravel_url($site->wordpress_laravel_target_id);
		
	  	$fields_to_validator["url"] = $site->url;
		$fields_to_validator["name"] = $site->name;
		$fields_to_validator["laravel_version"] = $site->laravel_version;
		
		
		$phpVersion = phpversion(); // ejemplo: "8.0.30"
		$phpVersionParts = explode('.', $phpVersion);
		$major = (int) ($phpVersionParts[0] ?? 0);
		$minor = (int) ($phpVersionParts[1] ?? 0);
		
		if ($site->laravel_version == "10.*"){
			
			if ($major < 8 || ($major === 8 && $minor < 1)) {
				return redirect('wordpress_plus_laravel/create')
					->withErrors("Unable to create WordPress + Laravel 10 integration with PHP version minor than 8.1")
					->withInput();
			}
		}else if (($site->laravel_version == "11.*") || ($site->laravel_version == "12.*")){
			if ($major < 8 || ($major === 8 && $minor < 2)) {
				return redirect('wordpress_plus_laravel/create')
					->withErrors("Unable to create WordPress + Laravel 10 integration with PHP version minor than 8.2")
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
		 
		$site->create_wordpress_laravel();
		OServer::reload_server();
		
		return Redirect::to("/wordpress_plus_laravel/logs/$site->id");
		
	}
	
	public function logs(Request $request,$id)
	{
		$current_user = Auth::user();
		$site = Site::findOrFail($id);
		$app_root = app(PeteOption::class)->get_meta_value('app_root');

		$target_site = Site::findOrFail($site->wordpress_laravel_target_id);

		$paths = [
			'web_server_error_file'  => "$app_root/wwwlog/{$site->name}/error.log",
			'web_server_access_file' => "$app_root/wwwlog/{$site->name}/access.log",
		];

		$logs = collect($paths)->mapWithKeys(fn ($path, $key) => [
			$key . '_content' => is_readable($path) ? file_get_contents($path) : 'file not found',
		])->all();

		return view(
			'wordpress-plus-laravel-plugin::logs',
			array_merge(['site' => $site, 'current_user' => $current_user, 'target_site' => $target_site], $paths, $logs)
		);
	}
	
	
	public function delete(Request $request)
	{
		Log::info("Enter in delete WordPressPlusLaravelController");
		$user = Auth::user();
		$site = Site::findOrFail($request->input("site_id"));
		
		if($user->is_owner_or_admin($site)){
			Log::info("Enter in delete WordPressPlusLaravelController");
			$site->delete_wordpress_laravel();

			$debug = env('PETE_DEBUG');
			if($debug == "active"){
				Log::info('Ouput deleteDebug' . $site->output);
			}

			$site->delete();
		}
		OServer::reload_server();
		return Redirect::to('/wordpress_plus_laravel');
	}
	
	public function wl_generate_ssl(Request $request){

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
