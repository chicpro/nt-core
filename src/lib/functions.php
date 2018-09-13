<?php
function getSearchColumn(string $column)
{
    return preg_replace('#[^a-z0-9_\-\.]#i', '', trim($column));
}

function getSearchString(string $string)
{
    $pattern = '/\+|-|&|\||!|\(|\)|\{|\}|\[|\]|\^|"|~|\*|\?|\:|\\\/';

    return preg_replace($pattern, '', trim($string));
}

function getPaging(int $totalItems, int $itemsPerPage, int $maxPagesToShow, int $currentPage, string $urlPattern, string $qstr = '')
{
    if ($qstr) {
        $qstr = preg_replace('#^\?*#', '', trim($qstr));
        $qstr = str_replace('&', '&amp;', preg_replace('#^&#', '', preg_replace('#&?page=[0-9]*#', '', str_replace('&amp;', '&', $qstr))));
    }

    $p = parse_url($urlPattern);
    $q = '';

    if ($qstr) {
        if (isset($p['query']) && $p['query'])
            $q = '&amp;';
        else
            $q = '?';
    }

    $urlPattern .= $q . $qstr;

    $detect = new Mobile_Detect;

    if ($detect->isMobile() && !$detect->isTablet() && $maxPagesToShow > 3)
        $maxPagesToShow = 3;

    return new PAGING($totalItems, $itemsPerPage, $maxPagesToShow, $currentPage, $urlPattern);
}

function getHtmlChar($str)
{
    $search  = ['&amp;'];
    $replace = ['&'];

    $str = str_replace($search, $replace, $str);

    return htmlentities($str, ENT_QUOTES);
}

function gotoUrl(string $url)
{
    $url = str_replace("&amp;", "&", $url);

    if (!headers_sent())
        header('Location: '.$url);
    else {
        echo '<script>';
        echo 'location.replace("'.$url.'");';
        echo '</script>';
    }
    exit;
}

function alert(string $msg = '', string $url = '')
{
    $url = preg_replace("/[\<\>\'\"\\\'\\\"\(\)]/", "", $url);

    echo '<script>'.PHP_EOL;
    echo 'alert("'.strip_tags($msg).'");'.PHP_EOL;
    if ($url)
        echo 'document.location.replace("'.str_replace('&amp;', '&', $url).'");'.PHP_EOL;
    else
        echo 'history.back();'.PHP_EOL;
    echo '</script>';
    exit;
}

function alertClose(string $msg='')
{
    echo '<script>'.PHP_EOL;
    echo 'alert("'.strip_tags($msg).'");'.PHP_EOL;
    echo 'window.close();'.PHP_EOL;
    echo '</script>';
    exit;
}

function dieJson(string $str, string $key = 'error')
{
    die(json_encode(array($key=>$str)));
}

function dieJsonp(string $str, string $key = 'error', string $callback = 'callback')
{
    die($callback.'('.json_encode(array($key=>$str)).')');
}

function randomChar(string $length, bool $special = true)
{
    $str = 'abcdefghijklmnopqrstuvwxyz0123456789!@#$%&*ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    if (!$special)
        $str = preg_replace('#[^a-z]#i', '', $str);

    $max = strlen($str) - 1;
    $chr = '';
    $len = abs($length);

    for ($i=0; $i<$len; $i++) {
        $chr .= $str[random_int(0, $max)];
    }

    return $chr;
}

function arrayMapDeep($fn, $array)
{
    if (is_array($array)) {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = arrayMapDeep($fn, $value);
            } else {
                $array[$key] = call_user_func($fn, $value);
            }
        }
    } else {
        $array = call_user_func($fn, $array);
    }

    return $array;
}

function isNullTime(string $time)
{
    return preg_replace('#[0:\-\s]#', '', $time) == '';
}

function getSelected($val1, $val2)
{
    return ($val1 == $val2 ? ' selected="selected"': '');
}

function getChecked($val1, $val2)
{
    return ($val1 == $val2 ? ' checked="checked"': '');
}

function getRichTime(string $time)
{
    if (substr($time, 0, 10) == NT_TIME_YMD)
        return substr($time, 11, 5);
    else if (substr($time, 0, 4) == substr(NT_TIME_YMD, 0, 4))
        return substr($time, 5, 5);
    else
        return substr($time, 2, 8);
}

function getSubstr(string $str, string $len, string $suffix = '…')
{
    $arr_str = preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
    $str_len = count($arr_str);

    if ($str_len >= $len) {
        $slice_str = array_slice($arr_str, 0, $len);
        $str = join('', $slice_str);

        return $str . ($str_len > $len ? $suffix : '');
    } else {
        $str = join('', $arr_str);
        return $str;
    }
}

function getCharCount(string $str)
{
    if (!$str)
        return 0;

    $arr_str = preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);

    return count($arr_str);


}

function getYN($val)
{
    return $val ? _('Y') : _('N');
}

function setHttp(string $url)
{
    if (!trim($url))
        return;

    if (!preg_match('#^https?\://#i', $url))
        $url = "http://" . $url;

    return $url;
}

function getMaskedString(string $str, int $len1, int $len2 = 0, int $limit = 0, string $mark='*')
{
    $arrStr = preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
    $strLen = count($arrStr);

    $len1 = abs($len1);
    $len2 = abs($len2);

    if($strLen <= ($len1 + $len2))
        return $str;

    $strHead = '';
    $strBody = '';
    $strFoot = '';

    $strHead = join('', array_slice($arrStr, 0, $len1));
    if($len2 > 0)
        $strFoot = join('', array_slice($arrStr, $len2 * -1));

    $arrBody = array_slice($arrStr, $len1, ($strLen - $len1 - $len2));

    if(!empty($arrBody)) {
        $lenBody = count($arrBody);
        $limit   = abs($limit);

        if($limit > 0 && $lenBody > $limit)
            $lenBody = $limit;

        $strBody = str_pad('', $lenBody, $mark);
    }

    return $strHead.$strBody.$strFoot;
}

function getShortenedString(string $str, int $len1, int $len2 = 0, string $replace='……')
{
    $arrStr = preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
    $strLen = count($arrStr);

    $len1 = abs($len1);
    $len2 = abs($len2);

    if($strLen <= ($len1 + $len2))
        return $str;

    $strHead = '';
    $strBody = '';
    $strFoot = '';

    $strHead = join('', array_slice($arrStr, 0, $len1));
    if($len2 > 0)
        $strFoot = join('', array_slice($arrStr, $len2 * -1));

    $arrBody = array_slice($arrStr, $len1, ($strLen - $len1 - $len2));

    if(!empty($arrBody))
        $strBody = $replace;

    return $strHead.$strBody.$strFoot;
}

function getThemeDir()
{
    $dirs = array();

    $dirname = NT_PATH.DIRECTORY_SEPARATOR.THEME_DIR;
    $handle = opendir($dirname);
    while ($file = readdir($handle)) {
        if ($file == '.'||$file == '..')
            continue;

        if (is_dir($dirname.DIRECTORY_SEPARATOR.$file))
            $dirs[] = $file;
    }
    closedir($handle);
    natsort($dirs);

    return $dirs;
}

function getMemberUID()
{
    $nt = $GLOBALS['nt'];
    $DB = $GLOBALS['DB'];

    while (1) {
        $uid = date('YmdHis', time()) . str_pad((int)(microtime()*100), 2, "0", STR_PAD_LEFT);

        $sql = " select count(mb_uid) as cnt from `{$nt['member_table']}` where mb_uid = :mb_uid ";

        $DB->prepare($sql);
        $DB->bindValue(':mb_uid', $uid);
        $DB->execute();

        $row = $DB->fetch();

        if (!$row['cnt'])
            break;

        usleep(10000);
    }

    return $uid;
}

function getMember(int $uid, string $field = '*')
{
    $nt = $GLOBALS['nt'];
    $DB = $GLOBALS['DB'];

    if (!$DB->pdo)
        return null;

    $sql = " select $field from `{$nt['member_table']}` where mb_uid = :mb_uid ";

    $DB->prepare($sql);
    $DB->bindValue(':mb_uid', $uid);
    $DB->execute();

    return $DB->fetch();
}

function baseConvert( $num, $base = null, $index = null )
{
    if ($num <= 0)
        return '0';

    if (!$index)
        $index = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    if (!$base)
        $base = strlen($index);
    else
        $index = substr($index, 0, $base);

    $res = '';

    while ( $num > 0 ) {
        $char = bcmod( $num, $base );
        $res .= substr( $index, $char, 1 );
        $num = bcsub( $num, $char );
        $num = bcdiv( $num, $base );
    }

    return $res;
}

function getConfig()
{
    $nt = $GLOBALS['nt'];
    $DB = $GLOBALS['DB'];

    if (!$DB->pdo)
        return null;

    $sql = " select * from `{$nt['config_table']}` ";
    $row = array();

    $DB->prepare($sql);
    if ($DB->execute())
        $row = $DB->fetch();

    return $row;
}

function __c(string $key)
{
    return $GLOBALS['config'][$key];
}

function getBoardConfig(string $bo_id)
{
    $nt = $GLOBALS['nt'];
    $DB = $GLOBALS['DB'];

    if (!$DB->pdo)
        return null;

    $sql = " select * from `{$nt['board_config_table']}` where bo_id = :bo_id ";
    $row = array();

    $DB->prepare($sql);
    $DB->bindValue(':bo_id', $bo_id);
    if ($DB->execute())
        $row = $DB->fetch();

    return $row;
}

function getPost(int $no, string $column = '*')
{
    $nt = $GLOBALS['nt'];
    $DB = $GLOBALS['DB'];

    $sql = " select {$column} from `{$nt['board_table']}` where bo_no = :bo_no ";

    $DB->prepare($sql);
    $DB->bindValue('bo_no', $no);
    $DB->execute();

    return $DB->fetch();
}

function getComment(int $no, string $column = '*')
{
    $nt = $GLOBALS['nt'];
    $DB = $GLOBALS['DB'];

    $sql = " select {$column} from `{$nt['board_comment_table']}` where cm_no = :cm_no ";

    $DB->prepare($sql);
    $DB->bindValue('cm_no', $no);
    $DB->execute();

    return $DB->fetch();
}

function getContent(string $content)
{
    $domains = array();

    $file = NT_CONFIG_PATH.'/safeiframe.txt';
    if (is_file($file)) {
        $f = file($file);

        foreach($f as $domain){
            if (!preg_match("/^#/", $domain)) {
                $domain = trim($domain);
                if ($domain)
                    $domains[] = $domain;
            }
        }
    }

    $domains[] = $_SERVER['HTTP_HOST'].'/';

    $safeiframe = implode('|', $domains);

    $config = HTMLPurifier_Config::createDefault();

    $config->set('Cache.SerializerPath', NT_CACHE_PATH);
    $config->set('HTML.SafeEmbed', false);
    $config->set('HTML.SafeObject', false);
    $config->set('Output.FlashCompat', false);
    $config->set('HTML.SafeIframe', true);
    $config->set('HTML.Nofollow', true);
    $config->set('URI.SafeIframeRegexp','%^(https?:)?//('.$safeiframe.')%');
    $config->set('Attr.AllowedFrameTargets', array('_blank'));

    $purifier = new HTMLPurifier($config);

    return $purifier->purify($content);
}

function getUploadedFileName()
{
    return baseConvert((int)preg_replace('#[^0-9]#', '', microtime(true)) * random_int(1, 1000));
}

function getEditorImages(string $content)
{
    if(!$content)
        return false;

    $pattern = "#<img[^>]*src=[\'\"]?([^>\'\"]+[^>\'\"]+)[\'\"]?[^>]*>#i";

    preg_match_all($pattern, $content, $matchs);

    return $matchs;
}

function getPagesContent($id, bool $viewCount = true)
{
    global $html, $nt, $DB;

    ob_start();

    $sql = " select * from `{$nt['pages_table']}` where pg_id = :pg_id or pg_no = :pg_no order by pg_no limit 0, 1 ";
    $DB->prepare($sql);
    $DB->execute([':pg_id' => $id, ':pg_no' => $id]);
    $pages = $DB->fetch();

    if (!$pages['pg_no'] || !$pages['pg_use']) {
        require_once NT_THEME_PATH.DIRECTORY_SEPARATOR.'404.php';
        $content = ob_get_contents();
        ob_end_clean();

        return array('content' => $content, 'code' => 404);
    }

    $tag = new TAGS();
    $tags = $tag->getTags('pages', $pages['pg_no']);

    $html->setPageTitle(getHtmlChar($pages['pg_subject']));

    $header = $pages['pg_header'];
    $footer = $pages['pg_footer'];

    $canonical = NT_URL.'/'.$pages['pg_id'];
    $html->addMetaTag('canonical', $canonical);
    $html->addOGTag('url', $canonical);

    $html->addMetaTag('description', $pages['pg_content']);
    $html->addOGTag('description', $pages['pg_content']);

    $html->addMetaTag('keywords', $tags);

    $ogImage = '';
    $contentImages = getEditorImages($pages['pg_content']);
    if (!empty($contentImages[1]))
        $ogImage = array_shift($contentImages[1]);

    if ($ogImage)
        $html->addOGTag('image', $ogImage);

    if (trim($pages['pg_css'])) {
        $html->addStyleString('<style type="text/css">
        '.$pages['pg_css'].'
        </style>', 'header', 10);
    }

    if (is_file(NT_THEME_PATH.DIRECTORY_SEPARATOR.$header))
        $html->loadPage($header);
    else
        $html->loadPage('header.sub.php');

    echo PHP_EOL.$pages['pg_content'].PHP_EOL;

    if (is_file(NT_THEME_PATH.DIRECTORY_SEPARATOR.$footer))
        $html->loadPage($footer);
    else
        $html->loadPage('footer.sub.php');

    $content = ob_get_contents();
    ob_end_clean();

    if ($viewCount && !$_SESSION['ss_page_'.$pages['pg_no'].'_view']) {
        $sql = " update `{$nt['pages_table']}` set pg_views = pg_views + 1 where pg_no = :pg_no ";
        $DB->prepare($sql);

        $_SESSION['ss_page_'.$pages['pg_no'].'_view'] = $DB->execute([':pg_no' => $pages['pg_no']]);
    }

    return array('content' => $content, 'code' => '');
}

function removeScriptString(string $string)
{
    return preg_replace('#<script\\b[^>]*>(.*?)<\\/script>#is', '', $string);
}

// gettext ngettext function
function _n(string $msgid1 , string $msgid2 , int $n)
{
    return ngettext($msgid1, $msgid2, $n);
}

// gettext dgettext function
function _d(string $message, string $domain)
{
    return dgettext($domain, $message);
}

// gettext dngettext function
function _dn(string $msgid1 , string $msgid2 , int $n, string $domain )
{
    return dngettext($domain, $msgid1, $msgid2, $n);
}