<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
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
	
  function cacheItem($key,$data)
  {
	$cache_folder = $this->folder;
	$key = md5($key);
	$fw = fopen($this->folder."/$key","w");
	fwrite($fw,$data);
	fclose($fw);
	return true;
  }
	
  function isCached($key)
  {
	$cache_folder = $this->folder;
	$key = md5($key);
	return file_exists($this->folder."/$key");
  }
	
  function getCached($key)
  {
	$cache_folder = $this->folder;
	$key = md5($key);
	$fw = fopen("$cache_folder/$key","r");
	$data = fread($fw,filesize($this->folder."/$key"));
	fclose($fw);
	return $data;
  }
	
  function invalidate($key)
  {
	$cache_folder = $this->folder;
	$key = md5($key);
	@unlink($this->folder."/$key");
  }
}

$cachelib = new Cachelib();
?>
