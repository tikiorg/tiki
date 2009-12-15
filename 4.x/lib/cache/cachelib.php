<?php
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * \brief A basic library to handle a cache of some Tiki Objects,
 * usage is simple and feel free to improve it
 */

class Cachelib {

  var $folder;

  function Cachelib() {
		global $tikidomain;
		$this->folder = "temp/cache";
		if ($tikidomain) { 
			$this->folder.= "/$tikidomain"; 
		}
    if(!is_dir($this->folder)) {
		mkdir($this->folder);
		chmod($this->folder,0777);
    }
  }
	
  function cacheItem($key, $data, $type='') {
		$key = $type.md5($key);
		$fw = fopen($this->folder."/$key","w");
		fwrite($fw,$data);
		fclose($fw);
		return true;
  }
	
  function isCached($key, $type='') {
		$key = $type.md5($key);
		return is_file($this->folder."/$key");
  }
	
  function getCached($key, $type='') {
		$key = $type.md5($key);
		if ( !file_exists($this->folder."/$key")) { 	
			return serialize(false);
		} 
		$fw = fopen($this->folder."/$key","r");
		if ($l = filesize($this->folder."/$key"))
			$data = fread($fw, $l);
		else
			$data = '';
		fclose($fw);
		return $data;
  }
	
  /** gets the timestamp of item insertion in cache,
   *  returns false if key doesn't exist
   */
  function getCachedDate($key, $type='') {
      $key = $type.md5($key);
      if( is_file($this->folder."/$key") ) {
          return filemtime($this->folder."/$key");
      } else return false;
  }
		
  function invalidate($key, $type='') {
		$key = $type.md5($key);
		if (is_file($this->folder."/$key")) {
			unlink($this->folder."/$key");
		}
  }


	function empty_full_cache(){
		global $tikidomain,$logslib;
		$this->erase_dir_content("templates_c/$tikidomain");
		$this->erase_dir_content("temp/public/$tikidomain");
		$this->erase_dir_content("temp/cache/$tikidomain");
		$this->erase_dir_content("modules/cache/$tikidomain");
		if (is_object($logslib)) {
			$logslib->add_log('system','erased full cache');
		}
	}
	function empty_type_cache($type) {
		$path = $this->folder;
		$all = opendir($path);
		while ($file = readdir($all)) {
			if (strpos($file, $type) === 0) {
				unlink("$path/$file");
			}
		}
	}
		

  function du($path, $begin=null) {
	if (!$path or !is_dir($path)) return (array('total' => 0,'cant' =>0));
	$total = 0; 
	$cant = 0;
	$back = array();
	$all = opendir($path);
	while ($file = readdir($all)) {
		if (is_dir($path.'/'.$file) and $file <> ".." and $file <> "." and $file <> "CVS" and $file <> ".svn" ) {
			$du = $this->du($path.'/'.$file);
			$total+= $du['total'];
			$cant+= $du['cant'];
			unset($file);
		} elseif (!is_dir($path.'/'.$file)) { 
			if (isset($begin) && substr($file, 0, strlen($begin)) != $begin)
				continue; // the file name doesn't begin with the good beginning
			$stats = @stat($path.'/'.$file); // avoid the warning if safe mode on
			$total += $stats['size'];
			$cant++;
			unset($file);
		}
	}
	closedir($all);
	unset($all);
	$back['total'] = $total;
	$back['cant'] = $cant;
	return $back;
  }

  function erase_dir_content($path) {
  	global $tikidomain;
  	
	if (!$path or !is_dir($path)) return 0;
	if ($dir = opendir($path)) {
		// If using multiple Tikis but flushing cache on default install...
		if (empty($tikidomain) && is_file('db/virtuals.inc')) {
			$virtuals = array_map('trim', file('db/virtuals.inc'));
		} else {
			$virtuals = false;
		}

		while (false !== ($file = readdir($dir))) {
			if (substr($file,0,1) == "." or $file == 'CVS' or $file == '.svn' or $file == "index.php" or $file == "README" or ($virtuals && in_array($file, $virtuals)) ) continue;
			if (is_dir($path."/".$file)) {
				$this->erase_dir_content($path."/".$file);
				rmdir($path."/".$file);
			} else {
				unlink($path."/".$file);
			}
		}
		closedir($dir);
	}
  }

  function cache_templates($path,$newlang) {
	global $prefs, $smarty, $tikidomain;

	$oldlang=$prefs['language'];
	$prefs['language']=$newlang;
	if (!$path or !is_dir($path)) return 0;
	if ($dir = opendir($path)) {
		while (false !== ($file = readdir($dir))) {
			$a=explode(".",$file);
			$ext=strtolower(end($a));
			if (substr($file,0,1) == "." or $file == 'CVS') continue;
			if (is_dir($path."/".$file)) {
				$prefs['language']=$oldlang;
				$this->cache_templates($path."/".$file,$newlang);
				$prefs['language']=$newlang;
			} else {
				if ($ext=="tpl") {
					$file=substr($path."/".$file,10);
					$comppath=$smarty->_get_compile_path($file);
					//rewrite the language thing, see setup_smarty.php
					if ($smarty->use_sub_dirs) {
					  $comppath=preg_replace("#/".$oldlang."/#","/".$newlang."/",$comppath,1);
				        } else {
					   $comppath=preg_replace("#/".$tikidomain.$oldlang."#","/".$tikidomain.$newlang,$comppath,1);
					}
					if(!$smarty->_is_compiled($file,$comppath)) {
						$smarty->_compile_resource($file,$comppath);
					}
				}
			}
		}
		closedir($dir);
	}
	$prefs['language']=$oldlang;
  }

}

global $cachelib;
$cachelib = new Cachelib();
