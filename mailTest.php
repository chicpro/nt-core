<?php
if (PHP_SAPI != 'cli')
    die('CLI only'.PHP_EOL);

if (!isset($argv[1]) || !isset($argv[2]))
    die('Usage : php '.basename(__FILE__).' "from e-amil" "to e-mail"'.PHP_EOL);

ini_set('display_errors', 1);
require_once './_common.php';

$name = 'Mail Test';
$from = $argv[1];
$to   = $argv[2];

$subject = '메일 수신 테스트입니다.';
$content = '메일 수신 테스트입니다.';

$mailer = new MAILER();

$mailer->setFrom($from);
$mailer->setFromName($name);
$mailer->setAddress($to);
$mailer->setSubject($subject);
$mailer->setContent($content);

$result = $mailer->send();

print_r($result);
echo PHP_EOL;