<?php
require_once "lib/pear/NNTP.php";


class Newslib extends Tikilib {
  var $db;
  var $nntp;
  
  function Newslib($db) 
  {
    
    if(!$db) {
      die("Invalid db object passed to NewsLib constructor");
    }
    $this->nntp = new Net_NNTP;
    $this->db = $db;
  }

  function get_server($user, $serverId)
  {
    $query = "select * from tiki_newsreader_servers where user='$user' and serverId='$serverId'";
    $result = $this->query($query);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;	
  }
  
  function news_mark($user,$serverId,$groupName)
  {
  	$groupName = addslashes($groupName);
    $now = date("U");
	if($this->getOne("select count(*) from tiki_newsreader_marks where user='$user' and serverId=$serverId and groupName='$groupName'")) {
      $query = "update tiki_newsreader_marks set timestamp=$now where user='$user' and serverId=$serverId and groupName='$groupName'";		
      $this->query($query);
	} else {
	  $query = "insert into tiki_newsreader_marks(user,serverId,groupName,timestamp) values('$user',$serverId,'$groupName',$now)";
	  $this->query($query);
	}
  }
  
  function news_get_mark($user,$serverId,$groupName)
  {
	if($this->getOne("select count(*) from tiki_newsreader_marks where user='$user' and serverId=$serverId and groupName='$groupName'")) {
      return $this->getOne("select timestamp from tiki_newsreader_marks where user='$user' and serverId=$serverId and groupName='$groupName'");		
	} else {
	  return 0;
	}
  }
  
  function replace_server($user,$serverId,$server,$port,$username,$password)
  {
    $server = addslashes($server);	
    $username = addslashes($username);	
    $password = addslashes($password);	
    if($serverId) {
      $query = "update tiki_newsreader_servers set
      server = '$server',
      port = $port,
      username = '$username',
      password = '$password'
      where user='$user' and serverId=$serverId";	
      $this->query($query);
      return $serverId;
    } else {
      $query = "insert into tiki_newsreader_servers(user,serverId,server,port,username,password)
      values('$user',$serverId,'$server',$port,'$username','$password')";	
      $this->query($query);
      $serverId = $this->getOne("select max(serverId) from tiki_newsreader_servers where user='$user' and server='$server'");
      return $serverId;
    }
  }
  
  function remove_server($user,$serverId)
  {
    $query = "delete from tiki_newsreader_servers where user='$user' and serverId=$serverId";
    $this->query($query);  	
  }

  function list_servers($user,$offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_desc"," desc",$sort_mode);
    $sort_mode = str_replace("_asc"," asc",$sort_mode);
    if($find) {
	$findesc = $this->qstr('%'.$find.'%');
      $mid=" and (title like $findesc or description like $findesc)";  
    } else {
      $mid=""; 
    }
    $query = "select * from tiki_newsreader_servers where user='$user' $mid order by $sort_mode,serverId desc limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_newsreader_servers where user='$user' $mid";
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
  
  function news_select_group($group) 
  {
    return $this->nntp->selectGroup($group);
  }
  
  function news_split_headers($id)
  {
    return $this->nntp->splitHeaders($id);
  }
  
  function news_get_body($id)
  {
    return $this->nntp->getBody($id);
  }
  
  function news_set_server($server,$port,$user,$pass)
  {
    
    $ret = $this->nntp->connect($server,$port,$user,$pass);
       
    if( PEAR::isError($ret)) {
      return false;
    } else {
      return true;
    }
  }
  
  function news_get_groups()
  {
    return $this->nntp->getGroups();
  }
}

$newslib = new Newslib($dbTiki);

?>