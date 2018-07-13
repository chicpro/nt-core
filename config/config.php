<?php
// PHPMailer SMTP
define('NT_SMTP_HOST',   '127.0.0.1');
define('NT_SMTP_PORT',   '25');
define('NT_SMTP_AUTH',   false);
define('NT_SMTP_USER',   '');
define('NT_SMTP_PASS',   '');
define('NT_SMTP_SECURE', 'ssl');

// 회원상수
define('NT_MEMBER_ID_PATTERN', '#^[a-z0-9\_]+$#i');

// 일반상수
define('NT_TOKEN_LENGTH',  16);
define('NT_EMAIL_PATTERN', '/^([0-9a-zA-Z_\-\.]+)@([0-9a-zA-Z_\-\.]+)\.([0-9a-zA-Z_\-]+)$/');

// 게시판 상수
define('NT_BOARD_ID_PATTERN', '#[^a-z0-9\_]#i');

// Locales
$_LOCALES = array(
    'ko' => array('ko_KR', 'Korean'),
    'en' => array('en_US', 'English')
);

// DB 테이블
define('NT_TABLE_PREFIX', 'nt_');

$nt['config_table']         = NT_TABLE_PREFIX.'config';
$nt['member_table']         = NT_TABLE_PREFIX.'member';
$nt['board_table']          = NT_TABLE_PREFIX.'board';
$nt['board_config_table']   = NT_TABLE_PREFIX.'board_config';
$nt['board_comment_table']  = NT_TABLE_PREFIX.'board_comment';
$nt['board_file_table']     = NT_TABLE_PREFIX.'board_file';
$nt['board_link_table']     = NT_TABLE_PREFIX.'board_link';
$nt['visit_table']          = NT_TABLE_PREFIX.'visit';
$nt['pages_table']          = NT_TABLE_PREFIX.'pages';
$nt['tags_table']           = NT_TABLE_PREFIX.'tags';