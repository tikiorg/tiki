<?php
/**
 * TikiDb class extender.
 *
 * @package   Tiki
 * @subpackage Core\TikiDb
 * @copyright (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project. All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * @license   LGPL. See license.txt for more details
 */
// $Id$

/**
 * TikiDb Bridging class
 * @package Tiki\Core\TikiDb
 */
class TikiDb_Bridge extends TikiDb
{
	/**
	 *
	 */
	function startTimer() // {{{
	{
		self::get()->startTimer();
	} // }}}

	/**
	 * @param $starttime
	 */
	function stopTimer($starttime) // {{{
	{
		self::get()->stopTimer($starttime);
	} // }}}

	/**
	 * @param $str
	 *
	 * @return mixed
	 */function qstr( $str ) // {{{
	{
		return self::get()->qstr($str);
	} // }}}

	/**
	 * @param null $query
	 * @param null $values
	 * @param      $numrows
	 * @param      $offset
	 * @param bool $reporterrors
	 * @return mixed
	 */function query( $query = null, $values = null, $numrows = -1, $offset = -1, $reporterrors = true ) // {{{
	{
		return self::get()->query($query, $values, $numrows, $offset, $reporterrors);
	} // }}}

	/**
	 * @param null $query
	 * @param null $values
	 * @param      $numrows
	 * @param      $offset
	 * @param bool $reporterrors
	 * @return mixed
	 */function fetchAll( $query = null, $values = null, $numrows = -1, $offset = -1, $reporterrors = true ) // {{{
	{
		return self::get()->fetchAll($query, $values, $numrows, $offset, $reporterrors);
	} // }}}

	/**
	 * @param      $query
	 * @param      $error
	 * @param null $values
	 * @param      $numrows
	 * @param      $offset
	 * @return mixed
	 */function queryError( $query, &$error, $values = null, $numrows = -1, $offset = -1 ) // {{{
	{
		return self::get()->queryError($query, $error, $values, $numrows, $offset);
	} // }}}

	/**
	 * @param      $query
	 * @param null $values
	 * @param bool $reporterrors
	 * @param int  $offset
	 * @return mixed
	 */function getOne( $query, $values = null, $reporterrors = true, $offset = 0 ) // {{{
	{
		return self::get()->getOne($query, $values, $reporterrors, $offset);
	} // }}}

	/**
	 * @param TikiDb_ErrorHandler $handler
	 */function setErrorHandler( TikiDb_ErrorHandler $handler ) // {{{
	{
		self::get()->setErrorHandler($handler);
	} // }}}

	/**
	 * @param $prefix
	 */function setTablePrefix( $prefix ) // {{{
	{
		self::get()->setTablePrefix($prefix);
	} // }}}

	/**
	 * @param $prefix
	 */function setUsersTablePrefix( $prefix ) // {{{
	{
		self::get()->setUsersTablePrefix($prefix);
	} // }}}

	/**
	 * @return mixed
	 */function getServerType() // {{{
	{
		return self::get()->getServerType();
	} // }}}

	/**
	 * @param $type
	 */function setServerType( $type ) // {{{
	{
		self::get()->setServerType($type);
	} // }}}

	/**
	 * @return mixed
	 */function getErrorMessage() // {{{
	{
		return self::get()->getErrorMessage();
	} // }}}

	/**
	 * @param $message
	 */protected function setErrorMessage( $message ) // {{{
	{
		self::get()->setErrorMessage($message);
	} // }}}

	/**
	 * @param $query
	 * @param $values
	 * @param $result
	 */protected function handleQueryError( $query, $values, $result ) // {{{
	{
		self::get()->handleQueryError($query, $values, $result);
	} // }}}

	/**
	 * @param $query
	 */protected function convertQueryTablePrefixes( &$query ) // {{{
	{
		self::get()->convertQueryTablePrefixes($query);
	} // }}}

	/**
	 * @param $sort_mode
	 * @return mixed
	 */function convertSortMode( $sort_mode ) // {{{
	{
		return self::get()->convertSortMode($sort_mode);
	} // }}}

	/**
	 * @return mixed
	 */function getQuery() // {{{
	{
		return self::get()->getQuery();
	} // }}}

	/**
	 * @param $sql
	 * @return mixed
	 */function setQuery( $sql ) // {{{
	{
		return self::get()->setQuery($sql);
	} // }}}

	/**
	 * @param $field
	 * @param $ifNull
	 * @return mixed
	 */function ifNull( $field, $ifNull ) // {{{
	{
		return self::get()->ifNull($field, $ifNull);
	} // }}}

	/**
	 * @param $field
	 * @param $values
	 * @param $bindvars
	 * @return mixed
	 */function in( $field, $values, &$bindvars ) // {{{
	{
		return self::get()->in($field, $values, $bindvars);
	} // }}}

	/**
	 * @return mixed
	 */function concat() // {{{
	{
		$arr = func_get_args();
		return call_user_func_array(array( self::get(), 'concat' ), $arr);
	} // }}}

	/**
	 * @param $tableName
	 * @return mixed
	 */function table($tableName) // {{{
	{
		return self::get()->table($tableName);
	} // }}}
}
