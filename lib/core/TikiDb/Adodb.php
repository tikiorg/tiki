<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class TikiDb_Adodb extends TikiDb
{
	private $db;

	function __construct( $db ) // {{{
	{
		if (!$db) {
			die ("Invalid db object passed to TikiDB constructor");
		}

		$this->db=$db;
		$this->db->SetFetchMode(ADODB_FETCH_ASSOC);
	} // }}}

	function __destruct() // {{{
	{
		if ($this->db) {
			$this->db->Close();
		}
	} // }}}

	function qstr( $str ) // {{{
	{
		return $this->db->quote($str);
	} // }}}

	function query( $query = null, $values = null, $numrows = -1, $offset = -1, $reporterrors = parent::ERR_DIRECT ) // {{{
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

			$this->handleQueryError($query, $values, $result, $reporterrors);
		}

		global $num_queries;
		$num_queries++;
		$this->setQuery(null);

		return $result;
	} // }}}
}
