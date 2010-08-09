<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$dir_level = 4;
require_once('../../../../tiki-setup.php');

$smarty->template_dir = dirname(__FILE__).'/templates/';

if (!isset($_REQUEST["offset"])) {
  $offset = 0;
} else {
  $offset = $_REQUEST["offset"];
} 
$smarty->assign('offset', $offset);

if (isset($_REQUEST["find"])) {
  $find = strip_tags($_REQUEST["find"]);
} else {
  $find = '';
}
$smarty->assign('find', $find);
$maxRecords = 10;

$listpages = $tikilib->list_pages($offset, $maxRecords, 'pageName_asc', $find);

$cant_pages = ceil($listpages["cant"] / $maxRecords);
$smarty->assign('cant_pages', $cant_pages);

$smarty->assign('actual_page', 1 + ($offset / $maxRecords));
if ($listpages["cant"] > ($offset + $maxRecords)) {
  $smarty->assign('next_offset', $offset + $maxRecords);
} else {
  $smarty->assign('next_offset', -1);
} 

if ($offset > 0) {
  $smarty->assign('prev_offset', $offset - $maxRecords);
} else {
  $smarty->assign('prev_offset', -1);
} 

$smarty->assign('listpages',$listpages['data']);
$smarty->display('fck_tikilink.tpl');
