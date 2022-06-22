clear
cd public/ 
sudo systemctl start mariadb
sudo systemctl --no-pager status mariadb
sudo php -S localhost:80