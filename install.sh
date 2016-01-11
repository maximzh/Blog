#!/bin/bash
clear
echo "Choose your destiny:"

echo "1) install project"
echo "2) create database"
echo "3) load fixtures"
echo "4) run tests"
echo "5) clear cache"
echo "6) exit"

read choice
case "$choice" in

1) echo "installing project..."
composer install
npm install
./node_modules/.bin/bower install
./node_modules/.bin/gulp
;;

2) echo "creating db and loading fixtures..."
app/console doctrine:database:drop --force
app/console doctrine:database:create
app/console doctrine:schema:update --force
;;

3) echo "loading fixtures..."
app/console doctrine:schema:update --force
app/console hautelook_alice:doctrine:fixtures:load -n
;;

4) echo "running tests..."
php phpunit -c app
;;

5) echo "clearing cache..."
app/console cache:clear -e dev
app/console cache:clear -e test
app/console cache:clear -e prod
;;

6)
exit 0
;;

esac

