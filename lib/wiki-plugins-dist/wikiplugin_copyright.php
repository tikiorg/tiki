<?php

require_once('lib/tikilib.php');
require_once('tiki-setup.php');
require_once("lib/copyrights/copyrightslib.php");

// Insert copyright notices
// Usage:
// {COPYRIGHT()}
// text
// ~title~ &copy; ~year~ ; ~authors~
// text
// {COPYRIGHT}

function wikiplugin_copyright($data,$params) {
  global $dbTiki;
  $copyrightslib = new CopyrightsLib($dbTiki);

  if( !isset($_REQUEST['copyrightpage']) ) {
  	return '';
  }

  //extract($params);
  $result='';

  $copyrights = $copyrightslib->list_copyrights($_REQUEST['copyrightpage']);

  for ($i = 0; $i < $copyrights['cant']; $i++) {
    $notice = str_replace("~title~", $copyrights['data'][$i]['title'], $data);
    $notice = str_replace("~year~", $copyrights['data'][$i]['year'], $notice);
    $notice = str_replace("~authors~", $copyrights['data'][$i]['authors'], $notice);
    $result=$result.$notice;
  }

  return $result;
}
?>