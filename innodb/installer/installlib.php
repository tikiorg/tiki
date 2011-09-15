<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}


require_once 'lib/setup/twversion.class.php';
require_once('lib/core/TikiDb/Bridge.php');

class Installer extends TikiDb_Bridge
{
	var $patches = array();
	var $scripts = array();

	var $installed = array();
	var $executed = array();

	var $success = array();
	var $failures = array();
	
	var $useInnoDB = false;

	function Installer() // {{{
	{
		$this->buildPatchList();
		$this->buildScriptList();
	} // }}}

	function cleanInstall() // {{{
	{
		$this->runFile( dirname(__FILE__) . '/../db/tiki.sql' );
		if($this->useInnoDB) {
			$this->runFile( dirname(__FILE__) . '/../db/tiki_innodb.sql' );
		} else {
			$this->runFile( dirname(__FILE__) . '/../db/tiki_myisam.sql' );
		}
		$this->buildPatchList();
		$this->buildScriptList();

		// Base SQL file contains the distribution tiki patches up to this point
		$patches = $this->patches;
		foreach( $patches as $patch ) {
			if( preg_match( '/_tiki$/', $patch ) ) {
				$this->recordPatch( $patch );
			}
		}

		$this->update();
	} // }}}

	function update() // {{{
	{
		if( ! $this->tableExists( 'tiki_schema' ) ) {
			// DB too old to handle auto update

			if( file_exists( dirname(__FILE__) . '/../db/custom_upgrade.sql' ) ) {
				$this->runFile( dirname(__FILE__) . '/../db/custom_upgrade.sql' );
			} else {
				// If 1.9
				if( ! $this->tableExists( 'tiki_minichat' ) ) {
					$this->runFile( dirname(__FILE__) . '/../db/tiki_1.9to2.0.sql' );
				}

				$this->runFile( dirname(__FILE__) . '/../db/tiki_2.0to3.0.sql' );
			}
		}

		$TWV = new TWVersion;
		$dbversion_tiki = $TWV->getBaseVersion();

		$secdb = dirname(__FILE__) . '/../db/tiki-secdb_' . $dbversion_tiki . '_mysql.sql';
		if( file_exists( $secdb ) )
			$this->runFile( $secdb );
		
		$patches = $this->patches;
		foreach( $patches as $patch ) {
			$this->installPatch( $patch );
		}

		foreach( $this->scripts as $script )
			$this->runScript( $script );
	} // }}}

	function installPatch( $patch ) // {{{
	{
		if( ! in_array( $patch, $this->patches ) )
			return;

		$schema = dirname(__FILE__) . "/schema/$patch.sql";
		$script = dirname(__FILE__) . "/schema/$patch.php";

		$pre = "pre_$patch";
		$post = "post_$patch";
		$standalone = "upgrade_$patch";

		if( file_exists( $script ) ) {
			require $script;
		}

		if( function_exists( $standalone ) )
			$standalone( $this );
		else {
			if( function_exists( $pre ) )
				$pre( $this );
	
			$status = $this->runFile( $schema );
	
			if( function_exists( $post ) )
				$post( $this );
		}

		if (!isset($status) || $status ) {
			$this->installed[] = $patch;
			$this->recordPatch( $patch );
		}
	} // }}}

	function runScript( $script ) // {{{
	{
		$file = dirname(__FILE__) . "/script/$script.php";

		if( file_exists( $file ) ) {
			require $file;
		}

		if( function_exists( $script ) )
			$script( $this );

		$this->executed[] = $script;
	} // }}}

	function recordPatch( $patch ) // {{{
	{
		$this->query( "INSERT INTO tiki_schema (patch_name, install_date) VALUES(?, NOW())", array($patch) );
		$this->patches = array_diff( $this->patches, array( $patch ) );
	} // }}}

	function runFile( $file ) // {{{
	{
		if ( !is_file($file) || !$command = file_get_contents($file) ) {
			print('Fatal: Cannot open '.$file);
			exit(1);
		}

		// split the file into several queries?
		$statements = preg_split("#(;\s*\n)|(;\s*\r\n)#", $command);

		$status = true;
		foreach ($statements as $statement) {
			if (trim($statement)) {
				if (preg_match('/^\s*(?!-- )/m', $statement)) {// If statement is not commented
					$display_errors = ini_get('display_errors');
					ini_set('display_errors', 'Off');

					if($this->useInnoDB) {
						// Convert all MyISAM statments to InnoDB
						$statement = str_ireplace("MyISAM", "InnoDB", $statement);
					}

					if ($this->query($statement, array(), -1, -1, true, $file) === false) {
						$status = false;
					}
					ini_set('display_errors', $display_errors);
				}
			}
		}

		$this->query("update `tiki_preferences` set `value`= `value`+1 where `name`='versionOfPreferencesCache'");
		return $status;
	} // }}}

	function query( $query = null, $values = array(), $numrows = -1, $offset = -1, $reporterrors = true, $patch ='' ) // {{{
	{
		$error = '';
		$result = $this->queryError( $query, $error, $values );

		if( $result && empty($error) ) {
			$this->success[] = $query;
			return $result;
		} else {
			$this->failures[] = array( $query, $error, substr( basename( $patch ), 0, -4 ) );
			return false;
		}
	} // }}}

	function buildPatchList() // {{{
	{
		$this->patches = array();

		$files = glob( dirname(__FILE__) . '/schema/*_*.sql' );
		foreach( $files as $file ) {
			$filename = basename( $file );
			$this->patches[] = substr( $filename, 0, -4 );
		}

		// Add standalone PHP scripts
		$files = glob( dirname(__FILE__) . '/schema/*_*.php' );
		foreach( $files as $file ) {
			$filename = basename( $file );
			$patch = substr( $filename, 0, -4 );
			if (!in_array($patch, $this->patches)) $this->patches[] = $patch;
		}

		$installed = array();
		$results = $this->query( "SELECT patch_name FROM tiki_schema" );
		if( $results ) {
			while( $row = $results->fetchRow() ) {
				$installed[] = reset($row);
			}
		} else {
			// Erase initial error
			$this->failures = array();
		}

		$this->patches = array_diff( $this->patches, $installed );

		sort( $this->patches );
	} // }}}

	function buildScriptList() // {{{
	{
		$files = glob( dirname(__FILE__) . '/script/*.php' );
		if (empty($files))
			return;
		foreach( $files as $file ) {
			$filename = basename( $file );
			$this->scripts[] = substr( $filename, 0, -4 );
		}
	} // }}}

	function tableExists( $tableName ) // {{{
	{
		$result = $this->query( "show tables" );
		$list = array();
		while( $row = $result->fetchRow() )
			$list[] = reset( $row );

		return in_array( $tableName, $list );
	} // }}}

	function requiresUpdate() // {{{
	{
		return count( $this->patches ) > 0 ;
	} // }}}
	
	/**
	*	Determine if the web server is an IIS server
	*	@return true if IIS server, else false
  	* \static
	*/
	static function isIIS() {
		static $IIS;

		// Sample value Microsoft-IIS/7.5
		if (!isset($IIS) && isset($_SERVER['SERVER_SOFTWARE'])) {
			$IIS = substr($_SERVER['SERVER_SOFTWARE'], 0, 13) == 'Microsoft-IIS';
		}

		return $IIS;
	}


	/**
	*	Build the full URL for the current page
	*	Helper function for checkIISFileAccess
	*	@return Full URL to the current page
  	* \static
	*/
	static function curPageURL() {
		$pageURL = 'http';
		if (isset($_SERVER["HTTPS"]) && ($_SERVER["HTTPS"] == "on")) {
			$pageURL .= 's';
		}
		$pageURL .= '://';
		if ($_SERVER['SERVER_PORT'] != '80') {
			$pageURL .= $_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
		} else {
			$pageURL .= $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		}
		return $pageURL;
	}


	/**
	*	Build the Tiki base URL
	*	Helper function for checkIISFileAccess
	*	@return The tiki base folder URL. Not including the "/"
  	* \static
	*/
	static function tikiBaseURL() {
		$baseURL = self::curPageURL();
		$fileName = basename($_SERVER['SCRIPT_NAME']);
		$pos = strpos($baseURL,$fileName)-1;
		if($pos <0) {
			return $baseURL;
		}
		return substr($baseURL,0,$pos);
	}

	
	/**
	*	If running IIS, check the file access in folders with web.config with a known "problem file"
	*	If the URL Rewrite module missing the file will fail to load
	*
	*	@return false if IIS file access seems to fail (with a valid check). Otherwise true
  	* \static
	*/
	static function checkIISFileAccess()
	{
		require_once 'Zend/Http/Client.php';	
		static	$rcAccess;
		
		// A small file that fails when URL Rewrite is not present
		$checkFile = 'lib/metrics.js';

		if(!isset($rcAccess)) {

			try {
				// Build the URL
				$baseURL = self::tikiBaseURL();
				$checkURL = $baseURL."/".$checkFile;
			
				// Do the check
				$client = new Zend_Http_Client($checkURL, 
						array('maxredirects' => 0,
							'timeout'      => 10));
				if($client != null) {
					$response = $client->request('GET');
					if($response->isError())
						$rcAccess = false;	// Probably no URL Rewrite present
					else
						$rcAccess = true; 	// URL Rewrite present?
				}
	
			} catch(Zend_Http_Client_Exception $e) {
				$err = $e->getMessage();
			}
			if(!isset($rcAccess)) {
				return true; // Something when wrong. Assume it's not caused by a missing URL Rewrite module
			}

		}
		return $rcAccess;
	}


	/**
	*	Build a warning string if IIS is used. It requires the URL Rewrite module for proper operation.
	*	@return IIS warning string. An empty string is returned for other servers.
  	* \static
	*/
	static function buildIISWarning() {
		// Prepare IIS warning string
		$iis_warning = '';
		if(Installer::isIIS() && !Installer::checkIISFileAccess()) {
			$iis_warning = tra('
				<div style="text-align:left">
				<br/>
				<h3>IIS Installation Note</h3>
				<span style="color:red">Your system does <b>not</b> seem to have the <b>URL Rewrite</b> module installed.</span>
				<p>
				For proper operation on IIS the <b>URL Rewrite</b> module should be installed. 
				Without it you will be able to operate Tiki, but you may encounter some problems with images and some features.<br/>
				Please see <a href="http://doc.tiki.org/Windows+Server+Install">Windows Server Install</a> on tiki.org for more information.
				</p>
				</div>');
		}
		return $iis_warning;
	}	
	
	/**
	 * Get a list of installed engines in the MySQL instance
	 * $return array of engine names
	*/
	function getEngines() {
		$engines = array();
		$result = $this->query('show engines');
		if ( $result ) {
			while ( $res = $result->fetchRow() ) {
				$engines[] = $res['Engine'];
			}		
		}		
		return $engines;
	}
	
	/**
	 * Check if InnoDB is an avaible engine
	 * @return true if the InnoDB engine is available
	 */ 
	function hasInnoDB() {
		$engines = $this->getEngines();
		foreach($engines as $engine) {
			if(strcmp(strtoupper($engine), 'INNODB') == 0) {
				return true;
			}
		}
		return false;
	}
}
