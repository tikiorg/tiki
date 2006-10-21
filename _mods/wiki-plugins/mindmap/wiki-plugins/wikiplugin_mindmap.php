<?php
/* $Header: /cvsroot/tikiwiki/_mods/wiki-plugins/mindmap/wiki-plugins/wikiplugin_mindmap.php,v 1.5 2006-10-21 00:36:46 fumphco Exp $
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
	$help.="[,mode=>inline|window|fullscreen]";
	$help.="[,width=>100%]";
	$help.="[,height=>400]";
	$help.=")}{MINDMAP}~/np~";
	$help.= tra("^Parameters: key=>value,...\n");
	$help.= tra("|| __key__ | __default__ | __comments__\n");
	$help.= tra("src |  ./lib/mindmap/wikiplugin-mindmap.mm |  The location of the mindmap. This can be either an absolute URL, a relative URL, or the numeric ID of a wiki attachment. Wiki attachments currently work with the flash plugin only. A relative URL must begin with ./\n");
	$help.="plugin | flash | This selects the type of plugin to display the mindmap. Valid values are 'flash' or 'java'. Your browser must have a compatible flash plugin or java runtime.\n";
	$help.="mode  | inline | Valid values are 'inline', 'window' or 'fullscreen'. Currently, window and fullscreen modes only work with the java plugin.\n";
	$help.="width  | 100% | numeric or percentage width\n";
	$help.="height | 400 | numeric or percentage height\n";
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
		$width = "100%";
  }
  if (!isset($height)) {
		$height = 400;
  }
	if ($plugin=="java" and isset($mode) and ($mode == "window" or $mode == "fullscreen")) {
		$width="100%";
		$height="100%";
		$script_begin="
<script type='text/javascript'>
	fmw = window.open('','fmw'";
		if ($mode == "fullscreen") {
			$script_begin.=",'fullscreen=yes')";
		} else {
			$script_begin.=")";
		}
		$line_begin="
	fmw.document.write('";
		$line_end="');";
		$script_end="
	fmw.document.close();
</script>";
		$script_tag="scr'+'ipt";
	} else {
		$script_begin="
";
		$line_begin="
";
		$line_end="";
		$script_end="";
		$script_tag="script";
	}
	$id = uniqid("mm_");
	if ((isset($plugin)) and ($plugin == "java")) {
		$ret=$script_begin;
		$ret.=$line_begin.'<APPLET ID="'.$id.'" CODE="freemind.main.FreeMindApplet.class" ARCHIVE="lib/mindmap/freemindbrowser.jar" WIDTH="'.$width.'" HEIGHT="'.$height.'">'.$line_end;
		$ret.=$line_begin.'	<PARAM NAME="type" VALUE="application/x-java-applet;version=1.4">'.$line_end;
		$ret.=$line_begin.'	<PARAM NAME="scriptable" VALUE="false">'.$line_end;
		$ret.=$line_begin.'	<PARAM NAME="modes" VALUE="freemind.modes.browsemode.BrowseMode">'.$line_end;
		$ret.=$line_begin.'	<PARAM NAME="browsemode_initial_map" VALUE="'.$src.'">'.$line_end;
		$ret.=$line_begin.'	<PARAM NAME="initial_mode" VALUE="Browse">'.$line_end;
		$ret.=$line_begin.'	<PARAM NAME="selection_method" VALUE="selection_method_direct">'.$line_end;
	  $ret.=$line_begin.'</APPLET>'.$line_end;
		$ret.=$script_end;
	} else {
		$ret=$script_begin;
		$ret.=$line_begin.'<'.$script_tag.' type="text/javascript" src="lib/mindmap/flashobject.js"></'.$script_tag.'>'.$line_end;
		$ret.=$line_begin.'<div id="flashcontent_'.$id.'">'.$line_end;
		$ret.=$line_begin.'		  A problem occurred while trying to display the mindmap. '.$line_end;
		$ret.=$line_begin.'		  Check if both Flash plugin and Javascript are activated.'.$line_end;
		$ret.=$line_begin.'</div>'.$line_end;
		$ret.=$line_begin.'<'.$script_tag.' type="text/javascript">'.$line_end;
		$ret.=$line_begin.'		var '.$id.' = new FlashObject("lib/mindmap/visorFreemind.swf", "visorFreeMind", "'.$width.'", "'.$height.'", 6, "#9999ff");'.$line_end;
		$ret.=$line_begin.'		'.$id.'.addParam("quality", "high");'.$line_end;
		$ret.=$line_begin.'		'.$id.'.addParam("bgcolor", "#ffffff");'.$line_end;
		$ret.=$line_begin.'		'.$id.'.addVariable("openUrl", "_blank");'.$line_end;
		$ret.=$line_begin.'		'.$id.'.addVariable("initLoadFile", "'.$src.'");'.$line_end;
		$ret.=$line_begin.'		'.$id.'.addVariable("startCollapsedToLevel","5");'.$line_end;
		$ret.=$line_begin.'		'.$id.'.write("flashcontent_'.$id.'");'.$line_end;
		$ret.=$line_begin.'</'.$script_tag.'>'.$line_end;
	$ret.=$script_end;
	}
	return $ret;
}
?>
