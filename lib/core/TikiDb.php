<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

abstract class TikiDb
{
	private static $instance;

	private $errorHandler;
	private $errorMessage;
	private $serverType;

	protected $savedQuery;

	private $tablePrefix;
	private $usersTablePrefix;

	public static function get() // {{{
	{
		return self::$instance;
	} // }}}

	public static function set( TikiDb $instance ) // {{{
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
		$now=$micro + $sec;
		$elapsed_in_db+=$now - $starttime;
	} // }}}

	abstract function qstr( $str );

	abstract function query( $query = null, $values = null, $numrows = -1, $offset = -1, $reporterrors = true );

	function lastInsertId() // {{{
	{
		return $this->getOne( 'SELECT LAST_INSERT_ID()' );
	} // }}}

	function queryError( $query, &$error, $values = null, $numrows = -1, $offset = -1 ) // {{{
	{
		$this->errorMessage = '';
		$result = $this->query( $query, $values, $numrows, $offset, false );
		$error = $this->errorMessage;

		return $result;
	} // }}}

	function getOne( $query, $values = null, $reporterrors = true, $offset = 0 ) // {{{
	{
		$result = $this->query( $query, $values, 1, $offset, $reporterrors );

		if ( $result ) {
			$res = $result->fetchRow();

			if ( empty( $res ) ) {
				return $res;
			}
		
			return reset( $res );
		}

		return false;
	} // }}}

	function fetchAll( $query = null, $values = null, $numrows = -1, $offset = -1, $reporterrors = true ) // {{{
	{
		$result = $this->query( $query, $values, $numrows, $offset, $reporterrors );

		$rows = array();
		
		if ($result) {
			while( $row = $result->fetchRow() ) {
				$rows[] = $row;
			}
		}
		return $rows;
	} // }}}

	function fetchMap( $query = null, $values = null, $numrows = -1, $offset = -1, $reporterrors = true ) // {{{
	{
		$result = $this->fetchAll( $query, $values, $numrows, $offset, $reporterrors );

		$map = array();

		foreach( $result as $row ) {
			$key = array_shift( $row );
			$value = array_shift( $row );

			$map[ $key ] = $value;
		}

		return $map;
	} // }}}

	function setErrorHandler( TikiDb_ErrorHandler $handler ) // {{{
	{
		$this->errorHandler = $handler;
	} // }}}

	function setTablePrefix( $prefix ) // {{{
	{
		$this->tablePrefix = $prefix;
	} // }}}

	function setUsersTablePrefix( $prefix ) // {{{
	{
		$this->usersTablePrefix = $prefix;
	} // }}}

	function getServerType() // {{{
	{
		return $this->serverType;
	} // }}}

	function setServerType( $type ) // {{{
	{
		$this->serverType = $type;
	} // }}}

	function getErrorMessage() // {{{
	{
		return $this->errorMessage;
	} // }}}

	protected function setErrorMessage( $message ) // {{{
	{
		$this->errorMessage = $message;
	} // }}}

	protected function handleQueryError( $query, $values, $result ) // {{{
	{
		if( $this->errorHandler )
			$this->errorHandler->handle( $this, $query, $values, $result );
		else {
			throw new TikiDb_Exception( $this->getErrorMessage() );
		}
	} // }}}

	protected function convertQueryTablePrefixes( &$query ) // {{{
	{
		$db_table_prefix = $this->tablePrefix;
		$common_users_table_prefix = $this->usersTablePrefix;

		if ( !is_null($db_table_prefix) && !empty($db_table_prefix) ) {

			if( !is_null($common_users_table_prefix) && !empty($common_users_table_prefix) ) {
				$query = str_replace("`users_", "`".$common_users_table_prefix."users_", $query);
			} else {
				$query = str_replace("`users_", "`".$db_table_prefix."users_", $query);
			}

			$query = str_replace("`tiki_", "`".$db_table_prefix."tiki_", $query);
			$query = str_replace("`messu_", "`".$db_table_prefix."messu_", $query);
			$query = str_replace("`sessions", "`".$db_table_prefix."sessions", $query);
		}
	} // }}}

	function convertSortMode( $sort_mode ) // {{{
	{
		if ( !$sort_mode ) {
			return '';
		}
		// parse $sort_mode for evil stuff
		$sort_mode = str_replace('pref:','',$sort_mode);
		$sort_mode = preg_replace('/[^A-Za-z_,.]/', '', $sort_mode);

		if ($sort_mode == 'random') {
			return "RAND()";
		}

		$sorts=explode(',', $sort_mode);
		foreach($sorts as $k => $sort) {

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

	function in( $field, $values, &$bindvars ) // {{{
	{
		$parts = explode('.', $field);
		foreach($parts as &$part)
			$part = '`' . $part . '`';
		$field = implode('.', $parts);
		$bindvars = array_merge( $bindvars, $values );

		if( count( $values ) > 0 ) {
			$values = rtrim( str_repeat( '?,', count( $values ) ), ',' );
			return " $field IN( $values ) ";
		} else {
			return " 0 ";
		}
	} // }}}

	function parentObjects(&$objects, $table, $childKey, $parentKey) // {{{
	{
		$query = "select `$childKey`, `$parentKey` from `$table` where `$childKey` in (".implode(',',array_fill(0, count($objects),'?')).')';
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
		$s = implode(',',$arr);
		if (strlen($s) > 0) return "CONCAT($s)";
		else return '';
	} // }}}

	function table($tableName) // {{{
	{
		return new TikiDb_Table($this, $tableName);
	} // }}}
}
