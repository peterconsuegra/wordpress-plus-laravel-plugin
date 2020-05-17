#!/bin/bash

while getopts p:r:m:a:b:g:u:j:v:n:l:e:s:t:w:c:o:k:q:y:i: option 
do 
case "${option}" 
	in 
	p) db_root_pass=${OPTARG};;
	r) route=${OPTARG};;
	m) mysqlcommand=${OPTARG};;
	a) server_conf=${OPTARG};;
	b) wordpress_laravel_git_branch=${OPTARG};;
	g) wordpress_laravel_git=${OPTARG};;
	u) wordpress_laravel_url=${OPTARG};;
	j) os_version=${OPTARG};;
	v) os=${OPTARG};;
	n) project_name=${OPTARG};;
	l) wp_load_path=${OPTARG};;
	e) wp_url=${OPTARG};;
	s) id=${OPTARG};;
	t) server=${OPTARG};;
	w) server_version=${OPTARG};;
	c) action_name=${OPTARG};;
	o) laravel_version=${OPTARG};;
	k) debug=${OPTARG};;
	q) odb=${OPTARG};;
	y) udb=${OPTARG};;
	i) pdb=${OPTARG};;
esac 
done

#DEBUG
if test "$debug" = 'active'; then

echo "db_root_pass: $db_root_pass"
echo "app_root: $route" 
echo "mysqlcommand: $mysqlcommand"
echo "server_conf: $server_conf"
echo "wordpress_laravel_git_branch: $wordpress_laravel_git_branch"
echo "wordpress_laravel_git: $wordpress_laravel_git"
echo "wordpress_laravel_url: $wordpress_laravel_url"
echo "os_version: $os_version"
echo "os: $os"
echo "site_name: $project_name"
echo "wp_load_path: $wp_load_path"
echo "wp_url: $wp_url"
echo "id $id"
echo "apache_version $apache_version"
echo "debug $debug"
echo "DB: "$odb
echo "UDB: "$udb
echo "PDB: "$pdb

fi

if test "$action_name" = 'New'; then
	cd $route && php /usr/local/bin/composer.phar create-project laravel/laravel=$laravel_version $project_name --prefer-dist 2>&1
else
	cd $route && git clone -b $wordpress_laravel_git_branch $wordpress_laravel_git $project_name
fi

#APACHE OPTIONS#############
############################

if test "$server" = 'apache'; then

rm -rf $route/$project_name/.env
	
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

DB_HOST=localhost
DB_DATABASE=$odb
DB_USERNAME=$udb
DB_PASSWORD=$pdb

WP_LOAD_PATH=$wp_load_path
WP_URL=http://$wp_url" > $route/$project_name/.env
	

	username=`id -un`
    logs_route=/Users/$username/wwwlog
	mkdir $logs_route/$project_name
	touch $logs_route/$project_name/error.log
	touch $logs_route/$project_name/access.log
	
	echo "
	<VirtualHost *:80>

	    ServerName $wordpress_laravel_url
	    ServerAlias www.$wordpress_laravel_url
	    DocumentRoot $route/$project_name/public
			
	      <Directory $route/$project_name>
              SetOutputFilter DEFLATE
              Options FollowSymLinks
              AllowOverride All
              Order Deny,Allow
              Require all granted
	      </Directory>
		
  	    ErrorLog $logs_route/$project_name/error.log
  	    CustomLog $logs_route/$project_name/access.log combined
		
	</VirtualHost>" > $server_conf/$project_name.conf
	
find $route/$project_name -type f -exec chmod 644 {} \;    
find $route/$project_name -type d -exec chmod 755 {} \;
chmod -R ug+rwx storage $route/$project_name/bootstrap/cache

fi

echo "Running the following commands..."
echo "cd $route/$project_name && php artisan key:generate"
echo "cd $route/$project_name && php artisan migrate"

#RUN COMPOSER INSTALL
echo "cd $route/$project_name && php /usr/local/bin/composer.phar install 2>&1"
cd $route/$project_name && php /usr/local/bin/composer.phar install 2>&1
#HACK TO GENERATE APP_KEY
echo "`cd $route/$project_name && php artisan key:generate --show`"
laravel_key=`cd $route/$project_name && php artisan key:generate --show`
echo "APP_KEY=$laravel_key" >> $route/$project_name/.env

#INSTALL wordpress-plus-laravel PACKAGE
cd $route/$project_name && php /usr/local/bin/composer.phar require peteconsuegra/wordpress-plus-laravel:dev-master
cd $route/$project_name && php artisan migrate

echo "Executing aditional operations..."
cd $route/$project_name && php artisan new_wordpress_plus_laravel






