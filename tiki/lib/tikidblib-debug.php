<?php

// Database access functions

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
}

class TikiDB {
// Database access functions

var $db; // The ADODB db object used to access the database

function TikiDB ($db) {
  if (!$db) {
    die ("Invalid db object passed to TikiLib constructor");
  }
  $this->db = $db;
}


// Use ADOdb->qstr() for 1.8
function qstr($str) {
    if (function_exists('mysql_real_escape_string')) {
        return "'" . mysql_real_escape_string($str). "'";
    } else {
        return "'" . mysql_escape_string($str). "'";
    }
}

    // These functions are only for performance collection of all queries
    // uncomment them if you want to profile queries
    // to record the query stats, create a table:
    // CREATE TABLE tiki_querystats (
    //   qcount int(11) default NULL,
    //   qtext varchar(255) default NULL,
    //   qtime float default NULL,
    //   UNIQUE KEY qtext (qtext)
    // ) TYPE=MyISAM;
    //
    // to show queries to tune use queries like this one:
    // select `qcount` *qtime , qtext from `tiki_querystats` order by 1 ;

    // Queries the database, *returning* an error if one occurs, rather
    // than exiting while printing the error.
    // -rlpowell
    function queryError( $query, &$error, $values = null, $numrows = -1,
    $offset = -1 )
    {
    $this->convert_query($query);

    //for performance stats
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

    return $result;
    }

    // Queries the database reporting an error if detected
    // 
    function query($query, $values = null, $numrows = -1,
    $offset = -1, $reporterrors = true )
    {
    $this->convert_query($query);

    //echo "query: $query <br/>";
    //echo "<pre>";
    //print_r($values);
    //echo "\n";
    //for performance stats
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

    return $result;
}

// Gets one column for the database.
function getOne($query, $values = null, $reporterrors = true, $offset = 0) {
    $this->convert_query($query);

    //echo "<pre>";
    //echo "query: $query \n";
    //print_r($values);
    //echo "\n";
    //for performance stats
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

    if ($res === false)
        return (NULL); //simulate pears behaviour

    list($key, $value) = each($res);
    return $value;
}


// Reports SQL error from PEAR::db object.
function sql_error($query, $values, $result) {
    global $ADODB_LASTDB;

    trigger_error($ADODB_LASTDB . " error:  " . $this->db->ErrorMsg(). " in query:<br/>" . $query . "<br/>", E_USER_WARNING);
    // only for debugging.
    echo "Values: <br>";
    print_r($values);
    if($result===false) echo "<br>\$result is false";
    if($result===null) echo "<br>\$result is null";
    if(empty($result)) echo "<br>\$result is empty";
    // end only for debugging
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

            case "sqlite":
            $query = preg_replace("/`/", "", $query);
            break;
    }

}

function ifNull($narg1,$narg2) {
  return $this->db->ifNull($narg1,$narg2);
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
        case "pgsql72":
            case "postgres7":
            case "oci8":
            case "sybase":
            // Postgres needs " " around column names
            //preg_replace("#([A-Za-z]+)#","\"\$1\"",$sort_mode);
            $sort_mode = str_replace("_asc", "\" asc", $sort_mode);
        $sort_mode = str_replace("_desc", "\" desc", $sort_mode);
        $sort_mode = str_replace(",", "\",\"",$sort_mode);

        $sort_mode = "\"" . $sort_mode;
        break;

        case "sqlite":
            $sort_mode = str_replace("_asc", " asc", $sort_mode);
            $sort_mode = str_replace("_desc", " desc", $sort_mode);
            break;

        case "mysql3":
            case "mysql":
        default:
            $sort_mode = str_replace("_asc", "` asc", $sort_mode);
            $sort_mode = str_replace("_desc", "` desc", $sort_mode);
            $sort_mode = str_replace(",", "`,`",$sort_mode);
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
}
?>
