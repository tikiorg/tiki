<?php
/* $Header: /cvsroot/tikiwiki/tiki/lib/smarty_tiki/function.gallery.php,v 1.9 2005-01-22 22:55:52 mose Exp $ */
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_gallery($params, &$smarty)
{
    global $tikilib;
    global $dbTiki;
    global $imagegallib;
    include_once('lib/imagegals/imagegallib.php');
    extract($params);
    // Param = id

    if (empty($id)) {
        $smarty->trigger_error("assign: missing 'id' parameter");
        return;
    }
    $img = $tikilib->get_random_image($id);
    print('<div style="text-align: center">');
    if ($hidelink != 1) {
        print('<a href="tiki-browse_image.php?galleryId='.$img['galleryId'].'&amp;imageId='.$img['imageId'].'');
		$scale = $imagegallib->get_gallery_next_scale($id);
		if ($scale['xsize']!=0) print('&amp;scaled&amp;xsize='.$scale['xsize'].'&amp;ysize='.$scale['ysize'].'');
        print('">');
     }
    print ('<img alt="thumbnail" class="athumb" src="show_image.php?id='.$img['imageId'].'&amp;thumb=1" />');
    if ($hidelink !=1) {
        print('</a>');
    }
    if ($hideimgname !=1)  {  
    	  print('<br /><b>'.$img['name'].'</b>');
    }
    if ($showgalleryname == 1) {
        print('<br /><small>'.tra("Gallery").': <a href="tiki-browse_gallery.php?galleryId='.$img['galleryId'].'">'.$img['gallery'].'</a></small>');
    } 
    print('</div>');
}    
?>
