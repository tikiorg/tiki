<?php
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

class TikiResult{
	var $result;
	var $numrows;

	function __construct ($result) {
		$this->result = &$result;
		$this->numrows = count ($this->result);
	}

	function fetchRow() {
		return array_shift($this->result);
	}

	function numRows() {
		return $this->numrows;
	}
}

class TikiDB {
	// Database access functions

	var $db;   // The db object used to access the database
	var $_sql; // Internal variable to store the query string
	var $driver; // name of the driver used to access the DB
	var $sql_error_msg = ""; // The last error message

	function TikiDB($db) {
		if (!$db) {
			die ("Invalid db object passed to TikiDB constructor");
		}

		$this->db=$db;
		$this->driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
	}

	function startTimer() {
		list($micro, $sec) = explode(' ', microtime());
		return $micro + $sec;
	}

	function stopTimer($starttime) {
		global $elapsed_in_db;
		list($micro, $sec) = explode(' ', microtime());
		$now = $micro + $sec;
		$elapsed_in_db += $now - $starttime;
	}

	function qstr($str) {
		return PDO::quote($str);
	}

	function _query( $query, $values = null, $numrows = -1, $offset = -1 )
	{
		error_reporting(E_ALL);
		global $num_queries;
		$num_queries++;

		$numrows = intval($numrows);
		$offset = intval($offset);
		if ( $query == null ) {
			$query = $this->_sql;
		}
		$this->convert_query_table_prefixes($query);

		if( $offset != -1 && $numrows != -1 )
			$query .= " LIMIT $offset,$numrows";
		elseif( $numrows != -1 )
			$query .= " LIMIT $numrows";

		$starttime=$this->startTimer();

		$pq = $this->db->prepare($query);

		if ($values and !is_array($values)) {
			$values = array($values);
		}
		if ($values) {
			$count = 1;
			foreach($values as $value) {
				$pq->bindValue($count++,$value,is_int($value)?(PDO::PARAM_INT):(PDO::PARAM_STR)) ;
			}
		}

		$result = $pq->execute();

		$this->stopTimer($starttime);

		if (!$result) {
			$tmp = $pq->errorInfo();
			$this->sql_error_msg = $tmp[2];
			$pq->closeCursor();
			return false;
		} else {
			$this->sql_error_msg = "";
			$tmp = new TikiResult($pq->fetchAll(PDO::FETCH_ASSOC));
			$pq->closeCursor();
			return $tmp;
		}
	}

	// Queries the database reporting an error if detected
	// 
	function query($query = null, $values = null, 
			$numrows = -1, $offset = -1, $reporterrors = true ) {

		$result = $this->_query($query,$values, $numrows, $offset);
		if (!$result ) {
			if ($reporterrors) {
				$this->sql_error($query, $values, $result);
			}
		}

		return $result;
	}
	// Queries the database, *returning* an error if one occurs, rather
	// than exiting while printing the error.
	// -rlpowell
	function queryError( $query, &$error, $values = null, 
			$numrows = -1, $offset = -1 ) {

		$result = $this->_query($query,$values, $numrows, $offset);
		if (!$result ) {
			$error = $this->sql_error_msg ;
			return false;
		}

		return $result;
	}

	/**
	 * Sets the SQL query string for later execution.
	 *
	 * @param string The SQL query
	 * @param string The common table prefix
	 */
	function setQuery( $sql ) {
		$this->_sql = $sql;
	}

	// Gets one column for the database.
	function getOne($query, $values = null, $reporterrors = true, $offset = 0) {

		$result = $this->query($query, $values, 1, $offset);
		$res = $result->fetchRow();
		if ($result) {
			list($key, $value) = each($res);
			return $value;
		} else {
			if ($reporterrors) {
				$this->sql_error($query, $values, $result);
			}
			return (NULL); //simulate pears behaviour
		}
	}

	// Reports SQL error from PEAR::db object.
	function sql_error($query, $values, $result) {
		global $smarty, $prefs, $ajaxlib;

		trigger_error($this->driver . " error:  " . htmlspecialchars($this->sql_error_msg). " in query:<br /><pre>\n" . htmlspecialchars($query) . "\n</pre><br />", E_USER_WARNING);
		// only for debugging.
		$outp = "<div class='simplebox'><b>".htmlspecialchars(tra("An error occured in a database query!"))."</b></div>";

		include_once ('installer/installlib.php');
		$installer = new Installer;
		if( $installer->requiresUpdate() ) {
			$outp.= '<div class="simplebox highlight">' . tra('Your database requires an update to match the current TikiWiki version. Please proceed to <a href="tiki-install.php">the installer</a>. Using Tiki with an incorrect database version usually provoke errors.') . '</div>';
		}

		$outp.= "<br /><table class='form'>";
		$outp.= "<tr class='heading'><td colspan='2'>Context:</td></tr>";
		$outp.= "<tr class='formcolor'><td>File</td><td>".htmlspecialchars(basename($_SERVER['SCRIPT_NAME']))."</td></tr>";
		$outp.= "<tr class='formcolor'><td>Url</td><td>".htmlspecialchars(basename($_SERVER['REQUEST_URI']))."</td></tr>";
		$outp.= "<tr class='heading'><td colspan='2'>Query:</td></tr>";
		$outp.= "<tr class='formcolor'><td colspan='2'><tt>".htmlspecialchars($query)."</tt></td></tr>";
		$outp.= "<tr class='heading'><td colspan='2'>Values:</td></tr>";
		foreach ($values as $k=>$v) {
			if (is_null($v)) $v='<i>NULL</i>';
			else $v=htmlspecialchars($v);
			$outp.= "<tr class='formcolor'><td>".htmlspecialchars($k)."</td><td>$v</td></tr>";
		}
		$outp.= "<tr class='heading'><td colspan='2'>Message:</td></tr><tr class='formcolor'><td colspan='2'>".htmlspecialchars($this->sql_error_msg)."</td></tr>\n";

		$q=$query;
		foreach($values as $v) {
			if (is_null($v)) $v='NULL';
			else $v="'".addslashes($v)."'";
			$pos=strpos($q, '?');
			if ($pos !== FALSE)
				$q=substr($q, 0, $pos)."$v".substr($q, $pos+1);
		}

		$outp.= "<tr class='heading'><td colspan='2'>Built query was probably:</td></tr><tr class='formcolor'><td colspan='2'>".htmlspecialchars($q)."</td></tr>\n";

		if (function_exists('xdebug_get_function_stack')) {
			function mydumpstack($stack) {
				$o='';
				foreach($stack as $line) {
					$o.='* '.$line['file']." : ".$line['line']." -> ".$line['function']."(".var_export($line['params'], true).")<br />";
				}
				return $o;
			}
			$outp.= "<tr class='heading'><th>Stack Trace</th><td>".mydumpstack(xdebug_get_function_stack())."</td></tr>";
		}

		$outp.= "</table>";
		//if($result===false) echo "<br>\$result is false";
		//if($result===null) echo "<br>\$result is null";
		//if(empty($result)) echo "<br>\$result is empty";

		$showviaajax=false;
		if ($prefs['feature_ajax'] == 'y') {
			global $ajaxlib;
			include_once('lib/ajax/xajax/xajax_core/xajaxAIO.inc.php');
			if ($ajaxlib && $ajaxlib->canProcessRequest()) {
				// this was a xajax request -> return a xajax answer
				$objResponse = new xajaxResponse();
				$page ="<html><head>";
				$page.=" <title>Tiki SQL Error (xajax)</title>";
				$page.=" <link rel='stylesheet' href='styles/thenews.css' type='text/css' />";
				$page.="</head><body>$outp</body></html>";
				$page=addslashes(str_replace(array("\n", "\r"), array(' ', ' '), $page));
				$objResponse->script("bugwin=window.open('', 'tikierror', 'width=760,height=500,scrollbars=1,resizable=1');".
						"bugwin.document.write('$page');");
				echo $objResponse->getOutput();
				die();
			}
		}

		if ( ! isset($_SESSION['fatal_error']) ) {
			// Do not show the error if an error has already occured during the same script execution (error.tpl already called),
			//   because tiki should have died before another error.
			// This happens when error.tpl is called by tiki.sql... and tiki.sql is also called again in error.tpl, entering in an infinite loop.
			require_once('tiki-setup.php');
			if ( $smarty ) {
				$smarty->assign('msg', $outp);
				$_SESSION['fatal_error'] = 'y';
				$smarty->display('error.tpl');
				unset($_SESSION['fatal_error']);
			} else {
				echo $outp;
			}
			die;
		}
	}

	// functions to support DB abstraction
	// unsused (Sept)
	function convert_query(&$query) {

		switch ($this->driver) {
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
	}

	function convert_query_table_prefixes(&$query) {

		$db_table_prefix = isset($GLOBALS["db_table_prefix"])?$GLOBALS["db_table_prefix"]:'' ;
		$common_tiki_users = isset($GLOBALS["common_tiki_users"])?$GLOBALS["common_tiki_users"]:'';
		$common_users_table_prefix = isset($GLOBALS["common_users_table_prefix"])?$GLOBALS["common_users_table_prefix"]:'';

		if ( isset($db_table_prefix) && !is_null($db_table_prefix) && !empty($db_table_prefix) ) {

			//printf("convert_query_table_prefixes():\$db_table_prefix = %s<br />\n", $db_table_prefix );

			if( isset($common_users_table_prefix) && !is_null($common_users_table_prefix) && !empty($common_users_table_prefix) ) {
				$query = str_replace("`users_", "`".$common_users_table_prefix."users_", $query);
			} else {
				$query = str_replace("`users_", "`".$db_table_prefix."users_", $query);
			}

			$query = str_replace("`tiki_", "`".$db_table_prefix."tiki_", $query);
			$query = str_replace("`messu_", "`".$db_table_prefix."messu_", $query);
			$query = str_replace("`sessions", "`".$db_table_prefix."sessions", $query);
			$query = str_replace("`galaxia_", "`".$db_table_prefix."galaxia_", $query);

			//printf("convert_query_table_prefixes():\$query = %s<br />\n", $query );
		}
	}

	function blob_encode(&$blob) {
		//FIXME
		return;
		switch($this->db->blobEncodeType) {
			case 'I':
				$blob=$this->db->BlobEncode($blob);
				break;
			case 'C':
				$blob=$this->db->qstr($this->db->BlobEncode($blob));
				break;
			case 'false':
			default:
		}
	}

	function convert_sortmode($sort_mode) {

		if ( !$sort_mode ) {
			return '';
		}
		// parse $sort_mode for evil stuff
		$sort_mode = str_replace('pref:','',$sort_mode);
		$sort_mode = preg_replace('/[^A-Za-z_,.]/', '', $sort_mode);

		if ($sort_mode == 'random') {
			$map = array("postgres7" => "RANDOM()",
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

			return $map[$this->driver];
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

			switch ($this->driver) {
				case "postgres7":
				case "postgres8":
				case "oci8":
				case "sybase":
				case "mssql":
					$sort = preg_replace('/_asc$/', '" asc', $sort);
					$sort = preg_replace('/_desc$/', '" desc', $sort);
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
	}

	function convert_binary() {

		switch ($this->driver) {
			case "oci8":
			case "postgres7":
			case "postgres8":
			case "sqlite":
				return;
			break;

			case "mysql3":
			case "mysql":
			case "mysqli":
				return "binary";
			break;
		}
	}

	function sql_cast($var,$type) {

		switch ($this->driver) {
			case "sybase":
				switch ($type) {
					case "int":
						return " CONVERT(numeric(14,0),$var) ";
					break;
					case "string":
						return " CONVERT(varchar(255),$var) ";
					break;
					case "float":
						return " CONVERT(numeric(10,5),$var) ";
					break;
				}
			break;

			default:
				return($var);
			break;
		}
	}

	function IfNull($field, $ifNull ) {
		return " IFNULL($field, $ifNull) "; // if MySQL
	}

	function concat() {
		$s = "";
		$arr = func_get_args();

		// suggestion by andrew005@mnogo.ru
		$s = implode(',',$arr);
		if (strlen($s) > 0) return "CONCAT($s)";
		else return '';
	}


	function debugger_log($query, $values)
	{
		// Will spam only if debug parameter present in URL
		// \todo DON'T FORGET TO REMOVE THIS BEFORE 1.8 RELEASE
		if (!isset($_REQUEST["debug"])) return;
		// spam to debugger log
		include_once ('lib/debug/debugger.php');
		global $debugger;
		if (is_array($values) && strpos($query, '?'))
			foreach ($values as $v)
			{
				$q = strpos($query, '?');
				if ($q)
				{
					$tmp = substr($query, 0, $q)."'".$v."'".substr($query, $q + 1);
					$query = $tmp;
				}
			}

		$debugger->msg($this->num_queries.': '.$query);
	}
}

?>
