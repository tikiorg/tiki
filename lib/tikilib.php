<?php
class TikiLib {
  var $db;  // The PEAR db object used to access the database
    
  function TikiLib($db) 
  {
    if(!$db) {
      die("Invalid db object passed to UsersLib constructor");  
    }
    $this->db = $db;  
  }
  
  function sql_error($query, $result) 
  {
    trigger_error("MYSQL error:  ".$result->getMessage()." in query:<br/>".$query."<br/>",E_USER_WARNING);
    die;
  }
  
  /* Dynamic content generation system */
  function remove_contents($contentId) 
  {
    $query = "delete from tiki_programmed_content where contentId=$contentId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $query = "delete from tiki_content where contentId=$contentId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
  }
  
  function list_content($offset = 0,$maxRecords = -1,$sort_mode = 'contentId_desc', $find='')
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where description like '%".$find."%' ";  
    } else {
      $mid=''; 
    }
    $query = "select * from tiki_content $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_content";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $cant = $this->db->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      // Add actual version
      // Add number of programmed versions
      // Add next programmed version
      // Add number of old versions
      $now = date("U");
      $id = $res["contentId"];
      $query = "select count(*) from tiki_programmed_content where publishDate>$now and contentId=$id";
      $res["future"] = $this->db->getOne($query);
      $query = "select max(publishDate) from tiki_programmed_content where contentId=$id and publishDate<=$now";
      $res["actual"] = $this->db->getOne($query);
      $query = "select min(publishDate) from tiki_programmed_content where contentId=$id and publishDate:$now";
      $res["next"] = $this->db->getOne($query);
      $query = "select count(*) from tiki_programmed_content where contentId = $id and publishdate<$now";
      $res["old"] = $this->db->getOne($query);
      if($res["old"]>0) $res["old"]--;
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;  	
  }
  
  function get_actual_content_date($contentId)
  {
    $now = date("U");
    $query = "select max(publishDate) from tiki_programmed_content where contentId=$contentId and publishDate<=$now";
    $res = $this->db->getOne($query);
    return $res;
  }

  function get_actual_content($contentId)
  {
    $data ='';
    $now = date("U");
    $query = "select max(publishDate) from tiki_programmed_content where contentId=$contentId and publishDate<=$now";
    $res = $this->db->getOne($query);
    $query = "select data from tiki_programmed_content where contentId=$contentId and publishDate=$res";
    $data = $this->db->getOne($query);
    return $data;
  }
  

  
  function get_next_content($contentId)
  {
    $now = date("U");
    $query = "select min(publishDate) from tiki_programmed_content where contentId=$contentId and publishDate>$now";
    $res = $this->db->getOne($query);
    return $res;
  }

  function list_programmed_content($contentId,$offset = 0,$maxRecords = -1,$sort_mode = 'publishDate_desc', $find='')
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where contentId=$contentId and data like '%".$find."%' ";  
    } else {
      $mid="where contentId=$contentId"; 
    }
    $query = "select * from tiki_programmed_content $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_programmed_content where contentId=$contentId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $cant = $this->db->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;  	
  }
  
  function replace_programmed_content($pId,$contentId,$publishDate, $data)
  {
    $data = addslashes($data);
    if(!$pId) {
      $query = "replace into tiki_programmed_content(contentId,publishDate,data) values($contentId,$publishDate, '$data')";
      $result = $this->db->query($query);
      if(DB::isError($result)) $this->sql_error($query, $result);
      $query = "select max(pId) from tiki_programmed_content where publishDate=$publishDate and data='$data'";
      $id = $this->db->getOne($query);
      
    } else {
      $query = "update tiki_programmed_content set contentId=$contentId, publishDate=$publishDate, data='$data' where pId=$pId";
      $result = $this->db->query($query);
      if(DB::isError($result)) $this->sql_error($query, $result);
      $id = $pId;
    }
    return $id;
  }
  
  
  
  function remove_programmed_content($id)
  {
    $query = "delete from tiki_programmed_content where pId=$id";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    return true;
  }
  
  function get_content($id) 
  {
    $query = "select * from tiki_content where contentId=$id";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }
  
  function get_programmed_content($id) 
  {
    $query = "select * from tiki_programmed_content where pId=$id";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }
  
  function replace_content($contentId,$description)
  {
    $description = addslashes($description);
    if($contentId>0) {
      $query = "update tiki_content set description='$description' where contentId=$contentId";
      $result = $this->db->query($query);
      if(DB::isError($result)) $this->sql_error($query, $result);
      return $contentId;
    } else {
      $query = "insert into tiki_content(description) values('$description')";
      $result = $this->db->query($query);
      if(DB::isError($result)) $this->sql_error($query, $result);
      $query = "select max(contentId) from tiki_content where description = '$description'";
      $id = $this->db->getOne($query);
      return $id;
    }
  }
  
  

  function remove_orphan_images()
  {
    $merge  = Array();
    
    // Find images in tiki_pages
    $query = "select data from tiki_pages";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      preg_match_all("/src=\"([^\"]+)\"/",$res["data"],$reqs1);
      preg_match_all("/src=\'([^\']+)\'/",$res["data"],$reqs2);
      preg_match_all("/src=([A-Za-z0-9:\?\=\/\.\-\_]+)\}/",$res["data"],$reqs3);
      $merge = array_merge($merge, $reqs1[1],$reqs2[1],$reqs3[1]);
      $merge = array_unique($merge);
    }  

    // Find images in Tiki articles    
    $query = "select body from tiki_articles";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      preg_match_all("/src=\"([^\"]+)\"/",$res["body"],$reqs1);
      preg_match_all("/src=\'([^\']+)\'/",$res["body"],$reqs2);
      preg_match_all("/src=([A-Za-z0-9:\?\=\/\.\-\_]+)\}/",$res["body"],$reqs3);
      $merge = array_merge($merge, $reqs1[1],$reqs2[1],$reqs3[1]);
      $merge = array_unique($merge);
    }  
    
    // Find images in tiki_submissions
    $query = "select body from tiki_submissions";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      preg_match_all("/src=\"([^\"]+)\"/",$res["body"],$reqs1);
      preg_match_all("/src=\'([^\']+)\'/",$res["body"],$reqs2);
      preg_match_all("/src=([A-Za-z0-9:\?\=\/\.\-\_]+)\}/",$res["body"],$reqs3);
      $merge = array_merge($merge, $reqs1[1],$reqs2[1],$reqs3[1]);
      $merge = array_unique($merge);
    }

    // Find images in tiki_blog_posts    
    $query = "select data from tiki_blog_posts";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      preg_match_all("/src=\"([^\"]+)\"/",$res["data"],$reqs1);
      preg_match_all("/src=\'([^\']+)\'/",$res["data"],$reqs2);
      preg_match_all("/src=([A-Za-z0-9:\?\=\/\.\-\_]+)\}/",$res["data"],$reqs3);
      $merge = array_merge($merge, $reqs1[1],$reqs2[1],$reqs3[1]);
      $merge = array_unique($merge);
    }  
    
    $positives = Array();
    foreach($merge as $img) {
      if(strstr($img,'show_image')) {
        preg_match("/id=([0-9]+)/",$img,$rq);
        $positives[] = $rq[1];
      }
    }
    
    $query = "select imageId from tiki_images where galleryId=0";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $id = $res["imageId"];
      if(!in_array($id,$positives)) {
        $this->remove_image($id);
      }
    }
    
  }

  // Banner functions
  function select_banner($zone)
  {
    // Things to check
    // UseDates and dates
    // Hours
    // weekdays
    // zone
    // maxImpressions and impressions
    $dw = strtolower(date("D"));
    $hour = date("H").date("i");
    $now = date("U");
    // 
    // 
    $query = "select * from tiki_banners where $dw = 'y' and  hourFrom<=$hour and hourTo>=$hour and
    ( ((useDates = 'y') and (fromDate<=$now and toDate>=$now)) or (useDates = 'n') ) and
    impressions<maxImpressions and zone='$zone'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $rows = $result->numRows();
    if(!$rows) return false;
    $bid = rand(0,$rows-1);
    //print("Rows: $rows bid: $bid");
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC,$bid);
    $id= $res["bannerId"];
    
    switch($res["which"]) {
    case 'useHTML':
      $raw = $res["HTMLData"];
      break;
    case 'useImage':
      $raw = "<a target='_blank' href='banner_click.php?id=".$res["bannerId"]."&amp;url=".urlencode($res["url"])."'><img border='0' src=\"banner_image.php?id=".$id."\" /></a>";
      break;
    case 'useFixedURL':
      $fp = fopen($res["fixedURLData"],"r");
      if ($fp) {
        $raw = fread($fp,999999);
      }
      fclose($fp);
      break;
    case 'useText':
      $raw = "<a target='_blank' class='bannertext' href='banner_click.php?id=".$res["bannerId"]."&amp;url=".urlencode($res["url"])."'>".$res["textData"]."</a>";
      break;
    } 
    // Increment banner impressions here
    $id = $res["bannerId"];
    $query = "update tiki_banners set impressions = impressions + 1 where bannerId = $id";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    return $raw;
  }

  function add_click($bannerId) 
  {
    $query = "update tiki_banners set clicks = clicks + 1 where bannerId=$bannerId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
  }  
    
  function list_banners($offset = 0,$maxRecords = -1,$sort_mode = 'created_desc', $find='', $user)
  {
    if($user == 'admin') {
      $mid = '';
    } else {
      $mid = "where client = '$user'";
    }
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      if($mid) {
        $mid.=" and url like '%".$find."%' ";  
      } else {
        $mid.=" where url like '%".$find."%' ";  
      }
    } 
    $query = "select * from tiki_banners $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_banners";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $cant = $this->db->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;  	
  }
  
  function remove_banner($bannerId)
  {
    $query = "delete from tiki_banners where bannerId=$bannerId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
  }
  
  function get_banner($bannerId) 
  {
    $query = "select * from tiki_banners where bannerId=$bannerId";
    $result = $this->db->query($query);
    if(!$result->numRows()) return false;
    if(DB::isError($result)) $this->sql_error($query, $result);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }
  function replace_banner($bannerId, $client, $url, $title='', $alt='', $use, $imageData,$imageType,$imageName,
                          $HTMLData, $fixedURLData, $textData, $fromDate, $toDate, $useDates, 
                          $mon, $tue, $wed, $thu, $fri, $sat, $sun,
                          $hourFrom, $hourTo, $maxImpressions, $zone)
  {
    $url = addslashes($url);
    $title = addslashes($title);
    $alt = addslashes($alt);
    $imageData = addslashes(urldecode($imageData));
    //$imageData = '';
    $imageName = addslashes($imageName);
    $HTMLData = addslashes($HTMLData);
    $fixedURLData = addslashes($fixedURLData);
    $textData = addslashes($textData);
    $zone = addslashes($zone);
    
    $now = date("U");
    if($bannerId) {
      $query = "update tiki_banners set 
                client = '$client',
                url = '$url',
                title = '$title',
                alt = '$alt',
                which = '$use',
                imageData = '$imageData',
                imageType = '$imageType',
                imageName = '$imageName',
                HTMLData = '$HTMLData',
                fixedURLData = '$fixedURLData',
                textData = '$textData',
                fromDate = $fromDate,
                toDate = $toDate,
                useDates = '$useDates',
                created = $now,
                zone = '$zone',
                hourFrom = '$hourFrom',
                hourTo = '$hourTo',
                maxImpressions = $maxImpressions where bannerId=$bannerId";
       $result = $this->db->query($query);
       if(DB::isError($result)) $this->sql_error($query, $result);
    } else {
      $query = "insert into tiki_banners(client, url, title, alt, which, imageData, imageType, HTMLData, 
                fixedURLData, textData, fromDate, toDate, useDates, mon, tue, wed, thu, fri, sat, sun,
                hourFrom, hourTo, maxImpressions,created,zone,imageName,impressions,clicks) 
                values('$client','$url','$title','$alt','$use','$imageData','$imageType','$HTMLData',
                '$fixedURLData', '$textData', $fromDate, $toDate, '$useDates', '$mon','$tue','$wed','$thu',
                '$fri','$sat','$sun','$hourFrom','$hourTo',$maxImpressions,$now,'$zone','$imageName',0,0)";
      $result = $this->db->query($query);
      if(DB::isError($result)) $this->sql_error($query, $result);
      $query = "select max(bannerId) from tiki_banners where created=$now";
      $bannerId = $this->db->getOne($query);
    }
    return $bannerId;
    
  }
                          
  
  function banner_add_zone($zone)
  {
    $zone = addslashes($zone);
    $query = "replace into tiki_zones(zone) values('$zone')";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    return true;
  }
  
  function banner_get_zones()
  {
    $query = "select * from tiki_zones";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $ret= Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
    	$ret[]=$res;
    }	
    return $ret;
  }
  
  function banner_remove_zone($zone)
  {
    $query = "delete from tiki_zones where zone='$zone'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    /*
    $query = "delete from tiki_banner_zones where zoneName='$zone'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    */
    return true;
  }
  
  /* Hot words methods */
  function get_hotwords()
  {
    $query = "select * from tiki_hotwords";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $ret= Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
    	$ret[$res["word"]]=$res["url"];
    }	
    return $ret;
  }
  
  function list_hotwords($offset = 0,$maxRecords = -1,$sort_mode = 'word_desc', $find='')
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where word like '%".$find."%' ";  
    } else {
      $mid=''; 
    }
    $query = "select * from tiki_hotwords $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_hotwords";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $cant = $this->db->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;  	
  }
  
  function add_hotword($word,$url)
  {
    $word=addslashes($word);
    $url=addslashes($url);
    $query = "replace into tiki_hotwords(word,url) values('$word','$url')";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    return true;	
  }
  
  function remove_hotword($word)
  {
    $query = "delete from tiki_hotwords where word='$word'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);    	
  }
  
  /* BLOG METHODS */
  function list_blogs($offset = 0,$maxRecords = -1,$sort_mode = 'created_desc', $find='')
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where title like '%".$find."%' or description like '%".$find."%' ";  
    } else {
      $mid=''; 
    }
    $query = "select * from tiki_blogs $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_blogs";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $cant = $this->db->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }

  function add_blog_hit($blogId)
  {
    $query = "update tiki_blogs set hits = hits+1 where blogId=$blogId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    return true;
  }
   
  function replace_blog($title,$description,$user,$public,$maxPosts,$blogId)
  { 
    $title = addslashes($title);
    $description = addslashes($description);
    $now = date("U");
    if($blogId) {
      $query = "update tiki_blogs set title='$title',description='$description',user='$user',public='$public',lastModif=$now,maxPosts=$maxPosts where blogId=$blogId";
    } else {
      $query = "insert into tiki_blogs(created,lastModif,title,description,user,public,posts,maxPosts,hits)
                       values($now,$now,'$title','$description','$user','$public',0,$maxPosts,0)";
    }
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $query = "select max(blogId) from tiki_blogs where lastModif=$now";
    $id=$this->db->getOne($query);
    return $id;
  }
  
  function get_blog($blogId)
  {
    $query = "select * from tiki_blogs where blogId=$blogId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);                     
    if($result->numRows()) {
      $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    } else {
      return false;
    }
    return $res;
  }
  
  function list_blog_posts($blogId, $offset = 0,$maxRecords = -1,$sort_mode = 'created_desc', $find='', $date='')
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where blogId=$blogId and data like '%".$find."%' ";  
    } else {
      $mid="where blogId=$blogId "; 
    }
    if($date) {
      $mid.=" and  created<=$date ";
    }
    $query = "select * from tiki_blog_posts $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_blog_posts where blogId=$blogId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $cant = $this->db->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  function blog_post($blogId,$data,$user)
  {
    // update tiki_blogs and call activity functions
    $data=addslashes($data);
    $now = date("U");
    $query = "insert into tiki_blog_posts(blogId,data,created,user) values($blogId,'$data',$now,'$user')";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $query = "select max(postId) from tiki_blog_posts where created=$now and user='$user'";
    $id = $this->db->getOne($query);
    $query = "update tiki_blogs set lastModif=$now,posts=posts+1 where blogId=$blogId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $this->add_blog_activity($blogId);
    return $id;
  }
  
  function list_user_blogs($user,$include_public=false)
  {
    $query = "select * from tiki_blogs where user='$user'";
    if($include_public) {
      $query.=" or public='y'";
    }
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $ret=Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[]=$res;
    }
    return $ret; 
  }
  
  function remove_blog($blogId)
  {
    $query = "delete from tiki_blogs where blogId=$blogId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $query = "delete from tiki_blog_posts where blogId=$blogId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    return true;
  }
  
  function remove_post($postId)
  {
    $query = "select blogId from tiki_blog_posts where postId=$postId";
    $blogId = $this->db->getOne($query);
    $query = "delete from tiki_blog_posts where postId=$postId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $query = "update tiki_blogs set posts=posts-1 where blogId=$blogId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    return true;
  }
  
  function get_post($postId)
  {
    $query = "select * from tiki_blog_posts where postId=$postId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);                     
    if($result->numRows()) {
      $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    } else {
      return false;
    }
    return $res;
  }
  
  function update_post($postId,$data,$user)
  { 
    $data = addslashes($data);
    $query = "update tiki_blog_posts set data='$data',user='$user' where postId=$postId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);                     
  }
  
  function list_posts($offset = 0,$maxRecords = -1,$sort_mode = 'created_desc', $find='')
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where data like '%".$find."%' ";  
    } else {
      $mid=''; 
    }
    $query = "select * from tiki_blog_posts $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_blog_posts";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $cant = $this->db->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $blogId=$res["blogId"];
      $query = "select title from tiki_blogs where blogId=$blogId";
      $res["blogTitle"]=$this->db->getOne($query);
      $res["size"]=strlen($res["data"]);
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  function list_user_posts($user,$offset = 0,$maxRecords = -1,$sort_mode = 'created_desc', $find='') 
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where user=$user and data like '%".$find."%' ";  
    } else {
      $mid=' where user=$user '; 
    }
    $query = "select * from tiki_blog_posts $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_blog_posts";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $cant = $this->db->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  
  function add_blog_activity($blogId)
  {
    
    //Caclulate activity, update tiki_blogs and purge activity table
    $today = mktime(0,0,0,date("m"),date("d"),date("Y"));
    $day0 = $today - (24*60*60);
    $day1 = $today - (2*24*60*60);
    $day2 = $today - (3*24*60*60);
    // Purge old activity
    $query="delete from tiki_blog_activity where day<$day2";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    // Register new activity
    $query = "select * from tiki_blog_activity where blogId=$blogId and day=$today";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    if($result->numRows()) {
      $query = "update tiki_blog_activity set posts=posts+1 where blogId=$blogId and day=$today";
    } else {
      $query = "insert into tiki_blog_activity(blogId,day,posts) values($blogId,$today,1)";
    }
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    // Calculate activity
    $query = "select posts from tiki_blog_activity where blogId=$blogId and day=$today";
    $vtoday = $this->db->getOne($query);
    $query = "select posts from tiki_blog_activity where blogId=$blogId and day=$day0";
    $day0 = $this->db->getOne($query);
    $query = "select posts from tiki_blog_activity where blogId=$blogId and day=$day1";
    $day1 = $this->db->getOne($query);
    $query = "select posts from tiki_blog_activity where blogId=$blogId and day=$day2";
    $day2 = $this->db->getOne($query);
    $activity = (2 * $vtoday) + ($day0) + (0.5 * $day1) + (0.25 * $day2);
    // Update tiki_blogs with activity information
    $query = "update tiki_blogs set activity=$activity where blogId=$blogId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
  }
  
  
  /* CMS functions -ARTICLES- & -SUBMISSIONS- */
  function list_articles($offset = 0,$maxRecords = -1,$sort_mode = 'publishDate_desc', $find='', $date='')
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where title like '%".$find."%' or heading like '%".$find."%' or body like '%".$find."%' ";  
    } else {
      $mid=''; 
    }
    if($date) {
      if($mid) {
        $mid.=" and  publishDate<=$date ";
      } else {
        $mid=" where publishDate<=$date ";
      } 
    }
    $query = "select * from tiki_articles $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_articles";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $cant = $this->db->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      if(empty($res["body"])) {
        $res["isEmpty"] = 'y'; 
      } else {
        $res["isEmpty"] = 'n'; 
      }
      if(strlen($res["image_data"])>0) {
        $res["hasImage"] = 'y';
      } else {
        $res["hasImage"] = 'n';
      }
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  function list_submissions($offset = 0,$maxRecords = -1,$sort_mode = 'publishDate_desc', $find='', $date='')
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where title like '%".$find."%' or heading like '%".$find."%' or body like '%".$find."%' ";  
    } else {
      $mid=''; 
    }
    if($date) {
      if($mid) {
        $mid.=" and  publishDate<=$date ";
      } else {
        $mid=" where publishDate<=$date ";
      } 
    }
    $query = "select * from tiki_submissions $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_submissions";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $cant = $this->db->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      if(empty($res["body"])) {
        $res["isEmpty"] = 'y'; 
      } else {
        $res["isEmpty"] = 'n'; 
      }
      if(strlen($res["image_data"])>0) {
        $res["hasImage"] = 'y';
      } else {
        $res["hasImage"] = 'n';
      }
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }

  function get_article($articleId) 
  {
    $query = "select * from tiki_articles where articleId=$articleId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);                     
    if($result->numRows()) {
      $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    } else {
      return false;
    }
    return $res;
  }
  
  function get_submission($subId) 
  {
    $query = "select * from tiki_submissions where subId=$subId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);                     
    if($result->numRows()) {
      $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    } else {
      return false;
    }
    return $res;
  }
  
  function approve_submission($subId) 
  {
    $data = $this->get_submission($subId);
    if(!$data) return false;
    if(!$data["image_x"]) $data["image_x"]=0;
    if(!$data["image_y"]) $data["image_y"]=0;
    $this->replace_article ($data["title"],$data["authorName"],$data["topicId"],$data["useImage"],$data["image_name"],$data["image_size"],$data["image_type"],$data["image_data"],$data["heading"],$data["body"],$data["publishDate"],$data["author"],0,$data["image_x"],$data["image_y"]);  
    $this->remove_submission($subId);
  }
  
  function add_article_hit($articleId)
  {
    $query = "update tiki_articles set reads=reads+1 where articleId=$articleId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);                     
    return true;
  }

  function remove_article($articleId)
  {
    if($articleId) {
      $query = "delete from tiki_articles where articleId=$articleId";
      $result = $this->db->query($query);
      if(DB::isError($result)) $this->sql_error($query,$result);                     
      return true;
    }
  }
  
  function remove_submission($subId)
  {
    if($subId) {
      $query = "delete from tiki_submissions where subId=$subId";
      $result = $this->db->query($query);
      if(DB::isError($result)) $this->sql_error($query,$result);                     
      return true;
    }
  }
  
  function replace_article ($title,$authorName,$topicId,$useImage,$imgname,$imgsize,$imgtype,$imgdata,$heading,$body,$publishDate,$user,$articleId,$image_x,$image_y)
  {
    $title = addslashes($title);
    $heading = addslashes($heading);
    $authorName = addslashes($authorName);
    $imgdata = addslashes($imgdata);
    $imgname = addslashes($imgname);
    $body = addslashes($body);
    $hash = md5($title.$heading.$body);
    $now = date("U");

    $query = "select name from tiki_topics where topicId = $topicId";
    $topicName = $this->db->getOne($query);
    $size = strlen($body);

    if($articleId) {
      // Update the article
      $query = "update tiki_articles set
                title = '$title',
                authorName = '$authorName',
                topicId = $topicId,
                topicName = '$topicName',
                size = $size,
                useImage = '$useImage',
                image_name = '$imgname',
                image_type = '$imgtype',
                image_size = '$imgsize',
                image_data = '$imgdata',
                image_x = $image_x,
                image_y = $image_y,
                heading = '$heading',
                body = '$body',
                publishDate = $publishDate,
                created = $now,
                author = '$user' 
                where articleId = $articleId";
      $result = $this->db->query($query);
      if(DB::isError($result)) $this->sql_error($query,$result);                     
    } else {
      // Insert the article
      $query = "insert into tiki_articles(title,authorName,topicId,useImage,image_name,image_size,image_type,image_data,publishDate,created,heading,body,hash,author,reads,votes,points,size,topicName,image_x,image_y)
                         values('$title','$authorName',$topicId,'$useImage','$imgname','$imgsize','$imgtype','$imgdata',$publishDate,$now,'$heading','$body','$hash','$user',0,0,0,$size,'$topicName',$image_x,$image_y)";
      $result = $this->db->query($query);
      if(DB::isError($result)) $this->sql_error($query,$result);                     
    }
    $query = "select max(articleId) from tiki_articles where created = $now and title='$title' and hash='$hash'";
    $id=$this->db->getOne($query);
    return $id;
  }
  
  function replace_submission ($title,$authorName,$topicId,$useImage,$imgname,$imgsize,$imgtype,$imgdata,$heading,$body,$publishDate,$user,$subId)
  {
    $title = addslashes($title);
    $heading = addslashes($heading);
    $authorName = addslashes($authorName);
    $imgdata = addslashes($imgdata);
    $imgname = addslashes($imgname);
    $body = addslashes($body);
    $hash = md5($title.$heading.$body);
    $now = date("U");

    $query = "select name from tiki_topics where topicId = $topicId";
    $topicName = $this->db->getOne($query);
    $size = strlen($body);

    if($subId) {
      // Update the article
      $query = "update tiki_submissions set
                title = '$title',
                authorName = '$authorName',
                topicId = $topicId,
                topicName = '$topicName',
                size = $size,
                useImage = '$useImage',
                image_name = '$imgname',
                image_type = '$imgtype',
                image_size = '$imgsize',
                image_data = '$imgdata',
                heading = '$heading',
                body = '$body',
                publishDate = $publishDate,
                created = $now,
                author = '$user' 
                where subId = $subId";
      $result = $this->db->query($query);
      if(DB::isError($result)) $this->sql_error($query,$result);                     
    } else {
      // Insert the article
      $query = "insert into tiki_submissions(title,authorName,topicId,useImage,image_name,image_size,image_type,image_data,publishDate,created,heading,body,hash,author,reads,votes,points,size,topicName)
                         values('$title','$authorName',$topicId,'$useImage','$imgname','$imgsize','$imgtype','$imgdata',$publishDate,$now,'$heading','$body','$hash','$user',0,0,0,$size,'$topicName')";
      $result = $this->db->query($query);
      if(DB::isError($result)) $this->sql_error($query,$result);                     
    }
    $query = "select max(subId) from tiki_submissions where created = $now and title='$title' and hash='$hash'";
    $id=$this->db->getOne($query);
    return $id;
  }
  
  /* CMS functions -TOPICS -*/
  function add_topic($name,$imagename,$imagetype,$imagesize,$imagedata)
  {
    $now=date("U");
    $imagename=addslashes($imagename);
    $name=addslashes($name);
    $imagedata=addslashes($imagedata);
    $query = "insert into tiki_topics(name,image_name,image_type,image_size,image_data,active,created)
                     values('$name','$imagename','$imagetype',$imagesize,'$imagedata','y',$now)";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);                     
    $query = "select max(topicId) from tiki_topics where created=$now and name='$name'";
    $topicId = $this->db->getOne($query);
    return $topicId;
  }
  
  function remove_topic($topicId)
  {
    $query = "delete from tiki_topics where topicId=$topicId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);                     
    return true;
  }
  
  function activate_topic($topicId)
  {
    $query = "update tiki_topics set active='y' where topicId=$topicId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);                     
  }
  
  function deactivate_topic($topicId)
  {
    $query = "update tiki_topics set active='n' where topicId=$topicId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);                     
  }
  
  function get_topic_image($topicId) 
  {
    $query = "select image_name,image_size,image_type,image_data from tiki_topics where topicId=$topicId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);                     
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }
  
  function get_topic($topicId)
  {
    $query = "select topicId,name,image_name,image_size,image_type from tiki_topics where topicId=$topicId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);                     
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }
  
  function list_topics()
  {
    $query = "select topicId,name,image_name,image_size,image_type,active from tiki_topics order by name";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);                     
    $ret=Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[]=$res;
    }
    return $ret;
  }
  
  function list_active_topics()
  {
    $query = "select * from tiki_topics where active='y'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);                     
    $ret=Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[]=$res;
    }
    return $ret;
  }
  
  function add_featured_link($url,$title,$description='') 
  {
    $title=addslashes($title);
    $url=addslashes($url);
    $description=addslashes($description);
    $query = "replace tiki_featured_links(url,title,description) values('$url','$title','$description')";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
  }
  
  function remove_featured_link($url)
  {
    $query = "delete from tiki_featured_links where url='$url'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
  }
  
  function get_featured_links($max=10)
  {
    $query = "select * from tiki_featured_links limit 0,$max";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res;
    }
    return $ret; 
  }
  
  function capture_images($data)
  {
    $cacheimages = $this->get_preference("cacheimages",'y');
    if($cacheimages != 'y') return $data;	
    preg_match_all("/src=\"([^\"]+)\"/",$data,$reqs1);
    preg_match_all("/src=\'([^\']+)\'/",$data,$reqs2);
    preg_match_all("/src=([A-Za-z0-9:\?\=\/\.\-\_]+)\}/",$data,$reqs3);
    $merge = array_merge($reqs1[1],$reqs2[1],$reqs3[1]);
    $merge = array_unique($merge);
    // Now for each element in the array capture the image and
    // if the capture was succesful then change the reference to the
    // internal image
    $page_data = $data;
    foreach($merge as $img) {
      // This prevents caching images
      if(!strstr($img,"show_image.php") && !strstr($img,"nocache")) {
      //print("Procesando: $img<br/>");
      $fp = fopen($img,"r");
      if($fp) {
        $data = fread($fp,4999999);
        //print("Imagen leida:".strlen($data)." bytes<br/>");
        fclose($fp);
        if(strlen($data)>0) {
          $url_info = parse_url($img);
          $pinfo = pathinfo($url_info["path"]);
          $type = "image/".$pinfo["extension"];
          $name = $pinfo["basename"];
          $size = strlen($data);
          $url = $img;
          
          if(function_exists("ImageCreateFromString")&&(!strstr($type,"gif"))) {
            // Now create image and thumbnail
            $img = imagecreatefromstring($data);
            $size_x = imagesx($img);
            $size_y = imagesy($img);
            // Create thumbnail here 
            // Use the gallery preferences to get the data
            $t = imagecreate(100,100);
            imagecopyresized ( $t, $img, 0,0,0,0, 100,100, $size_x, $size_y);
            $tmpfname = tempnam ("/tmp", "FOO").'.jpg';     
            imagejpeg($t,$tmpfname);
            // Now read the information
            $fp = fopen($tmpfname,"r");
            $t_data = fread($fp, filesize($tmpfname));
            fclose($fp);
            unlink($tmpfname);
            $t_pinfo = pathinfo($tmpfname);
            $t_type = $t_pinfo["extension"];
            $t_type='image/'.$t_type;
            $imageId = $this->insert_image(0,'','',$name, $type, $data, $size, $size_x, $size_y, 'admin',$t_data,$t_type);
            //print("Imagen generada en $imageId<br/>");
          } else {
            //print("No GD detected generating image without thumbnail<br/>");
            $imageId = $this->insert_image(0,'','',$name, $type, $data, $size, 100, 100, 'admin','','');
          }
          // Now change it!
          //print("Changing $url to imageId: $imageId");
          $foo = parse_url($_SERVER["REQUEST_URI"]);
          $foo2=str_replace("tiki-editpage","show_image",$foo["path"]);
          $showurl = $_SERVER["SERVER_NAME"].$foo2;
          $page_data = str_replace("$url","http://$showurl?id=$imageId",$page_data);
        } // if strlen
      } // if $fp
      }
    } // foreach
    return $page_data;
  }
  
  function update_session($sessionId)
  {
    $now = date("U");
    $oldy = $now-(5*60);
    $query = "replace into tiki_sessions(sessionId,timestamp) values('$sessionId',$now)";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $query = "delete from tiki_sessions where timestamp<$oldy";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    return true;
  }
  
  function count_sessions()
  {
    $query = "select count(*) from tiki_sessions";
    $cant = $this->db->getOne($query);
    return $cant;
  }

  function replace_user_module($name,$title,$data)
  {
    $name = addslashes($name);
    $title = addslashes($title);
    $data = addslashes($data);
    if( (!empty($name)) && (!empty($title)) && (!empty($data)) ) {
      $query = "replace into tiki_user_modules(name,title,data) values('$name','$title','$data')";
      $result = $this->db->query($query);
      if(DB::isError($result)) $this->sql_error($query,$result);
      return true;
    }
  }
  
  function assign_module($name,$title,$position,$order,$cache_time=0,$rows=10,$groups)
  {
    $name = addslashes($name);
    $groups = addslashes($groups);
    $query = "delete from tiki_modules where name='$name'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $query = "insert into tiki_modules(name,title,position,ord,cache_time,rows,groups) values('$name','$title','$position',$order,$cache_time,$rows,'$groups')";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    return true;
  }
  
  function get_assigned_module($name) 
  {
    $query = "select * from tiki_modules where name='$name'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    if($res["groups"]) {
      $grps = unserialize($res["groups"]);
      $res["module_groups"]='';
      foreach($grps as $grp) {
      	$res["module_groups"].=" $grp ";
      }
    }
    return $res;
  }
  
  function unassign_module($name) 
  {
    $query = "delete from tiki_modules where name='$name'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    return true;
  }
  
  function get_rows($name)
  {
    $query = "select rows from tiki_modules where name='$name'";
    $rows = $this->db->getOne($query);
    if($rows==0) $rows=10;
    return $rows;
  }
  
  function module_up($name) 
  {
    $query = "update tiki_modules set ord=ord-1 where name='$name'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    return true;
  }
  
  function module_down($name)
  {
    $query = "update tiki_modules set ord=ord+1 where name='$name'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    return true;
  }
  
  function get_assigned_modules($position)
  {
    $query = "select name,title,position,ord,cache_time,rows,groups from tiki_modules where position='$position' order by ord asc";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      if($res["groups"]) {
        $grps = unserialize($res["groups"]);
        $res["module_groups"]='';
        foreach($grps as $grp) {
      	  $res["module_groups"].=" $grp ";
        }
      } else {
      	$res["module_groups"]='&nbsp;';
      }
      $ret[] = $res;
    }
    return $ret;
  }
  
  function get_all_modules() 
  {
    $user_modules = $this->list_user_modules();
    $all_modules=Array();
    foreach($user_modules["data"] as $um) {
      $all_modules[]=$um["name"];
    }
    // Now add all the system modules
    $h = opendir("templates/modules");
    while (($file = readdir($h)) !== false) {
      if(substr($file,0,3)=='mod') {
        if(!strstr($file,"nocache")){
          $name = substr($file,4,strlen($file)-8);
          $all_modules[]=$name;
        }
      }
    }  
    closedir($h);
    return $all_modules;
  }
  
  function is_user_module($name)
  {
    $query = "select name from tiki_user_modules where name='$name'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    return $result->numRows();
  }
  
  function remove_user_module($name)
  {
    $this->unassign_module($name);
    $query = " delete from tiki_user_modules where name='$name'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    return true;
  }
  
  function get_user_module($name)
  {
    $query = "select * from tiki_user_modules where name='$name'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }
  
  function list_user_modules()
  {
    $query = "select * from tiki_user_modules";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $query_cant = "select count(*) from tiki_user_modules";
    $cant = $this->db->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }

  function cache_links($links)
  {
    $cachepages = $this->get_preference("cachepages",'y');
    if($cachepages != 'y') return false;	
    foreach($links as $link) {
      if(!$this->is_cached($link)) {
        $this->cache_url($link);
      }
    }
  }
  
  function get_links($data)
  {
    $links = Array();
    if(preg_match_all("/\[([^\|\]]+)/",$data,$r1)) {
      $res = $r1[1];
      $links = array_unique($res);
    }
    return $links;
  }
  
  function get_links_nocache($data)
  {
    $links = Array();
    if(preg_match_all("/\[([^\]]+)/",$data,$r1)) {
      $res = Array();
      foreach($r1[1] as $alink) {
        $parts = explode('|',$alink);
        if(isset($parts[1])&& $parts[1] == 'nocache' ) {
          $res[] = $parts[0];
        } else {
          if(isset($parts[2]) && $parts[2] == 'nocache') {
            $res[] = $parts[0];
          }
        }
      }

      $links = array_unique($res);
    }
    
    return $links;
  }
  
  function is_cached($url)
  {
    if(strstr($url,"tiki-index")) {
      return true;
    }
    if(strstr($url,"tiki-edit")) {
      return true;
    }
    $query = "select cacheId from tiki_link_cache where url='$url'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $cant = $result->numRows();
    return $cant;
  }
  
  function list_cache($offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where url like '%".$find."%'";  
    } else {
      $mid=""; 
    }
    $query = "select cacheId,url,refresh from tiki_link_cache $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_link_cache";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $cant = $this->db->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }

  function cache_url($url)
  {
    $url=addslashes($url);
    // This function stores a cached representation of a page in the cache
    // Check if the URL is not already cached
    //if($this->is_cached($url)) return false;
    $fp = fopen($url,"r");
    if(!$fp) return false;
    $data = fread($fp,999999);
    fclose($fp);
    
    // Check for META tags with equiv
    /*
    print("Len: ".strlen($data)."<br/>");
    
    preg_match_all("/\<meta([^\>\<\n\t]+)/i",$data,$reqs);
    foreach($reqs[1] as $meta)
    {
      print("Un meta: $meta<br/>");
      if(stristr($meta,'refresh')) {
        print("Es refresh<br/>");
        preg_match("/url=([^ \"\'\n\t]+)/i",$meta,$urls);	
        if(strlen($urls[1])) {
          $urli=$urls[1];	
          print("URL: $urli<br/>");	
        }
      }	
    }
    print("pepe");
    */
    $data = addslashes($data);
    $refresh = date("U");
    $query = "insert into tiki_link_cache(url,data,refresh) values('$url','$data',$refresh)";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    return true;
  }
  
  function refresh_cache($cacheId)
  {
    $query = "select url from tiki_link_cache where cacheId=$cacheId";
    $url = $this->db->getOne($query);
    $fp = fopen($url,"r");
    if(!$fp) return false;
    $data = fread($fp,999999);
    fclose($fp);
    $data = addslashes($data);
    $refresh = date("U");
    $query = "update tiki_link_cache set data='$data', refresh=$refresh where cacheId=$cacheId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    return true;
  }
  
  function remove_cache($cacheId)
  {
    $query = "delete from tiki_link_cache where cacheId=$cacheId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    return true;
  }
  
  function get_cache($cacheId)
  {
    $query = "select * from tiki_link_cache where cacheId=$cacheId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }
  
  function get_cache_id($url)
  {
    if(!$this->is_cached($url)) return false;
    $query = "select cacheId from tiki_link_cache where url='$url'";
    $id = $this->db->getOne($query);
    return $id;
  }
  
  function add_image_hit($id) 
  {
    $query = "update tiki_images set hits=hits+1 where imageId=$id";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    return true;                        
  }
  
  function add_gallery_hit($id)
  {
    $query = "update tiki_galleries set hits=hits+1 where galleryId=$id";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    return true;                        
  }

  function ImageCopyResampleBicubic (&$dst_img, &$src_img, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h)
/*
port to PHP by John Jensen July 10 2001 (updated 4/21/02) -- original code (in C, for the PHP GD Module) by jernberg@fairytale.se
*/
{
$palsize = ImageColorsTotal ($src_img);
for ($i = 0; $i < $palsize; $i++) { // get palette.
$colors = ImageColorsForIndex ($src_img, $i);
ImageColorAllocate ($dst_img, $colors['red'], $colors['green'], $colors['blue']);
}

$scaleX = ($src_w - 1) / $dst_w;
$scaleY = ($src_h - 1) / $dst_h;

$scaleX2 = (int) ($scaleX / 2);
$scaleY2 = (int) ($scaleY / 2);

for ($j = $src_y; $j < $dst_h; $j++) {
$sY = (int) ($j * $scaleY);
$y13 = $sY + $scaleY2;

for ($i = $src_x; $i < $dst_w; $i++) {
$sX = (int) ($i * $scaleX);
$x34 = $sX + $scaleX2;

$color1 = ImageColorsForIndex ($src_img, ImageColorAt ($src_img, $sX, $y13));
$color2 = ImageColorsForIndex ($src_img, ImageColorAt ($src_img, $sX, $sY));
$color3 = ImageColorsForIndex ($src_img, ImageColorAt ($src_img, $x34, $y13));
$color4 = ImageColorsForIndex ($src_img, ImageColorAt ($src_img, $x34, $sY));

$red = ($color1['red'] + $color2['red'] + $color3['red'] + $color4['red']) / 4;
$green = ($color1['green'] + $color2['green'] + $color3['green'] + $color4['green']) / 4;
$blue = ($color1['blue'] + $color2['blue'] + $color3['blue'] + $color4['blue']) / 4;

ImageSetPixel ($dst_img, $i + $dst_x - $src_x, $j + $dst_y - $src_y, ImageColorClosest ($dst_img, $red, $green, $blue));
}
}
}
    
   function rebuild_thumbnails($galleryId)
  {
    if(!function_exists("ImageCreateFromString")) return false;
    $gal_info = $this->get_gallery($galleryId);
    $query = "select * from tiki_images where galleryId=$galleryId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      if(!strstr($res["name"],"gif")) {
      $img = imagecreatefromstring($res["data"]);
      $res["xsize"]=imagesx($img);
      $res["ysize"]=imagesy($img);
      // Rebuild the thumbnail for this image
      $t = imagecreate($gal_info["thumbSizeX"],$gal_info["thumbSizeY"]);
      print("From: ".$res["xsize"]."x".$res["ysize"]." to: ".$gal_info["thumbSizeX"]."x".$gal_info["thumbSizeY"]."<br/>");
      //imagecopyresized ( $t, $img, 0,0,0,0, $gal_info["thumbSizeX"],$gal_info["thumbSizeY"], $res["xsize"], $res["ysize"]);
       $this->ImageCopyResampleBicubic( $t, $img, 0,0,0,0, $gal_info["thumbSizeX"],$gal_info["thumbSizeY"], $res["xsize"], $res["ysize"]);
      $tmpfname = tempnam ("/tmp", "FOO").'.jpg';
      imagejpeg($t,$tmpfname);
      // Now read the information
      $fp = fopen($tmpfname,"r");
      $t_data = fread($fp, filesize($tmpfname));
      fclose($fp);
      unlink($tmpfname);
      $t_pinfo = pathinfo($tmpfname);
      $t_type = $t_pinfo["extension"];
      $t_type='image/'.$t_type;
      $imageId = $res["imageId"];
      $t_data = addslashes($t_data);
      $query2 = "update tiki_images set t_data='$t_data', t_type='$t_type' where imageId=$imageId";
      $result2 = $this->db->query($query2);
      if(DB::isError($result2)) $this->sql_error($query2,$result2);
      }
    }
    return true;
  }


  
  function insert_image($galleryId,$name,$description,$filename, $filetype, $data, $size, $xsize, $ysize, $user,$t_data,$t_type) 
  {
    $name = addslashes(strip_tags($name));
    $description = addslashes(strip_tags($description));
    $data = addslashes($data);
    $t_data = addslashes($t_data);
    $now = date("U");
    $query = "insert into tiki_images(galleryId,name,description,filename,filetype,filesize,data,xsize,ysize,user,created,t_data,t_type,hits)
                          values($galleryId,'$name','$description','$filename','$filetype',$size,'$data',$xsize,$ysize,'$user',$now,'$t_data','$t_type',0)";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $query = "update tiki_galleries set lastModif=$now where galleryId=$galleryId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $query = "select max(imageId) from tiki_images where created=$now";
    $imageId = $this->db->getOne($query);
    return $imageId;
  }
  
  function remove_image($id)
  {
    $query = "delete from tiki_images where imageId=$id";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    return true;                        
  }
  
  function get_images($offset,$maxRecords,$sort_mode,$find,$galleryId)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where galleryId=$galleryId and name like '%".$find."%' or description like '%".$find."%'";  
    } else {
      $mid="where galleryId=$galleryId"; 
    }
    $query = "select imageId,name,description,created,filename,filesize,xsize,ysize,user,hits from tiki_images $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_images where galleryId=$galleryId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $cant = $this->db->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  function list_images($offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" name like '%".$find."%' or description like '%".$find."%'";  
    } else {
      $mid=""; 
    }
    $query = "select imageId,name,description,created,filename,filesize,xsize,ysize,user,hits from tiki_images $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_images";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $cant = $this->db->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  
  function get_gallery_owner($galleryId)
  {
    $query = "select user from tiki_galleries where galleryId=$galleryId";
    $user = $this->db->getOne($query);
    return $user;
  }
  
  function get_gallery($id) 
  {
    $query = "select * from tiki_galleries where galleryId='$id'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }
  
  function move_image($imgId,$galId) 
  {
    $query = "update tiki_images set galleryId=$galId where imageId=$imgId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    return true;
  }
  
  // Add an option to stablish Image size (x,y)
  function get_image($id) 
  {
    $query = "select * from tiki_images where imageId='$id'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }
  
  function replace_gallery($name, $description, $theme, $user,$maxRows,$rowImages,$thumbSizeX,$thumbSizeY,$public) 
  {
    // if the user is admin or the user is the same user and the gallery exists then replace if not then
    // create the gallary if the name is unused.
    $name = addslashes(strip_tags($name));
    $description = addslashes(strip_tags($description));
    $query = "select name,user from tiki_galleries where name='$name'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $now = date("U");
    if($result->numRows()) {
      $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
      if( ($user == 'admin') || ($res["user"]==$user) ) {
      $query = "update tiki_galleries set maxRows=$maxRows, rowImages=$rowImages, thumbSizeX=$thumbSizeX, thumbSizeY=$thumbSizeY, description='$description', theme='$theme', lastModif=$now, public='$public' where name='$name'";
      $result = $this->db->query($query);
      if(DB::isError($result)) $this->sql_error($query,$result);
      } 
    } else {
      // Create a new record
      $query =  "insert into tiki_galleries(name,description,theme,created,user,lastModif,maxRows,rowImages,thumbSizeX,thumbSizeY,public,hits) 
                                    values ('$name','$description','$theme',$now,'$user',$now,$maxRows,$rowImages,$thumbSizeX,$thumbSizeY,'$public',0)";
      $result = $this->db->query($query);
      if(DB::isError($result)) $this->sql_error($query,$result);
    }
    return true;
  }
  
  function remove_gallery($id, $user)
  {
    $query = "select name,user from tiki_galleries where galleryId='$id'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $now = date("U");
    if($result->numRows()) {
      $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
      if( ($user == 'admin') || ($res["user"]==$user) ) {
        $query = "delete from tiki_galleries where galleryId='$id'";
        $result = $this->db->query($query);
        if(DB::isError($result)) $this->sql_error($query,$result);
        $query = "delete from tiki_images where galleryId='$id'";
        $result = $this->db->query($query);
        if(DB::isError($result)) $this->sql_error($query,$result);
      }  
    }
  }
  
  function get_gallery_info($id)
  {
    $query = "select * from tiki_galleries where galleryId='$id'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;  
  }
  

  function vote_page($page, $points) 
  {
    $query = "update pages set points=points+$points, votes=votes+1 where pageName='$page'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
  }

  function get_votes($page) 
  {
    $query = "select points,votes from pages where pageName='$page'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }

  function tag_exists($tag) 
  {
    $query = "select distinct tagName from tiki_tags where tagName = '$tag'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    return $result->numRows($result);
  }

  function remove_tag($tagname) 
  {
    $query = "delete from tiki_tags where tagName='$tagname'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $action = "removed tag: $tagname";
    $t = date("U");
    $query = "insert into tiki_actionlog(action,pageName,lastModif,user,ip,comment) values('$action','HomePage',$t,'admin','".$_SERVER["REMOTE_ADDR"]."','')";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    return true;    
  }

  function get_tags() 
  {
    $query = "select distinct tagName from tiki_tags";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) { 
      $ret[] = $res["tagName"];  
    }
    return $ret;
  }

  // This function can be used to store the set of actual pages in the "tags"
  // table preserving the state of the wiki under a tag name.
  function create_tag($tagname,$comment='') 
  {
    $tagname = addslashes($tagname);
    $comment = addslashes($comment);
    $query = "select * from tiki_pages";
    $result=$this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) { 
      $query = "replace into tiki_tags(tagName,pageName,hits,data,lastModif,comment,version,user,ip,flag)
                values('$tagname','".$res["pageName"]."',".$res["hits"].",'".addslashes($res["data"])."',".$res["lastModif"].",'".$res["comment"]."',".$res["version"].",'".$res["user"]."','".$res["ip"]."','".$res["flag"]."')";
      $result2=$this->db->query($query);
      if(DB::isError($result2)) $this->sql_error($query,$result2);
    }
    $action = "created tag: $tagname";
    $t = date("U");
    $query = "insert into tiki_actionlog(action,pageName,lastModif,user,ip,comment) values('$action','HomePage',$t,'admin','".$_SERVER["REMOTE_ADDR"]."','$comment')";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    return true;
  }

  // This funcion recovers the state of the wiki using a tagName from the
  // tags table
  function restore_tag($tagname) 
  {
    $query = "select * from tiki_tags where tagName='$tagname'";
    $result=$this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) { 
      $query = "replace into tiki_pages(pageName,hits,data,lastModif,comment,version,user,ip,flag)
                values('".$res["pageName"]."',".$res["hits"].",'".addslashes($res["data"])."',".$res["lastModif"].",'".$res["comment"]."',".$res["version"].",'".$res["user"]."','".$res["ip"]."','".$res["flag"]."')";
      $result2=$this->db->query($query);
      if(DB::isError($result2)) $this->sql_error($query,$result2);
    }
    $action = "recovered tag: $tagname";
    $t = date("U");
    $query = "insert into tiki_actionlog(action,pageName,lastModif,user,ip,comment) values('$action','HomePage',$t,'admin','".$_SERVER["REMOTE_ADDR"]."','')";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    return true;
  }

  // This funcion return the $limit most accessed pages
  // it returns pageName and hits for each page
  function get_top_pages($limit) 
  {
    $query = "select pageName, hits from tiki_pages order by hits desc limit 0,$limit";
    $result=$this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $aux["pageName"] = $res["pageName"];
      $aux["hits"] = $res["hits"];
      $ret[] = $aux;  
    }  
    return $ret;
  }
  
  function wiki_ranking_top_pages($limit) 
  {
    $query = "select pageName, hits from tiki_pages order by hits desc limit 0,$limit";
    $result=$this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $aux["name"] = $res["pageName"];
      $aux["hits"] = $res["hits"];
      $aux["href"] = 'tiki-index.php?page='.$res["pageName"];
      $ret[] = $aux;  
    }  
    $retval["data"]=$ret;
    $retval["title"]=tra("Wiki top pages");
    $retval["y"]=tra("Hits");
    return $retval;
  }
  
  function wiki_ranking_last_pages($limit)
  {
    $query = "select pageName,lastModif,hits from tiki_pages order by lastModif desc limit 0,$limit";
    $result=$this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $aux["name"] = $res["pageName"];
      $aux["hits"] = date("F d Y (h:i)",$res["lastModif"]);
      $aux["href"] = 'tiki-index.php?page='.$res["pageName"];
      $ret[] = $aux;  
    }  
    $ret["data"]=$ret;
    $ret["title"]=tra("Wiki last pages");
    $ret["y"]=tra("Modified");
    return $ret;
    
  }
  
  function gal_ranking_top_galleries($limit) 
  {
    $query = "select * from tiki_galleries order by hits desc limit 0,$limit";
    $result=$this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $aux["name"] = $res["name"];
      $aux["hits"] = $res["hits"];
      $aux["href"] = 'tiki-browse_gallery.php?galleryId='.$res["galleryId"];
      $ret[] = $aux;  
    }  
    $retval["data"]=$ret;
    $retval["title"]=tra("Wiki top galleries");
    $retval["y"]=tra("Hits");
    return $retval;
  }
  
  function gal_ranking_top_images($limit) 
  {
    $query = "select * from tiki_images order by hits desc limit 0,$limit";
    $result=$this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $aux["name"] = $res["name"];
      $aux["hits"] = $res["hits"];
      $aux["href"] = 'tiki-browse_image.php?imageId='.$res["imageId"];
      $ret[] = $aux;  
    }  
    $retval["data"]=$ret;
    $retval["title"]=tra("Wiki top images");
    $retval["y"]=tra("Hits");
    return $retval;
  }
  
  function gal_ranking_last_images($limit) 
  {
    $query = "select * from tiki_images order by created desc limit 0,$limit";
    $result=$this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $aux["name"] = $res["name"];
      $aux["hits"] = date("F d Y (h:i)",$res["created"]);
      $aux["href"] = 'tiki-browse_image.php?imageId='.$res["imageId"];
      $ret[] = $aux;  
    }  
    $retval["data"]=$ret;
    $retval["title"]=tra("Wiki last images");
    $retval["y"]=tra("Uploaded");
    return $retval;
  }
  
  function cms_ranking_top_articles($limit)
  {
    $query = "select * from tiki_articles order by reads desc limit 0,$limit";
    $result=$this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $aux["name"] = $res["title"];
      $aux["hits"] = $res["reads"];
      $aux["href"] = 'tiki-read_article.php?articleId='.$res["articleId"];
      $ret[] = $aux;  
    }  
    $retval["data"]=$ret;
    $retval["title"]=tra("Wiki top articles");
    $retval["y"]=tra("Reads");
    return $retval;
  }
  
  function cms_ranking_top_articles($limit)
  {
    $query = "select * from tiki_articles order by reads desc limit 0,$limit";
    $result=$this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $aux["name"] = $res["title"];
      $aux["hits"] = $res["reads"];
      $aux["href"] = 'tiki-read_article.php?articleId='.$res["articleId"];
      $ret[] = $aux;  
    }  
    $retval["data"]=$ret;
    $retval["title"]=tra("Wiki top articles");
    $retval["y"]=tra("Reads");
    return $retval;
  }
  
  function blog_ranking_top_blogs($limit) 
  {
    $query = "select * from tiki_blogs order by hits desc limit 0,$limit";
    $result=$this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $aux["name"] = $res["title"];
      $aux["hits"] = $res["hits"];
      $aux["href"] = 'tiki-view_blog.php?blogId='.$res["blogId"];
      $ret[] = $aux;  
    }  
    $retval["data"]=$ret;
    $retval["title"]=tra("Most visited blogs");
    $retval["y"]=tra("Hits");
    return $retval;
  }
  
  function blog_ranking_top_active_blogs($limit) 
  {
    $query = "select * from tiki_blogs order by activity desc limit 0,$limit";
    $result=$this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $aux["name"] = $res["title"];
      $aux["hits"] = $res["activity"];
      $aux["href"] = 'tiki-view_blog.php?blogId='.$res["blogId"];
      $ret[] = $aux;  
    }  
    $retval["data"]=$ret;
    $retval["title"]=tra("Most active blogs");
    $retval["y"]=tra("Activity");
    return $retval;
  }
  
  function blog_ranking_last_posts($limit) 
  {
    $query = "select * from tiki_blog_posts order by created desc limit 0,$limit";
    $result=$this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $q = "select title from tiki_blogs where blogId=".$res["blogId"];
      $name = $this->db->getOne($q);
      $aux["name"] = $name;
      $aux["hits"] = date("F d Y (h:i)",$res["created"]);
      $aux["href"] = 'tiki-view_blog.php?blogId='.$res["blogId"];
      $ret[] = $aux;  
    }  
    $retval["data"]=$ret;
    $retval["title"]=tra("Blogs last posts");
    $retval["y"]=tra("Date");
    return $retval;
  }
  
  function wiki_ranking_top_authors($limit)
  {
    $query = "select distinct user from tiki_pages";
    $result=$this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res["user"];  
    }
    $retu = Array();
    foreach($ret as $author) {
      $query = "select count(*) from tiki_pages where user='$author'";
      $cant = $this->db->getOne($query);
      $aux["name"] = $author;
      $aux["hits"] = $cant;
      $aux["href"] = '';
      $retu[] = $aux;
    }
    $retval["data"]=$retu;
    $retval["title"]=tra("Wiki top authors");
    $retval["y"]=tra("Pages");
    return $retval;
  }
  
  function cms_ranking_top_authors($limit)
  {
    $query = "select distinct author from tiki_articles";
    $result=$this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res["author"];  
    }
    $retu = Array();
    foreach($ret as $author) {
      $query = "select count(*) from tiki_articles where author='$author'";
      $cant = $this->db->getOne($query);
      $aux["name"] = $author;
      $aux["hits"] = $cant;
      $aux["href"] = '';
      $retu[] = $aux;
    }
    $retval["data"]=$retu;
    $retval["title"]=tra("Top article authors");
    $retval["y"]=tra("Articles");
    return $retval;
  }
  
  

  // Sets the admin password to $pass

  // Dumps the database to dump/new.tar
  function dump() 
  {
    unlink("dump/new.tar");
    $tar = new tar();
    $tar->addFile("styles/main.css");
    // Foreach page
    $query = "select * from tiki_pages";
    $result = $this->db->query($query);
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $pageName = $res["pageName"].'.html';
      $dat = $this->parse_data($res["data"]);
      // Now change index.php?page=foo to foo.html
      // and index.php to HomePage.html
      $dat = preg_replace("/tiki-index.php\?page=([^\'\" ]+)/","$1.html",$dat);
      $dat = preg_replace("/tiki-editpage.php\?page=([^\'\" ]+)/","",$dat);
      //preg_match_all("/tiki-index.php\?page=([^ ]+)/",$dat,$cosas);
      //print_r($cosas);
      $data = "<html><head><title>".$res["pageName"]."</title><link rel='StyleSheet' href='styles/main.css' type='text/css'></head><body><a class='wiki' href='HomePage.html'>home</a><br/><h1>".$res["pageName"]."</h1><div class='wikitext'>".$dat.'</div></body></html>';
      $tar->addData($pageName,$data,$res["lastModif"]);
    }
    $tar->toTar("dump/new.tar",FALSE);
    unset($tar);
    $action = "dump created";
    $t = date("U");
    $query = "insert into tiki_actionlog(action,pageName,lastModif,user,ip,comment) values('$action','HomePage',$t,'admin','".$_SERVER["REMOTE_ADDR"]."','')";
    $result=$this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
  }

  // Removes a specific version of a page
  function remove_version($page,$version,$comment='') 
  {
    $query="delete from tiki_history where pageName='$page' and version='$version'";
    $result=$this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $action="Removed version $version";
    $t = date("U");
    $query = "insert into tiki_actionlog(action,pageName,lastModif,user,ip,comment) values('$action','$page',$t,'admin','".$_SERVER["REMOTE_ADDR"]."','$comment')";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    return true;
  }

  // Removes all the versions of a page and the page itself
  function remove_all_versions($page,$comment='') 
  {
    $query = "delete from tiki_pages where pageName = '$page'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $query = "delete from tiki_history where pageName = '$page'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $query = "delete from tiki_links where fromPage = '$page'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $action="Removed";
    $t = date("U");
    $query = "insert into tiki_actionlog(action,pageName,lastModif,user,ip,comment) values('$action','$page',$t,'admin','".$_SERVER["REMOTE_ADDR"]."','$comment')";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    return true;
  }

  function use_version($page,$version,$comment='') 
  {
    $query = "select * from tiki_history where pageName='$page' and version='$version'";
    $result=$this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    if(!$result->numRows()) return false;
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    $query = "update tiki_pages set data='".addslashes($res["data"])."',lastModif=".$res["lastModif"].",user='".$res["user"]."',comment='".$res["comment"]."',version=version+1,ip='".$res["ip"]."' where pageName='$page'";
    $result=$this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $query = "delete from tiki_links where fromPage = '$page'";
    $result=$this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $pages = $this->get_pages($res["data"]);
    foreach($pages as $a_page) {
      $this->replace_link($page,$a_page);
    }
    //$query="delete from tiki_history where pageName='$page' and version='$version'";
    //$result=$this->db->query($query);
    //if(DB::isError($result)) $this->sql_error($query,$result);
    $action="Changed actual version to $version";
    $t = date("U");
    $query = "insert into tiki_actionlog(action,pageName,lastModif,user,ip,comment) values('$action','$page',$t,'admin','".$_SERVER["REMOTE_ADDR"]."','$comment')";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    return true;
  }

  // Removes last version of the page (from pages) if theres some
  // version in the tiki_history then the last version becomes the actual version
  function remove_last_version($page,$comment='') 
  {
    $query = "select * from tiki_history where pageName='$page' order by lastModif desc";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    if($result->numRows()) {
      // We have a version 
      $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
      $this->use_version($res["pageName"],$res["version"]);
      $this->remove_version($res["pageName"],$res["version"]);
    } else {
      $this->remove_all_versions($page); 
    }
    $action="Removed last version";
    $t = date("U");
    $query = "insert into tiki_actionlog(action,pageName,lastModif,user,ip,comment) values('$action','$page',$t,'admin','".$_SERVER["REMOTE_ADDR"]."','$comment')";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
  }

  function get_user_versions($user) 
  {
    $query = "select pageName,version, lastModif, user, ip, comment from tiki_history where user='$user' order by lastModif desc";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
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

  function remove_user($user) 
  {
    $query = "delete from tiki_users where user = '$user'";
    $result =  $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $action = "user $user removed";
    $t = date("U");
    $query = "insert into tiki_actionlog(action,pageName,lastModif,user,ip,comment) values('$action','HomePage',$t,'admin','".$_SERVER["REMOTE_ADDR"]."','')";
    $result=$this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    return true;
  }

  function user_exists($user) 
  {
    $query = "select user from tiki_users where user='$user'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    if($result->numRows()) return true;
    return false;
  }

  function add_user($user, $pass, $email) 
  {
    $user = addslashes($user);
    $pass = addslashes($pass);
    $email = addslashes($email);
    if(user_exists($user)) return false;  
    $query = "insert into tiki_users(user,password,email) values('$user','$pass','$email')";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $action = "user $user added";
    $t = date("U");
    $query = "insert into tiki_actionlog(action,pageName,lastModif,user,ip,comment) values('$action','HomePage',$t,'admin','".$_SERVER["REMOTE_ADDR"]."','')";
    $result=$this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    return true;
  }

  function get_user_info($user) 
  {
    $query = "select user, email, lastLogin from tiki_users where user='$user'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    $aux = Array();
    $aux["user"] = $res["user"];
    $user = $aux["user"];
    $aux["email"] = $res["email"];
    $aux["lastLogin"] = $res["lastLogin"];
    // Obtain lastChanged
    $query2 = "select count(*) from tiki_pages where user='$user'";
    $result2 = $this->db->query($query2);
    if(DB::isError($result2)) $this->sql_error($query2,$result2);
    $res2 = $result2->fetchRow();
    $aux["versions"] = $res2[0];
    // Obtain versions
    $query3 = "select count(*) from tiki_history where user='$user'";
    $result3 = $this->db->query($query3);
    if(DB::isError($result3)) $this->sql_error($query2,$result3);
    $res3 = $result3->fetchRow();
    $aux["lastChanged"] = $res3[0];
    $ret[] = $aux;
    return $aux;
  }

  function list_galleries($offset = 0, $maxRecords = -1, $sort_mode = 'name_desc', $user, $find) 
  {
    // If $user is admin then get ALL galleries, if not only user galleries are shown
    $sort_mode = str_replace("_"," ",$sort_mode);
    $old_sort_mode ='';
    if(in_array($sort_mode,Array('images desc','images asc'))) {
      $old_offset = $offset;
      $old_maxRecords = $maxRecords;
      $old_sort_mode = $sort_mode;
      $sort_mode ='user desc';
      $offset = 0;
      $maxRecords = -1;
    }
    // If the user is not admin then select it's own galleries or public galleries
    if($user != 'admin') {
      $whuser = "where user='$user' or public='y'";
    } else {
      $whuser = "";
    }
    if($find) {
      if(empty($whuser)) {
        $whuser = "where name like '%".$find."%' or description like '%".$find.".%'";
      } else {
        $whuser .= " and name like '%".$find."%' or description like '%".$find.".%'";
      }
    }
    // If sort mode is versions then offset is 0, maxRecords is -1 (again) and sort_mode is nil
    // If sort mode is links then offset is 0, maxRecords is -1 (again) and sort_mode is nil
    // If sort mode is backlinks then offset is 0, maxRecords is -1 (again) and sort_mode is nil
    $query = "select * from tiki_galleries $whuser order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_galleries $whuser";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $result_cant = $this->db->query($query_cant);
    if(DB::isError($result_cant)) $this->sql_error($query_cant,$result_cant);
    $res2 = $result_cant->fetchRow();
    $cant = $res2[0];
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $aux = Array();
      $aux["name"] = $res["name"];
      $gid = $res["galleryId"];
      $aux["id"] = $gid;
      $aux["galleryId"] = $res["galleryId"];
      $aux["description"] = $res["description"];
      $aux["created"] = $res["created"];
      $aux["lastModif"] = $res["lastModif"];
      $aux["user"] = $res["user"];
      $aux["hits"] = $res["hits"];
      $aux["public"] = $res["public"];
      $aux["theme"] = $res["theme"];
      $aux["images"] = $this->db->getOne("select count(*) from tiki_images where galleryId='$gid'");
      $ret[] = $aux;
    }
    if($old_sort_mode == 'images asc') {
      usort($ret,'compare_images');  
    }
    if($old_sort_mode == 'images desc') {
      usort($ret,'r_compare_images');
    }
    
    if(in_array($old_sort_mode,Array('images desc','images asc'))) {
      $ret = array_slice($ret, $old_offset, $old_maxRecords);    
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }

  

  function list_pages($offset = 0, $maxRecords = -1, $sort_mode = 'pageName_desc') 
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($sort_mode == 'size desc') {
      $sort_mode = ' length(data) desc';  
    }  
    if($sort_mode == 'size asc') {
      $sort_mode = ' length(data) asc';
    }
    $old_sort_mode ='';
    if(in_array($sort_mode,Array('versions desc','versions asc','links asc','links desc','backlinks asc','backlinks desc'))) {
      $old_offset = $offset;
      $old_maxRecords = $maxRecords;
      $old_sort_mode = $sort_mode;
      $sort_mode ='user desc';
      $offset = 0;
      $maxRecords = -1;
    }
    // If sort mode is versions then offset is 0, maxRecords is -1 (again) and sort_mode is nil
    // If sort mode is links then offset is 0, maxRecords is -1 (again) and sort_mode is nil
    // If sort mode is backlinks then offset is 0, maxRecords is -1 (again) and sort_mode is nil
    $query = "select pageName, hits, length(data) as len ,lastModif, user, ip, comment, version, flag from tiki_pages order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_pages";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $result_cant = $this->db->query($query_cant);
    if(DB::isError($result_cant)) $this->sql_error($query_cant,$result_cant);
    $res2 = $result_cant->fetchRow();
    $cant = $res2[0];
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $aux = Array();
      $aux["pageName"] = $res["pageName"];
      $page = $aux["pageName"];
      $aux["hits"] = $res["hits"];
      $aux["lastModif"] = $res["lastModif"];
      $aux["user"] = $res["user"];
      $aux["ip"] = $res["ip"];
      $aux["len"] = $res["len"];
      $aux["comment"] = $res["comment"];
      $aux["version"] = $res["version"];
      $aux["flag"] = $res["flag"] == 'y' ? 'locked' : 'unlocked';
      $aux["versions"] = $this->db->getOne("select count(*) from tiki_history where pageName='$page'");
      $aux["links"] = $this->db->getOne("select count(*) from tiki_links where fromPage='$page'");
      $aux["backlinks"] = $this->db->getOne("select count(*) from tiki_links where toPage='$page'");
      $ret[] = $aux;
    }
    // If sortmode is versions, links or backlinks sort using the ad-hoc function and reduce using old_offse and old_maxRecords
    if($old_sort_mode == 'versions asc') {
      usort($ret,'compare_versions');  
    }
    if($old_sort_mode == 'versions desc') {
      usort($ret,'r_compare_versions');
    }
    if($old_sort_mode == 'links desc') {
      usort($ret,'compare_links');
    }
    if($old_sort_mode == 'links asc') {
      usort($ret,'r_compare_links');
    }
    if($old_sort_mode == 'backlinks desc') {
      usort($ret,'compare_backlinks');
    }
    if($old_sort_mode == 'backlinks asc') {
      usort($ret,'r_compare_backlinks');
    }
    if(in_array($old_sort_mode,Array('versions desc','versions asc','links asc','links desc','backlinks asc','backlinks desc'))) {
      $ret = array_slice($ret, $old_offset, $old_maxRecords);    
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }

  


  function get_users($offset = 0,$maxRecords = -1,$sort_mode = 'user_desc')
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    $old_sort_mode ='';
    if(in_array($sort_mode,Array('versions desc','versions asc','changed asc','changed desc'))) {
      $old_offset = $offset;
      $old_maxRecords = $maxRecords;
      $old_sort_mode = $sort_mode;
      $sort_mode ='user desc';
      $offset = 0;
      $maxRecords = -1;
    }
    // Return an array of users indicating name, email, last changed pages, versions, lastLogin 
    $query = "select user, email, lastLogin from tiki_users order by $sort_mode limit $offset,$maxRecords";
    $cant = $this->db->getOne("select count(*) from tiki_users");
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $aux = Array();
      $aux["user"] = $res["user"];
      $user = $aux["user"];
      $aux["email"] = $res["email"];
      $aux["lastLogin"] = $res["lastLogin"];
      // Obtain lastChanged
      $aux["versions"] = $this->db->getOne("select count(*) from tiki_pages where user='$user'");
      // Obtain versions
      $aux["lastChanged"] = $this->db->getOne("select count(*) from tiki_history where user='$user'");
      $ret[] = $aux;
    }
    if($old_sort_mode == 'changed asc') {
      usort($ret,'compare_changed');  
    }
    if($old_sort_mode == 'changed desc') {
      usort($ret,'r_compare_changed'); 
    }
    if($old_sort_mode == 'versions asc') {
      usort($ret,'compare_versions');  
    }
    if($old_sort_mode == 'versions desc') {
      usort($ret,'r_compare_versions'); 
    }
    if(in_array($old_sort_mode,Array('versions desc','versions asc','changed asc','changed desc'))) {
      $ret = array_slice($ret, $old_offset, $old_maxRecords);    
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval; 
  }

  function get_all_preferences()
  {
    $query = "select name,value from tiki_preferences";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $ret=Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[$res["name"]] = $res["value"];
    }
    return $ret;
  }

  function get_preference($name, $default='') 
  {
    $query = "select value from tiki_preferences where name='$name'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    if($result->numRows()) {
      $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
      return $res["value"];
    } else {
      return $default; 
    } 
  }

  function set_preference($name, $value) 
  {
    $name = addslashes($name);
    $value = addslashes($value);
    $query = "replace into tiki_preferences(name,value) values('$name','$value')";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    return true;  
  }

  function get_user_preference($user, $name, $default='') 
  {
    $query = "select value from tiki_user_preferences where prefName='$name' and user='$user'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    if($result->numRows()) {
      $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
      return $res["value"];
    } else {
      return $default; 
    } 
  }

  function set_user_preference($user, $name, $value) 
  {
    $name = addslashes($name);
    $value = addslashes($value);
    $query = "replace into tiki_user_preferences(user,prefName,value) values('$user','$name','$value')";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    return true;  
  }

  function validate_user($user,$pass) 
  {
    $query = "select user from tiki_users where user='$user' and password='$pass'"; 
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    if($result->numRows()) {
      $t = date("U");
      $query = "update tiki_users set lastLogin='$t' where user='$user'";
      $result = $this->db->query($query);
      if(DB::isError($result)) $this->sql_error($query,$result);
      return true; 
    }
    return false;
  }

  // Like pages are pages that share a word in common with the current page
  function get_like_pages($page) 
  {
    preg_match_all("/([A-Z])([a-z]+)/",$page,$words);
    $words=$words[0];
    $exps = Array();
    foreach($words as $word) {
      $exps[] = "pageName like '%$word%'";
    }
    $exp = implode(" or ",$exps);
    $query = "select pageName from tiki_pages where $exp";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res["pageName"];
    }
    return $ret;
  }

  // Returns information about a specific version of a page
  function get_version($page, $version) 
  {
    $query = "select * from tiki_history where pageName='$page' and version=$version";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }

  // Returns all the versions for this page
  // without the data itself
  function get_page_history($page) 
  {
    $query = "select version, lastModif, user, ip, comment from tiki_history where pageName='$page' order by version desc";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $aux = Array();
      $aux["version"] = $res["version"];
      $aux["lastModif"] = $res["lastModif"];
      $aux["user"] = $res["user"];
      $aux["ip"] = $res["ip"];
      $aux["comment"] = $res["comment"];
      $ret[]=$aux; 
    }
    return $ret;
  }

  function is_locked($page) 
  {
    $query = "select flag from tiki_pages where pageName='$page'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    if($res["flag"]=='L') return true;
    return false;  
  }

  function lock_page($page) 
  {
    $query = "update tiki_pages set flag='L' where pageName='$page'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    return true;
  }

  function unlock_page($page) 
  {
    $query = "update tiki_pages set flag='' where pageName='$page'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    return true;
  }

  // Returns backlinks for a given page
  function get_backlinks($page) 
  {
    $query = "select fromPage from tiki_links where toPage = '$page'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $aux["fromPage"] = $res["fromPage"];
      $ret[] = $aux;
    }
    return $ret; 
  }

  function find_pages($words='',$offset=0,$maxRecords=-1) 
  {
    
    if(!$words) {
      $query="select * from tiki_pages order by hits desc limit $offset,$maxRecords";
    } else {
      $vwords = split(' ',$words);
      $parts = Array();
      foreach ($vwords as $aword) {
        $parts[] = " (locate('$aword',pageName) or locate('$aword',data) )"; 
      }
      $part = implode(" and ",$parts);
      $query='select * from tiki_pages where '.$part.' order by hits desc '."limit $offset,$maxRecords"; 
    }
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $queryCant = 'select count(*) from tiki_pages where '.$part.' order by hits desc '; 
    $cant = $this->db->getOne($queryCant);
    
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $aux = Array();
      $aux["pageName"] = $res["pageName"];
      // Build an excerpt
      $aux["data"] = substr($res["data"],0,240);
      $aux["hits"] = $res["hits"];
      $aux["lastModif"] = $res["lastModif"];
      $aux["href"]='tiki-index.php?page='.$res["pageName"];
      $ret[] = $aux;
    }
    $retval = Array();
    $retval["data"]=$ret;
    $retval["cant"]=$cant;
    return $retval;
  }

  function find_galleries($words='',$offset=0,$maxRecords=-1) 
  {
    if(!$words) {
      $query="select * from tiki_galleries order by hits desc limit $offset,$maxRecords";
    } else {
      $vwords = split(' ',$words);
      $parts = Array();
      foreach ($vwords as $aword) {
        $parts[] = " (locate('$aword',name) or locate('$aword',description) )"; 
      }
      $part = implode(" and ",$parts);
      $query='select * from tiki_galleries where '.$part.' order by hits desc '."limit $offset,$maxRecords"; 
    }
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $queryCant = 'select count(*) from tiki_galleries where '.$part.' order by hits desc '; 
    $cant = $this->db->getOne($queryCant);
    
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $aux = Array();
      $aux["pageName"] = $res["name"];
      // Build an excerpt
      $aux["data"] = substr($res["description"],0,240);
      $aux["hits"] = $res["hits"];
      $aux["lastModif"] = $res["lastModif"];
      $aux["href"]='tiki-browse_gallery.php?galleryId='.$res["galleryId"];
      $ret[] = $aux;
    }
    $retval = Array();
    $retval["data"]=$ret;
    $retval["cant"]=$cant;
    return $retval;
  }

  function find_images($words='',$offset=0,$maxRecords=-1) 
  {
    if(!$words) {
      $query="select * from tiki_images order by hits desc limit $offset,$maxRecords";
    } else {
      $vwords = split(' ',$words);
      $parts = Array();
      foreach ($vwords as $aword) {
        $parts[] = " (locate('$aword',name) or locate('$aword',description) )"; 
      }
      $part = implode(" and ",$parts);
      $query='select * from tiki_images where '.$part.' order by hits desc '."limit $offset,$maxRecords"; 
    }
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $queryCant = 'select count(*) from tiki_images where '.$part.' order by hits desc '; 
    $cant = $this->db->getOne($queryCant);
    
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $aux = Array();
      $aux["pageName"] = $res["name"];
      // Build an excerpt
      $aux["data"] = substr($res["description"],0,240);
      $aux["hits"] = $res["hits"];
      $aux["lastModif"] = $res["created"];
      $aux["href"]='tiki-browse_image.php?imageId='.$res["imageId"];
      $ret[] = $aux;
    }
    $retval = Array();
    $retval["data"]=$ret;
    $retval["cant"]=$cant;
    return $retval;
  }

  function find_blogs($words='',$offset=0,$maxRecords=-1) 
  {
    if(!$words) {
      $query="select * from tiki_blogs order by hits desc limit $offset,$maxRecords";
    } else {
      $vwords = split(' ',$words);
      $parts = Array();
      foreach ($vwords as $aword) {
        $parts[] = " (locate('$aword',title) or locate('$aword',description) )"; 
      }
      $part = implode(" and ",$parts);
      $query='select * from tiki_blogs where '.$part.' order by hits desc '."limit $offset,$maxRecords"; 
    }
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $queryCant = 'select count(*) from tiki_blogs where '.$part.' order by hits desc '; 
    $cant = $this->db->getOne($queryCant);
    
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $aux = Array();
      $aux["pageName"] = $res["title"];
      // Build an excerpt
      $aux["data"] = substr($res["description"],0,240);
      $aux["hits"] = $res["hits"];
      $aux["lastModif"] = $res["lastModif"];
      $aux["href"]='tiki-view_blog.php?blogId='.$res["blogId"];
      $ret[] = $aux;
    }
    $retval = Array();
    $retval["data"]=$ret;
    $retval["cant"]=$cant;
    return $retval;
  }

  function find_articles($words='',$offset=0,$maxRecords=-1) 
  {
    if(!$words) {
      $query="select * from tiki_articles order by reads desc limit $offset,$maxRecords";
    } else {
      $vwords = split(' ',$words);
      $parts = Array();
      foreach ($vwords as $aword) {
        $parts[] = " (locate('$aword',title) or locate('$aword',heading) or locate('$aword',body) )"; 
      }
      $part = implode(" and ",$parts);
      $query='select * from tiki_articles where '.$part.' order by reads desc '."limit $offset,$maxRecords"; 
    }
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $queryCant = 'select count(*) from tiki_articles where '.$part.' order by reads desc '; 
    $cant = $this->db->getOne($queryCant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $aux = Array();
      $aux["pageName"] = $res["title"];
      // Build an excerpt
      $aux["data"] = substr($res["heading"],0,240);
      $aux["hits"] = $res["reads"];
      $aux["lastModif"] = $res["publishDate"];
      $aux["href"]='tiki-read_article.php?articleId='.$res["articleId"];
      $ret[] = $aux;
    }
    $retval = Array();
    $retval["data"]=$ret;
    $retval["cant"]=$cant;
    return $retval;
  }

  function find_posts($words='',$offset=0,$maxRecords=-1) 
  {
    if(!$words) {
      $query="select * from tiki_blog_posts order by hits desc limit $offset,$maxRecords";
    } else {
      $vwords = split(' ',$words);
      $parts = Array();
      foreach ($vwords as $aword) {
        $parts[] = " (locate('$aword',data))"; 
      }
      $part = implode(" and ",$parts);
      $query='select * from tiki_blog_posts where '.$part.' order by created desc '."limit $offset,$maxRecords"; 
    }
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $queryCant = 'select count(*) from tiki_blog_posts where '.$part.' order by created desc '; 
    $cant = $this->db->getOne($queryCant);
    
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $aux = Array();
      $qb = "select title from tiki_blogs where blogId=".$res["blogId"];
      $blogName = $this->db->getOne($qb);
      $aux["pageName"] = $blogName.' ['.date("F d Y (h:i)",$res["created"]).']'.' by:'.$res["user"];
      // Build an excerpt
      $aux["data"] = substr($res["data"],0,240);
      $aux["hits"] = 1;
      $aux["lastModif"] = $res["created"];
      $day=date("d",$res["created"]);
      $mon=date("m",$res["created"]);
      $year=date("Y",$res["created"]);
      $aux["href"]='tiki-view_blog.php?blogId='.$res["blogId"].'&amp;find='.$words;
      //.'&amp;day='.$day.'&amp;mon='.$mon.'&amp;year='.$year;
      $ret[] = $aux;
    }
    $retval = Array();
    $retval["data"]=$ret;
    $retval["cant"]=$cant;
    return $retval;
  }

  // tikilib.php a Library to access the Tiki's Data Model
  // This implements all the functions needed to use Tiki
  function page_exists($pageName) 
  {
    $query = "select pageName from tiki_pages where pageName = '$pageName'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    return $result->numRows();
  }

  function version_exists($pageName, $version) 
  {
    $query = "select pageName from tiki_history where pageName = '$pageName' and version='$version'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    return $result->numRows();
  }

  function add_hit($pageName) {
    $query = "update tiki_pages set hits=hits+1 where pageName = '$pageName'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    return true; 
  }

  function create_page($name, $hits, $data, $lastModif, $comment, $user='system', $ip='0.0.0.0') 
  {
    $name = addslashes($name);
    $data = addslashes($data);
    $comment = addslashes($comment);
    if($this->page_exists($name)) return false;
    $query = "insert into tiki_pages(pageName,hits,data,lastModif,comment,version,user,ip) values('$name',$hits,'$data',$lastModif,'$comment',1,'$user','$ip')";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $pages = $this->get_pages($data);
    foreach($pages as $a_page) {
      $this->replace_link($name,$a_page);
    }
    // Update the log
    if($name != 'SandBox') {
      $action = "Created";
      $query = "insert into tiki_actionlog(action,pageName,lastModif,user,ip,comment) values('$action','$name',$lastModif,'$user','$ip','$comment')";
      $result = $this->db->query($query);
      if(DB::isError($result)) $this->sql_error($query,$result);
    }
    return true;
  }

  function get_user_pages($user,$max) 
  {
    $query = "select pageName from tiki_pages where user='$user' limit 0,$max";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $ret=Array();
    while( $res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[]=$res;
    }
    return $ret;
  }
  
  function get_user_galleries($user,$max) 
  {
    $query = "select name,galleryId from tiki_galleries where user='$user' limit 0,$max";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $ret=Array();
    while( $res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[]=$res;
    }
    return $ret;
  }

  function get_page_info($pageName)
  {
    $query = "select * from tiki_pages where pageName='$pageName'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    if(!$result->numRows()) return false;
    $ret = $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    $ret["pageName"] = $pageName;
    return $ret;  
  }

  function parse_data($data) 
  {
    
    global $feature_hotwords;
    global $cachepages;
    $data = stripslashes($data);
    if($feature_hotwords == 'y') {
      $words = $this->get_hotwords();
      foreach($words as $word=>$url) {
      	//print("Replace $word by $url<br/>");
        $data  = preg_replace("/ $word /i"," <a class=\"wiki\" href=\"$url\" target='_blank'>$word</a> ",$data);	
      }
    }	
    
    //$data = strip_tags($data);
    // tables
    preg_match_all("/(\%[^\%]+\%)/",$data,$pages);
    foreach(array_unique($pages[1]) as $page) {
      $pagex=substr($page,1,strlen($page)-2);
      $repl='<table cellpadding="0" cellspacing="0" border="1">';
      // First split by lines
      $lines = explode("\\",$pagex);
      foreach ($lines as $line) {
        $repl.='<tr>';
        $columns = explode("&",$line);
        foreach($columns as $column) {
          $repl.='<td valign="top">'.$column.'</td>';  
        }  
        $repl.='</tr>';  
      }
      $repl.='</table>'; 
      $data = str_replace($page, $repl, $data);
    }
    // Links to internal pages
    preg_match_all("/[ \n\t\r]([A-Z][a-z0-9_\-]+[A-Z][a-z0-9_\-]+[A-Za-z0-9\-_]*)[ \n\t\r]/",$data,$pages);
    foreach(array_unique($pages[1]) as $page) {
      if($this->page_exists($page)) {
        $repl = "<a href='tiki-index.php?page=$page' class='wiki'>$page</a>";
      } else {
        $repl = "$page<a href='tiki-editpage.php?page=$page' class='wiki'>?</a>";
      } 
      $data = str_replace($page,$repl,$data);
    }
    // Images
    preg_match_all("/(\{img [^\}]+})/",$data,$pages);
    foreach(array_unique($pages[1]) as $page) {
      $parts = explode(" ",$page);
      $imgdata = Array();
      $imgdata["src"]='';
      $imgdata["height"]='';
      $imgdata["width"]='';
      $imgdata["link"]='';
      $imgdata["align"]='';
      $imgdata["desc"]='';
      foreach($parts as $part) {
        $part = str_replace('}','',$part);
        $part = str_replace('{','',$part);
        $part = str_replace('\'','',$part);
        $part = str_replace('"','',$part);
        if(strstr($part,'=')) {
            $subs = explode("=",$part,2);
            $imgdata[$subs[0]]=$subs[1];
        }
      }
      //print("todo el tag es: ".$page."<br/>");
      //print_r($imgdata);
      $repl = "<div class=\"innerimg\"><img src='".$imgdata["src"]."' border='0' ";
      if($imgdata["width"]) $repl.=" width='".$imgdata["width"]."'";
      if($imgdata["height"]) $repl.=" height='".$imgdata["height"]."'";
      $repl.= " /></div>";
      if($imgdata["link"]) {
        $repl ="<a href='".$imgdata["link"]."'>".$repl."</a>";
      }
      if($imgdata["desc"]) {
        $repl="<table cellpadding='0' cellspacing='0'><tr><td>".$repl."</td></tr><tr><td class='mini'>".$imgdata["desc"]."</td></tr></table>"; 
      }
      if($imgdata["align"]) {
        $repl ="<div align='".$imgdata["align"]."'>".$repl."</div>"; 
      }
      $data = str_replace($page,$repl,$data);
    }
    
    $target='';
    if($this->get_preference('popupLinks','n')=='y') {
      $target='target="_blank"';  
    }
    
    $links = $this->get_links($data);
        
    // Note that there're links that are replaced 
        
    foreach($links as $link) {
      if( $this->is_cached($link) && $cachepages == 'y') {
        $cosa="<a class=\"wikicache\" target=\"_blank\" href=\"tiki-view_cache.php?url=$link\">(cache)</a>";
        $link2 = str_replace("/","\/",$link);
        $link2 = str_replace("?","\?",$link2);
        $link2 = str_replace("&","\&",$link2);
        $pattern = "/\[$link2\|([^\]\|]+)\|([^\]]+)\]/";
        $data = preg_replace($pattern,"<a class='wiki' $target href='$link'>$1</a>",$data);
        $pattern = "/\[$link2\|([^\]\|]+)\]/";
        $data = preg_replace($pattern,"<a class='wiki' $target href='$link'>$1</a> $cosa",$data);
        $pattern = "/\[$link2\]/";
        $data = preg_replace($pattern,"<a class='wiki' $target href='$link'>$link</a> $cosa",$data);
      } else {
        $link2 = str_replace("/","\/",$link);
        $link2 = str_replace("?","\?",$link2);
        $link2 = str_replace("&","\&",$link2);
        $pattern = "/\[$link2\|([^\]\|]+)([^\]])*\]/";
        $data = preg_replace($pattern,"<a class='wiki' $target href='$link'>$1</a>",$data);
        $pattern = "/\[$link2\]/";
        $data = preg_replace($pattern,"<a class='wiki' $target href='$link'>$link</a>",$data);
      }
    }
    
    // Title bars
    $data = preg_replace("/-=([^=]+)=-/","<div class='titlebar'>$1</div>",$data);
    
    // Now tokenize the expression and process the tokens
    /* Use tab and newline as tokenizing characters as well  */
    $lines = explode("\n",$data);
    $data = ''; $listbeg='';
    foreach ($lines as $line) {
      // If the first character is ' ' and we are not in pre then we are in pre
      if(substr($line,0,1)==' ') {
        if($listbeg) {
          $data.=$listbeg;
          $listbeg = false; 
        }
        // If the first character is space then 
        // change spaces for &nbsp;
        $line = '<font face="courier" size="2">'.str_replace(' ','&nbsp;',substr($line,1)).'</font>';
        $line.='<br/>';
      } else {
        // Reemplazar las bold
        $line = preg_replace("/__([^_]+)__/","<b>$1</b>",$line);
        $line = preg_replace("/\'\'([^']+)\'\'/","<i>$1</i>",$line);
        // Reemplazar las definiciones
        $line = preg_replace("/;([^:]+):([^\n]+)/","<dl><dt>$1</dt><dd>$2</dd></dl>",$line);
        /*
        $line = preg_replace("/\[([^\|]+)\|([^\]]+)\]/","<a class='wiki' $target href='$1'>$2</a>",$line);
        // Segundo intento reemplazar los [link] comunes
        $line = preg_replace("/\[([^\]]+)\]/","<a class='wiki' $target href='$1'>$1</a>",$line);
        $line = preg_replace("/\-\=([^=]+)\=\-/","<div class='wikihead'>$1</div>",$line);
        */
        
        // This line is parseable then we have to see what we have
        if(strstr($line,"----")) {
          if($listbeg) {
            $data.=$listbeg;
            $listbeg = false; 
          }
          $line='<hr/>';
        } else {
          if(substr($line,0,1)=='*') {
            if($listbeg && $listbeg!='</ul>') {
              $data.=$listbeg;  
              $listbeg=false;
            }
            $line = '<li>'.substr($line,1).'</li>';
            if(!$listbeg) {
              $listbeg = '</ul>';
              $line = '<ul>'.$line; 
            }
          } elseif(substr($line,0,1)=='#') {
            if($listbeg && $listbeg!='</ol>') {
              $data.=$listbeg;  
              $listbeg=false;
            }
            $line = '<li>'.substr($line,1).'</li>'; 
            if(!$listbeg) {
              $listbeg = '</ol>';
              $line = '<ol>'.$line; 
            }
          } elseif(substr($line,0,3)=='!!!') {
            $line = '<h3>'.substr($line,3).'</h3>';
          } elseif(substr($line,0,2)=='!!') {
            $line = '<h2>'.substr($line,2).'</h2>';
          } elseif(substr($line,0,1)=='!') {
            $line = '<h1>'.substr($line,1).'</h1>';
          } else {
            if($listbeg) {
              $data.=$listbeg;  
              $listbeg=false;
            } else {
              $line.='<br/>'; 
            }
          }
        }
      } 
      $data.=$line;
    }
    return $data;  
  }   



  function get_pages($data) {
    preg_match_all("/\b([A-Z][a-z]+[A-Z][a-z]+[A-Za-z]*)\b/",$data,$pages);
    return $pages[1];
  }

  function replace_link($pageFrom, $pageTo) {
    $query = "replace into tiki_links(fromPage,toPage) values('$pageFrom','$pageTo')";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result); 
  }

  function update_page($pageName,$edit_data,$edit_comment, $edit_user, $edit_ip) 
  {
    $edit_data = addslashes($edit_data);
    $edit_comment = addslashes($edit_comment);
    if(!$this->page_exists($pageName)) return false;
    $t = date("U");
    // Get this page information
    $info = $this->get_page_info($pageName);
    // Store the old version of this page in the history table
    $version = $info["version"];
    $lastModif = $info["lastModif"];
    $user = $info["user"];
    $ip = $info["ip"];
    $comment = $info["comment"];
    $data = addslashes($info["data"]);
    $query = "insert into tiki_history(pageName, version, lastModif, user, ip, comment, data) 
              values('$pageName',$version,$lastModif,'$user','$ip','$comment','$data')";
    if($pageName != 'SandBox') {              
      $result = $this->db->query($query);
      if(DB::isError($result)) $this->sql_error($query,$result);
    }
    // Update the pages table with the new version of this page            
    $version += 1;
    //$edit_data = addslashes($edit_data);
    $query = "update tiki_pages set data='$edit_data', comment='$edit_comment', lastModif=$t, version=$version, user='$edit_user', ip='$edit_ip' where pageName='$pageName'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    // Parse edit_data updating the list of links from this page
    $pages = $this->get_pages($edit_data);
    foreach($pages as $page) {
      $this->replace_link($pageName,$page);
    }
    // Update the log
    if($pageName != 'SandBox') {              
      $action = "Updated";
      $query = "insert into tiki_actionlog(action,pageName,lastModif,user,ip,comment) values('$action','$pageName',$t,'$edit_user','$edit_ip','$edit_comment')";
      $result = $this->db->query($query);
      if(DB::isError($result)) $this->sql_error($query,$result);
      $maxversions = $this->get_preference("maxVersions",0);
      if($maxversions) {
        $query = "select pageName,version from tiki_history where pageName='$pageName' order by lastModif desc limit $maxversions,-1"; 
        $result = $this->db->query($query);
        if(DB::isError($result)) $this->sql_error($query,$result);
        $toelim = $result->numRows();
        while($res= $result->fetchRow(DB_FETCHMODE_ASSOC)) {
          $page = $res["pageName"];
          $version = $res["version"];
          $query = "delete from tiki_history where pageName='$pageName' and version='$version'";
          $this->db->query($query);
        }
      }
    }
  }

  // This function get the last changes from pages from the last $days days
  // if days is 0 this gets all the registers
  function get_last_changes($days, $offset=0, $limit=-1, $sort_mode = 'lastModif_desc') 
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($days) {
      $toTime = mktime(23,59,59,date("m"),date("d"),date("Y"));
      $fromTime = $toTime - (24*60*60*$days);
      $query = "select action, lastModif, user, ip, pageName,comment from tiki_actionlog where lastModif>=$fromTime and lastModif<=$toTime order by $sort_mode limit $offset,$limit";
      $query_cant = "select count(*) from tiki_actionlog where lastModif>=$fromTime and lastModif<=$toTime";
    } else {
      $query = "select action, lastModif, user, ip, pageName,comment from tiki_actionlog order by $sort_mode limit $offset,$limit";
      $query_cant = "select count(*) from tiki_actionlog";
    }
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $cant = $this->db->getOne($query_cant);
    $ret = Array(); $r=Array();
    while($res=$result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $r["action"] = $res["action"];
      $r["lastModif"] = $res["lastModif"];
      $r["user"] = $res["user"];
      $r["ip"] = $res["ip"];
      $r["pageName"] = $res["pageName"];
      $r["comment"] = $res["comment"];
      $ret[]=$r;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
} // end of class



function compare_links($ar1,$ar2) {
  return $ar1["links"] - $ar2["links"];  
}
  
function compare_backlinks($ar1,$ar2) {
  return $ar1["backlinks"] - $ar2["backlinks"];  
}
  
function r_compare_links($ar1,$ar2) {
  return $ar2["links"] - $ar1["links"];  
}
  
function r_compare_backlinks($ar1,$ar2) {
  return $ar2["backlinks"] - $ar1["backlinks"];  
}

function compare_images($ar1,$ar2) {
  return $ar1["images"] - $ar2["images"];   
}

function r_compare_images($ar1,$ar2) {
  return $ar2["images"] - $ar1["images"];   
}

  
function compare_versions($ar1,$ar2) {
  return $ar1["versions"] - $ar2["versions"];   
}
  
function r_compare_versions($ar1,$ar2) {
  return $ar2["versions"] - $ar1["versions"];  
}

function compare_changed($ar1, $ar2) {
  return $ar1["lastChanged"] - $ar2["lastChanged"];
}

function r_compare_changed($ar1, $ar2) {
  return $ar2["lastChanged"] - $ar1["lastChanged"];
}
?>