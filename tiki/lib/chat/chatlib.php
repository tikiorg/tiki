<?php
class ChatLib extends TikiLib {

  function ChatLib($db) 
  {
    # this is probably uneeded now
    if(!$db) {
      die("Invalid db object passed to ChatLib constructor");  
    }
    $this->db = $db;  
  }
  
  function send_message($user, $channelId, $data)
  {
    $data = addslashes(strip_tags($data));
    $now= date("U");
    $info = $this->get_channel($channelId);
    $name = $info["name"];
    // Check if the user is registered in the channel or update the
    // user timestamp
    $query = "replace into tiki_chat_users(nickname,channelId,timestamp) values('$user',$channelId,$now)";
    $result = $this->query($query);

    // :TODO: If logging is used then log the message
    //$log = fopen("logs/${name}.txt","a");
    //fwrite($log,"$posterName: $data\n");
    //fclose($log);
    $query = "insert into tiki_chat_messages(channelId,poster,timestamp,data) values($channelId,'$user',$now,'$data')";
    $result = $this->query($query);
    return true;
  }
  
  function get_channel($channelId)
  {
    $query = "select * from tiki_chat_channels where channelId=$channelId";
    $result = $this->query($query);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }
  
  function send_private_message($user, $toNickname, $data)
  {
    $data = addslashes(strip_tags($data));
    $now= date("U");

    // :TODO: If logging is used then log the message
    //$log = fopen("logs/${name}.txt","a");
    //fwrite($log,"$posterName: $data\n");
    //fclose($log);
    $query = "insert into tiki_private_messages(poster,timestamp,data,toNickname) values('$user',$now,'$data','$toNickname')";
    $result = $this->query($query);
    return true;
  }
  
  function user_to_channel($user,$channelId)
  {
    $now= date("U");
    $query = "delete from tiki_chat_users where nickname='$user'";
    $result = $this->query($query);
    $query = "replace into tiki_chat_users(nickname,channelId,timestamp) values('$user',$channelId,$now)";
    $result = $this->query($query);
  }
  
  function get_chat_users($channelId)
  {
    $now = date("U") - (5*60);
    $query = "delete from tiki_chat_users where timestamp<$now";
    $result = $this->query($query);
    $query = "select nickname from tiki_chat_users where channelId=$channelId";
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res;
    }
    return $ret;
  }
  
  function get_messages($channelId,$last,$from)
  {
    $query = "select messageId,poster, data from tiki_chat_messages where timestamp>$from and channelId=$channelId and messageId>$last order by timestamp asc";
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $aux = Array();
      $aux["poster"] = $res["poster"];
      $aux["posterName"] = $res["poster"];
      $aux["data"] = $res["data"];
      $aux["messageId"] = $res["messageId"];
      $ret[] = $aux;
    }
    $num = count($ret);
    return $ret;
  }
  
  function get_private_messages($user)
  {
    $query = "select messageId,poster, data from tiki_private_messages where toNickname='$user' order by timestamp asc";
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $aux = Array();
      $aux["poster"] = $res["poster"];
      $aux["posterName"] = $res["poster"];
      $aux["data"] = $res["data"];
      $aux["messageId"] = $res["messageId"];
      $ret[] = $aux;
    }
    $query = "delete from tiki_private_messages where toNickname='$user'";
    $result = $this->query($query);
    $num = count($ret);
    return $ret;
  }
  
  function purge_messages($minutes)
  {
    // :TODO: pass old messages to the message log table
    $secs = $minutes * 60;
    $last = date("U") - $secs;
    $query = "delete from tiki_chat_messages where timestamp<$last";
    $result = $this->query($query);
    // :TODO: delete from modMessages y privateMessages
    return true;
  }
  
  function list_channels($offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where (name like '%".$find."%' or description like '%".$find."%')";
    } else {
      $mid="";
    }
    $query = "select * from tiki_chat_channels $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_chat_channels $mid";
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
  
  function list_active_channels($offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where active='y' and (name like '%".$find."%' or description like '%".$find."%')";
    } else {
      $mid=" where active='y' ";
    }
    $query = "select * from tiki_chat_channels $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_chat_channels $mid";
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
  
  function replace_channel($channelId, $name, $description, $max_users, $mode, $active,$refresh)
  {
    $description=addslashes($description);
    $name=addslashes($name);
    if($channelId) {
      $query = "update tiki_chat_channels set name='$name',description='$description',refresh=$refresh,max_users=$max_users,mode='$mode',active='$active' where channelId=$channelId";
    } else {
      $query = "replace into tiki_chat_channels(name,description,max_users,mode,moderator,active,refresh)
                values('$name','$description',$max_users,'$mode','','$active',$refresh)";
    }
    $result = $this->query($query);
    return true;
  }
  
  function remove_channel($channelId)
  {
    $query = "delete from tiki_chat_channels where channelId=$channelId";
    $result = $this->query($query);
    return true;
  }
  
  
}

$chatlib= new ChatLib($dbTiki);
?>
