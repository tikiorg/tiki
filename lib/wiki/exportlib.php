<?php
class ExportLib extends TikiLib {

  function ExportLib($db) 
  {
    # this is probably uneeded now
    if(!$db) {
      die("Invalid db object passed to ExportLib constructor");  
    }
    $this->db = $db;  
  }
  
  function MakeWikiZip()
  {
    $zipname         = "wikidb.zip";
    include_once("lib/tar.class.php");
    $tar = new tar();
    $query = "select pageName from tiki_pages order by pageName asc";
    $result = $this->query($query);
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $page = $res["pageName"];
      $content = $this->export_wiki_page($page, 0);
      $tar->addData($page,$content,date("U"));
    }
    $tar->toTar("dump/export.tar",FALSE);
    return '';
  }

  function export_wiki_page($pageName,$nversions=1)
  {
    $info=$this->get_page_info($pageName);
    $head = '';
    $head .= "Date: " . $this->get_rfc2822_datetime($info["lastModif"]) . "\r\n";
    $head .= sprintf("Mime-Version: 1.0 (Produced by Tiki)\r\n");
    $iter = $this->get_page_history($pageName);
    $parts = array();
    $parts[]=MimeifyPageRevision($info);
    if($nversions>1 || $nversions==0) {
    foreach ($iter as $revision) {
        $parts[] = MimeifyPageRevision($revision);
        if ($nversions > 0 && count($parts) >= $nversions)
            break;
    }
    }
    if (count($parts) > 1)
        return $head . MimeMultipart($parts);
    assert($parts);
    return $head . $parts[0];
  }  
  
  // Returns all the versions for this page
  // without the data itself
  function get_page_history($page)
  {
    $page = addslashes($page);
    $query = "select pageName, description, version, lastModif, user, ip, data, comment from tiki_history where pageName='$page' order by version desc";
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $aux = Array();
      $aux["version"] = $res["version"];
      $aux["lastModif"] = $res["lastModif"];
      $aux["user"] = $res["user"];
      $aux["ip"] = $res["ip"];
      $aux["data"] = $res["data"];
      $aux["pageName"] = $res["pageName"];
      $aux["description"] = $res["description"];
      $aux["comment"] = $res["comment"];
      //$aux["percent"] = levenshtein($res["data"],$actual);
      $ret[]=$aux;
    }
    return $ret;
  }
  
}

$exportlib= new ExportLib($dbTiki);

?>
