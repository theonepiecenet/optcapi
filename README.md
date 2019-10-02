### opct-db API 

this project forked from [optc-db/optc-db.github.io](https://github.com/optc-db/optc-db.github.io)

#### character info public API

개발 편의를 위해 캐릭터 정보 조회 API를 공개합니다. API는 예고없이 사용이 종료되거나 인증이 추가될 수 있음을 알려드립니다. 
모든 호출은 GET이며, 모든 응답은 JSON입니다.

* 캐릭터 정보 조회 : https://api.theonepiece.net/character/{characterNo}
 * ex) https://api.theonepiece.net/character/4
 * ex) https://api.theonepiece.net/character/1879
* 캐릭터 영문 정보 조회 : https://api.theonepiece.net/character/en/{characterNo}
 * ex) https://api.theonepiece.net/character/en/4
 * ex) https://api.theonepiece.net/character/en/1879
* 캐릭터 한글 번역 정보 조회 : https://api.theonepiece.net/character/kr/{characterNo} 
 * 번역된 데이터만 리턴됩니다.
 * 영문 정보와 조합해서 사용하시면 좋습니다.
 * ex) https://api.theonepiece.net/character/kr/4
 * ex) https://api.theonepiece.net/character/kr/1879

#### how to install

install composer

`curl -s http://getcomposer.org/installer | php`

`php composer.phar require slim/slim "^3.0"`

`php composer.phar require slim/php-view`

`php /var/www/html/composer.phar install --no-dev`


modify apache conf `/etc/apache2/apache2.conf`
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

