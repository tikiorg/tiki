<?php
include_once(GALAXIA_LIBRARY.'/src/common/Observable.php');
//!! Abstract class representing the base of the API
//! An abstract class representing the API base
/*!
This class is derived by all the API classes so they get the
database connection, database methods and the Observable interface.
*/
class Base extends Observable {
  var $db;  // The ADODB object used to access the database
	var $num_queries = 0;

  // Constructor receiving a ADODB database object.
  function Base($db)
  {
    if(!$db) {
      die("Invalid db object passed to Base constructor");
    }
    $this->db = $db;
  }

	// copied from tikilib.php
	function query($query, $values = null, $numrows = -1, $offset = -1, $reporterrors = true) {
		$this->convert_query($query);
		if ($numrows == -1 && $offset == -1)
			$result = $this->db->Execute($query, $values);
		else
			$result = $this->db->SelectLimit($query, $numrows, $offset, $values);
		if (!$result && $reporterrors)
			$this->sql_error($query, $values, $result);
		$this->num_queries++;
		return $result;
	}

	function getOne($query, $values = null, $reporterrors = true) {
		$this->convert_query($query);
		$result = $this->db->SelectLimit($query, 1, 0, $values);
		if (!$result && $reporterrors)
			$this->sql_error($query, $values, $result);

		$res = $result->fetchRow();
		$this->num_queries++;
		if ($res === false)
			return (NULL); //simulate pears behaviour
		list($key, $value) = each($res);
		return $value;
	}

	function sql_error($query, $values, $result) {
		global $ADODB_LASTDB;

		trigger_error($ADODB_LASTDB . " error:  " . $this->db->ErrorMsg(). " in query:<br />" . $query . "<br />", E_USER_WARNING);
		// only for debugging.
		print_r($values);
		//echo "<br />";
		die;
	}

	// functions to support DB abstraction
	function convert_query(&$query) {
		global $ADODB_LASTDB;

		switch ($ADODB_LASTDB) {
		case "oci8":
			$query = preg_replace("/`/", "\"", $query);
			// convert bind variables - adodb does not do that 
			$qe = explode("?", $query);
			$query = '';
			for ($i = 0; $i < sizeof($qe) - 1; $i++) {
				$query .= $qe[$i] . ":" . $i;
			}
			$query .= $qe[$i];
			break;
		case "postgres7":
		case "sybase":
			$query = preg_replace("/`/", "\"", $query);
			break;
		}
	}

	function convert_sortmode($sort_mode) {
		global $ADODB_LASTDB;

		switch ($ADODB_LASTDB) {
		case "pgsql72":
		case "postgres7":
		case "oci8":
		case "sybase":
			// Postgres needs " " around column names
			//preg_replace("#([A-Za-z]+)#","\"\$1\"",$sort_mode);
			$sort_mode = str_replace("_", "\" ", $sort_mode);
			$sort_mode = "\"" . $sort_mode;
			break;
		case "mysql3":
		case "mysql":
		default:
			$sort_mode = str_replace("_", "` ", $sort_mode);
			$sort_mode = "`" . $sort_mode;
			break;
		}
		return $sort_mode;
	}

	function convert_binary() {
		global $ADODB_LASTDB;

		switch ($ADODB_LASTDB) {
		case "pgsql72":
		case "oci8":
		case "postgres7":
			return;
			break;
		case "mysql3":
		case "mysql":
			return "binary";
			break;
		}
	}

	function qstr($string, $quoted = null)
	{
		if (!isset($quoted)) {
			$quoted = get_magic_quotes_gpc();
		}
		return $this->db->qstr($string,$quoted);
	}

	/* Make ending day - this makes the ending date for an instance to be done taking the date when it was created an
   * the expiration_time from the activity */
   function make_ending_date ($initTime, $expirationTime) {
   	if ($expirationTime == 0) {
   		return 0;
   	}
   	$years = (int)($expirationTime/535680);
   	$months = (int)(($expirationTime-($years*53680))/44640);
   	$days = (int)(($expirationTime-($years*53680)-($months*44640))/1440);
   	$hours = (int)(($expirationTime-($years*53680)-($months*44640)-($days*1440))/60);
   	$minutes = (int)($expirationTime-($years*53680)-($months*44640)-($days*1440)-($hours*60));
   	$endingDate = $initTime;
   	$endingDate = strtotime ("+ $years year",$endingDate);
   	$endingDate = strtotime ("+ $months month",$endingDate);
   	$endingDate = strtotime ("+ $days day",$endingDate);
   	$endingDate = strtotime ("+ $hours hour",$endingDate);
   	$endingDate = strtotime ("+ $minutes minute",$endingDate);
   	return $endingDate;
   }
   
   /*Get expiration members - this returns an array with the representation in years, months, days, hours
   * and minutes of the expirationTime that is stored in the db in minutes*/
   function get_expiration_members ($expirationTime) {
   	$time = array();
   	$time['year'] = (int)($expirationTime/535680);
	$time['month'] = (int)(($expirationTime-($time['year']*535680))/44640);
	$time['day'] = (int)(($expirationTime-($time['year']*535680)-($time['month']*44640))/1440);
	$time['hour'] = (int)(($expirationTime-($time['year']*535680)-($time['month']*44640)-($time['day']*1440))/60);
	$time['minute'] = (int)($expirationTime-($time['year']*535680)-($time['month']*44640)-($time['day']*1440)-($time['hour']*60));
	return $time;
   }
} //end of class

?>
