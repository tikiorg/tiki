<?php

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
 *
 * @package TikiWiki
 * @subpackage TikiPlugins
 * @version $Revision: 1.3 $
 */

function wikiplugin_include_help() {
	return tra("Include a page").":<br />~np~{INCLUDE(page=> [,start=>] [,stop=>])}{INCLUDE}~/np~";
}
function wikiplugin_include($data, $params) {
	global $tikilib;

	extract ($params);

	if (!isset($page)) {
		return ("<b>missing page for plugin INCLUDE</b><br/>");
	}

	$data = $tikilib->get_page_info($page);
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
	return $text;
}

?>
