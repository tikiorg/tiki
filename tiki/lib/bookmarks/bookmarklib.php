<?php
class BookmarkLib extends TikiLib {

  function BookmarkLib($db)
  {
    # this is probably uneeded now
    if(!$db) {
      die("Invalid db object passed to BookmarkLib constructor");  
    }
    $this->db = $db;  
  }
  
  function get_folder_path($folderId,$user)
  {
    $path = '';
    $info = $this->get_folder($folderId,$user);
    $path = '<a class="link" href=tiki-user_bookmarks.php?parentId="'.$info["folderId"].'">'.$info["name"].'</a>';
    while($info["parentId"]!=0) {
      $info = $this->get_folder($info["parentId"],$user);
      $path = $path = '<a class="link" href=tiki-user_bookmarks.php?parentId="'.$info["folderId"].'">'.$info["name"].'</a>'.'>'.$path;
    }
    return $path;
  }
  
  function get_folder($folderId,$user)
  {
    $query = "select * from tiki_user_bookmarks_folders where folderId=$folderId and user='$user'";
    $result = $this->query($query);
    if(!$result->numRows()) return false;
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }
  
  function get_url($urlId)
  {
    $query = "select * from tiki_user_bookmarks_urls where urlId=$urlId";
    $result = $this->query($query);
    if(!$result->numRows()) return false;
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }
  
  function remove_url($urlId,$user)
  {
    $query = "delete from tiki_user_bookmarks_urls where urlId=$urlId and user='$user'";
    $result = $this->query($query);
    return true;
  }
  
  function remove_folder($folderId,$user)
  {
    // Delete the category
    $query = "delete from tiki_user_bookmarks_folders where folderId=$folderId and user='$user'";
    $result = $this->query($query);
    // Remove objects for this category
    $query = "delete from tiki_user_bookmarks_urls where folderId=$folderId and user='$user'";
    $result = $this->query($query);
    // SUbfolders
    $query = "select folderId from tiki_user_bookmarks_folders where parentId=$folderId and user='$user'";
    $result = $this->query($query);
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      // Recursively remove the subcategory
      $this->remove_folder($res["folderId"],$user);
    }
    return true;
  }
  
  function update_folder($folderId,$name,$user)
  {
    $name = addslashes($name);
    $query = "update tiki_user_bookmarks_folders set name='$name' where folderId=$folderId and user='$user'";
    $result = $this->query($query);
  }
  
  function add_folder($parentId,$name,$user)
  {
    // Don't allow empty/blank folder names.
  	if(empty($name)) return false;
    $name = addslashes($name);
    $query = "insert into tiki_user_bookmarks_folders(name,parentId,user) values('$name',$parentId,'$user')";
    $result = $this->query($query);
  }
  
  function replace_url($urlId,$folderId,$name,$url,$user)
  {
    $now = date("U");
    $name = addslashes($name);
    if($urlId) {
      $query = "update tiki_user_bookmarks_urls set user='$user',lastUpdated=$now,folderId=$folderId,name='$name',url='$url' where urlId=$urlId";
    } else {
      $query = " insert into tiki_user_bookmarks_urls(name,url,data,lastUpdated,folderId,user)
      values('$name','$url','',$now,$folderId,'$user')";
    }
    $result = $this->query($query);
    $id = $this->getOne("select max(urlId) from tiki_user_bookmarks_urls where url='$url' and lastUpdated=$now");
    return $id;
  }
  
  function refresh_url($urlId)
  {
    $info = $this->get_url($urlId);
    if(strstr($info["url"],'tiki-') || strstr($info["url"],'messu-')) return false;
    @$fp = fopen($info["url"],"r");
    if(!$fp) return;
    $data = '';
    while(!feof($fp)) {
      $data .= fread($fp,4096);
    }
    fclose($fp);
    $data = addslashes($data);
    $now = date("U");
    $query = "update tiki_user_bookmarks_urls set lastUpdated=$now, data='$data' where urlId=$urlId";
    $result = $this->query($query);
    return true;
  }
  
  function list_folder($folderId,$offset,$maxRecords,$sort_mode='name_asc',$find,$user)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
	$findesc = $this->qstr('%'.$find.'%');
      $mid=" and name like $findesc or url like $findesc";
    } else {
      $mid="";
    }
    $query = "select * from tiki_user_bookmarks_urls where folderId=$folderId and user='$user' $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select  * from tiki_user_bookmarks_urls where folderId=$folderId and user='$user' $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $res["datalen"]=strlen($res["data"]);
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }

  function get_child_folders($folderId,$user)
  {
    $ret=Array();
    $query = "select * from tiki_user_bookmarks_folders where parentId=$folderId and user='$user'";
    $result = $this->query($query);
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $cant = $this->getOne("select count(*) from tiki_user_bookmarks_urls where folderId=".$res["folderId"]);
      $res["urls"]=$cant;
      $ret[]=$res;
    }
    return $ret;
  }

  
}

$bookmarklib= new BookmarkLib($dbTiki);
?>