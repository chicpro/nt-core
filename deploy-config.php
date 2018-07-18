<?php
# A regex matching the ref of the "push". <code>git pull</code> will only run if this matches. Default is the master branch.
define( 'REF_REGEX', '#^refs/heads/master$#' );

# Write Log File
define('LOG_WRITE', false);

# Log location; make sure it exists
define( 'LOG', '../logs/deploy.log' );

# Where is your repo directory? This script will chdir to it. If %s is present, it gets replaced with the repository name
define( 'REPO_DIR', dirname( __FILE__ ) . "/" );

# If set to true, $_POST gets logged
define( 'DUMP_POSTDATA', false );

# In your webhook URL to github, you can append ?auth={{ this field }} as a very simple gut-check authentication
define( 'AUTH_KEY', '' );

# Where is your git binary, and what command would you like to run?
define( 'GIT_COMMAND', 'sudo -u %s /usr/bin/git pull' );

# Do we want to do IP verification?
define( 'VERIFY_IP', false );

# Deplayment String
define( 'DEPLOYMENT_STRING', '#\[deployment\]#i' );
