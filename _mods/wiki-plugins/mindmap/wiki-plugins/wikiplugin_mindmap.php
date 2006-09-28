<?php
/* $Header: /cvsroot/tikiwiki/_mods/wiki-plugins/mindmap/wiki-plugins/wikiplugin_mindmap.php,v 1.1 2006-09-28 01:06:16 fumphco Exp $
 *
 * The freemind plugin source is available at http://freemind.sourceforge.net
 * Can use either the freemind java applet or the much lighter flash plugin
 *
 * Wikiplugin Params:
 *  src    : URL of mindmap (required, must finish with .mm)
 *         : (Absolute or relative urls allowed, relative urls should begin with ./)
 *  plugin : flash|java (optional, default is flash)
 *  width  : width (optional, default is 600)
 *  height : height (optional, default is 400)
 *
 * The function returns some text that will replace the content in the
 * wiki page.
 */
function wikiplugin_mindmap_help() {
	return tra("Browse a mindmap using freemind flash plugin or java applet").":<br />~np~{MINDMAP(src=>./lib/mindmap/wikiplugin_mindmap.mm, [plugin=>flash*|java], [height=>600], [width=400])}{MINDMAP}~/np~";
}
function wikiplugin_mindmap($data, $params) {
	extract ($params, EXTR_SKIP);
// required parameters
	if (!isset($src)) {
	  return ("<b> MINDMAP src parameter is required</b><br/>");
	}
        if (substr($src,-3) == ".mm") {
	} else {
	 return ("<b> MINDMAP src parameter must finish with '.mm'</b><br/>");
	}
// optional parameters
  	if (!isset($width)) {
    	  $width = 600;
  	}
  	if (!isset($height)) {
    	  $height = 400;
  	}
	if ((isset($plugin)) and ($plugin == "java")) {
          $ret='<APPLET CODE="freemind.main.FreeMindApplet.class" ARCHIVE="lib/mindmap/freemindbrowser.jar"';
	  $ret.=' WIDTH="'.$width.'" HEIGHT="'.$height.'">';
	  $ret.=' <PARAM NAME="type" VALUE="application/x-java-applet;version=1.4">';
	  $ret.=' <PARAM NAME="scriptable" VALUE="false">';
	  $ret.=' <PARAM NAME="modes" VALUE="freemind.modes.browsemode.BrowseMode">';
	  $ret.=' <PARAM NAME="browsemode_initial_map" VALUE="'.$src.'">';
	  $ret.=' <PARAM NAME="initial_mode" VALUE="Browse">';
	  $ret.=' <PARAM NAME="selection_method" VALUE="selection_method_direct">';
	  $ret.='</APPLET>';
	} else {
	  $ret='<script type="text/javascript" src="lib/mindmap/flashobject.js"></script>
	  <div id="flashcontent">
	    Flash plugin or Javascript are turned off.
	    Activate both and reload to view the mindmap
	  </div>
	  <script type="text/javascript">
	    var fo = new FlashObject("lib/mindmap/visorFreemind.swf", "visorFreeMind", '.$width.', '.$height.', 6, "#9999ff");
	    fo.addParam("quality", "high");
	    fo.addParam("bgcolor", "#ffffff");
	    fo.addVariable("openUrl", "_blank");
	    fo.addVariable("initLoadFile", "'.$src.'");
	    fo.addVariable("startCollapsedToLevel","5");
	    fo.write("flashcontent");
	  </script>';
	}
	return $ret;
}
?>
