<?
require_once('tiki-setup.php');
if($rss_articles != 'y') {
 die;
}
header("content-type: text/xml");
$foo = parse_url($_SERVER["REQUEST_URI"]);
$foo1=str_replace("tiki-articles_rss.php",$tikiIndex,$foo["path"]);
$foo2=str_replace("tiki-articles_rss.php","img/tiki.jpg",$foo["path"]);
$foo3=str_replace("tiki-articles_rss","tiki-read_article.php",$foo["path"]);
$home = 'http://'.$_SERVER["SERVER_NAME"].$foo1;
$img = 'http://'.$_SERVER["SERVER_NAME"].$foo2;
$read = 'http://'.$_SERVER["SERVER_NAME"].$foo3;
//$title = $tikilib->get_preference("title","Tiki RSS feedpepe");
$now = date("U");
$changes = $tikilib->list_articles(0,$max_rss_articles,'publishDate_desc', '', $now);
//print_r($changes);die;
print('<');
print('?xml version="1.0" ?');
print('>');
?>
<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
         xmlns="http://purl.org/rss/1.0/">
<channel rdf:about="<?=$home?>">
  <title>Tiki RSS feed for articles</title>
  <link><?=$home?></link>
  <description>
    Last articles.
  </description>
  <image rdf:resource="<?=$img?>" />
  <items>
    <rdf:Seq>
      <?php
        // LOOP collecting last changes to the wiki
        foreach($changes["data"] as $chg) {
          print('<rdf:li resource="'.$read.'?articleId='.$chg["articleId"].'">'."\n");
          print('<title>'.$chg["title"].'</title>'."\n");
          print('<link>'.$read.'?articleId='.$chg["articleId"].'</link>'."\n");
          $data = date("m/d/Y h:i",$chg["publishDate"]);
          print('<description>'.$chg["heading"].'</description>'."\n");
          print('</rdf:li>'."\n");
        }        
      ?>
    </rdf:Seq>  
  </items>
</channel>
</rdf:RDF>       