<?
require_once('tiki-setup.php');
require_once('lib/tikilib.php'); # httpScheme()

if($rss_file_galleries != 'y') {
 die;
}
header("content-type: text/xml");
$foo = parse_url($_SERVER["REQUEST_URI"]);
$foo1=str_replace("tiki-file_galleries_rss.php",$tikiIndex,$foo["path"]);
$foo2=str_replace("tiki-file_galleries_rss.php","img/tiki.jpg",$foo["path"]);
$foo3=str_replace("tiki-file_galleries_rss","tiki-download_file.php",$foo["path"]);
$home = httpScheme().'://'.$_SERVER["SERVER_NAME"].$foo1;
$img = httpScheme().'://'.$_SERVER["SERVER_NAME"].$foo2;
$read = httpScheme().'://'.$_SERVER["SERVER_NAME"].$foo3;
$title = $tikilib->get_preference("title","Tiki RSS feed for file galleries");
$now = date("U");
$changes = $tikilib->list_files(0,$max_rss_file_galleries,'created_desc', '');

//print_r($changes);die;
print('<');
print('?xml version="1.0" ?');
print('>');
?>
<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
         xmlns="http://purl.org/rss/1.0/">
<channel rdf:about="<?=$home?>">
  <title>Tiki RSS feed for file galleries</title>
  <link><?=$home?></link>
  <description>
    Last files uploaded to the file galleries.
  </description>
  <image rdf:resource="<?=$img?>" />
  <items>
    <rdf:Seq>
      <?php
        
        foreach($changes["data"] as $chg) {
          print('<rdf:li resource="'.$read.'?fileId='.$chg["fileId"].'">'."\n");
          print('<title>'.$chg["filename"].'</title>'."\n");
          print('<link>'.$read.'?fileId='.$chg["fileId"].'</link>'."\n");
          $data = date("m/d/Y h:i",$chg["created"]);
          print('<description>'.$chg["description"].'</description>'."\n");
          print('</rdf:li>'."\n");
        }        
      ?>
    </rdf:Seq>  
  </items>
</channel>
</rdf:RDF>       