<?php
if($feature_directory == 'y') {
  $ranking = $tikilib->dir_list_all_valid_sites2(0,$module_rows,'hits_desc','');
  $smarty->assign('modTopdirSites',$ranking["data"]);
}
?>