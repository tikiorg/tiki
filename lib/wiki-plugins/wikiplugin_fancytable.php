<?php

// Displays the data using the Tikiwiki odd/even table style
//
// Parameters:
//   head -- the column header row
//   headclass -- css class to apply on head row
//
// Usage:
//   - The data (and the head paramter) is given one row per line, with columns
//   separated by |. (~|~ was used as a separator before 4.0 and is also accepted)
//   - In any cell, indicate number of columns to span with forward slashes at the beginning, number of rows to span with backslashes.	
//
// Example:
// {FANCYTABLE( head=" header column 1 | header column 2 | header column 3", headclass=xx, headaligns= , headvaligns= , colwidths= , colaligns= , colvaligns= )}
// row 1 column 1 | row 1 column 2 | row 1 column 3
// row 2 column 1 | row 2 column 2 | row 2 column 3
// {FANCYTABLE}
function wikiplugin_fancytable_help() {
	return tra("Displays the data using the Tikiwiki odd/even table style").":<br />~np~{FANCYTABLE(head=>,headclass=>)}".tra("cells")."{FANCYTABLE}~/np~ - ''".tra("heads and cells separated by |")."''";
}

function wikiplugin_fancytable_info() {
	return array(
		'name' => tra('Fancy Table'),
		'documentation' => 'PluginFancyTable',
		'description' => tra("Displays the data using the Tikiwiki odd/even table style"),
		'prefs' => array('wikiplugin_fancytable'),
		'body' => tra('One row per line, cells separated by |.'),
		'params' => array(
			'head' => array(
				'required' => false,
				'name' => tra('Heading row'),
				'description' => tra('Header rows of the table. Use >> to separate multiple rows.'),
			),
			'headclass' => array(
				'required' => false,
				'name' => tra('Heading CSS class'),
				'description' => tra('CSS class to apply to the heading row.'),
			),
			'headaligns' => array(
				'required' => false,
				'name' => tra('Header horizontal align'),
				'description' => tra('Horizonatal alignments for header cells separated by |. Choices: left, right, center, justify.'),
			),
			'headvaligns' => array(
				'required' => false,
				'name' => tra('Header vertical align'),
				'description' => tra('Vertical alignments for header cells separated by |. Choices: top, middle, bottom, baseline.'),
			),
			'sortable' => array(
				'required' => false,
				'name' => tra('Column sort'),
				'description' => 'Indicate whether columns are sortable or not.',
				'options' => array(
					array('text' => tra('No'), 'value' => 'n'), 
					array('text' => tra('Yes'), 'value' => 'y'), 
				),
			),
			'sortList' => array(
				'required' => false,
				'name' => tra('Pre-sorted columns'),
				'description' => tra('Bracketed numbers for column number and sort direction (0 = ascending, 1 = descending), for example: [0,0],[1,0]'),
			),
			'colwidths' => array(
				'required' => false,
				'name' => tra('Column widths'),
				'description' => tra('Column widths in pixels or percentages separated by |.'),
			),
			'colaligns' => array(
				'required' => false,
				'name' => tra('Cell horizontal align'),
				'description' => tra('Table body column horizonatal alignments separated by |. Choices: left, right, center, justify.'),
			),
			'colvaligns' => array(
				'required' => false,
				'name' => tra('Cell vertical align'),
				'description' => tra('Table body column vertical alignments separated by |. Choices: top, middle, bottom, baseline.'),
			),
		),
	);
}

function wikiplugin_fancytable($data, $params) {
	global $tikilib, $prefs;
	static $iFancytable = 0;
	++$iFancytable;
	extract ($params,EXTR_SKIP);
	if (empty($sortable)) $sortable = 'n';
	$tdend = '</td>';
	$trbeg = "\r\t\t<tr>";
	$trend = "\r\t\t</tr>";

	// Start the table
	$wret = '<table class="normal'.($sortable=='y'? ' fancysort':'').'" id="fancytable_'.$iFancytable.'">' . "\r\t";
	
	//header rows
  	if (isset($head)) {
		if (strpos($head, '~|~') !== FALSE) {
			$separator = '~|~'; 
		} elseif (strpos($head, '|') !== FALSE) {
			$separator = '|';
		}
		if (isset($headclass)) {
			if (strpos($headclass,'"')) {
				$headclass = str_replace('"',"'",$class);
				$tdhdr = "\r\t\t\t<th $headclass\"";
			} 
		} else {
			$tdhdr = "\r\t\t\t<th";
		}
		if (isset($colwidths) || isset($headaligns) || isset($headvaligns)) {
			$colsw = isset($colwidths) ?  explode('|', $colwidths) : '';
			$haligns = isset($headaligns) ?  explode('|', $headaligns) : '';
			$hvaligns = isset($headvaligns)?  explode('|', $headvaligns) : '';
		}
		$hlines = explode('>>', $head);
		$rowheads = process_lines($hlines, $separator, 'h', $tdhdr, '</th>', $colsw, $haligns, $hvaligns);
		$wret .= '<thead>' . $rowheads . "\r\t" . '</thead>' . "\r\t" . '<tbody>' ;
	} 
	
	//table body rows
	$lines = split("\n", $data);
	if (strpos($data, '~|~') !== FALSE) {
		$separator = '~|~'; 
	} elseif (strpos($data, '|') !== FALSE) {
		$separator = '|';
	}

	if (isset($colwidths) || isset($colaligns) || isset($colvaligns)) {
		$colsw = isset($colwidths) ?  explode('|', $colwidths) : '';
		$caligns = isset($colaligns) ?  explode('|', $colaligns) : '';
		$cvaligns = isset($colvaligns)?  explode('|', $colvaligns) : '';
	}
	$wret .= process_lines($lines, $separator, 'r', '', '</td>', $colsw, $caligns, $cvaligns);

	// End the table
	if (isset($head)) {
		$wret .= "\r\t" . '</tbody>';
	}
	$wret .= "\r" . '</table>' . "\r";
	if ($sortable == 'y' && $prefs['javascript_enabled'] == 'y') {
		if (empty($sortList)) {
			$wret .= '{JQ(notonready=false)}$jq("#fancytable_'.$iFancytable.'").tablesorter();{JQ}';
		} else {
			$wret .= '{JQ(notonready=false)}$jq("#fancytable_'.$iFancytable.'").tablesorter({sortList:['.$sortList.']});{JQ}';
		}
	}
	return $wret;
}

//Header and body rows are processed with this function
 function process_lines($lines, $separator, $type, $cellbeg, $cellend, $widths, $aligns, $valigns) {
	$trbeg = "\r\t\t<tr>";
	$trend = "\r\t\t</tr>";
	$l = 0;
	$keepi = '';
	$keepl = '';
	$rnum = '';
	$wret = '';
	if ($type == 'h') $h = 1; //note which are header rows
	$row_is_odd = true;
	//Each row
	foreach ($lines as $line) {
		$line = trim($line);
		if (strlen($line) > 0) {
			if ($type == 'r') {
				if ($row_is_odd) {
					$cellbeg = "\r\t\t\t" . '<td class="odd"';
					$row_is_odd = false;
				} else {
					$cellbeg = "\r\t\t\t" . '<td class="even"';
					$row_is_odd = true;
				}
			}	
			$i = 0;
			$row = '';
			$parts = explode($separator, $line);
			//Each column within a row
			foreach ($parts as $column) {
				$colspan = '';
				$rowspan = '';
				/*Match / (colspan) or \ (rowspan) characters in whichever order at the beginning of the cell
				$matches[0][0] shows entire match. There are 3 strings being matched in the preg_match_all, so $matches[1][0] shows the character
				matched for the first string (\), $matches[2][0] the second character (/), and $matches[3][0] the third character (\) */
				if (preg_match_all("/^(\\\\)*(\/)*(\\\\)*/", $column, $matches)) {
					$column = substr($column, strlen($matches[0][0]));
					//create colspan if there are / characters at beginning of cell
					if ($matches[2][0]) {
						$colspan = ' colspan="' . substr_count($matches[0][0], $matches[2][0]) . '"';
					}
					//create rowspan if there are \ characters at beginning of cell
					if ($matches[1][0] || $matches[3][0]) {
						if (!empty($matches[1][0])) $rnum = substr_count($matches[0][0], $matches[1][0]);
						if (!empty($matches[3][0])) $rnum .= substr_count($matches[0][0], $matches[3][0]);
						$rowspan = ' rowspan="' . $rnum . '"';
						//Note the row and column number when rowspan is set so subsequent rows and columns can be processed properly
						$keepi = $i;
						$keepl = $l;
					}
				}
				if (isset($widths) || isset($aligns) || isset($valigns)) {
					if (($i == $keepi) && ($rnum > 0) && ($l > $keepl)) $i++;	//skip column number is still under rowspan
					$colstyle = ' style="';
					$colstyle .= !empty($widths) ? ' width: ' . $widths[$i] . ';' : '';
					$colstyle .= !empty($aligns) ? ' text-align: ' . $aligns[$i] . ';' : '';
					$colstyle .= !empty($valigns) ? ' vertical-align: ' . $valigns[$i] : '';
					$colstyle .= '"';
					$i++;   //increment column number
				}
				$row .= $cellbeg . $colspan . $rowspan . $colstyle . '>' . $column . $cellend;
			}
			$wret .= $trbeg . $row . $trend;
		}
		$l++;   //increment row number
		$rnum--;  //decrement to represent how many rowspan rows are left
	}
	return $wret;
}  