<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_split_info()
{
	return array(
		'name' => tra('Split'),
		'documentation' => 'PluginSplit',
		'description' => tra('Arrange content on a page into rows and columns'),
		'prefs' => array( 'wikiplugin_split' ),
		'filter' => 'wikicontent',
		'iconname' => 'th-large',
		'introduced' => 1,
		'tags' => array( 'basic' ),
		'params' => array(
			'joincols' => array(
				'required' => false,
				'name' => tra('Join Columns'),
				'description' => tra('Generate the colspan attribute if columns are missing'),
				'since' => '1',
				'filter' => 'alpha',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'fixedsize' => array(
				'required' => false,
				'name' => tra('Fixed Size'),
				'description' => tra('Generate the width attribute for the columns'),
				'since' => '1',
				'filter' => 'alpha',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'colsize' => array(
				'required' => false,
				'name' => tra('Column Sizes'),
				'description' => tr('Specify all column widths in number of pixels or percent, separating each width
					by a pipe (%0)', '<code>|</code>'),
				'since' => '1',
				'seprator' => '|',
				'filter' => 'text',
				'default' => '',
			),
			'first' => array(
				'required' => false,
				'name' => tra('First'),
				'description' => tra('Cells specified are ordered first left to right across rows (default) or top to
					bottom down columns'),
				'since' => '1',
				'filter' => 'alpha',
				'default' => 'line',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Column'), 'value' => 'col'), 
					array('text' => tra('Line'), 'value' => 'line')
				)
			),
			'edit' => array(
				'required' => false,
				'name' => tra('Editable'),
				'description' => tr('Display edit icon for each section. Works when used on a wiki page and the %0
					parameter is set to %1', '<code>first</code>', '<code>col</code>'),
				'since' => '1',
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'customclass' => array(
				'required' => false,
				'name' => tra('Custom Class'),
				'description' => tra('Add a class to customize the design'),
				'since' => '3.0',
				'filter' => 'text',
				'default' => '',
			),
		),
	);
}
function wikiplugin_split_rollback($data, $hashes)
{
	foreach ($hashes as $hash=>$match) {
		$data = str_replace($hash, $match, $data);
	}
	return $data;
}

/*
 * \note This plugin should carefuly change text it have to parse
 *       because some of wiki syntaxes are sensitive for
 *       start of new line ('\n' character - e.g. lists and headers)... such
 *       user lines must stay with the same layout when applying
 *       this plugin to render them properly after...
 *		$data = the preparsed data (plugin, code, np.... already parsed)
 *		$pos is the position in the object where the non-parsed data begins
 */
function wikiplugin_split($data, $params, $pos)
{
	global $tikilib, $tiki_p_admin_wiki, $tiki_p_admin, $section;
	global $replacement;
	preg_match_all('/{(SPLIT|CODE|HTML|FADE|JQ|JS|MOUSEOVER|VERSIONS).+{\1}/ismU', $data, $matches);
	$hashes = array();
	foreach ($matches[0] as $match) {
		if (empty($match)) continue;
		$hash = md5($match);
		$hashes[$hash] = $match;
		$data = str_replace($match, $hash, $data);
	}

	// Remove first <ENTER> if exists...
	// it may be here if present after {SPLIT()} in original text
	if (substr($data, 0, 2) == "\r\n")
		$data2 = substr($data, 2);
	else
		$data2 = $data;
	
	extract($params, EXTR_SKIP);
	$fixedsize = (!isset($fixedsize) || $fixedsize == 'y' || $fixedsize == 1 ? true : false);
	$joincols  = (!isset($joincols)  || $joincols  == 'y' || $joincols  == 1 ? true : false);
	// Split data by rows and cells

	$sections = preg_split("/@@@+/", $data2);
	$rows = array();
	$maxcols = 0;
	foreach ($sections as $i) {
		// split by --- but not by ----
		//	$rows[] = preg_split("/([^\-]---[^\-]|^---[^\-]|[^\-]---$|^---$)+/", $i);
		//	not to eat the character close to - and to split on --- and not ----
		$rows[] = preg_split("/(?<!-)---(?!-)/", $i);
		$maxcols = max($maxcols, count(end($rows)));
	}

	// Is there split sections present?
	// Do not touch anything if no... even don't generate <table>
	if (count($rows) <= 1 && count($rows[0]) <= 1)
	   return wikiplugin_split_rollback($data, $hashes);

	$percent = false;
	if (isset($colsize)) {
		$tdsize= explode("|", $colsize);
		$tdtotal=0;
		for ($i=0;$i<$maxcols;$i++) {
			if (!isset($tdsize[$i])) {
				$tdsize[$i]=0;
			} else {
				$tdsize[$i] = trim($tdsize[$i]);
				if (strstr($tdsize[$i], '%')) {
					$percent = true;
				}
			}
			$tdtotal+=$tdsize[$i];
		}
		$tdtotaltd=floor($tdtotal/100*100);
		if ($tdtotaltd == 100) // avoir IE to do to far
			$class = 'class="table split"';
		else
			$class = 'class="split" width="'.$tdtotaltd.'%"';
	} elseif ($fixedsize) {
		$columnSize = floor(100 / $maxcols);
		$class = 'class="table split"';
		$percent = true;	
	}
	if (!isset($edit)) $edit = 'n';
	$result = "<div class='table-responsive'><div><table class='table".($percent ? " normalnoborder" : "").( !empty($customclass) ? " $customclass" : "")."'>";

	// Attention: Dont forget to remove leading empty line in section ...
	//            it should remain from previous '---' line...
	// Attention: original text must be placed between \n's!!!
	if (!isset($first) || $first != 'col') {
		foreach ($rows as $r) {
			$result .= "<tr>";
			$idx = 1;
			foreach ($r as $i) {
				// Remove first <ENTER> if exists
				if (substr($i, 0, 2) == "\r\n") $i = substr($i, 2);
				// Generate colspan for last element if needed
				$colspan = ((count($r) == $idx) && (($maxcols - $idx) > 0) ? ' colspan="'.($maxcols - $idx + 1).'"' : '');
				$idx++;
				// Add cell to table
				if (isset($colsize)) {
					$width = ' width="'.$tdsize[$idx-2].'"';
				} elseif ($fixedsize) {
					$width = ' width="'.$columnSize.'%" ';
				} else {
					$width = '';
				}
				$result .= '<td valign="top"'.$width.$colspan.'>'
					// Insert "\n" at data begin (so start-of-line-sensitive syntaxes will be parsed OK)
					."\n"
					// now prepend any carriage return and newline char with br
					.preg_replace("/\r?\n/", "<br />\r\n", $i)
					. '</td>';
			}

			$result .= "</tr>";
		}
	} else { // column first
		if ($edit == 'y') {
			global $user;
			if (isset($_REQUEST['page'])) {
				$type = 'wiki page';
				$object = $_REQUEST['page'];
				if ($tiki_p_admin_wiki == 'y' || $tiki_p_admin == 'y')
					$perm = true;
				else
					$perm = $tikilib->user_has_perm_on_object($user, $object, $type, 'tiki_p_edit');
			} else { // TODO: other object type
				$perm = false;
			}
		}
		$ind = 0;
		$icell = 0;
		$result .= '<tr>';
		$idx = 0;
		foreach ($rows as $r) {
			$result .= '<td valign="top" '.(($fixedsize && isset($tdsize))? ' width="'.$tdsize[$idx].(strstr($tdsize[$idx], '%')?'':'%').'"' : '').'>';
			foreach ($r as $i) {
				if (substr($i, 0, 2) == "\r\n") {
					$i = substr($i, 2);
					$ind += 2;
				}
				if ($edit == 'y' && $perm && $section == 'wiki page') {
					$result .= '<div class="split"><div style="float:right">';
					$result .= "$pos-$icell-".htmlspecialchars(substr($data, $pos, 10));
					$result .= '<a href="tiki-editpage.php?page='.$object.'&amp;pos='.$pos.'&amp;cell='.$icell.'">'
						.'<img src="img/icons/page_edit.png" alt="'.tra('Edit').'" title="'.tra('Edit').'" width="16" height="16" /></a></div><br />';
					$ind += strlen($i);
					while (isset($data[$ind]) && ($data[$ind] == '-' || $data[$ind] == '@'))
						++$ind;
				}
				else
					$result .= '<div>';
				$result .= "\n".preg_replace("/\r?\n/", "<br />\r\n", $i). '</div>';
				++$icell;
			}
			++$idx;
			$result .= '</td>';
		}
		$result .= '</tr>';
	}
	// Close HTML table (no \n at end!)
	$result .= "</table></div></div>";

	return wikiplugin_split_rollback($result, $hashes);
}

// find the real start and the real end of a cell
// $pos = start pos in the non parsed data of all the split cells
// $cell = cellule indice
function  wikiplugin_split_cell($data, $pos, $cell)
{
	$start = $pos;
	$end = $pos;
	$no_parsed = '~np~|~pp~|\\<pre\\>|\\<PRE\\>';
	$no_parsed_end = '';
	$match = '@@@+|----*|\{SPLIT\}|\{SPLIT\(|~np~|~pp~|\\<pre\\>|\\<PRE\\>';
	while (true) {
		if (!preg_match("#($match)#m", substr($data, $pos), $matches)) {
			if ($cell)
				$end = $start;
			break;
		} else {
			$end = $pos + strpos(substr($data, $pos), $matches[1]);
			$start_next_tag = $end + strlen($matches[1]);
			if (substr($matches[1], 0, 3) == '@@@' || $matches[1] == '---') {
				if (!$cell)
					break;
				--$cell;
				if (!$cell)
					$start = $start_next_tag;
			} elseif ($matches[1] == $match) {
				$match = '@@@+|----*|\{SPLIT\}|\{SPLIT\(|~np~|~pp~|\\<pre\\>|\\<PRE\\>';
			} elseif ($matches[1] == '{SPLIT}') {
				if (!$cell)
					break;
			} elseif ($matches[1] == '{SPLIT(') {
				$match = '{SPLIT}';
			} elseif ($matches[1] == '~np~') {
				$match = '~/np~';
			} elseif ($matches[1] == '~pp~') {
				$match = '~/pp~';
			} elseif ($matches[1] == '<pre>') {
				$match = '</pre>';
			} elseif ($matches[1] == '<PRE>') {
				$match = '</PRE>';
			}
		$pos = $start_next_tag;
		}
	}
	return array($start, $end - $start);
}
