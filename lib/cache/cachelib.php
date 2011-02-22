<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
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

class Cachelib
{
	public $implementation;

	function __construct() {
		global $prefs;

		if( isset($prefs['memcache_enabled']) && $prefs['memcache_enabled'] == 'y' ) {
			$this->implementation = new CacheLibMemcache;
		} else {
			$this->implementation = new CacheLibFileSystem;
		}
	}

	function cacheItem($key, $data, $type='') {
		return $this->implementation->cacheItem( $key, $data, $type );
	}

	function isCached($key, $type='') {
		return $this->implementation->isCached( $key, $type );
	}

	function getCached($key, $type='') {
		return $this->implementation->getCached( $key, $type );
	}

	function getSerialized($key, $type = '') {
		$data = $this->getCached( $key, $type );
		
		if( $data ) {
			return unserialize( $data );
		}
	}

	function invalidate($key, $type='') {
		return $this->implementation->invalidate( $key, $type );
	}

	/**
	 * Deprecated - use empty_cache()
	 */
	function empty_full_cache() {
		$this->empty_cache();
	}
	
	/**
	 * Empty one or more caches
	 * 
	 * Checks for existance of libs because it's called from the installer
	 * 
	 * @param mixed $dir_names		all|templates_c|temp_cache|temp_public|modules_cache|prefs (default all)
	 * @param string $log_section	Type of log message. Default 'system'
	 */
	function empty_cache( $dir_names = array('all'), $log_section = 'system' ) {
		global $tikidomain, $logslib, $tikilib;
		
		if (!is_array($dir_names)) {
			$dir_names = array($dir_names);
		}
		if (in_array( 'all', $dir_names )) {
			$this->erase_dir_content("templates_c/$tikidomain");
			$this->erase_dir_content("temp/public/$tikidomain");
			$this->erase_dir_content("temp/cache/$tikidomain");
			$this->erase_dir_content("modules/cache/$tikidomain");
			$this->flush_opcode_cache();
			if (is_object($tikilib)) { 
				$tikilib->set_lastUpdatePrefs();
			}
			if (is_object($logslib)) {
				$logslib->add_log( $log_section, 'erased all cache content');
			}
		}
		if (in_array( 'templates_c', $dir_names )) {
			$this->erase_dir_content("templates_c/$tikidomain");
			$this->flush_opcode_cache();
			if (is_object($logslib)) {
				$logslib->add_log( $log_section, 'erased templates_c content' );
			}
		}
		if (in_array( 'temp_cache', $dir_names)) {
			$this->erase_dir_content("temp/cache/$tikidomain");
			if (is_object($logslib)) {
				$logslib->add_log( $log_section, 'erased temp/cache content' );
			}
		}
		if (in_array( 'temp_public', $dir_names)) {
			$this->erase_dir_content("temp/public/$tikidomain");
			if (is_object($logslib)) { 
				$logslib->add_log( $log_section, 'erased temp/public content' );
			}
		}
		if (in_array( 'modules_cache', $dir_names)) {
			$this->erase_dir_content("modules/cache/$tikidomain");
			if (is_object($logslib)) {
				$logslib->add_log( $log_section, 'erased modules/cache content' );
			}
		}
		if (in_array( 'prefs', $dir_names)) {
			if (is_object($tikilib)) {
				$tikilib->set_lastUpdatePrefs();
			}
		}
	}

	function empty_type_cache($type) {
		return $this->implementation->empty_type_cache( $type );
	}

	function count_cache_files($path, $begin=null) {
		global $tikidomain;
		
		if (!$path or !is_dir($path)) return (array('total' => 0,'cant' =>0));
		$total = 0; 
		$cant = 0;
		$back = array();
		$all = opendir($path);
		
		// If using multiple Tikis but flushing cache on default install...
		if (empty($tikidomain) && is_file('db/virtuals.inc')) {
			$virtuals = array_map('trim', file('db/virtuals.inc'));
		} else {
			$virtuals = false;
		}

		while ($file = readdir($all)) {
			if (substr($file,0,1) == "." or $file == 'CVS' or $file == '.svn' or $file == "index.php" or $file == "README" or $file == "web.config" or ($virtuals && in_array($file, $virtuals)) ) continue;
			if (is_dir($path.'/'.$file) and $file <> ".." and $file <> "." and $file <> "CVS" and $file <> ".svn" ) {
				$du = $this->count_cache_files($path.'/'.$file);
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

	function flush_opcode_cache() {
		if( function_exists( 'apc_clear_cache' ) ) {
			apc_clear_cache();
		}

		if( function_exists( 'xcache_clear_cache' ) && ! ini_get( 'xcache.admin.enable_auth' ) ) {
			foreach( range( 0, xcache_count( XC_TYPE_PHP ) - 1 ) as $index ) {
				xcache_clear_cache( XC_TYPE_PHP, $index );
			}
		}
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
				if (substr($file,0,1) == "." or $file == 'CVS' or $file == '.svn' or $file == "index.php" or $file == "README" or $file == "web.config" or ($virtuals && in_array($file, $virtuals)) ) continue;
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
						//rewrite the language thing, see lib/init/smarty.php
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

class CacheLibFileSystem
{
	var $folder;

	function __construct() {
		global $tikidomain;
		$this->folder = realpath( "temp/cache" );
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
		@file_put_contents($this->folder."/$key",$data);
		return true;
	}

	function isCached($key, $type='') {
		$key = $type.md5($key);
		return is_file($this->folder."/$key");
	}

	function getCached($key, $type='') {
		$key = $type.md5($key);
		if (is_readable($this->folder."/$key")) {
			return @file_get_contents($this->folder."/$key");
		} else {
			return false;
		}
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
}

class CacheLibMemcache
{
	private function getKey( $key, $type ) {
		return $type.md5($key);
	}

	function cacheItem($key, $data, $type='') {
		global $memcachelib;
		$memcachelib->set( $this->getKey( $key, $type ), $data );
		return true;
	}

	function isCached($key, $type='') {
		return false;
	}

	function getCached($key, $type='') {
		global $memcachelib;
		return $memcachelib->get( $this->getKey( $key, $type ) );
	}

	function invalidate($key, $type='') {
		global $memcachelib;
		return $memcachelib->delete( $this->getKey( $key, $type ) );
	}

	function empty_type_cache( $type ) {
		global $memcachelib;
		return $memcachelib->flush();
	}
}

global $cachelib;
$cachelib = new Cachelib();
