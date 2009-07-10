<?php

require_once 'TikiDb.php';

class TikiDb_Adodb extends TikiDb {
	private $db;

	function __construct( $db ) // {{{
	{
		if (!$db) {
			die ("Invalid db object passed to TikiDB constructor");
		}

		$this->db=$db;

		global $ADODB_LASTDB;
		$this->setServerType( $ADODB_LASTDB );
	} // }}}

	function qstr( $str ) // {{{
	{
		if (function_exists('mysql_real_escape_string')) {
			return "'" . mysql_real_escape_string($str). "'";
		} else {
			return "'" . mysql_escape_string($str). "'";
		}
	} // }}}

	function query( $query, $values = null, $numrows = -1, $offset = -1, $reporterrors = true ) // {{{
	{
		global $num_queries;
		$num_queries++;

		$numrows = intval($numrows);
		$offset = intval($offset);
		if ( $query == null ) {
			$query = $this->getQuery();
		}
		$this->convertQuery($query);
		$this->convertQueryTablePrefixes( $query );

		$starttime=$this->startTimer();
		if ($numrows == -1 && $offset == -1)
			$result = $this->db->Execute($query, $values);
		else
			$result = $this->db->SelectLimit($query, $numrows, $offset, $values);

		$this->stopTimer($starttime);

		if (!$result ) {
			$this->setErrorMessage( $this->db->ErrorMsg() );

			if ($reporterrors) {
				$this->handleQueryError( $query, $values, $result );
			}
		}

		global $num_queries;
		$num_queries++;
		$this->setQuery( null );

		return $result;
	} // }}}
}

?>
