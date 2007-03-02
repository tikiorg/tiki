<?php

// Displays the data using the Tikiwiki odd/even table style
//
// Parameters:
//   head -- the column header row
//   headclass -- css class to apply on head row
//
// Usage:
//   The data (and the head paramter) is given one row per line, with columns
//   separated by ~|~.
//
// Example:
// {FANCYTABLE( head => header column 1 ~|~ header column 2 ~|~ header column 3, headclass=>xx )}
// row 1 column 1 ~|~ row 1 column 2 ~|~ row 1 column 3
// row 2 column 1 ~|~ row 2 column 2 ~|~ row 2 column 3
// {FANCYTABLE}
function wikiplugin_fancytable_help() {
	return tra("Displays the data using the Tikiwiki odd/even table style").":<br />~np~{FANCYTABLE(head=>,headclass=>)}".tra("cells")."{FANCYTABLE}~/np~ - ''".tra("heads and cells separated by ~|~")."''";
}

function wikiplugin_fancytable($data, $params) {
	global $tikilib;

	// Start the table
	$wret = "<table class=\"normal\">";

	$tdend = "</td>";
	$trbeg = "<tr>";
	$trend = "</tr>";

	// Parse the parameters
	extract ($params,EXTR_SKIP);

	if (isset($headclass)) {
		if (strpos($headclass,'"')) $headclass = str_replace('"',"'",$class);
		$tdhdr = "<td class=\"heading $headclass\">";
	} else {
		$tdhdr = "<td class=\"heading\">";
	}

	if (isset($head)) {
		$parts = explode("~|~", $head);

		$row = "";

		foreach ($parts as $column) {
			$row .= $tdhdr . $column . $tdend;
		}

		$wret .= $trbeg . $row . $trend;
	}

	// Each line of the data is a row, the first line is the header
	$row_is_odd = true;
	$lines = split("\n", $data);

	foreach ($lines as $line) {
		$line = trim($line);

		if (strlen($line) > 0) {
			if ($row_is_odd) {
				$tdbeg = "<td class=\"odd\">";

				$row_is_odd = false;
			} else {
				$tdbeg = "<td class=\"even\">";

				$row_is_odd = true;
			}

			$parts = explode("~|~", $line);
			$row = "";

			foreach ($parts as $column) {
				if (strcmp(trim($column), "~blank~") == 0) {
					$row .= $tdbeg . "&nbsp;" . $tdend;
				} else {
					$row .= $tdbeg . $column . $tdend;
				}
			}

			$wret .= $trbeg . $row . $trend;
		}
	}

	// End the table
	$wret .= "</table>";

	return $wret;
}

?>
