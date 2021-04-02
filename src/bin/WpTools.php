<?php

namespace Pete\WordPressPlusLaravel\bin;

use Log;

class WpTools{
	
	public static function insert_template($template_path,$file_path){
		
		if (!copy($template_path, $file_path)) {
		    echo "failed to copy $template_path...\n";
		}
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
	
	public static function set_column_to_null_by_default($table,$column_name){
		$db_name = env('DB_DATABASE');
		$db_user_pass = env('DB_PASSWORD');
		$db_user = env('DB_USERNAME');
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
	
	public static function add_column_to_table($table,$column_name,$data_type,$column_after){
		
		$db_name = env('DB_DATABASE');
		$db_user_pass = env('DB_PASSWORD');
		$db_user = env('DB_USERNAME');
		$db_host = env('DB_HOST');
		
		$conn=mysqli_connect($db_host,$db_user,$db_user_pass,$db_name);
		// Check connection
		if (mysqli_connect_errno()){
		  Log::info("Failed to connect to MySQL: " . mysqli_connect_error());
		 }else{
		   Log::info("success conection");
		 }
		
		Log::info("ALTER TABLE $table ADD $column_name $data_type NULL");
 		$conn->query("ALTER TABLE $table ADD $column_name $data_type NULL");
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
	
}