<?php
class RefererLib extends TikiLib {

  function RefererLib($db) 
  {
    # this is probably uneeded now
    if(!$db) {
      die("Invalid db object passed to UsersLib constructor");  
    }
    $this->db = $db;  
  }
  
  function clear_referer_stats()
  {
    $query = "delete from tiki_referer_stats";
    $result = $this->query($query);
  }
  
  function list_referer_stats($offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where (referer like '%".$find."%')";
    } else {
      $mid="";
    }
    $query = "select * from tiki_referer_stats $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_referer_stats $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }

  
  
  
}

$refererlib= new RefererLib($dbTiki);	
?>