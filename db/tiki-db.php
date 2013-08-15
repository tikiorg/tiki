<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
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

$local_php = TikiInit::getCredentialsFile();
$re = false;
if ( file_exists($local_php) ) {
	$re = include($local_php);
}

if (! isset($client_charset)) {
	$client_charset = 'utf8';
}

$credentials = array(
	'api_tiki' => empty($api_tiki) ? $default_api_tiki : $api_tiki,
	'api_tiki_forced' => ! empty($api_tiki),
	'primary' => false,
	'shadow' => false,
);

// Load connection strings from environment variables, as used by Azure and possibly other hosts
$connectionString = null;
foreach (array('MYSQLCONNSTR_Tiki', 'MYSQLCONNSTR_DefaultConnection') as $envVar) {
	if (isset($_SERVER[$envVar])) {
		$connectionString = $_SERVER[$envVar];
		continue;
	}
}

if ($connectionString && preg_match('/^Database=(?P<dbs>.+);Data Source=(?P<host>.+);User Id=(?P<user>.+);Password=(?P<pass>.+)$/', $connectionString, $parts)) {
	$parts['charset'] = $client_charset;
	$parts['socket'] = null;

	$credentials['primary'] = $parts;
	$re = true;
} else {
	if (isset($shadow_host, $shadow_user, $shadow_pass, $shadow_dbs)) {
		$credentials['shadow'] = array(
			'host' => $shadow_host,
			'user' => $shadow_user,
			'pass' => $shadow_pass,
			'dbs' => $shadow_dbs,
			'charset' => $client_charset,
			'socket' => isset($socket_tiki) ? $socket_tiki : null,
		);
	}

	if (isset($host_tiki, $user_tiki, $pass_tiki, $dbs_tiki)) {
		$credentials['primary'] = array(
			'host' => $host_tiki,
			'user' => $user_tiki,
			'pass' => $pass_tiki,
			'dbs' => $dbs_tiki,
			'charset' => $client_charset,
			'socket' => null,
		);
	}
}

unset($host_map, $db_tiki, $host_tiki, $user_tiki, $pass_tiki, $dbs_tiki, $shadow_user, $shadow_pass, $shadow_host, $shadow_dbs);

global $systemConfiguration;
$systemConfiguration = new Zend_Config(
	array(
		'preference' => array(
			'feature_jison_wiki_parser' => 'n',		// hard code json parser off, as it's more than just "experimental"
													// Developer Notice:
													// if you want to help improve this feature then either comment out the line above
													// or add 'feature_jison_wiki_parser' = 'y' to your tiki.ini file
													// and enable that in your db/local.php
		),
		'rules' => array(),
	),
	array('readOnly' => false)
);
if (isset ($system_configuration_file)) {
	if (! is_readable($system_configuration_file)) {
		die('Configuration file could not be read.');
	}
	if (! isset($system_configuration_identifier)) {
		$system_configuration_identifier = null;
	}
	$systemConfiguration = $systemConfiguration->merge(new Zend_Config_Ini($system_configuration_file, $system_configuration_identifier));
}

if ( $re === false ) {
	if (! defined('TIKI_IN_INSTALLER')) {
		header('location: tiki-install.php');
		exit;
	} else {
		// we are in the installer don't redirect...
		return ;
	}
}

if ( $dbversion_tiki == '1.10' ) {
	$dbversion_tiki = '2.0';
}

/**
 *
 */
class TikiDb_LegacyErrorHandler implements TikiDb_ErrorHandler
{
    /**
     * @param TikiDb $db
     * @param $query
     * @param $values
     * @param $result
     */
    function handle( TikiDb $db, $query, $values, $result ) // {{{
	{
		global $smarty, $prefs;

		$msg = $db->getErrorMessage();
		$q=$query;
		if (is_array($values)) {
			foreach ($values as $v) {
				if (is_null($v)) {
					$v = 'NULL';
				} else {
					$v = "'" . addslashes($v) . "'";
				}
				$pos = strpos($q, '?');
				if ($pos !== false) {
					$q = substr($q, 0, $pos) . "$v" . substr($q, $pos + 1);
				}
			}
		}

		if (function_exists('xdebug_get_function_stack')) {
            /**
             * @param $stack
             * @return string
             */
            function mydumpstack($stack)
			{
				$o='';
				foreach ($stack as $line) {
					$o.='* '.$line['file']." : ".$line['line']." -> ".$line['function']."(".var_export($line['params'], true).")<br />";
				}
				return $o;
			}
			$stacktrace = mydumpstack(xdebug_get_function_stack());
		} else {
			$stacktrace = false;
		}

		require_once 'installer/installlib.php';
		$installer = new Installer;

		require_once('tiki-setup.php');

		$smarty->assign('msg', $msg);
		$smarty->assign('base_query', $query);
		$smarty->assign('values', $values);
		$smarty->assign('built_query', $q);
		$smarty->assign('stacktrace', $stacktrace);
		$smarty->assign('requires_update', $installer->requiresUpdate());

		header("Cache-Control: no-cache, pre-check=0, post-check=0");

		$smarty->display('database-connection-error.tpl');
		$this->log($msg.' - '.$q);
		die;
	} // }}}
    /**
     * @param $msg
     */
    function log($msg)
	{
		global $user, $tikilib;
		$query = 'insert into `tiki_actionlog` (`objectType`,`action`,`object`,`user`,`ip`,`lastModif`, `comment`, `client`) values (?,?,?,?,?,?,?,?)';
		$result = $tikilib->query($query, array('system', 'db error', 'system', $user, $tikilib->get_ip_address(), $tikilib->now, $msg, substr($_SERVER['HTTP_USER_AGENT'], 0, 200)));
	} // }}}
}

$initializer = new TikiDb_Initializer;
$initializer->setPreferredConnector($api_tiki);
$initializer->setInitializeCallback(function ($db) {
	global $db_table_prefix, $common_users_table_prefix, $db_tiki;

	$db->setServerType($db_tiki);

	if (! defined('TIKI_CONSOLE')) {
		$db->setErrorHandler(new TikiDb_LegacyErrorHandler);
	}

	if ( isset( $db_table_prefix ) ) {
		$db->setTablePrefix($db_table_prefix);
	}

	if ( isset( $common_users_table_prefix ) ) {
		$db->setUsersTablePrefix($common_users_table_prefix);
	}
});

try {
	$db = $initializer->getConnection($credentials['primary']);
} catch (Exception $e) {
	echo $e;
	require_once 'lib/init/smarty.php';

	$smarty->assign('msg', $e->getMessage());
	$smarty->assign('where', 'connection');
	echo $smarty->fetch('database-connection-error.tpl');
	exit;
}

if (! $db && ! defined('TIKI_IN_INSTALLER')) {
	header('location: tiki-install.php');
	exit;
}

TikiDb::set($db);

if ($credentials['shadow']) {
	global $dbMaster, $dbSlave;
	// Set-up the replication
	$dbMaster = $db;

	try {
		if ($dbSlave = $initializer->getConnection($credentials['shadow'])) {
			$db = new TikiDb_MasterSlaveDispatch($dbMaster, $dbSlave);
			TikiDb::set($db);
		}
	} catch (Exception $e) {
		// Just a slave, ignore
	}
}

unset($credentials);
