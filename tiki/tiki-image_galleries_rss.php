<?
require_once('tiki-setup.php');
if($rss_image_galleries != 'y') {
 die;
}
header("content-type: text/xml");
$foo = parse_url($_SERVER["REQUEST_URI"]);
$foo1=str_replace("tiki-image_galleries_rss","tiki-index",$foo["path"]);
$foo2=str_replace("tiki-image_galleries_rss.php","img/tiki.jpg",$foo["path"]);
$foo3=str_replace("tiki-image_galleries_rss","tiki-browse_image.php",$foo["path"]);
$home = 'http://'.$_SERVER["SERVER_NAME"].$foo1;
$img = 'http://'.$_SERVER["SERVER_NAME"].$foo2;
$read = 'http://'.$_SERVER["SERVER_NAME"].$foo3;
$title = $tikilib->get_preference("title","Tiki RSS feed for image galleries");
$now = date("U");
$changes = $tikilib->list_images(0,10,'created_desc', '');

//print_r($changes);die;
print('<');
print('?xml version="1.0" ?');
print('>');
?>
<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
         xmlns="http://purl.org/rss/1.0/">
<channel rdf:about="<?=$home?>">
  <title>Tiki RSS feed for image galleries</title>
  <link><?=$home?></link>
  <description>
    Last images uploaded to the image galleries.
  </description>
  <image rdf:resource="<?=$img?>" />
  <items>
    <rdf:Seq>
      <?php
        // LOOP collecting last changes to the wiki
        foreach($changes["data"] as $chg) {
          print('<rdf:li resource="'.$read.'?imageId='.$chg["imageId"].'">'."\n");
          print('<title>'.$chg["name"].'</title>'."\n");
          print('<link>'.$read.'?imageId='.$chg["imageId"].'</link>'."\n");
          $data = date("m/d/Y h:i",$chg["created"]);
          print('<description>'.$chg["description"].'</description>'."\n");
          print('</rdf:li>'."\n");
        }        
      ?>
    </rdf:Seq>  
  </items>
</channel>
</rdf:RDF>       