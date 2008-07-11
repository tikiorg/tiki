<?php

// $Id$

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// To (re-)enable this script the file has to be named tiki-installer.php and the following four lines
// must start with two '/' and 'stopinstall:'. (Make sure there are no spaces inbetween // and stopinstall: !)

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

error_reporting (E_ALL);

session_start();

include_once("lib/init/initlib.php");

// Define and load Smarty components
define('SMARTY_DIR', "lib/smarty/libs/");
require_once ( 'lib/smarty/libs/Smarty.class.php');

$commands = array();
ini_set('magic_quotes_runtime',0);

if (!empty($_REQUEST['lang'])) {
	$language = $prefs['language'] = $_REQUEST['lang'];
} else {
	$language = $prefs['language'] = 'en';
}
include_once('lib/init/tra.php');

function list_tables( $dbTiki )
{
	static $list = array();
	if( $list )
		return $list;

	$result = $dbTiki->Execute( "show tables" );
	while( $row = $result->fetchRow() )
		$list[] = reset( $row );

	return $list;
}

function has_tiki_db( $dbTiki )
{
	return in_array( 'users_users', list_tables( $dbTiki ) );
}

function has_tiki_db_20( $dbTiki )
{
	return in_array( 'tiki_pages_translation_bits', list_tables( $dbTiki ) );
}

function process_sql_file($file,$db_tiki) {
	global $dbTiki;
	if ( ! is_object($dbTiki) ) return false;

	global $succcommands;
	global $failedcommands;
	global $smarty;
	if(!isset($succcommands)) {
	  $succcommands=array();
	  $failedcommands=array();
	}

	$command = '';
	if(!$fp = fopen("db/$file", "r")) {
		print('Fatal: Cannot open db/'.$file);
		exit(1);
	}

	while(!feof($fp)) {
		$command.= fread($fp,4096);
	}

	switch ( $db_tiki ) {
	  case 'sybase': $statements = split("(\r|\n)go(\r|\n)", $command); break;
          case 'mssql': $statements = split("(\r|\n)go(\r|\n)", $command); break;
	  case 'oci8': $statements = preg_split("#(;\s*\n)|(\n/\n)#", $command); break;
	  default: $statements = preg_split("#(;\s*\n)|(;\s*\r\n)#", $command); break;
	}
	$prestmt="";
	$do_exec=true;
	foreach ($statements as $statement) {
		//echo "executing $statement </br>";
			if (trim($statement)) {
				switch ($db_tiki) {
				case "oci8":
					// we have to preserve the ";" in sqlplus programs (triggers)
					if (preg_match("/BEGIN/",$statement)) {
						$prestmt=$statement.";";
						$do_exec=false;
					}
					if (preg_match("/END/",$statement)) {
						$statement=$prestmt."\n".$statement.";";
						$do_exec=true;
					}
					if($do_exec) $result = $dbTiki->Execute($statement);
					break;
				default:
					$result = $dbTiki->Execute($statement);
					break;
			}

			if (!$result) {
				$failedcommands[]= "Command: ".$statement."\nMessage: ".$dbTiki->ErrorMsg()."\n\n";
				//trigger_error("DB error:  " . $dbTiki->ErrorMsg(). " in query:<br /><pre>" . $command . "<pre/><br />", E_USER_WARNING);
				// Do not die at the moment. We need some better error checking here
				//die;
			} else {
				$succcommands[]=$statement;
			}
		}
	}
	$dbTiki->Execute("update `tiki_preferences` set `value`=`value`+1 where `name`='lastUpdatePrefs'");

	$smarty->assign_by_ref('succcommands', $succcommands);
	$smarty->assign_by_ref('failedcommands', $failedcommands);
}

function write_local_php($dbb_tiki,$host_tiki,$user_tiki,$pass_tiki,$dbs_tiki,$dbversion_tiki="2.0") {
	global $local;
	global $db_tiki;
	if ($dbs_tiki and $user_tiki) {
		$db_tiki=addslashes($dbb_tiki);
		$host_tiki=addslashes($host_tiki);
		$user_tiki=addslashes($user_tiki);
		$pass_tiki=addslashes($pass_tiki);
		$dbs_tiki=addslashes($dbs_tiki);
		$fw = fopen($local, 'w');
		$filetowrite="<?php\n\$db_tiki='".$db_tiki."';\n";
		$filetowrite.="\$dbversion_tiki='".$dbversion_tiki."';\n";
		$filetowrite.="\$host_tiki='".$host_tiki."';\n";
		$filetowrite.="\$user_tiki='".$user_tiki."';\n";
		$filetowrite.="\$pass_tiki='".$pass_tiki."';\n";
		$filetowrite.="\$dbs_tiki='".$dbs_tiki."';\n";
		$filetowrite.="?>";
		fwrite($fw, $filetowrite);
		fclose ($fw);
	}
}

function create_dirs($domain=''){
	global $docroot;
	$dirs=array(
		'backups',
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

  if (file_exists('lib/Galaxia'))
    array_push($dirs, 'lib/Galaxia/processes');

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
		} else if (!is_writeable($dir)) {
			@chmod($dir,02777);
			if (!is_writeable($dir)) {
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

class Smarty_Tikiwiki extends Smarty {

	function Smarty_Tikiwiki() {
		$this->template_dir = "templates/";
		$this->compile_dir = "templates_c/";
		$this->config_dir = "configs/";
		$this->cache_dir = "cache/";
		$this->caching = false;
		$this->assign('app_name', 'Tikiwiki');
		$this->plugins_dir = array(
			dirname(dirname(SMARTY_DIR))."/smarty_tiki",
			SMARTY_DIR."plugins"
		);
                // we cannot use subdirs in safe mode
                if(ini_get('safe_mode')) {
                        $this->use_sub_dirs = false;
                }
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
	/*Header ('Location: tiki-install_disable.php');
	die;*/

	$remove = 'no';
	if (isset($_REQUEST['remove'])) $remove = 'yes';
	$removed = false;
	$fh = fopen('installer/tiki-installer.php', 'rb');
	$data = fread($fh, filesize('installer/tiki-installer.php'));
	fclose($fh);

	if (is_writable("installer/tiki-installer.php")) {
		/* first try to delete the file if requested */
		if ($remove=='yes' && @unlink("installer/tiki-installer.php")) {
			$removed = true;
		}
		/* if it fails, then try to rename it */
		else if (@rename("installer/tiki-installer.php","installer/tiki-installer.done")) {
			$removed = true;
		}
		/* otherwise here's an attempt to change the content of the file to prevent execution */
		else {
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
	} else { // TODO: display this via translantable error msg template ?
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
	die;
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
        		} else if (!is_writeable($save_path)) {
                		$errors .= "The directory '$save_path' is not writeable.\n";
        		}
		}

        	if ($errors) {
                	$save_path = TikiInit::tempdir();

                	if (is_dir($save_path) && is_writeable($save_path)) {
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
		\$ bash
		\$ cd $docroot
		\$ chmod +x setup.sh
		\$ ./setup.sh

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

function load_profiles() {
	// the profiles are only mysql-safe at this time, so make other DB's only show the default, which is empty
	global $db_tiki;
	global $smarty;
	if ($db_tiki == 'mysql' || $db_tiki == 'mysqli') {
        	$profiles = array();
        	$h = opendir('db/profiles/');

        	while ($file = readdir($h)) {
                	if (substr($file,-4,4) == '.prf') {
                        	// Assign the filename of the profile to the name field
                        	$prof1 = array("name" => $file);
                        	// Open the profile and pull out the description from the first line
                        	$fp = fopen("db/profiles/$file", "r");
                        	$desc = substr(fgets($fp,40),2);
                        	fclose($fp);
                        	$prof1["desc"] = $desc;
                        	// Assign the record to the profile array
                        	$profiles[] = $prof1;
                	}
        	}

        	closedir ($h);
        	sort($profiles);
	} else {
        	$prof1 = array("name" => "_default.prf");
        	$prof1["desc"] = "Default installation profile";
        	$profiles[] = $prof1;
	}
	$smarty->assign('profiles', $profiles);
}



function load_sql_scripts() {
	global $smarty;
	global $dbversion_tiki;
	$files = array();
	$h = opendir('db/');
	//echo $dbversion_tiki . "---";

	while ($file = readdir($h)) {
        	if (preg_match('#1\..*to.*\.sql$#',$file) || preg_match('#secdb#',$file)) {
                	$files[] = $file;
        	}
	}

	closedir ($h);
	sort($files);
	reset($files);
	$smarty->assign('files', $files);
}

// from PHP manual (ini-get function example)
function return_bytes( $val ) {
	$val = trim($val);
	$last = strtolower($val{strlen($val)-1});
	switch ( $last ) {
		// The 'G' modifier is available since PHP 5.1.0
		case 'g': $val *= 1024;
		case 'm': $val *= 1024;
		case 'k': $val *= 1024;
	}
	return $val;
}

include 'lib/cache/cachelib.php';
$cachelib->empty_full_cache();

// -----------------------------------------------------------------------------
// end of functions .. now starts the processing

// After install. This should remove this script.
if (isset($_REQUEST['kill'])) {
	kill_script();
	die;
}

if (is_file('db/virtuals.inc')) {
	$virtuals = array_map('trim',file('db/virtuals.inc'));
	foreach ($virtuals as $v) {
		if ($v) {
			if (is_file("db/$v/local.php") and is_readable("db/$v/local.php")) {
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

if ($virtuals and isset($_REQUEST['multi']) and in_array($_REQUEST['multi'],$virtuals)) {
	$local = 'db/'.$_REQUEST['multi'].'/local.php';
	$multi = $_REQUEST['multi'];
} else {
	$local = 'db/local.php';
	$multi = '';
}

$_SESSION["install-logged-$multi"] = 'y';

// Init smarty
$smarty = new Smarty_Tikiwiki();
$smarty->load_filter('pre', 'tr');
$smarty->load_filter('output', 'trimwhitespace');
$smarty->assign('mid', 'tiki-install.tpl');
$smarty->assign('style', 'tikineat.css');
$smarty->assign('virt',$virt);
$smarty->assign('multi', $multi);
if ($language != 'en')
	$smarty->assign('lang', $language);

// Tiki Database schema version
$tiki_version = '2.0';
$smarty->assign('tiki_version', $tiki_version);

// Available DB Servers
$dbservers = array();
if ( function_exists('mysqli_connect') ) $dbservers['mysqli'] = 'MySQL Improved (mysqli). Requires MySQL 4.1+';
if ( function_exists('mysql_connect') ) $dbservers['mysql'] = 'MySQL classic (mysql)';
if ( function_exists('pg_connect') ) $dbservers['pgsql'] = 'PostgeSQL 7.2+';
if ( function_exists('oci_connect') ) $dbservers['oci8'] = 'Oracle';
if ( function_exists('sybase_connect') ) $dbservers['sybase'] = 'Sybase';
if ( function_exists('sqlite_open') ) $dbservers['sqlite'] = 'SQLLite';
if ( function_exists('mssql_connect') ) $dbservers['mssql'] = 'MSSQL';
$smarty->assign_by_ref('dbservers', $dbservers);

$errors = '';

// changed to path_translated 28/4/04 by damian
// for IIS compatibilty
if (empty($_SERVER['PATH_TRANSLATED'])) {
	// in PHP5, $_SERVER['PATH_TRANSLATED'] is no longer set
	// the following is hopefully a good workaround
	// nope, it wasn't - PHP5 doesn't allow pass-by-reference
	$myFooVarForIncludeFiles = get_included_files();
	$_SERVER['PATH_TRANSLATED'] = array_shift($myFooVarForIncludeFiles);
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
TikiInit::prependIncludePath('lib/adodb');
TikiInit::prependIncludePath('lib/pear');


define('ADODB_FORCE_NULLS', 1);
define('ADODB_ASSOC_CASE', 2);
define('ADODB_CASE_ASSOC', 2); // typo in adodb's driver for sybase?
include_once ('adodb.inc.php');
//include_once ('adodb-pear.inc.php'); //really needed?


// next block checks if there is a local.php and if we can connect through this.
// sets $dbcon to false if there is no valid local.php
if (!file_exists($local)) {
	$dbcon = false;
	$smarty->assign('dbcon', 'n');
} else {
	// include the file to get the variables
	include ($local);

	if (!isset($db_tiki)) {
		//upgrade from 1.7.X
		//$db_tiki="mysql";
		//upgrade from 2.0 : if no db is specified, use the first db that this php installation can handle
		$db_tiki = reset($dbservers);
		write_local_php($db_tiki,$host_tiki,$user_tiki,$pass_tiki,$dbs_tiki);
	}

	if ($db_tiki == 'sybase') {
	        // avoid database change messages
		ini_set('sybct.min_server_severity', '11');
	}

	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

	// avoid errors in ADONewConnection() (wrong darabase driver etc...)
	if( ! isset($dbservers[$db_tiki]) ) {
		$dbcon = false;
		$smarty->assign('dbcon', 'n');
	} else {
		$dbTiki = &ADONewConnection($db_tiki);

		if (!$dbTiki->Connect($host_tiki, $user_tiki, $pass_tiki, $dbs_tiki)) {
			$dbcon = false;
			$smarty->assign('dbcon', 'n');
			$tikifeedback[] = array('num'=>1,'mes'=>$dbTiki->ErrorMsg());
		} else {
			$smarty->assign( 'tikidb_created',  has_tiki_db( $dbTiki ) );
			$smarty->assign( 'tikidb_is20',  has_tiki_db_20( $dbTiki ) );

			$dbcon = true;
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

if ($admin_acc=='n') {
        $smarty->assign('noadmin', 'y');
} else {
        $smarty->assign('noadmin', 'n');
}


// We won't update database info unless we can't connect to the
// database.
// we won't reset the db connection if there is a admin account set
// and the admin is not logged
//debugging:
/*
if ($dbcon) echo "dbcon true <br>";
if ($_REQUEST['resetdb']=='y') echo '$_REQUEST[resetdb]==y<br>';
if (isset($_REQUEST['dbinfo'])) echo '$_REQUEST[dbinfo] is set<br>';
if (isset($_SESSION['install-logged'])) {echo '$_SESSION[install-logged] is set<br>';
 if ($_SESSION['install-logged']=='y') echo '$_SESSION[install-logged]==y<br>';
}
echo "admin_acc=$admin_acc<br>";
*/
if ((!$dbcon or (isset($_REQUEST['resetdb']) and $_REQUEST['resetdb']=='y' &&
		($admin_acc=='n' || (isset($_SESSION["install-logged-$multi"]) && $_SESSION["install-logged-$multi"]=='y'))
	)) && isset($_REQUEST['dbinfo'])) {

	$dbTiki = &ADONewConnection($_REQUEST['db']);

	if (isset($_REQUEST['name']) and $_REQUEST['name']) {
		if (!@$dbTiki->Connect($_REQUEST['host'], $_REQUEST['user'], $_REQUEST['pass'], $_REQUEST['name'])) {
			$dbcon = false;
			$smarty->assign('dbcon', 'n');
			$tikifeedback[] = array('num'=>1,'mes'=>$dbTiki->ErrorMsg());
		} else {
			$dbcon = true;
			$smarty->assign('dbcon', 'y');
			$smarty->assign( 'tikidb_created',  has_tiki_db( $dbTiki ) );
			$smarty->assign( 'tikidb_is20',  has_tiki_db_20( $dbTiki ) );
			write_local_php($_REQUEST['db'], $_REQUEST['host'], $_REQUEST['user'], $_REQUEST['pass'], $_REQUEST['name']);
		}
	}
}

if ( isset($_REQUEST['restart']) ) $_SESSION["install-logged-$multi"] = '';

//Load Profiles
load_profiles();

//Load SQL scripts
load_sql_scripts();

$smarty->assign('admin_acc', $admin_acc);

// If no admin account then we are logged
if ( $admin_acc == 'n' ) $_SESSION["install-logged-$multi"] = 'y';

$smarty->assign('dbdone', 'n');
$smarty->assign('logged', $logged);

if ( is_object($dbTiki) && isset($_SESSION["install-logged-$multi"]) && $_SESSION["install-logged-$multi"] == 'y' ) {
	$smarty->assign('logged', 'y');

	if ( isset($_REQUEST['scratch']) ) {
		process_sql_file('tiki-'.$dbversion_tiki.'-'.$db_tiki.'.sql', $db_tiki);
		$smarty->assign('dbdone', 'y');
		if ( isset($_REQUEST['profile']) ) process_sql_file('profiles/'.$_REQUEST['profile'], $db_tiki);
		$_SESSION[$cookie_name] = 'admin';
	}

	if ( isset($_REQUEST['update']) ) {
		$is19 = ! has_tiki_db_20($dbTiki);
		process_sql_file($_REQUEST['file'], $db_tiki);

		if( $_REQUEST['file'] == 'tiki_1.9to2.0.sql' && $is19 ) {
			$dbTiki->Execute( "INSERT INTO users_grouppermissions (groupName, permName, value) SELECT groupName, 'tiki_p_edit_categorized', '' FROM users_grouppermissions WHERE permName = 'tiki_p_view_categories'" );
			$dbTiki->Execute( "INSERT INTO users_grouppermissions (groupName, permName, value) SELECT groupName, 'tiki_p_view_categorized', '' FROM users_grouppermissions WHERE permName = 'tiki_p_view_categories'" );
			$dbTiki->Execute( "INSERT INTO users_objectpermissions (groupName, permName, objectType, objectId) SELECT groupName, 'tiki_p_edit_categorized', objectType, objectId FROM users_objectpermissions WHERE permName = 'tiki_p_view_categories'" );
			$dbTiki->Execute( "INSERT INTO users_objectpermissions (groupName, permName, objectType, objectId) SELECT groupName, 'tiki_p_view_categorized', objectType, objectId FROM users_objectpermissions WHERE permName = 'tiki_p_view_categories'" );
		}
		$smarty->assign('dbdone', 'y');
	}
}
$smarty->assign_by_ref('tikifeedback', $tikifeedback);

$php_memory_limit = return_bytes(ini_get('memory_limit'));
$smarty->assign('php_memory_limit', intval($php_memory_limit));
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

include "lib/headerlib.php";
$headerlib->add_cssfile('styles/tikineat.css');
$smarty->assign_by_ref('headerlib',$headerlib);

$mid_data = $smarty->fetch('tiki-install.tpl');
$smarty->assign('mid_data', $mid_data);

$smarty->display("tiki-print.tpl");

?>
