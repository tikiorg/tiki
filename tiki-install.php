<?php # $Header: /cvsroot/tikiwiki/tiki/tiki-install.php,v 1.10 2003-08-06 22:47:04 franck Exp $

session_start();
// Define and load Smarty components
define('SMARTY_DIR',"Smarty/");
require_once(SMARTY_DIR.'Smarty.class.php');

$commands=Array();
function process_sql_file($file) {
  global $dbTiki;
  global $commands;
  global $smarty;
  $command = '';
  $fp = fopen("db/$file","r");
  while($line = fgets($fp,1024)) {
    if(substr($line,0,1)!='#') {
      $command.=$line;
    }
    if(strstr($command,';')) {
      $command = trim($command);
      $commands[] = $command;
      $result = $dbTiki->query($command);
      if(DB::isError($result)) {
      	trigger_error("DB error:  ".$result->getMessage()." in query:<br/>".$command."<br/>",E_USER_WARNING);
	// Do not die at the moment. Wen need some better error checking here
      	//die;
      }
      $command = '';
    }
  }
  $smarty->assign('commands',$commands);
}

	function isWindows() {
		static $windows;
		
		if (!isset($windows)) {
			$windows = substr(PHP_OS, 0, 3) == 'WIN';
		}
		
		return $windows;
	}

class Smarty_Sterling extends Smarty {
  function Smarty_Sterling() {
    $this->template_dir = "templates/";
    $this->compile_dir = "templates_c/";
    $this->config_dir = "configs/";
    $this->cache_dir = "cache/";
    $this->caching = false;
    $this->assign('app_name','Sterling');
    //$this->debugging = true;
    //$this->debug_tpl = 'debug.tpl';
  }
  
  function _smarty_include($_smarty_include_tpl_file, $_smarty_include_vars)  
  {
    global $style;
    global $style_base;

    if(isset($style)&&isset($style_base)) {
      if(file_exists("templates/styles/$style_base/$_smarty_include_tpl_file")) {
        $_smarty_include_tpl_file="styles/$style_base/$_smarty_include_tpl_file";
      } 
      
    }

    return parent::_smarty_include($_smarty_include_tpl_file, $smarty_include_vars);
  }

  function fetch($_smarty_tpl_file, $_smarty_cache_id = null, $_smarty_compile_id = null, $_smarty_display = false) {
    global $language;
    global $style;
    global $style_base;
    
    // default language to English
    $language = 'en';
    
    if(isset($style)&&isset($style_base)) {
      if(file_exists("templates/styles/$style_base/$_smarty_tpl_file")) {
        $_smarty_tpl_file="styles/$style_base/$_smarty_tpl_file";
      }
    }
    
    $_smarty_cache_id = $language.$_smarty_cache_id;
    $_smarty_compile_id = $language.$_smarty_compile_id;
    return parent::fetch($_smarty_tpl_file, $_smarty_cache_id, $_smarty_compile_id, $_smarty_display);
  }
 
}	

if(isset($_REQUEST['kill'])) {
  @$removed = rename('tiki-install.php','tiki-install.done');
  if ($removed == true) {
	header('location: tiki-index.php');
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

$smarty = new Smarty_Sterling();
$smarty->load_filter('pre','tr');
$smarty->load_filter('output','trimwhitespace');
$smarty->assign('style','default.css');
$smarty->assign('mid','tiki-install.tpl');

// Avalible DB Servers

$dbservers=array("Mysql 3.X" => "mysql3",
            "Mysql 4.X" => "mysql4",
	    "PostgeSQL 7.2 or higher" => "pgsql72",
	    "Oracle 8i" => "oci8",
	    "Oracle 9i" => "oci9");

$dbtodsn=array("mysql3" => "mysql",
          "mysql4" => "mysql",
	  "pgsql72" => "pgsql",
	  "oci8" => "oci8",
	  "oci9" => "oci8");

$smarty->assign_by_ref('dbservers',$dbservers);
		$errors = '';

		$docroot = dirname($_SERVER['SCRIPT_FILENAME']);

		if (ini_get('session.save_handler') == 'files') {
			$save_path = ini_get('session.save_path');

			if (!is_dir($save_path)) {
				$errors .= "The directory '$save_path' does not exist or PHP is not allowed to access it (check open_basedir entry in php.ini).\n";
			} else 
			if (!is_writeable($save_path)) {
				$errors .= "The directory '$save_path' is not writeable.\n";
			}

			if ($errors) {
				$save_path = TikiSetup::tempdir();
				if (is_dir($save_path) && is_writeable($save_path)) {
					ini_set('session.save_path', $save_path);
					$errors = '';
				}
			}
		}

		$wwwuser	= '';
		$wwwgroup	= '';
		
		if (isWindows()) {
			$wwwuser	= 'SYSTEM';
			$wwwgroup	= 'SYSTEM';
		}

		if (function_exists('posix_getuid')) {
			$user		= @posix_getpwuid(@posix_getuid());
			$group		= @posix_getpwuid(@posix_getgid());
			$wwwuser	= $user ? $user['name'] : false;
			$wwwgroup	= $group ? $group['name'] : false;
		}

		if (!$wwwuser) {
			$wwwuser = 'nobody (or the user account the web server is running under)';
		}
		if (!$wwwgroup) {
			$wwwgroup = 'nobody (or the group account the web server is running under)';
		}

// First checking writeable directories

		$dirs = array(
			'backups',
			'dump',
			'img/wiki',
			'img/wiki_up',
			'modules/cache',
			'temp',
			'templates_c',
			# 'var',
			# 'var/log',
			# 'var/log/irc',
		);

		foreach ($dirs as $dir) {
			if (!is_dir($dir)) {
				$errors .= "The directory '$docroot/$dir' does not exist.\n";
			} else 
			if (!is_writeable($dir)) {
				$errors .= "The directory '$docroot/$dir' is not writeable by $wwwuser.\n";
			}
		}

		if ($errors) {
			$PHP_CONFIG_FILE_PATH = PHP_CONFIG_FILE_PATH;
			ob_start();
			phpinfo(INFO_MODULES);
			$httpd_conf = 'httpd.conf';
			if (preg_match('/Server Root<\/b><\/td><td\s+align="left">([^<]*)</', ob_get_contents(), $m)) {
				$httpd_conf = $m[1] . '/' . $httpd_conf;
			}
			ob_end_clean();

			print "
<html><body>
<h2><font color='red'>Tiki Installer cannot proceed:</font></h2>
<pre>
$errors
";

			if (!isWindows()) {
				print "You may either chmod the directories above manually to 777, or run one of the sets of commands below.
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

<b><a href='tiki-install.php'>Execute the Tiki installer again</a></b> if you've completed the steps above.
</pre></body></html>";
			}
			exit;
		}

/*
// better write detection above
$can_write = is_writable('db/') &&
			 is_writable('templates_c/') &&
			 is_writable('temp') &&
			 is_writable('backups') &&
			 is_writable('img/wiki') &&
			 is_writable('img/wiki_up') &&
			 is_writable('dump') &&
			 is_writable('modules/cache');

if($can_write) {
  $smarty->assign('can_write','y');
} else {
  $smarty->assign('can_write','n');
}
*/
			 
// Second check try to connect to the database
// if no local.php => no con
// if local then build dsn and try to connect
//   then get con or nocon
$separator='';
$current_path=ini_get('include_path');
if(strstr($current_path, ';')) {
	$separator=';'; 
} else {
	$separator=':'; 
}
if($separator=='') $separator = ':'; // guess
ini_set('include_path', $current_path.$separator.'lib/pear');
include_once('DB.php');




if(!file_exists('db/local.php')) {
  $dbcon = false;
  $smarty->assign('dbcon','n');
} else {
  // include the file to get the variables
  include('db/local.php');
  $dsn = "$db_tiki://$user_tiki:$pass_tiki@$host_tiki/$dbs_tiki";    
  $dbTiki = DB::connect($dsn);
  if (DB::isError($dbTiki)) {        
    $dbcon = false;
    $smarty->assign('dbcon','n');
  } else {
    $dbcon = true;
    $smarty->assign('dbcon','y');
  }
}
// We won't update database info unless we can't connect to the
// database.
if(!$dbcon && isset($_REQUEST['dbinfo'])) {
  $filetowrite='<'.'?'.'php'."\n";
  $filetowrite.='$db_tiki="'.$dbtodsn[$_REQUEST['db']].'";'."\n";
  $filetowrite.='$dbversion_tiki="'.$_REQUEST['db'].'";'."\n";
  switch ($_REQUEST["connmethod"]) {
  case "hostname":
    $filetowrite.='$host_tiki="'.$_REQUEST['host'].'";'."\n";
    break;
  case "tcp":
    $filetowrite.='$host_tiki="tcp('.$_REQUEST['tcphost'].')";'."\n";
    break;
  case "socket":
    $filetowrite.='$host_tiki="unix('.$_REQUEST['socket'].')";'."\n";
    break;
  }
  $filetowrite.='$user_tiki="'.$_REQUEST['user'].'";'."\n";
  $filetowrite.='$pass_tiki="'.$_REQUEST['pass'].'";'."\n";
  $filetowrite.='$dbs_tiki="'.$_REQUEST['name'].'";'."\n";
  $filetowrite.='?'.'>';
  $fw = fopen('db/local.php','w');
  fwrite($fw,$filetowrite);
  fclose($fw);
  include('db/local.php');
  $dsn = "$db_tiki://$user_tiki:$pass_tiki@$host_tiki/$dbs_tiki";    
  $dbTiki = DB::connect($dsn);
  if (DB::isError($dbTiki)) {        
    $dbcon = false;
    $smarty->assign('dbcon','n');
  } else {
    $dbcon = true;
    $smarty->assign('dbcon','y');
  } 
}


if(isset($_REQUEST['restart'])) {
  $_SESSION['install-logged']='';
}
$noadmin = false;
$admin_acc = 'n';
if($dbcon) {
  // Try to see if we have an admin account
  $query = "select hash from users_users where login='admin'";
  @$result =  $dbTiki->query($query);
  if(DB::isError($result)) {
    $admin_acc = 'n';
    $noadmin = true;
  } else {
    if($result->numRows()) {

	    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
		if (isset($res['hash'])) {
			$admin_acc = 'y';
		} else {
			$admin_acc = 'n';
		}
// below is the old method where the admin's pwd must be 'admin' or it won't work
//	    $hash = $res['hash'];
//	    if($hash == md5('admin')) {
//	    	$admin_acc = 'y';
//		} else {
//	    	$admin_acc = 'n';
//	    }
    } else {

    	$admin_acc = 'n';
    	$noadmin =true;
    }
  }
}

if($noadmin) {
  $smarty->assign('noadmin','y');
} else {
  $smarty->assign('noadmin','n');
}

//Load SQL scripts
$files=Array();
$h = opendir('db/');
echo $dbversion_tiki."---";
while($file = readdir($h)) {
  if(strstr($file,'to') && strstr($file,$dbversion_tiki)) {
    $files[]=$file;
  }
}
closedir($h);
$smarty->assign('files',$files);

// If no admin account then allow the creation of an admin account
if(!$noadmin && $admin_acc == 'n' && isset($_REQUEST['createadmin'])) {
  if($_REQUEST['pass1']==$_REQUEST['pass2']) {
    $hash = md5($_REQUEST['pass1']);
    $query = "delete from users_users where login='admin'";
    $dbTiki->query($query);
    $pass1 = $_REQUEST['pass1'];
    $query = "insert into users_users(login,password,hash) 
    values('admin','$pass1','$hash')";
    $dbTiki->query($query);
    $admin_acc = 'y';
  } 
}
$smarty->assign('admin_acc',$admin_acc);

// Since we do have an admin account the user must login to 
// use the install script
if(isset($_REQUEST['login'])) {
	
	$logged = 'n';
	$pass = $_REQUEST['pass'];
	
    // first verify that the user exists
    $query = "select email from users_users where lower(login) = 'admin'";
    $result = $dbTiki->query($query);
    if(!$result->numRows()) {
        $logged = 'n';
    } else {
		$res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    	$hash=md5('admin'.$pass.$res['email']);
		$hash2 = md5($pass);
	   	// next verify the password with 2 hashes methods, the old one (passà)) and the new one (login.pass;email)
    	$query = "select login from users_users where lower(login) = 'admin' and hash in ('$hash', '$hash2')";
      	$result = $dbTiki->query($query);
	  	if($result->numRows()) {
			$logged = 'y';
			$_SESSION['install-logged']='y';
	    }
    }
}

//$logged = 'n';
//if($dbcon && $admin_acc=='y' && isset($_REQUEST['login'])){
//	$hash = md5($_REQUEST['pass']);
//	$cant = $dbTiki->getOne("select count(*) from users_users where login='admin' and hash='$hash'");
//	if($cant) {
//	  $logged = 'y';
//	  $_SESSION['install-logged']='y';
//	} else {
//	  $logged = 'n';
//	}
//}

// If no admin account then we are logged
if($noadmin) {
	$logged = 'y';  
	$_SESSION['install-logged']='y';
}

$smarty->assign('dbdone','n');
$smarty->assign('logged',$logged);

if(isset($_SESSION['install-logged']) && $_SESSION['install-logged']=='y') {
	$smarty->assign('logged','y');
	if(isset($_REQUEST['scratch'])) {
	  process_sql_file('tiki-'.$dbversion_tiki.'.sql');
	  $smarty->assign('dbdone','y');
	}
	if(isset($_REQUEST['update'])) {
	  process_sql_file($_REQUEST['file']);
	  $smarty->assign('dbdone','y');
	}
}



$smarty->display("tiki.tpl");
?>
