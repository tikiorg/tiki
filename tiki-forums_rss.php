<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-forums_rss.php,v 1.10 2003-08-07 04:33:57 rossta Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once ('tiki-setup.php');

require_once ('lib/tikilib.php');

if ($rss_forums != 'y') {
	die;
}

header ("content-type: text/xml");
$foo = parse_url($_SERVER["REQUEST_URI"]);
$foo1 = str_replace("tiki-forums_rss.php", $tikiIndex, $foo["path"]);
$foo2 = str_replace("tiki-forums_rss.php", "img/tiki.jpg", $foo["path"]);
$foo3 = str_replace("tiki-forums_rss", "tiki-view_forum_thread", $foo["path"]);
$foo4 = str_replace("tiki-forums_rss.php", "lib/rss/rss-style.css", $foo["path"]);
$home = httpPrefix(). $foo1;
$img = httpPrefix(). $foo2;
$read = httpPrefix(). $foo3;
$css = httpPrefix(). $foo4;

$now = date("U");
$changes = $tikilib->list_all_forum_topics(0, $max_rss_forums, 'commentDate_desc', '');
$title = "Tiki RSS feed for forums";
$desc = "Last topics in forums.";

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

			echo $home

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

			// LOOP collecting last topics in forums (index)
			foreach ($changes["data"] as $chg) {
				print ('        <rdf:li resource="' . $read . '?forumId=' . $chg["threadId"] . '" />' . "\n");
			}

			?>

			</rdf:Seq>
		</items>
	</channel>

	<?php

	// LOOP collecting last topics in forums
	foreach ($changes["data"] as $chg) {
		print ('<item rdf:about="' . $read . '?forumId=' . $chg["threadId"] . '">' . "\n");

		print ('<title>' . htmlspecialchars($chg["title"]). ': ' . $tikilib->date_format(
			$tikilib->get_short_datetime_format(), $chg["commentDate"]). '</title>' . "\n");
		print ('<link>' . $read . '?forumId=' . $chg["threadId"] . '</link>' . "\n");
		$data = $tikilib->date_format($tikilib->get_short_datetime_format(), $chg["commentDate"]);
		print ('<description>' . htmlspecialchars($chg["data"]). '</description>' . "\n");
		print ('</item>' . "\n\n");
	}

	?>

</rdf:RDF>