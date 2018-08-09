<?php
ini_set('display_errors', 1);
define('_SETUP_', true);
require_once './_common.php';

$html->setPageTitle('NT-CORE Setup');
$html->addStyleSheet(NT_CSS_URL.DIRECTORY_SEPARATOR.'setup.css', 'header', 0);
$html->addStyleSheet('https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/css/bootstrap.min.css','header', 0, '',  'integrity="sha384-Smlep5jCw/wG7hdkwQ/Z5nLIefveQRIY9nfy6xoR1uRYBtpZgI6339F5dgvm/e9B" crossorigin="anonymous"');

$html->addJavaScript('https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js', 'header', 0);
$html->addJavaScript('https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js', 'footer', 0, '', 'integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"');
$html->addJavaScript('https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/js/bootstrap.min.js', 'footer', 0, '', 'integrity="sha384-o+RDsa0aLu++PJvFqy8fFScvbHFLtbvScb8AjopnFD+iEQ7wo/CG0xlczd+2O/em" crossorigin="anonymous"');

if (!isset($nt['config_table']) || !isset($nt['member_table']) || !empty($config))
    die(_('Setup can not be executed.'));

if (!$DB->pdo)
    die($DB->error);

$mb_id       = trim(strip_tags($_POST['mb_id']));
$mb_name     = trim(strip_tags($_POST['mb_name']));
$mb_email    = trim(strip_tags($_POST['mb_email']));
$mb_password = trim($_POST['mb_password']);

if (strlen($mb_id) < 1)
    alert(_('Please enter ID.'));

if (!preg_match(NT_MEMBER_ID_PATTERN, $mb_id))
    alert(_('Please enter the ID format to fit.'));

if (strlen($mb_name) < 1)
    alert(_('Please enter Name.'));

if (strlen($mb_email) <1)
    alert(_('Please enter Email.'));

if (!preg_match(NT_EMAIL_PATTERN, $mb_email))
    die(_('Please enter the Email format to fit.'));

if (strlen($mb_password) < 1)
    alert(_('Please enter Password'));

// Config Table Create
$sql = " CREATE TABLE `{$nt['config_table']}` (
    `cf_site_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `cf_site_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `cf_site_index` int(11) UNSIGNED DEFAULT NULL,
    `cf_locale` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
    `cf_email_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `cf_email_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `cf_theme` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `cf_page_rows` int(11) NOT NULL,
    `cf_page_limit` int(11) NOT NULL,
    `cf_enc_salt` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
    `cf_max_level` int(11) NOT NULL,
    `cf_member_level` int(11) NOT NULL,
    `cf_super_admin` int(11) NOT NULL,
    `cf_description` text COLLATE utf8mb4_unicode_ci NULL,
    `cf_keywords` text COLLATE utf8mb4_unicode_ci NULL,
    `cf_recaptcha_site_key` varchar(255) COLLATE utf8mb4_unicode_ci NULL,
    `cf_recaptcha_secret_key` varchar(255) COLLATE utf8mb4_unicode_ci NULL,
    `cf_css_version` varchar(10) COLLATE utf8mb4_unicode_ci NULL,
    `cf_js_version` varchar(10) COLLATE utf8mb4_unicode_ci NULL,
    `cf_token_time` int(11) NOT NULL DEFAULT '0',
    `cf_password_length` int(11) NOT NULL DEFAULT '0',
    `cf_2factor_auth` tinyint(4) NOT NULL DEFAULT '0',
    `cf_exclude_agent` text COLLATE utf8mb4_unicode_ci NULL,
    `cf_menus` text COLLATE utf8mb4_unicode_ci NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; ";

if ($DB->exec($sql) === false)
    die($DB->error);

$salt = randomChar(16);

// Config Table Value
$flds = array(
    'cf_site_name'       => 'NT-Core',
    'cf_site_url'        => NT_URL,
    'cf_site_index'      => 0,
    'cf_locale'          => 'ko',
    'cf_email_name'      => 'NT-Core',
    'cf_email_address'   => $mb_email,
    'cf_theme'           => 'simple',
    'cf_page_rows'       => 15,
    'cf_page_limit'      => 10,
    'cf_enc_salt'        => $salt,
    'cf_max_level'       => 10,
    'cf_member_level'    => 2,
    'cf_super_admin'     => 100,
    'cf_keywords'        => '',
    'cf_css_version'     => '',
    'cf_js_version'      => '',
    'cf_token_time'      => 10,
    'cf_password_length' => 8,
    'cf_2factor_auth'    => 0
);

$fields = array();
$holder = array();
$values = array();

foreach ($flds as $key => $val) {
    $fields[] = $key;
    $holder[] = ':'.$key;
    $values[':'.$key] = $val;
}

$sql = " insert into `{$nt['config_table']}` ( ".implode(', ', $fields)." ) values ( ".implode(', ', $holder)." ) ";
$DB->prepare($sql);
$DB->bindValueArray($values);

if ($DB->execute() === false)
    die($DB->error);

$config = getConfig();

// Member Table Create
$sql = " CREATE TABLE `{$nt['member_table']}` (
    `mb_uid` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
    `mb_id` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
    `mb_name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
    `mb_email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    `mb_password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `mb_2factor_auth` tinyint(4) NOT NULL,
    `mb_2factor_secret` varchar(30) COLLATE utf8mb4_unicode_ci NULL,
    `mb_admin` int(11) NOT NULL,
    `mb_level` int(11) NOT NULL,
    `mb_memo` text COLLATE utf8mb4_unicode_ci NULL,
    `mb_leave` date NULL,
    `mb_block` date NULL,
    `mb_date` datetime NOT NULL,
    PRIMARY KEY (`mb_uid`),
    UNIQUE KEY `mb_id` (`mb_id`),
    UNIQUE KEY `mb_email` (`mb_email`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; ";

if ($DB->exec($sql) === false)
    die($DB->error);

// Super Admin Member
$mb_uid      = getMemberUID();
$mb_password = passwordCreate($mb_password);

$sql = " insert into `{$nt['member_table']}` ( mb_uid, mb_id, mb_name, mb_email, mb_password, mb_2factor_auth, mb_admin, mb_level, mb_date ) values ( :mb_uid, :mb_id, :mb_name, :mb_email, :mb_password, :mb_2factor_auth, :mb_admin, :mb_level, :mb_date ) ";

$DB->prepare($sql);
$DB->bindValueArray(
    [
        ':mb_uid'          => $mb_uid,
        ':mb_id'           => $mb_id,
        ':mb_name'         => $mb_name,
        ':mb_email'        => $mb_email,
        ':mb_password'     => $mb_password,
        ':mb_2factor_auth' => 0,
        ':mb_admin'        => $config['cf_super_admin'],
        ':mb_level'        => $config['cf_max_level'],
        ':mb_date'         => NT_TIME_YMDHIS
    ]
);

if (!$DB->execute()) {
    die($DB->error);
}

// Board Table Create
$sql = " CREATE TABLE `{$nt['board_table']}` (
    `bo_no` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `bo_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
    `bo_parent` int(11) UNSIGNED DEFAULT NULL,
    `mb_uid` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `bo_name` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `bo_password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `bo_subject` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `bo_category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `bo_notice` tinyint(4) DEFAULT NULL,
    `bo_secret` tinyint(4) DEFAULT NULL,
    `bo_reply` tinyint(4) UNSIGNED DEFAULT NULL,
    `bo_content` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `bo_comment` int(11) DEFAULT NULL,
    `bo_link` tinyint(4) UNSIGNED DEFAULT NULL,
    `bo_file` tinyint(4) UNSIGNED DEFAULT NULL,
    `bo_view` int(11) UNSIGNED DEFAULT NULL,
    `bo_date` datetime DEFAULT NULL,
    `bo_ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    PRIMARY KEY (`bo_no`),
    KEY `bo_id` (`bo_id`),
    KEY `bo_parent` (`bo_parent`),
    KEY `mb_uid` (`mb_uid`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; ";

if ($DB->exec($sql) === false)
    die($DB->error);

// Board Comment Table Create
$sql = " CREATE TABLE `{$nt['board_comment_table']}` (
    `cm_no` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `bo_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
    `bo_no` int(11) UNSIGNED NOT NULL,
    `cm_parent` int(11) UNSIGNED DEFAULT NULL,
    `mb_uid` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `cm_name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
    `cm_password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `cm_reply` tinyint(4) DEFAULT NULL,
    `cm_content` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `cm_date` datetime DEFAULT NULL,
    `cm_ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    PRIMARY KEY (`cm_no`),
    KEY `bo_id` (`bo_id`),
    KEY `bo_no` (`bo_no`),
    KEY `cm_parent` (`cm_parent`),
    KEY `mb_uid` (`mb_uid`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; ";

if ($DB->exec($sql) === false)
    die($DB->error);

// Board Config Table Create
$sql = " CREATE TABLE `{$nt['board_config_table']}` (
    `bo_id` varchar(20) NOT NULL,
    `bo_title` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `bo_use` tinyint(4) DEFAULT NULL,
    `bo_category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `bo_subject_len` int(11) DEFAULT NULL,
    `bo_page_rows` int(11) DEFAULT NULL,
    `bo_page_limit` int(11) DEFAULT NULL,
    `bo_list_level` int(11) DEFAULT NULL,
    `bo_view_level` int(11) DEFAULT NULL,
    `bo_write_level` int(11) DEFAULT NULL,
    `bo_comment_level` int(11) DEFAULT NULL,
    `bo_reply_level` int(11) DEFAULT NULL,
    `bo_file_limit` int(11) DEFAULT NULL,
    `bo_link_limit` int(11) DEFAULT NULL,
    `bo_captcha_use` tinyint(4) DEFAULT NULL,
    `bo_captcha_comment` tinyint(4) DEFAULT NULL,
    PRIMARY KEY (`bo_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; ";

if ($DB->exec($sql) === false)
    die($DB->error);

// Board File Table Create
$sql = " CREATE TABLE `{$nt['board_file_table']}` (
    `bo_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
    `bo_no` int(11) UNSIGNED DEFAULT NULL,
    `fl_no` int(11) UNSIGNED NOT NULL,
    `fl_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `fl_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `fl_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `fl_down` int(11) DEFAULT NULL,
    KEY `bo_id` (`bo_id`),
    KEY `bo_no` (`bo_no`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; ";

if ($DB->exec($sql) === false)
    die($DB->error);

// Baord Link Table Create
$sql = " CREATE TABLE `{$nt['board_link_table']}` (
    `bo_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
    `bo_no` int(11) UNSIGNED DEFAULT NULL,
    `ln_no` int(11) UNSIGNED NOT NULL,
    `ln_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    KEY `bo_id` (`bo_id`),
    KEY `bo_no` (`bo_no`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; ";

if ($DB->exec($sql) === false)
    die($DB->error);

// free Board Create
$sql = " insert into `{$nt['board_config_table']}` ( bo_id, bo_title, bo_use, bo_subject_len, bo_page_rows, bo_page_limit, bo_list_level, bo_view_level, bo_write_level, bo_comment_level, bo_reply_level, bo_file_limit, bo_link_limit, bo_captcha_use, bo_captcha_comment ) values ( 'free', 'Free Board', '1', '50', '{$config['cf_page_rows']}', '{$config['cf_page_limit']}', '1', '1', '1', '1', '1', '2', '1', '0', '0' ) ";

if ($DB->exec($sql) === false)
    die($DB->error);

$sql = " CREATE TABLE `{$nt['visit_table']}` (
    `vi_date` date NOT NULL,
    `vi_time` time NOT NULL,
    `vi_referer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `vi_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `vi_ip` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
    KEY `vi_date` (`vi_date`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; ";

if ($DB->exec($sql) === false)
    die($DB->error);

// Pages table
$sql = " CREATE TABLE `{$nt['pages_table']}` (
    `pg_no` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `pg_id` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
    `pg_subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `pg_content` text COLLATE utf8mb4_unicode_ci NOT NULL,
    `pg_css` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `pg_header` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `pg_footer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `pg_use` tinyint(4) DEFAULT NULL,
    `pg_views` int(11) UNSIGNED NOT NULL,
    `pg_date` datetime NOT NULL,
    `pg_ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    PRIMARY KEY (`pg_no`),
    UNIQUE KEY `pg_id` (`pg_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; ";


if ($DB->exec($sql) === false)
    die($DB->error);

// Tags table
$sql = " CREATE TABLE `{$nt['tags_table']}` (
    `tg_type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
    `tg_no` int(11) UNSIGNED NOT NULL,
    `tg_word` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    KEY `tg_type` (`tg_type`),
    KEY `tg_no` (`tg_no`),
    KEY `tg_word` (`tg_word`),
    UNIQUE KEY `tg_unique` (`tg_type`,`tg_no`,`tg_word`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; ";


if ($DB->exec($sql) === false)
    die($DB->error);

// Directory Create
@mkdir(NT_FILE_PATH, 0755, true);
@mkdir(NT_CACHE_PATH, 0755, true);
@mkdir(NT_SESSION_PATH, 0755, true);

// Delete locale files
if (isset($NTLOCALE)) {
    $localeDir = NT_DATA_PATH.DIRECTORY_SEPARATOR.$NTLOCALE->localeDirectory;

    $mtime = 0;
    $files = array_diff(scandir($localeDir), array('.', '..'));
    foreach ($files as $f) {
        if (is_dir($localeDir.DIRECTORY_SEPARATOR.$f))
            $mtime = $f;
    }

    $NTLOCALE->deleteLocaleFile($localeDir.DIRECTORY_SEPARATOR.$mtime);
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title><?php echo $html->title; ?></title>
<?php
echo $html->getPageStyle('header');
echo $html->getStyleString('header');
echo $html->getPageScript('header');
echo $html->getScriptString('header');
?>
</head>
<body>

<div class="highlight-clean">
    <div class="container">
        <div class="intro">
            <h2 class="text-center"><?php echo _('Complete!'); ?></h2>
            <p class="text-center"><?php echo _('NT-Core Setup is done well'); ?></p>
            <a class="btn btn-primary col-md-6" role="button" href="<?php echo NT_URL; ?>"><?php echo _('Home'); ?></a>
        </div>
    </div>
</div>

<?php
echo $html->getPageStyle('footer');
echo $html->getStyleString('footer');
echo $html->getPageScript('footer');
echo $html->getScriptString('footer');
?>
</body>
</html>