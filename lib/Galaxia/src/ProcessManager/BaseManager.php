<?php
include_once('lib/Galaxia/src/common/Observable.php');
class BaseManager extends Observable {
  var $db;  // The PEAR db object used to access the database

  // Constructor receiving a PEAR::Db database object.
  function BaseManager($db)
  {
    if(!$db) {
      die("Invalid db object passed to BaseManager constructor");
    }
    $this->db = $db;
  }


  // Queries the database reporting an error if detected
  function query($query,$reporterrors=true) {
    $result = $this->db->query($query);
    if(DB::isError($result) && $reporterrors) $this->sql_error($query,$result);
    return $result;
  }

  // Gets one column for the database.
  function getOne($query,$reporterrors=true) {
    $result = $this->db->getOne($query);
    if(DB::isError($result) && $reporterrors) $this->sql_error($query,$result);
    return $result;
  }
  
  // Reports SQL error from PEAR::db object.
  function sql_error($query, $result)
  {
    trigger_error("MYSQL error:  ".$result->getMessage()." in query:<br/>".$query."<br/>",E_USER_WARNING);
    die;
  }


} //end of class

?>
