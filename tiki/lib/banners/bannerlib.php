<?php
class BannerLib extends TikiLib {

  function BannerLib($db) 
  {
    # this is probably uneeded now
    if(!$db) {
      die("Invalid db object passed to BannersLib constructor");  
    }
    $this->db = $db;  
  }
  
  function select_banner($zone)
  {
    // Things to check
    // UseDates and dates
    // Hours
    // weekdays
    // zone
    // maxImpressions and impressions
    # TODO localize
    $dw = strtolower(date("D"));
    $hour = date("H").date("i");
    $now = date("U");
    $raw='';
    //
    //
    $query = "select * from tiki_banners where $dw = 'y' and  hourFrom<=$hour and hourTo>=$hour and
    ( ((useDates = 'y') and (fromDate<=$now and toDate>=$now)) or (useDates = 'n') ) and
    impressions<maxImpressions and zone='$zone'";
    $result = $this->query($query);
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
      $raw = "<div align='center'><a target='_blank' href='banner_click.php?id="
             .$res["bannerId"]
             ."&amp;url="
             .urlencode($res["url"])
             ."'><img alt='banner' border='0' src=\"banner_image.php?id="
             .$res["bannerId"]
             ."\" /></a></div>";
      break;
    case 'useFixedURL':
      @$fp = fopen($res["fixedURLData"],"r");
      if ($fp) {
        $raw = '';
        while(!feof($fp)) {
          $raw .= fread($fp,4096);
        }
      }
      fclose($fp);
      break;
    case 'useText':
      $raw = "<a target='_blank' class='bannertext' href='banner_click.php?id=".$res["bannerId"]."&amp;url=".urlencode($res["url"])."'>".$res["textData"]."</a>";
      break;
    }
    // Increment banner impressions here
    $id = $res["bannerId"];
    if($id) {
      $query = "update tiki_banners set impressions = impressions + 1 where bannerId = $id";
      $result = $this->query($query);
    }
    return $raw;
  }
  
  function add_click($bannerId)
  {
    $query = "update tiki_banners set clicks = clicks + 1 where bannerId=$bannerId";
    $result = $this->query($query);
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
	$findesc = $this->qstr('%'.$find.'%');
      if($mid) {
        $mid.=" and url like $findesc ";
      } else {
        $mid.=" where url like $findesc ";
      }
    }
    $query = "select * from tiki_banners $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_banners $mid";
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
  
  function list_zones()
  {
    $query = "select zone from tiki_zones";
    $query_cant = "select count(*) from tiki_zones";
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
  
  function remove_banner($bannerId)
  {
    $query = "delete from tiki_banners where bannerId=$bannerId";
    $result = $this->query($query);
  }
  
  function get_banner($bannerId)
  {
    $query = "select * from tiki_banners where bannerId=$bannerId";
    $result = $this->query($query);
    if(!$result->numRows()) return false;
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
                mon = '$mon' ,tue = '$tue', wed = '$wed', thu = '$thu', fri = '$fri', sat = '$sat', sun = '$sun',
                maxImpressions = $maxImpressions where bannerId=$bannerId";
       $result = $this->query($query);
    } else {
      $query = "insert into tiki_banners(client, url, title, alt, which, imageData, imageType, HTMLData,
                fixedURLData, textData, fromDate, toDate, useDates, mon, tue, wed, thu, fri, sat, sun,
                hourFrom, hourTo, maxImpressions,created,zone,imageName,impressions,clicks)
                values('$client','$url','$title','$alt','$use','$imageData','$imageType','$HTMLData',
                '$fixedURLData', '$textData', $fromDate, $toDate, '$useDates', '$mon','$tue','$wed','$thu',
                '$fri','$sat','$sun','$hourFrom','$hourTo',$maxImpressions,$now,'$zone','$imageName',0,0)";
      $result = $this->query($query);
      $query = "select max(bannerId) from tiki_banners where created=$now";
      $bannerId = $this->getOne($query);
    }
    return $bannerId;
  }
  
  function banner_add_zone($zone)
  {
    $zone = addslashes($zone);
    $query = "replace into tiki_zones(zone) values('$zone')";
    $result = $this->query($query);
    return true;
  }
  
  function banner_get_zones()
  {
    $query = "select * from tiki_zones";
    $result = $this->query($query);
    $ret= Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[]=$res;
    }
    return $ret;
  }
  
  function banner_remove_zone($zone)
  {
    $query = "delete from tiki_zones where zone='$zone'";
    $result = $this->query($query);
    if(0) {
    $query = "delete from tiki_banner_zones where zoneName='$zone'";
    $result = $this->query($query);
    }

    return true;
  }
  
  
  
}
$bannerlib= new BannerLib($dbTiki);
?>