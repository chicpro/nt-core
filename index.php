<?php
require_once './_common.php';

$route = new Klein\Klein();

$route->respond('GET', '/', function($request, $response) {
    global $isMember, $isAdmin, $isGuest, $member, $html, $nt, $DB;

    $page = array();

    if (__c('cf_site_index')) {
        $sql = " select pg_no from `{$nt['pages_table']}` where pg_no = :pg_no and pg_use = :pg_use ";
        $DB->prepare($sql);
        $DB->execute([':pg_no' => __c('cf_site_index'), ':pg_use' => 1]);
        $row = $DB->fetch();

        if ($row['pg_no']) {
            $page = getPagesContent($row['pg_no'], false);
        }
    }

    if (!empty($page)) {
        if (isset($page['code']) && $page['code'])
            $response->code((int)$page['code']);

        echo $page['content'];
    } else {
        require_once NT_THEME_PATH.DIRECTORY_SEPARATOR.'index.php';
    }

    $response->send();
});

$route->respond('GET', '/'.SITEMAP_DIR, function($request, $response) {
    $file = NT_SITEMAP_PATH.DIRECTORY_SEPARATOR.'sitemap-index.xml';
    if (!is_file($file)) {
        $response->code(404);
    } else {
        header("Content-Type:text/xml");
        require_once $file;
    }

    $response->send();
});

$route->respond('GET', '/'.SITEMAP_DIR.'/[:file]', function($request, $response) {
    $file = NT_SITEMAP_PATH.DIRECTORY_SEPARATOR.$request->file;
    if (!is_file($file)) {
        $response->code(404);
    } else {
        header("Content-Type:text/xml");
        require_once $file;
    }

    $response->send();
});

$route->respond('GET', '/'.MEMBER_DIR.'/login', function($request, $response) {
    global $isMember, $isAdmin, $isGuest, $member, $html, $nt, $DB;

    require_once NT_MEMBER_PATH.DIRECTORY_SEPARATOR.'login.php';
    $response->send();
});

$route->respond('GET', '/'.MEMBER_DIR.'/terms', function($request, $response) {
    global $isMember, $isAdmin, $isGuest, $member, $html, $nt, $DB;

    require_once NT_MEMBER_PATH.DIRECTORY_SEPARATOR.'terms.php';
    $response->send();
});

$route->respond(array('GET', 'POST'), '/'.MEMBER_DIR.'/signup', function($request, $response) {
    global $isMember, $isAdmin, $isGuest, $member, $html, $nt, $DB;

    require_once NT_MEMBER_PATH.DIRECTORY_SEPARATOR.'signup.php';
    $response->send();
});

$route->respond('GET', '/'.MEMBER_DIR.'/logout', function($request, $response) {
    global $isMember, $isAdmin, $isGuest, $member, $html, $nt, $DB;

    require_once NT_MEMBER_PATH.DIRECTORY_SEPARATOR.'logout.php';
    $response->send();
});

$route->respond('GET', '/'.MEMBER_DIR.'/account', function($request, $response) {
    global $isMember, $isAdmin, $isGuest, $member, $html, $nt, $DB;

    $_GET['w'] = 'u';
    require_once NT_MEMBER_PATH.DIRECTORY_SEPARATOR.'signup.php';
    $response->send();
});

$route->respond('GET', '/'.MEMBER_DIR.'/find', function($request, $response) {
    global $isMember, $isAdmin, $isGuest, $member, $html, $nt, $DB;

    require_once NT_MEMBER_PATH.DIRECTORY_SEPARATOR.'findPassword.php';
    $response->send();
});

$route->respond('GET', '/'.MEMBER_DIR.'/password', function($request, $response) {
    global $isMember, $isAdmin, $isGuest, $member, $html, $nt, $DB;

    require_once NT_MEMBER_PATH.DIRECTORY_SEPARATOR.'password.php';
    $response->send();
});

$route->respond('GET', '/'.MEMBER_DIR.'/leave', function($request, $response) {
    global $isMember, $isAdmin, $isGuest, $member, $html, $nt, $DB;

    require_once NT_MEMBER_PATH.DIRECTORY_SEPARATOR.'memberLeave.php';
    $response->send();
});

$route->respond('GET', '/'.BOARD_DIR.'/[:id]', function($request, $response) {
    global $isMember, $isAdmin, $isGuest, $member, $html, $nt, $DB;

    $_REQUEST['id'] = $request->id;
    require_once NT_BOARD_PATH.DIRECTORY_SEPARATOR.'list.php';
    $response->send();
});

$route->respond('GET', '/'.BOARD_DIR.'/[:id]/p/[i:page]', function($request, $response) {
    global $isMember, $isAdmin, $isGuest, $member, $html, $nt, $DB;

    $_REQUEST['id'] = $request->id;
    $_REQUEST['p']  = $request->page;
    require_once NT_BOARD_PATH.DIRECTORY_SEPARATOR.'list.php';
    $response->send();
});

$route->respond('GET', '/'.BOARD_DIR.'/[:id]/write', function($request, $response) {
    global $isMember, $isAdmin, $isGuest, $member, $html, $nt, $DB;

    $_REQUEST['w']  = '';
    $_REQUEST['id'] = $request->id;
    require_once NT_BOARD_PATH.DIRECTORY_SEPARATOR.'write.php';
    $response->send();
});

$route->respond('GET', '/'.BOARD_DIR.'/[:id]/edit/[i:no]', function($request, $response) {
    global $isMember, $isAdmin, $isGuest, $member, $html, $nt, $DB;

    $_REQUEST['w']  = 'u';
    $_REQUEST['id'] = $request->id;
    $_REQUEST['no'] = $request->no;
    require_once NT_BOARD_PATH.DIRECTORY_SEPARATOR.'write.php';
    $response->send();
});

$route->respond('GET', '/'.BOARD_DIR.'/[:id]/reply/[i:no]', function($request, $response) {
    global $isMember, $isAdmin, $isGuest, $member, $html, $nt, $DB;

    $_REQUEST['w']  = 'r';
    $_REQUEST['id'] = $request->id;
    $_REQUEST['no'] = $request->no;
    require_once NT_BOARD_PATH.DIRECTORY_SEPARATOR.'write.php';
    $response->send();
});

$route->respond('GET', '/'.BOARD_DIR.'/[:id]/[i:no]', function($request, $response) {
    global $isMember, $isAdmin, $isGuest, $member, $html, $nt, $DB;

    $_REQUEST['id'] = $request->id;
    $_REQUEST['no'] = $request->no;
    require_once NT_BOARD_PATH.DIRECTORY_SEPARATOR.'view.php';
    $response->send();
});

$route->respond('GET', '/download/[:id]/[i:no]/[i:fn]', function($request, $response) {
    global $isMember, $isAdmin, $isGuest, $member, $html, $nt, $DB;

    $_REQUEST['id'] = $request->id;
    $_REQUEST['no'] = $request->no;
    $_REQUEST['fn'] = $request->fn;
    require_once NT_BOARD_PATH.DIRECTORY_SEPARATOR.'download.php';
    $response->send();
});

$route->respond('GET', '/'.BOARD_DIR.'/[:id]/delete/[i:no]', function($request, $response) {
    global $isMember, $isAdmin, $isGuest, $member, $html, $nt, $DB;

    $_REQUEST['id'] = $request->id;
    $_REQUEST['no'] = $request->no;
    require_once NT_BOARD_PATH.DIRECTORY_SEPARATOR.'delete.php';
    $response->send();
});

$route->respond('GET', '/'.BOARD_DIR.'/[:id]/[edit|read|delete:action]/[i:no]/password', function($request, $response) {
    global $isMember, $isAdmin, $isGuest, $member, $html, $nt, $DB;

    $_REQUEST['id'] = $request->id;
    $_REQUEST['no'] = $request->no;
    $_REQUEST['action'] = $request->action;
    require_once NT_BOARD_PATH.DIRECTORY_SEPARATOR.'password.php';
    $response->send();
});

$route->respond('GET', '/'.BOARD_DIR.'/[:id]/[i:no]/comment', function($request, $response) {
    global $isMember, $isAdmin, $isGuest, $member, $html, $nt, $DB;

    $_REQUEST['id'] = $request->id;
    $_REQUEST['no'] = $request->no;
    require_once NT_BOARD_PATH.DIRECTORY_SEPARATOR.'comment.php';
    $response->send();
});

$route->respond('GET', '/'.BOARD_DIR.'/[:id]/[i:no]/comment/[i:cn]/[edit|reply|delete:action]', function($request, $response) {
    global $isMember, $isAdmin, $isGuest, $member, $html, $nt, $DB;

    $_REQUEST['id'] = $request->id;
    $_REQUEST['no'] = $request->no;
    $_REQUEST['cn'] = $request->cn;
    $_REQUEST['action'] = $request->action;
    require_once NT_BOARD_PATH.DIRECTORY_SEPARATOR.'commentAction.php';
    $response->send();
});

$route->respond('GET', '/[:id]', function($request, $response) {
    global $isMember, $isAdmin, $isGuest, $member, $html, $nt, $DB;

    $page = getPagesContent($request->id);

    if (isset($page['code']) && $page['code'])
        $response->code($page['code']);

    echo $page['content'];

    $response->send();
});

$route->onHttpError(function ($code, $router) {
    global $isMember, $isAdmin, $isGuest, $member, $html, $nt, $DB;

    switch ($code) {
        case 404:
            require_once NT_THEME_PATH.DIRECTORY_SEPARATOR.'404.php';
            break;
        default:
            break;
    }
});

$route->dispatch();