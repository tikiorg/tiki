<?php
// $Header$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/*
A basic library to handle a cache of some Tiki Objects,
usage is simple and feel free to improve it
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
	
  function cacheItem($key,$data) {
		$key = md5($key);
		$fw = fopen($this->folder."/$key","w");
		fwrite($fw,$data);
		fclose($fw);
		return true;
  }
	
  function isCached($key) {
		$key = md5($key);
		return is_file($this->folder."/$key");
  }
	
  function getCached($key) {
		$key = md5($key);
	if ( filesize($this->folder."/$key") == 0 ) { 	
			return serialize(false);
		} 
		$fw = fopen($this->folder."/$key","r");
		$data = fread($fw,filesize($this->folder."/$key"));
		fclose($fw);
		return $data;
}
	
  /** gets the timestamp of item insertion in cache,
   *  returns false if key doesn't exist
   */
  function getCachedDate($key) {
      $key = md5($key);
      if( is_file($this->folder."/$key") ) {
          return filemtime($this->folder."/$key");
      } else return false;
  }
		
  function invalidate($key) {
		$key = md5($key);
		if (is_file($this->folder."/$key")) {
			unlink($this->folder."/$key");
		}
  }

	function empty_full_cache(){
		global $tikidomain,$logslib;
		$this->erase_dir_content("templates_c/$tikidomain");
		$this->erase_dir_content("temp/cache/$tikidomain");
		$this->erase_dir_content("modules/cache/$tikidomain");
		if (is_object($logslib)) {
			$logslib->add_log('system','erased full cache');
		}
	}

  function du($path, $begin=null) {
	if (!$path or !is_dir($path)) return (array('total' => 0,'cant' =>0));
	$total = 0; 
	$cant = 0;
	$back = array();
	$all = opendir($path);
	while ($file = readdir($all)) {
		if (is_dir($path.'/'.$file) and $file <> ".." and $file <> "." and $file <> "CVS") {
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
	if (!$path or !is_dir($path)) return 0;
	if ($dir = opendir($path)) {
		while (false !== ($file = readdir($dir))) {
			if (substr($file,0,1) == "." or $file == 'CVS' or $file == "index.php" or $file == "README" ) continue;
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

$cachelib = new Cachelib();
?>
