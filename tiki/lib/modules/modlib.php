<?php
class ModLib extends TikiLib {

  function ModLib($db) 
  {
    # this is probably uneeded now
    if(!$db) {
      die("Invalid db object passed to ModLib constructor");  
    }
    $this->db = $db;  
  }
  
  function replace_user_module($name,$title,$data)
  {
    $name = addslashes($name);
    $title = addslashes($title);
    $data = addslashes($data);
    if( (!empty($name)) && (!empty($title)) && (!empty($data)) ) {
      $query = "replace into tiki_user_modules(name,title,data) values('$name','$title','$data')";
      $result = $this->query($query);
      return true;
    }
  }
  
  function assign_module($name,$title,$position,$order,$cache_time=0,$rows=10,$groups,$params)
  {
    $params=addslashes($params);
    $name = addslashes($name);
    $groups = addslashes($groups);
    $query = "delete from tiki_modules where name='$name'";
    $result = $this->query($query);
    //check for valid values
    $cache_time=is_int($cache_time) ? $cache_time : 0 ;
    $rows=is_int($rows) ? $rows : 10 ;
    $query = "insert into tiki_modules(name,title,position,ord,cache_time,rows,groups,params) values('$name','$title','$position',$order,$cache_time,$rows,'$groups','$params')";
    $result = $this->query($query);
    return true;
  }
  
  function get_assigned_module($name)
  {
    $query = "select * from tiki_modules where name='$name'";
    $result = $this->query($query);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    if($res["groups"]) {
      $grps = unserialize($res["groups"]);
      $res["module_groups"]='';
      foreach($grps as $grp) {
        $res["module_groups"].=" $grp ";
      }
    }
    return $res;
  }
  
  function unassign_module($name)
  {
    $query = "delete from tiki_modules where name='$name'";
    $result = $this->query($query);
    $query = "delete from tiki_user_assigned_modules where name='$name'";
    $result = $this->query($query);
    return true;
  }
  
  function get_rows($name)
  {
    $query = "select rows from tiki_modules where name='$name'";
    $rows = $this->getOne($query);
    if($rows==0) $rows=10;
    return $rows;
  }
  
  function module_up($name)
  {
    $query = "update tiki_modules set ord=ord-1 where name='$name'";
    $result = $this->query($query);
    return true;
  }

  function module_down($name)
  {
    $query = "update tiki_modules set ord=ord+1 where name='$name'";
    $result = $this->query($query);
    return true;
  }
  
  function get_all_modules()
  {
    $user_modules = $this->list_user_modules();
    $all_modules=Array();
    foreach($user_modules["data"] as $um) {
      $all_modules[]=$um["name"];
    }
    // Now add all the system modules
    $h = opendir("templates/modules");
    while (($file = readdir($h)) !== false) {
      if(substr($file,0,3)=='mod') {
        if(!strstr($file,"nocache")){
          $name = substr($file,4,strlen($file)-8);
          $all_modules[]=$name;
        }
      }
    }
    closedir($h);
    return $all_modules;
  }

  function remove_user_module($name)
  {
    $name=addslashes($name);
    $this->unassign_module($name);
    $query = " delete from tiki_user_modules where name='$name'";
    $result = $this->query($query);
    return true;
  }
  
  function list_user_modules()
  {
    $query = "select * from tiki_user_modules";
    $result = $this->query($query);
    $query_cant = "select count(*) from tiki_user_modules";
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

$modlib= new ModLib($dbTiki);

?>