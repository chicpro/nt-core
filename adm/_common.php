<?php
define('_ADMIN_', true);
require_once '../common.php';

if(!$isAdmin)
    alert(_('Only the administrator can access it.'), NT_URL);

$c = '';
$s = '';
$q = '';
$p = 1;

$qstr = array();

if (isset($_REQUEST['c'])) {
    $c = getSearchColumn($_REQUEST['c']);

    if ($c)
        $qstr['c'] = $c;
}

if (isset($_REQUEST['s']))
    $s = getSearchString($_REQUEST['s']);

if (isset($_REQUEST['q'])) {
    $q = getSearchString($_REQUEST['q']);

    if ($q) {
        $qstr['s'] = $q;
        $qstr['q'] = $q;
    }
}

if (isset($_REQUEST['p'])) {
    $p = (int)preg_replace('#[^0-9]#', '', $_REQUEST['p']);
}

if ($p < 1)
    $p = 1;