<?php
class FlinksLib extends TikiLib {

  function FlinksLib($db) 
  {
    # this is probably uneeded now
    if(!$db) {
      die("Invalid db object passed to FlinksLib constructor");  
    }
    $this->db = $db;  
  }
  
  function add_featured_link($url,$title,$description='',$position=0,$type='f')
  {
    $title=addslashes($title);
    $url=addslashes($url);
    $description=addslashes($description);
    $query = "replace tiki_featured_links(url,title,description,position,hits,type) values('$url','$title','$description',$position,0,'$type')";
    $result = $this->query($query);
  }
  
  function remove_featured_link($url)
  {
    $query = "delete from tiki_featured_links where url='$url'";
    $result = $this->query($query);
  }
  
  function update_featured_link($url, $title, $description, $position=0,$type='f')
  {
    $query = "update tiki_featured_links set title='$title', type='$type', description='$description', position=$position where url='$url'";
    $result = $this->query($query);
  }
  
  function add_featured_link_hit($url)
  {
    if($count_admin_pvs == 'y' || $user!='admin') {
      $query = "update tiki_featured_links set hits = hits + 1 where url = '$url'";
      $result = $this->query($query);
    }
  }
  
  function get_featured_link($url)
  {
    $query = "select * from tiki_featured_links where url='$url'";
    $result = $this->query($query);
    if(!$result->numRows()) return false;
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }
  
  function generate_featured_links_positions()
  {
    $query = "select url from tiki_featured_links order by hits desc";
    $result = $this->query($query);
    $position = 1;
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $url = $res["url"];
      $query2="update tiki_featured_links set position=$position where url='$url'";
      $result2 = $this->query($query2);
      $position++;
    }
    return true;
  }
    
}

$flinkslib= new FlinksLib($dbTiki);

?>