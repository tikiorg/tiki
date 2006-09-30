<?php
/* $Header: /cvsroot/tikiwiki/_mods/wiki-plugins/mindmap/wiki-plugins/wikiplugin_mindmap.php,v 1.2 2006-09-30 05:56:04 fumphco Exp $
 *
 * The freemind plugin source is available at http://freemind.sourceforge.net
 * Can use either the freemind java applet or the much lighter flash plugin
 *
 * The function returns some text that will replace the content in the
 * wiki page.
 */
function wikiplugin_mindmap_help() {
	$help = tra("Browse a mindmap using freemind flash plugin or java applet");
	$help.=":<br />~np~{MINDMAP(";
	$help.="src=>99|./lib/mindmap/wikiplugin_mindmap.mm";
	$help.="[,plugin=>flash|java]";
	$help.="[,height=>700]";
	$help.="[,width=>400]";
	$help.=")}{MINDMAP}~/np~";
	$help.= tra("^Parameters: key=>value,...\n");
	$help.= tra("|| __key__ | __default__ | __comments__\n");
	$help.= tra("src |  ./lib/mindmap/wikiplugin-mindmap.mm |  The location of the mindmap. This can be either an absolute URL, a relative URL, or the numeric ID of a wiki attachment. Wiki attachments currently work with the flash plugin only. A relative URL must begin with ./\n");
	$help.="plugin | flash | This selects the type of plugin to display the mindmap. Valid values are 'flash' or 'java'. Your browser must have a compatible flash plugin or java runtime.\n";
	$help.="width  | 700 | numeric or percentage width\n";
	$help.="height | 400 | numeric height\n";
	$help.= "||^";
	return $help;
}
function wikiplugin_mindmap($data, $params) {
	extract ($params, EXTR_SKIP);
// required parameters
	if (!isset($src)) {
		$src = "./lib/mindmap/wikiplugin-mindmap.mm";
	}
	if (is_numeric($src)) {
		$src = "./tiki-download_wiki_attachment.php?attId=".$src;
	}
// optional parameters
  	if (!isset($width)) {
			$width = 700;
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
