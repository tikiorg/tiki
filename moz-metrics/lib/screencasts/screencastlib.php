<?php
require_once("lib/webdav/webdavlib.php");

class ScreencastLib {
	
	/**
	 * Constructor initializes the class and handler
	 * @param $base string full path to base upload directory
	 * @param $handlerType string name of handler class that actually does the work
	 * @param $options array assoc array for handler class options
	 * @return object
	 */
	function ScreencastLib($base, $handlerType, $options=array()) {		
		if ( !$base || !$handlerType ) return null;
		if ( !isset($options['base']) ) $options['base'] = $base;
		if ( isset($options['http'] ) ) $this->http = $options['http'];

		$handler = "ScreencastUpload" . $handlerType;
		$handler = new $handler($options);
		
		if ( !is_object($handler) ) return null;
		
		$this->base = $base;
		$this->handler = $handler;

	}
	
	/**
	 * Adds file using specified handler
	 * @param $file string current file location or data stream
	 * @param $newfile string destination file name
	 * @param $isStream bool whether $file is a data stream or file name
	 * @return bool
	 */
	function add($file, $newfile, $isStream=false) {
		if ( !$isStream && $this->handler && $this->handler->useStream !== false )
			$file = file_get_contents($file);
			
		if ( !$file ) return false;

		return $this->handler->add($file, $newfile);
	}
	
	/**
	 * Finds files matching a particular string
	 * @param $string string
	 * @param $showBase bool return file name with full path
	 * @return array
	 */
	function find($string, $showBase=false) {			
		if( !empty($this->handler) ) {
			return $this->handler->find($string, $showBase);	
		} else {
			return array();
		}
	}
		
}

class ScreencastUploadWebdav extends WebDavLib {
		
	/**
	 * Constructor function to set class options
	 * @param $options array assoc array of options for the handler
	 * @return object
	 */	
	function ScreencastUploadWebdav($options=array()) {
		foreach($options as $k => $v ) {
			$this->$k = $v;
		}
	}
	
	/**
	 * Adds a file using the WebDavLib as a vechicle
	 * @param $data string _data stream_ of file
	 * @param $file string destination name of file
	 * @return bool
	 */
	function add($data, $file) {
		$bytes = $this->put($file, $data);

		if ( strlen($data) != $bytes )
			return false;
		
		return true;
	}
	
	/**
	 * Finds all file names in base directory
	 * @param $string string 
	 * @param $showBase bool show full path or just file name
	 * @return array
	 */
	function find($string, $showBase=false) {
		$files = $this->listFiles();
		$found = array();
		$httpBase = ($showBase) ? $this->http : "";
		
		if ( !is_array($files) || count($files) == 0 )
			return false;

		foreach( $files as $file ) {
			if ( stripos($file, $string) !== false && trim($file) )
				$found[] = $httpBase . $file;
		}
		
		return $found;
			
	}		
}

class ScreencastUploadLocal extends ScreencastUploadWebdav {
	
	/**
	 * Deny the use of data streams for adding files
	 * @var bool
	 */
	var $useStream = false;
	
	/**
	 * Adds a file in tmp PHP directory to destination
	 * @param $data string file name of temporary file
	 * @param $file string destination file name
	 * @return bool
	 */
	function add($data, $file) {
		$dest = $this->base . $file ;

		if ( move_uploaded_file($data, $dest) && chmod($dest, 0644) )
			return true;
		
		return false;	
	}
	
	/**
	 * Finds file names in base directory starting with a given string
	 * @param $string string
	 * @param $showBase bool 
	 * @return array
	 */
	function find($string, $showBase='false') {
		$files = scandir($this->base);
		$found = array();
		$httpBase = ($showBase) ? $this->http : "";
		if ( !is_array($files) || !count($files) )
			return false;
			
	    foreach ($files as $file) {
	    	if ( is_file($this->base . $file) && strpos($file, $string) === 0 ) {
           		$found[] = $httpBase . $file;
        	}
    	}
    	
    	return $found;
	}
}

global $prefs;
$screencastlib = new ScreencastLib($prefs['feature_wiki_screencasts_base'],
	$prefs['feature_wiki_screencasts_upload_type'],
	array('http' => $prefs['feature_wiki_screencasts_httpbase'], 'user' => $prefs['feature_wiki_screencasts_user'], 'pass' => $prefs['feature_wiki_screencasts_pass']));
