<?php

require_once('tiki-setup.php');
require_once('lib/tikilib.php'); # httpScheme()
include_once("lib/imagegals/imagegallib.php");

if($rss_image_gallery != 'y') {
 die;
}
header("content-type: text/xml");
$foo = parse_url($_SERVER["REQUEST_URI"]);
if(!isset($_REQUEST["galleryId"])) {
  die;
}
$foo1=str_replace("tiki-image_gallery_rss.php",$tikiIndex,$foo["path"]);
$foo2=str_replace("tiki-image_gallery_rss.php","img/tiki.jpg",$foo["path"]);
$foo3=str_replace("tiki-image_gallery_rss","tiki-browse_image",$foo["path"]);
$home = httpPrefix().$foo1;
$img = httpPrefix().$foo2;
$read = httpPrefix().$foo3;

$now = date("U");
$changes = $imagegallib->get_images( 0,$max_rss_image_gallery,'created_desc', '', $_REQUEST["galleryId"]);
$info = $imagegallib->get_gallery($_REQUEST["galleryId"]);
$galleryname = $info["name"];
$gallerydesc = $info["description"];
//print_r($changes);die;
print('<');
print('?xml version="1.0" ?');
print('>');
?>
<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
         xmlns="http://purl.org/rss/1.0/">
<channel rdf:about="<?php echo $home?>">
  <title>Tiki RSS feed for the image gallery: <?php echo $galleryname?></title>
  <link><?php echo $home?></link>
  <description>
    <?php echo $gallerydesc?>
  </description>
  <image rdf:resource="<?php echo $img?>" />
  <items>
    <rdf:Seq>
      <?php
        // LOOP collecting last changes to the wiki
        foreach($changes["data"] as $chg) {
          print('<rdf:li resource="'.$read.'?imageId='.$chg["imageId"].'">'."\n");
          print('<title>'.$chg["name"].'</title>'."\n");
          print('<link>'.$read.'?imageId='.$chg["imageId"].'</link>'."\n");
          $data = $tikilib->date_format($tikilib->get_short_datetime_format(),$chg["created"]);
          print('<description>'.$chg["description"].'</description>'."\n");
          print('</rdf:li>'."\n");
        }        
      ?>
    </rdf:Seq>  
  </items>
</channel>
</rdf:RDF>       
