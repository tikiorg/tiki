<?php

require_once('tiki-setup.php');
require_once('lib/tikilib.php'); # httpScheme()

if($rss_forums != 'y') {
 die;
}
header("content-type: text/xml");
$foo = parse_url($_SERVER["REQUEST_URI"]);
$foo1=str_replace("tiki-forums_rss.php",$tikiIndex,$foo["path"]);
$foo2=str_replace("tiki-forums_rss.php","img/tiki.jpg",$foo["path"]);
$foo3=str_replace("tiki-forums_rss","tiki-view_forum_thread",$foo["path"]);
$home = httpPrefix().$foo1;
$img = httpPrefix().$foo2;
$read = httpPrefix().$foo3;

$now = date("U");
$changes = $tikilib->list_all_forum_topics(0,$max_rss_forums,'commentDate_desc', '');

//print_r($changes);die;
print('<');
print('?xml version="1.0" ?');
print('>');
?>
<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
         xmlns="http://purl.org/rss/1.0/">
<channel rdf:about="<?php echo $home?>">
  <title>Tiki RSS feed for forums</title>
  <link><?php echo $home?></link>
  <description>
    Last topics in forums
  </description>
  <image rdf:resource="<?php echo $img?>" />
  <items>
    <rdf:Seq>
      <?php
        
        foreach($changes["data"] as $chg) {
          print('<rdf:li resource="'.$read.'?parentId='.$chg["threadId"].'">'."\n");
          print('<title>'.$chg["title"].': '.date("m/d/Y h:i",$chg["commentDate"]).'</title>'."\n");
          print('<link>'.$read.'?parentId='.$chg["threadId"].'</link>'."\n");
          $data = date("m/d/Y h:i",$chg["commentDate"]);
          print('<description>'.$chg["data"].'</description>'."\n");
          print('</rdf:li>'."\n");
        }        
      ?>
    </rdf:Seq>  
  </items>
</channel>
</rdf:RDF>       