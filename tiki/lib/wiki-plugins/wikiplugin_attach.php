<?php
// $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_attach.php,v 1.19.2.3 2008-02-27 15:18:49 nyloth Exp $
// Displays an attachment or a list of attachments
// Currently works with wiki pages and tracker items.
// Parameters:
// 	See help text.
// Examples:
// 	{ATTACH(name=>foobar.zip)}  -- make link to foobar.zip
// 	{ATTACH(showdesc=>1,bullets=>1)} -- make links to all attachments as a bullet list
// 	{ATTACH(all=>1,bullets=>1)} -- make links to *all* attachments in the whole wiki as a bullet list

function wikiplugin_attach_help() {
    $help  = tra("Displays an attachment or a list of them");
    $help .= "<br />";
    $help .= "~np~{ATTACH(name|file=file.ext, page=WikiPage, showdesc=0|1, bullets=>0|1, image=>0|1, inline=0|1, id=1|num=1, dls=0|1, icon=0|1,)}" . tra("Comment") . "{ATTACH}~/np~ ";
    $help .= "<br />";
    $help .= "name => " . tra("Gives the name of the attached file to link to");
    $help .= "<br />";
    $help .= "file =>" . tra("Same as name");
    $help .= "<br />";
    $help .= "page => " . tra("Gives the name of another page the attached file is on. The file on that page is linked to instead. Only works with wiki pages");
    $help .= "<br />";
    $help .= "showdesc => " . tra("Shows the description as the link text instead of the file name");
    $help .= "<br />";
    $help .= "bullets => " . tra("Makes the list of attachments a bulleted list");
    $help .= "<br />";
    $help .= "image =>" . tra("Says that this file is an image, and should be displayed inline using the img tag");
    $help .= "<br />";
    $help .= "inline =>" . tra("Puts the stuff between {ATTACH} tags as the link text instead of the file name or description");
    $help .= "<br />";
    $help .= "all => " . tra("Shows all attachments from the whole wiki");
    $help .= "<br />";
    $help .= "num => " . tra("Gives the number, in the list of attachments, of the attachment to link to");
    $help .= "<br />";
    $help .= "id => " . tra("Gives the actual id of the attachment to link in. You probably should never use this");
    $help .= "<br />";
    $help .= "dls => " . tra("Puts the number of downloads in the alt comment");
    $help .= "<br />";
    $help .= "icon =>" . tra("Shows a file icon");

    return $help;
}

function wikiplugin_attach($data, $params) {
    global $atts;
    global $mimeextensions;
    global $wikilib;
    global $tikilib;
    global $user;
    include_once('lib/wiki/wikilib.php');

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
	if( strstr( $_REQUEST["SCRIPT_NAME"], "tiki-index.php" ) || strstr( $_REQUEST["SCRIPT_NAME"], "tiki-editpage.php" ) || strstr( $_REQUEST["SCRIPT_NAME"], 'tiki-pagehistory.php') ) {
	    $atts_item_name = $_REQUEST["page"];
	    $atts = $wikilib->list_wiki_attachments($atts_item_name,0,-1,'created_desc','');
	}
    }

    # Save for restoration before this script ends
    $old_atts = $atts;
    $url = '';

    if( isset( $page ) ) {
	if($tikilib->user_has_perm_on_object($user,$page,'wiki page','tiki_p_wiki_view_attachments') || $tikilib->user_has_perm_on_object($user, $_REQUEST['page'], 'wiki page', 'tiki_p_wiki_admin_attachments')) {
	    $atts = $wikilib->list_wiki_attachments($page,0,-1,'created_desc','');
          $url = "&amp;page=$page";
 	}
    }

	if (isset($all)) {
		$atts = $wikilib->list_all_attachements(0,-1,'page_asc','');
	} elseif (isset($page)) {
		if($tikilib->user_has_perm_on_object($user,$page,'wiki page','tiki_p_wiki_view_attachments') || $tikilib->user_has_perm_on_object($user, $_REQUEST['page'], 'wiki page', 'tiki_p_wiki_admin_attachments')) {
			$atts = $wikilib->list_wiki_attachments($page,0,-1,'created_desc','');
			$url = "&amp;page=$page";
		}
	}

    if( ! array_key_exists( "cant", $atts ) )
    {
    	if( array_key_exists( "data", $atts ) )
	{
	    $atts['cant'] = count($atts["data"]);
	} else {
	    $atts['cant'] = 0;
	    $atts["data"] = "";
	}
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
	return "''".tra('No such attachment on this page')."''";
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
	if ( (!$name and !$id) or $id == $atts['data'][$n]['attId'] or $name == $atts['data'][$n]['filename'] )
	{
	    $link = "";
	    if( isset( $bullets ) && $bullets )
	    {
		$link .= "<li>";
	    }

	    if(isset($image) and $image )
	    {
		$link = '<img src="tiki-download_wiki_attachment.php?attId='.$atts['data'][$n]['attId'].$url.'" class="wiki"';
		$link.= ' alt="';
		if (isset($showdesc)) {
		    $link.= $atts['data'][$n]['filename'];
		} else {
		    $link.= $atts['data'][$n]['comment'];
		}
		if (isset($dls)) {
		    $link.= " ".$atts['data'][$n]['hits'];
		}
		$link.= '"/>';
	    } else {
		$link = '<a href="tiki-download_wiki_attachment.php?attId='.$atts['data'][$n]['attId'].$url.'" class="wiki"';
		$link.= ' title="';

		if (isset($showdesc)) {
		    $link.= $atts['data'][$n]['filename'];
		} else {
		    $link.= $atts['data'][$n]['comment'];
		}
		if (isset($dls)) {
		    $link.= " ".$atts['data'][$n]['hits'];
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
			$string = strtolower(substr($atts['data'][$n]['filename'], strlen($atts['data'][$n]['filename'])-3));
			if (is_file("img/icn/".$string.".gif"))
				$link.= '<img src="img/icn/'.$string.'.gif" border="0" />&nbsp;';
			else
				$link.= '<img src="img/icn/else.gif" border="0" />&nbsp;';
		    }
		}

		if (isset($showdesc) && !empty($atts['data'][$n]['comment'])) {
		    $link.= strip_tags($atts['data'][$n]['comment']);
		} else if( isset( $inline ) ) {
		    $link.= $data;
		} else {
		    $link.= strip_tags($atts['data'][$n]['filename']);
		}

		$link.= '</a>';

		if( isset( $all ) )
		{
		    $link .= " From Page ((" . $atts['data'][$n]['page'] . "))";
		}

	    }

	    if( isset( $bullets ) && $bullets )
	    {
		$link .= "</li>";
	    }

	    $out[] = $link;
	}
    }

    $separator = " ";

    if( isset( $inline ) ) {
    	if( array_key_exists( 1, $out ) ) {
	    $data = $out[1];
	} else {
	    $data = "";
	}
    } else {
	$data = implode($separator,$out);
    }

    if( isset( $bullets ) && $bullets )
    {
	$data = "<ul>".$data."</ul>";
    }

    if( strlen( $data ) == 0 )
    {
	$data = "<strong>".tra('No such attachment on this page')."</strong>";
    }

    $atts = $old_atts;

    return $data;
}

?>
