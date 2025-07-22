<?php

namespace Peterconsuegra\WordPressPlusLaravel\Console;

use Illuminate\Console\Command;
use Log;
use Peterconsuegra\WordPressPlusLaravel\bin\WpTools;

class NewWordPressPlusLaravel extends Command {
	
	
    protected $name = 'new_wordpress_plus_laravel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'New WordPressPlusLaravel Command';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'new_wordpress_plus_laravel {--db_user=} {--db_name=} {--db_pass=} {--integration_type=}';

    
    public function handle() {
		
		//GET LARAVEL VERSION
		$version = app()->version();
		$num = substr($version, 0, 3);
		$float_version = (float)$num;
		$this->comment("Laravel version: ".$float_version);
		
		$db_user = $this->option('db_user');
		$db_name = $this->option('db_name');
		$db_pass = $this->option('db_pass');

		/*
		if($float_version >= 8){
			$code = "use App\Http\Controllers\HelloController;";
			WpTools::add_code_to_file($file_path,'/*',$code);
		}
		*/

		// WPAuthMiddleware LOGIC
		$template_path = base_path()."/vendor/peteconsuegra/wordpress-plus-laravel/templates/middleware/WPAuthMiddleware.php";
		$file_path = base_path()."/app/Http/Middleware/WPAuthMiddleware.php";	
		WPTools::create_folder(base_path()."/app/Http/Middleware/");
		WpTools::insert_template($template_path,$file_path);
		$this->comment("Add WPAuthMiddleware.php to /app/Http/Middleware/WPAuthMiddleware.php");
		
		//ADD HELLO CONTROLLER FOR BUILT IN EXAMPLES
		$controller_template_path = WpTools::get_hello_controller($float_version);
		$file_path = base_path()."/app/Http/Controllers/HelloController.php";	
		WpTools::insert_template($controller_template_path,$file_path);
		$this->comment("Add HelloController.php to /app/Http/Controllers/HelloController.php");
		
		//ADD HELLO CONTROLLER ROUTES
		$file_path = base_path()."/routes/web.php";
		$routes_code = WpTools::get_laravel_routes_code($float_version);
		WpTools::add_code_to_end_of_file($file_path,$routes_code);
		$this->comment("Add code Route::get('/', 'HelloController@wordpress_code_example'); to routes/web.php");
		
		//ADD WPAuthMiddleware LOGIC
		if($float_version <= 10){
			
			//public static function add_code_to_file_pro($file,$pointer,$var,$row_plus)
			WpTools::add_code_to_file(base_path()."/app/Http/Kernel.php","'auth' => \App\Http\Middleware\Authenticate::class,","'auth.wp' => \App\Http\Middleware\WPAuthMiddleware::class,");
			$this->comment("Add code middleware 'auth.wp' => \App\Http\Middleware\WPAuthMiddleware::class, to app/Http/Kernel.php");
			
		}else if($float_version > 10){

			//public static function add_code_to_file_pro($file,$pointer,$var,$row_plus)
			WpTools::addCodeAfter(base_path()."/bootstrap/app.php",'->withMiddleware(function (Middleware $middleware) {','$middleware->append(\App\Http\Middleware\WPAuthMiddleware::class);');
			$this->comment('Add code $middleware->append(\App\Http\Middleware\WPAuthMiddleware::class);, to bootstrap/app.php');

			WpTools::addCodeAfter(base_path()."/bootstrap/app.php",'->withMiddleware(function (Middleware $middleware) {','$middleware->alias(["auth.wp" => \App\Http\Middleware\WPAuthMiddleware::class]);');
			$this->comment('Add code $middleware->append(\App\Http\Middleware\WPAuthMiddleware::class);, to bootstrap/app.php');

		}
		
		$this->comment("before integration_type WordPress option");
		
		if($this->option('integration_type') == "inside_wordpress"){
			
			$this->comment("Inside WordPress option");
			rename(base_path()."/public/.htaccess", base_path()."/.htaccess");
			
			//delete index.php file
			unlink(base_path()."/public/index.php");
			
			$template_path = base_path()."/vendor/peteconsuegra/wordpress-plus-laravel/templates/files/index.php";
			$file_path = base_path()."/index.php";	
			WpTools::insert_template($template_path,$file_path);
			
			$template_path = base_path()."/vendor/peteconsuegra/wordpress-plus-laravel/templates/views/wordpress_plus_laravel_examples_inside_wordpress.blade.php";
			$file_path = base_path()."/resources/views/wordpress_plus_laravel_examples.blade.php";	
			WpTools::insert_template($template_path,$file_path);
			$this->comment("Add file wordpress_code_example.php ");
			

	        //ADD HELLO CONTROLLER VIEWS
			$template_path = base_path()."/vendor/peteconsuegra/wordpress-plus-laravel/templates/views/list_orders_inside.blade.php";
			$file_path = base_path()."/resources/views/list_orders.blade.php";	
			WpTools::insert_template($template_path,$file_path);
			$this->comment("Add file list_orders.blade.php");
		
			$template_path = base_path()."/vendor/peteconsuegra/wordpress-plus-laravel/templates/views/list_posts_inside.blade.php";
			$file_path = base_path()."/resources/views/list_posts.blade.php";	
			WpTools::insert_template($template_path,$file_path);
			$this->comment("Add file list_posts.blade.php");
		
			$template_path = base_path()."/vendor/peteconsuegra/wordpress-plus-laravel/templates/views/list_products_inside.blade.php";
			$file_path = base_path()."/resources/views/list_products.blade.php";	
			WpTools::insert_template($template_path,$file_path);
			$this->comment("Add file list_products.blade.php");
		
			$template_path = base_path()."/vendor/peteconsuegra/wordpress-plus-laravel/templates/views/list_users_inside.blade.php";
			$file_path = base_path()."/resources/views/list_users.blade.php";	
			WpTools::insert_template($template_path,$file_path);
			$this->comment("Add file list_users.blade.php");
			

		}else{
			$template_path = base_path()."/vendor/peteconsuegra/wordpress-plus-laravel/templates/views/wordpress_plus_laravel_examples.blade.php";
			$file_path = base_path()."/resources/views/wordpress_plus_laravel_examples.blade.php";	
			WpTools::insert_template($template_path,$file_path);
			$this->comment("Add file wordpress_code_example.php ");
			
	        //ADD HELLO CONTROLLER VIEWS
			$template_path = base_path()."/vendor/peteconsuegra/wordpress-plus-laravel/templates/views/list_orders.blade.php";
			$file_path = base_path()."/resources/views/list_orders.blade.php";	
			WpTools::insert_template($template_path,$file_path);
			$this->comment("Add file list_orders.blade.php");
		
			$template_path = base_path()."/vendor/peteconsuegra/wordpress-plus-laravel/templates/views/list_posts.blade.php";
			$file_path = base_path()."/resources/views/list_posts.blade.php";	
			WpTools::insert_template($template_path,$file_path);
			$this->comment("Add file list_posts.blade.php");
		
			$template_path = base_path()."/vendor/peteconsuegra/wordpress-plus-laravel/templates/views/list_products.blade.php";
			$file_path = base_path()."/resources/views/list_products.blade.php";	
			WpTools::insert_template($template_path,$file_path);
			$this->comment("Add file list_products.blade.php");
		
			$template_path = base_path()."/vendor/peteconsuegra/wordpress-plus-laravel/templates/views/list_users.blade.php";
			$file_path = base_path()."/resources/views/list_users.blade.php";	
			WpTools::insert_template($template_path,$file_path);
			$this->comment("Add file list_users.blade.php");
			
		}
			
		

		
    }

}