<?php
class HotwordsLib extends TikiLib {

  function HotwordsLib($db)
  {
    # this is probably uneeded now
    if(!$db) {
      die("Invalid db object passed to HotwordLib constructor");  
    }
    $this->db = $db;  
  }
  
  function list_hotwords($offset = 0,$maxRecords = -1,$sort_mode = 'word_desc', $find='')
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
	$findesc = $this->qstr('%'.$find.'%');
      $mid=" where (word like $findesc) ";
    } else {
      $mid='';
    }
    $query = "select * from tiki_hotwords $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_hotwords $mid";
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
  
  function add_hotword($word,$url)
  {
    $word=addslashes($word);
    $url=addslashes($url);
    $query = "replace into tiki_hotwords(word,url) values('$word','$url')";
    $result = $this->query($query);
    return true;
  }
  
  function remove_hotword($word)
  {
    $query = "delete from tiki_hotwords where word='$word'";
    $result = $this->query($query);

  }

}
$hotwordlib= new HotwordsLib($dbTiki);
?>