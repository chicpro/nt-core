<?php
require_once './_common.php';
require_once NT_BOARD_PATH.DIRECTORY_SEPARATOR.'board.inc.php';

$tag = new TAGS();

$qstr['p'] = $p;

// 토큰체크
$token = new TOKEN();
if (!$token->verifyToken($_POST['token']))
    alert(_('Please use it in the correct way.'));

// reCAPTCHA 체크
if (__c('cf_recaptcha_site_key') && $board['bo_captcha_use']) {
    $captcha = new reCAPTCHA();
    if (!$captcha->checkResponse((string)$_POST['g-recaptcha-response']))
        alert(_('Please check the anti-spam code.'));
}

$w  = substr($_REQUEST['w'], 0, 1);
$no = (int)preg_replace('#[^0-9]#', '', $_REQUEST['no']);

$bo_notice = ($_REQUEST['bo_notice'] == 1 ? 1 : 0);
$bo_secret = ($_REQUEST['bo_secret'] == 1 ? 1 : 0);

$bo_subject = trim($_REQUEST['bo_subject']);
$bo_content = trim($_REQUEST['bo_content']);

if (strlen($bo_subject) < 1)
    alert(_('Please enter a Subject.'));

if (strlen($bo_content) < 1)
    alert(_('Please enter a Contents.'));

if (isset($_REQUEST['bo_link']) && is_array($_REQUEST['bo_link'])) {
    $_REQUEST['bo_link'] = array_map('strip_tags', $_REQUEST['bo_link']);
    $_REQUEST['bo_link'] = array_map('trim', $_REQUEST['bo_link']);
}

if ($w == 'r' || $w == 'u') {
    $wr = getPost($no);

    if (!$wr['bo_no'])
        alert(_('The post does not exist.'));
}

if ($w == '' || $w == 'u') {
    if (!$isAdmin && $bo_notice)
        alert(_('You do not have permission to write a notice.'));

    if ($w == 'u' && !$isAdmin && $wr['bo_notice'])
        $bo_notice = 1;

    if ($w == 'u') {
        if (!$isAdmin && ($isGuest || $write['mb_uid'] != $member['mb_uid']) && $member['mb_level'] < $board['bo_write_level'])
            alert(_('You do not have permission to write a post.'));
    } else {
        if (!$isAdmin && $member['mb_level'] < $board['bo_write_level'])
            alert(_('You do not have permission to write a post.'));
    }
} else if ($w == 'r') {
    if ($wr['bo_notice'])
        alert(_('You can not reply to notice posts.'));

    if (!$isAdmin && $member['mb_level'] < $board['bo_reply_level'])
        alert(_('You do not have permission to reply.'));
}

$category = array();
if ($board['bo_category']) {
    $categories = explode(',', $board['bo_category']);
    $categories = array_map('trim', $categories);

    if (!empty($categories)) {
        foreach ($categories as $val) {
            if ($val)
                $category[] = $val;
        }
    }
}

$bo_category = '';
if (isset($_REQUEST['bo_category']) && $_REQUEST['bo_category'])
    $bo_category = strip_tags($_REQUEST['bo_category']);

if (!empty($category) && !in_array($bo_category, $category))
    alert(_('Please select the category correctly.'));

if ($w == '' || $w == 'r') {
    if ($member['mb_uid']) {
        $mb_uid      = $member['mb_uid'];
        $bo_name     = $member['mb_name'];
        $bo_password = $member['mb_password'];
    } else {
        $mb_uid  = '';
        $bo_name = trim(strip_tags($_REQUEST['bo_name']));
        if (!$bo_name)
            alert(_('Please enter a Name.'));

        if (!isset($_REQUEST['bo_password']) || !$_REQUEST['bo_password'])
            alert(_('Please enter a Password.'));

        $bo_password = passwordCreate($_REQUEST['bo_password']);
    }

    if ($w == 'r') {
        $sql = " select max(bo_reply) as max_reply from `{$nt['board_table']}` where bo_parent = :bo_parent ";

        $DB->prepare($sql);
        $DB->bindValue(':bo_parent', $wr['bo_parent']);
        $DB->execute();

        $row = $DB->fetch();

        $bo_reply    = $row['max_reply'] + 1;
        $bo_parent   = $wr['bo_parent'];
        $bo_password = $wr['bo_password'];
        $bo_secret   = $wr['bo_secret'];
    } else {
        $bo_reply  = 0;
        $bo_parent = 0;
    }

    $bo_view = 0;

    $sql = " insert into `{$nt['board_table']}` ( bo_id, mb_uid, bo_name, bo_password, bo_subject, bo_content, bo_category, bo_notice, bo_secret, bo_reply, bo_parent, bo_view, bo_date, bo_ip ) values ( :bo_id, :mb_uid, :bo_name, :bo_password, :bo_subject, :bo_content, :bo_category, :bo_notice, :bo_secret, :bo_reply, :bo_parent, :bo_view, :bo_date, :bo_ip ) ";

    $DB->prepare($sql);
    $DB->bindValueArray(
        [
            ':bo_id'        => $id,
            ':mb_uid'       => $mb_uid,
            ':bo_name'      => $bo_name,
            ':bo_password'  => $bo_password,
            ':bo_subject'   => $bo_subject,
            ':bo_content'   => $bo_content,
            ':bo_category'  => $bo_category,
            ':bo_notice'    => $bo_notice,
            ':bo_secret'    => $bo_secret,
            ':bo_reply'     => $bo_reply,
            ':bo_parent'    => $bo_parent,
            ':bo_view'     => $bo_view,
            ':bo_date'      => NT_TIME_YMDHIS,
            ':bo_ip'        => $_SERVER['REMOTE_ADDR']
        ]
    );

    if (!$DB->execute())
        alert(_($DB->error));

    $no = $DB->lastInsertId();

    if ($w == '') {
        $sql = " update `{$nt['board_table']}` set bo_parent = :bo_parent where bo_no = :bo_no ";
        $DB->prepare($sql);
        $DB->execute([':bo_parent' => $no, ':bo_no' => $no]);
    }

    if ($bo_secret && $isGuest)
        $_SESSION['ss_password_'.$id.'_'.$no] = true;
} else if ($w =='u') {
    if ($wr['mb_uid'])
        $mb = getMember($wr['mb_uid'], 'mb_level');

    if ($member['mb_level'] < $mb['mb_level'])
        alert(_('You can not edit posts made by members of a higher level than yourself.'));

    if (!$isAdmin) {
        if ($member['mb_uid']) {
            if ($member['mb_uid'] != $wr['mb_uid'])
                alert(_('You can not edit it because it is not your own post.'));
        } else {
            if ($wr['mb_uid'])
                alert(_('Please log in and edit your post.'));
        }
    }

    $sql = " update `{$nt['board_table']}` set bo_subject = :bo_subject, bo_content = :bo_content, bo_category = :bo_category, bo_notice = :bo_notice, bo_secret = :bo_secret where bo_no = :bo_no ";

    $DB->prepare($sql);
    $DB->bindValueArray(
        [
            ':bo_subject'   => $bo_subject,
            ':bo_content'   => $bo_content,
            ':bo_category'  => $bo_category,
            ':bo_notice'    => $bo_notice,
            ':bo_secret'    => $bo_secret,
            ':bo_no'        => $no
        ]
    );

    if (!$DB->execute())
        alert(_('There was an error processing your request. Please try again.'));
} else {
    alert(_('Please use it in the correct way.'));
}

// Link
if (!empty($_REQUEST['bo_link']) && is_array($_REQUEST['bo_link'])) {
    if ($w == 'u') {
        $sql = " delete from `{$nt['board_link_table']}` where bo_no = :bo_no ";
        $DB->prepare($sql);
        $DB->bindValue(':bo_no', $wr['bo_no']);
        $DB->execute();
    }

    $ln_no = 0;
    foreach ($_REQUEST['bo_link'] as $val) {
        if ($val) {
            $sql = " insert into `{$nt['board_link_table']}` ( bo_id, bo_no, ln_no, ln_url ) value ( :bo_id, :bo_no, :ln_no, :ln_url ) ";
            $DB->prepare($sql);
            $DB->bindValueArray([':bo_id' => $id, ':bo_no' => $no, ':ln_no' => $ln_no, ':ln_url' => $val]);
            $DB->execute();

            $ln_no++;
        }
    }

    if ($ln_no > 0) {
        $sql = " select count(*) as cnt from `{$nt['board_link_table']}` where bo_no = :bo_no ";
        $DB->prepare($sql);
        $DB->bindValue(':bo_no', $no);
        $DB->execute();
        $row = $DB->fetch();

        $sql = " update `{$nt['board_table']}` set bo_link = :bo_link where bo_no = :bo_no ";
        $DB->prepare($sql);
        $DB->bindValueArray([':bo_link' => (int)$row['cnt'], ':bo_no' => $no]);
        $DB->execute();
    }
}

// File
$uploadCount = count($_FILES['bo_file']['name']);
$maxFilesize = ini_get('upload_max_filesize');

$fileDir = NT_FILE_PATH.DIRECTORY_SEPARATOR.$id;
if (!is_dir($fileDir))
    mkdir($fileDir, 0755, true);

$uploadMsg = '';
$upload    = array();
$blockExt  = array('php', 'pht', 'html', 'htm', 'exe', 'jsp', 'asp', 'inc', 'sh');

for ($i = 0; $i < $uploadCount; $i++) {
    $upload[$i]['file'] = '';
    $upload[$i]['name'] = '';
    $upload[$i]['type'] = '';

    if (isset($_REQUEST['bo_file_del'][$i]) && $_REQUEST['bo_file_del'][$i]) {
        $upload[$i]['delete'] = true;

        $sql = " select fl_file from `{$nt['board_file_table']}` where bo_no = :bo_no and fl_no = :fl_no ";
        $DB->prepare($sql);
        $DB->bindValueArray(['bo_no' => $no, ':fl_no' => $i]);
        $DB->execute();
        $row = $DB->fetch();

        @unlink(NT_FILE_PATH.DIRECTORY_SEPARATOR.$row['fl_file']);
    } else {
        $upload[$i]['delete'] = false;
    }

    $tempFile = $_FILES['bo_file']['tmp_name'][$i];
    $fileName = $_FILES['bo_file']['name'][$i];

    if ($fileName) {
        if ($_FILES['bo_file']['error'][$i] == 1) {
            $uploadMsg .= sprinf(_('"%s" can not be uploaded because it is larger than the value set on the server.'), $filename).'\\n';
            continue;
        } else if ($_FILES['bo_file']['error'][$i] != 0) {
            $uploadMsg .= sprintf(_('"%s" was not successfully uploaded.'), $filename).'\\n';
            continue;
        }
    }

    if (is_uploaded_file($tempFile)) {
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (in_array($ext, $blockExt))
            $ext .= '-'.strtolower(randomChar(3, false));

        $destFile = $fileDir.DIRECTORY_SEPARATOR.getUploadedFileName().'.'.$ext;

        if (!move_uploaded_file($tempFile, $destFile))
            continue;

        @chmod($destFile, 0644);

        $sql = " select fl_file from `{$nt['board_file_table']}` where bo_no = :bo_no and fl_no = :fl_no ";
        $DB->prepare($sql);
        $DB->bindValueArray([':bo_no' => $no, ':fl_no' => $i]);
        $DB->execute();

        $row = $DB->fetch();

        if ($row['fl_file'])
            @unlink(NT_FILE_PATH.DIRECTORY_SEPARATOR.$row['fl_file']);

        $upload[$i]['name'] = $fileName;
        $upload[$i]['file'] = str_replace(NT_FILE_PATH.DIRECTORY_SEPARATOR, '', $destFile);
        $upload[$i]['type'] = mime_content_type($destFile);
    }
}

for ($i = 0; $i < count($upload); $i++) {
    $sql = " select count(*) as cnt from `{$nt['board_file_table']}` where bo_no = :bo_no and fl_no = :fl_no ";
    $DB->prepare($sql);
    $DB->bindValueArray([':bo_no' => $no, ':fl_no' => $i]);
    $DB->execute();
    $row = $DB->fetch();

    if ($row['cnt']) {
        if ($upload[$i]['delete'] || $upload[$i]['file']) {
            $sql = " update `{$nt['board_file_table']}` set fl_file = :fl_file, fl_name = :fl_name, fl_type = :fl_type, fl_down = :fl_down where bo_no = :bo_no and fl_no = :fl_no ";

            $DB->prepare($sql);
            $DB->bindValueArray(
                [
                    ':fl_file' => $upload[$i]['file'],
                    ':fl_name' => $upload[$i]['name'],
                    ':fl_type' => $upload[$i]['type'],
                    ':fl_down' => 0,
                    ':bo_no'   => $no,
                    ':fl_no'   => $i
                ]
            );
            $DB->execute();
        }
    } else {
        $sql = " insert into `{$nt['board_file_table']}` ( fl_file, fl_name, fl_type, bo_id, bo_no, fl_no, fl_down ) values ( :fl_file, :fl_name, :fl_type, :bo_id, :bo_no, :fl_no, :fl_down ) ";

        $DB->prepare($sql);
        $DB->bindValueArray(
            [
                ':fl_file' => $upload[$i]['file'],
                ':fl_name' => $upload[$i]['name'],
                ':fl_type' => $upload[$i]['type'],
                ':bo_id'   => $id,
                ':bo_no'   => $no,
                ':fl_no'   => $i,
                ':fl_down' => 0
            ]
        );
        $DB->execute();
    }
}

$sql = " select max(fl_no) as max_no from `{$nt['board_file_table']}` where bo_no = :bo_no ";

$DB->prepare($sql);
$DB->bindValue(':bo_no', $no);
$DB->execute();
$row = $DB->fetch();

for ($i = (int)$row['max_no']; $i >= 0; $i--) {
    $sql = " select fl_file from `{$nt['board_file_table']}` where bo_no = :bo_no and fl_no = :fl_no ";
    $DB->prepare($sql);
    $DB->bindValueArray([':bo_no' => $no, ':fl_no' => $i]);
    $DB->execute();
    $row2 = $DB->fetch();

    if ($row2['fl_file'])
        break;

    $sql = " delete from `{$nt['board_file_table']}` where bo_no = :bo_no and fl_no = :fl_no ";
    $DB->prepare($sql);
    $DB->bindValueArray([':bo_no' => $no, ':fl_no' => $i]);
    $DB->execute();
}

if (!empty($upload)) {
    $sql = " select count(*) as cnt from `{$nt['board_file_table']}` where bo_no = :bo_no ";
    $DB->prepare($sql);
    $DB->bindValue(':bo_no', $no);
    $DB->execute();
    $row = $DB->fetch();

    $sql = " update `{$nt['board_table']}` set bo_file = :bo_file where bo_no = :bo_no ";
    $DB->prepare($sql);
    $DB->bindValueArray([':bo_file' => (int)$row['cnt'], ':bo_no' => $no]);
    $DB->execute();
}

// Editor Image
$editorImages = getEditorImages($bo_content);

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

        $bo_content = str_replace(DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR.$image, DIRECTORY_SEPARATOR.$ym.DIRECTORY_SEPARATOR.$image, $bo_content);
    }

    $sql = " update `{$nt['board_table']}` set bo_content = :bo_content where bo_no = :bo_no ";
    $DB->prepare($sql);
    $DB->execute([':bo_content' => $bo_content, ':bo_no' => $no]);
}

// 수정 후 변경된 이미지 처리
if ($w == 'u') {
    $editorImages1 = getEditorImages($wr['bo_content']);

    $sql = " select bo_content from `{$nt['board_table']}` where bo_no = :bo_no ";
    $DB->prepare($sql);
    $DB->execute([':bo_no' => $no]);
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

            $sql = " select count(*) as cnt from `{$nt['board_table']}` where bo_content like :file and bo_no <> :bo_no ";
            $DB->prepare($sql);
            $DB->execute([':file' => '%'.$file.'%', ':bo_no' => $no]);
            $cnt = $DB->fetchColumn();

            if ($cnt > 0)
                continue;

            @unlink(NT_DATA_PATH.$file);
        }
    }
}

// Tags
if (isset($_POST['tags']))
    $tag->insertTag($_POST['tags'], 'board', $no);

$qstr = http_build_query($qstr, '', '&amp;');
$href = NT_URL.'/'.BOARD_DIR.'/'.$id.'/'.$no.($qstr ? '?'.$qstr : '');

if ($uploadMsg)
    alert($uploadMsg, $href);
else
    gotoUrl($href);