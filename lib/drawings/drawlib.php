<?php
 
class DrawLib extends TikiLib {
    
  function DrawLib($db) 
  {
    if(!$db) {
      die("Invalid db object passed to DrawLib constructor");  
    }
    $this->db = $db;  
  }

  
}

$drawlib= new DrawLib($dbTiki);



?>