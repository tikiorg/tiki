<?php

if(isset($_REQUEST["userfilesprefs"])) {
  $tikilib->set_preference("uf_use_db",$_REQUEST["uf_use_db"]);
  $tikilib->set_preference("uf_use_dir",$_REQUEST["uf_use_dir"]);
  $tikilib->set_preference("userfiles_quota",$_REQUEST["userfiles_quota"]);
  $smarty->assign('uf_use_db',$_REQUEST["uf_use_db"]);
  $smarty->assign('uf_use_dir',$_REQUEST["uf_use_dir"]);
  $smarty->assign('userfiles_quota',$_REQUEST['userfiles_quota']);
}

?>
