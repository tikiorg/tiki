<?php
require_once 'lib/setup/twversion.class.php';

class Installer
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
		global $db_tiki;
		$TWV = new TWVersion;
		$dbversion_tiki = $TWV->getBaseVersion();

		$this->runFile( dirname(__FILE__) . '/../db/tiki-'.$dbversion_tiki.'-'.$db_tiki.'.sql' );
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
			// DB not old enough to handle auto update

			// If 1.9
			if( ! $this->tableExists( 'tiki_minichat' ) ) {
				$this->runFile( dirname(__FILE__) . '/../db/tiki_1.9to2.0.sql' );
			}

			$this->runFile( dirname(__FILE__) . '/../db/tiki_2.0to3.0.sql' );
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

		if( file_exists( $script ) ) {
			require $script;
		}

		if( function_exists( $pre ) )
			$pre( $this );

		$this->runFile( $schema );

		if( function_exists( $post ) )
			$post( $this );

		$this->installed[] = $patch;
		$this->recordPatch( $patch );
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
		global $db_tiki;

		if ( !is_file($file) || !$fp = fopen($file, "r") ) {
			print('Fatal: Cannot open '.$file);
			exit(1);
		}

		while(!feof($fp)) {
			$command.= fread($fp,4096);
		}

		fclose($fp);

		switch ( $db_tiki ) {
		case 'sybase':
			$statements = split("(\r|\n)go(\r|\n)", $command);
			break;
		case 'mssql':
			$statements = split("(\r|\n)go(\r|\n)", $command);
			break;
		case 'oci8':
			$statements = preg_split("#(;\s*\n)|(\n/\n)#", $command);
			break;
		default:
			$statements = preg_split("#(;\s*\n)|(;\s*\r\n)#", $command);
			break;
		}

		$prestmt="";
		$do_exec=true;
		foreach ($statements as $statement) {
			if (trim($statement)) {
				switch ($db_tiki) {
				case "oci8":
					// we have to preserve the ";" in sqlplus programs (triggers)
					if (preg_match("/BEGIN/",$statement)) {
						$prestmt=$statement.";";
						$do_exec=false;
					}
					if (preg_match("/END/",$statement)) {
						$statement=$prestmt."\n".$statement.";";
						$do_exec=true;
					}
					if($do_exec)
						$result = $this->query($statement);
					break;
				default:
					$result = $this->query($statement);
					break;
				}
			}
		}

		$this->query("update `tiki_preferences` set `value`=`value`+1 where `name`='lastUpdatePrefs'");
	} // }}}

	function query( $query, $values = array() ) // {{{
	{
		global $dbTiki, $tikilib;

		$error = '';
		if( $tikilib ) {
			$result = $tikilib->queryError( $query, $error, $values );
		} elseif( $dbTiki && method_exists( $dbTiki, 'Execute' ) ) {
			$result = $dbTiki->Execute( $query, $values );
			$error = $dbTiki->ErrorMsg();
		}

		if( $result ) {
			$this->success[] = $query;
			return $result;
		} else {
			$this->failures[] = array( $query, $error );
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
		foreach( $files as $file ) {
			$filename = basename( $file );
			$this->scripts[] = substr( $filename, 0, -4 );
		}
	} // }}}

	function tableExists( $tableName ) // {{{
	{
		global $db_tiki;
		static $list = null;
		if( is_null( $list ) )
		{
			$list = array();
			switch ( $db_tiki ) {
			    case 'sqlite':
	    			$result = $this->query( "SELECT name FROM sqlite_master WHERE type = 'table'" );
		    		break;
			    default:
			        $result = $this->query( "show tables" );
			        break;
			}
			while( $row = $result->fetchRow() )
				$list[] = reset( $row );
		}

		return in_array( $tableName, $list );
	} // }}}

	function requiresUpdate() // {{{
	{
		return count( $this->patches ) > 0 ;
	} // }}}
}

?>
