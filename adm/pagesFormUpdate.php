<?php
require_once './_common.php';

$token = new TOKEN();
$tag   = new TAGS();

// 토큰체크
if (!$token->verifyToken($_REQUEST['token'], 'ss_adm_token'))
    dieJson(_('Please use it in the correct way.'));

$w  = substr(trim($_REQUEST['w']), 0, 1);
$no = (int)preg_replace('#[^0-9]#', '', $_REQUEST['no']);

if ($w == 'd') {
    $sql = " select pg_no, pg_content from `{$nt['pages_table']}` where pg_no = :pg_no ";
    $DB->prepare($sql);
    $DB->execute([':pg_no' => $no]);
    $row = $DB->fetch();

    if (!$row['pg_no'])
        dieJson(_('Page does not exist'));

    $imgs = getEditorImages($row['pg_content']);

    foreach ($imgs[1] as $src) {
        if (!preg_match('#^'.preg_quote(NT_DATA_URL).'.+$#', $src))
            continue;

        $file = str_replace(NT_DATA_URL, '', $src);

        $sql = " select count(*) as cnt from `{$nt['pages_table']}` where pg_content like :file and pg_no <> :pg_no ";
        $DB->prepare($sql);
        $DB->execute([':file' => '%'.$file.'%', ':pg_no' => $no]);
        $cnt = $DB->fetchColumn();

        if ($cnt > 0)
            continue;

        @unlink(NT_DATA_PATH.$file);
    }

    $sql = " delete from `{$nt['pages_table']}` where pg_no = :pg_no ";
    $DB->prepare($sql);
    $result = $DB->execute([':pg_no' => $no]);

    if (!$result)
        dieJson(_('An error occurred while editing the information. Please try again.'));

    dieJson('');
}

$column = '';
$save   = '';
$q      = '';

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

$pg_id      = trim(strip_tags($_REQUEST['pg_id']));
$pg_subject = trim(strip_tags($_REQUEST['pg_subject']));
$pg_content = trim($_REQUEST['pg_content']);
$pg_css     = trim($_REQUEST['pg_css']);
$pg_header  = trim($_REQUEST['pg_header']);
$pg_footer  = trim($_REQUEST['pg_footer']);
$pg_use     = (int)preg_replace('#[^0-9]#', '', $_REQUEST['pg_use']);

if (!$pg_id)
    dieJson(_('Please enter a Page ID'));

if (!$pg_subject)
    dieJson(_('Please enter a Subject'));

if (!$pg_content)
    dieJson(_('Please enter a Contents'));

$pg_id = preg_replace('#\s+#', ' ', $pg_id);
$pg_id = str_replace(' ', '-', $pg_id);

if ($pg_header) {
    if (!preg_match('#^header-?[a-z]*\.php$#', $pg_header))
        dieJson(_('Please enter the header file correctly'));

    if (!is_file(NT_THEME_PATH.DIRECTORY_SEPARATOR.$pg_header))
        dieJson(sprintf(_('File %s does not exist.'), $pg_header));
}

if ($pg_footer) {
    if (!preg_match('#^footer-?[a-z]*\.php$#', $pg_footer))
        dieJson(_('Please enter the footer file correctly'));

    if (!is_file(NT_THEME_PATH.DIRECTORY_SEPARATOR.$pg_footer))
        dieJson(sprintf(_('File %s does not exist.'), $pg_footer));
}

if ($w == '') {
    $sql = " select count(*) as cnt from `{$nt['pages_table']}` where pg_id = :pg_id ";
    $DB->prepare($sql);
    $DB->execute([':pg_id' => $pg_id]);
    $cnt = $DB->fetchColumn();

    if ($cnt > 0)
        dieJson(sprintf(_('%s is a duplicate page id'), $pg_id));

    $sql = " insert into `{$nt['pages_table']}` ( pg_id, pg_subject, pg_content, pg_css, pg_header, pg_footer, pg_use, pg_views, pg_date, pg_ip ) values ( :pg_id, :pg_subject, :pg_content, :pg_css, :pg_header, :pg_footer, :pg_use, :pg_views, :pg_date, :pg_ip ) ";

    $DB->prepare($sql);
    $DB->bindValueArray(
        [
            ':pg_id'      => $pg_id,
            ':pg_subject' => $pg_subject,
            ':pg_content' => $pg_content,
            ':pg_css'     => $pg_css,
            ':pg_header'  => $pg_header,
            ':pg_footer'  => $pg_footer,
            ':pg_use'     => $pg_use,
            ':pg_views'   => 0,
            ':pg_date'    => NT_TIME_YMDHIS,
            ':pg_ip'      => $_SERVER['REMOTE_ADDR']
        ]
    );

    $result = $DB->execute();

    if ($result)
        $no = $DB->lastInsertId();
} else if ($w == 'u') {
    $sql = " select pg_no, pg_content from `{$nt['pages_table']}` where pg_no = :pg_no ";
    $DB->prepare($sql);
    $DB->execute([':pg_no' => $no]);
    $pages = $DB->fetch();
    if (!$pages['pg_content'])
        dieJson(_('Page does not exist'));

    $sql = " select count(*) as cnt from `{$nt['pages_table']}` where pg_id = :pg_id and pg_no <> :pg_no ";
    $DB->prepare($sql);
    $DB->execute([':pg_id' => $pg_id, ':pg_no' => $no]);
    $cnt = $DB->fetchColumn();

    if ($cnt > 0)
        dieJson(sprintf(_('%s is a duplicate page id'), $pg_id));

    $sql = " update `{$nt['pages_table']}` set pg_id = :pg_id, pg_subject = :pg_subject, pg_content = :pg_content, pg_css = :pg_css, pg_header = :pg_header, pg_footer = :pg_footer, pg_use = :pg_use where pg_no = :pg_no ";

    $DB->prepare($sql);
    $DB->bindValueArray(
        [
            ':pg_id'      => $pg_id,
            ':pg_subject' => $pg_subject,
            ':pg_content' => $pg_content,
            ':pg_css'     => $pg_css,
            ':pg_header'  => $pg_header,
            ':pg_footer'  => $pg_footer,
            ':pg_use'     => $pg_use,
            ':pg_no'      => $no
        ]
    );

    $result = $DB->execute();
}

// Editor Image
$editorImages = getEditorImages($pg_content);

if (is_array($editorImages) && !empty($editorImages)) {
    foreach ($editorImages[1] as $img) {
        if (!$img)
            continue;

        if (!preg_match('#^'.preg_quote(NT_DATA_URL).'.+#i', $img))
            continue;

        if (strpos($img, DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR) === false)
            continue;

        $image = basename(str_replace(NT_URL, '', $img));

        $file = NT_DATA_PATH.DIRECTORY_SEPARATOR.EDITOR_FILE_DIR.DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR.$image;

        if(!is_file($file))
            continue;

        $ym = substr(str_replace('-', '', NT_TIME_YMD), 2, 4);
        $newFile = str_replace(DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR.$ym.DIRECTORY_SEPARATOR, $file);
        $newDir  = dirname($newFile);
        if (!is_dir($newDir))
            mkdir($newDir, 0755, true);

        rename($file, $newFile);

        $pg_content = str_replace(DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR.$image, DIRECTORY_SEPARATOR.$ym.DIRECTORY_SEPARATOR.$image, $pg_content);
    }

    $sql = " update `{$nt['pages_table']}` set pg_content = :pg_content where pg_no = :pg_no ";
    $DB->prepare($sql);
    $DB->execute([':pg_content' => $pg_content, ':pg_no' => $no]);
}

// 수정 후 변경된 이미지 처리
if ($w == 'u') {
    $editorImages1 = getEditorImages($pages['pg_content']);

    $sql = " select pg_content from `{$nt['pages_table']}` where pg_no = :pg_no ";
    $DB->prepare($sql);
    $DB->execute([':pg_no' => $no]);
    $content = $DB->fetchColumn();
    $editorImages2 = getEditorImages($content);

    $m1 = $editorImages1[1];
    $m2 = $editorImages2[1];

    $diff = array_diff($m1, $m2);

    if (!empty($m1)) {
        foreach ($diff as $src) {
            if (!preg_match('#^'.preg_quote(NT_DATA_URL).'.+$#', $src))
                continue;

            $file = str_replace(NT_DATA_URL, '', $src);

            $sql = " select count(*) as cnt from `{$nt['pages_table']}` where pg_content like :file and pg_no <> :pg_no ";
            $DB->prepare($sql);
            $DB->execute([':file' => '%'.$file.'%', ':pg_no' => $no]);
            $cnt = $DB->fetchColumn();

            if ($cnt > 0)
                continue;

            unlink(NT_DATA_PATH.DIRECTORY_SEPARATOR.$file);
        }
    }
}

// Tags
if (isset($_POST['tags']))
    $tag->insertTag($_POST['tags'], 'pages', $no);

if (!$result) {
    dieJson(_('An error occurred while editing the information. Please try again.'));
} else {
    $qstr = array_merge(array('w' => 'u', 'no' => $no, 'p' => $p), $qstr);
    $href = NT_ADMIN_URL.DIRECTORY_SEPARATOR.'pagesForm.php?'.http_build_query($qstr, '', '&');
    die(json_encode(['error' => '', 'href' => $href]));
}