<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_fancytable_info()
{
	return array(
		'name' => tra('Fancy Table'),
		'documentation' => 'PluginFancyTable',
		'description' => tra('Create a formatted table'),
		'prefs' => array('wikiplugin_fancytable'),
		'body' => tra('Rows separated by >> in the header; for the table body, one row per line. Cells separated by | in both cases.'),
		'icon' => 'img/icons/table.png',
		'tags' => array( 'basic' ),
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
				'description' => tra('Table body column horizontal alignments separated by |. Choices: left, right, center, justify.'),
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

function wikiplugin_fancytable($data, $params)
{
	global $prefs;
	$tagremove = array();
	$pluginremove = array();
	static $iFancytable = 0;
	++$iFancytable;
	extract($params, EXTR_SKIP);
	if (empty($sortable)) $sortable = 'n';

	//Start the table
	$wret = '<table class="normal'.($sortable=='y'? ' fancysort':'').'" id="fancytable_'.$iFancytable.'">' . "\r\t";

	//Header
	if (isset($head)) {
		if (!empty($headclass)) {
			$tdhdr = "\r\t\t\t<th class=\"$headclass\"";
		} else {
			$tdhdr = "\r\t\t\t<th";
		}
		//replace tiki tags, plugins and other enclosing characters with hash strings before creating table so that any
		//pipes (| or ~|~) inside aren't mistaken for cell dividers
		preprocess_section($head, $tagremove, $pluginremove);

		//now create header table rows
		$headrows = process_section(
			$head, 'h', '>>', $tdhdr, '</th>', isset($colwidths) ? $colwidths : '',
			isset($headaligns) ? $headaligns : '', isset($headvaligns) ? $headvaligns : ''
		);

		//restore original tags and plugin syntax
		postprocess_section($headrows, $tagremove, $pluginremove);

		$wret .= '<thead>' . $headrows . "\r\t" . '</thead>' . "\r\t" . '<tbody>';
	}

	//Body
	//replace tiki tags, plugins and other enclosing characters with hash strings before creating table so that any
	//pipes (| or ~|~) inside aren't mistaken for cell dividers
	preprocess_section($data, $tagremove, $pluginremove);

	if ($sortable == 'y' && $prefs['disableJavascript'] == 'n' && $prefs['feature_jquery_tablesorter'] == 'y') {
		$type = 's';	//sortable rows - do not assign odd/even class to these since jquery will do it
	} else {
		$type = 'r';	//plain rows
	}
	//now create table body rows
	$bodyrows = process_section(
		$data, $type, "\n", '', '</td>', isset($colwidths) ? $colwidths : '',
		isset($colaligns) ? $colaligns : '', isset($colvaligns) ? $colvaligns : ''
	);

	//restore original tags and plugin syntax
	postprocess_section($bodyrows, $tagremove, $pluginremove);

	$wret .= $bodyrows;

	//end the table
	if (isset($head)) {
		$wret .= "\r\t" . '</tbody>';
	}
	$wret .= "\r" . '</table>' . "\r";
	if ($sortable == 'y' && $prefs['disableJavascript'] == 'n') {
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

/**
 * To keep pipes (| or ~|~) inside other Tiki tags or plugins, or other "enclosing" characters from being mistaken as
 * cell dividers, replace them with hash strings until after the table is created
 *
 * @param 		string			$data			Header or body text with tags and plugins removed
 * @param 		array			$tagremove		Key => value pairs of Hash => data for tags or enclosing characters
 * @param 		array			$pluginremove	Key => value pairs of Hash => data for plugins
 */
function preprocess_section (&$data, &$tagremove, &$pluginremove)
{
	$parserlib = TikiLib::lib('parser');
	//first replace plugins with hash strings since they may contain pipe characters
	$parserlib->plugins_remove($data, $pluginremove);

	//then replace tags or other enclosing charcters that could enclose a pipe (| or ~|~) character with a hash string
	$tikilib = TikiLib::lib('tiki');
	$tags = array(
		array(
			'start' => '\(\(',		// (( ))
			'end'	=> '\)\)',
		),
		array(
			'start' => '\[',		// [ ]
			'end'	=> '\]',
		),
		array(
			'start' => '~np~',		// ~np~ ~/np~
			'end'	=> '~\/np~',
		),
		array(
			'start' => '~tc~',		// ~tc~ ~/tc~
			'end'	=> '~\/tc~',
		),
		array(
			'start' => '~hc~',		// ~hc~ ~/hc~
			'end'	=> '~\/hc~',
		),
		array(
			'start' => '\^',		// ^ ^
			'end'	=> '\^',
		),
		array(
			'start' => '__',		// __ __
			'end'	=> '__',
		),
		array(
			'start' => '\:\:',		// :: ::
			'end'	=> '\:\:',
		),
		array(
			'start' => '\:\:\:',		// ::: :::
			'end'	=> '\:\:\:',
		),
		array(
			'start' => '\'\'',		// '' ''
			'end'	=> '\'\'',
		),
		array(
			'start' => '-\+',		// -+ +-
			'end'	=> '\+-',
		),
		array(
			'start' => '-=',		// -= =-
			'end'	=> '=-',
		),
		array(
			'start' => '===',		// === ===
			'end'	=> '===',
		),
		array(
			'start' => '--',		// -- --
			'end'	=> '--',
		),
	array(
			'start' => '\(',		// ( )
			'end'	=> '\)',
		),
	array(
		'start' => '\"',		// " "
		'end'	=> '\"',
	),
	);
	$count = count($tags) - 1;
	$pattern = '/(';
	foreach ($tags as $key => $tag) {
		$pattern .= $tag['start'] . '(?:(?!' . $tag['end'] . ').)*' . $tag['end'];
		if ($key < $count) {
			$pattern .= '|';
		}
	}
	$pattern .= ')/';
	preg_match_all($pattern, $data, $matches);
	foreach ($matches[0] as $match) {
		$tagremove['key'][] = '§' . md5($tikilib->genPass()) . '§';
		$tagremove['data'][] = $match;
		$data = isset($tagremove['data']) ? str_replace($tagremove['data'], $tagremove['key'], $data) : $data;
	}
}

/**
 * Process header or body string to convert to table rows
 *
 * @param 		string			$data		Header or body string to be broken down into table rows
 * @param 		string			$type		Indicates whether rows are sortable or not, which impacts class assigned
 * @param 		string			$line_sep	Row separator (>> for header and \n for body)
 * @param 		string			$cellbeg	HTML <th or <td tag and appropriate class attribute
 * @param 		string			$cellend	HTML </th> or </td> ending tags
 * @param 		numeric			$widths		User input for column widths
 * @param 		string			$aligns		User input for horizontal text alignment within cells
 * @param 		string			$valigns	User input for vertical text alignment within cells
 *
 * @return 		string			$wret		HTML string for the header or body rows processed
 */
function process_section ($data, $type, $line_sep, $cellbeg, $cellend, $widths, $aligns, $valigns)
{
	$separator = strpos($data, '~|~') === false ? '|' : '~|~';
	$lines = explode($line_sep, $data);
	$widths = !empty($widths) ?  explode('|', $widths) : '';
	$aligns = !empty($aligns) ?  explode('|', $aligns) : '';
	$valigns = !empty($valigns)?  explode('|', $valigns) : '';
	$trbeg = "\r\t\t<tr>";
	$trend = "\r\t\t</tr>";
	$l = 0;
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
			//don't set odd/even class if tablesorter is on because jquery will add it
			//and the classes won't alternate correctly if added here too
			} elseif ($type == 's') {
				$cellbeg = "\r\t\t\t" . '<td';
			}
			$c = 0;
			$row = '';
			$parts = explode($separator, $line);
			//Each column within a row
			foreach ($parts as $column) {
				$colnum = 'col' . $c;
				$colspan = '';
				$rowspan = '';
				/*
				 * Match / (colspan) or \ (rowspan) characters in whichever order at the beginning of the cell
				 * $matches[0][0] shows entire match. There are 3 strings being matched in the preg_match_all,
				 * so $matches[1][0] shows the character matched for the first string (\), $matches[2][0] the
				 * second character (/), and $matches[3][0] the third character (\)
				*/
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
							if ((${$colnum}['span'] - ($l - ${$colnum}['line'])) > 0) {
								$c++;
							}
						}
						//Note the info for this new rowspan
						${$colnum}['line'] = $l;
						${$colnum}['col'] = $c;
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
					$colstyle .= !empty($widths[$c]) ? ' width: ' . $widths[$c] . ';' : '';
					$colstyle .= !empty($aligns[$c]) ? ' text-align: ' . $aligns[$c] . ';' : '';
					$colstyle .= !empty($valigns[$c]) ? ' vertical-align: ' . $valigns[$c] : '';
					$colstyle .= '"';
					$c++;//increment column number
				}
				$row .= $cellbeg . $colspan . $rowspan . $colstyle . '>' . $column . $cellend;
			}
			$wret .= $trbeg . $row . $trend;
		}
		$l++;//increment row number
	}
	return $wret;
}

/**
 * Replace hash strings with original tag and plugin content
 *
 * @param 		string		$data			Header or body text processed into table rows with hash strings replacing
 * 												tags and plugins
 * @param 		array		$tagremove		Tag hash => data pairs for tags that were replaced with hash strings
 * @param 		array		$pluginremove	Plugin Hash => data pairs for plugins that were replaced with hash strings
 */
function postprocess_section (&$data, &$tagremove, &$pluginremove)
{
	//first restore tag strings
	$parserlib = TikiLib::lib('parser');
	if (isset($tagremove['key']) and count($tagremove['key'])
		and count($tagremove['key']) == count($tagremove['data'])) {
		$data = str_replace($tagremove['key'], $tagremove['data'], $data);
	}
	//then restore plugin strings
	$parserlib->plugins_replace($data, $pluginremove);
	//reset variables since this function is run for both the header and body
	$tagremove = array();
	$pluginremove = array();
}
