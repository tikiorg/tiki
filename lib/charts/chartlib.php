<?php
class ChartLib extends TikiLib {

  function ChartLib($db) 
  {
    # this is probably uneeded now
    if(!$db) {
      die("Invalid db object passed to ChartLib constructor");  
    }
    $this->db = $db;  
  }

  function get_chart($chartId)
  {
    $query = "select * from tiki_charts where chartId='$chartId'";
    $result = $this->query($query);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;	
  }

  function get_chart_item($itemId)
  {
    $query = "select * from tiki_chart_items where itemId='$itemId'";
    $result = $this->query($query);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;	
  }

  
  function replace_chart($chartId, $vars)
  {
    $TABLE_NAME = 'tiki_charts';
    $now = date("U");
    $vars['created']=$now;
    
    foreach($vars as $key=>$value)
    {
      $vars[$key]=addslashes($value);
    }
  
    if($chartId) {
      // update mode
      $first = true;
      $query ="update $TABLE_NAME set";
      foreach($vars as $key=>$value) {
        if(!$first) $query.= ',';
        if(!is_numeric($value)) $value="'".$value."'";
        $query.= " $key=$value ";
        $first = false;
      }
      $query .= " where chartId=$chartId ";
      $this->query($query);
    } else {
      unset($vars['chartId']);
      // insert mode
      $first = true;
      $query = "insert into $TABLE_NAME(";
      foreach(array_keys($vars) as $key) {
        if(!$first) $query.= ','; 
        $query.= "$key";
        $first = false;
      } 
      $query .=") values(";
      $first = true;
      foreach(array_values($vars) as $value) {
        if(!$first) $query.= ','; 
        if(!is_numeric($value)) $value="'".$value."'";
        $query.= "$value";
        $first = false;
      } 
      $query .=")";
      $this->query($query);
      $chartId = $this->getOne("select max(chartId) from $TABLE_NAME where created=$now"); 
    }
    // Get the id
    return $chartId;
  }

  function replace_chart_item($itemId, $vars)
  {
    $TABLE_NAME = 'tiki_chart_items';
    $now = date("U");
    $vars['created']=$now;
    if(!isset($vars['votes'])) $vars['votes']=0;
    if(!isset($vars['points'])) $vars['points']=0;
    
    $vars['average'] = $vars['votes'] ? $vars['points']/$vars['votes'] : 0;
    
    foreach($vars as $key=>$value)
    {
      $vars[$key]=addslashes($value);
    }
  
    if($itemId) {
      // update mode
      $first = true;
      $query ="update $TABLE_NAME set";
      foreach($vars as $key=>$value) {
        if(!$first) $query.= ',';
        if(!is_numeric($value)) $value="'".$value."'";
        $query.= " $key=$value ";
        $first = false;
      }
      $query .= " where itemId=$itemId ";
      $this->query($query);
    } else {
      unset($vars['itemId']);
      // insert mode
      $first = true;
      $query = "insert into $TABLE_NAME(";
      foreach(array_keys($vars) as $key) {
        if(!$first) $query.= ','; 
        $query.= "$key";
        $first = false;
      } 
      $query .=") values(";
      $first = true;
      foreach(array_values($vars) as $value) {
        if(!$first) $query.= ','; 
        if(!is_numeric($value)) $value="'".$value."'";
        $query.= "$value";
        $first = false;
      } 
      $query .=")";
      $this->query($query);
      $itemId = $this->getOne("select max(itemId) from $TABLE_NAME where created=$now"); 
    }
    // Get the id
    return $itemId;
  }

  
  function remove_chart($chartId)
  {
    $query = "delete from tiki_charts where chartId=$chartId";
    $this->query($query);  	
  }

  function remove_chart_item($itemId)
  {
    $query = "delete from tiki_chart_items where itemId=$itemId";
    $this->query($query);  	
  }

  
  function list_charts($offset,$maxRecords,$sort_mode,$find,$where='')
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where ((title like '%".$find."%') or (description like '%".$find."%'))";
    } else {
      $mid="";
    }
    if($where) {
      if($mid) {
      	$mid.= " and ($where) ";
      } else {
      	$mid = "where ($where) ";
      }
    }
    $query = "select * from tiki_charts $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_charts $mid";
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


  function list_chart_items($offset,$maxRecords,$sort_mode,$find,$where='')
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where ((title like '%".$find."%') or (description like '%".$find."%'))";
    } else {
      $mid="";
    }
    if($where) {
      if($mid) {
      	$mid.= " and ($where) ";
      } else {
      	$mid = "where ($where) ";
      }
    }
    $query = "select * from tiki_chart_items $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_chart_items $mid";
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
$chartlib= new ChartLib($dbTiki);
?> 
