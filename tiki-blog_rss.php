<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-blog_rss.php,v 1.13 2003-08-21 00:51:20 redflo Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

  require_once('tiki-setup.php');
  require_once('lib/tikilib.php');
  include_once('lib/blogs/bloglib.php');

  header("content-type: text/xml");
  $foo = parse_url($_SERVER["REQUEST_URI"]);

  if($rss_blog != 'y') {
   die;
  }

  if($tiki_p_read_blog != 'y') {
    $smarty->assign('msg',tra("Permission denied you can not view this section"));
    $smarty->display("styles/$style_base/error.tpl");
    die;  
  }

  if(!isset($_REQUEST["blogId"])) {
    die;
  }

  $foo1=str_replace("tiki-blog_rss.php",$tikiIndex,$foo["path"]);
  $foo2=str_replace("tiki-blog_rss.php","img/tiki.jpg",$foo["path"]);
  $foo3=str_replace("tiki-blog_rss","tiki-view_blog",$foo["path"]);
  $foo4=str_replace("tiki-blog_rss.php","lib/rss/rss-style.css",$foo["path"]);
  $home = httpPrefix().$foo1;
  $img = httpPrefix().$foo2;
  $read = httpPrefix().$foo3;
  $css = httpPrefix().$foo4;
  $title = $tikilib->get_preference("title","Tiki RSS feed for the weblog:");
  $now = date("U");
  $changes = $bloglib->list_blog_posts($_REQUEST["blogId"], 0,$max_rss_blog,'created_desc', '', $now);
  $info = $tikilib->get_blog($_REQUEST["blogId"]);
  $blogtitle = $info["title"];
  $blogdesc = $info["description"];

print '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
print '<?xml-stylesheet href="' . $css . '" type="text/css"?>' . "\n";

?>

<rdf:RDF xmlns:dc = "http://purl.org/dc/elements/1.1/"
	xmlns:h = "http://www.w3.org/1999/xhtml" xmlns:rdf = "http://www.w3.org/1999/02/22-rdf-syntax-ns#"
	xmlns = "http://purl.org/rss/1.0/">
	<channel rdf:about = "<?php echo $home; ?>">
		<title><?php

		echo $title;

		?>

		<?php

		echo htmlspecialchars($blogtitle);

		?>

		</title>

		<link>
			<?php

			echo $home;

			?>

		</link>

		<description>
			<?php

			echo htmlspecialchars($blogdesc);

			?>

		</description>

		<image rdf:about = "<?php echo $img; ?>"> <title><?php

		echo $title;

		?>

		<?php

		echo htmlspecialchars($blogtitle);

		?>

		</title>

		<link>
			<?php

			echo $home

			?>

		</link> </image>

		<items>
			<rdf:Seq>
				<?php

			// LOOP collecting last changes to blog (index)
			foreach ($changes["data"] as $chg) {
				print ('        <rdf:li resource="' . $read . '?blogId=' . $_REQUEST["blogId"] . '" />' . "\n");
			}

			?>

			</rdf:Seq>
		</items>
	</channel>

	<?php

	// LOOP collecting last changes to blogs
	foreach ($changes["data"] as $chg) {
		print ('<item rdf:about="' . $read . '?blogId=' . $_REQUEST["blogId"] . '">' . "\n");

		print ('<title>' . $tikilib->date_format($tikilib->get_long_datetime_format(), $chg["created"]). '</title>' . "\n");
		print ('<link>' . $read . '?blogId=' . $_REQUEST["blogId"] . '</link>' . "\n");
		$data = $tikilib->date_format($tikilib->get_short_datetime_format(), $chg["created"]);
		print ('<description>' . htmlspecialchars($chg["data"]). '</description>' . "\n");
		print ('</item>' . "\n\n");
	}

	?>

</rdf:RDF>
