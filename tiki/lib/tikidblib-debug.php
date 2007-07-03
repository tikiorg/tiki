<?php
//
// $Header: /cvsroot/tikiwiki/tiki/lib/tikidblib-debug.php,v 1.16 2007-07-03 15:01:13 sept_7 Exp $
//


// INSTRUCTIONS

    // This Library is a replacement for the tikidblib.php library to record
    // SQL query statistics. Copy the original tikidblib.php somewhere
    // and copy this library over the tikidblib.php
    //
    // To record the query stats you have to create a table:
    // CREATE TABLE tiki_querystats (
    //   qcount int(11) default NULL,
    //   qtext varchar(255) default NULL,
    //   qtime float default NULL,
    //   UNIQUE KEY qtext (qtext)
    // ) TYPE=MyISAM;
    //
    // to show queries that are executed very often use:
    // select `qcount`, `qtime` , `qtext` from `tiki_querystats` order by 1 ;
    //
    // to show queries that take much time to execute:
    // select `qtime`/`qcount` as time_per_query, `qcount` , `qtext` from `tiki_querystats` order by 1 ;


//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class TikiDB {
// Database access functions

var $db; // The ADODB db object used to access the database

function TikiDB($db)
{
  $this->TikiLib($db);
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
    list($micro,$sec)=explode(' ',microtime());
    $query_start=$sec+$micro;

    if ($numrows == -1 && $offset == -1)
        $result = $this->db->Execute($query, $values);
    else
        $result = $this->db->SelectLimit($query, $numrows, $offset, $values);

    list($micro,$sec)=explode(' ',microtime());
    $query_stop=$sec+$micro;
    $qdiff=$query_stop-$query_start;
    $querystat="insert into `tiki_querystats` values(1,'".addslashes($query)."',$qdiff)";
    $qresult=$this->db->Execute($querystat);
    if(!$qresult) {
       $querystat="update `tiki_querystats` set `qcount`=`qcount`+1, `qtime`=`qtime`+$qdiff where `qtext`='".addslashes($query)."'";
       $qresult=$this->db->Execute($querystat);
    }

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
function query($query, $values = null, $numrows = -1,
        $offset = -1, $reporterrors = true )
{
    $numrows = intval($numrows);
    $offset = intval($offset);
    $this->convert_query($query);
    $this->convert_query_table_prefixes($query);

    //echo "query: $query <br />";
    //echo "<pre>";
    //print_r($values);
    //echo "\n";
    list($micro,$sec)=explode(' ',microtime());
    $query_start=$sec+$micro;

    if ($numrows == -1 && $offset == -1)
        $result = $this->db->Execute($query, $values);
    else
        $result = $this->db->SelectLimit($query, $numrows, $offset, $values);

    list($micro,$sec)=explode(' ',microtime());
    $query_stop=$sec+$micro;
    $qdiff=$query_stop-$query_start;
    $querystat="insert into `tiki_querystats` values(1,'".addslashes($query)."',$qdiff)";
    $qresult=$this->db->Execute($querystat);
    if(!$qresult) {
      $querystat="update `tiki_querystats` set `qcount`=`qcount`+1, `qtime`=`qtime`+$qdiff where `qtext`='".addslashes($query)."'";
      $qresult=$this->db->Execute($querystat);
    }

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
    return $result;
}

// Gets one column for the database.
function getOne($query, $values = null, $reporterrors = true, $offset = 0) {
    $this->convert_query($query);
    $this->convert_query_table_prefixes($query);
    //echo "<pre>";
    //echo "query: $query \n";
    //print_r($values);
    //echo "\n";
    list($micro,$sec)=explode(' ',microtime());
    $query_start=$sec+$micro;

    $result = $this->db->SelectLimit($query, 1, $offset, $values);
    list($micro,$sec)=explode(' ',microtime());
    $query_stop=$sec+$micro;
    $qdiff=$query_stop-$query_start;
    $querystat="insert into `tiki_querystats` values(1,'".addslashes($query)."',$qdiff)";
    $qresult=$this->db->Execute($querystat);
    if(!$qresult) {
      $querystat="update `tiki_querystats` set `qcount`=`qcount`+1, `qtime`=`qtime`+$qdiff where `qtext`='".addslashes($query)."'";
      $qresult=$this->db->Execute($querystat);
    }

    //echo "\n</pre>\n";
    if (!$result) {
        if ($reporterrors) {
                $this->sql_error($query, $values, $result);
        } else {
                return $result;
        }
    }

    $res = $result->fetchRow();

    //count the number of queries made
    global $num_queries;
    $num_queries++;
    //$this->debugger_log($query, $values);

    if ($res === false)
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
    $outp.= "<tr class='formcolor'><td>File</td><td>".$_SERVER['SCRIPT_NAME']."</td></tr>";
    $outp.= "<tr class='formcolor'><td>Url</td><td>".$_SERVER['REQUEST_URI']."</td></tr>";
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
    echo "<pre>";
    var_dump(debug_backtrace());
    echo "</pre>";
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
    // force ending to either _asc or _desc
    if ( substr($sort_mode, $sep)!=='_asc' ) {
        $sort_mode = substr($sort_mode, 0, $sep) . '_desc';
    }

    switch ($ADODB_LASTDB) {
        case "postgres7":
        case "postgres8":
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
        case "postgres8":
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
