<?php

class TemplatesLib extends TikiLib {

  function TemplatesLib($db) 
  {
    # this is probably uneeded now
    if(!$db) {
      die("Invalid db object passed to TemplatesLib constructor");  
    }
    $this->db = $db;  
  }
  
  function list_all_templates($offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where (content like '%".$find."%')";
    } else {
      $mid="";
    }
    $query = "select name,created,templateId from tiki_content_templates $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_content_templates $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $query2= "select section from tiki_content_templates_sections where templateId=".$res["templateId"];
      $result2 = $this->query($query2);
      $sections = Array();
      while($res2 = $result2->fetchRow(DB_FETCHMODE_ASSOC)) {
        $sections[] = $res2["section"];
      }
      $res["sections"]=$sections;
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  function replace_template($templateId, $name, $content)
  {
    $name = addslashes($name);
    $content = addslashes($content);
    // Check the name
    $now = date("U");
    if($templateId) {
      $query = "update tiki_content_templates set content='$content', name='$name', created=$now where templateId=$templateId";
    } else {
      $query = "replace into tiki_content_templates(content,name,created)
                values('$content','$name',$now)";
    }
    $result = $this->query($query);
    $id  = $this->getOne("select max(templateId) from tiki_content_templates where created=$now and name='$name'");
    return $id;
    return true;
  }
  
  function add_template_to_section($templateId,$section)
  {
    $query = "replace into tiki_content_templates_sections(templateId,section) values($templateId,'$section')";
    $result = $this->query($query);
  }
  
  function remove_template_from_section($templateId,$section)
  {
    $query = "delete from tiki_content_templates_sections where templateId=$templateId and section='$section'";
    $result = $this->query($query);
  }
  
  function template_is_in_section($templateId,$section)
  {
    $cant = $this->getOne("select count(*) from tiki_content_templates_sections where templateId=$templateId and section='$section'");
    return $cant;
  }
  
  function remove_template($templateId)
  {
    $query = "delete from tiki_content_templates where templateId=$templateId";
    $result = $this->query($query);
    $query = "delete from tiki_content_templates_sections where templateId=$templateId";
    $result = $this->query($query);
    return true;
  }
  
  
  
}

$templateslib= new TemplatesLib($dbTiki);
?>

