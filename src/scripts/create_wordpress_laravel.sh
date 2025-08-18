#!/bin/bash

while getopts p:r:m:z:a:b:g:u:j:v:n:l:e:t:w:c:o:h:x:k:q:y:i:s:d: option 
do 
case "${option}" 
	in 
	p) db_root_pass=${OPTARG};;
	r) route=${OPTARG};;
	m) logs_route=${OPTARG};;
	z) os_distribution=${OPTARG};;
	a) server_conf=${OPTARG};;
	b) wordpress_laravel_git_branch=${OPTARG};;
	g) wordpress_laravel_git=${OPTARG};;
	u) wordpress_laravel_url=${OPTARG};;
	j) os_version=${OPTARG};;
	v) os=${OPTARG};;
	n) project_name=${OPTARG};;
	l) wp_load_path=${OPTARG};;
	e) wp_url=${OPTARG};;
	t) server=${OPTARG};;
	w) server_version=${OPTARG};;
	c) action_name=${OPTARG};;
	o) laravel_version=${OPTARG};;
	h) ssl=${OPTARG};;
	x) db_host=${OPTARG};;
	k) debug=${OPTARG};;
	q) odb=${OPTARG};;
	y) udb=${OPTARG};;
	i) pdb=${OPTARG};;
	s) integration_type=${OPTARG};;
	d) wordpress_laravel_name=${OPTARG};;
esac 
done

#DEBUG
if [[ $debug == "active" ]]; then
	
echo "db_root_pass: $db_root_pass"
echo "app_root: $route" 
echo "logs_route: $logs_route"
echo "logs_route: $logs_route"
echo "wordpress_laravel_name: $wordpress_laravel_name"
echo "server_conf: $server_conf"
echo "wordpress_laravel_git_branch: $wordpress_laravel_git_branch"
echo "wordpress_laravel_git: $wordpress_laravel_git"
echo "wordpress_laravel_url: $wordpress_laravel_url"
echo "os_version: $os_version"
echo "os: $os"
echo "site_name: $project_name"
echo "wp_load_path: $wp_load_path"
echo "wp_url: $wp_url"
echo "debug: $debug"
echo "DB: "$odb
echo "UDB: "$udb
echo "PDB: "$pdb
echo "db_host: $db_host"
echo "action_name: $action_name"
echo "integration_type: $integration_type"

fi

if [[ $integration_type == "inside_wordpress" ]]; then
	project_route=$wp_load_path/$wordpress_laravel_name
else
	project_route=$route/$project_name
fi

mkdir $project_route

if [[ $action_name == "New" ]]; then

	if [[ $laravel_version == "10.*" ]]; then
		echo "cd $project_route && git clone https://github.com/peterconsuegra/wordpress-laravel10-boilerplate.git ."
		cd $project_route && git clone https://github.com/peterconsuegra/wordpress-laravel10-boilerplate.git .
		echo "cd $project_route && composer install --ignore-platform-reqs 2>&1"
		cd $project_route && composer install --ignore-platform-reqs 2>&1
	elif [[ "$laravel_version" == "11.*" ]]; then
		echo "cd $project_route && git clone https://github.com/peterconsuegra/wordpress-laravel11-boilerplate.git ."
		cd $project_route && git clone https://github.com/peterconsuegra/wordpress-laravel11-boilerplate.git .
		echo "cd $project_route && composer install --ignore-platform-reqs 2>&1"
		cd $project_route && composer install --ignore-platform-reqs 2>&1
	elif [[ "$laravel_version" == "12.*" ]]; then
		echo "cd $project_route && git clone https://github.com/peterconsuegra/wordpress-laravel12-boilerplate.git ."
		cd $project_route && git clone https://github.com/peterconsuegra/wordpress-laravel12-boilerplate.git .
		echo "cd $project_route && composer install --ignore-platform-reqs 2>&1"
		cd $project_route && composer install --ignore-platform-reqs 2>&1
	else
		echo "cd $project_route && composer create-project laravel/laravel="$laravel_version" . --prefer-dist 2>&1"
		cd $project_route && composer create-project laravel/laravel="$laravel_version" . --prefer-dist 2>&1
		cd $project_route && php artisan schema:dump --prune
	fi

	
	rm $project_route/public/index.php
	rm $project_route/index.php
	rm $project_route/public/.htaccess
	rm $project_route/.htaccess

	echo "before if integration"

	if [[ $integration_type == "inside_wordpress" ]]; then
		echo "enter if inside_wordpress"
		
		if [[ $laravel_version == "10.*" ]]; then
			cp "$route/Pete/vendor/peteconsuegra/wordpress-plus-laravel-plugin/src/templates/same_domain_laravel10_index.php" "$project_route/index.php"
			cp "$route/Pete/vendor/peteconsuegra/wordpress-plus-laravel-plugin/src/templates/htaccess_laravel10.txt" "$project_route/.htaccess"
		elif [[ "$laravel_version" == "11.*" ]]; then
			cp "$route/Pete/vendor/peteconsuegra/wordpress-plus-laravel-plugin/src/templates/same_domain_laravel11_index.php" "$project_route/index.php"
			cp "$route/Pete/vendor/peteconsuegra/wordpress-plus-laravel-plugin/src/templates/htaccess_laravel11.txt" "$project_route/.htaccess"
		elif [[ "$laravel_version" == "12.*" ]]; then
			cp "$route/Pete/vendor/peteconsuegra/wordpress-plus-laravel-plugin/src/templates/same_domain_laravel12_index.php" "$project_route/index.php"
			cp "$route/Pete/vendor/peteconsuegra/wordpress-plus-laravel-plugin/src/templates/htaccess_laravel12.txt" "$project_route/.htaccess"
		fi
	else
		echo "enter if separate_domain"

		if [[ $laravel_version == "10.*" ]]; then
			cp "$route/Pete/vendor/peteconsuegra/wordpress-plus-laravel-plugin/src/templates/separate_domain_laravel10_index.php" "$project_route/public/index.php"
			cp "$route/Pete/vendor/peteconsuegra/wordpress-plus-laravel-plugin/src/templates/htaccess_laravel10.txt" "$project_route/public/.htaccess"
		elif [[ "$laravel_version" == "11.*" ]]; then
			cp "$route/Pete/vendor/peteconsuegra/wordpress-plus-laravel-plugin/src/templates/separate_domain_laravel11_index.php" "$project_route/public/index.php"
			cp "$route/Pete/vendor/peteconsuegra/wordpress-plus-laravel-plugin/src/templates/htaccess_laravel11.txt" "$project_route/public/.htaccess"
		elif [[ "$laravel_version" == "12.*" ]]; then
			cp "$route/Pete/vendor/peteconsuegra/wordpress-plus-laravel-plugin/src/templates/separate_domain_laravel12_index.php" "$project_route/public/index.php"
			cp "$route/Pete/vendor/peteconsuegra/wordpress-plus-laravel-plugin/src/templates/htaccess_laravel12.txt" "$project_route/public/.htaccess"
		fi

	fi

elif [[ $action_name == "Import" ]]; then
	
	echo "cd $project_route && git clone -b $wordpress_laravel_git_branch $wordpress_laravel_git ."
	cd $project_route && git clone -b $wordpress_laravel_git_branch $wordpress_laravel_git .
	echo "cd $project_route && composer install --ignore-platform-reqs 2>&1"
	cd $project_route && composer install --ignore-platform-reqs 2>&1
	
fi

#APACHE OPTIONS#############
############################


rm -rf $project_route/.env
	
echo "
APP_ENV=local
APP_DEBUG=true

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_DRIVER=sync

MAIL_DRIVER=smtp
MAIL_HOST=mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null

DB_CONNECTION=mysql
DB_HOST=$db_host
DB_PORT=3306
DB_DATABASE=$odb
DB_USERNAME=$udb
DB_PASSWORD=$pdb
WP_INTEGRATION=$integration_type
WP_LARAVEL_NAME=$project_name" > $project_route/.env

if [[ $ssl == "true" ]]; then

echo "
WP_URL=https://$wp_url
WP_URL_LOGIN=https://$wp_url/wp-login.php" >> $project_route/.env

else

echo "
WP_URL=http://$wp_url
WP_URL_LOGIN=http://$wp_url/wp-login.php
" >> $project_route/.env

fi
	
mkdir $logs_route/$project_name
touch $logs_route/$project_name/error.log
touch $logs_route/$project_name/access.log
	
find $project_route -type f -exec chmod 644 {} \;    
find $project_route -type d -exec chmod 755 {} \;
chmod -R ug+rwx storage $project_route/bootstrap/cache

if [[ $os_distribution == "ubuntu" ]]; then
	cd /etc/apache2/sites-enabled && ln -s /etc/apache2/sites-available/$project_name.conf $project_name.conf
fi

echo "Running the following commands..."
echo "cd $route/$project_name && php artisan key:generate"

#HACK TO GENERATE APP_KEY
echo "`cd $project_route && php artisan key:generate --show`"
laravel_key=`cd $project_route && php artisan key:generate --show`
echo "APP_KEY=$laravel_key" >> $project_route/.env

#INSTALL wordpress-plus-laravel PACKAGE LEGACY
#cd $project_route && composer require peteconsuegra/wordpress-plus-laravel --update-with-dependencies

