<?php
require_once NT_CONFIG_PATH.DIRECTORY_SEPARATOR.'bootstrap.php';

$html->addStyleSheet(NT_CSS_URL.DIRECTORY_SEPARATOR.'adm.css', 'header', 0, __c('cf_css_version'));
$html->addStyleSheet(NT_CSS_URL.DIRECTORY_SEPARATOR.'common.css', 'header', 0, __c('cf_css_version'));

$html->addJavaScript(NT_JS_URL.DIRECTORY_SEPARATOR.'common.js.php', 'footer', 10);
$html->addJavaScript(NT_JS_URL.DIRECTORY_SEPARATOR.'adm.js.php', 'footer', 10);

$html->addScriptString('<script>
var nt_ajax_url = "'.NT_AJAX_URL.'";
var nt_img_url  = "'.NT_IMG_URL.'";
</script>', 'header', 0);