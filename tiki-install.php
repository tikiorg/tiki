<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-install.php,v 1.82 2007-02-08 13:51:20 sylvieg Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

if (!file_exists("installer/tiki-installer.php")) {
	header ("Status: 410 Gone");
	header ("HTTP/1.0 410 Gone"); 
	header ('location: index.php');
	die('TikiWiki installer has been disabled.');
} else {
	include_once("installer/tiki-installer.php");
}

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
	if(!$fp = fopen("db/$file", "r")) {
		print('Fatal: Cannot open db/'.$file);
		exit(1);
	}

	while(!feof($fp)) {
		$command.= fread($fp,4096);
	}

	switch ($db_tiki) {
	  case "sybase":
	    $statements=split("(\r|\n)go(\r|\n)",$command);
	    break;
          case "mssql":
	    $statements=split("(\r|\n)go(\r|\n)",$command);
            break;
	  case "oci8":
	    $statements=preg_split("#(;\s*\n)|(\n/\n)#",$command);
	    break;
	  default:
		$statements=preg_split("#(;\s*\n)|(;\s*\r\n)#",$command);
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

function write_local_php($dbb_tiki,$host_tiki,$user_tiki,$pass_tiki,$dbs_tiki,$dbversion_tiki="1.9") {
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
	// remove the header and die comments to render this script dead
	$removed = false;
	$fh = fopen('tiki-install.php', 'rb');
	$data = fread($fh, filesize('tiki-install.php'));
	fclose($fh);

	$data = preg_replace('/\/\/stopinstall: /', '', $data);

	if (is_writable("tiki-install.php")) {
		$fh = fopen('tiki-install.php', 'wb');
		if (fwrite($fh, $data) > 0) {
			$removed = true;
		}
		fclose($fh);
	}

	if ($removed == true) {
		header ('location: tiki-index.php');
	} else { // TODO: display this via translantable error msg template ?
		print "<html><head><title>Ooops !</title></head><body>
<h1 style='color: red'>Ooops !</h1>
<p>Tikiwiki installer failed to rename the <b>tiki-install.php</b> file.</p>
<p style='border: solid 1px red; margin: 0 10% 0 10%; text-align: center; width: 80%'>Leaving this file on a publicly accessible site is a <strong>security risk</strong>.</p>
<p>Please remove or rename the <b>tiki-install.php</b> from your Tiki installation folder 'manually' (e.g. using SSH or FTP).
<strong>Somebody else could be potentially able to wipe out your Tikiwiki database if you do not remove or rename this file !</strong></p>
<p><a href='index.php'>Proceed to your site</a> after you have removed or renamed <b>tiki-install.php</b>.</p>
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
>>>>>>> 1.62.2.29
}

?>
