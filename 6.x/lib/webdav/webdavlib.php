<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once("lib/pear/HTTP/WebDAV/Client.php");

class WebDavLib extends HTTP_WebDAV_Client_Stream
{

	/**
	 * Constructor
	 * @param $base string base path of webdav share
	 * @param $user string username for auth
	 * @param $pass string pass for auth
	 */
	function WebDavLib($base="", $user="", $pass="") {
		if ($base) $base = $this->base;
		if ($user) $user = $this->user;
		if ($pass) $pass = $this->pass;	
	}
	
	/**
	 * Sets base for webdav share
	 * @param $base string base path for webdav share
	 */
	function setBase($base) {
		$this->base = $base;
	}
	
	/**
	 * Sets username for webdav auth
	 * @param $user string a username
	 */
	function setUser($user) {
		$this->user = $user;
	}
	
	/**
	 * Sets password for webdav auth
	 * @param $pass string a password
	 */
	function setPass($pass) {
		$this->pass = $pass;
	}
	
	/**
	 * Puts a file on a webdav share
	 * @param $string name of file your adding or appending
	 * @param $string binary data of file
	 * @param $append bool flag for append mode
	 * @returns $bytes int number of bytes written
	 */
	function put($file, $data, $append=false, $reset=true) {
		$path = $this->base . $file;
		$mode = ($append) ? "a" : "w";
		$bytes = 0;
		
		if ( $append && ( $data === "" || $data === null  ) ) return $bytes;
		
		if( $reset ) $this->stream_reset();
		  
		if ( $this->stream_open($path, $mode, array()) ) {
			$bytes = $this->stream_write($data);
			$this->stream_close();
		}
		
		if( $reset ) $this->stream_reset();
		
		return $bytes; 
	}
	
	/**
	 * Appends a chunk of data to a file
	 * @params $file string name of file to append
	 * @params $data string data
	 * @params $offset int starting position of the chunk in data
	 * @params $chunkSize int size of chunk in bytes
	 * @returns $bytes int number of bytes written
	 */
	function putChunk($file, $data, $offset=0, $chunkSize=102400) {
		$chunk = substr($data, $offset, $chunkSize);
		$bytes = $this->put($file, $chunk, true, false);

		if ( $bytes < $chunkSize ) $this->stream_reset();
		
		return $bytes;
	}
	
	/**
	 * Retrieves a file from a webdav share
	 * @param $file string file name
	 * @param $chunkSize int buffer size for partial requests (uneeded)
	 * @returns $data string data stream of file
	 */
	function get($file, $chunkSize=false) {
		$path = $this->base . $file;
		$mode = "r";
		$data = "";
		
		$this->stream_reset();
		
		if ( !$this->stream_open($path, $mode, array()) )
			return false;
		
		if (!$chunkSize) {
			$fileStat = $this->stream_stat();
			$chunkSize = $fileStat["size"];
		}
		
		while( !$this->stream_eof()  ) {
			$data .= $this->stream_read($chunkSize);
		}
		
		$this->stream_close();
		$this->stream_reset();
		
		return $data;
	}
	
	/**
	 * Returns a list of files inside a directory
	 * @params $dir string directory relative to the base url
	 * @returns $files array collection of files inside directory
	 */
	function listFiles($dir="") {
		$path = $this->base . $dir;
		$files = array();
		
		if ( !$this->dir_opendir($path, array()) )
			return false;
		
		while ( $file = $this->dir_readdir() )
			$files[] = $file;
	
		$this->dir_closedir();
		 		
		return $files;
	}
	
	/**
	 * Delete multiple files from a webdav share
	 * @params $files array names of files to remove
	 * @params $dir string directory relative to base url
	 * @return bool true on success
	 */
	function unlinkFiles($files, $dir="") {
		$path = $this->base . $dir;
		
		foreach ($files as $file) {
				$filePath = $path . $file;
				if ( !$this->unlink($filePath) ) 
					return false;
		}
		
		return true;
	}
	
	/**
	 * Maintainence function to allow multiple gets and puts
	 * without creating or re-init a class
	 */
	function stream_reset() {
		$this->position = 0;
	}
	
}

$webdavlib = new WebDavLib();
