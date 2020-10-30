<?php

namespace Pete\WordPressPlusLaravel\Console;


use Illuminate\Console\Command;
use Log;
use Pete\WordPressPlusLaravel\bin\WpTools;

class AdaptWordPressPlusLaravel extends Command {
	

    protected $name = 'adapt_wordpress_plus_laravel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adapt WordPressPlusLaravel Command';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'adapt_wordpress_plus_laravel';

    
    public function handle() {
		
		$version = app()->version();
		$num = substr($version, 0, 3);
		$float_version = (float)$num;
		$this->comment("Laravel version: ".$float_version);
					
		//Rename helpers method __ to ___ in vendor/laravel/framework/src/Illuminate/Foundation/helpers.php
		WpTools::renameHelperFunctions();
		$this->comment("Rename helpers method __ to ___ in vendor/laravel/framework/src/Illuminate/Foundation/helpers.php");
		
		
		
    }

}
