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
  
  function ranking_exists($chartId)
  {
    return $this->getOne("select count(*) from tiki_charts_rankings where chartId=$chartId");
  }
  
  function generate_new_ranking($chartId)
  {
    $maxPeriod = $this->get_last_period($chartId);
    $newPeriod = $maxPeriod + 1;
    $info = $this->get_chart($chartId);
    // Now just loop the items table and get the topN
    $topN=$info['topN'];
    $query = "select * from tiki_chart_items order by average limit 0,$topN";
    $result = $this->query($query);
    $position=1;
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $itemId = $res['itemId'];
      if($maxPeriod) {
        $lastPosition = $this->getOne("select position from tiki_charts_rankings where itemId=$itemId and chartId=$chartId and period=$maxPeriod");
      } else {
        $lastPosition = 0;
      }
      $query2="insert into tiki_charts_rankings(chartId,itemId,position,lastPosition,period)
      values($chartId,$itemId,$position,$lastPosition,$newPeriod)";
      $this->query($query2);
      $position++;
    }
    $now = date("U");
    $query = "update tiki_charts set lastChart=$now where chartId=$chartId";
    $this->query($query);
  }
  
  function drop_rankings($chartId) {
    $query = "delete from tiki_charts_rankings where chartId=$chartId";
    $this->query($query);
  }
  
  function get_ranking($chartId,$period) 
  {
    $query = "select tci.itemId,tci.title,tci.URL,tci.votes,tci.points,tci.average,tcr.position,tcr.lastPosition from tiki_charts_rankings tcr,tiki_chart_items tci where tcr.itemId = tci.itemId and tcr.chartId=$chartId and period=$period order by position asc";
    $result = $this->query($query);
	$ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {  
      if($res['lastPosition']) {
      	$res['dif']=$res['position']-$res['position'];
      } else {
      	$res['dif']='new';
      }
      if($res['dif']==0) $res['dif']='-';
      $ret[]=$res;
    }
    return $ret;
  }
  
  function purge_user_votes($chartId,$again)
  {
    $now = date("U");
    $query = "delete from tiki_charts_chart_votes where timestamp + $again < $now";
    $this->query($query);
  }
  
  function user_has_voted_chart($user,$chartId)
  {
    if($user) {
      return $this->getOne("select count(*) from tiki_charts_chart_votes where user='$user' and chartId=$chartId");
    } else {
	  return isset($_SESSION['chart_votes']) && in_array($chartId,$_SESSION['chart_votes']);    
    }
  }
  
  function get_last_period($chartId) {
    if($this->ranking_exists($chartId)) {
    	$maxPeriod = $this->getOne("select max(period) from tiki_charts_rankings where chartId=$chartId");
    } else {
    	$maxPeriod = 0;
    }
    return $maxPeriod;
  }
  
  function get_first_period($chartId) {
    if($this->ranking_exists($chartId)) {
    	$maxPeriod = $this->getOne("select min(period) from tiki_charts_rankings where chartId=$chartId");
    } else {
    	$maxPeriod = 0;
    }
    return $maxPeriod;
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
