<?php

/**
 * Root directory of Tiki installation
 */
global $tiki_root_dir;
$tiki_root_dir = dirname(dirname(dirname(__FILE__))) . '/';

/**
 * Ask DB Abstraction Team for all possible values
 */
//$api_tiki = 'pear';
$api_tiki = 'adodb';

/**
 * Database connectivity related parameters
 */
$db_tiki        = 'mysql';
$dbversion_tiki = '1.8';
$host_tiki      = 'localhost';
$user_tiki      = 'root';
$pass_tiki      = '';
$dbs_tiki       = 'tiki';
$tikidomain     = '';

/**
 * Execute local config file (may override all defined here vars)
 *
 * CVS Developers: Do not change any of the above.
 * Instead, create a file, called local.php, containing any of
 * the variables listed above that are different for your
 * development environment.  This will protect you from
 * accidentally committing your username/password to CVS!
 */
$tiki_local_conf = dirname(__FILE__) . '/local.php';
if (file_exists($tiki_local_conf))
    require_once($tiki_local_conf);

?>
