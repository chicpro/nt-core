<?php
/**
 * COMMON Class
 */

class HTML
{
    protected $hs;
    protected $fs;
    protected $hc;
    protected $fc;
    protected $hss;
    protected $fss;
    protected $hcss;
    protected $fcss;

    public $title;
    public $ogTag;
    public $metaTag;

    public function __construct()
    {
        $this->hs = array();
        $this->fs = array();
        $this->hc = array();
        $this->fc = array();

        $this->hss  = array();
        $this->fss  = array();
        $this->hcss = array();
        $this->fcss = array();

        $this->title   = '';
        $this->ogTag   = array();
        $this->metaTag = array();
    }

    public function getFavicon()
    {
        $favicon = NT_FILE_PATH.DIRECTORY_SEPARATOR.'favicon.ico';

        if (!is_file($favicon))
            return '';

        return '<link href="'.str_replace(NT_FILE_PATH, NT_FILE_URL, $favicon).'" rel="shortcut icon">'.PHP_EOL;
    }

    public function setPageTitle(string $title)
    {
        $this->title = getHtmlChar($title);
    }

    public function addMetaTag(string $name, string $content)
    {
        if ($name) {
            $name    = strip_tags($name);
            $content = strip_tags(removeScriptString($content));

            switch ($name) {
                case 'description':
                    $this->metaTag[$name] = preg_replace('#\s+#', ' ', str_replace(array("\r\n", "\r", "\n", '&nbsp;'), ' ', $content));
                    break;
                default:
                    $this->metaTag[$name] = $content;
                    break;
            }
        }
    }

    public function addOGTag(string $property, string $content)
    {
        if ($property) {
            $property = strip_tags($property);
            $content  = strip_tags(removeScriptString($content));

            switch ($property) {
                case 'description':
                    $this->ogTag[$property] = preg_replace('#\s+#', ' ', str_replace(array("\r\n", "\r", "\n", '&nbsp;'), ' ', $content));
                    break;
                default:
                    $this->ogTag[$property] = $content;
                    break;
            }
        }
    }

    public function addStyleSheet(string $style, string $location, int $order = 0, string $ver = '', string $extra = '', string $prepend = '', string $append = '')
    {
        if(trim($style))
            $this->mergeStyleSheet($style, $location, $order, $ver, $extra, $prepend, $append);
    }

    public function addJavaScript(string $script, string $location, int $order = 0, string $ver = '', string $extra = '', string $prepend = '', string $append = '')
    {
        if(trim($script))
            $this->mergeJavaScript($script, $location, $order, $ver, $extra, $prepend, $append);
    }

    public function addStyleString(string $style, string $location, int $order = 0)
    {
        if(trim($style))
            $this->mergeStyleString($style, $location, $order);
    }

    public function addScriptString(string $script, string $location, int $order = 0)
    {
        if(trim($script))
            $this->mergeScriptString($script, $location, $order);
    }

    protected function mergeStyleSheet(string $style, string $location, int $order, string $ver, string $extra, string $prepend, string $append)
    {
        switch ($location) {
            case 'footer':
                $links = $this->fc;
                break;
            default:
                $links = $this->hc;
                break;
        }

        $isMerge = true;

        foreach($links as $link) {
            if($link[1] == $style) {
                $isMerge = false;
                break;
            }
        }

        if($isMerge) {
            switch ($location) {
                case 'footer':
                    $this->fc[] = array($order, $style, $ver, $extra, $prepend, $append);
                    break;
                default:
                    $this->hc[] = array($order, $style, $ver, $extra, $prepend, $append);
                    break;
            }
        }
    }

    protected function mergeJavaScript(string $js, string $location, int $order, string $ver, string $extra, string $prepend, string $append)
    {
        switch ($location) {
            case 'footer':
                $scripts = $this->fs;
                break;
            default:
                $scripts = $this->hs;
                break;
        }

        $isMerge = true;

        foreach($scripts as $script) {
            if($script[1] == $js) {
                $isMerge = false;
                break;
            }
        }

        if($isMerge) {
            switch ($location) {
                case 'footer':
                    $this->fs[] = array($order, $js, $ver, $extra, $prepend, $append);
                    break;
                default:
                    $this->hs[] = array($order, $js, $ver, $extra, $prepend, $append);
                    break;
            }
        }
    }

    protected function mergeStyleString(string $css, string $location, int $order)
    {
        switch ($location) {
            case 'footer':
                $styles = $this->fcss;
                break;
            default:
                $styles = $this->hcss;
                break;
        }

        $isMerge = true;

        foreach($styles as $style) {
            if($style[1] == $css) {
                $isMerge = false;
                break;
            }
        }

        if($isMerge) {
            switch ($location) {
                case 'footer':
                    $this->fcss[] = array($order, $css);
                    break;
                default:
                    $this->hcss[] = array($order, $css);
                    break;
            }
        }
    }

    protected function mergeScriptString(string $js, string $location, int $order)
    {
        switch ($location) {
            case 'footer':
                $scripts = $this->fss;
                break;
            default:
                $scripts = $this->hss;
                break;
        }

        $isMerge = true;

        foreach($scripts as $script) {
            if($script[1] == $js) {
                $isMerge = false;
                break;
            }
        }

        if($isMerge) {
            switch ($location) {
                case 'footer':
                    $this->fss[] = array($order, $js);
                    break;
                default:
                    $this->hss[] = array($order, $js);
                    break;
            }
        }
    }

    public function getPageStyle(string $location)
    {
        switch (strtolower($location)) {
            case 'footer':
                $links = $this->fc;
                break;
            default:
                $links = $this->hc;;
                break;
        }

        $stylesheet = array();

        if(!empty($links)) {
            foreach ($links as $key => $row) {
                $order[$key] = $row[0];
                $index[$key] = $key;
                $style[$key] = $row[1];
            }

            array_multisort($order, SORT_ASC, $index, SORT_ASC, $links);

            foreach($links as $link) {
                if(!trim($link[1]))
                    continue;

                if ($link[2])
                    $link[1] = preg_replace('#\.css$#i', '.css?ver='.$link[2].'$1', $link[1]);

                $s = array();

                $s[] = $link[4];
                $s[] = '<link rel="stylesheet" href="'.$link[1].'"'.($link[3] ? ' '.$link[3] : '').'>';
                $s[] = $link[5];

                $stylesheet[]= implode('', $s);
            }
        }

        return (empty($stylesheet) ? '' : implode(PHP_EOL, $stylesheet).PHP_EOL);
    }

    public function getPageScript(string $location)
    {
        switch (strtolower($location)) {
            case 'footer':
                $scripts = $this->fs;
                break;
            default:
                $scripts = $this->hs;
                break;
        }

        $javascript = array();

        if(!empty($scripts)) {
            foreach ($scripts as $key => $row) {
                $order[$key] = $row[0];
                $index[$key] = $key;
                $script[$key] = $row[1];
            }

            array_multisort($order, SORT_ASC, $index, SORT_ASC, $scripts);

            foreach($scripts as $js) {
                if(!trim($js[1]))
                    continue;

                if ($js[2])
                    $js[1] = preg_replace('#\.js$#i', '.js?ver='.$js[2].'$1', $js[1]);

                $s = array();

                $s[] = $js[4];
                $s[] = '<script src="'.$js[1].'"'.($js[3] ? ' '.$js[3] : '').'></script>';
                $s[] = $js[5];

                $javascript[] = implode('', $s);
            }
        }

        return (empty($javascript) ? '' : implode(PHP_EOL, $javascript).PHP_EOL);
    }

    public function getStyleString(string $location)
    {
        switch (strtolower($location)) {
            case 'footer':
                $styles = $this->fcss;
                break;
            default:
                $styles = $this->hcss;
                break;
        }

        $css = array();

        if(!empty($styles)) {
            foreach ($styles as $key => $row) {
                $order[$key] = $row[0];
                $index[$key] = $key;
                $script[$key] = $row[1];
            }

            array_multisort($order, SORT_ASC, $index, SORT_ASC, $styles);

            foreach($styles as $s) {
                if(!trim($s[1]))
                    continue;

                $css[] = $s[1];
            }
        }

        return (empty($css) ? '' : implode(PHP_EOL, $css).PHP_EOL);
    }

    public function getScriptString(string $location)
    {
        switch (strtolower($location)) {
            case 'footer':
                $scripts = $this->fss;
                break;
            default:
                $scripts = $this->hss;
                break;
        }

        $javascript = array();

        if(!empty($scripts)) {
            foreach ($scripts as $key => $row) {
                $order[$key] = $row[0];
                $index[$key] = $key;
                $script[$key] = $row[1];
            }

            array_multisort($order, SORT_ASC, $index, SORT_ASC, $scripts);

            foreach($scripts as $js) {
                if(!trim($js[1]))
                    continue;

                $javascript[] = $js[1];
            }
        }

        return (empty($javascript) ? '' : implode(PHP_EOL, $javascript).PHP_EOL);
    }

    public function getMetaTag()
    {
        $metaTags = array();

        if (!array_key_exists('title', $this->metaTag))
            $this->addMetaTag('title', $this->title);

        if (!array_key_exists('description', $this->metaTag))
            $this->addMetaTag('description', (string)__c('cf_description'));

        if (!array_key_exists('keywords', $this->metaTag))
            $this->addMetaTag('keywords', (string)__c('cf_keywords'));

        if (!empty($this->metaTag)) {
            foreach (array('title', 'description', 'keywords') as $key) {
                if (array_key_exists($key, $this->metaTag) &&$this->metaTag[$key]) {
                    $metaTags[] = '<meta name="'.$key.'" content="'.htmlentities($this->metaTag[$key], ENT_QUOTES).'"/>';
                    unset($this->metaTag[$key]);
                }
            }

            foreach ($this->metaTag as $name => $content) {
                if (!$content)
                    continue;

                switch ($name) {
                    case 'canonical':
                        $metaTags[] = '<link rel="'.$name.'" href="'.$content.'"/>';
                        break;
                    default:
                        $metaTags[] = '<meta name="'.$name.'" content="'.htmlentities($content, ENT_QUOTES).'"/>';
                        break;
                }
            }
        }

        return (empty($metaTags) ? '' : implode(PHP_EOL, $metaTags).PHP_EOL);
    }

    public function getOGTag()
    {
        $metaTags = array();

        if (!array_key_exists('type', $this->ogTag))
            $this->addOGTag('type', 'website');

        if (!array_key_exists('title', $this->ogTag))
            $this->addOGTag('title', $this->title);

        if (!array_key_exists('description', $this->ogTag))
            $this->addOGTag('description', (string)__c('cf_description'));

        if (!array_key_exists('url', $this->ogTag))
            $this->addOGTag('url', NT_URL.$_SERVER['REQUEST_URI']);

        if (!array_key_exists('site_name', $this->ogTag))
            $this->addOGTag('site_name', __c('cf_site_name'));

        if (defined('NT_LOCALE'))
            $this->addOGTag('locale', NT_LOCALE);

        if (!empty($this->ogTag)) {
            foreach (array('type', 'title', 'description', 'url', 'site_name', 'image', 'locale') as $key) {
                if (array_key_exists($key, $this->ogTag) && $this->ogTag[$key]) {
                    $metaTags[] = '<meta property="og:'.$key.'" content="'.htmlentities($this->ogTag[$key], ENT_QUOTES).'"/>';
                    unset($this->ogTag[$key]);
                }
            }

            foreach ($this->ogTag as $property => $content) {
                if (!$content)
                    continue;

                $metaTags[] = '<meta property="og:'.$property.'" content="'.htmlentities($content, ENT_QUOTES).'"/>';
            }
        }

        return (empty($metaTags) ? '' : implode(PHP_EOL, $metaTags).PHP_EOL);
    }

    public function getPageHeader(string $name = null, bool $once = true)
    {
        if($name)
            $file = "header-{$name}.php";
        else
            $file = 'header.php';

        $this->loadPage($file, $once);
    }

    public function getPageFooter(string $name = null, bool $once = true)
    {
        if($name)
            $file = "footer-{$name}.php";
        else
            $file = 'footer.php';

        $this->loadPage($file, $once);
    }

    public function loadPage(string $file, bool $once = true)
    {
        global $html, $config, $member, $isGuest, $isMember, $isAdmin, $isSuper, $nt, $DB;

        $page = NT_THEME_PATH.DIRECTORY_SEPARATOR.$file;

        if (is_file($page)) {
            if ($once)
                require_once($page);
            else
                require($page);
        }

    }
}