<?php

// PERMISSIONS: NEEDS p_admin to assign permissions to a page
if($tiki_p_admin != 'y') {
  $smarty->assign('msg',tra("You dont have permission to use this feature"));
  $smarty->display('error.tpl');
  die;
}






?>