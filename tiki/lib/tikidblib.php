<?php
//
// $Header: /cvsroot/tikiwiki/tiki/lib/tikidblib.php,v 1.25 2006-11-16 18:44:04 niclone Exp $
//

// $access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

$local_php = 'db/local.php';
if (is_file($local_php)) {
    require_once($local_php);
}

class TikiDB {
// Database access functions

var $db;   // The ADODB db object used to access the database
var $_sql; // Internal variable to store the query string

function TikiDB($db)
{
  if (!$db) die("Invalid db object passed to TikiLib constructor");
  $this->db = $db;
}

function startTimer() {
  list($micro, $sec) = explode(' ', microtime());
  return $micro + $sec;
}

function stopTimer($starttime) {
  global $elapsed_in_db;
  list($micro, $sec) = explode(' ', microtime());
  $now=$micro + $sec;
  $elapsed_in_db+=$now - $starttime;
}

// Use ADOdb->qstr() for 1.8
function qstr($str) {
    if (function_exists('mysql_real_escape_string')) {
        return "'" . mysql_real_escape_string($str). "'";
    } else {
        return "'" . mysql_escape_string($str). "'";
    }
}

// Queries the database, *returning* an error if one occurs, rather
// than exiting while printing the error.
// -rlpowell
function queryError( $query, &$error, $values = null, $numrows = -1,
        $offset = -1 )
{
    $numrows = intval($numrows);
    $offset = intval($offset);
    $this->convert_query($query);
    $this->convert_query_table_prefixes($query);
    
    $starttime=$this->startTimer();
    if ($numrows == -1 && $offset == -1)
        $result = $this->db->Execute($query, $values);
    else
        $result = $this->db->SelectLimit($query, $numrows, $offset, $values);
    $this->stopTimer($starttime);

    if (!$result )
    {
        $error = $this->db->ErrorMsg();
        $result=false;
    }

    //count the number of queries made
    global $num_queries;
    $num_queries++;
    //$this->debugger_log($query, $values);
    return $result;
}

// Queries the database reporting an error if detected
// 
function query($query = null, $values = null, $numrows = -1,
        $offset = -1, $reporterrors = true )
{
    if ( $query == null ) {
        $query = $this->_sql;
    }
    $numrows = intval($numrows);
    $offset = intval($offset);
    $this->convert_query($query);
    $this->convert_query_table_prefixes($query);

    //echo "query: $query <br />";
    //echo "<pre>";
    //print_r($values);
    //echo "\n";

    $starttime=$this->startTimer();
    if ($numrows == -1 && $offset == -1)
        $result = $this->db->Execute($query, $values);
    else
        $result = $this->db->SelectLimit($query, $numrows, $offset, $values);
    $this->stopTimer($starttime);

    //print_r($result);
    //echo "\n</pre>\n";

    if (!$result )
    {
        if ($reporterrors)
        {
            $this->sql_error($query, $values, $result);
        }
    }

    //count the number of queries made
    global $num_queries;
    $num_queries++;
    //$this->debugger_log($query, $values);
    $this->_sql = null;
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
    $this->convert_query($query);
    $this->convert_query_table_prefixes($query);

    //echo "<pre>";
    //echo "query: $query \n";
    //print_r($values);
    //echo "\n";
    $starttime=$this->startTimer();
    $result = $this->db->SelectLimit($query, 1, $offset, $values);

    //echo "\n</pre>\n";
    if (!$result) {
        if ($reporterrors) {
                $this->sql_error($query, $values, $result);
        } else {
	        $this->stopTimer($starttime);
                return $result;
        }
    }

    $res = $result->fetchRow();
    $this->stopTimer($starttime);

    //count the number of queries made
    global $num_queries;
    $num_queries++;
    //$this->debugger_log($query, $values);

    if ($res == false)
        return (NULL); //simulate pears behaviour

    list($key, $value) = each($res);
    return $value;
}


// Reports SQL error from PEAR::db object.
function sql_error($query, $values, $result) {
    global $ADODB_LASTDB, $smarty;

    trigger_error($ADODB_LASTDB . " error:  " . $this->db->ErrorMsg(). " in query:<br /><pre>\n" . $query . "\n</pre><br />", E_USER_WARNING);
    // only for debugging.
    //trigger_error($ADODB_LASTDB . " error:  " . $this->db->ErrorMsg(). " in query:<br />" . $query . "<br />", E_USER_WARNING);
    $outp = "<div class='simplebox'><b>".tra("An error occured in a database query!")."</b></div>";
    $outp.= "<br /><table class='form'>";
   	$outp.= "<tr class='heading'><td colspan='2'>Context:</td></tr>";
		$outp.= "<tr class='formcolor'><td>File</td><td>".basename($_SERVER['SCRIPT_NAME'])."</td></tr>";
		$outp.= "<tr class='formcolor'><td>Url</td><td>".basename($_SERVER['REQUEST_URI'])."</td></tr>";
		$outp.= "<tr class='heading'><td colspan='2'>Query:</td></tr>";
		$outp.= "<tr class='formcolor'><td colspan='2'><tt>$query</tt></td></tr>";
    $outp.= "<tr class='heading'><td colspan='2'>Values:</td></tr>";
    foreach ($values as $k=>$v) {
      $outp.= "<tr class='formcolor'><td>$k</td><td>$v</td></tr>";
    }
    $outp.= "<tr class='heading'><td colspan='2'>Message:</td></tr><tr class='formcolor'><td>Error Message</td><td>".$this->db->ErrorMsg()."</td></tr>\n";
    $outp.= "</table>";
    //if($result===false) echo "<br>\$result is false";
    //if($result===null) echo "<br>\$result is null";
    //if(empty($result)) echo "<br>\$result is empty";
    require_once('tiki-setup.php');
    if ($smarty) {
      $smarty->assign('msg',$outp);
      $smarty->display("error.tpl");
    } else {
      echo $outp;
    }
    // -- debugging stuff: after php 5.1.1 will disclose db user and password
    // echo "<pre>";
    // var_dump(debug_backtrace());
    // echo "</pre>";
    die;
}

function ifNull($narg1,$narg2) {
  return $this->db->ifNull($narg1,$narg2);
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

            $temp_max = sizeof($qe) - 1;
            for ($i = 0; $i < $temp_max; $i++) {
                $query .= $qe[$i] . ":" . $i;
            }

            $query .= $qe[$i];
        break;

        case "postgres7":
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
    global $ADODB_LASTDB;

    if ( !$sort_mode ) {
        return '';
    }
    // parse $sort_mode for evil stuff
    $sort_mode = preg_replace('/[^A-Za-z_,]/', '', $sort_mode);
    $sep = strrpos($sort_mode, '_');
    // force ending to either _asc or _desc unless it's "random"
    if ( substr($sort_mode, $sep)!=='_asc' && $sort_mode != 'random') {
        $sort_mode = substr($sort_mode, 0, $sep) . '_desc';
    }

    if ($sort_mode == 'random') {
	$map = array("postgres7" => "RANDOM()",
		     "mysql3" => "RAND()",
		     "mysql" => "RAND()",
		     "mssql" => "NEWID()",
		     "firebird" => "1", // does this exist in tiki?

		     // below is still needed, return 1 just for not breaking query
		     "oci8" => "1",
		     "sqlite" => "1",
		     "sybase" => "1");

	return $map[$ADODB_LASTDB];
    }

    switch ($ADODB_LASTDB) {
        case "postgres7":
        case "oci8":
        case "sybase":
        case "mssql":
            // Postgres needs " " around column names
            //preg_replace("#([A-Za-z]+)#","\"\$1\"",$sort_mode);

	    $sort_mode = preg_replace("/_asc$/", "\" asc", $sort_mode);
	    $sort_mode = preg_replace("/_desc$/", "\" desc", $sort_mode);
	    $sort_mode = str_replace(",", "\",\"",$sort_mode);
	    $sort_mode = "\"" . $sort_mode;
	    break;

        case "sqlite":
            $sort_mode = preg_replace("/_asc$/", " asc", $sort_mode);
            $sort_mode = preg_replace("/_desc$/", " desc", $sort_mode);
            break;

        case "mysql3":
        case "mysql":
        default:
	    $sort_mode = preg_replace("/_asc$/", "` asc", $sort_mode);
	    $sort_mode = preg_replace("/_desc$/", "` desc", $sort_mode);
	    $sort_mode = str_replace(",", "`,`",$sort_mode);
	    $sort_mode = "`" . $sort_mode;
	    break;
    }

    return $sort_mode;
}

function convert_binary() {
    global $ADODB_LASTDB;

    switch ($ADODB_LASTDB) {
        case "oci8":
        case "postgres7":
        case "sqlite":
            return;
        break;

        case "mysql3":
        case "mysql":
            return "binary";
        break;
    }
}

function sql_cast($var,$type) {
    global $ADODB_LASTDB;
    switch ($ADODB_LASTDB) {
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
