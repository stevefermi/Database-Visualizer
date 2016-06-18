# Overview

Database Visualizer is some kind of tool which helps you to visualize your data in database like mysql, oracle and etc. You are allowed to share the chart with your friends to get comments or likes.

It helps you to draw bar chart, line chart and point chart right now. More types of chart will be added soon.

![shared_chart.jpg](https://ooo.0o0.ooo/2016/06/15/57616dc267a1f.jpg)


# Install

The installation is not quite simple right now. 

### Prerequisite

Apache or Nginx or any other HTTP Server is needed to support the backend server.

PHP is also needed.

### Procedure

1. Download or ``` git clone ``` the whole repo. put them into /var/www/html
2. write .htaccess or any other configuration files. .htaccess for an example:
```
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond $1 !^(visualizer/index\.php)
RewriteRule ^(.*)$ /visualizer/index.php/$1 [L]  
```
3. Open your browser and type: 127.0.0.1/webpage/index.html

# Version

Currently the version is 0.000000001 alpha. The function is very simple and easy. There's lots of bugs at the same time.

# Contributor

@stevefermi

@cyy

# Special Thanks

Alipay G2 : https://g2.alipay.com/

phpRS : https://github.com/caoym/phprs-restful

ezSQL : https://github.com/caoym/ezsql

Mustache.js : https://github.com/janl/mustache.js