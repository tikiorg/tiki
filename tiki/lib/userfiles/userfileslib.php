<?php
class UserFilesLib extends TikiLib {

  function UserFilesLib($db)
  {
    # this is probably uneeded now
    if(!$db) {
      die("Invalid db object passed to UserFilesLib constructor");  
    }
    $this->db = $db;  
  }
  
  function userfiles_quota($user)
  {
	if($user == 'admin') {
		return 0;
	}
    $part1 = $this->getOne("select sum(filesize) from tiki_userfiles where user='$user'");
    $part2 = $this->getOne("select sum(size) from tiki_user_notes where user='$user'");
    return $part1+$part2;
  }
  
  function upload_userfile($user,$name,$filename,$filetype,$filesize,$data,$path)
  {
    $name = addslashes($name);
    $filename = addslashes($filename);
    $data = addslashes($data);
    $now = date("U");
    $query = "insert into tiki_userfiles(user,name,filename,filetype,filesize,data,created,hits,path)
    values('$user','$name','$filename','$filetype','$filesize','$data',$now,0,'$path')";
    $this->query($query);
  }
  
  function list_userfiles($user,$offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_desc"," desc",$sort_mode);
    $sort_mode = str_replace("_asc"," asc",$sort_mode);
    if($find) {
	$findesc = $this->qstr('%'.$find.'%');
      $mid=" and (filename like $findesc)";  
    } else {
      $mid=" "; 
    }
    $query = "select fileId,user,name,filename,filetype,filesize,created,hits from tiki_userfiles where user='$user' $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_userfiles where user='$user' $mid";
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
  
  function get_userfile($user,$fileId)
  {
    $query = "select * from tiki_userfiles where user='$user' and fileId='$fileId'";
    $result = $this->query($query);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;	
  }
  
   
  function remove_userfile($user,$fileId)
  {
    global $uf_use_dir;
    $path = $this->getOne("select path from tiki_userfiles where user='$user' and fileId=$fileId");
    if($path) {
      @unlink($uf_use_dir.$path);
    }
    $query = "delete from tiki_userfiles where user='$user' and fileId=$fileId";
    $this->query($query);  	
  }
  
}

$userfileslib= new UserFilesLib($dbTiki);
?>