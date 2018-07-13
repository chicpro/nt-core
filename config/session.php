<?php
define('NT_COOKIE_DOMAIN', '');

//define('NT_SESSION_HANDLER',   'redis');
//define('NT_SESSION_SAVE_PATH', 'tcp://127.0.0.1:6379');

define('NT_SESSION_HANDLER',   'files');
define('NT_SESSION_SAVE_PATH', NT_SESSION_PATH);