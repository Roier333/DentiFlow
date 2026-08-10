#!/bin/sh

# Railway provides the port to which its health check and public proxy connect.
# Apache defaults to port 80, so adapt both its listener and virtual host at boot.
: "${PORT:=8080}"

sed -i "s/^Listen 80$/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

exec apache2-foreground
