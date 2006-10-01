<?php
/* $Header: /cvsroot/tikiwiki/_mods/wiki-plugins/mindmap/wiki-plugins/wikiplugin_mindmap.php,v 1.4 2006-10-01 01:22:03 fumphco Exp $
 *
 * The freemind plugin source is available at http://freemind.sourceforge.net
 * Both the freemind java applet and the much lighter flash plugin are supported.
 *
 * The function returns some text that will replace the content in the
 * wiki page.
 */
function wikiplugin_mindmap_help() {
	$help = tra("Browse a mindmap using freemind flash plugin or java applet");
	$help.=":<br />~np~{MINDMAP(";
	$help.="src=>99|./lib/mindmap/wikiplugin_mindmap.mm";
	$help.="[,plugin=>flash|java]";
	$help.="[,width=>700]";
	$help.="[,height=>400]";
	$help.=")}{MINDMAP}~/np~";
	$help.= tra("^Parameters: key=>value,...\n");
	$help.= tra("|| __key__ | __default__ | __comments__\n");
	$help.= tra("src |  ./lib/mindmap/wikiplugin-mindmap.mm |  The location of the mindmap. This can be either an absolute URL, a relative URL, or the numeric ID of a wiki attachment. Wiki attachments currently work with the flash plugin only. A relative URL must begin with ./\n");
	$help.="plugin | flash | This selects the type of plugin to display the mindmap. Valid values are 'flash' or 'java'. Your browser must have a compatible flash plugin or java runtime.\n";
	$help.="width  | 700 | numeric width\n";
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
	$id = uniqid("mm_");
	if ((isset($plugin)) and ($plugin == "java")) {
		$ret='
		<APPLET ID="'.$id.'" CODE="freemind.main.FreeMindApplet.class" ARCHIVE="lib/mindmap/freemindbrowser.jar" WIDTH="'.$width.'" HEIGHT="'.$height.'">
			<PARAM NAME="type" VALUE="application/x-java-applet;version=1.4">
			<PARAM NAME="scriptable" VALUE="false">
			<PARAM NAME="modes" VALUE="freemind.modes.browsemode.BrowseMode">
			<PARAM NAME="browsemode_initial_map" VALUE="'.$src.'">
			<PARAM NAME="initial_mode" VALUE="Browse">
			<PARAM NAME="selection_method" VALUE="selection_method_direct">
	  </APPLET>';
	} else {
		$ret='
		<script type="text/javascript" src="lib/mindmap/flashobject.js"></script>
		<div id="flashcontent_'.$id.'">
		  A problem occurred while trying to display the mindmap.
		  Check if both Flash plugin and Javascript are activated.
		</div>
		<script type="text/javascript">
			var '.$id.' = new FlashObject("lib/mindmap/visorFreemind.swf", "visorFreeMind", '.$width.', '.$height.', 6, "#9999ff");
			'.$id.'.addParam("quality", "high");
			'.$id.'.addParam("bgcolor", "#ffffff");
			'.$id.'.addVariable("openUrl", "_blank");
			'.$id.'.addVariable("initLoadFile", "'.$src.'");
			'.$id.'.addVariable("startCollapsedToLevel","5");
			'.$id.'.write("flashcontent_'.$id.'");
		</script>';
	}
	return $ret;
}
?>
