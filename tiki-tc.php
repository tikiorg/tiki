<?php
if($feature_theme_control == 'y') {
// defined: $cat_type and cat_objid
// search for theme for $cat_type
// then search for theme for md5($cat_type.cat_objid)


include_once('lib/themecontrol/tcontrol.php');
include_once('lib/categories/categlib.php');

//SECTIONS
if(isset($section)) {
  $tc_theme = $tcontrollib->tc_get_theme_by_section($section);
}

// CATEGORIES
if(isset($cat_type)&&isset($cat_objid)) {

  $tc_categs = $categlib->get_object_categories($cat_type,$cat_objid);
  $tc_theme = '';

  if(count($tc_categs)) {
    $tc_theme = $tcontrollib->tc_get_theme_by_categ($tc_categs[0]);
  }
}  




//Objects
if(isset($cat_type)&&isset($cat_objid)) {
  $tc_theme = $tcontrollib->tc_get_theme_by_object($cat_type,$cat_objid);
}

if($tc_theme) {
  $style = $tc_theme;
  $tc_parts = explode('.',$style);
  $style_base = $tc_parts[0];
  $smarty->assign('style',$style);
  $smarty->assign('style_base',$style_base);
}


}
?>