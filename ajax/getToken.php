<?php
require_once './_common.php';

$type = preg_replace('#[^a-z0-9]#i', '', $_GET['type']);

$token = new TOKEN();

if ($type) {
    $type = 'ss_'.$type.'_token';
    $t = $token->getToken($type);
} else {
    $t = $token->getToken();
}

die($t);