<?php

require_once('tiki-setup.php');
require_once('lib/tikilib.php'); # httpScheme()
include_once('lib/blogs/bloglib.php');

if($rss_blogs != 'y') {
 die;
}
header("content-type: text/xml");
$foo = parse_url($_SERVER["REQUEST_URI"]);
$foo1=str_replace("tiki-blogs_rss.php",$tikiIndex,$foo["path"]);
$foo2=str_replace("tiki-blogs_rss.php","img/tiki.jpg",$foo["path"]);
$foo3=str_replace("tiki-blogs_rss","tiki-view_blog",$foo["path"]);
$home = httpPrefix().$foo1;
$img = httpPrefix().$foo2;
$read = httpPrefix().$foo3;

$now = date("U");
$changes = $bloglib->list_all_blog_posts(0,$max_rss_blogs,'created_desc', '',$now);

//print_r($changes);die;
print('<');
print('?xml version="1.0" ?');
print('>');
?>
<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
         xmlns="http://purl.org/rss/1.0/">
<channel rdf:about="<?php echo $home?>">
  <title>Tiki RSS feed for weblogs</title>
  <link><?php echo $home?></link>
  <description>
    Last posts to weblogs
  </description>
  <image rdf:resource="<?php echo $img?>" />
  <items>
    <rdf:Seq>
      <?php
        
        foreach($changes["data"] as $chg) {
          print('<rdf:li resource="'.$read.'?blogId='.$chg["blogId"].'">'."\n");
          print('<title>'.$chg["blogtitle"].': '.
          $tikilib->date_format($tikilib->get_short_datetime_format(), $chg["created"]).'</title>'."\n");
          print('<link>'.$read.'?blogId='.$chg["blogId"].'</link>'."\n");
          $data = $tikilib->date_format($tikilib->get_short_datetime_format(),$chg["created"]);
          print('<description>'.htmlspecialchars($chg["data"]).'</description>'."\n");
          print('</rdf:li>'."\n");
        }        
      ?>
    </rdf:Seq>  
  </items>
</channel>
</rdf:RDF>       