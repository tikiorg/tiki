<?php

require_once('tiki-setup.php');
require_once('lib/tikilib.php'); # httpScheme()

if($rss_articles != 'y') {
 die;
}
header("content-type: text/xml");
$foo = parse_url($_SERVER["REQUEST_URI"]);
$foo1=str_replace("tiki-articles_rss.php",$tikiIndex,$foo["path"]);
$foo2=str_replace("tiki-articles_rss.php","img/tiki.jpg",$foo["path"]);
$foo3=str_replace("tiki-articles_rss.php","tiki-read_article.php",$foo["path"]);
$home = httpPrefix().$foo1;
$img = httpPrefix().$foo2;
$read = httpPrefix().$foo3;
//$title = $tikilib->get_preference("title","Tiki RSS feedpepe");
$now = date("U");
$changes = $tikilib->list_articles(0,$max_rss_articles,'publishDate_desc', '', $now,$user);
//print_r($changes);die;
print('<');
print('?xml version="1.0" ?');
print('>');
?>
<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
         xmlns="http://purl.org/rss/1.0/">
<channel rdf:about="<?php echo $home?>">
  <title>Tiki RSS feed for articles</title>
  <link><?php echo $home?></link>
  <description>
    Last articles.
  </description>
  <image rdf:resource="<?php echo $img?>" />
  <items>
    <rdf:Seq>
      <?php
        // LOOP collecting last changes to the wiki
        foreach($changes["data"] as $chg) {
          print('<rdf:li resource="'.htmlspecialchars($read).'?articleId='.htmlspecialchars($chg["articleId"]).'">'."\n");
          print('<title>'.htmlspecialchars($chg["title"]).'</title>'."\n");
          print('<link>'.$read.'?articleId='.$chg["articleId"].'</link>'."\n");
          $data = $tikilib->date_format($tikilib->get_short_datetime_format(),$chg["publishDate"]);
          print('<description>'.htmlspecialchars($chg["heading"]).'</description>'."\n");
          print('</rdf:li>'."\n");
        }        
      ?>
    </rdf:Seq>  
  </items>
</channel>
</rdf:RDF>       