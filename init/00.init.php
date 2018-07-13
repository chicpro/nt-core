<?php
/**
 * Basci Config, Member
 */

// Configuration
$config = getConfig();

$isGuest  = true;
$isGember = false;
$isAdmin  = false;
$isSuper  = false;

$member['mb_level'] = 1;

if($_SESSION['ss_uid']) {
    $member = getMember($_SESSION['ss_uid']);

    if($member['mb_uid']) {
        if(!isNullTime((string)$member['mb_leave']) || !isNullTime((string)$member['mb_block'])) {
            unset($_SESSION['ss_uid']);
            unset($member);
        }

        $isMember = true;
        $isGuest  = false;

        if($member['mb_admin']) {
            $isAdmin = true;

            if($member['mb_admin'] >= __c('cf_super_admin'))
                $isSuper = true;
        }
    } else {
        unset($_SESSION['ss_uid']);
    }
}

// Theme
if (isset($config['cf_theme']) && $config['cf_theme']) {
    $themeDir = NT_PATH.DIRECTORY_SEPARATOR.THEME_DIR.DIRECTORY_SEPARATOR.__c('cf_theme');

    define('NT_THEME_PATH', $themeDir);
    define('NT_THEME_URL',  str_replace(NT_PATH, NT_URL, $themeDir));

    unset($themeDir);

    // Theme Configuration
    require_once NT_THEME_PATH.DIRECTORY_SEPARATOR.'config.php';
}

// Locale
if (isset($_GET['locale'])) {
    $locale = $_GET['locale'];
    setcookie('locale', $locale);
} else if (isset($_COOKIE['locale'])) {
    $locale = $_COOKIE['locale'];
} else if (__c('cf_locale')) {
    $locale = __c('cf_locale');
}

if (array_key_exists($locale, $_LOCALES)) {
    $NTLOCALE = new NTLOCALE();
    $NTLOCALE->addTextDomain('default', NT_LOCALE_PATH);

    // Theme Locale set
    if (defined('THEME_LOCALE_DOMAIN') && defined('NT_THEME_LOCALE_PATH')) {
        $NTLOCALE->addTextDomain(THEME_LOCALE_DOMAIN, NT_THEME_LOCALE_PATH);
    }

    $NTLOCALE->setLocale($locale);
    $NTLOCALE->textDomain('default');

    define('NT_LOCALE', $_LOCALES[$locale][0]);
}

// Visit log
if (!isset($_SESSION['ss_visit_log']) || !$_SESSION['ss_visit_log']) {
    $excludedAgents = array_map('trim', explode("\n", __c('cf_exclude_agent')));

    if (empty($excludedAgents) || str_replace($excludedAgents, '', $_SERVER['HTTP_USER_AGENT']) == $_SERVER['HTTP_USER_AGENT']) {
        $sql = " insert into `{$nt['visit_table']}` ( vi_date, vi_time, vi_referer, vi_agent, vi_ip ) values ( :vi_date, :vi_time, :vi_referer, :vi_agent, :vi_ip ) ";
        $DB->prepare($sql);
        $DB->bindValueArray([
            ':vi_date'    => NT_TIME_YMD,
            ':vi_time'    => NT_TIME_HIS,
            ':vi_referer' => $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : '',
            ':vi_agent'   => $_SERVER['HTTP_USER_AGENT'],
            ':vi_ip'      => $_SERVER['REMOTE_ADDR']
        ]);

        $_SESSION['ss_visit_log'] = $DB->execute();
    }

    unset($excludedAgents);
}

// Setup
if (empty($config) && (!defined('_SETUP_') || _SETUP_ !== true))
    gotoUrl(NT_URL.DIRECTORY_SEPARATOR.'setup.php');