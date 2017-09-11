<?php
/**
 * Starts up a PHP server on localhost:8001.
 *
 * This webserver is used to test cURL requests issued by the RemoteRequest.php class.
 *
 * Based off @link http://tech.vg.no/2013/07/19/using-phps-built-in-web-server-in-your-test-suites/
 *
 * @package    bunq_assignment
 * @author     Dimitri
 */

// Define server parameters
define('WEB_SERVER_HOST', 'localhost');
define('WEB_SERVER_PORT', 8001);

//Windows command 'START /MIN php -S %s:%d -t %s > null 2>&1 '
//UNIX command 'php -S %s:%d -t %s >/dev/null 2>&1 & echo $!'

$command = sprintf(
    'php -S %s:%d -t %s >/dev/null 2>&1 & echo $!',
    WEB_SERVER_HOST,
    WEB_SERVER_PORT,
    realpath(__DIR__ . '/../resources/')
);

// Execute the command and store the process ID
$output = array();
exec($command, $output);
$pid = (int) $output[0];

echo sprintf(
        '%s - Web server started on %s:%d with PID %d',
        date('r'),
        WEB_SERVER_HOST,
        WEB_SERVER_PORT,
        $pid
    ) . PHP_EOL;

// Kill the web server when the process ends
register_shutdown_function(function() use ($pid) {
    echo sprintf('%s - Killing process with ID %d', date('r'), $pid) . PHP_EOL;
    exec('kill ' . $pid);
});