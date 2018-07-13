<?php
require_once './_common.php';

$html->setPageTitle(_d('Oops, the page does not exist.', THEME_LOCALE_DOMAIN));

$html->getPageHeader();
?>

<div class="page-404">
    <div class="col-6 mx-auto">
        <p class="text-center text-secondary mb-4">
            <i class="icon" data-feather="alert-triangle"></i>
        </p>
        <p class="h2 text-muted text-center mb-4">404</p>
        <p class="h4 text-dark mb-5 text-center"><?php echo _d("Oops, the page you're looking for does not exist.", THEME_LOCALE_DOMAIN); ?></p>

        <p class="text-center">
            <a href="<?php echo NT_URL; ?>" class="btn btn-outline-primary"><?php echo _d('Go to Homepage', THEME_LOCALE_DOMAIN); ?></a>
        </p>
    </div>
</div>

<?php
$html->getPageFooter();
?>