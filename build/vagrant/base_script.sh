#!/bin/bash

# shits and giggles #
alias fucking=sudo

apt-get update
apt-get -y install python-software-properties

curl -sL https://deb.nodesource.com/setup_6.x | sudo -E bash -

# add needed repos and update #
apt-get update

# basic tools #
apt-get -y install vim curl

# install php and enable it should be v7.0#
apt-get -y install php7.0 php-curl php-gd php-imap php-xdebug php-xml php7.0-bcmath
phpenmod gd
phpenmod imap

service php7.0-fpm restart

echo "xdebug.remote_enable = 1" >> /etc/php/7.0/mods-available/xdebug.ini
echo "xdebug.remote_connect_back = 1" >> /etc/php/7.0/mods-available/xdebug.ini
echo "xdebug.remote_port = 9000" >> /etc/php/7.0/mods-available/xdebug.ini
echo "xdebug.scream=0" >> /etc/php/7.0/mods-available/xdebug.ini
echo "xdebug.cli_color=1" >> /etc/php/7.0/mods-available/xdebug.ini
echo "xdebug.show_local_vars=1" >> /etc/php/7.0/mods-available/xdebug.ini

# install composer globally #
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# install git #
apt-get -y install git

apt-get autoclean