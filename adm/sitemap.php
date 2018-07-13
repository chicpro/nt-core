<?php
require_once './_common.php';

$html->setPageTitle(_('Sitemap'));

require_once NT_ADMIN_PATH.DIRECTORY_SEPARATOR.'header.php';

$created = date('Y-m-d H:i:s', filemtime(NT_SITEMAP_PATH.DIRECTORY_SEPARATOR.'sitemap-index.xml'));
?>

<div class="col-8">
    <div class="col-8 mx-auto sitemap-create">
        <div class="col-8 mx-auto">
            <button type="button" id="create-sitemap" class="btn btn-primary btn-lg d-block col mx-auto"><?php echo _('Create Sitemap'); ?></button>
            <div class="text-center text-muted mt-3"><?php echo _('Recently created'); ?> : <?php echo $created; ?></div>
        </div>
    </div>
</div>

<?php
require_once NT_ADMIN_PATH.DIRECTORY_SEPARATOR.'footer.php';
?>