<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * \brief {SPLIT} wiki plugin implementation
 * Usage:
 *
 *  {SPLIT(joincols=>[y|n|0|1],fixedsize=>[y|n|0|1],colsize=>size1|size2|...,first=>[col|line])}
 *
 * If `joincols' eq true (yes) 'colspan' attribute will be generated if column missed.
 * If `fixedsize' eq true (yes) 'width' attribute will be generated for TDs.
 * Both paramaters have default value 'y'
 * first='col': r1c1---r2c1---r3c1@@@r1c2---r2c2 (the editorial way)
 * first='line' (default): r1c1---r1c2@@@r2c1---r2c2 (the html table way)
 * if first=col,edit=y, each section of the split is editable
 *
 * If you set the style for class .wikiplugin-split you are able to alter
 * the output of this plugin.
 *
 */

function wikiplugin_split_info()
{
	return array(
		'name' => tra('Split'),
		'documentation' => 'PluginSplit',
		'description' => tra('Split a page into rows and columns'),
		'prefs' => array( 'wikiplugin_split' ),
		'filter' => 'wikicontent',
		'params' => array(
			'joincols' => array(
				'required' => false,
				'name' => tra('Join Columns'),
				'description' => 'y|n'.' '.tra('Generate the colspan attribute if columns are missing' ),
				'filter' => 'alpha',
			),
			'fixedsize' => array(
				'required' => false,
				'name' => tra('Fixed Size'),
				'description' => 'y|n'.' '.tra('Generate the width attribute on the columns'),
				'filter' => 'alpha',
			),
			'colsize' => array(
				'required' => false,
				'name' => tra('Column Size'),
				'description' => tra('?'),
			),
			'first' => array(
				'required' => false,
				'name' => tra('First'),
				'description' => 'col|line',
				'filter' => 'alpha',
			),
			'edit' => array(
				'required' => false,
				'name' => tra('Editable'),
				'description' => 'y|n'.' '.tra('Display edit icon for each section'),
			),
			'customclass' => array(
				'required' => false,
				'name' => tra('Custom class'),
				'description' => tra('add a class to customize the design'),
			),
		),
	);
}
function wikiplugin_split_rollback($data, $hashes) {
	foreach($hashes as $hash=>$match) {
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
	preg_match_all('/{SPLIT.+{SPLIT}/imU', $data, $matches);
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
	
	extract ($params, EXTR_SKIP);
   $fixedsize = (!isset($fixedsize) || $fixedsize == 'y' || $fixedsize == 1 ? true : false);
   $joincols  = (!isset($joincols)  || $joincols  == 'y' || $joincols  == 1 ? true : false);
   // Split data by rows and cells

   $sections = preg_split("/@@@+/", $data2);
   $rows = array();
   $maxcols = 0;
   foreach ($sections as $i)
   {
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
			$class = 'class="normalnoborder split"';
		else
			$class = 'class="split" width="'.$tdtotaltd.'%"';
	} elseif ($fixedsize) {
		$columnSize = floor(100 / $maxcols);
		$class = 'class="normalnoborder split"';
		$percent = true;	
	}
	if (!isset($edit)) $edit = 'n';
	$result = "<table border='0' cellpadding='0' cellspacing='0' class='wikiplugin-split".($percent ? " normalnoborder" : "").( !empty($customclass) ? " $customclass" : "")."'>";

    // Attention: Dont forget to remove leading empty line in section ...
    //            it should remain from previous '---' line...
    // Attention: origianl text must be placed between \n's!!!
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
  	                    .'<img src="pics/icons/page_edit.png" alt="'.tra('Edit').'" title="'.tra('Edit').'" width="16" height="16" /></a></div><br />';
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
	$result .= "</table>";

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
			if (substr($matches[1], 0, 4) == '----') {
			} else if (substr($matches[1], 0, 3) == '@@@' || $matches[1] == '---') {
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
			} elseif ($matches[1] == '{SPLIT(')  {
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
