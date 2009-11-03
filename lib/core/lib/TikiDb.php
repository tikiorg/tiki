<?php
require_once 'lib/core/lib/TikiDb/ErrorHandler.php';

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

	function queryError( $query, &$error, $values = null, $numrows = -1, $offset = -1 ) // {{{
	{
		$result = $this->query( $query, $values, $numrows, $offset, false );
		$error = $this->errorMessage;

		return $result;
	} // }}}

	function getOne( $query, $values = null, $reporterrors = true, $offset = 0 ) // {{{
	{
		$result = $this->query( $query, $values, 1, $offset, $reporterrors );
		$res = $result->fetchRow();
		if (empty($res)) {
			return $res;
		}
		return reset( $res );
	} // }}}

	function fetchAll( $query = null, $values = null, $numrows = -1, $offset = -1, $reporterrors = true ) // {{{
	{
		$result = $this->query( $query, $values, $numrows, $offset, $reporterrors );

		$rows = array();
		while( $row = $result->fetchRow() ) {
			$rows[] = $row;
		}

		return $rows;
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
			require_once 'TikiDb/Exception.php';
			throw new TikiDb_Exception( $this->getErrorMessage() );
		}
	} // }}}

	protected function convertQuery( &$query ) // {{{
	{
		switch ($this->getServerType()) {
			case "oci8":
				$query = preg_replace("/`/", "\"", $query);

				// convert bind variables - adodb does not do that
				$qe = explode("?", $query);
				$query = '';

				$temp_max = sizeof($qe) - 1;
				for ($i = 0; $i < $temp_max; $i++) {
					$query .= $qe[$i] . ":" . $i;
				}

				$query .= $qe[$i];
			break;

			case "pgsql":
			case "postgres7":
			case "postgres8":
			case "sybase":
				$query = preg_replace("/`/", "\"", $query);
			break;

			case "mssql":
				$query = preg_replace("/`/","",$query);
				$query = preg_replace("/\?/","'?'",$query);
			break;

			case "sqlite":
				$query = preg_replace("/`/", "", $query);
			break;
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
			$map = array(	"pgsql" => "RANDOM()",
					"postgres7" => "RANDOM()",
					"postgres8" => "RANDOM()",
					"mysql3" => "RAND()",
					"mysql" => "RAND()",
					"mysqli" => "RAND()",
					"mssql" => "NEWID()",
					"firebird" => "1", // does this exist in tiki?

					// below is still needed, return 1 just for not breaking query
					"oci8" => "1",
					"sqlite" => "1",
					"sybase" => "1");

			return $map[$this->getServerType()];
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

			switch ($this->getServerType()) {
				case "pgsql":
				case "postgres7":
				case "postgres8":
				case "oci8":
				case "sybase":
				case "mssql":
					$sort = preg_replace('/_asc$/', '" asc', $sort);
					$sort = preg_replace('/_desc$/', '" desc', $sort);
					$sort = str_replace('.', '"."', $sort);
					$sort = '"' . $sort;
				break;

				case "sqlite":
					$sort = preg_replace('/_asc$/', ' asc', $sort);
				$sort = preg_replace('/_desc$/', ' desc', $sort);
				break;

				case "mysql3":
					case "mysql": 
					case "mysqli":
				default:
					$sort = preg_replace('/_asc$/', '` asc', $sort);
					$sort = preg_replace('/_desc$/', '` desc', $sort);
					$sort = '`' . $sort;
					$sort = str_replace('.', '`.`', $sort);
					break;
			}
			$sorts[$k]=$sort;
		}

		$sort_mode=implode(',', $sorts);
		return $sort_mode;
	} // }}}

	function convertBinary() // {{{
	{
		switch ($this->getServerType()) {
		case "oci8":
		case "pgsql":
		case "postgres7":
		case "postgres8":
		case "sqlite":
			return;

		case "mysql3":
		case "mysql":
		case "mysqli":
			return "binary";
		}
	} // }}}
	
	function cast( $var,$type ) // {{{
	{
		switch ($this->getServerType()) {
		case "pgsql":
		case "postgres7":
		case "postgres8":
			switch ($type) {
				case "int":
					return "$var::INT4";
				case "string":
					return "$var::VARCHAR";
				default:
					return($var);
			}
		case "sybase":
			switch ($type) {
			case "int":
				return " CONVERT(numeric(14,0),$var) ";
			case "string":
				return " CONVERT(varchar(255),$var) ";
			case "float":
				return " CONVERT(numeric(10,5),$var) ";
			default:
				return($var);
			}

		default:
			return($var);
		}
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
		$values = rtrim( str_repeat( '?,', count( $values ) ), ',' );
		return " $field IN( $values ) ";
	} // }}}

	function concat() // {{{
	{
		$arr = func_get_args();

		// suggestion by andrew005@mnogo.ru
		$s = implode(',',$arr);
		if (strlen($s) > 0) return "CONCAT($s)";
		else return '';
	} // }}}
}
