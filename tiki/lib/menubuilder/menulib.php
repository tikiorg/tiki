<?php
class MenuLib extends TikiLib {

  function MenuLib($db) 
  {
    # this is probably uneeded now
    if(!$db) {
      die("Invalid db object passed to MenuLib constructor");  
    }
    $this->db = $db;  
  }
  
  function list_menus($offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
	$findesc = $this->qstr('%'.$find.'%');
      $mid=" where (name like $findesc or description like $findesc)";
    } else {
      $mid="";
    }
    $query = "select * from tiki_menus $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_menus $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $query = "select count(*) from tiki_menu_options where menuId=".$res["menuId"];
      $res["options"]=$this->getOne($query);
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  function replace_menu($menuId, $name, $description, $type)
  {
    $description = addslashes($description);
    $name = addslashes($name);
    // Check the name


    if($menuId) {
      $query = "update tiki_menus set name='$name',description='$description',type='$type' where menuId=$menuId";
    } else {
      $query = "replace into tiki_menus(name,description,type)
                values('$name','$description','$type')";
    }
    $result = $this->query($query);
    return true;
  }

  function get_max_option($menuId)
  {
    $query = "select max(position) from tiki_menu_options where menuId=$menuId";
    $max = $this->getOne($query);
    return $max;
  }

  function replace_menu_option($menuId,$optionId, $name, $url, $type, $position)
  {


    $name = addslashes($name);
    // Check the name

    if($optionId) {
      $query = "update tiki_menu_options set name='$name',url='$url',type='$type',position=$position where optionId=$optionId";
    } else {
      $query = "replace into tiki_menu_options(menuId,name,url,type,position)
                values($menuId,'$name','$url','$type',$position)";
    }

    $result = $this->query($query);
    return true;
  }

  function remove_menu($menuId)
  {
    $query = "delete from tiki_menus where menuId=$menuId";
    $result = $this->query($query);
    $query = "delete from tiki_menu_options where menuId=$menuId";
    $result = $this->query($query);
    return true;
  }

  function remove_menu_option($optionId)
  {
    $query = "delete from tiki_menu_options where optionId=$optionId";
    $result = $this->query($query);
    return true;
  }

  

  function get_menu_option($optionId)
  {
    $query = "select * from tiki_menu_options where optionId=$optionId";
    $result = $this->query($query);
    if(!$result->numRows()) return false;
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }

  
  
}

$menulib= new MenuLib($dbTiki);

?>