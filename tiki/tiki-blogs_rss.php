<?
require_once('tiki-setup.php');
require_once('lib/tikilib.php'); # httpScheme()

if($rss_blogs != 'y') {
 die;
}
header("content-type: text/xml");
$foo = parse_url($_SERVER["REQUEST_URI"]);
$foo1=str_replace("tiki-blogs_rss.php",$tikiIndex,$foo["path"]);
$foo2=str_replace("tiki-blogs_rss.php","img/tiki.jpg",$foo["path"]);
$foo3=str_replace("tiki-blogs_rss","tiki-view_blog.php",$foo["path"]);
$home = httpScheme().'://'.$_SERVER["SERVER_NAME"].$foo1;
$img = httpScheme().'://'.$_SERVER["SERVER_NAME"].$foo2;
$read = httpScheme().'://'.$_SERVER["SERVER_NAME"].$foo3;

$now = date("U");
$changes = $tikilib->list_all_blog_posts(0,$max_rss_blogs,'created_desc', '',$now);

//print_r($changes);die;
print('<');
print('?xml version="1.0" ?');
print('>');
?>
<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
         xmlns="http://purl.org/rss/1.0/">
<channel rdf:about="<?=$home?>">
  <title>Tiki RSS feed for weblogs</title>
  <link><?=$home?></link>
  <description>
    Last posts to weblogs
  </description>
  <image rdf:resource="<?=$img?>" />
  <items>
    <rdf:Seq>
      <?php
        
        foreach($changes["data"] as $chg) {
          print('<rdf:li resource="'.$read.'?blogId='.$chg["blogId"].'">'."\n");
          print('<title>'.$chg["blogtitle"].': '.date("m/d/Y h:i",$chg["created"]).'</title>'."\n");
          print('<link>'.$read.'?blogId='.$chg["blogId"].'</link>'."\n");
          $data = date("m/d/Y h:i",$chg["created"]);
          print('<description>'.$chg["data"].'</description>'."\n");
          print('</rdf:li>'."\n");
        }        
      ?>
    </rdf:Seq>  
  </items>
</channel>
</rdf:RDF>       