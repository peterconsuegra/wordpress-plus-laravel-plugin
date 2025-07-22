<?php

#use Peterconsuegra\WordPressPlusLaravel\bin\WpTools
namespace Peterconsuegra\WordPressPlusLaravel\bin;

use Log;

class WpTools{
	
	public static $file_path;

	public static function create_folder($path){
		if (! is_dir($path)) {
			// 0755 = rwxr-xr-x; `true` lets PHP create any missing parents
			if (! mkdir($path, 0755, true) && ! is_dir($path)) {
				throw new \RuntimeException("Failed to create directory: $path");
			}
		}
	}
	
	public static function replace_migration_if_table_exists($table,$migration_file){
		$dir = base_path()."/database/migrations";
		if(\Schema::hasTable($table)){
			WpTools::search_file_with_pattern($dir,$migration_file);
			$template_path = base_path()."/vendor/peteconsuegra/wordpress-plus-laravel/templates/migrations/$migration_file";
			WpTools::insert_template($template_path,WpTools::$file_path);
		}
	}
	
	public static function search_file_with_pattern($dir,$file_to_search){
		
		$files = scandir($dir);
		
		foreach($files as $key => $value){
    	
		    $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
    	
		    if(!is_dir($path)) {
    			Log::info("$path");
				if(strpos($path, $file_to_search) !== false){
					WpTools::$file_path = $path;
		        }
    	
		    } else if($value != "." && $value != "..") {
    	
		       WpTools::search_file_with_pattern($path,$file_to_search);
    	
		    }  
		} 
	}
	

	public static function search_file($dir,$file_to_search,$content){
		
		$files = scandir($dir);
		
		foreach($files as $key => $value){
    	
		    $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
    	
		    if(!is_dir($path)) {
    	
		        if($file_to_search == $value){
					$file_content = @file_get_contents($path);
					if(strpos($file_content, $content) !== false){
						WpTools::$file_path = $path;
					} 
		          
		        }
    	
		    } else if($value != "." && $value != "..") {
    	
		       WpTools::search_file($path,$file_to_search,$content);
    	
		    }  
		} 
		
	}
	
	public static function get_laravel_routes_code($laravel_version){
		$routes_code = "";
		if($laravel_version >= 8){
			$routes_code .= "use App\Http\Controllers\HelloController;\n";
			$routes_code .= "Route::get('list_users', [HelloController::class,'list_users']);\n";
			$routes_code .= "Route::get('list_orders', [HelloController::class,'list_orders']);\n";
			$routes_code .= "Route::get('list_posts', [HelloController::class,'list_posts']);\n";
			$routes_code .= "Route::get('list_products', [HelloController::class,'list_products']);\n";
			$routes_code .= "Route::get('edit_posts', [HelloController::class, 'edit_posts']);\n";
			$routes_code .= "Route::get('edit_post', [HelloController::class, 'edit_post']);\n";
			$routes_code .= "Route::post('update_post', [HelloController::class, 'update_post']);\n";
			$routes_code .= "Route::get('/wordpress_plus_laravel_examples', [HelloController::class, 'wordpress_plus_laravel_examples']);\n";
		}
		
		return $routes_code;
	}
	
	public static function get_hello_controller($laravel_version){
		if($laravel_version <= 10){
			$controller_template_path = base_path()."/vendor/peteconsuegra/wordpress-plus-laravel/templates/controllers/HelloController6.php";
		}else if($laravel_version >= 11){
			$controller_template_path = base_path()."/vendor/peteconsuegra/wordpress-plus-laravel/templates/controllers/HelloController11.php";
		}
		return $controller_template_path;
	}
	
	public static function insert_template($template_path,$file_path){
		
		if (!copy($template_path, $file_path)) {
		    echo "failed to copy $template_path...\n";
		}
	}
	
	public static function add_code_to_file_pro($file,$pointer,$var,$row_plus){
		
		$lines = array();
		$sw = false;
		$sw_row_plus=false;
		$loop_cont=0;
		$find_cont=0;
		$find_flag=0;
		$activator=0;
		$first=true;
		
		foreach(file($file) as $line)
		{
			//Log::info($line);
			$find_cont=$find_cont*$activator;
			if($var == trim($line)){
				$first = false;
			}
			
			if($pointer == trim($line) && ($sw == false) && ($first == true))
			{
				$sw = true;
				$find_flag=$loop_cont;
				$activator=1;
			}
			
			if($find_cont > $row_plus && ($sw_row_plus == false)){
				array_push($lines, "$var  \n");
				$sw_row_plus=true;
			}
			
			array_push($lines, $line);
			$loop_cont++;
			$find_cont++;
		}
		
		file_put_contents($file, $lines);
	}
	
	public static function delete_code_in_file($file,$pointer){
		$lines = array();
		$sw = false;
		$first=true;
		foreach(file($file) as $line)
		{
			if(!strpos($line, $pointer) !== false){
				array_push($lines, $line);
			}
			
		}
		file_put_contents($file, $lines);
	}
	
	public static function get_code_in_file($file,$pointer){
		$lines = array();
		$sw = false;
		$first=true;
		foreach(file($file) as $line)
		{
			if(strpos($line, $pointer) !== false){
				return $line;
			}
			
		}
		return "not_found";
	}
	
	public static function get_user_namespace($file,$pointer){
		$namespace = WpTools::get_code_in_file($file,$pointer);
		$namespace = str_replace("namespace ","",$namespace);
		$namespace = str_replace(";","",$namespace);
		$namespace = trim($namespace);
		$namespace = "use ".$namespace."\User;";
		return $namespace;
	}
	
	public static function add_code_to_file($file,$pointer,$var,$first=true){
		$lines = array();
		$sw = false;
		foreach(file($file) as $line)
		{
			//Log::info($line);
			
			if($var == trim($line)){
				$first = false;
			}
			
			if($pointer == trim($line) && ($sw == false) && ($first == true))
			{
				array_push($lines, "$var  \n");
				$sw = true;
			}
			array_push($lines, $line);
		}
		file_put_contents($file, $lines);
	}

	public static function addCodeAfter(string $file, string $pointer, string $codeLine): void
    {
        // Read file as an array of lines (without trailing new-line chars)
        $lines = file($file, FILE_IGNORE_NEW_LINES);

        // Bail if the line is already present anywhere in the file
        if (in_array(trim($codeLine), array_map('trim', $lines), true)) {
            return;
        }

        $out       = [];
        $inserted  = false;

        foreach ($lines as $line) {
            $out[] = $line;                        // keep current line
            if (!$inserted && trim($line) === trim($pointer)) {
                $out[]  = $codeLine;               // add our line right after pointer
                $inserted = true;
            }
        }

        // Persist changes (re-add new-line characters)
        file_put_contents($file, implode(PHP_EOL, $out) . PHP_EOL);
    }
	
	public static function set_column_to_null_by_default($table,$column_name,$db_name,$db_user,$db_user_pass){
	
		$db_host = env('DB_HOST');
		
		$conn=mysqli_connect($db_host,$db_user,$db_user_pass,$db_name);
		// Check connection
		if (mysqli_connect_errno()){
		  Log::info("Failed to connect to MySQL: " . mysqli_connect_error());
		 }else{
		   Log::info("success conection");
		 }
		
		Log::info("ALTER TABLE `$table` CHANGE `$column_name` `$column_name` DATETIME NULL DEFAULT NULL");
		$conn->query("ALTER TABLE `$table` CHANGE `$column_name` `$column_name` DATETIME NULL DEFAULT NULL");
		$conn->close();
	}
	
	public static function add_column_to_table($table,$column_name,$data_type,$column_after,$db_name,$db_user,$db_user_pass){
		
		$db_host = env('DB_HOST');
		
		$conn=mysqli_connect($db_host,$db_user,$db_user_pass,$db_name);
		// Check connection
		if (mysqli_connect_errno()){
			Log::info("Failed to connect to MySQL: " . mysqli_connect_error());
			}else{
		   	Log::info("success conection");
		}
		
		$check = $conn->query("SHOW COLUMNS FROM $table LIKE '$column_name'");
		
		if(is_bool($check)){
			//MYSQL
			Log::info("MySQL DB");
			if(!$check){
				Log::info("ALTER TABLE $table ADD $column_name $data_type NULL");
			 	$conn->query("ALTER TABLE $table ADD $column_name $data_type NULL");
			}
		}else{
			//MariaDB DOCKER
			Log::info("MariaDB DB");
			$num = mysqli_num_rows($check);
			if($num == 0){
				Log::info("ALTER TABLE $table ADD $column_name $data_type NULL");
			 	$conn->query("ALTER TABLE $table ADD $column_name $data_type NULL");
			}
		}

	 	$conn->close();
		
	}
	
	public static function add_code_to_end_of_file($file,$var,$first=true){
		
		foreach(file($file) as $line)
		{
			
			if($var == trim($line)){
				$first = false;
			}
			
		}
		
		if($first==true){
			file_put_contents($file, "\n".$var, FILE_APPEND);
		}
		 
	}
	
    public static function renameHelperFunctions()
    {
        $vendorDir   = base_path()."/vendor";
        $helpersPath = $vendorDir . '/laravel/framework/src/Illuminate/Foundation/helpers.php';

        if ( ! file_exists($helpersPath)) {
            return;
        }

        $content = file_get_contents($helpersPath);
        $content = str_replace("function_exists('__')", "function_exists('___')", $content);
        $content = str_replace('function __', 'function ___', $content);
        file_put_contents($helpersPath, $content);
    }
	
    public static function rename_woo_wakeup()
    {
		$wordpress_path = env('WP_LOAD_PATH');
        $file1 = $wordpress_path . '/wp-content/plugins/woocommerce/includes/rest-api/Utilities/SingletonTrait.php';
		$file2 = $wordpress_path . '/wp-content/plugins/woocommerce/packages/woocommerce-admin/src/FeaturePlugin.php';

        if ( ! file_exists($file2)) {
            return;
        }

        $content = file_get_contents($file2);
        $content = str_replace("private function __wakeup()", "public function __wakeup()", $content);
        file_put_contents($file2, $content);
    }
	
}