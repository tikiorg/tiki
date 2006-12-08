<?php
/* $Header: /cvsroot/tikiwiki/_mods/wiki-plugins/mindmap/wiki-plugins/wikiplugin_mindmap.php,v 1.7 2006-12-08 01:04:22 uid138927 Exp $
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
	$help.="src=>99|./lib/mindmap/wikiplugin-mindmap.mm";
	$help.="[,plugin=>flash|java]";
	$help.="[,mode=>inline|window|fullscreen]";
	$help.="[,width=>100%]";
	$help.="[,height=>400]";
	$help.=")}Mindmap Title{MINDMAP}~/np~";
	$help.= tra("^Parameters: key=>value,...\n");
	$help.= tra("|| __key__ | __default__ | __comments__\n");
	$help.= tra("src |  ./lib/mindmap/wikiplugin-mindmap.mm |  The location of the mindmap. This can be either an absolute URL, a relative URL, or the numeric ID of a wiki attachment. Wiki attachments currently work with the flash plugin only. A relative URL must begin with ./\n");
	$help.="plugin | flash | This selects the type of plugin to display the mindmap. Valid values are 'flash' or 'java'. Your browser must have a compatible flash plugin or java runtime.\n";
	$help.="mode  | inline | Valid values are 'inline' to embed in wiki page, 'window' to provide a link to view in a popup window) or 'fullscreen' to provide a link to view fullscreen in a new window (Alt-F4 to exit).\n";
	$help.="width  | 100% | numeric or percentage width\n";
	$help.="height | 400  | numeric or percentage height\n";
	$help.= "||^";
	return $help;
}
function fetch_mindmap($id,$src,$plugin,$mode,$title,$width,$height) {
	global $smarty;
	// snippets for wrapping script lines
	$prefix='';
	$suffix='';
	$line_begin='';
	$line_end='';
	if ($mode=="window" or $mode=="fullscreen") {
		$width="100%";
		$height="100%";
		// snippets to open a new window for writing 
		$prefix.="\n<script type='text/javascript'>";
		$prefix.="\nfunction func_".$id."() {";
		$prefix.="\n	".$id." = window.open('','".$id."'";
		if ($mode == "fullscreen") {
			$prefix.=",'fullscreen=yes,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no');";
		} else {
			$prefix.=");";
		}
		// snippets to wrap document.write() around script lines
		$line_begin.="	".$id.".document.write('";
		$line_end.="');";
		// closes the document
		$suffix.="\n	".$id.".document.close();";
		$suffix.="\n}";
		$suffix.="\n</script>";
	}
	// per flash technote http://www.adobe.com/cfusion/knowledgebase/index.cfm?id=tn_16417
	// flashvars is used to pass variables into a SWF object
	$flash_vars="openUrl=_blank&initLoadFile=".urlencode($src)."&startCollapsedToLevel=5";
	// assign smarty variables
	$smarty->assign('mode', $mode);
	$smarty->assign('plugin', $plugin);
	$smarty->assign('title', $title);
	$smarty->assign('line_begin', $line_begin);
	$smarty->assign('line_end', $line_end);
	$smarty->assign('width', $width);
	$smarty->assign('height', $height);
	$smarty->assign('src', $src);
	$smarty->assign('flash_vars', $flash_vars);
	// fetch the html
	$script = $smarty->fetch('wiki-plugins/wikiplugin_mindmap.tpl');
	if ($mode=="window" or $mode=="fullscreen") {
		// escape all slashes in the script body for document.write()
		$ret=$prefix.str_replace("/","\/",$script).$suffix;
	} else {
		$ret = $script;
	}
	return $ret;
}
function wikiplugin_mindmap($data, $params) {
	extract ($params, EXTR_SKIP);
// optional plugin parameter
  if (!isset($plugin)) {
		$plugin = "flash";
  }
// required parameters
	if (!isset($src)) {
		$src = "./lib/mindmap/wikiplugin-mindmap.mm";
	}
	if (is_numeric($src)) {
		if ($plugin=="java") {
			return tra("Wiki attachments currently work with the flash plugin only.");
		}
		$src = "./tiki-download_wiki_attachment.php?attId=".$src;
	}
// optional other parameters
  if (!isset($plugin)) {
		$plugin = "flash";
  }
  if (!isset($mode)) {
		$mode = "inline";
  }
  if (!isset($width)) {
		$width = "100%";
  }
  if (!isset($height)) {
		$height = 400;
  }
  if (!isset($data) or trim($data)=="") {
		$data = "Mind Map";
  }
	$ret="";
	// unique id permits multiple mindmaps in a single wiki page
	$id=uniqid("mm_");
	if ($mode=="window" or $mode=="fullscreen") {
		$ret=fetch_mindmap($id,$src,$plugin,$mode,$data,$width,$height);
	}
	$ret.=fetch_mindmap($id,$src,$plugin,"inline",$data,$width,$height);
	$ret="~pp~".$ret."~/pp~";
	if ($mode=="window" or $mode=="fullscreen") {
		// add a link to launch mindmap plugin in a separate window
		$ret.="\n".'<div align="right"><a href="javascript: func_'.$id.'()">View '.$data.' in '.$mode.'</a></div>';
	}
	return $ret;
}
?>
