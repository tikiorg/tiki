<?
require_once('tiki-setup.php');
require_once('lib/tikilib.php'); # httpScheme()

header("content-type: text/xml");
$foo = parse_url($_SERVER["REQUEST_URI"]);
if($rss_blog != 'y') {
 die;
}
if(!isset($_REQUEST["blogId"])) {
  die;
}
$foo1=str_replace("tiki-blog_rss.php",$tikiIndex,$foo["path"]);
$foo2=str_replace("tiki-blog_rss.php","img/tiki.jpg",$foo["path"]);
$foo3=str_replace("tiki-blog_rss","tiki-view_blog",$foo["path"]);
$home = httpScheme().'://'.$_SERVER["SERVER_NAME"].$foo1;
$img = httpScheme().'://'.$_SERVER["SERVER_NAME"].$foo2;
$read = httpScheme().'://'.$_SERVER["SERVER_NAME"].$foo3;
$title = $tikilib->get_preference("title","pepe");
$now = date("U");
$changes = $tikilib->list_blog_posts($_REQUEST["blogId"], 0,$max_rss_blog,'created_desc', '', $now);
$info = $tikilib->get_blog($_REQUEST["blogId"]);
$blogtitle = $info["title"];
$blogdesc = $info["description"];
//print_r($changes);die;
print('<');
print('?xml version="1.0" ?');
print('>');
?>
<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
         xmlns="http://purl.org/rss/1.0/">
<channel rdf:about="<?=$home?>">
  <title>Tiki RSS feed for the weblog: <?=$blogtitle?></title>
  <link><?=$home?></link>
  <description>
    <?=$blogdesc?>
  </description>
  <image rdf:resource="<?=$img?>" />
  <items>
    <rdf:Seq>
      <?php
        // LOOP collecting last changes to the wiki
        foreach($changes["data"] as $chg) {
          print('<rdf:li resource="'.$read.'?blogId='.$_REQUEST["blogId"].'">'."\n");
          print('<title>'.date("F d Y (h:i)",$chg["created"]).'</title>'."\n");
          print('<link>'.$read.'?blogId='.$_REQUEST["blogId"].'</link>'."\n");
          $data = date("m/d/Y h:i",$chg["created"]);
          print('<description>'.$chg["data"].'</description>'."\n");
          print('</rdf:li>'."\n");
        }        
      ?>
    </rdf:Seq>  
  </items>
</channel>
</rdf:RDF>       