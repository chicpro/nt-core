<?php
/**
 * Path class
 */

class PATH
{
    public function __construct()
    {
        $path = $this->getBasePath();

        define('NT_PATH', $path['path']);
        define('NT_URL',  $path['url']);
        require_once(NT_PATH.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'path.php');
        unset($path);
    }

    private function getBasePath()
    {
        $result['path'] = str_replace('\\', '/', dirname(dirname(__FILE__)));
        $tilde_remove = preg_replace('/^\/\~[^\/]+(.*)$/', '$1', $_SERVER['SCRIPT_NAME']);
        $document_root = str_replace($tilde_remove, '', $_SERVER['SCRIPT_FILENAME']);
        $root = str_replace($document_root, '', $result['path']);
        $port = ($_SERVER['SERVER_PORT'] == 80 || $_SERVER['SERVER_PORT'] == 443) ? '' : ':'.$_SERVER['SERVER_PORT'];
        $http = 'http' . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on') ? 's' : '') . '://';
        $user = str_replace(str_replace($document_root, '', $_SERVER['SCRIPT_FILENAME']), '', $_SERVER['SCRIPT_NAME']);
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
        if (isset($_SERVER['HTTP_HOST']) && preg_match('/:[0-9]+$/', $host))
            $host = preg_replace('/:[0-9]+$/', '', $host);
        $host = preg_replace("/[\<\>\'\"\\\'\\\"\%\=\(\)\/\^\*]/", '', $host);
        $result['url'] = $http.$host.$port.$user.$root;

        return $result;
    }
}