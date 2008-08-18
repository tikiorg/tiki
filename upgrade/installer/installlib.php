<?php

class Installer
{
	private $patches = array();

	function __construct() // {{{
	{
		$this->buildPatchList();
	} // }}}

	function cleanInstall() // {{{
	{
		// FIXME : Should run the converted file
		$this->runFile( dirname(__FILE__) . '/db/tiki.sql' );

		// Base SQL file contains the distribution tiki patches up to this point
		foreach( $this->patches as $patch )
			if( preg_match( '/_tiki$/', $patch ) )
				$this->recordPatch( $patch );

		$this->update();
	} // }}}

	function update() // {{{
	{
		$installed = array();
		foreach( $this->patches as $patch )
		{
			$this->installPatch( $patch );
			$installed[] = $patch;
		}

		return $installed;
	} // }}}

	private function installPatch( $patch ) // {{{
	{
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

		if( $dbTiki ) {
			return $dbTiki->query( $query, $values );
		} else {
			// FIXME : Resolve for when used 
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
		if( ! $results ) {
			die("Your installation does not support the installer yet. Run db/tiki_2.0to3.0.sql one last time.\n");
		}

		while( $row = $results->fetchRow() ) {
			$installed[] = reset($row);
		}

		$this->patches = array_diff( $this->patches, $installed );

		sort( $this->patches );
	} // }}}
}

?>
