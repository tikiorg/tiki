<?php # $Header: /cvsroot/tikiwiki/tiki/categorize_list.php,v 1.3 2003-03-21 13:42:26 lrargerich Exp $
include_once('lib/categories/categlib.php');
if($feature_categories == 'y') {
   $smarty->assign('cat_categorize','n');
   if(isset($_REQUEST["cat_categorize"])&&$_REQUEST["cat_categorize"]=='on') {
     $smarty->assign('cat_categorize','y');
   }
   $categories = $categlib->list_all_categories(0,-1,'name_asc','',$cat_type,$cat_objid);
   if(isset($_REQUEST["cat_categories"])&&isset($_REQUEST["cat_categorize"])&&$_REQUEST["cat_categorize"]=='on') {
     for($i=0;$i<count($categories["data"]);$i++) {
       if(in_array($categories["data"][$i]["categId"],$_REQUEST["cat_categories"])) {
         $categories["data"][$i]["incat"]='y';
       } else {
         $categories["data"][$i]["incat"]='n';
       }
     }
   }
   $smarty->assign_by_ref('categories',$categories["data"]);
}
?>
