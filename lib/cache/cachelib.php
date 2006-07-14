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
			@chmod($this->folder,"0777");
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
		@unlink($this->folder."/$key");
  }
}

$cachelib = new Cachelib();
?>
