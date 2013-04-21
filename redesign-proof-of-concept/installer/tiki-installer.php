<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// To (re-)enable this script the file has to be named tiki-installer.php and the following four lines
// must start with two '/' and 'stopinstall:'. (Make sure there are no spaces inbetween // and stopinstall: !)

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

require_once('tiki-filter-base.php');

// Define and load Smarty components
$prefs = array();
$prefs['smarty_notice_reporting'] = 'n';
$prefs['smarty_compilation'] = 'always';
$prefs['smarty_security'] = 'y';
require_once 'lib/init/initlib.php';
set_error_handler("tiki_error_handling", error_reporting());
require_once ( 'lib/init/smarty.php');
require_once ('installer/installlib.php');

/**
 *
 */
class InstallerDatabaseErrorHandler implements TikiDb_ErrorHandler
{
    /**
     * @param TikiDb $db
     * @param $query
     * @param $values
     * @param $result
     */
    function handle(TikiDb $db, $query, $values, $result)
	{
	}
}

// Force autoloading
if (! class_exists('ADOConnection')) {
	die('AdoDb not found');
}

$dbTiki = ADONewConnection($db_tiki);
$db = new TikiDb_Adodb($dbTiki);
$db->setServerType($db_tiki);
$db->setErrorHandler(new InstallerDatabaseErrorHandler);
TikiDb::set($db);
$dbTiki = false;
$commands = array();
@ini_set('magic_quotes_runtime', 0);

// Initialize $prefs and force some values for the installer
$prefs = array(
	// tra() should not use $tikilib because this lib is not available in every steps of the installer
	//  and because we want to be sure that translations of the installer are the original ones, even for an upgrade
	'lang_use_db' => 'n'
);

// Which step of the installer
if (empty($_REQUEST['install_step'])) {
	$install_step = '0';

	if (isset($_REQUEST['setdbversion'])) {
		// Sets dbversion_tiki when installing the WebDeploy package
		$db = fopen('db/'.$tikidomainslash.'local.php', 'a');
		require_once 'lib/setup/twversion.class.php';
		$TWV = new TWVersion();
		fwrite($db, "\n\$dbversion_tiki='" . $TWV->getBaseVersion() . "';\n");
		fclose($db);
	}
} else {
	$install_step = $_REQUEST['install_step'];
}

// define the language to use, either from user-setting or default
if (!empty($_REQUEST['lang'])) {
	$language = $prefs['site_language'] = $prefs['language'] = $_REQUEST['lang'];
} else {
	$language = $prefs['site_language'] = $prefs['language'] = 'en';
}
include_once('lib/init/tra.php');

/**
 * @return bool
 */
function has_tiki_db()
{
	global $installer;
	return $installer->tableExists('users_users');
}

/**
 * @return bool
 */
function has_tiki_db_20()
{
	global $installer;
	return $installer->tableExists('tiki_pages_translation_bits');
}

/**
 * @param $dbb_tiki
 * @param $host_tiki
 * @param $user_tiki
 * @param $pass_tiki
 * @param $dbs_tiki
 * @param string $client_charset
 * @param string $api_tiki
 * @param string $dbversion_tiki
 */
function write_local_php($dbb_tiki, $host_tiki, $user_tiki, $pass_tiki, $dbs_tiki, $client_charset = '', $api_tiki = '', $dbversion_tiki = 'current')
{
	global $local;
	global $db_tiki;
	if ($dbs_tiki && $user_tiki) {
		$db_tiki = addslashes($dbb_tiki);
		$host_tiki = addslashes($host_tiki);
		$user_tiki = addslashes($user_tiki);
		$pass_tiki = addslashes($pass_tiki);
		$dbs_tiki = addslashes($dbs_tiki);
		$fw = fopen($local, 'w');
		$filetowrite = "<?php\n";
		$filetowrite .= "\$db_tiki='" . $db_tiki . "';\n";
		if ($dbversion_tiki == 'current') {
			require_once 'lib/setup/twversion.class.php';
			$twversion = new TWVersion();
			$dbversion_tiki = $twversion->getBaseVersion();
		}
		$filetowrite .= "\$dbversion_tiki='" . $dbversion_tiki . "';\n";
		$filetowrite .= "\$host_tiki='" . $host_tiki . "';\n";
		$filetowrite .= "\$user_tiki='" . $user_tiki . "';\n";
		$filetowrite .= "\$pass_tiki='" . $pass_tiki . "';\n";
		$filetowrite .= "\$dbs_tiki='" . $dbs_tiki . "';\n";
		if ( ! empty( $api_tiki ) ) {
			$filetowrite .= "\$api_tiki='" . $api_tiki . "';\n";
		}
		if ( ! empty( $client_charset ) ) {
			$filetowrite .= "\$client_charset='$client_charset';\n";
		}
		$filetowrite .= "// If you experience text encoding issues after updating (e.g. apostrophes etc showing up as strange characters) \n";
		$filetowrite .= "// \$client_charset='latin1';\n";
		$filetowrite .= "// \$client_charset='utf8';\n";
		$filetowrite .= "// See http://tiki.org/ReleaseNotes5.0#Known_Issues and http://doc.tiki.org/Understanding+Encoding for more info\n\n";
		$filetowrite .= "// If your php installation does not not have pdo extension\n";
		$filetowrite .= "// \$api_tiki = 'adodb';\n\n";
		$filetowrite .= "// Want configurations managed at the system level or restrict some preferences? http://doc.tiki.org/System+Configuration\n";
		$filetowrite .= "// \$system_configuration_file = '/etc/tiki.ini';\n";
		$filetowrite .= "// \$system_configuration_identifier = 'example.com';\n\n";
		fwrite($fw, $filetowrite);
		fclose($fw);
	}
}

/**
 * @param string $domain
 * @return string
 */
function create_dirs($domain='')
{
	global $tikipath;
	$dirs=array(
		'db',
		'dump',
		'img/wiki',
		'img/wiki_up',
		'img/trackers',
		'modules/cache',
		'temp',
		'temp/cache',
		'templates_c',
		'templates',
		'styles',
		'whelp');

	$ret = "";
	foreach ($dirs as $dir) {
		$dir = $dir.'/'.$domain;

		if (!is_dir($dir)) {
			$created = @mkdir($dir, 02775); // Try creating the directory
			if (!$created) {
				$ret .= "The directory '$tikipath$dir' could not be created.\n";
			}
		} else if (!TikiInit::is_writeable($dir)) {
			@chmod($dir, 02775);
			if (!TikiInit::is_writeable($dir)) {
				$ret .= "The directory '$tikipath$dir' is not writeable.\n";
			}
		}
	}
	return $ret;
}

/**
 * @return bool
 */
function isWindows()
{
	static $windows;

	if (!isset($windows)) {
		$windows = substr(PHP_OS, 0, 3) == 'WIN';
	}

	return $windows;
}

function check_session_save_path()
{
	global $errors;
	if (ini_get('session.save_handler') == 'files') {
        	$save_path = ini_get('session.save_path');
		// check if we can check it. The session.save_path can be outside
		// the open_basedir paths.
		$open_basedir=ini_get('open_basedir');
		if (empty($open_basedir)) {
        		if (!is_dir($save_path)) {
                		$errors .= "The directory '$save_path' does not exist or PHP is not allowed to access it (check open_basedir entry in php.ini).\n";
        		} else if (!TikiInit::is_writeable($save_path)) {
                		$errors .= "The directory '$save_path' is not writeable.\n";
        		}
		}

        	if ($errors) {
                	$save_path = TikiInit::tempdir();

                	if (is_dir($save_path) && TikiInit::is_writeable($save_path)) {
                        	ini_set('session.save_path', $save_path);

                        	$errors = '';
                	}
        	}
	}
}

function get_webserver_uid()
{
	global $wwwuser;
	global $wwwgroup;
	$wwwuser = '';
	$wwwgroup = '';

	if (isWindows()) {
        	$wwwuser = 'SYSTEM';

        	$wwwgroup = 'SYSTEM';
	}

	if (function_exists('posix_getuid')) {
        	$user = @posix_getpwuid(@posix_getuid());

        	$group = @posix_getpwuid(@posix_getgid());
        	$wwwuser = $user ? $user['name'] : false;
        	$wwwgroup = $group ? $group['name'] : false;
	}

	if (!$wwwuser) {
        	$wwwuser = 'nobody (or the user account the web server is running under)';
	}

	if (!$wwwgroup) {
        	$wwwgroup = 'nobody (or the group account the web server is running under)';
	}
}

function error_and_exit()
{
	global $errors, $tikipath;

        $PHP_CONFIG_FILE_PATH = PHP_CONFIG_FILE_PATH;

        $httpd_conf = 'httpd.conf';
/*
        ob_start();
        phpinfo (INFO_MODULES);

        if (preg_match('/Server Root<\/b><\/td><td\s+align="left">([^<]*)</', ob_get_contents(), $m)) {
                $httpd_conf = $m[1] . '/' . $httpd_conf;
        }

        ob_end_clean();
*/

        print "<html><body>\n<h2><IMG SRC=\"img/tiki/Tiki_WCG.png\" ALT=\"\" BORDER=0><br /\>
	<font color='red'>Tiki Installer cannot proceed</font></h2>\n<pre>\n$errors";

        if (!isWindows()) {
                print "<br /><br />Your options:


1- With FTP access:
	a) Change the permissions (chmod) of the directories to 777.
	b) Create any missing directories
	c) <a href='tiki-install.php'>Execute the Tiki installer again</a> (Once you have executed these commands, this message will disappear!)

or

2- With shell (SSH) access, you can run the command below.

	a) To run setup.sh, follow the instructions:
		\$ cd $tikipath
		\$ sh setup.sh

		The script will offer you options depending on your server configuration.

	b) <a href='tiki-install.php'>Execute the Tiki installer again</a> (Once you have executed these commands, this message will disappear!)


<hr>
If you have problems accessing a directory, check the open_basedir entry in
$PHP_CONFIG_FILE_PATH/php.ini or $httpd_conf.

<hr>

<a href='http://doc.tiki.org/Installation' target='_blank'>Consult the tiki.org installation guide</a> if you need more help or <a href='http://tiki.org/tiki-forums.php' target='_blank'>visit the forums</a>

";
        }
	print "</pre></body></html>";
        exit;
}



// Try to see if we have an admin account
/**
 * @param $dbTiki
 * @param $api_tiki
 * @return string
 */
function has_admin( $dbTiki, $api_tiki )
{
	$query = "select hash from users_users where login='admin'";
	$res = false;

	$db = TikiDb::get();
	$result = $db->fetchAll($query);

	if (is_array($result)) {
		$res = reset($result);
	}

	if ( $res && isset( $res['hash'] ) ) {
		$admin_acc = 'y';
	} else {
		$admin_acc = 'n';
	}

	return $admin_acc;
}

/**
 * @param $dbTiki
 * @return bool
 */
function get_admin_email( $dbTiki )
{
	global $installer;
	$query = "SELECT `email` FROM `users_users` WHERE `userId`=1";
	@$result = $installer->query($query);

	if ( $result && $res = $result->fetchRow() ) {
		return $res['email'];
	}

	return false;
}

/**
 * @param $dbTiki
 * @param $prefs
 * @return bool
 */
function update_preferences( $dbTiki, &$prefs )
{
	global $installer;
	$query = "SELECT `name`, `value` FROM `tiki_preferences`";
	@$result = $installer->query($query);

	if ( $result ) {
		while ( $res = $result->fetchRow() ) {
			if ( ! isset($prefs[$res['name']]) ) {
				$prefs[$res['name']] = $res['value'];
			}
		}
		return true;
	}

	return false;
}

/**
 * @param $account
 */
function fix_admin_account( $account )
{
	global $installer;

	$result = $installer->query('SELECT `id` FROM `users_groups` WHERE `groupName` = "Admins"');
	if ( ! $row = $result->fetchRow() ) {
		$installer->query('INSERT INTO `users_groups` (`groupName`) VALUES("Admins")');
	}

	$installer->query('INSERT IGNORE INTO `users_grouppermissions` (`groupName`, `permName`) VALUES("Admins", "tiki_p_admin")');

	$result = $installer->query('SELECT `userId` FROM `users_users` WHERE `login` = ?', array( $account ));
	if ( $row = $result->fetchRow() ) {
		$id = $row['userId'];
		$installer->query('INSERT IGNORE INTO `users_usergroups` (`userId`, `groupName`) VALUES(?, "Admins")', array( $id ));
	}
}

/* possible error after upgrade 4 */
function fix_disable_accounts()
{
	global $installer;
	$installer->query('update `users_users` set `waiting`=NULL where `waiting` = ? and `valid` is NULL', array('a'));
}

/**
 * @return array
 */
function list_disable_accounts()
{
	global $installer;
	$result = $installer->query('select `login` from `users_users` where `waiting` = ? and `valid` is NULL', array('a'));
	$ret = array();
	while ($res = $result->fetchRow()) {
		$ret[] = $res['login'];
	}
	return $ret;
}

/**
 * @param $api
 * @param $driver
 * @param $host
 * @param $user
 * @param $pass
 * @param $dbname
 * @param $client_charset
 * @param $dbTiki
 * @return bool|int
 */
function initTikiDB( &$api, &$driver, $host, $user, $pass, $dbname, $client_charset, &$dbTiki )
{
	global $tikifeedback;
	$dbcon = false;

	// This section handles the case of adodb (not the preferred case)
	if ( ( isset($api) && $api == 'adodb' ) || ! extension_loaded('pdo') ) {
		$api = 'adodb';
		$dbTiki = ADONewConnection($driver);
		$db = new TikiDb_Adodb($dbTiki);
		if (! $dbcon = (bool) @$dbTiki->Connect($host, $user, $pass, $dbname) ) {
			$tikifeedback[] = array( 'num' => 1, 'mes' => $dbTiki->ErrorMsg() );
		}

		// Attempt to create database. This might work if the $user has create database permissions.
		// First check that suggested database name will not cause issues
		$dbname_clean = preg_replace('/[^a-z0-9$_-]/', "", $dbname);
		if ($dbname_clean != $dbname) {
			$tikifeedback[] = array( 'num' => 1, 'mes'=> tra("Some invalid characters were detected in database name. Please use alphanumeric characters or _ or -.", '', false, array($dbname_clean)) );
			$attempt_creation=false;
		} else {
			$attempt_creation=true;
		}
		if ( (! $dbcon) && ($attempt_creation == true) ) {
			$dbh = ADONewConnection($driver);
			if ( @$dbh->Connect($host, $user, $pass) ) {
				$sql="CREATE DATABASE IF NOT EXISTS `$dbname_clean` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;";
				$dbcon=$dbh->Execute($sql);
				if ( $dbcon ) {
					$tikifeedback[] = array( 'num' => 1, 'mes'=> tra("Database `%0` was created.", '', false, array($dbname_clean)) );
				} else {
					$tikifeedback[] = array( 'num' => 1, 'mes'=> tra("Database `%0` creation failed. You need to create the database.", '', false, array($dbname_clean)) );
				}
			} else {
				$tikifeedback[] = array( 'num' => 1, 'mes' => $dbh->ErrorMsg() );
			}

			if ( $dbcon ) {
				$dbTiki = ADONewConnection($driver);
				$db = new TikiDb_Adodb($dbTiki);
				if (! $dbcon = (bool) @$dbTiki->Connect($host, $user, $pass, $dbname) ) {
					$tikifeedback[] = array( 'num' => 1, 'mes' => $dbTiki->ErrorMsg() );
				}
			}

		}

	// This section handles the case of PDO (preferred case)
	} else {
		$db_hoststring = "host=$host";

		if ( $driver == 'mysqli' ) {
			$driver = 'mysql';
			if ( isset( $socket_tiki ) ) {
				$db_hoststring = "unix_socket=$socket_tiki";
			}
		}

		try {
			$dbTiki = new PDO("$driver:$db_hoststring;dbname=$dbname", $user, $pass);
			$db = new TikiDb_Pdo($dbTiki);
			$dbcon = true;
		} catch ( PDOException $e ) {
			$dbcon = false;
			$tikifeedback[] = array( 'num' => 1, 'mes'=> $e->getMessage() );
		}

		// Attempt to create database. This might work if the $user has create database permissions.
		// First check that suggested database name will not cause issues
		$dbname_clean = preg_replace('/[^a-z0-9$_-]/', "", $dbname);
		if ($dbname_clean != $dbname) {
			$tikifeedback[] = array( 'num' => 1, 'mes'=> tra("Some invalid characters were detected in database name. Please use alphanumeric characters or _ or -.", '', false, array($dbname_clean)) );
			$attempt_creation=false;
		} else {
			$attempt_creation=true;
		}
		if ( (! $dbcon) && ($attempt_creation == true) ) {
			try {
				$dbh = new PDO("$driver:$db_hoststring", $user, $pass);
				$sql="CREATE DATABASE IF NOT EXISTS `$dbname_clean` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;";
				$dbcon=$dbh->exec($sql);
				if ( $dbcon ) {
					$tikifeedback[] = array( 'num' => 1, 'mes'=> tra("Database `%0` was created.", '', false, array($dbname_clean)) );
				} else {
					$tikifeedback[] = array( 'num' => 1, 'mes'=> tra("Database `%0` creation failed. You need to create the database.", '', false, array($dbname_clean)) );
				}
			} catch ( PDOException $e ) {
				$dbcon = false;
				$tikifeedback[] = array( 'num' => 1, 'mes'=> $e->getMessage() );
			}

			if ( $dbcon ) {
				try {
					$dbTiki = new PDO("$driver:$db_hoststring;dbname=$dbname", $user, $pass);
					$db = new TikiDb_Pdo($dbTiki);
				} catch ( PDOException $e ) {
					$dbcon = false;
					$tikifeedback[] = array( 'num' => 1, 'mes'=> $e->getMessage() );
				}
			}

		}
	}

	if ( $dbcon ) {
		$db->setErrorHandler(new InstallerDatabaseErrorHandler);

		if ( ! empty( $client_charset ) ) {
			$db->query("SET CHARACTER SET $client_charset");
		}

		TikiDb::set($db);
	}

	return $dbcon;
}

/**
 * @param $dbname
 */
function convert_database_to_utf8( $dbname )
{
	$db = TikiDb::get();

	if ( $result = $db->fetchAll('SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ?', $dbname)) {
		$db->query("ALTER DATABASE `$dbname` CHARACTER SET utf8 COLLATE utf8_unicode_ci");

		foreach ( $result as $row ) {
			$db->query("ALTER TABLE `{$row['TABLE_NAME']}` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci");
		}
	} else {
		die('MySQL INFORMATION_SCHEMA not available. Your MySQL version is too old to perform this operation. (convert_database_to_utf8)');
	}
}

/**
 * @param $dbname
 * @param $previous
 */
function fix_double_encoding( $dbname, $previous )
{
	$db = TikiDb::get();

	$text_fields = $db->fetchAll("SELECT TABLE_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND CHARACTER_SET_NAME IS NOT NULL", array($dbname));

	if ( $text_fields ) {
		foreach ( $text_fields as $field ) {
			$db->query("UPDATE `{$field['TABLE_NAME']}` SET `{$field['COLUMN_NAME']}` = CONVERT(CONVERT(CONVERT(CONVERT(`{$field['COLUMN_NAME']}` USING binary) USING utf8) USING $previous) USING binary)");
		}
	} else {
		die('MySQL INFORMATION_SCHEMA not available. Your MySQL version is too old to perform this operation. (fix_double_encoding)');
	}
}

// -----------------------------------------------------------------------------
// end of functions .. now starts the processing

// If using multiple Tikis
if (is_file('db/virtuals.inc')) {
	$virtuals = array_map('trim', file('db/virtuals.inc'));
	foreach ($virtuals as $v) {
		if ($v) {
			if (is_file("db/$v/local.php") && is_readable("db/$v/local.php")) {
				$virt[$v] = 'y';
			} else {
				$virt[$v] = 'n';
			}
		}
	}
} else {
	$virt = false;
	$virtuals = false;
}

$multi = '';
// If using multiple Tiki installations (MultiTiki)
if ($virtuals) {
	if (isset($_REQUEST['multi']) && in_array($_REQUEST['multi'], $virtuals)) {
		$multi = $_REQUEST['multi'];
	} else {
		if (isset($_SERVER['TIKI_VIRTUAL']) && is_file('db/'.$_SERVER['TIKI_VIRTUAL'].'/local.php')) {
			$multi = $_SERVER['TIKI_VIRTUAL'];
		} elseif (isset($_SERVER['SERVER_NAME']) && is_file('db/'.$_SERVER['SERVER_NAME'].'/local.php')) {
			$multi = $_SERVER['SERVER_NAME'];
		} elseif (isset($_SERVER['HTTP_HOST']) && is_file('db/'.$_SERVER['HTTP_HOST'].'/local.php')) {
			$multi = $_SERVER['HTTP_HOST'];
		}
	}
}
if (!empty($multi)) {
	$local = "db/$multi/local.php";
} else {
	$local = 'db/local.php';
}

$tikidomain = $multi;
$tikidomainslash = (!empty($tikidomain) ? $tikidomain . '/' : '');

$title = tra('Tiki Installer');

include 'lib/cache/cachelib.php';
$cachelib->empty_cache();

$_SESSION["install-logged-$multi"] = 'y';

// Init smarty
global $tikidomain;
$smarty->assign('mid', 'tiki-install.tpl');
$smarty->assign('virt', isset($virt) ? $virt : null);
$smarty->assign('multi', isset($multi) ? $multi : null);
$smarty->assign('lang', $language);

// Try to set a longer execution time for the installer
@ini_set('max_execution_time', '0');
$max_execution_time = ini_get('max_execution_time');
$smarty->assign('max_exec_set_failed', 'n');
if ($max_execution_time != 0) {
	$smarty->assign('max_exec_set_failed', 'y');
}

// Tiki Database schema version
include_once ('lib/setup/twversion.class.php');
$TWV = new TWVersion();
$smarty->assign('tiki_version_name', preg_replace('/^(\d+\.\d+)([^\d])/', '\1 \2', $TWV->version));

// Available DB Servers
$dbservers = array();
if (function_exists('mysqli_connect'))	$dbservers['mysqli'] = tra('MySQL Improved (mysqli)');
if (function_exists('mysql_connect'))	$dbservers['mysql'] = tra('MySQL classic (mysql)');
$smarty->assignByRef('dbservers', $dbservers);

$errors = '';

check_session_save_path();

get_webserver_uid();

$errors .= create_dirs($multi);

if ($errors) {
	error_and_exit();
}

// Second check try to connect to the database
// if no local.php => no con
// if local then build dsn and try to connect
//   then get con or nocon

//adodb settings

if (!defined('ADODB_FORCE_NULLS')) {
	define('ADODB_FORCE_NULLS', 1);
}

if (!defined('ADODB_ASSOC_CASE')) {
	define('ADODB_ASSOC_CASE', 2);
}

if (!defined('ADODB_CASE_ASSOC')) { // typo in adodb's driver for sybase? // so do we even need this without sybase? What's this?
	define('ADODB_CASE_ASSOC', 2);
}

include('lib/tikilib.php');

// Get list of available languages
$languages = TikiLib::list_languages(false, null, true);
$smarty->assignByRef("languages", $languages);

$client_charset = '';

// next block checks if there is a local.php and if we can connect through this.
// sets $dbcon to false if there is no valid local.php
$dbcon = false;
$installer = null;
if ( file_exists($local) ) {
	// include the file to get the variables
	$default_api_tiki = $api_tiki;
	$api_tiki = '';
	include $local;
	if ( ! $client_charset_forced = isset($client_charset) ) {
		$client_charset = '';
	}
	$previousDbApi = $api_tiki;
	if ( empty( $api_tiki ) ) {
		$api_tiki_forced = false;
		$api_tiki = $default_api_tiki;
		if ( ! empty( $dbversion_tiki ) && $dbversion_tiki[0] < 4 ) {
			$previousDbApi = 'adodb'; // AdoDB was the default DB abstraction layer before 4.0
		}
	} else {
		$api_tiki_forced = true;
	}

	unset( $default_api_tiki );

	// In case of replication, ignore it during installer.
	unset( $shadow_dbs, $shadow_user, $shadow_pass, $shadow_host );
	if ($dbversion_tiki == '1.10') {
		$dbversion_tiki = '2.0';
	}

	if (!isset($db_tiki)) {
		// if no db is specified, use the first db that this php installation can handle
		$db_tiki = reset($dbservers);
		write_local_php($db_tiki, $host_tiki, $user_tiki, $pass_tiki, $dbs_tiki, $client_charset, ($api_tiki_forced ? $api_tiki : ''), $dbversion_tiki);
	}

	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

	$dbcon = false;
	$smarty->assign('resetdb', 'n');
	if ( isset( $dbservers[$db_tiki] ) ) { // avoid errors in ADONewConnection() (wrong darabase driver etc...)
		if ( $dbcon = initTikiDB($api_tiki, $db_tiki, $host_tiki, $user_tiki, $pass_tiki, $dbs_tiki, $client_charset, $dbTiki) ) {
			$smarty->assign('resetdb', isset($_REQUEST['reset']) ? 'y' : 'n');

			$installer = new Installer;
			$installer->setServerType($db_tiki);

			if ( ! $client_charset_forced ) {

				write_local_php($db_tiki, $host_tiki, $user_tiki, $pass_tiki, $dbs_tiki, $client_charset, ($api_tiki_forced ? $api_tiki : ''), $dbversion_tiki);
			}
		}
	}
}

if ( $dbcon ) {
	$admin_acc = has_admin($dbTiki, $api_tiki);
}

if ( $admin_acc == 'n' ) {
	$smarty->assign('noadmin', 'y');
} else {
	$smarty->assign('noadmin', 'n');
}


// We won't update database info unless we can't connect to the database.
// We won't reset the db connection if there is an admin account set
// and the admin is not logged
if (
	(
		!$dbcon
		|| (
			isset($_REQUEST['resetdb'])
			&& $_REQUEST['resetdb'] == 'y'
			&& (
				$admin_acc == 'n'
				|| (isset($_SESSION["install-logged-$multi"])
				&& $_SESSION["install-logged-$multi"] == 'y')
			)
		)
	) && isset($_REQUEST['dbinfo'])
) {
	if ( ! empty($_REQUEST['name']) ) {
		if ( isset( $_REQUEST['force_utf8'] ) ) {
			$client_charset = 'utf8';
		} else {
			$client_charset = '';
		}

		$dbcon = initTikiDB($api_tiki, $_REQUEST['db'], $_REQUEST['host'], $_REQUEST['user'], $_REQUEST['pass'], $_REQUEST['name'], $client_charset, $dbTiki);

		if ($dbcon) {
			write_local_php($_REQUEST['db'], $_REQUEST['host'], $_REQUEST['user'], $_REQUEST['pass'], $_REQUEST['name'], $client_charset);
			include $local;
			// In case of replication, ignore it during installer.
			unset($shadow_dbs, $shadow_user, $shadow_pass, $shadow_host);
			$installer = new Installer;
			$installer->setServerType($db_tiki);
		}
	} else {
		$dbcon = false;
		$tikifeedback[] = array('num'=>1, 'mes'=>tra("No database name specified"));
	}
}
// Mark that InnoDB is to be used, if selected
if (isset($_REQUEST['useInnoDB'])) {
	if (intval($_REQUEST['useInnoDB']) > 0) {
		if ($installer != null) {
			$installer->useInnoDB = true;
		}
	}
}

if ( $dbcon ) {
	$smarty->assign('dbcon', 'y');
	$smarty->assign('dbname', $dbs_tiki);
} else {
	$smarty->assign('dbcon', 'n');
}

// Some initializations to avoid PHP error messages
$smarty->assign('tikidb_created', FALSE);
$smarty->assign('tikidb_is20', FALSE);

if ($dbcon) {
	$has_tiki_db = has_tiki_db();
	$smarty->assign('tikidb_created', $has_tiki_db);
	$oldPerms = $installer->getOne('SELECT COUNT(*) FROM `users_permissions` WHERE `permDesc` = \'Can view categorized items\'');
	$smarty->assign('tikidb_oldPerms', $oldPerms);

	if ($install_step == '6' && $has_tiki_db) {
		update_preferences($dbTiki, $prefs);
		$smarty->assign('admin_email', get_admin_email($dbTiki));
		$smarty->assign('upgradefix', (empty($dbversion_tiki) || $dbversion_tiki[0] < 4) ? 'y' : 'n');
	}
	$smarty->assign('tikidb_is20', has_tiki_db_20());
}

if (isset($_REQUEST['restart'])) {
	$_SESSION["install-logged-$multi"] = '';
}

$smarty->assign('admin_acc', $admin_acc);

// If no admin account then we are logged
if ($admin_acc == 'n') {
	$_SESSION["install-logged-$multi"] = 'y';
}

$smarty->assign('dbdone', 'n');
$smarty->assign('logged', $logged);

// Installation steps
if (
	isset($dbTiki)
	&& is_object($dbTiki)
	&& isset($_SESSION["install-logged-$multi"])
	&& $_SESSION["install-logged-$multi"] == 'y'
) {
	$smarty->assign('logged', 'y');

	if ( isset($_REQUEST['scratch']) ) {
		$installer->cleanInstall();
		$smarty->assign('installer', $installer);
		$smarty->assign('dbdone', 'y');
		$install_type = 'scratch';
		require_once 'lib/tikilib.php';
		$tikilib = new TikiLib;
		require_once 'lib/userslib.php';
		$userlib = new UsersLib;
		require_once 'lib/tikidate.php';
		$tikidate = new TikiDate();
	}

	if (isset($_REQUEST['update'])) {
		$installer->update();
		$smarty->assign('installer', $installer);
		$smarty->assign('dbdone', 'y');
		$install_type = 'update';
	}

	// Try to activate Apache htaccess file by making a symlink or copying _htaccess into .htaccess
	// Do nothing (but warn the user to do it manually) if:
	//   - there is no  _htaccess file,
	//   - there is already an existing .htaccess (that is not necessarily the one that comes from Tiki),
	//   - the copy does not work (e.g. due to filesystem permissions)
	//
	// TODO: Perform up-to-date check as in the SEFURL admin panel
	// TODO: Equivalent for IIS
	if ( strpos($_SERVER['SERVER_SOFTWARE'], 'Apache') !== false && !file_exists('.htaccess') && function_exists('symlink') && !@symlink('_htaccess', '.htaccess') && ! @copy('_htaccess', '.htaccess')) {
		$smarty->assign('htaccess_error', 'y');
	}
}

if (!isset($install_type)) {
	if (isset($_REQUEST['install_type'])) {
		$install_type = $_REQUEST['install_type'];
	} else {
		$install_type = '';
	}
}

if ( isset( $_GET['lockenter'] ) || isset( $_GET['nolockenter'] ) ) {
	if (isset( $_GET['lockenter'])) {
		touch('db/'.$tikidomainslash.'lock');
	}

	global $userlib, $cachelib;
	if (session_id()) {
		session_destroy();
	}
	include_once 'tiki-setup.php';
	$cachelib->empty_cache();
	if ($install_type == 'scratch') {
		$u = 'tiki-change_password.php?user=admin&oldpass=admin';
	} else {
		$u = '';
	}
	$userlib->user_logout($user, false, $u);	// logs out then redirects to home page or $u
	exit;
}

$smarty->assignByRef('tikifeedback', $tikifeedback);

$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

$email_test_tw = 'mailtest@tiki.org';
$smarty->assign('email_test_tw', $email_test_tw);

//  Sytem requirements test.
if ($install_step == '2') {
	$smarty->assign('mail_test_performed', 'n');
	if (isset($_REQUEST['perform_mail_test']) && $_REQUEST['perform_mail_test'] == 'y') {

		$email_test_to = $email_test_tw;
		$email_test_headers = '';
		$email_test_ready = true;

		if (!empty($_REQUEST['email_test_to'])) {
			$email_test_to =  $_REQUEST['email_test_to'];

			if (isset($_REQUEST['email_test_cc']) && $_REQUEST['email_test_cc'] == '1') {
				$email_test_headers .= "Cc: $email_test_tw\n";
			}

			// check email address format
			$validator = new Zend_Validate_EmailAddress();
			if (!$validator->isValid($email_test_to)) {
				$smarty->assign('email_test_err', tra('Email address not valid, test mail not sent'));
				$email_test_ready = false;
			}
		} else {	// no email supplied, check copy checkbox
			if (!isset($_REQUEST['email_test_cc']) || $_REQUEST['email_test_cc'] != '1') {
				$smarty->assign('email_test_err', tra('Email address empty and "copy" checkbox not set, test mail not sent'));
				$email_test_ready = false;
			}
		}
		$smarty->assign('email_test_to', $email_test_to);

		if ($email_test_ready) {	// so send the mail
			$email_test_headers .= 'From: noreply@tiki.org' . "\n";	// needs a valid sender
			$email_test_headers .= 'Reply-to: '. $email_test_to . "\n";
			$email_test_headers .= "Content-type: text/plain; charset=utf-8\n";
			$email_test_headers .= 'X-Mailer: Tiki/'.$TWV->version.' - PHP/' . phpversion() . "\n";
			$email_test_subject = tr('Test mail from Tiki installer %0', $TWV->version);
			$email_test_body = tra("Congratulations!\n\nYour server can send emails.\n\n");
			$email_test_body .= "\t".tra('Tiki version:').' '.$TWV->version . "\n";
			$email_test_body .= "\t".tra('PHP version:').' '.phpversion() . "\n";
			$email_test_body .= "\t".tra('Server:').' '.(empty($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_ADDR'] : $_SERVER['SERVER_NAME']) . "\n";
			$email_test_body .= "\t".tra('Sent:').' '.date(DATE_RFC822) . "\n";

			$sentmail = mail($email_test_to, $email_test_subject, $email_test_body, $email_test_headers);
			if ($sentmail) {
				$mail_test = 'y';
			} else {
				$mail_test = 'n';
			}
			$smarty->assign('mail_test', $mail_test);
			$smarty->assign('mail_test_performed', 'y');

		}
	}

	// copy of most of $tikilib->return_bytes() not available at this stage
	$memory_limit = trim(ini_get('memory_limit'));
	$last = strtolower($memory_limit{strlen($memory_limit)-1});
	switch ( $last ) {
		// The 'G' modifier is available since PHP 5.1.0
		case 'g': $memory_limit *= 1024;
		case 'm': $memory_limit *= 1024;
		case 'k': $memory_limit *= 1024;
	}
	$smarty->assign('php_memory_limit', intval($memory_limit));

	if ((extension_loaded('gd') && function_exists('gd_info'))) {
		$gd_test = 'y';
		$gd_info = gd_info();
		$smarty->assign('gd_info', $gd_info['GD Version']);

		$im = @imagecreate(110, 20);
		if ($im) {
				$smarty->assign('sample_image', 'y');
				imagedestroy($im);
		} else {
				$smarty->assign('sample_image', 'n');
		}

	} else {
		$gd_test = 'n';
	}
	$smarty->assign('gd_test', $gd_test);
} elseif ($install_step == 6 && !empty($_REQUEST['validPatches'])) {
	foreach ($_REQUEST['validPatches'] as $patch) {
		global $installer;
		$installer->recordPatch($patch);
	}
}

unset($TWV);

// write general settings
if ( isset($_REQUEST['general_settings']) && $_REQUEST['general_settings'] == 'y' ) {
	global $dbTiki;
	$switch_ssl_mode = ( isset($_REQUEST['feature_switch_ssl_mode']) && $_REQUEST['feature_switch_ssl_mode'] == 'on' ) ? 'y' : 'n';
	$show_stay_in_ssl_mode = ( isset($_REQUEST['feature_show_stay_in_ssl_mode']) && $_REQUEST['feature_show_stay_in_ssl_mode'] == 'on' ) ? 'y' : 'n';

	$installer->query(
		"DELETE FROM `tiki_preferences` WHERE `name` IN " .
		"('browsertitle', 'sender_email', 'https_login', 'https_port', ".
		"'feature_switch_ssl_mode', 'feature_show_stay_in_ssl_mode', 'language',".
		"'error_reporting_level', 'error_reporting_adminonly', 'smarty_notice_reporting', 'log_tpl')"
	);

	$query = "INSERT INTO `tiki_preferences` (`name`, `value`) VALUES"
		. " ('browsertitle', ?),"
		. " ('sender_email', ?),"
		. " ('https_login', ?),"
		. " ('https_port', ?),"
		. " ('error_reporting_level', ?),"
		. " ('error_reporting_adminonly', '" . (isset($_REQUEST['error_reporting_adminonly']) && $_REQUEST['error_reporting_adminonly'] == 'on' ? 'y' : 'n') . "'),"
		. " ('smarty_notice_reporting', '" . (isset($_REQUEST['smarty_notice_reporting']) && $_REQUEST['smarty_notice_reporting'] == 'on' ? 'y' : 'n') . "'),"
		. " ('log_tpl', '" . (isset( $_REQUEST['log_tpl']) && $_REQUEST['log_tpl'] == 'on' ? 'y' : 'n') . "'),"
		. " ('feature_switch_ssl_mode', '$switch_ssl_mode'),"
		. " ('feature_show_stay_in_ssl_mode', '$show_stay_in_ssl_mode'),"
		. " ('language', ?)";

	$installer->query($query, array($_REQUEST['browsertitle'], $_REQUEST['sender_email'], $_REQUEST['https_login'], $_REQUEST['https_port'], $_REQUEST['error_reporting_level'], $language));
	$installer->query("UPDATE `users_users` SET `email` = '".$_REQUEST['admin_email']."' WHERE `users_users`.`userId`=1");

	if ( isset( $_REQUEST['admin_account'] ) && ! empty( $_REQUEST['admin_account'] ) ) {
		fix_admin_account($_REQUEST['admin_account']);
	}
	if (isset($_REQUEST['fix_disable_accounts']) && $_REQUEST['fix_disable_accounts'] == 'on') {
		$ret = fix_disable_accounts();
	}

}


include_once "lib/headerlib.php";
$headerlib->add_js("var tiki_cookie_jar=new Array();");
$headerlib->add_cssfile('styles/fivealive.css');
$headerlib->add_jsfile('lib/tiki-js.js');
$headerlib->add_jsfile_dependancy("vendor/jquery/jquery-min/jquery-$headerlib->jquery_version.min.js");
$headerlib->add_jsfile('lib/jquery_tiki/tiki-jquery.js');
	$js = '
// JS Object to hold prefs for jq
var jqueryTiki = new Object();
jqueryTiki.ui = false;
jqueryTiki.ui_theme = "";
jqueryTiki.tooltips = false;
jqueryTiki.autocomplete = false;
jqueryTiki.superfish = false;
jqueryTiki.replection = false;
jqueryTiki.tablesorter = false;
jqueryTiki.colorbox = false;
jqueryTiki.cboxCurrent = "{current} / {total}";
jqueryTiki.carousel = false;

jqueryTiki.effect = "";
jqueryTiki.effect_direction = "";
jqueryTiki.effect_speed = 400;
jqueryTiki.effect_tabs = "";
jqueryTiki.effect_tabs_direction = "";
jqueryTiki.effect_tabs_speed = 400;
';
$headerlib->add_js($js, 100);


$smarty->assignByRef('headerlib', $headerlib);

$smarty->assign('install_step', $install_step);
$smarty->assign('install_type', $install_type);
$smarty->assignByRef('prefs', $prefs);
$smarty->assign('detected_https', isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == 'on');

if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6') !== false) {
	$smarty->assign('ie6', true);
}

$client_charset = '';

if ( file_exists($local) ) {
	include $local;
}

$smarty->assign('client_charset_in_file', $client_charset);

if ( isset( $_POST['convert_to_utf8'] ) ) {
	convert_database_to_utf8($dbs_tiki);
}

$smarty->assign('double_encode_fix_attempted', 'n');
if ( isset( $_POST['fix_double_encoding'] ) && ! empty($_POST['previous_encoding']) ) {
	fix_double_encoding($dbs_tiki, $_POST['previous_encoding']);
	$smarty->assign('double_encode_fix_attempted', 'y');
}

if ( $install_step == '4' ) {
	// Show the innodb option in the (re)install section if InnoDB is present
	if (isset($installer) and $installer->hasInnoDB()) {
		$smarty->assign('hasInnoDB', true);
	} else {
		$smarty->assign('hasInnoDB', false);
	}

	$value = '';
	if ( ($db = TikiDB::get()) && ($result = $db->fetchAll('show variables like "character_set_database"'))) {
		$res = reset($result);
		$variable = array_shift($res);
		$value = array_shift($res);
	}
	$smarty->assign('database_charset', $value);

}

if (((isset($value) && $value == 'utf8') || $install_step == '7') && $db = TikiDB::get()) {
	$result = $db->fetchAll('SELECT TABLE_COLLATION FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_COLLATION NOT LIKE "utf8%"', $dbs_tiki);
	if (!empty ($result) ) {
		$smarty->assign('legacy_collation', $result[0]['TABLE_COLLATION']);
	}
}

if ($install_step == '6') {
	$smarty->assign('disableAccounts', list_disable_accounts());
}

$mid_data = $smarty->fetch('tiki-install.tpl');
$smarty->assign('mid_data', $mid_data);

$smarty->assign('title', $title);
$smarty->assign('phpErrors', $phpErrors);
$smarty->display("tiki-install_screens.tpl");
