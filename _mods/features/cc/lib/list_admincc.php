<?php
require_once "listbrowser.php";
require_once "dbhandle.php";

class list_admincc extends listbrowser
{

  function list_admincc()
    {
      $this->listbrowser();
      $this->setLink("cc.php?page=editcc");
      $this->setTable("cc_cc");
      $this->setIdvar("cc_id");
      $this->setNoLimit();

    }

  function setacct_id($acct_id)
    {
      $filters = array();
      $filters['owner_id'] = $acct_id;
      $this->setFilter($filters);
    }
    
}

?>
