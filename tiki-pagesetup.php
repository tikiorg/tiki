<?
if(isset($page)) {
  $check = true; 
} else {
  $check = false; 
}

$ppps = Array(
'tiki_p_view',
'tiki_p_edit',
'tiki_p_rollback',
'tiki_p_remove',
);

// If we are in a page then get individual permissions
foreach($allperms as $vperm) {
  $perm=$vperm["permName"];
  if(in_array($perm,$ppps)) {
    if($user != 'admin') {
      // Check for individual permissions if this is a page
      if($check) {
        if($userlib->object_has_one_permission($page,TIKI_PAGE_RESOURCE)) {
          if($userlib->object_has_permission($user,$page,TIKI_PAGE_RESOURCE,$perm)) {
            $$perm = 'y';
            $smarty->assign("$perm",'y');     
          } else {
            $$perm = 'n';
            $smarty->assign("$perm",'n');     
          }
        } 
      } 
    } else {
      $$perm = 'y';
      $smarty->assign("$perm",'y');     
    }
  }
}
?>