<?php

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
$home = httpPrefix().$foo1;
$img = httpPrefix().$foo2;
$read = httpPrefix().$foo3;
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
<channel rdf:about="<?php echo $home?>">
  <title>Tiki RSS feed for file galleries</title>
  <link><?php echo $home?></link>
  <description>
    Last files uploaded to the file galleries.
  </description>
  <image rdf:resource="<?php echo $img?>" />
  <items>
    <rdf:Seq>
      <?php
        
        foreach($changes["data"] as $chg) {
          print('<rdf:li resource="'.$read.'?fileId='.$chg["fileId"].'">'."\n");
          print('<title>'.$chg["filename"].'</title>'."\n");
          print('<link>'.$read.'?fileId='.$chg["fileId"].'</link>'."\n");
          $data = $tikilib->date_format($tikilib->get_short_datetime_format(),$chg["created"]);
          print('<description>'.$chg["description"].'</description>'."\n");
          print('</rdf:li>'."\n");
        }        
      ?>
    </rdf:Seq>  
  </items>
</channel>
</rdf:RDF>       