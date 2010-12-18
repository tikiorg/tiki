<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// Displays the data using the Tikiwiki odd/even table style
//
// Usage:
//	- The data is given one row per line, with columns
//   separated by |. (~|~ was used as a separator before 4.0 and is also accepted)
//	- Head rows separated by >> 
//  - In any cell, indicate number of columns to span with forward slashes at the beginning, number of rows to span with backslashes.	
//
// Example:
// {FANCYTABLE( head=" head r1c1 | head r1c2 | head r1c3>>head r2c1 | head r2c2 | head r2c3", headclass=xx, 
// 				headaligns= , headvaligns= , colwidths= , colaligns= , colvaligns= , sortable= , sortList= )}
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
		'description' => tra('Create a formatted table'),
		'prefs' => array('wikiplugin_fancytable'),
		'body' => tra('Rows separated by >> in the header; for the table body, one row per line. Cells separated by | in both cases.'),
		'params' => array(
			'head' => array(
				'required' => false,
				'name' => tra('Heading Row'),
				'description' => tra('Header rows of the table. Use >> to separate multiple rows.'),
				'default' => '',
			),
			'headclass' => array(
				'required' => false,
				'name' => tra('Heading CSS Class'),
				'description' => tra('CSS class to apply to the heading row.'),
				'default' => '',
			),
			'headaligns' => array(
				'required' => false,
				'name' => tra('Header Horizontal Align'),
				'description' => tra('Horizonatal alignments for header cells separated by |. Choices: left, right, center, justify.'),
				'default' => '',
			),
			'headvaligns' => array(
				'required' => false,
				'name' => tra('Header Vertical Align'),
				'description' => tra('Vertical alignments for header cells separated by |. Choices: top, middle, bottom, baseline.'),
				'default' => '',
			),
			'sortable' => array(
				'required' => false,
				'name' => tra('Column Sort'),
				'description' => tra('Indicate whether columns are sortable or not (not sortable by default)'),
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				),
			),
			'sortList' => array(
				'required' => false,
				'name' => tra('Pre-sorted Columns'),
				'description' => tra('Bracketed numbers for column number and sort direction (0 = ascending, 1 = descending), for example: [0,0],[1,0]'),
				'default' => '',
			),
			'colwidths' => array(
				'required' => false,
				'name' => tra('Column Widths'),
				'description' => tra('Column widths followed by px for pixels or % for percentages. Each column separated by |.'),
				'default' => '',
			),
			'colaligns' => array(
				'required' => false,
				'name' => tra('Cell Horizontal Align'),
				'description' => tra('Table body column horizonatal alignments separated by |. Choices: left, right, center, justify.'),
				'default' => '',
			),
			'colvaligns' => array(
				'required' => false,
				'name' => tra('Cell Vertical Align'),
				'description' => tra('Table body column vertical alignments separated by |. Choices: top, middle, bottom, baseline.'),
				'default' => '',
			),
		),
	);
}

function wikiplugin_fancytable($data, $params) {
	global $tikilib, $prefs;
	static $iFancytable = 0;
	++$iFancytable;
	//Patterns to keep | within external and internal links from being treated as column separators
	$patterns[0] = '/(\[[^\](~|~)]+)\~\|\~([^\[(~|~)]+\])/'; //for [ | ]
	$patterns[1] = '/(\(\([^(~|~)]+)\~\|\~([^(~|~)]+\)\))/'; // for (( | ))
	$patterns[2] = '/(\[[^\](~|~)]+)\~\|\~([^\[(~|~)]+)\~\|\~([^(~|~)]+\])/'; // for [ | | ]
	$patterns[3] = '/(\(\([^(~|~)]+)\~\|\~([^(~|~)]+)\~\|\~([^(~|~)]+\)\))/'; // for (( | | ))
	$replace[0] = '$1|$2';
	$replace[1] = '$1|$2';
	$replace[2] = '$1|$2|$3';
	$replace[3] = '$1|$2|$3';
	extract ($params,EXTR_SKIP);
	if (empty($sortable)) $sortable = 'n';
	$tdend = '</td>';
	$trbeg = "\r\t\t<tr>";
	$trend = "\r\t\t</tr>";

	// Start the table
	$wret = '<table class="normal'.($sortable=='y'? ' fancysort':'').'" id="fancytable_'.$iFancytable.'">' . "\r\t";
	
	//header rows
  	if (isset($head)) {
  		//Although user can set | as column separators, program uses only ~|~
		//If | is being used, first replace all | with ~|~, then revert back to | for those (up to 2) inside links
		if (strpos($head, '~|~') == FALSE) {
			$head = str_replace('|', '~|~', $head);
			$head = preg_replace($patterns, $replace , $head);	
		}	
		if (!empty($headclass)) {
			$tdhdr = "\r\t\t\t<th class=\"$headclass\"";
		} else {
			$tdhdr = "\r\t\t\t<th";
		}
		$colsw = isset($colwidths) ?  explode('|', $colwidths) : '';
		$haligns = isset($headaligns) ?  explode('|', $headaligns) : '';
		$hvaligns = isset($headvaligns)?  explode('|', $headvaligns) : '';
		$hlines = explode('>>', $head);
		$rowheads = process_lines($hlines, '~|~', 'h', $tdhdr, '</th>', $colsw, $haligns, $hvaligns);
		$wret .= '<thead>' . $rowheads . "\r\t" . '</thead>' . "\r\t" . '<tbody>' ;
	} 
	
	//table body rows
	//Although user can set | as column separators, program uses only ~|~
	//If | is being used, first replace all | with ~|~, then revert back to | for those (up to 2) inside links
	if (strpos($data, '~|~') == FALSE) {
		$data = str_replace('|', '~|~', $data);
		$data = preg_replace($patterns, $replace , $data);
	}	
	$lines = explode("\n", $data);
	$colsw = isset($colwidths) ?  explode('|', $colwidths) : '';
	$caligns = isset($colaligns) ?  explode('|', $colaligns) : '';
	$cvaligns = isset($colvaligns)?  explode('|', $colvaligns) : '';
	$wret .= process_lines($lines, '~|~', 'r', '', '</td>', $colsw, $caligns, $cvaligns);

	// End the table
	if (isset($head)) {
		$wret .= "\r\t" . '</tbody>';
	}
	$wret .= "\r" . '</table>' . "\r";
	if ($sortable == 'y' && $prefs['javascript_enabled'] == 'y') {
		if ($prefs['feature_jquery_tablesorter'] != 'y') {
			$wret .= tra('The feature must be activated:').' feature_jquery_tablesorter';
		}
		if (empty($sortList)) {
			$js = '$("#fancytable_'.$iFancytable.'").tablesorter({widgets: ["zebra"]});';
		} else {
			$js = '$("#fancytable_'.$iFancytable.'").tablesorter({sortList:['.$sortList.'], widgets: ["zebra"]});';
		}
		global $headerlib;
		$headerlib->add_jq_onready($js);
	}
	return $wret;
}

//Header and body rows are processed with this function
 function process_lines($lines, $separator, $type, $cellbeg, $cellend, $widths, $aligns, $valigns) {
	$trbeg = "\r\t\t<tr>";
	$trend = "\r\t\t</tr>";
	$l = 0;
	$keepc = '';
	$keepl = '';
	$rnum = '';
	$rnum1 = '';
	$rnum2 = '';
	$wret = '';
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
			$c = 0;
			$row = '';
			$parts = explode($separator, $line);
			//Each column within a row
			foreach ($parts as $column) {
				$colnum = 'col' . $c;				
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
						if ($matches[1][0]) $rnum1 = substr_count($matches[0][0], $matches[1][0]);
						if ($matches[3][0]) $rnum2 = substr_count($matches[0][0], $matches[3][0]);
						$rnum = $rnum1+ $rnum2;
						$rowspan = ' rowspan="' . $rnum . '"';
						//If there's another rowspan still in force, bump up the column number
						if (isset(${$colnum}['col']) && ${$colnum}['col'] == $c) {
							if ((${$colnum}['span'] - ($l - ${$colnum}['line'])) > 0) $c++;
						}
						//Note the info for this new rowspan
						${$colnum}['col'] = $c;
						${$colnum}['line'] = $l;
						${$colnum}['span'] = $rnum;
					}
				}
				if (isset($widths) || isset($aligns) || isset($valigns)) {
					//If there's another rowspan still in force, bump up the column number
					if (isset(${$colnum}['col']) && ${$colnum}['col'] == $c && ($l > ${$colnum}['line'])) {
						if ((${$colnum}['span'] - ($l - ${$colnum}['line'])) > 0) $c++;
					}
					$colstyle = ' style="';
					$colstyle .= !empty($widths) ? ' width: ' . $widths[$c] . ';' : '';
					$colstyle .= !empty($aligns) ? ' text-align: ' . $aligns[$c] . ';' : '';
					$colstyle .= !empty($valigns) ? ' vertical-align: ' . $valigns[$c] : '';
					$colstyle .= '"';
					$c++;   //increment column number
				}
				$row .= $cellbeg . $colspan . $rowspan . $colstyle . '>' . $column . $cellend;
			}
			$wret .= $trbeg . $row . $trend;
		}
		$l++;   //increment row number
	}
	return $wret;
}  
