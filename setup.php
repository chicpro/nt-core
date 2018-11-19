<?php
define('_SETUP_', true);
$_GET['locale'] = 'ko';

require_once './_common.php';

if (!isset($nt['config_table']) || !isset($nt['member_table']) || !empty($config))
    die(_('Setup can not be executed.'));

$html->setPageTitle('NT-CORE Setup');
$html->addStyleSheet(NT_CSS_URL.DIRECTORY_SEPARATOR.'setup.css', 'header', 0);
require_once NT_CONFIG_PATH.DIRECTORY_SEPARATOR.'bootstrap.php';
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title><?php echo $html->title; ?></title>
<?php
echo $html->getPageStyle('header');
echo $html->getStyleString('header');
echo $html->getPageScript('header');
echo $html->getScriptString('header');
?>
</head>
<body>

<div class="container">
    <form class="col-md-4 mx-auto" method="post" action="./setupRun.php">
        <div class="form-group row">
            <div class="col">
                <h1 class="h3 mb-3 font-weight-normal"><?php echo $html->title; ?></h1>
            </div>
        </div>

        <?php if (!is_dir(NT_DATA_PATH) || !is_writable(NT_DATA_PATH)) { ?>
        <div class="form-group row">
            <div class="alert alert-danger" role="alert">
                <?php echo sprintf(_('Do not have permission to write to the %s folder.'), basename(NT_DATA_PATH)); ?>
            </div>
        </div>
        <?php } else { ?>
        <div class=" form-group row">
            <label for="mb_id" class="col-md-3 col-form-label"><?php echo _('ID'); ?></label>
            <div class="col">
                <input type="text" name="mb_id" id="mb_id" class="form-control" required autofocus>
                <small class="text-muted pt-1"><?php echo _('Only alphabetic, numeric, and _ can be used'); ?></small>
            </div>
        </div>
        <div class="form-group row">
            <label for="mb_name" class="col-md-3 col-form-label"><?php echo _('Name'); ?></label>
            <div class="col">
                <input type="text" name="mb_name" id="mb_name" class="form-control" required>
            </div>
        </div>
        <div class="form-group row">
            <label for="mb_email" class="col-md-3 col-form-label"><?php echo _('Email'); ?></label>
            <div class="col">
                <input type="email" name="mb_email" id="mb_email" class="form-control" required>
            </div>
        </div>
        <div class="form-group row">
            <label for="mb_password" class="col-md-3 col-form-label"><?php echo _('Password'); ?></label>
            <div class="col">
                <input type="text" name="mb_password" id="mb_password" class="form-control" required>
            </div>
        </div>
        <div class="form-group row">
            <div class="col">
                <button class="btn btn-lg btn-primary btn-block" type="submit"><?php echo _('Setup'); ?></button>
            </div>
        </div>
        <?php } ?>
    </form>
</div>

<?php
echo $html->getPageStyle('footer');
echo $html->getStyleString('footer');
echo $html->getPageScript('footer');
echo $html->getScriptString('footer');
?>
</body>
</html>