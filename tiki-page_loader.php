<?php

include_once ('tiki-setup.php');

include_once ('lib/htmlpages/htmlpageslib.php');

$refresh = 1000 * $_REQUEST["refresh"];

?>

<html>
	<head>
		<script language = 'Javascript' type = 'text/javascript'>

		<?php

		$zones = $htmlpageslib->list_html_page_content($_REQUEST["pageName"], 0, -1, 'zone_asc', '');
		$cmds = array();

		for ($i = 0; $i < count($zones["data"]); $i++) {
			$cmd = 'top.document.getElementById("' . $zones["data"][$i]["zone"] . '").innerHTML="' . $zones["data"][$i]["content"] . '";';

			echo $cmd;
		}

		?>

		</script>
	</head>

	<body onLoad="window.setInterval('location.reload()','<?php echo $refresh ?>');">
	<?php
		//print_r($cmds);
	?>
	</body>
</html>