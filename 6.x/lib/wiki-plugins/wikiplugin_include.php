<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * INCLUDE plugin
 * Includes a wiki page in another.
 *
 * Usage:
 * {INCLUDE(page=>name [,start=>start-marker] [,stop=>stop-marker])}{INCLUDE}
 *
 * Params:
 * @param	page	Gives the name of the page to include
 * @param	start	Gives a string to search for to begin the include. Text
 *			before that marker (and the marker itself) will not be included.
 *			Default is the beginning of the included page.
 *			The marker must appear on a line by itself; white space
 *			before or after the marker is ignored.
 * @param	stop		Gives a string to search for to end the include. Text
 *			after that marker (and the marker itself) will not be included.
 *			Default is the beginning of the included page.
 *			The marker must appear on a line by itself; white space
 *			before or after the marker is ignored.
 *
 * If both start and stop are specified and the pair of strings occurs
 * multiple times in the included page, each section so delimited will
 * be included in the calling page.
 *
 * NOTE: The design and implementation of the start/stop feature is experimental
 *	 and needs some feedback (and, no doubt, improvement) from the community. 
 *       In order to prevent infinite loops, any page can only be included
 *   directly or indirectly 5 times (set in $max_times).
 *
 * @package Tikiwiki
 * @subpackage TikiPlugins
 * @version $Revision: 1.11 $
 */

function wikiplugin_include_help() {
	return tra("Include a page").":<br />~np~{INCLUDE(page=> [,start=>] [,stop=>])}{INCLUDE}~/np~";
}

function wikiplugin_include_info() {
	return array(
		'name' => tra('Include'),
		'documentation' => 'PluginInclude',
		'description' => tra('Include content from another wiki page'),
		'prefs' => array('wikiplugin_include'),
		'params' => array(
			'page' => array(
				'required' => true,
				'name' => tra('Page Name'),
				'description' => tra('Wiki page name to include.'),
				'filter' => 'pagename',
				'default' => '',
			),
			'start' => array(
				'required' => false,
				'name' => tra('Start'),
				'description' => tra('When only a portion of the page should be included, specify the marker from which inclusion should start.'),
				'default' => '',
			),
			'stop' => array(
				'required' => false,
				'name' => tra('Stop'),
				'description' => tra('When only a portion of the page should be included, specify the marker at which inclusion should end.'),
				'default' => '',
			),
			'nopage_text' => array(
				'required' => false,
				'name' => tra('Nopage Text'),
				'description' => tra('Text to show when no page is found.'),
				'default' => '',
			),
			'pagedenied_text' => array(
				'required' => false,
				'name' => tra('Page Denied Text'),
				'description' => tra('Text to show when the page exists but is denied to the user.'),
				'default' => '',
			),
		),
	);
}

function wikiplugin_include($data, $params, $offset) {
	global $tikilib,$userlib,$user;
    static $included_pages, $data;

	$max_times = 5;
	$params = array_merge( array( 'nopage_text' => '', 'pagedenied_text' => '' ), $params );
	extract ($params,EXTR_SKIP);
	if (!isset($page)) {
		return ("<b>missing page for plugin INCLUDE</b><br />");
	}
	$memo = $page;
	if (isset($start)) $memo .= "/$start";
	if (isset($end)) $memo .= "/$end";
    if ( isset($included_pages[$memo]) ) {
        if ( $included_pages[$memo]>=$max_times ) {
            return '';
        }
        $included_pages[$memo]++;
    } else {
        $included_pages[$memo] = 1;
        // only evaluate permission the first time round
        // evaluate if object or system permissions enables user to see the included page
    	$data = $tikilib->get_page_info($page);
    	if (!$data) {
    		$text = $nopage_text;
    	}
		$perms = $tikilib->get_perm_object($page, 'wiki page', $data, false);
        if ($perms['tiki_p_view'] != 'y') {
            $included_pages[$memo] = $max_times;
            $text = $pagedenied_text;
            return($text);
        }
    }

	if ($data) {
		$text = $data['data'];
		if (isset($start) || isset($stop)) {
			$explText = explode("\n", $text);
			if (isset($start) && isset($stop)) {
				$state = 0;
				foreach ($explText as $i => $line) {
					if ($state == 0) {
						// Searching for start marker, dropping lines until found
						unset($explText[$i]);	// Drop the line
						if (0 == strcmp($start, trim($line))) {
							$state = 1;	// Start retaining lines and searching for stop marker
						}
					} else {
						// Searching for stop marker, retaining lines until found
						if (0 == strcmp($stop, trim($line))) {
							unset($explText[$i]);	// Stop marker, drop the line
							$state = 0; 		// Go back to looking for start marker
						}
					}
				}
			} else if (isset($start)) {
				// Only start marker is set. Search for it, dropping all lines until
				// it is found.
				foreach ($explText as $i => $line) {
					unset($explText[$i]); // Drop the line
					if (0 == strcmp($start, trim($line))) {
						break;
					}
				}
			} else {
				// Only stop marker is set. Search for it, dropping all lines after
				// it is found.
				$state = 1;
				foreach ($explText as $i => $line) {
					if ($state == 0) {
						// Dropping lines
						unset($explText[$i]);
					} else {
						// Searching for stop marker, retaining lines until found
						if (0 == strcmp($stop, trim($line))) {
							unset($explText[$i]);	// Stop marker, drop the line
							$state = 0; 		// Start dropping lines
						}
					}
				}
			}	
			$text = implode("\n", $explText);
		}
	}
	$text = $tikilib->parse_data($text, array('suppress_icons' => true));	// don't show edit icons (they don't work on included pages - yet)
	// append an edit button
	if ($perms['tiki_p_edit'] === 'y') {
		global $smarty;
		require_once $smarty->_get_plugin_filepath('block', 'ajax_href');
		require_once $smarty->_get_plugin_filepath('function', 'icon');
		$text .= '<a class="editplugin" title="'.tra('Edit this page').'" '.	// ironically smarty_block_self_link doesn't work for this! ;)
				smarty_block_ajax_href( array('template' => 'tiki-editpage.tpl'), 'tiki-editpage.php?page='.urlencode($page).'&returnto='.urlencode($GLOBALS['page']),$smarty, false) .
				smarty_function_icon(array( '_id' => 'page_edit', 'alt' => tra('Edit this page')), $smarty) . '</a>';
	}
	if ($tikilib->contains_html_block($text)) {	// add an identifying wrapper element
		$text = '<div class="wikiplugin_include" id="plugin_include_' . $offset . '">' . $text . '</div>';
	} else {
		$text = '<span class="wikiplugin_include" id="plugin_include_' . $offset . '">' . $text . '</span>';
	} 
	return '~np~' . $text . '~/np~';			// wrap in noparse tags as it already has been
}
