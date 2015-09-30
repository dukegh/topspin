# Beginer Site for article and photo management

## Setup

    useradd -m duke -s /bin/bash
    echo termcapinfo xterm ti@:te@ > ~/.screenrc

    apt-get update
    apt-get upgrade
    reboot
    apt-get -y install nginx git mysql-server-5.6 php5-cli php5-fpm php5-mysql php5-gd unzip

    curl -sL https://deb.nodesource.com/setup_0.12 | bash -
    apt-get install nodejs
    npm install -g gulp

    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer

    useradd -m topspin -s /bin/bash
    usermod -a -G www-data topspin
    su - topspin
    sed -i '1i export PATH="$HOME/.composer/vendor/bin:$PATH"' $HOME/.bashrc
    mkdir ~/logs
    mkdir -p ~/www/imgcache
    chgrp www-data ~/logs ~/www/imgcache

    git clone https://github.com/dukegh/topspin.git
    ln -s ../topspin/public/appfiles ~/www/appfiles
    cd topspin
    composer install
    composer update
    npm install
    gulp
    mkdir -p storage/framework/views
    mkdir storage/framework/sessions
    mkdir storage/framework/cache
    chgrp www-data storage storage/framework/views storage/framework/sessions storage/framework/cache public/appfiles

    echo 'CREATE DATABASE topspin CHARACTER SET utf8 COLLATE utf8_general_ci;'|mysql -uroot
    echo "GRANT ALL PRIVILEGES ON topspin.* TO 'topspin'@'localhost' IDENTIFIED BY 'topspinpwd';"|mysql -uroot
    php artisan migrate
    php artisan db:seed

## nginx config file
vim /etc/nginx/sites-available/topspin

    server {
        server_name topspin.sn00.net;
        charset utf-8;
        access_log off;
        sendfile off;
        client_max_body_size 100m;
        index index.html index.htm index.php;
        error_log /home/topspin/logs/topspin-error.log error;
        root /home/topspin/topspin/public;
    
        location / { try_files $uri $uri/ /index.php?$query_string; }
        location = (/favicon.ico|/robots.txt) { access_log off; log_not_found off; break;}
        location ~* ^.+\.(bmp|jpg|jpeg|pjpeg|gif|ico|cur|png|css|doc|txt|js|docx|rtf|ppt|pdf|svg|swf|flv|3gp|dll|msi|cdr|cdd|cue|cdi|mkv|nrg|pdi|mds|mdf|arj|zip|tgz|gz|rar|bz2|7z|xls|exe|tar|wav|avi|mp3|mp4|mov|wmv|vob|iso|mpg|midi|cda|wma|htm)$ { break; }
    
        location ~ \.php$ {
            fastcgi_pass unix:/var/run/php5-fpm.sock;
            fastcgi_split_path_info ^(.+\.php)(/.+)$;
            fastcgi_index index.php;
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
            include fastcgi_params;
            access_log /home/topspin/logs/topspin-access.log;
            fastcgi_intercept_errors off;
            fastcgi_buffer_size 16k;
            fastcgi_buffers 4 16k;
        }
    }
    
    proxy_cache_path /home/topspin/www/imgcache levels=1:2 keys_zone=thumbs:80m inactive=24h max_size=5G;
    
    server {
    	server_name img.topspin.sn00.net;
    	root /home/topspin/www/appfiles;
    	access_log off;
    	expires 365d;
    
    	location ~ ^[^_]+\.(jpg|gif|png)$ { break; }
    
    	location ~ .(jpg|gif|png)$ {
    		rewrite (.*) /$1 break;
    		proxy_pass http://unix:/tmp/nginxImgCache.sock:;
    		proxy_cache thumbs;
    		proxy_cache_valid 200 24h;
    		proxy_cache_valid 404 415 1m;
    	}
    
    	location / { return 404; }
    }
    
    server {
    	listen  unix:/tmp/nginxImgCache.sock;
    	location ~ ^/(?<img>[^_]+)_(?<w>\d+|-)x(?<h>\d+|-)\.(?<ext>jpg|gif|png)$ {
    		root /home/topspin/www/appfiles;
    		try_files /${img}.${ext} =404;
    		image_filter crop $w $h;
    	}
    }

ln -s /etc/nginx/sites-available/topspin /etc/nginx/sites-enabled/topspin

service nginx restart