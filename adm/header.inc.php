<?php
$html->addStyleSheet('https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/css/bootstrap.min.css','header', 0, '',  'integrity="sha384-Smlep5jCw/wG7hdkwQ/Z5nLIefveQRIY9nfy6xoR1uRYBtpZgI6339F5dgvm/e9B" crossorigin="anonymous"');
$html->addStyleSheet(NT_CSS_URL.DIRECTORY_SEPARATOR.'adm.css', 'header', 0, __c('cf_css_version'));
$html->addStyleSheet(NT_CSS_URL.DIRECTORY_SEPARATOR.'common.css', 'header', 0, __c('cf_css_version'));

$html->addJavaScript('https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js', 'header', 0);
$html->addJavaScript('https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js', 'footer', 0, '', 'integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"');
$html->addJavaScript('https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/js/bootstrap.min.js', 'footer', 0, '', 'integrity="sha384-o+RDsa0aLu++PJvFqy8fFScvbHFLtbvScb8AjopnFD+iEQ7wo/CG0xlczd+2O/em" crossorigin="anonymous"');
$html->addJavaScript(NT_JS_URL.DIRECTORY_SEPARATOR.'common.js.php', 'footer', 10);
$html->addJavaScript(NT_JS_URL.DIRECTORY_SEPARATOR.'adm.js.php', 'footer', 10);

$html->addScriptString('<script>
var nt_ajax_url = "'.NT_AJAX_URL.'";
var nt_img_url  = "'.NT_IMG_URL.'";
</script>', 'header', 0);