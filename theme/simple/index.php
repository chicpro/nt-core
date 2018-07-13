<?php
require_once './_common.php';

$html->setPageTitle('NT-Core');
//$html->addStyleSheet(NT_CSS_URL.DIRECTORY_SEPARATOR.'default.css', 'header', 10, __c('cf_css_version'));
//$html->addStyleSheet(NT_CSS_URL.DIRECTORY_SEPARATOR.'footer.css', 'footer', 10, __c('cf_css_version'));
//$html->addJavaScript(NT_JS_URL.DIRECTORY_SEPARATOR.'common.js', 'header', 10, __c('cf_js_version'));
//$html->addJavaScript(NT_JS_URL.DIRECTORY_SEPARATOR.'footer.js', 'tooter', 10, __c('cf_js_version'));

$html->addMetaTag('canonical', NT_URL);
$html->addOGTag('url', NT_URL);

$html->getPageHeader();
?>

<div class="index">
    <div class="col-3 mx-auto">
        <p class="text-center text-info mb-4">
            <i class="icon" data-feather="monitor"></i>
        </p>
        <p class="h1 text-muted text-center mb-4">NT-Core</p>
    </div>
</div>

<?php
$html->getPageFooter();
?>