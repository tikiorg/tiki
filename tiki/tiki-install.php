<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-install.php,v 1.51 2004-01-31 14:10:43 mose Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

# $Header: /cvsroot/tikiwiki/tiki/tiki-install.php,v 1.51 2004-01-31 14:10:43 mose Exp $
error_reporting (E_ERROR);
session_start();

include_once("lib/init/initlib.php");

// Define and load Smarty components
define('SMARTY_DIR', "lib/smarty/");
require_once (SMARTY_DIR . 'Smarty.class.php');

$commands = array();

function process_sql_file($file,$db_tiki) {
	global $dbTiki;

	global $succcommands;
	global $failedcommands;
	global $smarty;
	if(!isset($succcommands)) {
	  $succcommands=array();
	  $failedcommands=array();
	}

	$command = '';
	$fp = fopen("db/$file", "r");

	while(!feof($fp)) {
		$command.= fread($fp,4096);
	}
	
	switch ($db_tiki) {
	  case "sybase":
	    $statements=split("(\r|\n)go(\r|\n)",$command);
	    break;
	  case "oci8":
	    $statements=preg_split("#(;\n)|(\n/\n)#",$command);
	    break;
	  default:
		$statements=preg_split("#(;\n)|(;\r\n)#",$command);
	    break;
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

	$smarty->assign_by_ref('succcommands', $succcommands);
	$smarty->assign_by_ref('failedcommands', $failedcommands);
}

function write_local_php($db_tiki,$host_tiki,$user_tiki,$pass_tiki,$dbs_tiki,$dbversion_tiki="1.9") {
	$db_tiki=addslashes($db_tiki);
	$host_tiki=addslashes($host_tiki);
	$user_tiki=addslashes($user_tiki);
	$pass_tiki=addslashes($pass_tiki);
	$dbs_tiki=addslashes($dbs_tiki);
	$fw = fopen('db/local.php', 'w');
	$filetowrite="<?php\n\$db_tiki=\"$db_tiki\";\n";
	$filetowrite.="\$dbversion_tiki=\"$dbversion_tiki\";\n";
	$filetowrite.="\$host_tiki=\"$host_tiki\";\n";
	$filetowrite.="\$user_tiki=\"$user_tiki\";\n";
	$filetowrite.="\$pass_tiki=\"$pass_tiki\";\n";
	$filetowrite.="\$dbs_tiki=\"$dbs_tiki\";\n";
	$filetowrite.="?>";
        fwrite($fw, $filetowrite);
	fclose ($fw);
}

function create_dirs(){
	$dirs=array(
		'backups',
		'db',
		'dump',
		'img/wiki',
		'img/wiki_up',
		'modules/cache',
		'temp',
		'temp/cache',
		'templates_c',
		'var',
		'var/log',
		'var/log/irc',
		'templates',
		'styles');

  if (file_exists('lib/Galaxia'))
    array_push($dirs, 'lib/Galaxia/processes');

	$ret = "";
  foreach ($dirs as $dir) {
		// Create directories as needed
		if (!is_dir($dir)) {
			@mkdir($dir,02775);
		}
		@chmod($dir,02775);
		// Check again and report problems
		if (!is_dir($dir)) {
			$ret .= "The directory '$docroot/$dir' does not exist.\n";
		} else if (!is_writeable($dir)) {
			$ret .= "The directory '$docroot/$dir' is not writeable.\n";
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

class Smarty_TikiWiki extends Smarty {

	function Smarty_TikiWiki() {
		$this->template_dir = "templates/";
		$this->compile_dir = "templates_c/";
		$this->config_dir = "configs/";
		$this->cache_dir = "cache/";
		$this->caching = false;
		$this->assign('app_name', 'TikiWiki');
		$this->plugins_dir = array(
			dirname(SMARTY_DIR)."/smarty_tiki",
			SMARTY_DIR."plugins"
		);
	//$this->debugging = true;
	//$this->debug_tpl = 'debug.tpl';
	}

	function fetch($_smarty_tpl_file, $_smarty_cache_id = null, $_smarty_compile_id = null, $_smarty_display = false) {
		global $language;
		$language = 'en';
		$_smarty_cache_id = $language . $_smarty_cache_id;
		$_smarty_compile_id = $language . $_smarty_compile_id;
		return parent::fetch($_smarty_tpl_file, $_smarty_cache_id, $_smarty_compile_id, $_smarty_display);
	}
}

function kill_script() {
	@$removed = rename('tiki-install.php', 'tiki-install.done');

	if ($removed == true) {
		header ('location: tiki-index.php');
	} else {
		print "
	<html><body>
	<p><font color='red'><b>Security Alert!</b><br />
	Tiki installer failed to rename <b>tiki-install.php</b>.  Please remove or rename the file, <b>tiki-install.php</b>, manually.  Others can potentially wipe out your Tiki database if you do not remove or rename this file.</b></font><br />
	<a href='index.php'>Proceed to your site</a> after you have removed or renamed <b>tiki-install.php</b></p>
	</body></html>
	";
	}

	die;
}

function check_session_save_path() {
	global $errors;
	if (ini_get('session.save_handler') == 'files') {
        	$save_path = ini_get('session.save_path');

        	if (!is_dir($save_path)) {
                	$errors .= "The directory '$save_path' does not exist or PHP is not allowed to access it (check open_basedir entry in php.ini).\n";
        	} else if (!is_writeable($save_path)) {
                	$errors .= "The directory '$save_path' is not writeable.\n";
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
	global $errors;
        $PHP_CONFIG_FILE_PATH = PHP_CONFIG_FILE_PATH;

        ob_start();
        phpinfo (INFO_MODULES);
        $httpd_conf = 'httpd.conf';

        if (preg_match('/Server Root<\/b><\/td><td\s+align="left">([^<]*)</', ob_get_contents(), $m)) {
                $httpd_conf = $m[1] . '/' . $httpd_conf;
        }

        ob_end_clean();

        print "<html><body>\n<h2><font color='red'>Tiki Installer cannot proceed:</font></h2>\n<pre>\n$errors";

        if (!isWindows()) {
                print "You may either create missing directories and chmod directories manually to 777, or run one of the sets of commands below.
<b><a href='tiki-install.php'>Execute the Tiki installer again</a></b> after you run the commands below.

If you cannot become root, and are NOT part of the group $wwwgroup:
        \$ bash
        \$ cd $docroot
        \$ chmod +x setup.sh
        \$ ./setup.sh yourlogin yourgroup 02777
        Tip: You can find your group using the command 'id'.

If you cannot become root, but are a member of the group $wwwgroup:
        \$ bash
        \$ cd $docroot
        \$ chmod +x setup.sh
        \$ ./setup.sh mylogin $wwwgroup</i>

If you can become root:
        \$ bash
        \$ cd $docroot
        \$ chmod +x setup.sh
        \$ su -c './setup.sh $wwwuser'

If you have problems accessing a directory, check the open_basedir entry in 
$PHP_CONFIG_FILE_PATH/php.ini or $httpd_conf.

Once you have executed these commands, this message will disappear!

Note: If you cannot become root, you will not be able to delete certain
files created by apache, and will need to ask your system administrator
to delete them for you if needed.

<a href='http://tikiwiki.org/InstallTiki' target='_blank'>Consult the tikiwiki.org installation guide</a> if you need more help.

<b><a href='tiki-install.php'>Execute the Tiki installer again</a></b> if you have completed the steps above.";
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
	if ($db_tiki == "mysql") {
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
        	if (preg_match('#1\..to1\..#',$file)) {
                	$files[] = $file;
        	}
	}

	closedir ($h);
	sort($files);
	reset($files);
	$smarty->assign('files', $files);
}

function check_password() {
	global $logged;
	global $dbTiki;

        $logged = 'n';

        $pass = $_REQUEST['pass'];

        // first verify that the user exists
        $query = "select email from users_users where lower(login) = 'admin'";
        $result = $dbTiki->Execute($query);

        if (!$result->numRows()) {
                $logged = 'n';
        } else {
                $res = $result->fetchRow();

                $hash = md5('admin' . $pass . $res['email']);
                $hash2 = md5($pass);
                // next verify the password with 2 hashes methods, the old one (passà)) and the new one (login.pass;email)
                $query = "select login from users_users where lower(login) = 'admin' and hash in ('$hash', '$hash2')";
                $result = $dbTiki->Execute($query);

                if ($result->numRows()) {
                        $logged = 'y';

                        $_SESSION['install-logged'] = 'y';
                }
        }
}


// After install. This should remove this script.
if (isset($_REQUEST['kill'])) {
	kill_script();
	die;
}


// Init smarty
$smarty = new Smarty_TikiWiki();
//$smarty->load_filter('pre', 'tr');
$smarty->load_filter('output', 'trimwhitespace');
$smarty->assign('style', 'default.css');
$smarty->assign('mid', 'tiki-install.tpl');

// Tiki Database schema version
$tiki_version = '1.8';
$smarty->assign('tiki_version', $tiki_version);

// Available DB Servers
$dbservers = array('MySQL 3.x', 'MySQL 4.x', 'PostgeSQL 7.2+', 'Oracle 8i', 'Oracle 9i', 'Sybase/MSSQL','SQLLite');

$dbtodsn = array(
	"MySQL 3.x" => "mysql",
	"MySQL 4.x" => "mysql",
	"PostgeSQL 7.2+" => "pgsql",
	"Oracle 8i" => "oci8",
	"Oracle 9i" => "oci8",
	"Sybase/MSSQL" => "sybase",
	"SQLLite" => "sqlite"
);

$smarty->assign_by_ref('dbservers', $dbservers);

$errors = '';
$docroot = dirname($_SERVER['SCRIPT_FILENAME']);

check_session_save_path();

get_webserver_uid();

$errors .= create_dirs();

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
if (!file_exists('db/local.php')) {
	$dbcon = false;
	$smarty->assign('dbcon', 'n');
} else {
	// include the file to get the variables
	include ('db/local.php');

	if (!isset($db_tiki)) {
		//upgrade from 1.7.X
		$db_tiki="mysql";
		write_local_php($db_tiki,$host_tiki,$user_tiki,$pass_tiki,$dbs_tiki);
	}

	if ($db_tiki == 'sybase') {
	        // avoid database change messages
		ini_set('sybct.min_server_severity', '11');
	}
	
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	
	// avoid errors in ADONewConnection() (wrong darabase driver etc...)
	if(array_search($db_tiki,$dbtodsn)==FALSE) {
		$dbcon = false;
		$smarty->assign('dbcon', 'n');
	} else {
		$dbTiki = &ADONewConnection($db_tiki);

		if (!$dbTiki->Connect($host_tiki, $user_tiki, $pass_tiki, $dbs_tiki)) {
			$dbcon = false;
			$smarty->assign('dbcon', 'n');
		} else {
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


// next lines checks if there is a admin account in the db
$admin_acc = 'n';

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

if ((!$dbcon or 
	($_REQUEST['resetdb']=='y' && 
		($admin_acc=='n' || (isset($_SESSION['install-logged']) && $_SESSION['install-logged']=='y'))
	)
     ) && isset($_REQUEST['dbinfo'])) {
	write_local_php($dbtodsn[$_REQUEST['db']],$_REQUEST['host'],
		$_REQUEST['user'],$_REQUEST['pass'],$_REQUEST['name']);
	include ('db/local.php');
	$dbTiki = &ADONewConnection($db_tiki);

	if (!$dbTiki->Connect($host_tiki, $user_tiki, $pass_tiki, $dbs_tiki)) {
		$dbcon = false;

		$smarty->assign('dbcon', 'n');
	} else {
		$dbcon = true;

		$smarty->assign('dbcon', 'y');
	}
}

if (isset($_REQUEST['restart'])) {
	$_SESSION['install-logged'] = '';
}

// Tiki Package feature
// Written by dheltzel 12/4/2003 - status: experimental
include ('db/local.php');
include_once ('lib/pclzip.lib.php');

if (isset($_REQUEST['packages']))
	$smarty->assign('packages', 'y');

// This is the directory where it looks for the package files. If no .zip files are in this dir, the Installer will be disabled.
$package_dir = "packages/";
// This is the suffix it uses to look for an install SQL script
$pkg_sql_install_suf = "_install.sql";
// This is the suffix it uses to look for a remove SQL script
$pkg_sql_remove_suf = "_remove.sql";

// This is used to install files into the Tiki file structure from a zip file
/* Disabled during 1.8 release preparation. Needs check if admin is logged in!
if (isset($_REQUEST['install_pkg'])) {
	$archive = new PclZip($package_dir.$_REQUEST['pkgs']);

	if ($archive->extract() == 0) {
		die("Error : ".$archive->errorInfo(true));
	}
	else {
		if (isset($_REQUEST['runScript'])) {
			$pkg_sql_install_file = basename($_REQUEST['pkgs'],".zip").$pkg_sql_install_suf;
			if (is_file("db/".$pkg_sql_install_file)) {
				print "Running ".$pkg_sql_install_file."<br />";
				process_sql_file ($pkg_sql_install_file,$db_tiki);
			}
		}
		print "The application in <b>".$_REQUEST['pkgs']."</b> was installed successfully";
	}
	$smarty->assign('packages', 'y');
}

// This is used to remove files that were installed into the Tiki file structure by the install_pkg prcess
if (isset($_REQUEST['remove_pkg'])) {
	$archive = new PclZip($package_dir.$_REQUEST['pkgs']);
	if (isset($_REQUEST['runScript'])) {
		$pkg_sql_remove_file = basename($_REQUEST['pkgs'],".zip").$pkg_sql_remove_suf;
		if (is_file("db/".$pkg_sql_remove_file)) {
			print "Running ".$pkg_sql_remove_file."<br />";
			process_sql_file ($pkg_sql_remove_file,$db_tiki);
		}
	}

	// Read Archive contents
	$ziplist = $archive->listContent();
	if ($ziplist) {
		for ($i = 0; $i < sizeof($ziplist); $i++) {
			$file = $ziplist["$i"]["filename"];
			unlink($file);
			print $file." removed<br />";
		}
	}
	else {
		print "Nothing to remove for ".$_REQUEST['pkgs']."<br />";
	}
	$smarty->assign('packages', 'y');
}
*/

//Load Profiles
load_profiles();

//Load SQL scripts
load_sql_scripts();

/* disabled due to 1.8 release preparation
//Load packages
// the packages are only mysql-safe at this time, also they only show up if a zip file exists in $package_dir
$smarty->assign('pkg_available', 'n');
if ($db_tiki == "mysql") {
	$pkgs = array();
	$h = opendir($package_dir);

	while ($file = readdir($h)) {
		if (strstr($file, '.zip')) {
			// Assign the filename of the pkgs to the name field
			$pkg1 = array("name" => $file);
			$pkg1["desc"] = basename($file,".zip");
			// Assign the record to the pkgs array
			$pkgs[] = $pkg1;
			$smarty->assign('pkg_available', 'y');
		}
	}

	closedir ($h);
	sort($pkgs);
}
$smarty->assign('pkgs', $pkgs);
*/

// If no admin account then allow the creation of an admin account
if ($admin_acc == 'n' && isset($_REQUEST['createadmin'])) {
	if ($_REQUEST['pass1'] == $_REQUEST['pass2']) {
		$hash = md5($_REQUEST['pass1']);
		//$query = "delete from users_users where login='admin'";
		//$dbTiki->Execute($query);
		$pass1 = addslashes($_REQUEST['pass1']);
		$query = "insert into users_users(login,password,hash) 
    values('admin','$pass1','$hash')";
		$dbTiki->Execute($query);
		$admin_acc = 'y';
	}
}

$smarty->assign('admin_acc', $admin_acc);

// Since we do have an admin account the user must login to 
// use the install script
if (isset($_REQUEST['login'])) {
	check_password();
}

// If no admin account then we are logged
if ($admin_acc=='n') {
	$logged = 'y';

	$_SESSION['install-logged'] = 'y';
}

$smarty->assign('dbdone', 'n');
$smarty->assign('logged', $logged);

if (isset($_SESSION['install-logged']) && $_SESSION['install-logged'] == 'y') {
	$smarty->assign('logged', 'y');

	if (isset($_REQUEST['scratch'])) {
		process_sql_file ('tiki-' . $dbversion_tiki . "-" . $db_tiki . '.sql',$db_tiki);

		$smarty->assign('dbdone', 'y'); if (isset($_REQUEST['profile'])) {
			process_sql_file ('profiles/' . $_REQUEST['profile'],$db_tiki);
			//$profile = $_REQUEST['profile'];
			//print "Profile: $profile";
		}
	}

	if (isset($_REQUEST['update'])) {
		process_sql_file ($_REQUEST['file'],$db_tiki);

		$smarty->assign('dbdone', 'y');
	}
}

$smarty->display("tiki.tpl");

//print "<hr>";
//setup_help();

?>
