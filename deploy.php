<?php
# Array of the authorized IP addresses who can POST here. You can override this in your config if you so choose.
$authorized_ips = array(
	'207.97.227.253',
	'50.57.128.197',
	'108.171.174.178',
	'50.57.231.61',
	'54.235.183.49',
	'54.235.183.23',
	'54.235.118.251',
	'54.235.120.57',
	'54.235.120.61',
	'54.235.120.62'
);

# Put your deploy config file in the same dir as this file
if ( is_file( dirname( __FILE__ ) . '/deploy-config.php' ) )
	require_once 'deploy-config.php';

# A regex matching the ref of the "push". `git pull` will only run if this matches. Default is the master branch.
if ( !defined( 'REF_REGEX' ) )
	define( 'REF_REGEX', '#^refs/heads/master$#' );

if ( !defined( 'LOG_WRITE' ) )
	define( 'LOG_WRITE', true );

# Log location; make sure it exists
if ( !defined( 'LOG' ) )
	define( 'LOG', '../logs/deploy.log' );

# Where is your repo directory? This script will chdir to it. If %s is present, it gets replaced with the repository name
if ( !defined( 'REPO_DIR' ) )
	define( 'REPO_DIR', dirname( __FILE__ ) . "/" );

# Where is your git binary, and what command would you like to run?
if ( !defined( 'GIT_COMMAND' ) )
	define( 'GIT_COMMAND', 'git pull' );

# Do we want to do IP verification?
if ( !defined( 'VERIFY_IP' ) )
	define( 'VERIFY_IP', true );

if (LOG_WRITE === true) {
    if ( is_writable( LOG ) && $handle = fopen( LOG, 'a' ) ) {
        # Sweet taste of victory
        if(LOG_WRITE)
            fwrite( $handle, date( 'Y-m-d H:i:s' ) . "\n==============================\n" );
        else
            @fclose( $handle );
    } else {
        @fclose( $handle );
        header( $_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500 );
        die( 'Please complete installation' );
    }
}

# Do some authentication
if ( defined( 'AUTH_KEY' ) && ( !isset( $_GET['auth'] ) || AUTH_KEY != $_GET['auth'] ) ) {
	$error = "Auth key doesn't match";
} elseif ( !isset( $_POST['payload'] ) ) {
	$error = '$_POST["payload"] is not set';
} elseif ( VERIFY_IP && !in_array( $_SERVER['REMOTE_ADDR'], $authorized_ips ) ) {
	$error = "{$_SERVER['REMOTE_ADDR']} is not authorized. Authorized IPs are: " . implode( ', ', $authorized_ips );
} else {
	$error = false;
}

if ( false !== $error ) {
    if(LOG_WRITE)
	    fwrite( $handle, "*** ALERT ***\nFailed attempt to access deployment script!\n\nMESSAGE: $error\n\n" . print_r( $_SERVER, 1 ) . print_r( $_REQUEST, 1 ) . "\n\n\n" );
	@fclose( $handle );
    header( $_SERVER['SERVER_PROTOCOL'] . ' 401 Unauthorized', true, 401 );
	die( "You don't have permission to access this page." );
}

# We're authorized, let's do this!
if (LOG_WRITE) {
    $content = '';
    if ( defined( 'DUMP_POSTDATA' ) && DUMP_POSTDATA === true )
        $content .= print_r( $_POST, 1 ) . "\n\n";

    if ( false === fwrite( $handle, $content ) ) {
        echo "Couldn't write to log!\n";
    }
}

$payload = json_decode( $_POST['payload'] );
if ( preg_match( REF_REGEX, $payload->ref ) ) {
	# If we have a commit to master, we can pull on it
    # If commit message has [deployment], pull
    $cnt = count($payload->commits);
    $is_deploy = false;

    for ($i=0; $i<$cnt; $i++) {
        if (preg_match(DEPLOYMENT_STRING, $payload->commits[$i]->message)) {
            $is_deploy = true;
            break;
        }
    }

    if ($is_deploy === true) {
        $userInfo = posix_getpwuid(fileowner(__FILE__));
        $owner = $userInfo['name'];
        $output = array( 'bash> ' . sprintf(GIT_COMMAND, $owner) );
        chdir( sprintf( REPO_DIR, $payload->repository->name ) );
        exec( sprintf(GIT_COMMAND, $owner) . ' 2>&1', $output );

        if (LOG_WRITE)
            fwrite( $handle, "`$payload->ref` matches, executing:\n" . sprintf(GIT_COMMAND, $owner) . "\n" . implode( "\n", $output ) . "\n" );
    }
} else {
    if (LOG_WRITE)
	    fwrite( $handle, "`$payload->ref` doesn't match the ref criteria\n" );
}

if (LOG_WRITE)
    fwrite( $handle, date( 'Y-m-d H:i:s' ) . " Over and out!\n\n\n" );

@fclose( $handle );