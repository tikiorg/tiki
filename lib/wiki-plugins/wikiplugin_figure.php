<?php
/**
 * Emulates the new HTML5 <figure> and <figcaption> tags for backwards compatability with HTML4
 * and XML 1.1.
 *
 * This plugin assumes that images to be displayed have been saved into a file gallery.
 *
 * @usage preparing pages for ePub publication 
 *
 */


function wikiplugin_figure_help() {
        return tra("Emulate HTML5's new &#60;figure&#62; and &#60;figcaption&#62; tags");
}

/*
 * the "alt" attribute is required on <img> tags for XHTML compilance,
 * but it is legal for it to be empty
 *
 * @params $img // the image to be displayed
 * @params $caption // the caption to be displayed
 * @params $txt // optional alternate text
 *
 * @return $box // the populated container
 */
function wikiplugin_figure($img,$caption,$txt=null)
{
	$box = '';
if(isset($txt)) {$alt="\"".$txt."\"";} else {$alt="\"\"";}
// build the container

	$box = "<div class=\"figure\">";
    $box .= "  <img src=\"".$img."\" alt=".$alt." />";
    $box .= "  <p class=\"figcaption\">" . $caption . "</p>";
    $box .= "</div>";


	return $box;
}

?>
