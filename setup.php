<?php
define('_SETUP_', true);
$_GET['locale'] = 'ko';

require_once './_common.php';

if (!isset($nt['config_table']) || !isset($nt['member_table']) || !empty($config))
    die(_('Setup can not be executed.'));

$html->setPageTitle('NT-CORE Setup');
$html->addStyleSheet(NT_CSS_URL.DIRECTORY_SEPARATOR.'setup.css', 'header', 0);
$html->addStyleSheet('https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/css/bootstrap.min.css','header', 0, '',  'integrity="sha384-Smlep5jCw/wG7hdkwQ/Z5nLIefveQRIY9nfy6xoR1uRYBtpZgI6339F5dgvm/e9B" crossorigin="anonymous"');

$html->addJavaScript('https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js', 'header', 0);
$html->addJavaScript('https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js', 'footer', 0, '', 'integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"');
$html->addJavaScript('https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/js/bootstrap.min.js', 'footer', 0, '', 'integrity="sha384-o+RDsa0aLu++PJvFqy8fFScvbHFLtbvScb8AjopnFD+iEQ7wo/CG0xlczd+2O/em" crossorigin="anonymous"');
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