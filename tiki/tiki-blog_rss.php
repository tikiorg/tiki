<?php
require_once('tiki-setup.php');
require_once('lib/tikilib.php'); # httpScheme()
include_once('lib/blogs/bloglib.php');

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
$home = httpPrefix().$foo1;
$img = httpPrefix().$foo2;
$read = httpPrefix().$foo3;
$title = $tikilib->get_preference("title","pepe");
$now = date("U");
$changes = $bloglib->list_blog_posts($_REQUEST["blogId"], 0,$max_rss_blog,'created_desc', '', $now);
$info = $tikilib->get_blog($_REQUEST["blogId"]);
$blogtitle = $info["title"];
$blogdesc = $info["description"];
//print_r($changes);die;
print('<');
print('?xml version="1.0" encoding="UTF-8" ?');
print('>');
?>
<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
         xmlns="http://purl.org/rss/1.0/">
<channel rdf:about="<?php echo $home?>">
  <title>Tiki RSS feed for the weblog: <?php echo htmlspecialchars($blogtitle);?></title>
  <link><?php echo $home?></link>
  <description>
    <?php echo htmlspecialchars($blogdesc);?>
  </description>
  <image rdf:resource="<?php echo $img?>" />
  <items>
    <rdf:Seq>
      <?php
        // LOOP collecting last changes to the wiki
        foreach($changes["data"] as $chg) {
          print('<rdf:li resource="'.$read.'?blogId='.$_REQUEST["blogId"].'">'."\n");
          print('<title>'.
          $tikilib->date_format($tikilib->get_long_datetime_format(),$chg["created"]).'</title>'."\n");
          print('<link>'.$read.'?blogId='.$_REQUEST["blogId"].'</link>'."\n");
          $data = $tikilib->date_format($tikilib->get_short_datetime_format(),$chg["created"]);
          print('<description>'.htmlspecialchars($chg["data"]).'</description>'."\n");
          print('</rdf:li>'."\n");
        }        
      ?>
    </rdf:Seq>  
  </items>
</channel>
</rdf:RDF>       