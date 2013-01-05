<?php
/**
 * abstract class providing Database functions for use in Tiki.
 *
 * @package \lib
 * @subpackage core
 * @copyright (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project. All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * @licence Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 */
// $Id$

/**
 * Database functions for use in Tiki.
 * @package \lib
 * @subpackage core
 */
abstract class TikiDb
{
	/**
	 * instance to use
	 * 
	 * @access private
	 * @var mixed $instance
	 */
	private static $instance;

	/**
	 * Error Handler
	 * 
	 * @access private
	 * @var mixed $errorHandler
	 */
	private $errorHandler;
	/**
	 * Error Message
	 * @access private
	 * @var string $errorMessage
	 */
	private $errorMessage;
	/**
	 * Server type
	 * @access private
	 * @var mixed $serverType
	 */
	private $serverType;

	/**
	 * saved query
	 * @access protected
	 * @var mixed $savedQuery
	 */
	protected $savedQuery;

	/**
	 * the Table prefix
	 * @access private
	 * @var string $tablePrefix
	 */
	private $tablePrefix;
	/**
	 * the prefix to use for the Users table
	 * @access private
	 * @var string $usersTablePrefix
	 */
	private $usersTablePrefix;

	/**
	 * getter function
	 * @return mixed
	 */
	public static function get() // {{{
	{
		return self::$instance;
	} // }}}

	/**
	 * setter function
	 * @param TikiDb $instance
	 *
	 * @return TikiDb
	 */
	public static function set(TikiDb $instance) // {{{
	{
		return self::$instance = $instance;
	} // }}}

	/**
	 * records the start time 
	 * @return mixed
	 */
	function startTimer() // {{{
	{
		list($micro, $sec) = explode(' ', microtime());
		return $micro + $sec;
	} // }}}

	/**
	 * records the end time
	 * @param $starttime
	 */
	function stopTimer($starttime) // {{{
	{
		global $elapsed_in_db;
		list($micro, $sec) = explode(' ', microtime());
		$now = $micro + $sec;
		$elapsed_in_db += $now - $starttime;
	} // }}}

	/**
	 * surrounds the string $str with quotes
	 * @param string $str the string to be quoted
	 *
	 * @return mixed
	 */
	abstract function qstr($str);

	/**
	 * runs a Database query
	 * 
	 * @param null $query
	 * @param null $values
	 * @param      $numrows
	 * @param      $offset
	 * @param bool $reporterrors
	 *
	 * @return mixed
	 */
	abstract function query($query = null, $values = null, $numrows = -1, $offset = -1, $reporterrors = true);

	/**
	 * @return bool|mixed
	 */
	function lastInsertId() // {{{
	{
		return $this->getOne('SELECT LAST_INSERT_ID()');
	} // }}}

	/**
	 * gets the Id of the last inserted item
	 * @param      $query
	 * @param      $error
	 * @param null $values
	 * @param      $numrows
	 * @param      $offset
	 *
	 * @return mixed
	 */
	function queryError($query, &$error, $values = null, $numrows = -1, $offset = -1) // {{{
	{
		$this->errorMessage = '';
		$result = $this->query($query, $values, $numrows, $offset, false);
		$error = $this->errorMessage;

		return $result;
	} // }}}

	/**
	 * returns a single result
	 * @param      $query
	 * @param null $values
	 * @param bool $reporterrors
	 * @param int  $offset
	 *
	 * @return bool|mixed
	 */
	function getOne($query, $values = null, $reporterrors = true, $offset = 0) // {{{
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

	/**
	 * returns all available results
	 * @param null $query
	 * @param null $values
	 * @param      $numrows
	 * @param      $offset
	 * @param bool $reporterrors
	 *
	 * @return array
	 */
	function fetchAll($query = null, $values = null, $numrows = -1, $offset = -1, $reporterrors = true) // {{{
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

	/**
	 * creates an array mapping the returned data
	 * @param null $query
	 * @param null $values
	 * @param      $numrows
	 * @param      $offset
	 * @param bool $reporterrors
	 *
	 * @return array
	 */
	function fetchMap($query = null, $values = null, $numrows = -1, $offset = -1, $reporterrors = true) // {{{
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

	/**
	 * sets the Error Handler to use
	 * @param TikiDb_ErrorHandler $handler
	 */
	function setErrorHandler(TikiDb_ErrorHandler $handler) // {{{
	{
		$this->errorHandler = $handler;
	} // }}}

	/**
	 * sets the Table prefix to use
	 * @param $prefix
	 */
	function setTablePrefix($prefix) // {{{
	{
		$this->tablePrefix = $prefix;
	} // }}}

	/**
	 * sets the Users table prefix to use
	 * @param $prefix
	 */
	function setUsersTablePrefix($prefix) // {{{
	{
		$this->usersTablePrefix = $prefix;
	} // }}}

	/**
	 * discovers the server type
	 * @return mixed
	 */
	function getServerType() // {{{
	{
		return $this->serverType;
	} // }}}

	/**
	 * sets the server type
	 * @param $type
	 */
	function setServerType($type) // {{{
	{
		$this->serverType = $type;
	} // }}}

	/**
	 * retrieves the Error message
	 * @return string
	 */
	function getErrorMessage() // {{{
	{
		return $this->errorMessage;
	} // }}}

	/**
	 * sets the Error message
	 * @param string $message
	 */
	protected function setErrorMessage($message) // {{{
	{
		$this->errorMessage = $message;
	} // }}}

	/**
	 * handles any Error generated by the query
	 * @param $query
	 * @param $values
	 * @param $result
	 *
	 * @throws TikiDb_Exception
	 */
	protected function handleQueryError($query, $values, $result) // {{{
	{
		if ( $this->errorHandler )
			$this->errorHandler->handle($this, $query, $values, $result);
		else {
			throw new TikiDb_Exception($this->getErrorMessage());
		}
	} // }}}

	/**
	 * @param $query
	 */
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

	/**
	 * @param $sort_mode
	 *
	 * @return string
	 */
	function convertSortMode( $sort_mode ) // {{{
	{
		if ( !$sort_mode ) {
			return '';
		}
		// parse $sort_mode for evil stuff
		$sort_mode = str_replace('pref:', '', $sort_mode);
		$sort_mode = preg_replace('/[^A-Za-z_,.]/', '', $sort_mode);

		if ($sort_mode == 'random') {
			return "RAND()";
		}

		$sorts=explode(',', $sort_mode);
		foreach ($sorts as $k => $sort) {

			// force ending to either _asc or _desc unless it's "random"
			$sep = strrpos($sort, '_');
			$dir = substr($sort, $sep);
			if (($dir !== '_asc') && ($dir !== '_desc')) {
				if ( $sep != (strlen($sort) - 1) ) {
					$sort .= '_';
				}
				$sort .= 'asc';
			}

			$sort = preg_replace('/_asc$/', '` asc', $sort);
			$sort = preg_replace('/_desc$/', '` desc', $sort);
			$sort = '`' . $sort;
			$sort = str_replace('.', '`.`', $sort);
			$sorts[$k]=$sort;
		}

		$sort_mode=implode(',', $sorts);
		return $sort_mode;
	} // }}}

	/**
	 * @return mixed
	 */
	function getQuery() // {{{
	{
		return $this->savedQuery;
	} // }}}

	/**
	 * @param $sql
	 */
	function setQuery( $sql ) // {{{
	{
		$this->savedQuery = $sql;
	} // }}}

	/**
	 * @param $field
	 * @param $ifNull
	 *
	 * @return string
	 */
	function ifNull( $field, $ifNull ) // {{{
	{
		return " COALESCE($field, $ifNull) ";
	} // }}}

	/**
	 * @param $field
	 * @param $values
	 * @param $bindvars
	 *
	 * @return string
	 */
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

	/**
	 * @param $objects
	 * @param $table
	 * @param $childKey
	 * @param $parentKey
	 */
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

	/**
	 * @return string
	 */
	function concat() // {{{
	{
		$arr = func_get_args();

		// suggestion by andrew005@mnogo.ru
		$s = implode(',', $arr);
		if (strlen($s) > 0) return "CONCAT($s)";
		else return '';
	} // }}}

	/**
	 * @param $tableName
	 *
	 * @return TikiDb_Table
	 */
	function table($tableName) // {{{
	{
		return new TikiDb_Table($this, $tableName);
	} // }}}

	/**
	 * @return TikiDb_Transaction
	 */
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
		$engines = array();
		$result = $this->query('show engines');
		if ($result) {
			while ($res = $result->fetchRow()) {
				$engines[] = $res['Engine'];
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
		$engine = '';
		$result = $this->query('SHOW TABLE STATUS LIKE ?', 'tiki_schema');
		if ($result) {
			$res = $result->fetchRow();
			$engine  = $res['Engine'];
		}
		return $engine;
	}

	/**
	 * Determine if MySQL fulltext search is supported by the current DB engine
	 * Assumes that all tables use the same table engine
	 * @return true if it is supported, otherwise false
	 */ 
	function isMySQLFulltextSearchSupported()
	{
		$currentEngine = $this->getCurrentEngine();
		if (strcasecmp($currentEngine, "MyISAM") == 0) {
			return true;
		}
		return false;
	}
}
