<?php

 
class HtmlPagesLib extends TikiLib {

  function HtmlPagesLib($db)
  {
    # this is probably uneeded now
    if(!$db) {
      die("Invalid db object passed to HTMLpagesLib constructor");  
    }
    $this->db = $db;  
  }
  
  function remove_html_page($pageName)
  {
    $query = "delete from tiki_html_pages where pageName='$pageName'";
    $result = $this->query($query);
    return true;
  }
  
  function list_html_pages($offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
	$findesc = $this->qstr('%'.$find.'%');
      $mid=" where (name like $findesc or content like $findesc)";
    } else {
      $mid="";
    }
    $query = "select pageName,refresh,created,type from tiki_html_pages $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_html_pages $mid";
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
  
  function list_html_page_content($pageName,$offset,$maxRecords,$sort_mode,$find)
  {
    $pageName = addslashes($pageName);
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
	$findesc = $this->qstr('%'.$find.'%');
      $mid=" where pageName='$pageName' and (name like $findesc or content like $findesc)";
    } else {
      $mid=" where pageName='$pageName'";
    }
    $query = "select * from tiki_html_pages_dynamic_zones $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_html_pages_dynamic_zones $mid";
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
  
  function parse_html_page($pageName,$data)
  {
    //The data is needed because we may be previewing a page...
    preg_match_all("/\{t?ed id=([^\}]+)\}/",$data,$eds);
    for($i=0;$i<count($eds[0]);$i++) {
        $cosa = $this->get_html_page_content($pageName,$eds[1][$i]);
        $data=str_replace($eds[0][$i],'<span id="'.$eds[1][$i].'">'.$cosa["content"].'</span>',$data);
    }
    //$data=nl2br($data);
    return $data;
  }
  
  function replace_html_page($pageName, $type, $content, $refresh)
  {
    $pageName = addslashes($pageName);
    $content = addslashes($content);
    // Check the name
    $now = date("U");

    $query = "replace into tiki_html_pages(pageName,content,type,created,refresh)
              values('$pageName','$content','$type',$now,$refresh)";
    $result = $this->query($query);
     // For dynamic pages update the zones into the dynamic pages zone
    preg_match_all("/\{ed id=([^\}]+)\}/",$content,$eds);
    preg_match_all("/\{ted id=([^\}]+)\}/",$content,$teds);
    $all_eds = array_merge($eds[1],$teds[1]);

    $query = "select zone from tiki_html_pages_dynamic_zones where pageName='$pageName'";
    $result = $this->query($query);
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      if(!in_array($res["zone"],$all_eds)) {
        $query2="delete from tiki_html_pages_dynamic_zones where pageName='$pageName' and zone='".$res["zone"]."'";
        $result2 = $this->query($query2);
      }
    }

    for($i=0;$i<count($eds[0]);$i++) {
      if(!$this->getOne("select count(*) from tiki_html_pages_dynamic_zones where pageName='$pageName' and zone='".$eds[1][$i]."'")) {
      $query = "replace into tiki_html_pages_dynamic_zones(pageName,zone,type) values('$pageName','".$eds[1][$i]."','tx')";
      $result = $this->query($query);
      }
    }

    for($i=0;$i<count($teds[0]);$i++) {
      if(!$this->getOne("select count(*) from tiki_html_pages_dynamic_zones where pageName='$pageName' and zone='".$teds[1][$i]."'")) {
      $query = "replace into tiki_html_pages_dynamic_zones(pageName,zone,type) values('$pageName','".$teds[1][$i]."','ta')";
      $result = $this->query($query);
      }
    }


    return $pageName;
  }


  function replace_html_page_content($pageName, $zone, $content)
  {
    $pageName = addslashes($pageName);
    $content = addslashes($content);
    // Check the name
    $now = date("U");

    $query = "update tiki_html_pages_dynamic_zones set content='$content' where pageName='$pageName' and zone='$zone'";

    $result = $this->query($query);
    return $zone;
  }
  
  function remove_html_page_content($pageName,$zone)
  {
    $pageName = addslashes($pageName);
    $query = "delete from tiki_html_pages_dynamic_zones where pageName='$pageName' and zone='$zone'";
    $result = $this->query($query);
    return true;
  }
  
  function get_html_page($pageName)
  {
    $pageName = addslashes($pageName);
    $query = "select * from tiki_html_pages where pageName='$pageName'";
    $result = $this->query($query);
    if(!$result->numRows()) return false;
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }

  function get_html_page_content($pageName,$zone)
  {
    $pageName = addslashes($pageName);
    $query = "select * from tiki_html_pages_dynamic_zones where pageName='$pageName' and zone='$zone'";
    $result = $this->query($query);
    if(!$result->numRows()) return false;
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }

  
}

$htmlpageslib= new HtmlPagesLib($dbTiki);
?>



