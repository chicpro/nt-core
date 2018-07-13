<?php
$_ADMIN_LINK = array(
    array('name' => _('Dashboard'), 'link' => NT_ADMIN_URL, 'icon' => 'home', 'key' => 'index'),
    array('name' => _('Member'),    'icon' =>'users',
            'link' => array(
                array('name' => _('Member'), 'link' => NT_ADMIN_URL.DIRECTORY_SEPARATOR.'member.php', 'icon' =>'users', 'key' => 'member')
            )),
    array('name' => _('Board'),     'link' => NT_ADMIN_URL.DIRECTORY_SEPARATOR.'board.php',    'icon' =>'list',      'key' => 'board'),
    array('name' => _('Pages'),     'link' => NT_ADMIN_URL.DIRECTORY_SEPARATOR.'pages.php',    'icon' =>'file-text', 'key' => 'pages'),
    array('name' => _('Visiting'),  'link' => NT_ADMIN_URL.DIRECTORY_SEPARATOR.'visiting.php', 'icon' =>'info',      'key' => 'visiting')
);

if ($isSuper) {
    $_ADMIN_LINK[] = array('name' => _('Sitemap'),       'link' => NT_ADMIN_URL.DIRECTORY_SEPARATOR.'sitemap.php', 'icon' => 'cast',     'key' => 'sitemap');
    $_ADMIN_LINK[] = array('name' => _('Configuration'), 'link' => NT_ADMIN_URL.DIRECTORY_SEPARATOR.'config.php',  'icon' => 'settings', 'key' => 'config');
}

function getSidebarMenu()
{
    global $_ADMIN_LINK;

    $menu = array();

    $current = $_SERVER['SCRIPT_NAME'];

    foreach ($_ADMIN_LINK as $link) {
        if (is_array($link['link'])) {
            $mns = array();
            $i = 0;

            foreach ($link['link'] as $lnk) {
                if ($i == 0) {
                    $menus = '<li class="nav-item">
                                    <a class="nav-link" href="'.$lnk['link'].'">
                                        <span data-feather="'.$link['icon'].'"></span>
                                        '.getHtmlChar($link['name']).'
                                    </a>';
                }

                if (preg_match('#'.preg_quote($lnk['key']).'.*\.php$#', $current))
                    $active = ' active';
                else
                    $active = '';

                $mns[] = '<li class="nav-item ml-3">
                                <a class="nav-link'.$active.'" href="'.$lnk['link'].'">
                                    <span data-feather="'.$lnk['icon'].'"></span>
                                    '.getHtmlChar($lnk['name']).'
                                </a>
                            </li>';

                $i++;
            }

            if ($i > 0) {
                if (!empty($mns))
                    $menus .= '<ul class="nav flex-column">'.implode(PHP_EOL, $mns).'</ul>';

                $menus .= PHP_EOL.'</li>';

                $menu[] = $menus;
            }
        } else {
            if (preg_match('#'.preg_quote($link['key']).'.*\.php$#', $current))
                $active = ' active';
            else
                $active = '';

            $menu[] = '<li class="nav-item">
                            <a class="nav-link'.$active.'" href="'.$link['link'].'">
                                <span data-feather="'.$link['icon'].'"></span>
                                '.getHtmlChar($link['name']).'
                            </a>
                        </li>';
        }
    }

    echo implode(PHP_EOL, $menu);
}