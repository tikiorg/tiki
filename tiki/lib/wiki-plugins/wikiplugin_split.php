<?php
/**
 * \file
 * $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_split.php,v 1.9 2003-10-23 22:58:12 zaufi Exp $
 * 
 * \brief {SPLIT} wiki plugin implementation
 *
 */

function wikiplugin_split_help() {
	return tra("Split a page into columns").":<br />~np~{SPLIT()}".tra("column")."---".tra("column")."---".tra("column")."{SPLIT}~/np~";
}

/*
 * \note This plugin should carefuly change text it have
 *       bcose some of wiki synataxes are sensitive for
 *       start of line (like lists and headers)... such
 *       user lines must stay with the same layout after
 *       this plugin...
 *
 * Attention: (by zaufi)
 *  Smbd can explain what for this __nasty__ hack needed?
 *  The only result I see is that many wiki syntaxes become
 *  broken... like lists, -=titles=- and some others...
 *
 * $sections[$i] = preg_replace("/([\r\n])/","<br />",$sections[$i]);
 *
 * so in refactored plugin this code not present...
 */
function wikiplugin_split($data, $params) {
	global $tikilib;
	global $replacement;

    // Remove first <ENTER> if exists...
    // it may be here if present after {SPLIT()} in original text
	if (substr($data, 0, 1) == "\n") $data = substr($data, 1);

	extract ($params);
	$sections = preg_split("/---+/", $data);

    // Is there split sections present?
    // Do not touch anything if no... even don't generate <table>
    if (!is_array($sections) || count($sections) <= 1)
        return $data;

	$columnSize = floor(100 / count($sections));
	$result = "<table border='0' width='100%'><tr>";

    // Attention: Dont forget to remove leading empty line in section ...
    //            it should remain from previous '---' line...
    // Attention: origianl text must be placed between \n's!!!
	foreach ($sections as $i)
		$result .= "<td valign='top' width='" . $columnSize . "%'>
                   \n".substr($i, 1)."\n</td>";

    // Close HTML table (no \n at end!)
	$result .= "</tr></table>";

	return $result;
}

?>
