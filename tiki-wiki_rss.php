<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-wiki_rss.php,v 1.13 2003-08-07 04:33:57 rossta Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once ('tiki-setup.php');

require_once ('lib/tikilib.php'); # httpScheme()
include_once ('lib/wiki/histlib.php');

if ($rss_wiki != 'y') {
	die;
}

header ("content-type: text/xml");
$foo = parse_url($_SERVER["REQUEST_URI"]);
$foo1 = str_replace("tiki-wiki_rss.php", "tiki-index.php", $foo["path"]);
$foo2 = str_replace("tiki-wiki_rss.php", "img/tiki.jpg", $foo["path"]);
$foo3 = str_replace("tiki-wiki_rss.php", "lib/rss/rss-style.css", $foo["path"]);
$home = httpPrefix(). $foo1;
$img = httpPrefix(). $foo2;
$css = httpPrefix(). $foo3;
$title = $tikilib->get_preference("title", "Tiki RSS feed for the wiki pages");
$title = "Tiki RSS feed for the wiki pages";
$desc = "Last modifications to the Wiki.";
$changes = $histlib->get_last_changes(999, 0, $max_rss_wiki, $sort_mode = 'lastModif_desc');

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

		<image rdf:about = "<?php echo $img; ?>" rdf:url = "<?php echo $img; ?>"> <title><?php

		echo $title;

		?>

		</title>

		<link>
			<?php

			echo $home

			?>

		</link>

		<url>
			<?php

			echo $home

			?>

		</url> </image>

		<items>
			<rdf:Seq>
				<?php

			// LOOP collecting last changes to the wiki pages (index)
			foreach ($changes["data"] as $chg) {
				print ('        <rdf:li resource="' . $home . '?page=' . $chg["pageName"] . '" />' . "\n");
			}

			?>

			</rdf:Seq>
		</items>
	</channel>

	<?php

	// LOOP collecting last changes to the wiki pages
	foreach ($changes["data"] as $chg) {
		print ('<item rdf:about="' . $home . '?page=' . $chg["pageName"] . '">' . "\n");

		print ('  <title>' . $chg["pageName"] . ' ' . $chg["action"] . '</title>' . "\n");
		print ('  <link>' . $home . '?page=' . $chg["pageName"] . '</link>' . "\n");
		$data = $tikilib->date_format($tikilib->get_short_datetime_format(), $chg["lastModif"]);

		if (!empty($chg["comment"])) {
			$comment = "(" . htmlspecialchars($chg["comment"]). ")";
		} else {
			$comment = '';
		}

		print ('  <description>' . "[$data] :" . $chg["action"] . " " . htmlspecialchars(
			$chg["pageName"]). $comment . '</description>' . "\n");
		print ('</item>' . "\n\n");
	}

	?>

</rdf:RDF>