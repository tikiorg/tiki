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
 * provides a wrapper between Tiki and the AdoDb Library
 * @package Tiki\Core\TikiDb
 */
class TikiDb_Adodb extends TikiDb
{

	/**
	 * a TikiDb instance
	 * @var $db
	 */
	private $db;


	/**
	 * constructs an instance of TikiDb_Adodb
	 * @param $db
	 */
	function __construct( $db ) // {{{
	{
		if (!$db) {
			die ("Invalid db object passed to TikiDB constructor");
		}

		$this->db=$db;
	} // }}}

	/**
	 * surrounds the provided string with quotes ready for use in a query function
	 * @param string $str
	 *
	 * @return mixed
	 */function qstr( $str ) // {{{
	{
		return $this->db->quote($str);
	} // }}}

	/**
	 * executes a query on the Database, returns the query result or an error on failure.
	 * @param string $query
	 * @param mixed  $values
	 * @param int    $numrows
	 * @param int    $offset
	 * @param bool   $reporterrors
	 * @return mixed
	 */function query( $query = null, $values = null, $numrows = -1, $offset = -1, $reporterrors = true ) // {{{
	{
		global $num_queries;
		$num_queries++;

		$numrows = intval($numrows);
		$offset = intval($offset);
		if ( $query == null ) {
			$query = $this->getQuery();
		}
		$this->convertQueryTablePrefixes($query);

		$starttime=$this->startTimer();
		if ($numrows == -1 && $offset == -1)
			$result = $this->db->Execute($query, $values);
		else
			$result = $this->db->SelectLimit($query, $numrows, $offset, $values);

		$this->stopTimer($starttime);

		if (!$result ) {
			$this->setErrorMessage($this->db->ErrorMsg());

			if ($reporterrors) {
				$this->handleQueryError($query, $values, $result);
			}
		}

		global $num_queries;
		$num_queries++;
		$this->setQuery(null);

		return $result;
	} // }}}
}
