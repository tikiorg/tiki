<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_fancytable_info() {
	return array(
		'name' => tra('Fancy Table'),
		'documentation' => 'PluginFancyTable',
		'description' => tra('Create a formatted table'),
		'prefs' => array('wikiplugin_fancytable'),
		'body' => tra('Rows separated by >> in the header; for the table body, one row per line. Cells separated by | in both cases.'),
		'icon' => 'pics/icons/table.png',
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
				'description' => tra('Horizontal alignments for header cells separated by |. Choices: left, right, center, justify.'),
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
	extract ($params,EXTR_SKIP);
	if (empty($sortable)) $sortable = 'n';

	// Start the table
	$wret = '<table class="normal'.($sortable=='y'? ' fancysort':'').'" id="fancytable_'.$iFancytable.'">' . "\r\t";
	
	//if | is used as separator, mask tiki tags during processing and bring back at the end so that any pipes (|) 
	//aren't mistaken for cell dividers
	//pattern covers (( )), [ ], ~np~ /~np~, ~tc~ /~tc~, ~hc~ /~hc~, { = } (plugins with parameters)
	$pattern = '/(\(\([^\)\)]+\)\)|\[[^\]]+\]|~np~(?:(?!~\/np~).)*~\/np~|~tc~(?:(?!~\/tc~).)*~\/tc~'
				. '|~hc~(?:(?!~\/hc~).)*~\/hc~|\{[^\=]+[\=]+[^\=\}]+\})/';
	//process header
	if (isset($head)) {
		if (!empty($headclass)) {
			$tdhdr = "\r\t\t\t<th class=\"$headclass\"";
		} else {
			$tdhdr = "\r\t\t\t<th";
		}
		$separator = strpos($head, '~|~') === false ? '|' : '~|~';
		//skip the preg matching if ~|~ is used
		if ($separator != '~|~') {
			preg_match_all($pattern, $head, $head_matches);
			//replace all tiki tags in the header with numbered strings while being processed
			$head = preg_replace_callback($pattern, 'replace_head', $head);
		}
		//process header rows
		$headrows = process_section($head, 'h', $separator, '>>', $tdhdr, '</th>', isset($colwidths) ? $colwidths : '', 
					isset($headaligns) ? $headaligns : '', isset($headvaligns) ? $headvaligns : '');
		//skip the preg matching if ~|~ is used
		if ($separator != '~|~') {
			//bring the tiki tags back into the header. static veriable needed in case of multiple tables
			static $hh = 0;
			foreach ($head_matches[0] as $head_match) {
				$headrows = str_replace('~~~head' . $hh . '~~~', $head_match, $headrows);
				$hh++;
			}
		}
		$wret .= '<thead>' . $headrows . "\r\t" . '</thead>' . "\r\t" . '<tbody>';
	}
	//process body
	$separator = strpos($data, '~|~') === false ? '|' : '~|~';
	//skip the preg matching if ~|~ is used
	if ($separator != '~|~') {
		preg_match_all($pattern, $data, $body_matches);
		//replace all tiki tags in the body with numbered strings while being processed
		$data = preg_replace_callback($pattern, 'replace_body', $data);
	}
	//process table body rows
	$bodyrows = process_section($data, 'r', $separator, "\n", '', '</td>', isset($colwidths) ? $colwidths : '', 
				isset($colaligns) ? $colaligns : '', isset($colvaligns) ? $colvaligns : '');
	//skip the preg matching if ~|~ is used
	if ($separator != '~|~') {
		//bring the tiki tags back into the body. static veriable needed in case of multiple tables
		static $bb = 0;
		foreach ($body_matches[0] as $body_match) {
			$bodyrows = str_replace('~~~body' . $bb . '~~~', $body_match, $bodyrows);
			$bb++;
		}
	}
	$wret .= $bodyrows;

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

//preg_replace_callback functions to number replacements so they can be identified and undone later
//for the header
function replace_head($matches) {
	static $h = 0;
	$ret = '~~~head' . $h . '~~~';
	$h++;
	return $ret;
}
//for the body
function replace_body($matches) {
	static $b = 0;
	$ret = '~~~body' . $b . '~~~';
	$b++;
	return $ret;
}
//function to process header and body
function process_section ($data, $type, $separator, $line_sep, $cellbeg, $cellend, $widths, $aligns, $valigns) {
//	$separator = strpos($data, '~|~') === FALSE ? '|' : '~|~';
	$lines = explode($line_sep, $data);
	$widths = !empty($widths) ?  explode('|', $widths) : '';
	$aligns = !empty($aligns) ?  explode('|', $aligns) : '';
	$valigns = !empty($valigns)?  explode('|', $valigns) : '';
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
				$colstyle = '';
				if (!empty($widths) || !empty($aligns) || !empty($valigns)) {
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