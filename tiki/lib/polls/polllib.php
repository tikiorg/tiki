<?php
class PollLib extends TikiLib {

  function PollLib($db) 
  {
    # this is probably uneeded now
    if(!$db) {
      die("Invalid db object passed to UsersLib constructor");  
    }
    $this->db = $db;  
  }
  
  function list_polls($offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where (title like '%".$find."%')";
    } else {
      $mid="";
    }
    $query = "select * from tiki_polls $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_polls $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $query = "select count(*) from tiki_poll_options where pollId=".$res["pollId"];
      $res["options"]=$this->getOne($query);
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  function list_active_polls($offset,$maxRecords,$sort_mode,$find)
  {
    $now = date("U");
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where (active='a' or active='c') and publishDate<=$now and (title like '%".$find."%)'";
    } else {
      $mid=" where (active='a' or active='c') and publishDate<=$now ";
    }
    $query = "select * from tiki_polls $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_polls $mid";
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
  
  function list_current_polls($offset,$maxRecords,$sort_mode,$find)
  {
    $now = date("U");
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where active='c' and publishDate<=$now and (title like '%".$find."%')";
    } else {
      $mid=" where active='c' and publishDate<=$now ";
    }
    $query = "select * from tiki_polls $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_polls $mid";
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
  
  function list_all_polls($offset,$maxRecords,$sort_mode,$find)
  {
    $now = date("U");
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where publishDate<=$now and (title like '%".$find."%')";
    } else {
      $mid=" where publishDate<=$now ";
    }
    $query = "select * from tiki_polls $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_polls $mid";
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
  
  function list_poll_options($pollId,$offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where pollId=$pollId and (title like '%".$find."%')";
    } else {
      $mid=" where pollId=$pollId ";
    }
    $query = "select * from tiki_poll_options $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_poll_options $mid";
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
  
  function remove_poll($pollId)
  {
    $query = "delete from tiki_polls where pollId=$pollId";
    $result = $this->query($query);
    $query = "delete from tiki_poll_options where pollId=$pollId";
    $result = $this->query($query);
    $this->remove_object('poll',$pollId);
    return true;
  }
  
  function set_last_poll()
  {
    $now = date("U");
    $query = "select max(publishDate) from tiki_polls where publishDate<=$now";
    $last = $this->getOne($query);
    $query = "update tiki_polls set active='c' where publishDate=$last";
    $result = $this->query($query);
  }
  
  function close_all_polls()
  {
    $now = date("U");
    $query = "select max(publishDate) from tiki_polls where publishDate<=$now";
    $last = $this->getOne($query);
    $query = "update tiki_polls set active='x' where publishDate<$last and publishDate<=$now";
    $result = $this->query($query);
  }
  
  function active_all_polls()
  {
    $now = date("U");
    $query = "update tiki_polls set active='a' where publishDate<=$now";
    $result = $this->query($query);
  }
  
  function remove_poll_option($optionId)
  {
    $query = "delete from tiki_poll_options where optionId=$optionId";
    $result = $this->query($query);
    return true;
  }
  
  function get_poll_option($optionId)
  {
    $query = "select * from tiki_poll_options where optionId=$optionId";
    $result = $this->query($query);
    if(!$result->numRows()) return false;
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }
  
  function replace_poll($pollId, $title, $active, $publishDate)
  {
    $title = addslashes($title);
    // Check the name
    if($pollId) {
      $query = "update tiki_polls set title='$title',active='$active',publishDate=$publishDate where pollId=$pollId";
      $result = $this->query($query);
    } else {
      $query = "replace into tiki_polls(title,active,publishDate,votes)
                values('$title','$active',$publishDate,0)";
      $result = $this->query($query);
      $pollId=$this->getOne("select max(pollId) from tiki_polls where title='$title' and publishDate=$publishDate");
    }

    return $pollId;
  }
  
  function replace_poll_option($pollId,$optionId, $title)
  {
    $title = addslashes($title);
    // Check the name
    if($optionId) {
      $query = "update tiki_poll_options set title='$title' where optionId=$optionId";
    } else {
      $query = "replace into tiki_poll_options(pollId,title,votes)
                values($pollId,'$title',0)";
    }

    $result = $this->query($query);
    return true;
  }
  
  function get_random_active_poll()
  {
    // Get pollid from polls where active = 'y' and publishDate is less than now
    $res = $this->list_current_polls(0,-1,'title_desc','');
    $data = $res["data"];
    $bid = rand(0,count($data)-1);
    $pollId  = $data[$bid]["pollId"];
    return $pollId;
  }
  
  

  
}

$polllib= new PollLib($dbTiki);
?>