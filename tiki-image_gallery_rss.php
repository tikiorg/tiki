<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-image_gallery_rss.php,v 1.11 2003-08-07 04:33:57 rossta Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once ('tiki-setup.php');

require_once ('lib/tikilib.php'); # httpScheme()
include_once ("lib/imagegals/imagegallib.php");

if ($rss_image_gallery != 'y') {
	die;
}

if (!isset($_REQUEST["galleryId"])) {
	die;
}

header ("content-type: text/xml");
$foo = parse_url($_SERVER["REQUEST_URI"]);

$foo1 = str_replace("tiki-image_gallery_rss.php", $tikiIndex, $foo["path"]);
$foo2 = str_replace("tiki-image_gallery_rss.php", "img/tiki.jpg", $foo["path"]);
$foo3 = str_replace("tiki-image_gallery_rss", "tiki-browse_image", $foo["path"]);
$foo4 = str_replace("tiki-image_gallery_rss.php", "lib/rss/rss-style.css", $foo["path"]);
$home = httpPrefix(). $foo1;
$img = httpPrefix(). $foo2;
$read = httpPrefix(). $foo3;
$css = httpPrefix(). $foo4;

$now = date("U");
$changes = $imagegallib->get_images(0, $max_rss_image_gallery, 'created_desc', '', $_REQUEST["galleryId"]);
$info = $imagegallib->get_gallery($_REQUEST["galleryId"]);
$galleryname = $info["name"];
$gallerydesc = $info["description"];
$title = "Tiki RSS feed for the image gallery:";
$desc = "...";

print '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
print '<?xml-stylesheet href="' . $css . '" type="text/css"?>' . "\n";

?>

<rdf:RDF xmlns:dc = "http://purl.org/dc/elements/1.1/"
	xmlns:h = "http://www.w3.org/1999/xhtml" xmlns:rdf = "http://www.w3.org/1999/02/22-rdf-syntax-ns#"
	xmlns = "http://purl.org/rss/1.0/">
	<channel rdf:about = "<?php echo $home; ?>">
		<title></title>

		<title><?php

		echo $title;

		?>

		<?php

		echo htmlspecialchars($galleryname);

		?>

		</title>

		<link>
			<?php

			echo $home

			?>

		</link>

		<description>
			<?php

			echo htmlspecialchars($gallerydesc);

			?>

		</description>

		<image rdf:about = "<?php echo $img; ?>"> <title><?php

		echo $title;

		?>

		<?php

		echo htmlspecialchars($galleryname);

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

			// LOOP collecting last changes to image galleries (index)
			foreach ($changes["data"] as $chg) {
				print ('        <rdf:li resource="' . $read . '?imageId=' . $chg["imageId"] . '" />' . "\n");
			}

			?>

			</rdf:Seq>
		</items>
	</channel>

	<?php

	// LOOP collecting last changes to image galleries
	foreach ($changes["data"] as $chg) {
		print ('<item rdf:about="' . $read . '?imageId=' . $chg["imageId"] . '">' . "\n");

		print ('<title>' . $chg["name"] . '</title>' . "\n");
		print ('<link>' . $read . '?imageId=' . $chg["imageId"] . '</link>' . "\n");
		$data = $tikilib->date_format($tikilib->get_short_datetime_format(), $chg["created"]);
		print ('<description>' . htmlspecialchars($chg["description"]). '</description>' . "\n");
		print ('</item>' . "\n\n");
	}

	?>

</rdf:RDF>