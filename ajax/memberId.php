<?php
require_once './_common.php';

$id = trim($_POST['id']);

if (!$id)
    die(_('Please enter ID.'));

if (!preg_match(NT_MEMBER_ID_PATTERN, $id))
    die(_('Please enter the ID format to fit.'));

$sql = " select count(mb_uid) as cnt from `{$nt['member_table']}` where mb_id = :mb_id and mb_uid <> :mb_uid ";

$DB->prepare($sql);
$DB->bindValueArray([':mb_id' => $id, ':mb_uid' => (string)$member['mb_uid']]);
$DB->execute();

$row = $DB->fetch();

if ($row['cnt'])
    die(_('This ID is duplicated.'));

die('');