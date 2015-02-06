<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class TikiDb_Bridge extends TikiDb
{
	function startTimer() // {{{
	{
		self::get()->startTimer();
	} // }}}

	function stopTimer($starttime) // {{{
	{
		self::get()->stopTimer($starttime);
	} // }}}

	function qstr( $str ) // {{{
	{
		return self::get()->qstr($str);
	} // }}}

	function query( $query = null, $values = null, $numrows = -1, $offset = -1, $reporterrors = true ) // {{{
	{
		return self::get()->query($query, $values, $numrows, $offset, $reporterrors);
	} // }}}

	function fetchAll( $query = null, $values = null, $numrows = -1, $offset = -1, $reporterrors = true ) // {{{
	{
		return self::get()->fetchAll($query, $values, $numrows, $offset, $reporterrors);
	} // }}}

	function queryError( $query, &$error, $values = null, $numrows = -1, $offset = -1 ) // {{{
	{
		return self::get()->queryError($query, $error, $values, $numrows, $offset);
	} // }}}

	function queryException( $query, $values = null, $numrows = -1, $offset = -1 ) // {{{
	{
		return self::get()->queryException($query, $values, $numrows, $offset);
	} // }}}

	function getOne( $query, $values = null, $reporterrors = true, $offset = 0 ) // {{{
	{
		return self::get()->getOne($query, $values, $reporterrors, $offset);
	} // }}}

	function setErrorHandler( TikiDb_ErrorHandler $handler ) // {{{
	{
		self::get()->setErrorHandler($handler);
	} // }}}

	function setTablePrefix( $prefix ) // {{{
	{
		self::get()->setTablePrefix($prefix);
	} // }}}

	function setUsersTablePrefix( $prefix ) // {{{
	{
		self::get()->setUsersTablePrefix($prefix);
	} // }}}

	function getServerType() // {{{
	{
		return self::get()->getServerType();
	} // }}}

	function setServerType( $type ) // {{{
	{
		self::get()->setServerType($type);
	} // }}}

	function getErrorMessage() // {{{
	{
		return self::get()->getErrorMessage();
	} // }}}

	protected function setErrorMessage( $message ) // {{{
	{
		self::get()->setErrorMessage($message);
	} // }}}

	protected function handleQueryError( $query, $values, $result, $mode ) // {{{
	{
		self::get()->handleQueryError($query, $values, $result, $mode);
	} // }}}

	protected function convertQueryTablePrefixes( &$query ) // {{{
	{
		self::get()->convertQueryTablePrefixes($query);
	} // }}}

	function convertSortMode( $sort_mode, $fields = null ) // {{{
	{
		return self::get()->convertSortMode($sort_mode, $fields);
	} // }}}

	function getQuery() // {{{
	{
		return self::get()->getQuery();
	} // }}}

	function setQuery( $sql ) // {{{
	{
		return self::get()->setQuery($sql);
	} // }}}

	function ifNull( $field, $ifNull ) // {{{
	{
		return self::get()->ifNull($field, $ifNull);
	} // }}}

	function in( $field, $values, &$bindvars ) // {{{
	{
		return self::get()->in($field, $values, $bindvars);
	} // }}}

	function concat() // {{{
	{
		$arr = func_get_args();
		return call_user_func_array(array( self::get(), 'concat' ), $arr);
	} // }}}

	function table($tableName, $autoIncrement = true) // {{{
	{
		return self::get()->table($tableName, $autoIncrement);
	} // }}}
}
