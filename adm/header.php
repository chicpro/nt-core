<?php
require_once __DIR__.DIRECTORY_SEPARATOR.'header.inc.php';
require_once __DIR__.DIRECTORY_SEPARATOR.'header.sub.php';
require_once __DIR__.DIRECTORY_SEPARATOR.'sidebarMenu.php';
?>

<nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0">
    <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="<?php echo NT_ADMIN_URL; ?>">NT-Core</a>
    <ul class="navbar-nav px-3">
        <li class="nav-item text-nowrap px-3">
            <a class="nav-link" href="<?php echo NT_URL; ?>"><?php echo _('Home'); ?></a>
        </li>
        <li class="nav-item text-nowrap">
            <a class="nav-link" href="<?php echo NT_LINK_LOGOUT; ?>"><?php echo _('Log Out'); ?></a>
        </li>
    </ul>
</nav>

<div class="container-fluid">
    <div class="row">
        <nav class="col-md-2 d-none d-md-block bg-light sidebar">
            <div class="sidebar-sticky">
                <ul class="nav flex-column">
                    <?php getSidebarMenu(); ?>
                </ul>
            </div>
        </nav>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4 mb-5">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                <h1 class="h2"><?php echo $html->title; ?></h1>
            </div>