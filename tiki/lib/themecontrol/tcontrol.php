<?php
class ThemeControlLib extends TikiLib {

  function ThemeControlLib($db) 
  {
    # this is probably uneeded now
    if(!$db) {
      die("Invalid db object passed to ThemeControlLib constructor");  
    }
    $this->db = $db;  
  }
  
  function tc_assign_category($categId,$theme)
  {
    $this->tc_remove_cat($categId);
    $query="replace into tiki_theme_control_categs(categId,theme) values($categId,'$theme')";
    $this->query($query);
  }
  
  function tc_assign_section($section,$theme)
  {
    $this->tc_remove_section($section);
    $query="replace into tiki_theme_control_sections(section,theme) values('$section','$theme')";
    $this->query($query);
  }
  
  function tc_assign_object($objId,$theme,$type,$name)
  {
    $name=addslashes($name);
    $objId=md5($type.$objId);
    $this->tc_remove_object($objId);
    $query="replace into tiki_theme_control_objects(objId,theme,type,name) values('$objId','$theme','$type','$name')";
    $this->query($query);
  }
  
  
  function tc_get_theme_by_categ($categId)
  {
    if($this->getOne("select count(*) from tiki_theme_control_categs where categId=$categId")) {
	  return $this->getOne("select theme from tiki_theme_control_categs where categId=$categId");    
    } else {
      return '';
    }
  }
  
  function tc_get_theme_by_section($section)
  {
    if($this->getOne("select count(*) from tiki_theme_control_sections where section='$section'")) {
	  return $this->getOne("select theme from tiki_theme_control_sections where section='$section'");    
    } else {
      return '';
    }
  }
  
  function tc_get_theme_by_object($type,$objId)
  {
    $objId=md5($type.$objId);
    if($this->getOne("select count(*) from tiki_theme_control_objects where type='$type' and objId='$objId'")) {
	  return $this->getOne("select theme from tiki_theme_control_objects where type='$type' and objId='$objId'");    
    } else {
      return '';
    }
  }
  
  
  function tc_list_categories($offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_desc"," desc",$sort_mode);
    $sort_mode = str_replace("_asc"," asc",$sort_mode);
    if($find) {
      $mid=" and (theme like '%".$find."%')";  
    } else {
      $mid=""; 
    }
    $query = "select tc.categId,tc.name,theme from tiki_theme_control_categs ttt,tiki_categories tc where ttt.categId=tc.categId $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_theme_control_categs ttt,tiki_categories tc where ttt.categId=tc.categId $mid";
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
  
  function tc_list_sections($offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_desc"," desc",$sort_mode);
    $sort_mode = str_replace("_asc"," asc",$sort_mode);
    if($find) {
      $mid=" where (theme like '%".$find."%')";  
    } else {
      $mid=""; 
    }
    $query = "select * from tiki_theme_control_sections $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_theme_control_sections $mid";
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
  
  function tc_list_objects($type,$offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_desc"," desc",$sort_mode);
    $sort_mode = str_replace("_asc"," asc",$sort_mode);
    if($find) {
      $mid=" and (theme like '%".$find."%')";  
    } else {
      $mid=""; 
    }
    $query = "select * from tiki_theme_control_objects where type='$type' $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_theme_control_objects where type='$type' $mid";
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


  
  function tc_remove_cat($cat) 
  {
    $query = "delete from tiki_theme_control_categs where categId=$cat";
    $this->query($query);
  }
  
  function tc_remove_section($section) 
  {
    $query = "delete from tiki_theme_control_sections where section='$section'";
    $this->query($query);
  }
  
  function tc_remove_object($objId) 
  {
    $query = "delete from tiki_theme_control_objects where objId='$objId'";
    $this->query($query);
  }
  
}

$tcontrollib= new ThemeControlLib($dbTiki);
?>
