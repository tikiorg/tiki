<?php
// Initialization
require_once('tiki-setup.php');

if($feature_stats != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if($tiki_p_view_stats != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
}

$smarty->assign('pv_chart','n');
if(isset($_REQUEST["pv_chart"])) {
  $smarty->assign('pv_chart','y');
  $smarty->assign('days',$_REQUEST["days"]);
}

$smarty->assign('usage_chart','n');
if(isset($_REQUEST["chart"])) {
  $smarty->assign($_REQUEST["chart"]."_chart",'y');
}

$wiki_stats =  $tikilib->wiki_stats();
$smarty->assign_by_ref('wiki_stats',$wiki_stats);
$igal_stats = $tikilib->image_gal_stats();
$smarty->assign_by_ref('igal_stats',$igal_stats);
$fgal_stats = $tikilib->file_gal_stats();
$smarty->assign_by_ref('fgal_stats',$fgal_stats);
$cms_stats = $tikilib->cms_stats();
$smarty->assign_by_ref('cms_stats',$cms_stats);
$forum_stats = $tikilib->forum_stats();
$smarty->assign_by_ref('forum_stats',$forum_stats);
$blog_stats = $tikilib->blog_stats();
$smarty->assign_by_ref('blog_stats',$blog_stats);
$poll_stats = $tikilib->poll_stats();
$smarty->assign_by_ref('poll_stats',$poll_stats);
$faq_stats = $tikilib->faq_stats();
$smarty->assign_by_ref('faq_stats',$faq_stats);
$user_stats = $tikilib->user_stats();
$smarty->assign_by_ref('user_stats',$user_stats);
$site_stats = $tikilib->site_stats();
$smarty->assign_by_ref('site_stats',$site_stats);
$quiz_stats = $tikilib->quiz_stats();
$smarty->assign_by_ref('quiz_stats',$quiz_stats);


// Display the template
$smarty->assign('mid','tiki-stats.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>