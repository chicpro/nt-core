<?php
require_once './_common.php';

$w  = substr(trim($_POST['w']), 0, 1);
$id = trim($_REQUEST['id']);
$no = (int)preg_replace('#[^0-9]#', '', $_REQUEST['no']);

if (!$id)
    die(json_encode(['id' => '']));

$id = preg_replace('#\s+#', ' ', $id);
$id = str_replace(' ', '-', $id);

$sql = " select count(*) as cnt from `{$nt['pages_table']}` where pg_id = :pg_id and pg_no <> :pg_no ";
$DB->prepare($sql);
$DB->execute([':pg_id' => $id, ':pg_no' => $no]);
$cnt = $DB->fetchColumn();

if ($cnt > ($w == 'u' ? 1 : 0)) {
    $sql = " select pg_id from `{$nt['pages_table']}` where pg_id like :pg_id ";
    $DB->prepare($sql);
    $DB->execute([':pg_id' => $id.'%']);
    $result = $DB->fetchAll();

    $pgID = array();

    for ($i = 0; $row = array_shift($result); $i++) {
        $pgID[] = (int)array_pop(explode('-', preg_replace('#[^0-9]#', '', $row['pg_id'])));
    }

    $id .= '-'.(max($pgID) + 1);
}

die(json_encode(['id' => $id]));