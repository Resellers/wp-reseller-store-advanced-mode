#!/bin/bash

# install lamp server
yum -y install httpd mariadb-server svn php php-mysql php-gd php-xml
service httpd start
service mariadb start
chkconfig mariadb on
chkconfig httpd on

# download and copy wordpress to web folder

mkdir /wpinstall
cd /wpinstall
wget http://wordpress.org/latest.tar.gz
tar xzvf latest.tar.gz
rsync -avP /wpinstall/wordpress/ /var/www/html/
mkdir /var/www/html/wp-content/uploads
chown -R apache:apache /var/www/html/*

# configure wordpress
CONFIG_FILE=wp-config.php
cd /var/www/html
yes | cp wp-config-sample.php $CONFIG_FILE
chown apache:apache $CONFIG_FILE

#generate password
PASSWORD=$(date +%s|sha256sum|base64|head -c 32)

SRC="'WP_DEBUG', false"; DST="'WP_DEBUG', true"; sed -i "s/$SRC/$DST/g" $CONFIG_FILE
SRC="'DB_NAME', 'database_name_here'"; DST="'DB_NAME', 'wordpress'"; sed -i "s/$SRC/$DST/g" $CONFIG_FILE
SRC="'DB_USER', 'username_here'"; DST="'DB_USER', 'wordpressuser'"; sed -i "s/$SRC/$DST/g" $CONFIG_FILE
SRC="'DB_PASSWORD', 'password_here'"; DST="'DB_PASSWORD', '$PASSWORD'"; sed -i "s/$SRC/$DST/g" $CONFIG_FILE
SRC="'WP_DEBUG'"; DST="define( 'SCRIPT_DEBUG', true );"; grep -q "$DST" $CONFIG_FILE || sed -i "/$SRC/a$DST" $CONFIG_FILE

# create wordpress database
mysql -u root -e "CREATE DATABASE wordpress;"
mysql -u root -e "CREATE DATABASE wordpress;"
mysql -u root -e "CREATE USER wordpressuser@localhost IDENTIFIED BY '$PASSWORD';"
mysql -u root -e "GRANT ALL PRIVILEGES ON wordpress.* TO wordpressuser@localhost IDENTIFIED BY '$PASSWORD';"
mysql -u root -e "FLUSH PRIVILEGES;"

# install phpUnit
cd /wpinstall
wget https://phar.phpunit.de/phpunit-old.phar
chmod +x phpunit-old.phar
mv phpunit-old.phar /usr/local/bin/phpunit

# install wp-cli
wget https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
chmod +x wp-cli.phar
mv wp-cli.phar /usr/local/bin/wp

# install PHP_CodeSniffer
wget https://squizlabs.github.io/PHP_CodeSniffer/phpcs.phar
chmod +x phpcs.phar
mv phpcs.phar /usr/local/bin/phpcs

wget https://squizlabs.github.io/PHP_CodeSniffer/phpcbf.phar
chmod +x phpcbf.phar
mv phpcbf.phar /usr/local/bin/phpcbf
# configure WordPress Coding Standards

git clone -b master https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards.git wpcs
phpcs --config-set installed_paths ~/wpcs

cd /var/www/html
wp core install --url=$HOSTNAME --title="reseller store" --admin_user=bryan --admin_email=bxfocht@godaddy.com
wp theme install primer --activate
chown -R apache:apache /var/www/html/*