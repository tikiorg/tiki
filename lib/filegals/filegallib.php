<?php
class FileGalLib extends TikiLib {

  function FileGalLib($db) 
  {
    # this is probably uneeded now
    if(!$db) {
      die("Invalid db object passed to UsersLib constructor");  
    }
    $this->db = $db;  
  }
  
  function remove_file($id)
  {
    global $fgal_use_dir;
    $path = $this->getOne("select path from tiki_files where fileId=$id");
    if($path) {
      unlink($fgal_use_dir.$path);
    }
    $query = "delete from tiki_files where fileId=$id";
    $result = $this->query($query);
    return true;
  }
  
  function insert_file($galleryId,$name,$description,$filename,  $data, $size,$type ,$user,$path)
  {
    $name = addslashes(strip_tags($name));
    $path = addslashes($path);
    $description = addslashes(strip_tags($description));
    $data = addslashes($data);
    $now = date("U");
    $query = "insert into tiki_files(galleryId,name,description,filename,filesize,filetype,data,user,created,downloads,path)
                          values($galleryId,'$name','$description','$filename',$size,'$type','$data','$user',$now,0,'$path')";
    $result = $this->query($query);
    $query = "update tiki_file_galleries set lastModif=$now where galleryId=$galleryId";
    $result = $this->query($query);
    $query = "select max(fileId) from tiki_files where created=$now";
    $fileId = $this->getOne($query);
    return $fileId;
  }
  
  function list_file_galleries($offset = 0, $maxRecords = -1, $sort_mode = 'name_desc', $user, $find)
  {
    global $tiki_p_admin_file_galleries;
    // If $user is admin then get ALL galleries, if not only user galleries are shown
    $sort_mode = str_replace("_"," ",$sort_mode);
    $old_sort_mode ='';
    if(in_array($sort_mode,Array('files desc','files asc'))) {
      $old_offset = $offset;
      $old_maxRecords = $maxRecords;
      $old_sort_mode = $sort_mode;
      $sort_mode ='user desc';
      $offset = 0;
      $maxRecords = -1;
    }

    // If the user is not admin then select it's own galleries or public galleries
    if (($tiki_p_admin_file_galleries == 'y') or ($user == 'admin')) {
       $whuser = "";
    } else {
      $whuser = "where user='$user' or public='y'";
    }

    if($find) {
      if(empty($whuser)) {
        $whuser = "where name like '%".$find."%' or description like '%".$find.".%'";
      } else {
        $whuser .= " and name like '%".$find."%' or description like '%".$find.".%'";
      }
    }

    $query = "select * from tiki_file_galleries $whuser order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_file_galleries $whuser";
    $result = $this->query($query);
    $result_cant = $this->query($query_cant);
    $res2 = $result_cant->fetchRow();
    $cant = $res2[0];
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $aux = Array();
      $aux["name"] = $res["name"];
      $gid = $res["galleryId"];
      $aux["id"] = $gid;
      $aux["visible"] = $res["visible"];
      $aux["galleryId"] = $res["galleryId"];
      $aux["description"] = $res["description"];
      $aux["created"] = $res["created"];
      $aux["lastModif"] = $res["lastModif"];
      $aux["user"] = $res["user"];
      $aux["hits"] = $res["hits"];
      $aux["public"] = $res["public"];
      $aux["files"] = $this->getOne("select count(*) from tiki_files where galleryId='$gid'");
      $ret[] = $aux;
    }
    if($old_sort_mode == 'files asc') {
      usort($ret,'compare_files');
    }
    if($old_sort_mode == 'files desc') {
      usort($ret,'r_compare_files');
    }

    if(in_array($old_sort_mode,Array('files desc','files asc'))) {
      $ret = array_slice($ret, $old_offset, $old_maxRecords);
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  function remove_file_gallery($id)
  {
    global $fgal_use_dir;
    $query = "select path from tiki_files where galleryId='$id'";
    $result = $this->query($query);
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) { 
      $path = $res["path"];
      if($path) {
        @unlink($fgal_use_dir.$path);
      }
    }
    $query = "delete from tiki_file_galleries where galleryId='$id'";
    $result = $this->query($query);
    $query = "delete from tiki_files where galleryId='$id'";
    $result = $this->query($query);
    $this->remove_object('file gallery',$id);
    return true;
  }
  
  function get_file_gallery_info($id)
  {
    $query = "select * from tiki_file_galleries where galleryId='$id'";
    $result = $this->query($query);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }

  function replace_file_gallery($galleryId, $name, $description, $user,$maxRows,$public,$visible='y')
  {
    // if the user is admin or the user is the same user and the gallery exists then replace if not then
    // create the gallary if the name is unused.
    $name = addslashes(strip_tags($name));
    $description = addslashes(strip_tags($description));
    $now = date("U");
    if($galleryId>0) {
      $query = "update tiki_file_galleries set name='$name', maxRows=$maxRows, description='$description',lastModif=$now, public='$public', visible='$visible' where galleryId=$galleryId";
      $result = $this->query($query);
    } else {
      // Create a new record
      $query =  "insert into tiki_file_galleries(name,description,created,user,lastModif,maxRows,public,hits,visible)
                                    values ('$name','$description',$now,'$user',$now,$maxRows,'$public',0,'$visible')";
      $result = $this->query($query);
      $galleryId=$this->getOne("select max(galleryId) from tiki_file_galleries where name='$name' and lastModif=$now");
    }
    return $galleryId;
  }
  
}

$filegallib= new FileGalLib($dbTiki);
?>