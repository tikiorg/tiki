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
	$this->folder = "temp/".$tikidomain."cache";
    if(!file_exists($this->folder)) {
  	    // NO, this does _not_ create a world writeable directory. mkdir will '&' current mask (default 0022) with 0777 to give you '0755'. Older versions of php 4.1 (4.1.2 for sure) had the mask as a required paramter.
  	    mkdir($this->folder, 0777);
    }
  }
	
  function cacheItem($key,$data)
  {
	$cache_folder = $this->folder;
	$key = md5($key);
	$fw = fopen("$cache_folder/$key","w");
	fwrite($fw,$data);
	fclose($fw);
	return true;
  }
	
  function isCached($key)
  {
	$cache_folder = $this->folder;
	$key = md5($key);
	return file_exists("$cache_folder/$key");
  }
	
  function getCached($key)
  {
	$cache_folder = $this->folder;
	$key = md5($key);
	$fw = fopen("$cache_folder/$key","r");
	$data = fread($fw,filesize("$cache_folder/$key"));
	fclose($fw);
	return $data;
  }
	
  function invalidate($key)
  {
	$cache_folder = $this->folder;
	$key = md5($key);
	@unlink("$cache_folder/$key");
  }
}

$cachelib = new Cachelib();
?>
