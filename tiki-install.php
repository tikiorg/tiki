<?php # $Header: /cvsroot/tikiwiki/tiki/tiki-install.php,v 1.3 2003-05-01 18:06:50 rossta Exp $

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
  while($line = fgets($fp)) {
    if(substr($line,0,1)!='#') {
      $command.=$line;
    }
    if(strstr($command,';')) {
      $command = trim($command);
      $commands[] = $command;
      $result = $dbTiki->query($command);
      if(DB::isError($result)) {
      	trigger_error("MYSQL error:  ".$result->getMessage()." in query:<br/>".$command."<br/>",E_USER_WARNING);
      	die;
      }
      $command = '';
    }
  }
  $smarty->assign('commands',$commands);
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
  rename('tiki-install.php','tiki-install.done');
  header('location: tiki-index.php');
  die;
}

$smarty = new Smarty_Sterling();
$smarty->load_filter('pre','tr');
$smarty->load_filter('output','trimwhitespace');
$smarty->assign('style','default.css');
$smarty->assign('mid','tiki-install.tpl');


// First checking writeable directories
$can_write = is_writable('db/') &&
			 is_writable('templates_c/') &&
			 is_writable('temp') &&
			 is_writable('backups') &&
			 is_writable('img/wiki') &&
			 is_writable('img/wiki_up') &&
			 is_writable('modules/cache');
if($can_write) {
  $smarty->assign('can_write','y');
} else {
  $smarty->assign('can_write','n');
}		
			 
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
  $dsn = "mysql://$user_tiki:$pass_tiki@$host_tiki/$dbs_tiki";    
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
  $filetowrite.='$host_tiki="'.$_REQUEST['host'].'";'."\n";
  $filetowrite.='$user_tiki="'.$_REQUEST['user'].'";'."\n";
  $filetowrite.='$pass_tiki="'.$_REQUEST['pass'].'";'."\n";
  $filetowrite.='$dbs_tiki="'.$_REQUEST['name'].'";'."\n";
  $filetowrite.='?'.'>';
  $fw = fopen('db/local.php','w');
  fwrite($fw,$filetowrite);
  fclose($fw);
  include('db/local.php');
  $dsn = "mysql://$user_tiki:$pass_tiki@$host_tiki/$dbs_tiki";    
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
	    $hash = $res['hash'];
	    if($hash == md5('admin')) {
	    	$admin_acc = 'y';
		} else {
	    	$admin_acc = 'n';
	    }
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
while($file = readdir($h)) {
  if(strstr($file,'to')) {
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
$logged = 'n';
if($dbcon && $admin_acc=='y' && isset($_REQUEST['login'])){
	$hash = md5($_REQUEST['pass']);
	$cant = $dbTiki->getOne("select count(*) from users_users where login='admin' and hash='$hash'");
	if($cant) {
	  $logged = 'y';
	  $_SESSION['install-logged']='y';
	} else {
	  $logged = 'n';
	}
}

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
	  process_sql_file('tiki.sql');
	  $smarty->assign('dbdone','y');
	}
	if(isset($_REQUEST['update'])) {
	  process_sql_file($_REQUEST['file']);
	  $smarty->assign('dbdone','y');
	}
}



$smarty->display("tiki.tpl");
?>