<?php
class FileGalLib extends TikiLib {

  function FileGalLib($db) 
  {
    # this is probably uneeded now
    if(!$db) {
      die("Invalid db object passed to FilegalLib constructor");  
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
    $checksum=md5($data);
    $path = addslashes($path);
    $description = addslashes(strip_tags($description));
    $data = addslashes($data);
    $now = date("U");
    if($this->getOne("select count(*) from tiki_files where hash='$checksum'")) return false;
    
    $query = "insert into tiki_files(galleryId,name,description,filename,filesize,filetype,data,user,created,downloads,path,hash)
                          values($galleryId,'$name','$description','$filename',$size,'$type','$data','$user',$now,0,'$path','$checksum')";
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
  
  function set_file_gallery($file,$gallery)
  {
  	$query  = "update tiki_files set galleryId=$gallery where fileId=$file";
  	$this->query($query);
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

  function replace_file_gallery($galleryId, $name, $description, $user,$maxRows,$public,$visible='y',$show_id,$show_icon,$show_name,$show_size,$show_description,$show_created,$show_dl,$max_desc)
  {
    // if the user is admin or the user is the same user and the gallery exists then replace if not then
    // create the gallary if the name is unused.
    $name = addslashes(strip_tags($name));
    $description = addslashes(strip_tags($description));
    $now = date("U");
    if($galleryId>0) {
      $query = "update tiki_file_galleries set name='$name', maxRows=$maxRows, description='$description',lastModif=$now, public='$public', visible='$visible',show_icon='$show_icon',show_id='$show_id',show_name='$show_name',show_description='$show_description',show_size='$show_size',show_created='$show_created',show_dl='$show_dl',max_desc=$max_desc where galleryId=$galleryId";
      $result = $this->query($query);
    } else {
      // Create a new record
      $query =  "insert into tiki_file_galleries(name,description,created,user,lastModif,maxRows,public,hits,visible,show_id,show_icon,show_name,show_description,show_created,show_dl,max_desc)
                                    values ('$name','$description',$now,'$user',$now,$maxRows,'$public',0,'$visible',
                                    '$show_id','$show_icon','$show_name','$show_description','$show_created','$show_dl',$max_desc)";
      $result = $this->query($query);
      $galleryId=$this->getOne("select max(galleryId) from tiki_file_galleries where name='$name' and lastModif=$now");
    }
    return $galleryId;
  }
  
  function process_batch_file_upload($galleryId,$file,$user,$description)
  {

    global $fgal_match_regex;
    global $fgal_nmatch_regex;
    global $fgal_use_db;
    global $fgal_use_dir;
    $description = addslashes($description);
    include_once('lib/pclzip.lib.php');
    include_once('lib/mime/mimelib.php');
    $archive = new PclZip($file);
    $archive->extract('temp');
    $files=Array();
    $h = opendir("temp");
    $gal_info = $this->get_file_gallery_info($galleryId);
    while (($file = readdir($h)) !== false) {
    if( $file!='.' && $file!='..' && is_file("temp/$file") && $file!='license.txt' ) {
      $files[]=$file;
      // check filters
      $upl=1;
      if(!empty($fgal_match_regex)) {
        if(!preg_match("/$fgal_match_regex/",$file,$reqs)) $upl=0;
      }
      if(!empty($fgal_nmatch_regex)) {
        if(preg_match("/$fgal_nmatch_regex/",$file,$reqs)) $upl=0;
      }

      $fp = fopen('temp/'.$file,"rb");
      $data = '';
      $fhash='';
      if($fgal_use_db == 'n') {
        $fhash = md5($name = $file);
        @$fw = fopen($fgal_use_dir.$fhash,"wb");
        if(!$fw) {
          $smarty->assign('msg',tra('Cannot write to this file:').$fhash);
          $smarty->display("styles/$style_base/error.tpl");
          die;
        }
      }
      while(!feof($fp)) {
        if($fgal_use_db == 'y') {
          $data .= fread($fp,8192*16);
        } else {
          $data = fread($fp,8192*16);
          fwrite($fw,$data);
        }
      }
      fclose($fp);
      if($fgal_use_db == 'n') {
        fclose($fw);
        $data='';
      }
      $size = filesize('temp/'.$file);
      $name = $file;
      $type = tiki_get_mime('temp/'.$file);
      $fileId = $this->insert_file($galleryId,$name,$description,$name, $data, $size, $type, $user,$fhash);
      unlink('temp/'.$file);
    }
  }
  closedir($h);
  }
  
  // Added by LeChuck, May 2, 2003
  
  function get_file_info($id) {
    $query = "select * from tiki_files where fileId='$id'";
    $result = $this->query($query);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }	
  
  function replace_file($id,$name,$description) {
  	
  	// Update the fields in the database
  	$name = addslashes(strip_tags($name));
  	$description = addslashes(strip_tags($description));
    $query = "update tiki_files set name='$name', description='$description' where fileId=$id";
    $result = $this->query($query);
    
    // Get the gallery id for the file and update the last modified field
    $now = date("U");
    $galleryId = $this->getOne("select galleryId from tiki_files where fileId='$id'");
    if ($galleryId) {
	    $query = "update tiki_file_galleries set lastModif=$now where galleryId=$galleryId";
	    $this->query($query);
	}
	return $result;
  }
  
}

$filegallib= new FileGalLib($dbTiki);

?>