### 필요사항

- PHP 7.0 이상
- MariaDB 또는 Mysql DB Server
- composer
- PDO



### 사용법

```
git clone https://github.com/chicpro/nt-core.git
composer update
```



### 기본설정

##### data 디렉토리
```
$ mkdir data
$ chmod 707 data
```

##### nginx 설정
```
location / {
    try_files $uri $uri/ /index.php?$args;
}
```

##### DB 설정

- config/db.php

```
define('DB_HOST', 'localhost');
define('DB_NAME', 'dbname');
define('DB_USER', 'dbuser');
define('DB_PASS', 'dbpass');

define('DB_ERROR_MODE', ''); // SILENT, WARNING
```

##### 설치
브라우저에서 setup.php 실행
```
http://domain.com/setup.php
```



#### 사용 패키지

- Klein.php : https://github.com/klein/klein.php
- PHPMailer : https://github.com/PHPMailer/PHPMailer
- HTML Purifier : http://htmlpurifier.org/
- mobiledetect : http://mobiledetect.net/
- PHP Paginator : https://github.com/jasongrimes/php-paginator
- PHP ICO - The PHP ICO Generator : https://github.com/chrisbliss18/php-ico
- ua-parser PHP Library : https://github.com/ua-parser/uap-php
- Google Authenticator PHP class : https://github.com/PHPGangsta/GoogleAuthenticator
- evert/sitemap-php : https://github.com/evert/sitemap-php