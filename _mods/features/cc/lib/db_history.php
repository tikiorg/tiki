<?php
require_once "dbhandle.php";
require_once "listbrowser.php";

class db_history extends listbrowser
{
  function db_history() {
      $this->listbrowser();
      $this->setTable("history");
      $this->setNoLimit();
    }

  function setUsercc($acct_id,$cc) {
      $filters = array();
      $filters['acct_id'] = $acct_id;
      $filters['cc_id'] = $cc;
      $this->setFilter($filters);
    }
}


?>
