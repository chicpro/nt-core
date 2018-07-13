<?php
require_once './_common.php';

use SitemapPHP\Sitemap;

if (!is_dir(NT_SITEMAP_PATH))
    mkdir(NT_SITEMAP_PATH, 0755, true);

$url = setHttp(__c('cf_site_url'));

$sitemap = new Sitemap($url);

$sitemap->setPath(NT_SITEMAP_PATH.'/');

$priority = $sitemap::DEFAULT_PRIORITY;

$sitemap->addItem('/');

// Pages
$sql = " select pg_id, pg_date from `{$nt['pages_table']}` where pg_use = :pg_use ";
$DB->prepare($sql);
$DB->execute([':pg_use' => 1]);
$result = $DB->fetchAll();

for ($i = 0; $row = array_shift($result); $i++) {
    if (!$row['pg_id'])
        continue;

    $sitemap->addItem('/'.$row['pg_id'], $priority, null, $row['pg_date']);
}

// Board
$sql = " select bo_no, bo_id, bo_date from `{$nt['board_table']}` where bo_secret = :bo_secret order by bo_id ";
$DB->prepare($sql);
$DB->execute([':bo_secret' => 0]);
$result = $DB->fetchAll();

for ($i = 0; $row = array_shift($result); $i++) {
    $sitemap->addItem('/'.BOARD_DIR.'/'.$row['bo_id'].'/'.$row['bo_no'], $priority, null, $row['bo_date']);
}

$sitemap->createSitemapIndex($url.'/'.SITEMAP_DIR.'/', 'Today');

dieJson('');