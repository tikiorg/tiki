<?php
class HistLib extends TikiLib {

  function HistLib($db) 
  {
    # this is probably uneeded now
    if(!$db) {
      die("Invalid db object passed to HistLib constructor");  
    }
    $this->db = $db;  
  }
  
  // Removes a specific version of a page
  function remove_version($page,$version,$comment='')
  {
    $page = addslashes($page);
    $query="delete from tiki_history where pageName='$page' and version='$version'";
    $result=$this->query($query);
    $action="Removed version $version";
    $t = date("U");
    $query = "insert into tiki_actionlog(action,pageName,lastModif,user,ip,comment) values('$action','$page',$t,'admin','".$_SERVER["REMOTE_ADDR"]."','$comment')";
    $result = $this->query($query);
    return true;
  }
  
  function use_version($page,$version,$comment='')
  {
    $page = addslashes($page);
    $this->invalidate_cache($page);
    $query = "select * from tiki_history where pageName='$page' and version='$version'";
    $result=$this->query($query);
    if(!$result->numRows()) return false;
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    $query = "update tiki_pages set data='".addslashes($res["data"])."',lastModif=".$res["lastModif"].",user='".$res["user"]."',comment='".$res["comment"]."',version=version+1,ip='".$res["ip"]."' where pageName='$page'";
    $result=$this->query($query);
    $query = "delete from tiki_links where fromPage = '$page'";
    $result=$this->query($query);
    $this->clear_links($page);
    $pages = $this->get_pages($res["data"]);
    foreach($pages as $a_page) {
      $this->replace_link($page,$a_page);
    }
    //$query="delete from tiki_history where pageName='$page' and version='$version'";
    //$result=$this->query($query);
    //
    $action="Changed actual version to $version";
    $t = date("U");
    $query = "insert into tiki_actionlog(action,pageName,lastModif,user,ip,comment) values('$action','$page',$t,'admin','".$_SERVER["REMOTE_ADDR"]."','$comment')";
    $result = $this->query($query);
    return true;
  }
  
  function get_user_versions($user)
  {
    $query = "select pageName,version, lastModif, user, ip, comment from tiki_history where user='$user' order by lastModif desc";
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $aux = Array();
      $aux["pageName"] = $res["pageName"];
      $aux["version"] = $res["version"];
      $aux["lastModif"] = $res["lastModif"];
      $aux["ip"] = $res["ip"];
      $aux["comment"] = $res["comment"];
      $ret[]=$aux;
    }
    return $ret;
  }
  
  

  
}

$histlib= new HistLib($dbTiki);
?>