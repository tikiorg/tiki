<?php
/*
 * Tiki-Wiki ANAME plugin. Go to http://www.TikiMODS.com for more Tikiwiki Plugins.
 *
 * DESCRIPTION: Puts an anchor into a wiki page. Use in conjunction with the ALINK plugin, which makes links to the anchor.
 * 
 * INSTALLATION: Just put this file into your Tikiwiki site's lib/wiki-plugins folder.
 * 
 * USAGE SYNTAX:
 * 
 * 	{ANAME()}
 *	anchorname		the name of the anchor. Use this as the aname=> parameter in the ALINK plugin!
 *	{ANAME}
 *
 * EXAMPLE:  {ANAME()}anchorname{ANAME}
 * 
  */


function wikiplugin_aname_help() {
        return tra("Puts an anchor into a wiki page. Use in conjunction with the ALINK plugin, which makes links to the anchor").":<br />~np~{ANAME()}anchorname{ANAME}~/np~";
}

function wikiplugin_aname($data, $params)
{
        global $tikilib;
        extract ($params, EXTR_SKIP);
        
    // the following replace is necessary to maintain compliance with XHTML 1.0 Transitional
	// and the same behavior as tikilib.php and ALINK. This will change when the world arrives at XHTML 1.0 Strict.
	$data = ereg_replace('[^a-zA-Z0-9]+', '_', $data);

	return "<a id=\"$data\"></a>";
}

?>
