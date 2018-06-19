### opct-db API 

this project forked from [optc-db/optc-db.github.io](https://github.com/optc-db/optc-db.github.io)
#### how to install

install composer

`php composer.phar requre slim/slim "^3.0"`

`php composer.phar require slim/php-view'`

modify apache conf `/etc/apache2/apache.conf`
```
<Directory /var/www/>
    Allowoverride All
    Require all granted
</Directory>
```

add `.htaccess`
```
RewriteEngine On 
RewriteCond %{REQUEST_FILENAME} !-f 
RewriteRule ^ index.php [QSA,L]
```

`a2enmod rewrite`

`sudo service apache2 restart`
