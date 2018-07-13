<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<?php
echo $html->getFavicon();

if (trim(__c('cf_keywords')))
    echo '<meta name="keywords" content="'.getHtmlChar(__c('cf_keywords')).'">'.PHP_EOL;
?>
<title><?php echo $html->title; ?></title>
<?php
echo $html->getPageStyle('header');
echo $html->getStyleString('header');
echo $html->getPageScript('header');
echo $html->getScriptString('header');
?>
</head>
<body>