<?php
/**
 * Session class
 */

class SESSION
{
    public function __construct()
    {
        if (defined('_SESSION_DISABLE_') && _SESSION_DISABLE_ === true)
            return;

        @ini_set("session.use_trans_sid", 0);
        @ini_set("url_rewriter.tags","");

        if (isset($SESSION_CACHE_LIMITER))
            @session_cache_limiter($SESSION_CACHE_LIMITER);
        else
            @session_cache_limiter("no-cache, must-revalidate");

        ini_set("session.cache_expire", 180);
        ini_set("session.gc_maxlifetime", 10800);
        ini_set("session.gc_probability", 1);
        ini_set("session.gc_divisor", 100);

        require_once(NT_CONFIG_PATH.DIRECTORY_SEPARATOR.'session.php');

        ini_set('session.cookie_domain',  NT_COOKIE_DOMAIN);
        session_set_cookie_params(0, '/', NT_COOKIE_DOMAIN);

        ini_set('session.save_handler', NT_SESSION_HANDLER);
        ini_set('session.save_path',    NT_SESSION_SAVE_PATH);

        session_start();
    }
}