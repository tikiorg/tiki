<?php
 
/* Task properties:
   user, taskId, title, description, date, status, priority, completed, percentage
*/    
  
 
class NotepadLib extends TikiLib {

  function NotepadLib($db) 
  {
    # this is probably uneeded now
    if(!$db) {
      die("Invalid db object passed to NotepadLib constructor");  
    }
    $this->db = $db;  
  }
  
  function get_note($user, $noteId)
  {
    $query = "select * from tiki_user_notes where user='$user' and noteId='$noteId'";
    $result = $this->query($query);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;	
  }
  
  function set_note_parsing($user,$noteId,$mode)
  {
    $query = "update tiki_user_notes set parse_mode='$mode' where user='$user' and noteId=$noteId";
    $this->query($query);
    return true;
  }
  
  function remove_note($user,$noteId)
  {
    $query = "delete from tiki_user_notes where user='$user' and noteId=$noteId";
    $this->query($query);  	
  }

  function list_notes($user,$offset,$maxRecords,$sort_mode,$find)
  {
    
    $sort_mode = str_replace("_desc"," desc",$sort_mode);
    $sort_mode = str_replace("_asc"," asc",$sort_mode);
    if($find) {
      $mid=" and (name like '%".$find."%' or data like '%".$find."%')";  
    } else {
      $mid=""; 
    }
    $query = "select * from tiki_user_notes where user='$user' $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_user_notes where user='$user' $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $res['size']=strlen($res['data']);
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  
}

$notepadlib= new NotepadLib($dbTiki);
?>