<?php

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once ('tiki-setup.php');

require_once ('lib/tikilib.php'); # httpScheme()

if($tiki_p_map_view != 'y') {
  $smarty->assign('msg',tra("You do not have permissions to view the maps"));
  $smarty->display("styles/$style_base/error.tpl");
  die;
}

  header("content-type: text/xml");
  $foo = parse_url($_SERVER["REQUEST_URI"]);
  $foo1=str_replace("tiki-map_rss.php",$tikiIndex,$foo["path"]);
  $foo2=str_replace("tiki-map_rss.php","img/tiki.jpg",$foo["path"]);
  $foo3=str_replace("tiki-map_rss.php","tiki-map.phtml",$foo["path"]);
  $foo4=str_replace("tiki-map_rss.php","lib/rss/rss-style.css",$foo["path"]);
  $home = httpPrefix().$foo1;
  $img = httpPrefix().$foo2;
  $read = httpPrefix().$foo3;
  $css = httpPrefix().$foo4;
  
  $title = $tikilib->get_preference("title","Tiki RSS feed for maps");
  $title = "Tiki RSS feed for maps";
  $desc = "List of maps available";
  $now = date("U");

// Get mapfiles from the mapfiles directory
$files = array();
$h = opendir($map_path);

while (($file = readdir($h)) !== false) {
	if (preg_match('/\.map$/i', $file)) {
		$files[] = $file;
	}
}

closedir ($h);

sort ($files);	

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

			// LOOP collecting last changes to file galleries (index)
			foreach ($files as $file) {
				print ('        <rdf:li resource="' . $read . '?mapfile=' . $file . '" />' . "\n");
			}

			?>

			</rdf:Seq>
		</items>
	</channel>

	<?php

	// LOOP collecting last changes to file galleries
	foreach ($files as $file) {
		print ('<item rdf:about="' . $read . '?mapfile=' . $file . '">' . "\n");

		print ('<title>' . htmlspecialchars($file). '</title>' . "\n");
		print ('<link>' . $read . '?mapfile=' . $file . '</link>' . "\n");
		$data = $tikilib->date_format($tikilib->get_short_datetime_format(), filemtime($map_path.$file));
		print ('<description>' . htmlspecialchars($data). '</description>' . "\n");
		print ('</item>' . "\n\n");
	}

	?>

</rdf:RDF>
