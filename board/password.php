<?php
require_once './_common.php';
require_once NT_BOARD_PATH.DIRECTORY_SEPARATOR.'board.inc.php';

$no = (int)preg_replace('#[^0-9]#', '', $_REQUEST['no']);

$write = getPost($no);

if (!$write['bo_no'])
    alert(_('No posts found.'));

$html->setPageTitle(_('Check post password'));

$html->getPageHeader();

unset($_SESSION['ss_password_'.$id.'_'.$no]);

switch ($_REQUEST['action']) {
    case 'edit':
    case 'read':
    case 'delete':
        $action = $_REQUEST['action'];
        break;
    default:
        alert(_('Please use it in the correct way.'));
        break;
}
?>

<div class="col-sm-4 mx-auto post-password">
    <form name="fpassword" id="fpassword" class="form-token" method="post" action="<?php echo NT_BOARD_URL; ?>/passwordCheck.php">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <input type="hidden" name="no" value="<?php echo $no; ?>">
        <input type="hidden" name="p" value="<?php echo $p; ?>">
        <input type="hidden" name="c" value="<?php echo getHtmlChar($c); ?>">
        <input type="hidden" name="s" value="<?php echo getHtmlChar($s); ?>">
        <input type="hidden" name="q" value="<?php echo getHtmlChar($q); ?>">
        <input type="hidden" name="action" value="<?php echo $action; ?>">

        <h1 class="h3 mb-3 font-weight-normal"><?php echo $html->title; ?></h1>

        <label for="password" class="sr-only"><?php echo _('Password'); ?></label>
        <input type="password" name="pass" id="password" class="form-control" placeholder="<?php echo _('Password'); ?>" required>

        <div class="mt-3">
            <button class="btn btn-lg btn-primary btn-block" type="submit"><?php echo _('Submit'); ?></button>
        </div>

    </form>
</div>

<?php
$html->getPageFooter();
?>