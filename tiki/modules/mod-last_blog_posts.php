<?php

$ranking = $tikilib->list_posts(0, $module_rows, 'created_desc', '');

$smarty->assign('modLastBlogPosts', $ranking["data"]);

?>