#!/bin/bash
echo "Sync the code to document root"
rsync -avh --chown=apache:apache /home/centos/temp/ /var/www/html/webcontent
service httpd start
