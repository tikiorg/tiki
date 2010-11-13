<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],basename(__FILE__)) !== false) {
  header('location: index.php');
  exit;
}

require_once('lib/init/initlib.php');

// Define lang and load translation functions
if (!empty($_REQUEST['lang'])) {
	$language = $prefs['site_language'] = $prefs['language'] = $_REQUEST['lang'];
} else {
	$language = $prefs['site_language'] = $prefs['language'] = 'en';
}
include_once('lib/init/tra.php');

// Please use the local.php file instead containing these variables
// If you set sessions to store in the database, you will need a local.php file
// Otherwise you will be ok.
//$api_tiki		= 'pear';
//$api_tiki			= 'pdo';
$api_tiki			= 'pdo';
$db_tiki			= 'mysql';
$dbversion_tiki = '2.0';
$host_tiki		= 'localhost';
$user_tiki		= 'root';
$pass_tiki		= '';
$dbs_tiki			= 'tiki';
$tikidomain		= '';

/*
SVN Developers: Do not change any of the above.
Instead, create a file, called db/local.php, containing any of
the variables listed above that are different for your 
development environment.  This will protect you from 
accidentally committing your username/password to SVN!

example of db/local.php
<?php
$host_tiki   = 'myhost';
$user_tiki   = 'myuser';
$pass_tiki   = 'mypass';
$dbs_tiki    = 'mytiki';
$api_tiki    = 'adodb';

** Multi-tiki
**************************************
see http://tikiwiki.org/MultiTiki19

Setup of virtual tikis is done using setup.sh script
-----------------------------------------------------------
-> Multi-tiki trick for virtualhosting

$tikidomain variable is set to :
or TIKI_VIRTUAL
    That is set in apache virtual conf : SetEnv TIKI_VIRTUAL myvirtual
or SERVER_NAME
    From apache directive ServerName set for that virtualhost block
or HTTP_HOST
    From the real domain name called in the browser 
    (can be ServerAlias from apache conf)

*/

if (!isset($local_php) or !is_file($local_php)) {
	$local_php = 'db/local.php';
} else {
	$local_php = preg_replace(array('/\.\./','/^db\//'),array('',''),$local_php);
}
$tikidomain = '';
if (is_file('db/virtuals.inc')) {
	if (isset($_SERVER['TIKI_VIRTUAL']) and is_file('db/'.$_SERVER['TIKI_VIRTUAL'].'/local.php')) {
		$tikidomain = $_SERVER['TIKI_VIRTUAL'];
	} elseif (isset($_SERVER['SERVER_NAME']) and is_file('db/'.$_SERVER['SERVER_NAME'].'/local.php')) {
		$tikidomain = $_SERVER['SERVER_NAME'];
	} else if (isset($_REQUEST['multi']) && is_file('db/'.$_REQUEST['multi'].'/local.php')) {
		$tikidomain = $_REQUEST['multi'];
	} elseif (isset($_SERVER['HTTP_HOST'])) {
		if (is_file('db/'.$_SERVER['HTTP_HOST'].'/local.php')) {
			$tikidomain = $_SERVER['HTTP_HOST'];
		} else if (is_file('db/'.preg_replace('/^www\./','',$_SERVER['HTTP_HOST']).'/local.php')) {
			$tikidomain = preg_replace('/^www\./','',$_SERVER['HTTP_HOST']);
		}
	}
	if (!empty($tikidomain)) {
		$local_php = "db/$tikidomain/local.php";
	}
}
$tikidomainslash = (!empty($tikidomain) ? $tikidomain . '/' : '');

$re = false;
$default_api_tiki = $api_tiki;
$api_tiki = '';
if ( file_exists($local_php) ) $re = include($local_php);
if ( empty( $api_tiki ) ) {
	$api_tiki_forced = false;
	$api_tiki = $default_api_tiki;
} else {
	$api_tiki_forced = true;
}

if ( $re === false ) {
	if ( ! isset($in_installer) || $in_installer != 1) {
		header('location: tiki-install.php');
		exit;
	} else {
		// we are in the installer don't redirect...
		return ;
	}
}

if ( $dbversion_tiki == '1.10' ) $dbversion_tiki = '2.0';

require_once 'lib/core/TikiDb/ErrorHandler.php';
class TikiDb_LegacyErrorHandler implements TikiDb_ErrorHandler
{
	function handle( TikiDb $db, $query, $values, $result ) // {{{
	{
		global $smarty, $prefs, $ajaxlib;

		$msg = $db->getErrorMessage();
		$q=$query;
		foreach($values as $v) {
			if (is_null($v)) $v='NULL';
			else $v="'".addslashes($v)."'";
			$pos=strpos($q, '?');
			if ($pos !== FALSE)
				$q=substr($q, 0, $pos)."$v".substr($q, $pos+1);
		}

		if (function_exists('xdebug_get_function_stack')) {
			function mydumpstack($stack) {
				$o='';
				foreach($stack as $line) {
					$o.='* '.$line['file']." : ".$line['line']." -> ".$line['function']."(".var_export($line['params'], true).")<br />";
				}
				return $o;
			}
			$stacktrace = mydumpstack(xdebug_get_function_stack());
		} else {
			$stacktrace = false;
		}


		if ( ! isset($_SESSION['fatal_error']) ) {
			// Do not show the error if an error has already occured during the same script execution (error.tpl already called), because tiki should have died before another error.
			// This happens when error.tpl is called by tiki.sql... and tiki.sql is also called again in error.tpl, entering in an infinite loop.

			require_once 'installer/installlib.php';
			$installer = new Installer;

			require_once('tiki-setup.php');
			if ( ! $smarty ) {
				require_once 'lib/init/smarty.php';
			}

			$smarty->assign( 'msg', $msg );
			$smarty->assign( 'base_query', $query );
			$smarty->assign( 'values', $values );
			$smarty->assign( 'built_query', $q );
			$smarty->assign( 'stacktrace', $stacktrace );
			$smarty->assign( 'requires_update', $installer->requiresUpdate() );

			header("Cache-Control: no-cache, pre-check=0, post-check=0");

			if ($prefs['ajax_xajax'] === 'y') {
				global $ajaxlib;
				include_once('lib/ajax/xajax/xajax_core/xajaxAIO.inc.php');
				if ($ajaxlib && $ajaxlib->canProcessRequest()) {
					// this was a xajax request -> return a xajax answer
					$page = $smarty->fetch( 'database-connection-error.tpl' );
					$objResponse = new xajaxResponse();
					$page=addslashes(str_replace(array("\n", "\r"), array(' ', ' '), $page));
					$objResponse->script("bugwin=window.open('', 'tikierror', 'width=760,height=500,scrollbars=1,resizable=1');".
							"bugwin.document.write('$page');");
					echo $objResponse->getOutput();
					$this->log($msg.' - '.$q);
					die();
				}
			}

			$smarty->display('database-connection-error.tpl');
			unset($_SESSION['fatal_error']);
			$this->log($msg.' - '.$q);
			die;
		}
	} // }}}
	function log($msg) {
		global $user, $tikilib;
		$query = 'insert into `tiki_actionlog` (`objectType`,`action`,`object`,`user`,`ip`,`lastModif`, `comment`) values (?,?,?,?,?,?,?)';
		$result = $tikilib->query($query, array('system', 'db error', 'system', $user, $tikilib->get_ip_address(),  $tikilib->now, $msg));
	} // }}}
}

$dbInitializer = 'db/tiki-db-adodb.php';
if ($api_tiki == 'pdo' && extension_loaded("pdo") && in_array('mysql', PDO::getAvailableDrivers())) {
	$dbInitializer = 'db/tiki-db-pdo.php';
}

require $dbInitializer;
init_connection( TikiDb::get() );

if( isset( $shadow_host, $shadow_user, $shadow_pass, $shadow_dbs ) ) {
	global $dbMaster, $dbSlave;
	// Set-up the replication
	$dbMaster = TikiDb::get();

	$host_tiki = $shadow_host;
	$user_tiki = $shadow_user;
	$pass_tiki = $shadow_pass;
	$dbs_tiki = $shadow_dbs;
	require $dbInitializer;
	$dbSlave = TikiDb::get();
	init_connection( $dbSlave );

	require_once 'lib/core/TikiDb/MasterSlaveDispatch.php';
	$db = new TikiDb_MasterSlaveDispatch( $dbMaster, $dbSlave );
	TikiDb::set( $db );
}

unset( $host_map, $db_tiki, $host_tiki, $user_tiki, $pass_tiki, $dbs_tiki, $shadow_user, $shadow_pass, $shadow_host, $shadow_dbs );

function init_connection( $db ) {
	global $db_table_prefix, $common_users_table_prefix, $db_tiki;

	$db->setServerType( $db_tiki );
	$db->setErrorHandler( new TikiDb_LegacyErrorHandler );

	if( isset( $db_table_prefix ) )
		$db->setTablePrefix( $db_table_prefix );

	if( isset( $common_users_table_prefix ) )
		$db->setUsersTablePrefix( $common_users_table_prefix );
}

