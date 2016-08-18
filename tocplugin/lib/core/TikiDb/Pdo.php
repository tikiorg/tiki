<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class TikiDb_Pdo_Result
{
	public $result;
	public $numrows;

	function __construct ($result)
	{
		$this->result = &$result;
		$this->numrows = count($this->result);
	}

	function fetchRow()
	{
		return is_array($this->result) ? array_shift($this->result) : 0;
	}

	function numRows()
	{
		return $this->numrows;
	}
}

class TikiDb_Pdo extends TikiDb
{
	private $db;

	function __construct( $db ) // {{{
	{
		if (!$db) {
			die ("Invalid db object passed to TikiDB constructor");
		}

		$this->db=$db;
		$this->setServerType($db->getAttribute(PDO::ATTR_DRIVER_NAME));
	} // }}}

	function qstr( $str ) // {{{
	{
		return $this->db->quote($str);
	} // }}}

	private function _query($query, $values = null, $numrows = -1, $offset = -1) // {{{
	{
		global $num_queries;
		$num_queries++;

		$numrows = intval($numrows);
		$offset = intval($offset);
		if ( $query == null ) {
			$query = $this->getQuery();
		}

		$this->convertQueryTablePrefixes($query);

		if ( $offset != -1 && $numrows != -1 )
			$query .= " LIMIT $numrows OFFSET $offset";
		elseif ( $numrows != -1 )
			$query .= " LIMIT $numrows";

		$starttime=$this->startTimer();

		$result = false;
		if ($values) {
			if ( @ $pq = $this->db->prepare($query) ) {
				if (!is_array($values)) {
					$values = array($values);
				}
				$result = $pq->execute($values);
			}
		} else {
			$result = @ $this->db->query($query);
		}

		$this->stopTimer($starttime);

		if ( $result === false) {
			if ( !$values || ! $pq) { // Query preparation or query failed 
				$tmp = $this->db->errorInfo();
			} else { // Prepared query failed to execute
				$tmp = $pq->errorInfo();
				$pq->closeCursor();
			}
			$this->setErrorMessage($tmp[2]);
			return false;
		} else {
			$this->setErrorMessage("");
			if (($values && !$pq->columnCount()) || (!$values && !$result->columnCount())) {
				return array(); // Return empty result set for statements of manipulation
			} elseif ( !$values) {
				return $result->fetchAll(PDO::FETCH_ASSOC);
			} else {
				return $pq->fetchAll(PDO::FETCH_ASSOC);
			}
		}
	} // }}}

	function fetchAll($query = null, $values = null, $numrows = -1, $offset = -1, $reporterrors = parent::ERR_DIRECT ) // {{{
	{
		$result = $this->_query($query, $values, $numrows, $offset);
		if (! is_array($result) ) {
			$this->handleQueryError($query, $values, $result, $reporterrors);
		}

		return $result;
	} // }}}

	function query($query = null, $values = null, $numrows = -1, $offset = -1, $reporterrors = self::ERR_DIRECT ) // {{{
	{
		$result = $this->_query($query, $values, $numrows, $offset);
		if ( $result === false ) {
			$this->handleQueryError($query, $values, $result, $reporterrors);
		}

		return new TikiDb_Pdo_Result($result);
	} // }}}
}
