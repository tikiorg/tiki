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
  
  function user_vote($user,$itemId,$points=0)
  {
    $chartId=$this->getOne("select chartId from tiki_chart_items where itemId=$itemId");
    $now = date("U");
    
    // Register that the user has voted the item
    if($user) {
      $query = "replace into tiki_charts_votes(user,itemId,timestamp,chartId)
    	values('$user',$itemId,$now,$chartId)";
      $this->query($query);
    } else {
      $_SESSION['chart_votes'][] = $chartId;
      $_SESSION['chart_item_votes'][] = $itemId;
    }
    // Update points and votes for the item
    $query = "update tiki_chart_items set points=points+$points, votes=votes+1 where itemId=$itemId";
    $this->query($query);
    // Calculate average note that is the maxVoteValue is one average is the number of votes!
    if($this->getOne("select maxVoteValue from tiki_charts where chartId=$chartId")==1) {
    	$query = "update tiki_chart_items set average=votes where itemId=$itemId";
    	$this->query($query);
    } else {
    	$query = "update tiki_chart_items set average=points/votes where itemId=$itemId";
    	$this->query($query);
    }
    
  }
  
  function ranking_exists($chartId)
  {
    return $this->getOne("select count(*) from tiki_charts_rankings where chartId=$chartId");
  }
  
  function generate_new_ranking($chartId)
  {
    $maxPeriod = $this->get_last_period($chartId);
    $newPeriod = $maxPeriod + 1;
    $now = date("U");
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
      $query2="insert into tiki_charts_rankings(chartId,itemId,position,lastPosition,period,timestamp)
      values($chartId,$itemId,$position,$lastPosition,$newPeriod,$now)";
      $this->query($query2);
      $position++;
    }

    $query = "update tiki_charts set lastChart=$now where chartId=$chartId";
    $this->query($query);
  }
  
  function drop_rankings($chartId) {
    $query = "delete from tiki_charts_rankings where chartId=$chartId";
    $this->query($query);
  }
  
  
  
  function get_ranking($chartId,$period) 
  {
  	global $user;
    $query = "select tcr.rvotes,tcr.raverage,tci.itemId,tci.title,tci.URL,tci.votes,tci.points,tci.average,tcr.position,tcr.lastPosition from tiki_charts_rankings tcr,tiki_chart_items tci where tcr.itemId = tci.itemId and tcr.chartId=$chartId and period=$period order by position asc";
    $result = $this->query($query);
	$ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {  
      if($res['lastPosition'] != 0) {
      	$res['dif']=$res['lastPosition']-$res['position'];
      	if($res['dif']==0) $res['dif']='-';
      } else {
      	$res['dif']='new';
      }
      if($this->user_has_voted_item($user,$res['itemId'])) {
      	$res['voted']='y';
      } else {
      	$res['voted']='n';
      }
      $res['perm'] = $this->getOne("select count(*) from tiki_charts_rankings where itemId=".$res['itemId']);
      $ret[]=$res;
    }
    return $ret;
  }

  function max_dif($chartId)
  {
    return $this->getOne("select max(lastPosition-position) from tiki_charts_rankings where chartId=$chartId ");
  }
  
  function purge_user_votes($chartId,$again)
  {
    $now = date("U");
    $query = "delete from tiki_charts_votes where timestamp + $again < $now";
    $this->query($query);
  }
  
  function user_has_voted_chart($user,$chartId)
  {
    if($user) {
      return $this->getOne("select count(*) from tiki_charts_votes where user='$user' and chartId=$chartId");
    } else {
	  return isset($_SESSION['chart_votes']) && in_array($chartId,$_SESSION['chart_votes']);    
    }
  }
  
  function user_has_voted_item($user,$itemId)
  {
    if($user) {
      return $this->getOne("select count(*) from tiki_charts_votes where user='$user' and itemId=$itemId");
    } else {
	  return isset($_SESSION['chart_item_votes']) && in_array($itemId,$_SESSION['chart_item_votes']);    
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
    $period = $this->get_last_period($res['chartId']);
    if($period) {
	    // Permanency
	    $res['perm']=$this->getOne("select count(*) from tiki_charts_rankings where itemId=$itemId");
	    // Current position
		$res['position']= $this->getOne("select position from tiki_charts_rankings where itemId=$itemId and period=$period");   
	    // Last position
	    $res['lastPosition']= $this->getOne("select lastPosition from tiki_charts_rankings where itemId=$itemId and period=$period");   
	    // Best position
	    $res['best']= $this->getOne("select min(position) from tiki_charts_rankings where itemId=$itemId");   
	    $res['bestdate']= $this->getOne("select timestamp from tiki_charts_rankings where itemId=$itemId and position=".$res['best']);   
	    if($res['lastPosition'] != 0) {
      		$res['dif']=$res['position']-$res['position'];
      		if($res['dif']==0) $res['dif']='-';
      	} else {
      		$res['dif']='new';
      	}	    
	    // Dif
	} else {
		$res['perm']=0;
		$res['position']=0;
		$res['lastPosition']=0;
		$res['best']=0;
		$res['dif']=0;
	}
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
      $res['items'] = $this->getOne('select count(*) from tiki_chart_items where chartId='.$res['chartId']);
      $query2="select distinct(period) from tiki_charts_rankings where chartId=".$res['chartId'];
      $result2=$this->query($query2);
      $res['periods']=$result2->numRows();
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
