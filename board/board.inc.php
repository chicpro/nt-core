<?php
$id = preg_replace(NT_BOARD_ID_PATTERN, '', $_REQUEST['id']);

// 게시판 체크
$board = getBoardConfig($id);

if (!$board['bo_id'] || (!$isAdmin && !$board['bo_use']))
    alert(_('The board does not exist.'));

// 게시글 리스트
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

if (isset($_REQUEST['p']))
    $p = (int)preg_replace('#[^0-9]#', '', $_REQUEST['p']);