<?php
 
class DrawLib extends TikiLib {
    
  function DrawLib($db) 
  {
    if(!$db) {
      die("Invalid db object passed to DrawLib constructor");  
    }
    $this->db = $db;  
  }

/*
	drawId integer(12) not null auto_increment,
	name varchar(250),
	filename varchar(250),
	timestamp integer(14),
	user varchar(200),
	primary key(drawId)
*/

  function replace_drawing($drawId,$name, $filename_draw,$filename_pad,$user,$version)
  {
  	$name = addslashes($name);
  	$now = date("U");
  	
    if($drawId) {
    	
  		$query = "update tiki_drawings set
  		name = '$name',
  		$version = $version,
  		filename_draw = '$filename_draw',
  		filename_pad = '$filename_pad',
  		timestamp = '$now',
  		version = $version,
  		user='$user'
  		where drawId = $drawId";
  		$this->qeury($query);  	
    } else {
    	$query = "insert into tiki_drawings(name,filename_draw, filename_pad,timestamp,user, version)
    	values('$name','$filename_draw','$filename_pad',$now,'$user',$version)";
    	$this->query($query);
    }	
  	return true;
  }
  
  function update_drawing($name,$hash,$user)
  {
  	// Updates a drawing, the last version (if existed) is changed to 
  	// $hash while a new version is inserted with $name
  	$name = addslashes($name);
  	$version = $this->getOne("select max(version) from tiki_drawings where name='$name'");
  	if(!$version) $version = 0;
  	$version = $version + 1;
  	$this->replace_drawing(0,$name,'',$hash,$user,$version);
	$maxversions = $this->get_preference("maxVersions",0);
	$keep = $this->get_preference('keep_versions',0);
	$cant = $this->getOne("select count(*) from tiki_drawings where name='$name'");
	$now = date("U");
    $oktodel = $now - ($keep * 24 * 3600);
	if($cant>$maxversions) {
		$query = "select * from tiki_drawings where name='$name' and timestamp <= $oktodel limit $maxversions,-1";
    	$result = $this->query($query);
    	while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
			$query = "delete from tiki_drawings where drawId=".$res['drawId'];
			$this->query($query);			
		}
  	}
  }
  
  function set_drawing_gif($name,$hash)
  {
	$name = addslashes($name);
  	$id = $this->getOne("select max(drawId) from tiki_drawings where name='$name'");
  	if($id) {
  	  $query = "update tiki_drawings set filename_draw='$hash' where drawId=$id";
  	  $this->query($query);
  	}
  }
  
  function get_drawing($drawId)
  {
  	$query = "select * from tiki_drawings where drawId=$drawId";
  	$result = $this->query($query);	
  	$res = $result->fetchRow(DB_FETCHMODE_ASSOC);
  	return $res;
  	
  }
  
  function remove_drawing($drawId)
  {
		global $tikidomain;
  	$info = $this->get_drawing($drawId);
  	$f1 = "img/wiki/$tikidomain".$info['filename_draw'];
  	$f2 = "img/wiki/$tikidomain".$info['filename_pad'];
  	$max = $this->getOne("select count(*) from tiki_drawings where name='".$info['name']."'");
  	@unlink($f1);
	@unlink($f2);
  	if($max == 1) {
  		$f1 = "img/wiki/$tikidomain".$name.".pad_xml";
		unlink($f1);
		$f1 = "img/wiki/$tikidomain".$name.".gif";
	  	unlink($f1);
  	}

  	$query = "delete from tiki_drawings where drawId=$drawId";
  	$this->query($query);
  	$max = $this->getOne("select max(version) from tiki_drawings where name='".$info['name']."'");
    $query = "select * from tiki_drawings where name = '".$info['name']."' and version=$max";
  	$result = $this->query($query);	
  	$res = $result->fetchRow(DB_FETCHMODE_ASSOC);	
  	$f1 = "img/wiki/$tikidomain".$res['filename_draw'];
  	$f2 = "img/wiki/$tikidomain".$res['name'].'.gif';
	copy($f1,$f2);
	$f1 = "img/wiki/$tikidomain".$res['filename_pad'];
	$f2 = "img/wiki/$tikidomain".$res['name'].'.pad_xml';
  	copy($f1,$f2);
  }
  
  function remove_all_drawings($name)
  {
  	$name = addslashes($name);
  	$query = "delete from tiki_drawings where name='$name'";
  	$this->query($query);
  }
  
  function list_drawings($offset,$maxRecords,$sort_mode,$find)
  {
   
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
	$findesc = $this->qstr('%'.$find.'%');
    $mid=" where  (name like $findesc)";
    } else {
      $mid=" ";
    }
    
    $query = "select * from tiki_drawings $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_drawings $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $last_version = $this->getOne("select max(version) from tiki_drawings where name='".$res['name']."'");
      if($res['version']==$last_version) {
        $res['versions']=$this->getOne("select count(*) from tiki_drawings where name='".$res['name']."'");
        $ret[] = $res;
      }
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  function list_drawing_history($name,$offset,$maxRecords,$sort_mode,$find)
  {
    $name = addslashes($name);
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
	$findesc = $this->qstr('%'.$find.'%');
    $mid=" where name='$name' and (name like $findesc)";
    } else {
      $mid=" where name='$name' ";
    }
    $query = "select * from tiki_drawings $mid order by $sort_mode,version desc limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_drawings $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $res['versions']=$this->getOne("select count(*) from tiki_drawings where name='".$res['name']."'");
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }

 
}

$drawlib= new DrawLib($dbTiki);



?>
