<?php
  include_once('lib/categories/categlib.php');
  if($feature_categories == 'y') {
    $smarty->assign('cat_categorize','n');
    if(isset($_REQUEST["cat_categorize"])&&$_REQUEST["cat_categorize"]=='on') {
      $smarty->assign('cat_categorize','y');
    }
  
    if(isset($_REQUEST["cat_categories"])) {
      if(isset($_REQUEST["cat_categorize"])&&$_REQUEST["cat_categorize"]=='on') {
        $categlib->uncategorize_object($cat_type,$cat_objid);
        foreach($_REQUEST["cat_categories"] as $cat_acat) {
          if($cat_acat) {
            $catObjectId=$categlib->is_categorized($cat_type,$cat_objid);
            if(!$catObjectId) {
              // The object is not cateorized  
              $catObjectId = $categlib->add_categorized_object($cat_type,$cat_objid,$cat_desc,$cat_name,$cat_href);
            }
            $categlib->categorize($catObjectId,$cat_acat);
          }
        }
      }
    }
    $categories = $categlib->list_all_categories(0,-1,'name_asc','',$cat_type,$cat_objid);
    $smarty->assign_by_ref('categories',$categories["data"]);
  }
?>