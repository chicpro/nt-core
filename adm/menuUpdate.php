<?php
require_once './_common.php';

$menus = trim($_POST['menus']);

$sql = " update `{$nt['config_table']}` set cf_menus = :cf_menus ";
$DB->prepare($sql);

$result = $DB->execute([':cf_menus' => $menus]);

if (!$result)
    dieJson(_('An error occurred while editing the information. Please try again.'));

dieJson('');