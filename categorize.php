<?php
  if($feature_categories == 'y') {
    $smarty->assign('cat_categorize','n');
    if(isset($_REQUEST["cat_categorize"])&&$_REQUEST["cat_categorize"]=='on') {
      $smarty->assign('cat_categorize','y');
    }
  
    if(isset($_REQUEST["cat_categories"])) {
      if(isset($_REQUEST["cat_categorize"])&&$_REQUEST["cat_categorize"]=='on') {
        $tikilib->uncategorize_object($cat_type,$cat_objid);
        foreach($_REQUEST["cat_categories"] as $cat_acat) {
          if($cat_acat) {
            $catObjectId=$tikilib->is_categorized($cat_type,$cat_objid);
            if(!$catObjectId) {
              // The object is not cateorized  
              $catObjectId = $tikilib->add_categorized_object($cat_type,$cat_objid,$cat_desc,$cat_name,$cat_href);
            }
            $tikilib->categorize($catObjectId,$cat_acat);
          }
        }
      }
    }
    $categories = $tikilib->list_all_categories(0,-1,'name_asc','',$cat_type,$cat_objid);
    $smarty->assign_by_ref('categories',$categories["data"]);
  }
?>