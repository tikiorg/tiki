<?php
class DCSLib extends TikiLib {

  function DCSLib($db) 
  {
    # this is probably uneeded now
    if(!$db) {
      die("Invalid db object passed to DCSLib constructor");  
    }
    $this->db = $db;  
  }
  
  function remove_contents($contentId)
  {
    $query = "delete from tiki_programmed_content where contentId=$contentId";
    $result = $this->query($query);
    $query = "delete from tiki_content where contentId=$contentId";
    $result = $this->query($query);
  }
  
  function list_content($offset = 0,$maxRecords = -1,$sort_mode = 'contentId_desc', $find='')
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
	$findesc = $this->qstr('%'.$find.'%');
      $mid=" where (description like $findesc)";
    } else {
      $mid='';
    }
    $query = "select * from tiki_content $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_content $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      // Add actual version
      // Add number of programmed versions
      // Add next programmed version
      // Add number of old versions
      $now = date("U");
      $id = $res["contentId"];
      $query = "select count(*) from tiki_programmed_content where publishDate>$now and contentId=$id";
      $res["future"] = $this->getOne($query);
      $query = "select max(publishDate) from tiki_programmed_content where contentId=$id and publishDate<=$now";
      $res["actual"] = $this->getOne($query);
      $query = "select min(publishDate) from tiki_programmed_content where contentId=$id and publishDate=$now";
      $res["next"] = $this->getOne($query);
      $query = "select count(*) from tiki_programmed_content where contentId = $id and publishdate<$now";
      $res["old"] = $this->getOne($query);
      if($res["old"]>0) $res["old"]--;
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  function get_actual_content_date($contentId)
  {
    $now = date("U");
    $query = "select max(publishDate) from tiki_programmed_content where contentId=$contentId and publishDate<=$now";
    $res = $this->getOne($query);
    return $res;
  }
  
  function get_random_content($contentId)
  {
    $now = date("U");
    $query = "select data from tiki_programmed_content where contentId=$contentId and publishDate<=$now";
    $result = $this->query($query);
    $cant = $result->numRows();
    if(!$cant) return '';
    $x = rand(0,$cant-1);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC,$x);
    return $res["data"];
  }
  
  
  function get_next_content($contentId)
  {
    $now = date("U");
    $query = "select min(publishDate) from tiki_programmed_content where contentId=$contentId and publishDate>$now";
    $res = $this->getOne($query);
    return $res;
  }
  
  function list_programmed_content($contentId,$offset = 0,$maxRecords = -1,$sort_mode = 'publishDate_desc', $find='')
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
	$findesc = $this->qstr('%'.$find.'%');
      $mid=" where contentId=$contentId and (data like $findesc) ";
    } else {
      $mid=" where contentId=$contentId";
    }
    $query = "select * from tiki_programmed_content $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_programmed_content $mid";
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
  
  function replace_programmed_content($pId,$contentId,$publishDate, $data)
  {
    $data = addslashes($data);
    if(!$pId) {
      $query = "replace into tiki_programmed_content(contentId,publishDate,data) values($contentId,$publishDate, '$data')";
      $result = $this->query($query);
      $query = "select max(pId) from tiki_programmed_content where publishDate=$publishDate and data='$data'";
      $id = $this->getOne($query);
    } else {
      $query = "update tiki_programmed_content set contentId=$contentId, publishDate=$publishDate, data='$data' where pId=$pId";
      $result = $this->query($query);
      $id = $pId;
    }
    return $id;
  }
  
  function remove_programmed_content($id)
  {
    $query = "delete from tiki_programmed_content where pId=$id";
    $result = $this->query($query);
    return true;
  }
  
  function get_content($id)
  {
    $query = "select * from tiki_content where contentId=$id";
    $result = $this->query($query);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }
  
  function get_programmed_content($id)
  {
    $query = "select * from tiki_programmed_content where pId=$id";
    $result = $this->query($query);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }
  
  function replace_content($contentId,$description)
  {
    $description = addslashes($description);
    if($contentId>0) {
      $query = "update tiki_content set description='$description' where contentId=$contentId";
      $result = $this->query($query);
      return $contentId;
    } else {
      $query = "insert into tiki_content(description) values('$description')";
      $result = $this->query($query);
      $query = "select max(contentId) from tiki_content where description = '$description'";
      $id = $this->getOne($query);
      return $id;
    }
  }
  
  
  
}

$dcslib= new DCSLib($dbTiki);
?>