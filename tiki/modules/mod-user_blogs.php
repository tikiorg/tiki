<?php

$ranking = $tikilib->list_user_blogs($user, false);

$smarty->assign('modUserBlogs', $ranking);

?>