<?php
if($feature_directory == 'y') {
  $ranking = $tikilib->dir_stats();
  $smarty->assign('modDirStats',$ranking);
}
?>