<?php # $Header: /cvsroot/tikiwiki/tiki/tiki-pagesetup.php,v 1.4 2003-03-30 20:50:39 lrargerich Exp $

$check = isset($page);

$ppps = Array(
'tiki_p_view',
'tiki_p_edit',
'tiki_p_rollback',
'tiki_p_remove',
'tiki_p_rename',
'tiki_p_lock',
'tiki_p_view_attachments'
);

// If we are in a page then get individual permissions
foreach($allperms as $vperm) {
  $perm=$vperm["permName"];
  if(in_array($perm,$ppps)) {
    if($tiki_p_admin != 'y') {
      // Check for individual permissions if this is a page
      if($check) {
        if($userlib->object_has_one_permission($page,'wiki page')) {
          if($userlib->object_has_permission($user,$page,'wiki page',$perm)) {
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