<?php
// $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_attach.php,v 1.9 2005-03-12 16:50:00 mose Exp $
// Displays an attachment or a list of attachments
// Currently works with wiki pages and tracker items.
// Parameters: ln => line numbering (default false)
// 		inline => puts the stuff between {ATTACH} tags as the link text instead of the file name or description.
// 		showdesc => shows the description as the link text instead of the file name
// 		icon => shows a file icon
// 		dls => ????
// 		id => Gives the actual id of the attachment to link in.  Might as well just do a straight link in this case...
// 		num => Gives the number, in the list of attachments, of the attachment to link to
// 		name => Gives the name of the attached file to link to.
// 		file => Same as name.
// 		page => Gives the name of another page the attached file is on.  The file on that page is linked to instead.  Only works with wiki pages.
// Example:
// {ATTACH(name=>foobar.zip)}
//  comment about attachment, that will be display under attachment informations
// {ATTACH}
function wikiplugin_attach_help() {
    $help = tra("Displays an attachment or a list of them").": ";
    $help.= "~np~{ATTACH(name|file=file.ext,id=1|num=1,showdesc=0|1,dls=0|1,icon=0|1,inline=0|1)}".tra("comment")."{ATTACH}~/np~ ";
    $help.= tra("num is optional and is the order number of the attachment in the list. If not provided, a list of all attachments is displayed.  Inline makes the comment be the text of the link.");
    return $help;
}

function wikiplugin_attach($data, $params) {
    global $atts;
    global $mimeextensions;
    global $wikilib;
    global $tikilib;
    global $user;

    extract ($params,EXTR_SKIP);

    $loop = array();
		if (!isset($atts)) $atts = array();

    if( ! is_array( $atts ) || ! array_key_exists( "data", $atts ) || count( $atts["data"] ) < 1 )
    {
	# We're being called from a preview or something; try to build the atts ourselves.

	# See if we're being called from a tracker page.
	if( strstr( $_REQUEST["SCRIPT_NAME"], "tiki-view_tracker_item.php" ) )
	{
	    $atts_item_name = $_REQUEST["itemId"];

	# Get the tracker info.
	    $tracker_info = $trklib->get_tracker($atts_item_name);
	    $tracker_info = array_merge($tracker_info,$trklib->get_tracker_options($atts_item_name));

	    $attextra = 'n';

	    if (strstr($tracker_info["orderAttachments"],'|')) {
		$attextra = 'y';
	    }

	    $attfields = split(',',strtok($tracker_info["orderAttachments"],'|'));

	    $atts = $trklib->list_item_attachments($atts_item_name, 0, -1, 'comment_asc', '');
	}

	# See if we're being called from a wiki page.
	if( strstr( $_REQUEST["SCRIPT_NAME"], "tiki-index.php" ) || strstr( $_REQUEST["SCRIPT_NAME"], "tiki-editpage.php" ) )
	{
	    $atts_item_name = $_REQUEST["page"];

	    $atts = $wikilib->list_wiki_attachments($atts_item_name,0,-1,'created_desc','');
	}
    }

    # Save for restoration before this script ends
    $old_atts = $atts;

    if( isset( $page ) )
    {
	if($tikilib->user_has_perm_on_object($user,$page,'wiki page','tiki_p_wiki_view_attachments'))
	{

	    $atts = $wikilib->list_wiki_attachments($page,0,-1,'created_desc','');
	}
    }

    if( ! array_key_exists( "cant", $atts ) )
    {
	$atts['cant'] = count($atts["data"]);
    }


    if (!isset($num)) $num = 0;
    if (!isset($id)) {
	$id = 0;
    } else {
	$num = 0;
    }

    if( isset( $file ) )
    {
	$name = $file;
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

    $atts = $old_atts;

    return $data;
}

?>
