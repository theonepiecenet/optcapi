### opct-db API 

this project forked from [optc-db/optc-db.github.io](https://github.com/optc-db/optc-db.github.io)
#### how to install

install composer

`php composer.phar requre slim/slim "^3.0"`

`php composer.phar require slim/php-view'`

`curl -s http://getcomposer.org/installer | php`

`php /var/www/html/composer.phar install --no-dev`


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

#### how to use api ###

```
You don't need permission not now. 

I will notify you through github when permission is needed in the future.
```

##### 1. get character info #####
(GET) `/{lang}/character/{id}`

##### 2. get tags list #####
(GET) `/{lang}/tags`

##### 3. get search result #####
(POST) `/{lang}/search`

parameters

* key(string)

  * name(string) - character name. ex) `luffy`
  * type(array) - type list. ex) `['PSY', 'INT']`
  * class(array) - class list. ex) `['Fighter', 'Slasher']`
  * captain(array) - captain ability. ex) `['Universal ATK boosting captain', 'Type-boosting captain']`
  * special(array) - special ability.
  * sailor(array) - sailor ability.
  * limit(array) - limit break ability.
