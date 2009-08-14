<?php
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}
global $smarty, $tikilib, $user;

$smarty->assign('module_title', isset($module_params['title']) ? $module_params['title'] : tra('Articles'));
$urlParams = array(
	'topicId' => 'topic',
	'topic' => 'topicName',
	'categId' => 'categId',
	'type' => 'type',
	'lang' => 'lang',
	'start' => null,
	'sort' => null
);

foreach ( $urlParams as $p => $v ) {
	if ( isset($$p) ) continue;
	$$p = isset($module_params[$p]) ? $module_params[$p] : '';
}
if ( $start == '' ) $start = 0;
if ( $sort == '' ) $sort = 'publishDate_desc';

$ranking = $tikilib->list_articles($start, $module_rows, $sort, '', '', '', $user, $type, $topicId, 'y', $topic, $categId, '', '', $lang);

$smarty->assign_by_ref('urlParams', $urlParams);
$smarty->assign('modArticles', $ranking["data"]);
if (isset($module_params['title'])) {
	$smarty->assign('tpl_module_title', $module_params['title']);
}

