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

	function Installer() // {{{
	{
		$this->buildPatchList();
		$this->buildScriptList();
	} // }}}

	function cleanInstall() // {{{
	{
		$this->runFile( dirname(__FILE__) . '/../db/tiki.sql' );
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

		$prestmt="";
		$do_exec=true;
		$status = true;
		foreach ($statements as $statement) {
			if (trim($statement)) {
				if (preg_match('/^\s*(?!-- )/m', $statement)) {// If statement is not commented
					$display_errors = ini_get('display_errors');
					ini_set('display_errors', 'Off');

					if ($this->query($statement, array(), -1, -1, true, $file) === false) {
						$status = false;
					}
					ini_set('display_errors', $display_errors);
				}
			}
		}

		$this->query("update `tiki_preferences` set `value`= `value`+1 where `name`='lastUpdatePrefs'");
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
}
