<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

include_once ('tiki-setup.php');
$access->check_feature('feature_html_pages');
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

	<body onload = "window.setInterval('location.reload()','<?php
echo $refresh ?>');">

	</body>
</html>
