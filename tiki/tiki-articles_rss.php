<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-articles_rss.php,v 1.13 2003-08-21 00:51:19 redflo Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once ('tiki-setup.php');

require_once ('lib/tikilib.php'); # httpScheme()

  if($rss_articles != 'y') {
   die;
  }

  if($tiki_p_read_article != 'y') {
    $smarty->assign('msg',tra("Permission denied you cannot view this section"));
    $smarty->display("styles/$style_base/error.tpl");
    die;  
  }
  
  header("content-type: text/xml");
  $foo = parse_url($_SERVER["REQUEST_URI"]);
  $foo1=str_replace("tiki-articles_rss.php",$tikiIndex,$foo["path"]);
  $foo2=str_replace("tiki-articles_rss.php","img/tiki.jpg",$foo["path"]);
  $foo3=str_replace("tiki-articles_rss.php","tiki-read_article.php",$foo["path"]);
  $foo4=str_replace("tiki-articles_rss.php","lib/rss/rss-style.css",$foo["path"]);
  $home = httpPrefix().$foo1;
  $img = httpPrefix().$foo2;
  $read = httpPrefix().$foo3;
  $css = httpPrefix().$foo4;

  $title = "Tiki RSS feed for articles";
  $desc = "Last articles.";
  $now = date("U");
  $changes = $tikilib->list_articles(0,$max_rss_articles,'publishDate_desc', '', $now,$user);

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

			// LOOP collecting last changes to the articles
			foreach ($changes["data"] as $chg) {
				print ('        <rdf:li resource="' . htmlspecialchars(
					$read). '?articleId=' . htmlspecialchars($chg["articleId"]). '" />' . "\n");
			}

			?>

			</rdf:Seq>
		</items>
	</channel>

	<?php

	// LOOP collecting last changes to the articles
	foreach ($changes["data"] as $chg) {
		print ('<item rdf:about="' . htmlspecialchars($read). '?articleId=' . htmlspecialchars($chg["articleId"]). '">' . "\n");

		print ('<title>' . htmlspecialchars($chg["title"]). '</title>' . "\n");
		print ('<link>' . $read . '?articleId=' . $chg["articleId"] . '</link>' . "\n");
		$data = $tikilib->date_format($tikilib->get_short_datetime_format(), $chg["publishDate"]);
		print ('<description>' . htmlspecialchars($chg["heading"]). '</description>' . "\n");
		print ('</item>' . "\n\n");
	}

	?>

</rdf:RDF>
