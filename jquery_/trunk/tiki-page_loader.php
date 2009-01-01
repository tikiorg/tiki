<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-page_loader.php,v 1.12.2.1 2008-03-01 17:12:48 lphuberdeau Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
include_once ('tiki-setup.php');

if ($prefs['feature_html_pages'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_html_pages");

	$smarty->display("error.tpl");
	die;
}

include_once ('lib/htmlpages/htmlpageslib.php');

$refresh = 1000 * $_REQUEST["refresh"];

?>

<html>
	<head>
		<script language = 'Javascript' type = 'text/javascript'>

		<?php

		$zones = $htmlpageslib->list_html_page_content($_REQUEST["pageName"], 0, -1, 'zone_asc', '');
		$cmds = array();

		$temp_max = count($zones["data"]);
		for ($i = 0; $i < $temp_max; $i++) {
			$cmd = 'top.document.getElementById("' . $zones["data"][$i]["zone"] . '").innerHTML="' . $zones["data"][$i]["content"] . '";';

			echo $cmd;
		}

		?>

		</script>
	</head>

	<body onload = "window.setInterval('location.reload()','<?php echo $refresh ?>');">
		<?php

		//print_r($cmds);

		?>

	</body>
</html>
