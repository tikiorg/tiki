<?php
class RSSLib extends TikiLib {

  function RSSLib($db) 
  {
    # this is probably uneeded now
    if(!$db) {
      die("Invalid db object passed to RSSLib constructor");  
    }
    $this->db = $db;  
  }
  
  function list_rss_modules($offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where (name like '%".$find."%' or description like '%".$find."%')";
    } else {
      $mid="";
    }
    $query = "select * from tiki_rss_modules $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_rss_modules $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $res["minutes"]=$res["refresh"]/60;
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  function replace_rss_module($rssId, $name, $description, $url, $refresh)
  {
    //if($this->rss_module_name_exists($name)) return false;
    $description = addslashes($description);
    $name = addslashes($name);
    // Check the name

    $refresh = 60*$refresh;
    if($rssId) {
      $query = "update tiki_rss_modules set name='$name',description='$description',refresh=$refresh,url='$url' where rssId=$rssId";
    } else {
      $query = "replace into tiki_rss_modules(name,description,url,refresh,content,lastUpdated)
                values('$name','$description','$url',$refresh,'',1000000)";
    }
    $result = $this->query($query);
    return true;
  }
  
  function remove_rss_module($rssId)
  {
    $query = "delete from tiki_rss_modules where rssId=$rssId";
    $result = $this->query($query);
    return true;
  }
  
  function get_rss_module($rssId)
  {
    $query = "select * from tiki_rss_modules where rssId=$rssId";
    $result = $this->query($query);
    if(!$result->numRows()) return false;
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }
  
  function startElementHandler($parser, $name,$attribs) {
    if($this->flag) {
      $this->buffer.='<'.$name.'>';
    }
    if($name=='item' || $name=='items') {
      $this->flag=1;
    }

  }

  function endElementHandler($parser, $name) {
    if($name=='item' || $name=='items') {
      $this->flag=0;
    }
    if($this->flag) {
      $this->buffer.='</'.$name.'>';
    }
  }
  
  function characterDataHandler($parser, $data) {
    if($this->flag) {
      $this->buffer.=$data;
    }
  }
  
  function NewsFeed($data) {
    $news = Array();
    $this->buffer = '';
    $this->flag=0;
    $this->parser=xml_parser_create();
    xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false);
    xml_set_object($this->parser,$this);
    xml_set_element_handler($this->parser,"startElementHandler","endElementHandler");
    xml_set_character_data_handler($this->parser,"characterDataHandler");
    if (!xml_parse($this->parser, $data, 1)) {
                    return $news;
    }
    xml_parser_free($this->parser);
    preg_match_all("/<title>(.*)<\/title>/",$this->buffer,$titles);
    preg_match_all("/<link>(.*)<\/link>/",$this->buffer,$links);
    for($i=0;$i<count($titles[1]);$i++) {
      $anew["title"]=$titles[1][$i];
      if(isset($links[1][$i])) {
        $anew["link"] = $links[1][$i];
      } else {
        $anew["link"]='';
      }
      $news[]=$anew;
    }
    return $news;
  }

  function parse_rss_data($rssdata)
  {
    return $this->NewsFeed($rssdata);
  }
  
  function refresh_rss_module($rssId)
  {
    $info = $this->get_rss_module($rssId);
    @$fp = fopen($info["url"],"r");
    if(!$fp) return false;
    $data = '';
    while(!feof($fp)) {
      $data .= fread($fp,4096);
    }
    $datai = addslashes($data);
    $now = date("U");
    $query = "update tiki_rss_modules set content='$datai', lastUpdated=$now where rssId=$rssId";
    $result = $this->query($query);
    return $data;
  }
  
  function rss_module_name_exists($name)
  {
    $query = "select name from tiki_rss_modules where name='$name'";
    $result = $this->query($query);
    return $result->numRows();
  }
  
  function get_rss_module_id($name)
  {
    $query = "select rssId from tiki_rss_modules where name='$name'";
    $id = $this->getOne($query);
    return $id;
  }
  
  function get_rss_module_content($rssId)
  {

   $info = $this->get_rss_module($rssId);
   $now = date("U");
   if($info["lastUpdated"]+$info["refresh"]<$now) {
     $data = $this->refresh_rss_module($rssId);
   }
   $info = $this->get_rss_module($rssId);
   return $info["content"];
  }

  
}

$rsslib= new RSSLib($dbTiki);

?>