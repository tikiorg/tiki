<?php

if(isset($_REQUEST["trkset"])) {
  $tikilib->set_preference('t_use_db',$_REQUEST["t_use_db"]);
  $tikilib->set_preference('t_use_dir',$_REQUEST["t_use_dir"]);
  $smarty->assign('t_use_db',$_REQUEST["t_use_db"]);
  $smarty->assign('t_use_dir',$_REQUEST["t_use_dir"]);
}

?>
