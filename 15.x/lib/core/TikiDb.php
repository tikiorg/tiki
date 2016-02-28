<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

abstract class TikiDb
{
	const ERR_DIRECT = true;
	const ERR_NONE = false;
	const ERR_EXCEPTION = 'exception';

	private static $instance;

	private $errorHandler;
	private $errorMessage;
	private $serverType;

	protected $savedQuery;

	private $tablePrefix;
	private $usersTablePrefix;

	/**
	 * @return TikiDb
	 */
	public static function get() // {{{
	{
		return self::$instance;
	} // }}}

	public static function set(TikiDb $instance) // {{{
	{
		return self::$instance = $instance;
	} // }}}

	function startTimer() // {{{
	{
		list($micro, $sec) = explode(' ', microtime());
		return $micro + $sec;
	} // }}}

	function stopTimer($starttime) // {{{
	{
		global $elapsed_in_db;
		list($micro, $sec) = explode(' ', microtime());
		$now = $micro + $sec;
		$elapsed_in_db += $now - $starttime;
	} // }}}

	abstract function qstr($str);

	abstract function query($query = null, $values = null, $numrows = -1, $offset = -1, $reporterrors = self::ERR_DIRECT);

	function lastInsertId() // {{{
	{
		return $this->getOne('SELECT LAST_INSERT_ID()');
	} // }}}

	function queryError($query, &$error, $values = null, $numrows = -1, $offset = -1) // {{{
	{
		$this->errorMessage = '';
		$result = $this->query($query, $values, $numrows, $offset, self::ERR_NONE);
		$error = $this->errorMessage;

		return $result;
	} // }}}

	function queryException($query, $values = null, $numrows = -1, $offset = -1) // {{{
	{
		return $this->query($query, $values, $numrows, $offset, self::ERR_EXCEPTION);
	} // }}}

	function getOne($query, $values = null, $reporterrors = self::ERR_DIRECT, $offset = 0) // {{{
	{
		$result = $this->query($query, $values, 1, $offset, $reporterrors);

		if ($result) {
			$res = $result->fetchRow();

			if (empty($res)) {
				return $res;
			}

			return reset($res);
		}

		return false;
	} // }}}

	function fetchAll($query = null, $values = null, $numrows = -1, $offset = -1, $reporterrors = self::ERR_DIRECT) // {{{
	{
		$result = $this->query($query, $values, $numrows, $offset, $reporterrors);

		$rows = array();

		if ($result) {
			while ($row = $result->fetchRow()) {
				$rows[] = $row;
			}
		}
		return $rows;
	} // }}}

	function fetchMap($query = null, $values = null, $numrows = -1, $offset = -1, $reporterrors = self::ERR_DIRECT) // {{{
	{
		$result = $this->fetchAll($query, $values, $numrows, $offset, $reporterrors);

		$map = array();

		foreach ($result as $row) {
			$key = array_shift($row);
			$value = array_shift($row);

			$map[$key] = $value;
		}

		return $map;
	} // }}}

	function setErrorHandler(TikiDb_ErrorHandler $handler) // {{{
	{
		$this->errorHandler = $handler;
	} // }}}

	function setTablePrefix($prefix) // {{{
	{
		$this->tablePrefix = $prefix;
	} // }}}

	function setUsersTablePrefix($prefix) // {{{
	{
		$this->usersTablePrefix = $prefix;
	} // }}}

	function getServerType() // {{{
	{
		return $this->serverType;
	} // }}}

	function setServerType($type) // {{{
	{
		$this->serverType = $type;
	} // }}}

	function getErrorMessage() // {{{
	{
		return $this->errorMessage;
	} // }}}

	protected function setErrorMessage($message) // {{{
	{
		$this->errorMessage = $message;
	} // }}}

	protected function handleQueryError($query, $values, $result, $mode) // {{{
	{
		if ($mode === self::ERR_NONE) {
			return null;
		} elseif ($mode === self::ERR_DIRECT && $this->errorHandler) {
			$this->errorHandler->handle($this, $query, $values, $result);
		} elseif ($mode === self::ERR_EXCEPTION || ! $this->errorHandler) {
			TikiDb_Exception::classify($this->errorMessage);
		}
	} // }}}

	protected function convertQueryTablePrefixes( &$query ) // {{{
	{
		$db_table_prefix = $this->tablePrefix;
		$common_users_table_prefix = $this->usersTablePrefix;

		if ( !is_null($db_table_prefix) && !empty($db_table_prefix) ) {

			if ( !is_null($common_users_table_prefix) && !empty($common_users_table_prefix) ) {
				$query = str_replace("`users_", "`".$common_users_table_prefix."users_", $query);
			} else {
				$query = str_replace("`users_", "`".$db_table_prefix."users_", $query);
			}

			$query = str_replace("`tiki_", "`".$db_table_prefix."tiki_", $query);
			$query = str_replace("`messu_", "`".$db_table_prefix."messu_", $query);
			$query = str_replace("`sessions", "`".$db_table_prefix."sessions", $query);
		}
	} // }}}

	function convertSortMode( $sort_mode, $fields = null ) // {{{
	{
		if ( !$sort_mode ) {
			return '1';
		}
		// parse $sort_mode for evil stuff
		$sort_mode = str_replace('pref:', '', $sort_mode);
		$sort_mode = preg_replace('/[^A-Za-z_,.]/', '', $sort_mode);

		// Do not process empty sort modes
		if ( empty($sort_mode) ) {
			return '1';
		}

		if ($sort_mode == 'random') {
			return "RAND()";
		}

		$sorts = array();
		foreach (explode(',', $sort_mode) as $sort) {

			// force ending to either _asc or _desc unless it's "random"
			$sep = strrpos($sort, '_');
			$dir = substr($sort, $sep);
			if (($dir !== '_asc') && ($dir !== '_desc')) {
				if ( $sep != (strlen($sort) - 1) ) {
					$sort .= '_';
				}
				$sort .= 'asc';
			}

			// When valid fields are specified, skip those not available
			if (is_array($fields) && preg_match('/^(.*)_(asc|desc)$/', $sort, $parts)) {
				if (! in_array($parts[1], $fields)) {
					continue;
				}
			}

			$sort = preg_replace('/_asc$/', '` asc', $sort);
			$sort = preg_replace('/_desc$/', '` desc', $sort);
			$sort = '`' . $sort;
			$sort = str_replace('.', '`.`', $sort);
			$sorts[] = $sort;
		}

		if (empty($sorts)) {
			return '1';
		}

		$sort_mode=implode(',', $sorts);
		return $sort_mode;
	} // }}}

	function getQuery() // {{{
	{
		return $this->savedQuery;
	} // }}}

	function setQuery( $sql ) // {{{
	{
		$this->savedQuery = $sql;
	} // }}}

	function ifNull( $field, $ifNull ) // {{{
	{
		return " COALESCE($field, $ifNull) ";
	} // }}}

	function in($field, $values, &$bindvars) // {{{
	{
		$parts = explode('.', $field);
		foreach ($parts as &$part)
			$part = '`' . $part . '`';
		$field = implode('.', $parts);
		$bindvars = array_merge($bindvars, $values);

		if (count($values) > 0 ) {
			$values = rtrim(str_repeat('?,', count($values)), ',');
			return " $field IN( $values ) ";
		} else {
			return " 0 ";
		}
	} // }}}

	function parentObjects(&$objects, $table, $childKey, $parentKey) // {{{
	{
		$query = "select `$childKey`, `$parentKey` from `$table` where `$childKey` in (".implode(',', array_fill(0, count($objects), '?')) .')';
		foreach ($objects as $object) {
			$bindvars[] = $object['itemId'];
		}
		$result = $this->query($query, $bindvars);
		while ($res = $result->fetchRow()) {
			$ret[$res[$childKey]] = $res[$parentKey];
		}
		foreach ($objects as $i=>$object) {
			$objects[$i][$parentKey] = $ret[$object['itemId']];
		}
	} // }}}

	function concat() // {{{
	{
		$arr = func_get_args();

		// suggestion by andrew005@mnogo.ru
		$s = implode(',', $arr);
		if (strlen($s) > 0) return "CONCAT($s)";
		else return '';
	} // }}}

	function table($tableName, $autoIncrement = true) // {{{
	{
		return new TikiDb_Table($this, $tableName, $autoIncrement);
	} // }}}

	function begin() // {{{
	{
		return new TikiDb_Transaction;
	} // }}}

	/**
	* Get a list of installed engines in the MySQL instance
	* $return array of engine names
	*/
	function getEngines()
	{
		static $engines = array();
		if (empty($engines)) {
			$result = $this->query('show engines');
			if ($result) {
				while ($res = $result->fetchRow()) {
					$engines[] = $res['Engine'];
				}
			}
		}
		return $engines;
	}

	/**
	 * Check if InnoDB is an avaible engine
	 * @return true if the InnoDB engine is available
	 */
	function hasInnoDB()
	{
		$engines = $this->getEngines();
		foreach ($engines as $engine) {
			if (strcmp(strtoupper($engine), 'INNODB') == 0) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Detect the engine used in the current schema.
	 * Assumes that all tables use the same table engine
	 * @return string identifying the current engine, or an empty string if not installed
	 */
	function getCurrentEngine()
	{
		static $engine = '';
		if (empty($engine)) {
			$result = $this->query('SHOW TABLE STATUS LIKE ?', 'tiki_schema');
			if ($result) {
				$res = $result->fetchRow();
				$engine  = $res['Engine'];
			}
		}
		return $engine;
	}

	/**
	 * Determine if MySQL fulltext search is supported by the current DB engine
	 * Assumes that all tables use the same table engine.
	 * Fulltext search is assumed supported if
	 * 1) engine = MyISAM
	 * 2) engine = InnoDB and MySQL version >= 5.6
	 * @return true if it is supported, otherwise false
	 */
	function isMySQLFulltextSearchSupported()
	{
		$currentEngine = $this->getCurrentEngine();
		if (strcasecmp($currentEngine, "MyISAM") == 0) {
			return true;
		} elseif (strcasecmp($currentEngine, "INNODB") == 0) {
			$versionNr = $this->getMySQLVersionNr();
			if ($versionNr >= 5.6) {
				return true;
			} else {
				return false;
			}
		}
		return false;
	}


	/**
	 * Read the MySQL version string.
	 * @return version string
	 */
	function getMySQLVersion()
	{
		static $version = '';
		if (empty($version)) {
			$result = $this->query('select version() as Version');
			if ($result) {
				$res = $result->fetchRow();
				$version  = $res['Version'];
			}
		}
		return $version;
	}
	/**
	 * Read the MySQL version number, e.g. 5.5
	 * @return version float
	 */
	function getMySQLVersionNr()
	{
		$versionNr = 0.0;
		$version = $this->getMySQLVersion();
		$versionNr = floatval($version);
		return $versionNr;
	}

	function listTables()
	{
		$result = $this->fetchAll("show tables");
		$list = array();

		if ($result) {
			foreach ($result as $row) {
				$list[] = reset($row);
			}
		}

		return $list;
	}
	
	/*
	*	isMySQLConnSSL
	*	Check if MySQL is using an SSL connection
	*	@return true if MySQL uses SSL. Otherwise false;
	*/
	function isMySQLConnSSL() 
	{
		if(!$this->haveMySQLSSL()) {
			return false;
		}
		$result = $this->query('show status like "Ssl_cipher"');
		$ret = $result->fetchRow();
		$cypher = $ret['Value'];
		return !empty($cypher);
	}
	
	/*
	*	Check if the MySQL installation has SSL activated
	*	@return true is SSL is supported and activated on the current MySQL server
	*/
	function haveMySQLSSL()
	{
		static $haveMySQLSSL = null;
		
		if (!isset($haveMySQLSSL)) {
			$result = $this->query('show variables like "have_ssl"');
			$ret = $result->fetchRow();
			if (empty($ret)) {
				$result = $this->query('show variables like "have_openssl"');
				$ret = $result->fetchRow();
			}
			if (!isset($ret)) {
				$haveMySQLSSL = false;
			}
			$ssl = $ret['Value'];
			if (empty($ssl)) {
				$haveMySQLSSL = false;
			} else {
				$haveMySQLSSL = $ssl == 'YES';
			}
		}
		return $haveMySQLSSL;
	}
}
