#!/bin/bash
clear
echo "Choose your action:"

echo "1) install project"
echo "2) drop and recreate database"
echo "3) load fixtures"
echo "4) run tests"
echo "5) clear cache"
echo "6) generate entities AppBundle"
echo "7) update database schema"
echo "0) exit"

read choice
case "$choice" in

1) echo "installing project..."
composer install
npm install
./node_modules/.bin/bower install
./node_modules/.bin/gulp
;;

2) echo "creating db..."
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

6) echo "generating entities..."
app/console doctrine:generate:entities AppBundle
;;

7) echo "updating schema..."
app/console doctrine:schema:update --force
;;

0)
exit 0
;;

esac

