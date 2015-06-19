<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
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

/**
 *
 */
class Installer extends TikiDb_Bridge
{
	public $patches = array();
	public $scripts = array();

	public $installed = array();
	public $executed = array();

	public $success = array();
	public $failures = array();
	
	public $useInnoDB = false;

    /**
     *
     */
    function __construct() // {{{
	{
		$this->buildPatchList();
		$this->buildScriptList();
	} // }}}

	function cleanInstall() // {{{
	{
		if ($image = $this->getBaseImage()) {
			$this->runFile($image);
			$this->buildPatchList();
			$this->buildScriptList();
		} else {
			// No image specified, standard install
			$this->runFile(dirname(__FILE__) . '/../db/tiki.sql');
			if ($this->isMySQLFulltextSearchSupported()) {
				$this->runFile(dirname(__FILE__) . '/../db/tiki_fulltext_indexes.sql');
			}
			if ($this->useInnoDB) {
				$this->runFile(dirname(__FILE__) . '/../db/tiki_innodb.sql');
			} else {
				$this->runFile(dirname(__FILE__) . '/../db/tiki_myisam.sql');
			}
			$this->buildPatchList();
			$this->buildScriptList();

			// Base SQL file contains the distribution tiki patches up to this point
			$patches = $this->patches;
			foreach ( $patches as $patch ) {
				if ( preg_match('/_tiki$/', $patch) ) {
					$this->recordPatch($patch);
				}
			}
		}

		$this->update();
	} // }}}

	function update() // {{{
	{
		// Mark InnoDB usage for updates
		if (strcasecmp($this->getCurrentEngine(), "InnoDB") == 0) {
			$this->useInnoDB = true;
		}

		if ( ! $this->tableExists('tiki_schema') ) {
			// DB too old to handle auto update

			if ( file_exists(dirname(__FILE__) . '/../db/custom_upgrade.sql') ) {
				$this->runFile(dirname(__FILE__) . '/../db/custom_upgrade.sql');
			} else {
				// If 1.9
				if ( ! $this->tableExists('tiki_minichat') ) {
					$this->runFile(dirname(__FILE__) . '/../db/tiki_1.9to2.0.sql');
				}

				$this->runFile(dirname(__FILE__) . '/../db/tiki_2.0to3.0.sql');
			}
		}

		$TWV = new TWVersion;
		$dbversion_tiki = $TWV->getBaseVersion();

		// If a Mysql data file exists, use that. Very fast
		//	If data file is missing or the batch loader is not available, use the single insert method
		$secdb = dirname(__FILE__) . '/../db/tiki-secdb_' . $dbversion_tiki . '_mysql.sql';
		$secdbData = dirname(__FILE__) . '/../db/tiki-secdb_' . $dbversion_tiki . '_mysql.data';
		if ( file_exists($secdbData) ) {
			// A MySQL datafile exists
			$truncateTable = true;
			$rc = $this->runDataFile($secdbData, 'tiki_secdb', $truncateTable);
			if ($rc == false) {
				// The batch loader failed
				if ( file_exists($secdb) ) {
					// Run single inserts
					$this->runFile($secdb);
				}
			}
		} else if ( file_exists($secdb) ) {
			// Run single inserts
			$this->runFile($secdb);
		}
		
		$patches = $this->patches;
		foreach ($patches as $patch) {
			$this->installPatch($patch);
		}

		foreach ( $this->scripts as $script ) {
			$this->runScript($script);
		}
	} // }}}

    /**
     * @param $patch
     */
    function installPatch( $patch ) // {{{
	{
		if ( ! in_array($patch, $this->patches) ) {
			return;
		}

		$schema = dirname(__FILE__) . "/schema/$patch.sql";
		$script = dirname(__FILE__) . "/schema/$patch.php";
		$profile = dirname(__FILE__) . "/schema/$patch.yml";

		$pre = "pre_$patch";
		$post = "post_$patch";
		$standalone = "upgrade_$patch";

		if ( file_exists($script) ) {
			require $script;
		}

		global $dbs_tiki;
		$local_php = TikiInit::getCredentialsFile();
		if (empty($dbs_tiki) && is_readable($local_php)) {
			require($local_php);
			unset($db_tiki, $host_tiki, $user_tiki, $pass_tiki);
		}

		if ( function_exists($standalone) ) {
			$standalone($this);
		} else {
			if ( function_exists($pre) ) {
				$pre( $this );
			}
	
			if (file_exists($profile)) {
				$status = $this->applyProfile($profile);
			} else {
				$status = $this->runFile($schema);
			}
	
			if ( function_exists($post) ) {
				$post( $this );
			}
		}

		if (!isset($status) || $status ) {
			$this->installed[] = $patch;
			$this->recordPatch($patch);
		}
	} // }}}

    /**
     * @param $script
     */
    function runScript( $script ) // {{{
	{
		$file = dirname(__FILE__) . "/script/$script.php";

		if ( file_exists($file) ) {
			require $file;
		}

		if ( function_exists($script) )
			$script($this);

		$this->executed[] = $script;
	} // }}}

    /**
     * @param $patch
     */
    function recordPatch( $patch ) // {{{
	{
		$this->query("INSERT INTO tiki_schema (patch_name, install_date) VALUES(?, NOW())", array($patch));
		$this->patches = array_diff($this->patches, array($patch));
	} // }}}

	private function applyProfile($profileFile)
	{
		// By the time a profile install is requested, the installation should be functional enough to work
		require_once 'tiki-setup.php';
		$directory = dirname($profileFile);
		$profile = substr(basename($profileFile), 0, -4);

		$profile = Tiki_Profile::fromFile($directory, $profile);

		$tx = $this->begin();

		$installer = new Tiki_Profile_Installer;
		$installer->install($profile);

		$tx->commit();
	}

	/**
	 * Batch insert from a mysql data file
	 *
	 * @param $file				MySQL export file
	 * @param $targetTable		Target table
	 * @param $clearTable=true	Flag saying if the target table should be truncated or not
	 * @return bool
	 */
	function runDataFile( $file, $targetTable, $clearTable=true ) // {{{
	{
		if ( !is_file($file) || !$command = file_get_contents($file) ) {
			print('Fatal: Cannot open '.$file);
			exit(1);
		}

		if ($clearTable == true) {
			$statement = 'truncate table '.$targetTable;
			$this->query($statement);
		}

		// LOAD DATA INFILE doesn't like single \ directory separators. Replace with \\
		$inFile = str_replace('\\', '\\\\', $file);

		$status = true;
		$statement = 'LOAD DATA INFILE "'.$inFile.'" INTO TABLE '.$targetTable;
		if ($this->query($statement) === false) {
			$status = false;
		}
		return $status;
	}
		/**
     * @param $file
     * @return bool
     */
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
					if ($this->useInnoDB) {
						// Convert all MyISAM statments to InnoDB
						$statement = str_ireplace("MyISAM", "InnoDB", $statement);
					}

					if ($this->query($statement, array(), -1, -1, true, $file) === false) {
						$status = false;
					}
				}
			}
		}

		return $status;
	} // }}}

    /**
     * @param null $query
     * @param array $values
     * @param $numrows
     * @param $offset
     * @param bool $reporterrors
     * @param string $patch
     * @return bool
     */
    function query( $query = null, $values = array(), $numrows = -1, $offset = -1, $reporterrors = true, $patch ='' ) // {{{
	{
		$error = '';
		$result = $this->queryError($query, $error, $values);

		if ( $result && empty($error) ) {
			$this->success[] = $query;
			return $result;
		} else {
			$this->failures[] = array($query, $error, substr(basename($patch), 0, -4));
			return false;
		}
	} // }}}

	function buildPatchList() // {{{
	{
		$this->patches = array();

		$files = glob(dirname(__FILE__) . '/schema/*_*.sql');
		foreach ( $files as $file ) {
			$filename = basename($file);
			$this->patches[] = substr($filename, 0, -4);
		}

		$files = glob(dirname(__FILE__) . '/schema/*_*.yml');
		if($files != false){
			foreach ( $files as $file ) {
				$filename = basename($file);
				$this->patches[] = substr($filename, 0, -4);
			}
		}
		// Add standalone PHP scripts
		$files = glob(dirname(__FILE__) . '/schema/*_*.php');
		foreach ( $files as $file ) {
			$filename = basename($file);
			$patch = substr($filename, 0, -4);
			if (!in_array($patch, $this->patches)) {
				$this->patches[] = $patch;
			}
		}

		$installed = array();
		$results = false;
		
		if ($this->tableExists('tiki_schema')) {
			$installed = $this->table('tiki_schema')->fetchColumn('patch_name', array());
		}
		
		if ( empty($installed) ) {
			// Erase initial error
			$this->failures = array();
		}

		$this->patches = array_diff($this->patches, $installed);

		sort($this->patches);
	} // }}}

	function buildScriptList() // {{{
	{
		$files = glob(dirname(__FILE__) . '/script/*.php');
		if (empty($files))
			return;
		foreach ( $files as $file ) {
			if (basename($file) === "index.php")
				continue;
			$filename = basename($file);
			$this->scripts[] = substr($filename, 0, -4);
		}
	} // }}}

    /**
     * @param $tableName
     * @return bool
     */
    function tableExists( $tableName ) // {{{
	{
		$list = $this->listTables();
		return in_array($tableName, $list);
	} // }}}

	function isInstalled() // {{{
	{
		return $this->tableExists('tiki_preferences');
	} // }}}

    /**
     * @return bool
     */
    function requiresUpdate() // {{{
	{
		return count($this->patches) > 0 ;
	} // }}}

	private function getBaseImage() // {{{
	{
		$iniFile = __DIR__ . '/../db/install.ini';

		$ini = array();
		if (is_readable($iniFile)) {
			$ini = parse_ini_file($iniFile);
		}

		$direct = __DIR__ . '/../db/custom_tiki.sql';
		$fetch = null;
		$check = null;

		if (isset($ini['source.type'])) {
			switch ($ini['source.type']) {
			case 'local':
				$direct = $ini['source.file'];
				break;
			case 'http':
				$fetch = $ini['source.file'];
				if (isset($ini['source.md5'])) {
					$check = $ini['source.md5'];
				}
				break;
			}
		}

		if (is_readable($direct)) {
			return $direct;
		}

		if (! $fetch) {
			return;
		}

		$cacheFile = __DIR__ . '/../temp/cache/sql' . md5($fetch);

		if (is_readable($cacheFile)) {
			return $cacheFile;
		}

		$read = fopen($fetch, 'r');
		$write = fopen($cacheFile, 'w+');

		if ($read && $write) {
			while (! feof($read)) {
				fwrite($write, fread($read, 1024 * 100));
			}

			fclose($read);
			fclose($write);

			if (! $check || $check == md5_file($cacheFile)) {
				return $cacheFile;
			} else {
				unlink($cacheFile);
			}
		}
	} // }}}
	
}
