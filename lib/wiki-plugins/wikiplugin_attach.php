<?php
// $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_attach.php,v 1.8 2005-01-22 22:55:56 mose Exp $
// Displays an attachment or a list of attachments
// Parameters: ln => line numbering (default false)
// 		inline => puts the stuff between {ATTACH} tags as the link text instead of the file name or description.
// 		showdesc => shows the description as the link text instead of the file name
// 		icon => shows a file icon
// 		dls => ????
// 		id => Gives the actual id of the attachment to link in.  Might as well just do a straight link in this case...
// 		num => Gives the number, in the list of attachments, of the attachment to link to
// 		name => Gives the name of the attached file to link to.
// Example:
// {ATTACH(name=>foobar.zip)}
//  comment about attachment, that will be display under attachment informations
// {ATTACH}
function wikiplugin_attach_help() {
    $help = tra("Displays an attachment or a list of them").": ";
    $help.= "~np~{ATTACH(name=file.ext|id=1|num=1,showdesc=0|1,dls=0|1,icon=0|1,inline=0|1)}".tra("comment")."{ATTACH}~/np~ ";
    $help.= tra("num is optional and is the order number of the attachment in the list. If not provided, a list of all attachments is displayed.  Inline makes the comment be the text of the link.");
    return $help;
}

function wikiplugin_attach($data, $params) {
    global $atts;
    global $mimeextensions;
    extract ($params,EXTR_SKIP);
    $loop = array();
    if (!isset($num)) $num = 0;
    if (!isset($id)) {
	$id = 0;
    } else {
	$num = 0;
    }

    if( isset( $name ) )
    {
	$id = 0;
	$num = 0;
    } else {
	$name = '';
    }

    if (!$atts['cant']) {
	return "''".tra('no such attachment on this page')."''";
    } elseif ($num > 0 and $num < ($atts['cant']+1)) {
	$loop[] = $num;
    } else {
	$loop = range(1,$atts['cant']);
    }

    $out = array();
    if ($data) {
	$out[] = $data;
    }
    foreach ($loop as $n) {
	$n--;
	if ( (!$name and !$id) or $id == $atts['data'][$n]['attId'] or $name == $atts['data'][$n]['filename'] ) {
	    $link = '<a href="tiki-download_wiki_attachment.php?attId='.$atts['data'][$n]['attId'].'" class="wiki"';
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
	    } else if( isset( $inline ) ) {
		$link.= $data;
	    } else {
		$link.= strip_tags($atts['data'][$n]['filename']);
	    }
	    $link.= '</a>';
	    $out[] = $link;
	}
    }

    $separator = " ";

    if( isset( $inline ) ) {
	$data = $out[1];
    } else {
	$data = implode($separator,$out);
    }

    if( strlen( $data ) == 0 )
    {
	$data = "<strong>".tra('no such attachment on this page')."</strong>";
    }

    return $data;
}

?>
