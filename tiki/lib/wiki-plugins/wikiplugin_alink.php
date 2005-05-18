<?php
/*
 * Tiki-Wiki ALINK plugin. Go to http://www.TikiMODS.com for more TikiWiki Plugins.
 *
 * DESCRIPTION: Puts a link to an anchor in a wiki page. Use in conjunction with the ANAME plugin, which sets the location and name of the anchor.
 * 
 * INSTALLATION: Just put this file into your TikiWiki site's lib/wiki-plugins folder.
 * 
 * USAGE SYNTAX:
 * 
 * 	{ALINK(
 *		aname=>anchorname	the name of the anchor you created using the ANAME plugin!
 * 	)}
 *	 	yourlinktext		the text of the link
 *	 {ALINK}
 *
 * EXAMPLE:  {ALINK(aname=myanchor)}click here{ALINK}
 * 
  */


function wikiplugin_alink_help() {
        return tra("Puts a link to an anchor in a wiki page. Use in conjunction with the ANAME plugin, which sets the location and name of the anchor").":<br />~np~{ALINK(aname=anchorname)}".tra("linktext")."{ALINK}~/np~";
}

function wikiplugin_alink($data, $params)
{
        global $tikilib;
        extract ($params, EXTR_SKIP);

	if (!isset($aname)) {
		return ("<b>missing parameter for aname</b><br />");
	}

	return "<A HREF=\"#$aname\">$data</A>";
}

?>
