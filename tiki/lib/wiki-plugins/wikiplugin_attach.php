<?php
// $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_attach.php,v 1.5 2004-02-21 16:30:15 mose Exp $
// Displays an attachment or a list of attachments
// Parameters: ln => line numbering (default false)
// Example:
// {ATTACH(id=>22)}
//  comment about attachment, that will be display under attachment informations
// {ATTACH}
function wikiplugin_attach_help() {
	$help = tra("Displays an attachment or a list of them").": ";
	$help.= "~np~{ATTACH(num=1,showdesc=0|1,dls=0|1,icon=0|1)}".tra("comment")."{ATTACH}~/np~ ";
	$help.= "num is optionnal and is the order number of the attachment in the list. If not provided a list of all attachments is displayed.";
	return tra($help);
}

function wikiplugin_attach($data, $params) {
	global $atts;
	global $mimeextensions;
	extract ($params);
	$loop = array();
	if (!isset($num)) $num = 0;
	if (!$atts['cant']) {
		return "''".tra('no attachment on this page.')."''";
	} elseif ($num > 0 and $num < ($atts['cant']+1)) {
		$loop[] = $num;
	} else {
		$loop = range(1,$atts['cant']);
	}
	
	$out = array();
	$separator = "\n";
	if ($data) {
		$out[] = $data;
	}
	foreach ($loop as $n) {
		$n--;
		$link = '<a href="tiki-download_wiki_attachment.php?attId='.$atts['data'][$n]['attId'].'" class="link"';
		$link.= ' title="';
		if (isset($showdesc)) {
			$link.= $atts['data'][$n]['filename'];
		} else {
			$link.= $atts['data'][$n]['comment'];
		}
		if (isset($dls)) {
			$link.= " ".$atts['data'][$n]['downloads'];
		}
		$link.= '">';
		if (isset($icon)) {
			if (!isset($mimeextensions)) {
				require("lib/mime/mimeextensions.php");
			}
			$ext = $atts['data'][$n]['filetype'];
			if (isset($mimeextensions["$ext"]) and (is_file("img/icn/".$mimeextensions["$ext"].".gif"))) {
				$link.= '<img src="img/icn/'.$mimeextensions["$ext"].'.gif" border="0" />&nbsp;';
			} else {
				$link.= '<img src="img/icn/else.gif" border="0" />&nbsp;';
			}
		}
		if (isset($showdesc)) {
			$link.= strip_tags($atts['data'][$n]['comment']);
		} else {
			$link.= strip_tags($atts['data'][$n]['filename']);
		}
		$link.= '</a>';
		$out[] = $link;
	}
	$data = implode($separator,$out);
	return $data;
}

?>
