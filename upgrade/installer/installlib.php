<?php
require_once 'lib/setup/twversion.class.php';

class Installer
{
	private $patches = array();
	private $scripts = array();

	function __construct() // {{{
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

		// Base SQL file contains the distribution tiki patches up to this point
		foreach( $this->patches as $patch ) {
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

		$installed = array();
		foreach( $this->patches as $patch ) {
			$this->installPatch( $patch );
			$installed[] = $patch;
		}

		foreach( $this->scripts as $script ) {
			$this->runScript( $script );
		}

		return $installed;
	} // }}}

	private function installPatch( $patch ) // {{{
	{
		if( in_array( $patch, $this->patches ) )
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

		$this->recordPatch( $patch );
	} // }}}

	private function runScript( $script ) // {{{
	{
		$file = dirname(__FILE__) . "/script/$script.php";

		if( file_exists( $file ) ) {
			require $file;
		}

		if( function_exists( $script ) )
			$script( $this );
	} // }}}

	private function recordPatch( $patch ) // {{{
	{
		$this->query( "INSERT INTO tiki_schema (patch_name, install_date) VALUES(?, NOW())", array($patch) );
		$this->patches = array_diff( $this->patches, array( $patch ) );
	} // }}}

	private function runFile( $file ) // {{{
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
		global $dbTiki;

		if( $dbTiki && method_exists( $dbTiki, 'query' ) ) {
			return $dbTiki->query( $query, $values );
		} elseif( $dbTiki && method_exists( $dbTiki, 'Execute' ) ) {
			return $dbTiki->Execute( $query, $values );
		}
	} // }}}

	private function buildPatchList() // {{{
	{
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
		}

		$this->patches = array_diff( $this->patches, $installed );

		sort( $this->patches );
	} // }}}

	private function buildScriptList() // {{{
	{
		$files = glob( dirname(__FILE__) . '/script/*.php' );
		foreach( $files as $file ) {
			$filename = basename( $file );
			$this->scripts[] = substr( $filename, 0, -4 );
		}

		$installed = array();
		$results = $this->query( "SELECT patch_name FROM tiki_schema" );
		if( $results ) {
			while( $row = $results->fetchRow() ) {
				$installed[] = reset($row);
			}
		}

		$this->patches = array_diff( $this->patches, $installed );

		sort( $this->patches );
	} // }}}

	private function tableExists( $tableName ) // {{{
	{
		static $list = array();
		if( ! count( $list ) )
		{
			$result = $this->query( "show tables" );
			while( $row = $result->fetchRow() )
				$list[] = reset( $row );
		}

		return in_array( $tableName, $list );
	} // }}}
}

?>
