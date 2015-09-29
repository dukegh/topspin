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

    wget https://github.com/dukegh/topspin/archive/master.zip
    unzip master.zip
    mv topspin-master topspin
    cd topspin
    composer install
    composer update
    npm install
    gulp

    echo 'CREATE DATABASE topspin CHARACTER SET utf8 COLLATE utf8_general_ci;'|mysql -uroot
    echo "GRANT ALL PRIVILEGES ON topspin.* TO 'topspin'@'localhost' IDENTIFIED BY 'topspinpwd';"|mysql -uroot
    php artisan migrate
    php artisan db:seed

