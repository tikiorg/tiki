<?php

// Displays the data using the Tikiwiki odd/even table style
//
// Parameters:
//   head -- the column header row
//   headclass -- css class to apply on head row
//
// Usage:
//   - The data (and the head paramter) is given one row per line, with columns
//   separated by | or ~|~.
//   - In any cell, indicate number of columns to span with forward slashes at the beginning, number of rows to span with backslashes.	
//
// Example:
// {FANCYTABLE( head=" header column 1 ~|~ header column 2 ~|~ header column 3", headclass=xx )}
// row 1 column 1 ~|~ row 1 column 2 ~|~ row 1 column 3
// row 2 column 1 ~|~ row 2 column 2 ~|~ row 2 column 3
// {FANCYTABLE}
function wikiplugin_fancytable_help() {
	return tra("Displays the data using the Tikiwiki odd/even table style").":<br />~np~{FANCYTABLE(head=>,headclass=>)}".tra("cells")."{FANCYTABLE}~/np~ - ''".tra("heads and cells separated by ~|~")."''";
}

function wikiplugin_fancytable_info() {
	return array(
		'name' => tra('Fancy Table'),
		'documentation' => 'PluginFancyTable',
		'description' => tra("Displays the data using the Tikiwiki odd/even table style"),
		'prefs' => array('wikiplugin_fancytable'),
		'body' => tra('One row per line, cells separated by | or ~|~.'),
		'params' => array(
			'head' => array(
				'required' => false,
				'name' => tra('Heading row'),
				'description' => tra('Heading row of the table, same format as the body section.'),
			),
			'headclass' => array(
				'required' => false,
				'name' => tra('Heading CSS Class'),
				'description' => tra('CSS Class to apply on the heading row.'),
			),
		),
	);
}

function wikiplugin_fancytable($data, $params) {
	global $tikilib;

	// Start the table
	$wret = '<table class="normal">';

	$tdend = '</td>';
	$trbeg = '<tr>';
	$trend = '</tr>';

	// Parse the parameters
	extract ($params,EXTR_SKIP);

	if (isset($headclass)) {
		if (strpos($headclass,'"')) $headclass = str_replace('"',"'",$class);
		$tdhdr = "<td class=\"heading $headclass\"";
	} else {
		$tdhdr = '<td class="heading"';
	}

	if (isset($head)) {
		if (strpos($head, '~|~') !== FALSE) {
			$separator = '~|~'; 
		} elseif (strpos($head, '|') !== FALSE) {
			$separator = '|';
		}
		
		$parts = explode($separator, $head);

		$row = '';

		foreach ($parts as $column) {
			$tdhdrmid = '';
			if (preg_match_all("/^[\/]+/", $column, $matches)) {
				$tdhdrmid = ' colspan="' . strlen($matches[0][0]) . '">';
				$column = substr($column, strlen($matches[0][0]));
			} elseif (preg_match_all("/^[\\\\]+/", $column, $matches)) {
				$tdhdrmid = ' rowspan="' . strlen($matches[0][0]) . '">';
				$column = substr($column, strlen($matches[0][0]));
			} else {
				$tdhdrmid = '>';
			}
			$row .= $tdhdr . $tdhdrmid . $column . $tdend;
		}

		$wret .= $trbeg . $row . $trend;
	}

	// Each line of the data is a row, the first line is the header
	$row_is_odd = true;
	$lines = split("\n", $data);
	if (strpos($data, '~|~') !== FALSE) {
		$separator = '~|~'; 
	} elseif (strpos($data, '|') !== FALSE) {
		$separator = '|';
	}

	foreach ($lines as $line) {
		$line = trim($line);

		if (strlen($line) > 0) {
			if ($row_is_odd) {
				$tdbeg = '<td class="odd"';

				$row_is_odd = false;
			} else {
				$tdbeg = '<td class="even"';

				$row_is_odd = true;
			}
			
			$parts = explode($separator, $line);
			$row = '';

			foreach ($parts as $column) {
				$tdmid = '';
				if (preg_match_all("/^[\/]+/", $column, $matches)) {
					$tdmid = ' colspan="' . strlen($matches[0][0]) . '">';
					$column = substr($column, strlen($matches[0][0]));
				} elseif (preg_match_all("/^[\\\\]+/", $column, $matches)) {
					$tdmid = ' rowspan="' . strlen($matches[0][0]) . '">';
					$column = substr($column, strlen($matches[0][0]));
				} else {
					$tdmid = '>';
				}
				
				if (strcmp(trim($column), '~blank~') == 0) {
					$row .= $tdbeg . '&nbsp;' . $tdend;
				} else {
					$row .= $tdbeg . $tdmid . $column . $tdend;
				}
			}

			$wret .= $trbeg . $row . $trend;
		}
	}

	// End the table
	$wret .= '</table>';

	return $wret;
}