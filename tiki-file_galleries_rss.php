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
  $foo4=str_replace("tiki-file_galleries_rss.php","lib/rss/rss-style.css",$foo["path"]);
  $home = httpPrefix().$foo1;
  $img = httpPrefix().$foo2;
  $read = httpPrefix().$foo3;
  $css = httpPrefix().$foo4;
  
  $title = $tikilib->get_preference("title","Tiki RSS feed for file galleries");
  $title = "Tiki RSS feed for file galleries";
  $desc = "Last files uploaded to the file galleries.";
  $now = date("U");
  $changes = $tikilib->list_files(0,$max_rss_file_galleries,'created_desc', '');

  print '<?xml version="1.0" encoding="UTF-8"?>'."\n";
  print '<?xml-stylesheet href="'.$css.'" type="text/css"?>'."\n";
?>
<rdf:RDF xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:h="http://www.w3.org/1999/xhtml" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns="http://purl.org/rss/1.0/">
<channel rdf:about="<?php echo $home; ?>">
  <title><?php echo $title; ?></title>
  <link><?php echo $home; ?></link>
  <description>
    <?php echo $desc; ?>
  </description>
  <image rdf:about="<?php echo $img; ?>">
    <title><?php echo $title; ?></title>
    <link><?php echo $home?></link>
  </image>

  <items>
    <rdf:Seq>
      <?php
        // LOOP collecting last changes to file galleries (index)
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