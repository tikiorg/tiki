<?php
require_once "listbrowser.php";
require_once "dbhandle.php";

class list_cc extends listbrowser
{

  function list_cc()
    {
      $this->listbrowser();
      $this->setLink("cc.php?page=registeruserforcc");
      $this->setIdvar("cc_id");
      $this->setTable("cc_cc");
      $this->setNoLimit();
    }
}

?>
