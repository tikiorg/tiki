<?php
function smarty_function_gallery($params, &$smarty)
{
    global $tikilib;
    extract($params);
    // Param = id

    if (empty($id)) {
        $smarty->trigger_error("assign: missing 'id' parameter");
        return;
    }
    $img = $tikilib->get_random_image($id);
?>
<center>
<table width="98%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td align=center>
<a href="tiki-browse_image.php?galleryId=<? echo $img['galleryId']; ?>&amp;imageId=<? echo $img['imageId']; ?>"><img alt="thumbnail" class="athumb" src="show_image.php?id=<? echo $img['imageId']; ?>&amp;thumb=1" /></a><br/>
<b><? echo $img['name']; ?></b><br>
<? if ($showgalleryname == 1) { ?><small>From <a href="tiki-browse_gallery.php?galleryId=<? echo $img['galleryId']; ?>"><? echo $img['gallery']; ?></a></small><? } ?>
</td>
</tr>
</table>
</center>
<?
}
?>
