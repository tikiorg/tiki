<?php
 
class WikiLib extends TikiLib {
    
  function WikiLib($db) 
  {
    if(!$db) {
      die("Invalid db object passed to UsersLib constructor");  
    }
    $this->db = $db;  
  }

  // Methods to cache and handle the cached version of wiki pages
  // to prevent parsing large pages.
  function get_cache_info($page)
  {
    $query = "select cache,cache_timestamp from tiki_pages where pageName='$page'";
    $result = $this->query($query);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }
  
  function update_cache($page,$data)
  {
    $now = date('U');
    $data = addslashes($data);
    $query = "update tiki_pages set cache='$data', cache_timestamp=$now where pageName='$page'";
    $result = $this->query($query);
    return true;
  }

  
}

$wikilib= new WikiLib($dbTiki);

?>