<?php
/**
 * \file
 * $Header: /cvsroot/tikiwiki/tiki/tests/core/database.php,v 1.3 2003-12-28 11:41:45 mose Exp $
 *
 * \brief Database Layer
 *
 */

/**
 * \brief Database Abstraction Layer
 *
 * This class make real requests to configured database
 *
 */
class TikiCoreDatabase
{
    /// Database object
    var $db;
    /// Just profiling counter
    var $num_queries = 0;
    /// Constructor
    function TikiCoreDatabase($db)
    {
        if (!$db) die ("Invalid db object passed to TikiLib constructor");
        $this->db = $db;
    }
    /// Use ADOdb->qstr() for 1.8
    function qstr($str)
    {
        if (function_exists('mysql_real_escape_string'))
             return "'" . mysql_real_escape_string($str). "'";
        else return "'" . mysql_escape_string($str). "'";
    }
    /// Queries the database reporting an error if detected
    function query($query, $values = null, $numrows = -1, $offset = -1, $reporterrors = true)
    {
        $query = $this->convert_query($query);

        if ($numrows == -1 && $offset == -1) $result = $this->db->Execute($query, $values);
        else $result = $this->db->SelectLimit($query, $numrows, $offset, $values);

        if (!$result && $reporterrors) $this->sql_error($query, $values, $result);

        //count the number of queries made
        $this->num_queries++;

        return $result;
    }

    /// Gets one column for the database.
    function getOne($query, $values = null, $reporterrors = true)
    {
        $query = $this->convert_query($query);
        $result = $this->db->SelectLimit($query, 1, 0, $values);

        if (!$result && $reporterrors) $this->sql_error($query, $values, $result);

        $res = $result->fetchRow();

        if ($res === false) return (NULL); //simulate pears behaviour

        list($key, $value) = each($res);
        return $value;
    }

    // Reports SQL error from PEAR::db object.
    function sql_error($query, $values, $result)
    {
        global $ADODB_LASTDB;
        trigger_error($ADODB_LASTDB." error: ".$this->db->ErrorMsg()." in query:<br/>".$query."<br/>", E_USER_WARNING);
        die;
    }

    // functions to support DB abstraction
    function convert_query($query)
    {
        global $ADODB_LASTDB;

        switch ($ADODB_LASTDB)
        {
        case "oci8":
            $query = preg_replace("/`/", "\"", $query);
            // convert bind variables - adodb does not do that
            $qe = explode("?", $query);
            $query = '';
            for ($i = 0; $i < sizeof($qe) - 1; $i++)
                $query .= $qe[$i] . ":" . $i;
            $query .= $qe[$i];
            break;
        case "postgres7":
        case "sybase":
            $query = preg_replace("/`/", "\"", $query);
            break;
        }

        return ($query);
    }

    function convert_sortmode($sort_mode)
    {
        global $ADODB_LASTDB;

        switch ($ADODB_LASTDB)
        {
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

    function convert_binary()
    {
        global $ADODB_LASTDB;

        switch ($ADODB_LASTDB)
        {
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
}

?>
