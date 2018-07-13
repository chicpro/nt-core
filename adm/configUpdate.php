<?php
require_once './_common.php';

if (!$isSuper)
    dieJson(_('Only super administrators can access.'));

$token = new TOKEN();

// 토큰체크
if (!$token->verifyToken($_POST['token'], 'ss_adm_token'))
    dieJson(_('Please use it in the correct way.'));

// Favicon
if (isset($_POST['favicon_del']) && $_POST['favicon_del']) {
    @unlink(NT_FILE_PATH.DIRECTORY_SEPARATOR.'favicon.ico');
}

if ($_FILES['favicon']['name'] && is_uploaded_file($_FILES['favicon']['tmp_name'])) {
    $sizes = array( array(32, 32), array(180, 180), array(192,192), array(270, 270) );

    $icoSource = $_FILES['favicon']['tmp_name'];
    $icoTarget = NT_FILE_PATH.DIRECTORY_SEPARATOR.'favicon.ico';

    $icoLib = new PHP_ICO($icoSource, $sizes);
    $icoLib ->save_ico($icoTarget);
}

$flds = array(
    'cf_theme',
    'cf_locale',
    'cf_site_name',
    'cf_site_url',
    'cf_site_index',
    'cf_email_name',
    'cf_email_address',
    'cf_page_rows',
    'cf_page_limit',
    'cf_max_level',
    'cf_member_level',
    'cf_super_admin',
    'cf_description',
    'cf_keywords',
    'cf_recaptcha_site_key',
    'cf_recaptcha_secret_key',
    'cf_css_version',
    'cf_js_version',
    'cf_token_time',
    'cf_password_length',
    'cf_2factor_auth',
    'cf_exclude_agent'
);

$sql = " update `{$nt['config_table']}` set ";

$values = array();
$querys = array();

foreach ($flds as $k) {
    $querys[] = "{$k} = :{$k}";
    $values[':'.$k] = trim($_POST[$k]);
}

$sql .= implode(', ', $querys);
$DB->prepare($sql);
$DB->bindValueArray($values);
$result = $DB->execute();

if (!$result)
    dieJson(_('An error occurred while editing the information. Please try again.'));
else
    dieJson('');