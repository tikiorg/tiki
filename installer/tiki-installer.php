<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
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

require_once( 'tiki-filter-base.php' );

// Define and load Smarty components
require_once ( 'lib/smarty/libs/Smarty.class.php');
require_once ('installer/installlib.php');

$commands = array();
@ini_set('magic_quotes_runtime',0);

// Initialize $prefs and force some values for the installer
$prefs = array(
	// tra() should not use $tikilib because this lib is not available in every steps of the installer
	//  and because we want to be sure that translations of the installer are the original ones, even for an upgrade
	'lang_use_db' => 'n' 
);

// Which step of the installer
if (empty($_REQUEST['install_step'])) {
	$install_step = '0';
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

function has_tiki_db()
{
	global $installer;
	return $installer->tableExists('users_users');
}

function has_tiki_db_20()
{
	global $installer;
	return $installer->tableExists('tiki_pages_translation_bits');
}

function write_local_php($dbb_tiki, $host_tiki, $user_tiki, $pass_tiki, $dbs_tiki, $dbversion_tiki="4.0") {
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
		$filetowrite .= "\$dbversion_tiki='" . $dbversion_tiki . "';\n";
		$filetowrite .= "\$host_tiki='" . $host_tiki . "';\n";
		$filetowrite .= "\$user_tiki='" . $user_tiki . "';\n";
		$filetowrite .= "\$pass_tiki='" . $pass_tiki . "';\n";
		$filetowrite .= "\$dbs_tiki='" . $dbs_tiki . "';\n";
		fwrite($fw, $filetowrite);
		fclose($fw);
	}
}

function create_dirs($domain=''){
	global $docroot;
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
		// Create directories as needed
		if (!is_dir($dir)) {
			@mkdir($dir,02775);
		}
		@chmod($dir,02775);
		// Check again and report problems
		if (!is_dir($dir)) {
			$ret .= "The directory '$docroot/$dir' does not exist.\n";
		} else if (!TikiInit::is_writeable($dir)) {
			@chmod($dir,02777);
			if (!TikiInit::is_writeable($dir)) {
				$ret .= "The directory '$docroot/$dir' is not writeable.\n";
			}
		}
	}
	return $ret;
}

function isWindows() {
	static $windows;

	if (!isset($windows)) {
		$windows = substr(PHP_OS, 0, 3) == 'WIN';
	}

	return $windows;
}

class Smarty_Tikiwiki_Installer extends Smarty
{

	function Smarty_Tikiwiki_Installer($tikidomain) {
		parent::Smarty();
		if ($tikidomain) { $tikidomain.= '/'; }
		$this->template_dir = realpath('templates/');
		$this->compile_dir = realpath("templates_c/$tikidomain");
		$this->config_dir = realpath('configs/');
		$this->cache_dir = realpath("templates_c/$tikidomain");
		$this->caching = 0;
		$this->assign('app_name', 'Tikiwiki');
		include_once('lib/setup/third_party.php');
		$this->plugins_dir = array(	// the directory order must be like this to overload a plugin
			TIKI_SMARTY_DIR,
			SMARTY_DIR.'plugins'
		);

		// In general, it's better that use_sub_dirs = false
		// If ever you are on a very large/complex/multilingual site and your
		// templates_c directory is > 10 000 files, (you can check at tiki-admin_system.php)
		// you can change to true and maybe you will get better performance.
		// http://smarty.php.net/manual/en/variable.use.sub.dirs.php
		//
		$this->use_sub_dirs = false;

		// security_settings['MODIFIER_FUNCS'], ['IF_FUNCS'] and secure_dir not needed in installer

		$this->security_settings['ALLOW_SUPER_GLOBALS'] = true;
		//$this->debugging = true;
		//$this->debug_tpl = 'debug.tpl';
	}

	function fetch($_smarty_tpl_file, $_smarty_cache_id = null, $_smarty_compile_id = null, $_smarty_display = false) {
		global $language;
		$_smarty_cache_id = $language . $_smarty_cache_id;
		$_smarty_compile_id = $language . $_smarty_compile_id;
		return parent::fetch($_smarty_tpl_file, $_smarty_cache_id, $_smarty_compile_id, $_smarty_display);
	}
}

function kill_script() {
	$remove = 'no';
	if (isset($_REQUEST['remove'])) {
		$remove = 'yes';
	}
	$removed = false;
	
	if (is_writable("installer/tiki-installer.php")) {
		/* first try to delete the file if requested */
		if ( ($remove == 'yes') && @unlink("installer/tiki-installer.php")) {
			$removed = true;
		}
		/* if it fails, then try to rename it */
		elseif (@rename("installer/tiki-installer.php", "installer/tiki-installer.done")) {
			$removed = true;
		}
		/* otherwise here's an attempt to change the content of the file to prevent execution */
		else {
			$fh = fopen('installer/tiki-installer.php', 'rb');
			$data = fread($fh, filesize('installer/tiki-installer.php'));
			fclose($fh);
			$data = preg_replace('/\/\/stopinstall:/', '', $data);
			$fh = fopen('installer/tiki-installer.php', 'wb');
			if (fwrite($fh, $data) > 0) {
				$removed = true;
			}
			fclose($fh);
		}
	}

	if ($removed == true) {
		header ('location: tiki-index.php');
	} else {
		// TODO: display this via translantable error msg template
		print "<html><head><title>Ooops !</title></head><body>
<h1 style='color: red'>Ooops !</h1>
<p>Tikiwiki installer failed to rename the <b>installer/tiki-installer.php</b> file.</p>
<p style='border: solid 1px red; margin: 0 10% 0 10%; text-align: center; width: 80%'>Leaving this file on a publicly accessible site is a <strong>security risk</strong>.</p>
<p>Please remove or rename the <b>installer/tiki-installer.php</b> from your Tiki installation folder 'manually' (e.g. using SSH or FTP).
<strong>Somebody else could be potentially able to wipe out your Tikiwiki database if you do not remove or rename this file !</strong></p>
<p><a href='index.php'>Proceed to your site</a> after you have removed or renamed <b>installer/tiki-installer.php</b>.</p>
<p style='text-align: right'>Thank you</p>
</body></html>";
	}
	exit();
}

function check_session_save_path() {
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

function get_webserver_uid() {
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

function error_and_exit() {
	global $errors, $docroot, $wwwgroup, $wwwuser;

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

        print "<html><body>\n<h2><IMG SRC=\"img/tiki/tikilogo.png\" ALT=\"\" BORDER=0><br /\>
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
		\$ cd $docroot
		\$ sh setup.sh

		The script will offer you options depending on your server configuration.

	b) <a href='tiki-install.php'>Execute the Tiki installer again</a> (Once you have executed these commands, this message will disappear!)


<hr>
If you have problems accessing a directory, check the open_basedir entry in
$PHP_CONFIG_FILE_PATH/php.ini or $httpd_conf.

<hr>

<a href='http://doc.tikiwiki.org/Installation' target='_blank'>Consult the tikiwiki.org installation guide</a> if you need more help or <a href='http://tikiwiki.org/tiki-forums.php' target='_blank'>visit the forums</a>

";
        }
	print "</pre></body></html>";
        exit;
}



function has_admin() {
        // Try to see if we have an admin account
	global $dbTiki;
	global $admin_acc;
        $query = "select hash from users_users where login='admin'";

        @$result = $dbTiki->Execute($query);

        if (!$result) {
                $admin_acc = 'n';
        } else {
                if ($result->numRows()) {
                        $res = $result->fetchRow();

                        if (isset($res['hash'])) {
                                $admin_acc = 'y';
                        } else {
                                $admin_acc = 'n';
                        }
                } else {
                        $admin_acc = 'n';
                }
        }
}

function get_admin_email( $dbTiki ) {
	global $installer;
	$query = "SELECT `email` FROM `users_users` WHERE `userId`=1";
	@$result = $installer->query($query);

	if ( $result && $res = $result->fetchRow() ) {
		return $res['email'];
	}

	return false;
}
function update_preferences( $dbTiki, &$prefs ) {
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

function fix_admin_account( $account ) {
	global $installer;

	$result = $installer->query( 'SELECT `id` FROM `users_groups` WHERE `groupName` = "Admins"' );
	if( ! $row = $result->fetchRow() ) {
		$installer->query( 'INSERT INTO `users_groups` (`groupName`) VALUES("Admins")' );
	}
	
	$installer->query( 'INSERT IGNORE INTO `users_grouppermissions` (`groupName`, `permName`) VALUES("Admins", "tiki_p_admin")' );

	$result = $installer->query( 'SELECT `userId` FROM `users_users` WHERE `login` = ?', array( $account ) );
	if( $row = $result->fetchRow() ) {
		$id = $row['userId'];
		$installer->query( 'INSERT IGNORE INTO `users_usergroups` (`userId`, `groupName`) VALUES(?, "Admins")', array( $id ) );
	}
}

// -----------------------------------------------------------------------------
// end of functions .. now starts the processing

// TODO: check that this is no longer in use (using lock-file now) and remove this and function
// After install, this should remove this script.
if (isset($_REQUEST['kill'])) {
	kill_script();
	exit();
}

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

include 'lib/cache/cachelib.php';
$cachelib->empty_full_cache();

$_SESSION["install-logged-$multi"] = 'y';

// Init smarty
global $tikidomain;
$smarty = new Smarty_Tikiwiki_Installer($tikidomain);
$smarty->load_filter('pre', 'tr');
$smarty->load_filter('output', 'trimwhitespace');
$smarty->assign('mid', 'tiki-install.tpl');
$smarty->assign('virt', isset($virt) ? $virt : null );
$smarty->assign('multi', isset($multi) ? $multi : null );
$smarty->assign('lang', $language);

// Try to set a longer execution time for the installer
@ini_set('max_execution_time', '0');
$max_execution_time = ini_get('max_execution_time');
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
$smarty->assign_by_ref('dbservers', $dbservers);

$errors = '';

// changed to path_translated 28/4/04 by damian
// for IIS compatibilty
if (empty($_SERVER['PATH_TRANSLATED'])) {
	// in PHP5, $_SERVER['PATH_TRANSLATED'] is no longer set
	// the following is hopefully a good workaround
	// nope, it wasn't - PHP5 doesn't allow pass-by-reference
	$includedFiles = get_included_files();
	$_SERVER['PATH_TRANSLATED'] = array_shift($includedFiles);
}
$docroot = dirname($_SERVER['PATH_TRANSLATED']);

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

if (!defined('ADODB_FORCE_NULLS')) { define('ADODB_FORCE_NULLS', 1); }
if (!defined('ADODB_ASSOC_CASE')) { define('ADODB_ASSOC_CASE', 2); }
if (!defined('ADODB_CASE_ASSOC')) { define('ADODB_CASE_ASSOC', 2); } // typo in adodb's driver for sybase? // so do we even need this without sybase? What's this?
include_once ('lib/adodb/adodb.inc.php');

include('lib/tikilib.php');

// Get list of available languages
$languages = TikiLib::list_languages(false, null, true);
$smarty->assign_by_ref("languages", $languages);

// next block checks if there is a local.php and if we can connect through this.
// sets $dbcon to false if there is no valid local.php
if (!file_exists($local)) {
	$dbcon = false;
	$smarty->assign('dbcon', 'n');
} else {
	// include the file to get the variables
	include $local;
	// In case of replication, ignore it during installer.
	unset( $shadow_dbs, $shadow_user, $shadow_pass, $shadow_host );
	if ($dbversion_tiki == '1.10') {
		$dbversion_tiki = '2.0';
	}

	if (!isset($db_tiki)) {
		// if no db is specified, use the first db that this php installation can handle
		$db_tiki = reset($dbservers);
		write_local_php($db_tiki, $host_tiki, $user_tiki, $pass_tiki, $dbs_tiki);
	}

	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

	// avoid errors in ADONewConnection() (wrong darabase driver etc...)
	if (!isset($dbservers[$db_tiki])) {
		$dbcon = false;
		$smarty->assign('dbcon', 'n');
	} else {
		$dbTiki = ADONewConnection($db_tiki);
		$db = new TikiDb_Adodb($dbTiki);
		$db->setErrorHandler(new InstallerDatabaseErrorHandler);
		TikiDb::set($db);

		if (! $test = $dbTiki->Connect($host_tiki, $user_tiki, $pass_tiki, $dbs_tiki)) {
			$dbcon = false;
			$smarty->assign('dbcon', 'n');
			$tikifeedback[] = array('num'=>1, 'mes'=>$dbTiki->ErrorMsg());
		} else {
			$dbcon = true;
			$installer = new Installer;
			$installer->setServerType($db_tiki);
			if (!isset($_REQUEST['reset'])) {
				$smarty->assign('dbcon', 'y');
				$smarty->assign('resetdb', 'n');
			} else {
				$smarty->assign('dbcon', 'y');
				$smarty->assign('resetdb', 'y');
			}
		}
	}
}

if ($dbcon) {
	has_admin();
}

if ($admin_acc == 'n') {
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
	$dbTiki = ADONewConnection($_REQUEST['db']);
	$db = new TikiDb_Adodb( $dbTiki );
	$db->setErrorHandler( new InstallerDatabaseErrorHandler );
	TikiDb::set( $db );

	// try to connect to database and write db information
	if (isset($_REQUEST['name']) && $_REQUEST['name']) {
		if (!@$dbTiki->Connect($_REQUEST['host'], $_REQUEST['user'], $_REQUEST['pass'], $_REQUEST['name'])) {
			$dbcon = false;
			$smarty->assign('dbcon', 'n');
			$tikifeedback[] = array('num'=>1, 'mes'=>$dbTiki->ErrorMsg());
		} else {
			$dbcon = true;
			$smarty->assign('dbcon', 'y');
			write_local_php($_REQUEST['db'], $_REQUEST['host'], $_REQUEST['user'], $_REQUEST['pass'], $_REQUEST['name']);
			include $local;
			// In case of replication, ignore it during installer.
			unset( $shadow_dbs, $shadow_user, $shadow_pass, $shadow_host );
			$installer = new Installer;
			$installer->setServerType($db_tiki);
		}
	} else {
		$dbcon = false;
		$smarty->assign('dbcon', 'n');
		$tikifeedback[] = array('num'=>1, 'mes'=>tra("No database name specified"));
	}
}

if ($dbcon) {
	$has_tiki_db = has_tiki_db();
	$smarty->assign('tikidb_created', $has_tiki_db);
	$oldPerms = $dbTiki->getOne('SELECT COUNT(*) FROM `users_permissions` WHERE `permDesc` = \'Can view categorized items\'');
	$smarty->assign('tikidb_oldPerms', $oldPerms);
	
	if ($install_step == '6' && $has_tiki_db) {
		update_preferences($dbTiki, $prefs);
		$smarty->assign('admin_email', get_admin_email($dbTiki));
	}
	$smarty->assign('tikidb_is20',  has_tiki_db_20());
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
		require_once 'lib/setup/compat.php';
		require_once 'lib/tikidate.php';
		$tikidate = new TikiDate();
	}

	if (isset($_REQUEST['update'])) {
		$installer->update();
		$smarty->assign('installer', $installer);
		$smarty->assign('dbdone', 'y');
		$install_type = 'update';
	}
	
	// Try to activate Apache htaccess file by renaming _htaccess into .htaccess
	// Do nothing (but warn the user to do it manually) if:
	//   - there is no  _htaccess file,
	//   - there is already an existing .htaccess (that is not necessarily the one that comes from TikiWiki),
	//   - the rename does not work (e.g. due to filesystem permissions)
	//
	if ( !file_exists('.htaccess') && ! @rename('_htaccess', '.htaccess') ) {
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
		touch( 'db/lock' );
	}
	
	global $userlib, $cachelib;
	if (session_id()) {
		session_destroy();
	}
	include_once 'tiki-setup.php';
	$cachelib->empty_full_cache();
	if ($install_type == 'scratch') {
		$u = 'tiki-change_password.php?user=admin&oldpass=admin';
	} else {
		$u = '';
	}
	$userlib->user_logout($user, false, $u);	// logs out then redirects to home page or $u
	exit;
}

// unused? (4.x)
//if( isset( $_GET['lockchange'] ) )
//{
//	touch( 'db/lock' );
//	header( 'Location: tiki-change_password.php?user=admin' );
//	exit;
//}

$smarty->assign_by_ref('tikifeedback', $tikifeedback);

$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

$email_test_tw = 'mailtest@tikiwiki.org';
$smarty->assign('email_test_tw', $email_test_tw);

//  Sytem requirements test. 
if ($install_step == '2') {

	if (isset($_REQUEST['perform_mail_test']) && $_REQUEST['perform_mail_test'] == 'y') {

		$email_test_to = $email_test_tw;
		$email_test_headers = '';
		$email_test_ready = true;

		if (!empty($_REQUEST['email_test_to'])) {
			$email_test_to =  $_REQUEST['email_test_to'];
			
			if ($_REQUEST['email_test_cc'] == '1') {
				$email_test_headers .= "Cc: $email_test_tw\n";
			}

			// check email address format
			include_once('lib/core/lib/Zend/Validate/EmailAddress.php');
			$validator = new Zend_Validate_EmailAddress();
			if (!$validator->isValid($email_test_to)) {
				$smarty->assign('email_test_err', tra('Email address not valid, test mail not sent'));
				$smarty->assign('perform_mail_test', 'n');
				$email_test_ready = false;
			}
		} else {	// no email supplied, check copy checkbox
			if ($_REQUEST['email_test_cc'] != '1') {
				$smarty->assign('email_test_err', tra('Email address empty and "copy" checkbox not set, test mail not sent'));
				$smarty->assign('perform_mail_test', 'n');
				$email_test_ready = false;
			}
		}
		$smarty->assign('email_test_to', $email_test_to);
		
		if ($email_test_ready) {	// so send the mail
			$email_test_headers .= 'From: noreply@tikiwiki.org' . "\n";	// needs a valid sender
			$email_test_headers .= 'Reply-to: '. $email_test_to . "\n";
			$email_test_headers .= "Content-type: text/plain; charset=utf-8\n";
			$email_test_headers .= 'X-Mailer: Tiki/'.$TWV->version.' - PHP/' . phpversion() . "\n";
			$email_test_subject = tra('Test mail from Tiki installer ').$TWV->version;
			$email_test_body = tra("Congratulations!\n\nYour server can send emails.\n\n");
			$email_test_body .= "\t".tra('Tiki version:').' '.$TWV->version . "\n";
			$email_test_body .= "\t".tra('PHP version:').' '.phpversion() . "\n";
			$email_test_body .= "\t".tra('Server:').' '.(empty($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_ADDR'] : $_SERVER['SERVER_NAME']) . "\n";
			$email_test_body .= "\t".tra('Sent:').' '.date(DATE_RFC822) . "\n";
			
			$sentmail = mail($email_test_to, $email_test_subject, $email_test_body, $email_test_headers);
			if($sentmail){
				$mail_test = 'y';
			} else {
				$mail_test = 'n';
			}
			$smarty->assign('mail_test', $mail_test);
			$smarty->assign('perform_mail_test', 'y');
			
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
		} else{
				$smarty->assign('sample_image', 'n');
		}

		} else {
		$gd_test = 'n'; }
	$smarty->assign('gd_test', $gd_test);
}

unset($TWV);

// write general settings
if ( isset($_REQUEST['general_settings']) && $_REQUEST['general_settings'] == 'y' ) {
	global $dbTiki;
	$switch_ssl_mode = ( isset($_REQUEST['feature_switch_ssl_mode']) && $_REQUEST['feature_switch_ssl_mode'] == 'on' ) ? 'y' : 'n';
	$show_stay_in_ssl_mode = ( isset($_REQUEST['feature_show_stay_in_ssl_mode']) && $_REQUEST['feature_show_stay_in_ssl_mode'] == 'on' ) ? 'y' : 'n';

	$installer->query("DELETE FROM `tiki_preferences` WHERE `name` IN ('browsertitle', 'sender_email', 'https_login', 'https_port', 'feature_switch_ssl_mode', 'feature_show_stay_in_ssl_mode', 'language')");

	$query = "INSERT INTO `tiki_preferences` (`name`, `value`) VALUES"
		. " ('browsertitle', '" . $_REQUEST['browsertitle'] . "'),"
		. " ('sender_email', '" . $_REQUEST['sender_email'] . "'),"
		. " ('https_login', '" . $_REQUEST['https_login'] . "'),"
		. " ('https_port', '" . $_REQUEST['https_port'] . "'),"
		. " ('feature_switch_ssl_mode', '$switch_ssl_mode'),"
		. " ('feature_show_stay_in_ssl_mode', '$show_stay_in_ssl_mode'),"
		. " ('language', '$language')";

	$installer->query($query);
	$installer->query("UPDATE `users_users` SET `email` = '".$_REQUEST['admin_email']."' WHERE `users_users`.`userId`=1");

	if( isset( $_REQUEST['admin_account'] ) && ! empty( $_REQUEST['admin_account'] ) ) {
		fix_admin_account( $_REQUEST['admin_account'] );
	}
}


include "lib/headerlib.php";
$headerlib->add_js("var tiki_cookie_jar=new Array();");
$headerlib->add_cssfile('styles/fivealive.css');
$headerlib->add_jsfile( 'lib/tiki-js.js' );
$headerlib->add_jsfile( 'lib/jquery/jquery.js' );
$headerlib->add_jsfile( 'lib/jquery_tiki/tiki-jquery.js' );
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
jqueryTiki.sheet = false;
jqueryTiki.carousel = false;
jqueryTiki.jqs5 = false;

jqueryTiki.effect = "";
jqueryTiki.effect_direction = "";
jqueryTiki.effect_speed = 400;
jqueryTiki.effect_tabs = "";
jqueryTiki.effect_tabs_direction = "";
jqueryTiki.effect_tabs_speed = 400;
';
$headerlib->add_js($js, 100);


$smarty->assign_by_ref('headerlib',$headerlib);

$smarty->assign('install_step', $install_step);
$smarty->assign('install_type', $install_type);
$smarty->assign_by_ref('prefs', $prefs);
$smarty->assign('detected_https',isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == 'on');

if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6') !== false) {
	$smarty->assign('ie6', true);
}

$mid_data = $smarty->fetch('tiki-install.tpl');
$smarty->assign('mid_data', $mid_data);

$smarty->display("tiki-install_screens.tpl");
