<?php
/*
 * $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins-dist/wikiplugin_alignedbox.php,v 1.1 2003-10-07 08:47:37 dcengija Exp $
 *
 * Tiki-Wiki ALIGNEDBOX plugin.
 * 
 * Description: Display the specified text in an aligned box, which
 * looks just as a sidenote in a newspapers. Based on BOX plugin.
 *
 * Author: Davor Cengija, dcengija@users.sourceforge.net
 * 
 * Syntax:
 * 
 *  {ALIGNEDBOX([title=>Title],[bg=>color],[align=>left|right(default)|center],[width=>num[%]])}
 *   Text inside box
 *  {ALIGNEDBOX}
 * 
 */

function wikiplugin_alignedbox_help() {
  return tra("Insert theme styled aligned box on wiki page");
}

function wikiplugin_alignedbox($data,$params) {
  /* set default values for some args */
  $title="Message box";

  extract($params);
  $w = (isset($width)) ? " width=$width" : "";
  $back =  (isset($bg)) ? " style='background:$bg'" : "";
  $al = (isset($align)) ? " align='$align'" : " align='right'";
  $begin="<table$w$al><tr><td><div class=cbox$back><div class=cbox-title>$title</div><div class=cbox-data$back>";
  $end="</div></div></td></tr></table>";

  return $begin . $data . $end;
}

?>
