<?php
  require_once('tiki-setup.php');
  require_once('lib/tikilib.php'); # httpScheme()

  if($rss_file_gallery != 'y') {
   die;
  } 

  header("content-type: text/xml");
  $foo = parse_url($_SERVER["REQUEST_URI"]);
  if(!isset($_REQUEST["galleryId"])) {
    die;
  }
  $foo1=str_replace("tiki-file_gallery_rss.php",$tikiIndex,$foo["path"]);
  $foo2=str_replace("tiki-file_gallery_rss.php","img/tiki.jpg",$foo["path"]);
  $foo3=str_replace("tiki-file_gallery_rss","tiki-download_file",$foo["path"]);
  $foo4=str_replace("tiki-file_gallery_rss.php","lib/rss/rss-style.css",$foo["path"]);
  $home = httpPrefix().$foo1;
  $img = httpPrefix().$foo2;
  $read = httpPrefix().$foo3;
  $css = httpPrefix().$foo4;

  $now = date("U");
  $changes = $tikilib->get_files( 0,10,'created_desc', '', $_REQUEST["galleryId"]);
  $info = $tikilib->get_file_gallery($_REQUEST["galleryId"]);
  $galleryname = $info["name"];
  $gallerydesc = $info["description"];
  $title = "Tiki RSS feed for the file gallery:";

  print '<?xml version="1.0" encoding="UTF-8"?>'."\n";
  print '<?xml-stylesheet href="'.$css.'" type="text/css"?>'."\n";
?>
<rdf:RDF xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:h="http://www.w3.org/1999/xhtml" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns="http://purl.org/rss/1.0/">
<channel rdf:about="<?php echo $home; ?>">
  <title><?php echo $title; ?> <?php echo htmlspecialchars($galleryname);?></title>
  <link><?php echo $home; ?></link>
  <description>
    <?php echo htmlspecialchars($gallerydesc);?>
  </description>
  <image rdf:about="<?php echo $img; ?>">
    <title><?php echo $title; ?> <?php echo htmlspecialchars($galleryname);?></title>
    <link><?php echo $home?></link>
  </image>

  <items>
    <rdf:Seq>
      <?php
        // LOOP collecting last changes to file galleries
        foreach($changes["data"] as $chg) {
          print('        <rdf:li resource="'.$read.'?fileId='.$chg["fileId"].'" />'."\n");
        }        
      ?>
    </rdf:Seq>  
  </items>
</channel>

<?php
  // LOOP collecting last changes to file galleries
  foreach($changes["data"] as $chg) {
    print('<item rdf:about="'.$read.'?fileId='.$chg["fileId"].'">'."\n");
          print('<title>'.htmlspecialchars($chg["filename"]).'</title>'."\n");
          print('<link>'.$read.'?fileId='.$chg["fileId"].'</link>'."\n");
          $data = $tikilib->date_format($tikilib->get_short_datetime_format(),$chg["created"]);
          print('<description>'.htmlspecialchars($chg["description"]).'</description>'."\n");
    print('</item>'."\n\n");
  }        
?>

</rdf:RDF>       