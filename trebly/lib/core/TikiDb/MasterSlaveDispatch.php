<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once 'lib/core/TikiDb.php';

class TikiDb_MasterSlaveDispatch extends TikiDb
{
	private $master;
	private $slave;

	private $lastUsed;

	function __construct( TikiDb $master, TikiDb $slave ) {
		$this->master = $master;
		$this->slave = $slave;
		$this->lastUsed = $slave;
	}

	function startTimer() // {{{
	{
		$this->getApplicable()->startTimer();
	} // }}}

	function stopTimer($starttime) // {{{
	{
		$this->getApplicable()->stopTimer($starttime);
	} // }}}

	function qstr( $str ) // {{{
	{
		return $this->getApplicable()->qstr( $str );
	} // }}}

	function query( $query = null, $values = null, $numrows = -1, $offset = -1, $reporterrors = true ) // {{{
	{
		return $this->getApplicable( $query )->query( $query, $values, $numrows, $offset, $reporterrors );
	} // }}}

	function queryError( $query, &$error, $values = null, $numrows = -1, $offset = -1 ) // {{{
	{
		return $this->getApplicable( $query )->queryError( $query, $error, $values, $numrows, $offset );
	} // }}}

	function getOne( $query, $values = null, $reporterrors = true, $offset = 0 ) // {{{
	{
		return $this->getApplicable( $query )->getOne( $query, $values, $reporterrors, $offset );
	} // }}}

	function setErrorHandler( TikiDb_ErrorHandler $handler ) // {{{
	{
		$this->getApplicable()->setErrorHandler( $handler );
	} // }}}

	function setTablePrefix( $prefix ) // {{{
	{
		$this->getApplicable()->setTablePrefix( $prefix );
	} // }}}

	function setUsersTablePrefix( $prefix ) // {{{
	{
		$this->getApplicable()->setUsersTablePrefix( $prefix );
	} // }}}

	function getServerType() // {{{
	{
		return $this->getApplicable()->getServerType();
	} // }}}

	function setServerType( $type ) // {{{
	{
		$this->getApplicable()->setServerType( $type );
	} // }}}

	function getErrorMessage() // {{{
	{
		return $this->lastUsed->getErrorMessage();
	} // }}}

	protected function setErrorMessage( $message ) // {{{
	{
		$this->getApplicable()->setErrorMessage( $message );
	} // }}}

	protected function handleQueryError( $query, $values, $result ) // {{{
	{
		$this->getApplicable()->handleQueryError( $query, $values, $result );
	} // }}}

	protected function convertQueryTablePrefixes( &$query ) // {{{
	{
		$this->getApplicable( $query )->convertQueryTablePrefixes( $query );
	} // }}}

	function convertSortMode( $sort_mode ) // {{{
	{
		return $this->getApplicable()->convertSortMode( $sort_mode );
	} // }}}

	function getQuery() // {{{
	{
		return $this->getApplicable()->getQuery();
	} // }}}

	function setQuery( $sql ) // {{{
	{
		return $this->getApplicable()->setQuery( $sql );
	} // }}}

	function ifNull( $field, $ifNull ) // {{{
	{
		return $this->getApplicable()->ifNull( $field, $ifNull );
	} // }}}

	function in( $field, $values, &$bindvars ) // {{{
	{
		return $this->getApplicable()->in( $field, $values, $bindvars );
	} // }}}

	function concat() // {{{
	{
		$arr = func_get_args();
		return call_user_func_array( array( $this->getApplicable(), 'concat' ), $arr );
	} // }}}

	private function getApplicable( $query = '' ) {
		if( empty( $query ) ) {
			return $this->lastUsed = $this->slave;
		}

		// If it's a write
		// regex is for things starting with select in any case with potential
		// whitespace in front of it
		if (!preg_match('/^\s*select/i', $query)) {
			return $this->lastUsed = $this->master;
		}

		return $this->lastUsed = $this->slave;
	}
}
