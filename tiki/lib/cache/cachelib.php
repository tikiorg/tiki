<?php

/*
A basic library to handle a cache of some Tiki Objects,
usage is simple and feel free to improve it
*/

class Cachelib {

  var $folder = "temp/cache";

  function Cachelib() {
    if(!file_exists($this->folder)) {
  	    mkdir($this->folder);
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
