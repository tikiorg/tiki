<?php

function wikiplugin_split_help() {
	return tra("Split a page into columns").":<br />~np~{SPLIT()}".tra("column")."---".tra("column")."---".tra("column")."{SPLIT}~/np~";
}
function wikiplugin_split($data, $params) {
	global $tikilib;

	global $replacement;

	if (substr($data, 0, 1) == "\n") {
		$data = substr($data, 1);
	}

	extract ($params);
	$result = "<table border='0' width='100%'><tr>";
	$sections = preg_split("/---+/\n", $data);
	$count = count($sections);
	$columnSize = floor(100 / $count);

	for ($i = 0; $i < $count; $i++) {
		$result .= "<td valign='top' width='" . $columnSize . "%'>\n";

		if (substr($sections[$i], 0, 1) == "\n")
			$sections[$i] = substr($sections[$i], 1);
		$sections[$i] = preg_replace("/([\r\n])/","<br />",$sections[$i]);
		$result .= $sections[$i];
		$result .= "\n</td>";
	}

	$result .= "</tr></table>";

	return $result;
}

?>
