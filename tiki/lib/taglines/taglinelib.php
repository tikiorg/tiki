<?php
class TagLineLib extends TikiLib {

  function TagLineLib($db) 
  {
    # this is probably uneeded now
    if(!$db) {
      die("Invalid db object passed to TaglineLib constructor");  
    }
    $this->db = $db;  
  }
  
  function list_cookies($offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where (cookie like '%".$find."%')";
    } else {
      $mid="";
    }
    $query = "select * from tiki_cookies $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_cookies $mid";
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
  
  function replace_cookie($cookieId, $cookie)
  {
    $cookie=addslashes($cookie);
    // Check the name

    if($cookieId) {
      $query = "update tiki_cookies set cookie='$cookie' where cookieId=$cookieId";
    } else {
      $query = "replace into tiki_cookies(cookie)
                values('$cookie')";
    }
    $result = $this->query($query);
    return true;
  }
  
  function remove_cookie($cookieId)
  {
    $query = "delete from tiki_cookies where cookieId=$cookieId";
    $result = $this->query($query);
    return true;
  }
  
  function get_cookie($cookieId)
  {
    $query = "select * from tiki_cookies where cookieId=$cookieId";
    $result = $this->query($query);
    if(!$result->numRows()) return false;
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }
  
  function remove_all_cookies()
  {
    $query = "delete from tiki_cookies";
    $result = $this->query($query);
  }



  
}

$taglinelib= new TagLineLib($dbTiki);
?>