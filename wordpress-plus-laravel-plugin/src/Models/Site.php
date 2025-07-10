<?php

namespace Pete\WordPressPlusLaravel\Models;

use App\Site as BaseSite;
use App\PeteOption;
use Log;

// Extending Site Model 
class Site extends BaseSite   
{
    public function set_wordpress_laravel_url($site_id){
		$target_site = Site::findOrFail($site_id);
		$this->url = $this->name . '.' . $target_site->url;
	}

    public function delete_wordpress_laravel() {
			
		$pete_options = new PeteOption();
	    $app_root = $pete_options->get_meta_value('app_root');
	    $server_conf = $pete_options->get_meta_value('server_conf');
		$os_distribution = $pete_options->get_meta_value('os_distribution');
	
		$base_path = base_path();
		chdir("$base_path/scripts/");
			
		$command = "./delete_wordpress_laravel.sh -n {$this->name} -r {$app_root} -q {$base_path} -a {$server_conf} -s {$this->id} -p {$os_distribution} -o {$this->integration_type} -l {$this->wp_load_path} -d {$this->wordpress_laravel_name}";
		$output = shell_exec($command);
  	  	
		$this->url = $this->url . "_odeleted_" . $this->id;
		$this->name = $this->name . "_odeleted_" . $this->id;
		$this->save();
		$this->delete();
		
	  	if(env('PETE_DEBUG') == "active"){
			Log::info("######DELETE LOGIC DEBUG########");
			Log::info("COMMAND:");
  			Log::info($command);
	  		Log::info("OUTPUT:");
			Log::info($output);
  	  	}
	}

    public function wordpress_laravel() {
		
		$pete_options = new PeteOption();
	    $app_root = $pete_options->get_meta_value('app_root');
        $mysql_bin = $pete_options->get_meta_value('mysql_bin');
	    $server_conf = $pete_options->get_meta_value('server_conf');
		$os = $pete_options->get_meta_value('os');
		$os_version = $pete_options->get_meta_value('os_version');
		$server = $pete_options->get_meta_value('server');
		$server_version = $pete_options->get_meta_value('server_version');
		$apache_version = $pete_options->get_meta_value('apache_version');
		
		$logs_route = $pete_options->get_meta_value('logs_route');
		$os_distribution = $pete_options->get_meta_value('os_distribution');
		
		$db_root_pass = env('PETE_ROOT_PASS');
		$mysqlcommand = $mysql_bin . "mysql";
		$debug = env('PETE_DEBUG');

		$base_path = base_path();
		
		$target_site = Site::findOrFail($this->wordpress_laravel_target_id);
		$this->wordpress_laravel_url = $this->wordpress_laravel_name . '.' . $target_site->url;
		$this->url = $this->wordpress_laravel_url;	
	    $this->app_name = "WordPress+Laravel";
		$this->wp_load_path = $app_root . "/" . $target_site->name;
		$this->wp_url = $target_site->url;
		
		if($this->action_name == "new_wordpress_laravel"){
			$this->action_name = "New";
			$this->wordpress_laravel_git_branch = "something";
	  		$this->wordpress_laravel_git = "something";
		}else{
			$this->action_name = "Import";
			$this->laravel_version = "import";
		}
		
		$db_host = env('DB_HOST') ?? 'localhost';
		if($db_host != "localhost")
			$db_host=$db_host.":3306";
	
		$this->db_name = "db_" . substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);
		$this->db_user = "pete";
	    $db_user_pass = env('PETE_ROOT_PASS');
		
		#hack project_name for multiple dashboard.* logic
		if($this->integration_type == "separate_subdomain"){
			$this->name = $this->name . str_replace(".","",$this->wp_url);
		}else{
			$this->url = $target_site->url."/$this->name";
		}

		if($target_site->ssl == true){
			$ssl = "true";
		}else{
			$ssl = "false";
		}
			
		$command = "./wordpress_laravel.sh -p {$db_root_pass} -r {$app_root} -m {$logs_route} -z {$os_distribution} -a {$server_conf} -b {$this->wordpress_laravel_git_branch} -g {$this->wordpress_laravel_git} -n {$this->name} -u {$this->wordpress_laravel_url} -j {$os_version} -v {$os} -l {$this->wp_load_path} -e {$this->wp_url} -t {$server} -w {$server_version} -c {$this->action_name} -o {$this->laravel_version} -h {$ssl} -x {$db_host} -k {$debug} -q {$target_site->db_name} -y {$this->db_user} -i {$db_user_pass} -s {$this->integration_type} -d {$this->wordpress_laravel_name}";
	    chdir("$base_path/scripts/");
		
	   	putenv("COMPOSER_HOME=/usr/local/bin/composer");
		putenv("COMPOSER_CACHE_DIR=~/.composer/cache");
		
		$output = shell_exec($command);
	  	if($debug == "active"){
			Log::info("Action: wordpress_laravel");
			Log::info($command);
  			Log::info("Output:");
			Log::info($output);
	  	}
		
		$this->output = $this->output . "####### WORDPRESS LARAVEL #######\n";	 
		$this->output .= $output;
	   	$this->save();
		
		if($this->integration_type != "inside_wordpress")
			$this->create_config_file();
	  
	}
}
