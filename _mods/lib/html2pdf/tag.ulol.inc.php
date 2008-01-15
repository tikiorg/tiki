<?php
// $Header: /cvsroot/tikiwiki/_mods/lib/html2pdf/tag.ulol.inc.php,v 1.1 2008-01-15 09:21:13 mose Exp $

$g_list_item_nums = array();

function do_ulol_special(&$root) {
  global $g_list_item_nums;

  // Use 'start' attribute value
  $start = 1;
  if ($root->has_attribute('start')) {
    $start = $root->get_attribute('start');
  }

  array_unshift($g_list_item_nums,$start);

  return;
}

function do_ulol_special_post(&$root) {
  global $g_list_item_nums;
  array_shift($g_list_item_nums);

  return;
}

function get_list_item_num() {
  global $g_list_item_nums;
  return $g_list_item_nums[0];
}

function list_item_end() {
  global $g_list_item_nums;
  $g_list_item_nums[0]++;
}
?>