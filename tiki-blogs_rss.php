<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-blogs_rss.php,v 1.14 2003-08-21 00:51:20 redflo Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

  require_once('tiki-setup.php');
  require_once('lib/tikilib.php'); # httpScheme()
  include_once('lib/blogs/bloglib.php');

  if($rss_blogs != 'y') {
   die;
  }

  if($tiki_p_read_blog != 'y') {
    $smarty->assign('msg',tra("Permission denied you can not view this section"));
    $smarty->display("styles/$style_base/error.tpl");
    die;  
  }
  
  header("content-type: text/xml");
  $foo = parse_url($_SERVER["REQUEST_URI"]);
  $foo1=str_replace("tiki-blogs_rss.php",$tikiIndex,$foo["path"]);
  $foo2=str_replace("tiki-blogs_rss.php","img/tiki.jpg",$foo["path"]);
  $foo3=str_replace("tiki-blogs_rss","tiki-view_blog",$foo["path"]);
  $foo4=str_replace("tiki-blogs_rss.php","lib/rss/rss-style.css",$foo["path"]);
  $home = httpPrefix().$foo1;
  $img = httpPrefix().$foo2;
  $read = httpPrefix().$foo3;
  $css = httpPrefix().$foo4;

  $title="Tiki RSS feed for weblogs";
  $desc="Last posts to weblogs.";
  $now = date("U");
  $changes = $bloglib->list_all_blog_posts(0,$max_rss_blogs,'created_desc', '',$now);

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

		</title>

		<link>
			<?php

			echo $home;

			?>

		</link>

		<description>
			<?php

			echo $desc;

			?>

		</description>

		<image rdf:about = "<?php echo $img; ?>"> <title><?php

		echo $title;

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

			// LOOP collecting last changes to blogs (index)
			foreach ($changes["data"] as $chg) {
				print ('        <rdf:li resource="' . $read . '?blogId=' . $chg["blogId"] . '" />' . "\n");
			}

			?>

			</rdf:Seq>
		</items>
	</channel>

	<?php

	// LOOP collecting last changes to blogs
	foreach ($changes["data"] as $chg) {
		print ('<item rdf:about="' . $read . '?blogId=' . $chg["blogId"] . '">' . "\n");

		print ('<title>' . $chg["blogtitle"] . ': ' . $tikilib->date_format(
			$tikilib->get_short_datetime_format(), $chg["created"]). '</title>' . "\n");
		print ('<link>' . $read . '?blogId=' . $chg["blogId"] . '</link>' . "\n");
		$data = $tikilib->date_format($tikilib->get_short_datetime_format(), $chg["created"]);
		print ('<description>' . htmlspecialchars($chg["data"]). '</description>' . "\n");
		print ('</item>' . "\n\n");
	}

	?>

</rdf:RDF>
