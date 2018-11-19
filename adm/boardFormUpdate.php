<?php
require_once './_common.php';

$token = new TOKEN();

// 토큰체크
if (!$token->verifyToken($_POST['token'], 'ss_adm_token'))
    dieJson(_('Please use it in the correct way.'));

$w = substr(trim($_POST['w']), 0, 1);

$flds = array(
    'bo_id',
    'bo_title',
    'bo_use',
    'bo_skin',
    'bo_category',
    'bo_subject_len',
    'bo_page_rows',
    'bo_page_limit',
    'bo_gallery_cols',
    'bo_thumb_width',
    'bo_thumb_height',
    'bo_list_level',
    'bo_view_level',
    'bo_write_level',
    'bo_comment_level',
    'bo_reply_level',
    'bo_file_limit',
    'bo_link_limit',
    'bo_captcha_use',
    'bo_captcha_comment'
);

foreach ($flds as $key) {
    switch ($key) {
        case 'bo_id':
            $$key = preg_replace(NT_BOARD_ID_PATTERN, '', $_POST[$key]);
            break;
        case 'bo_title':
        case 'bo_skin':
        case 'bo_category':
            $$key = trim(strip_tags($_POST[$key]));
            break;
        default:
            $$key = (int)preg_replace('#[^0-9]#', '', $_POST[$key]);
            break;
    }
}

if (!$bo_id)
    dieJson(_('Please enter the Board ID.'));

if (!$bo_title)
    dieJson(_('Please enter the Board Title.'));

if (!$bo_skin)
    dieJson(_('Please specify the board skin correctly.'));

if (!$bo_page_row)
    $bo_page_rows = __c('cf_page_rows');

if (!$bo_page_limit)
    $bo_page_limit = __c('cf_page_limit');

if ($w == '') {
    $sql = " select count(*) as cnt from `{$nt['board_config_table']}` where bo_id = :bo_id ";

    $DB->prepare($sql);
    $DB->bindValue(':bo_id', $bo_id);
    $DB->execute();

    $row = $DB->fetch();

    if ($row['cnt'])
        dieJson(sprintf(_('%s is duplicated Board ID.'), $bo_id));

    $column = array();
    $holder = array();
    $values = array();

    foreach ($flds as $key) {
        $column[] = $key;
        $holder[] = ':'.$key;
        $values[':'.$key] = $$key;
    }

    $sql = " insert into `{$nt['board_config_table']}` ( ".implode(', ', $column)." ) values ( ".implode(', ', $holder)." ) ";

    $DB->prepare($sql);
    $DB->bindValueArray($values);
    $result = $DB->execute();
} else if ($w == 'u') {
    $sql = " select bo_id from `{$nt['board_config_table']}` where bo_id = :bo_id ";

    $DB->prepare($sql);
    $DB->bindValue(':bo_id', $bo_id);
    $DB->execute();

    $row = $DB->fetch();

    if (!$row['bo_id'])
        dieJson(_('Board information does not exist.'));

    $holder = array();
    $values = array();

    foreach ($flds as $key) {
        $values[':'.$key] = $$key;

        if ($key == 'bo_id')
            continue;

        $holder[] = "{$key} = :{$key}";
    }

    $sql = " update `{$nt['board_config_table']}` set ".implode(', ', $holder)." where bo_id = :bo_id ";

    $DB->prepare($sql);
    $DB->bindValueArray($values);
    $result = $DB->execute();
}

if (!$result)
    dieJson(_('An error occurred while adding or editing the information. Please try again.'));
else
    dieJson('');