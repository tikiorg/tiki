<?
require_once ('tiki-setup.php');

$query = '';
$show = array();
if (isset($_REQUEST['countries'])) {
	$query = "select value as country, count(*) as total from tiki_user_preferences where prefName='country' group by prefName, value order by total desc";
	$result = $tikilib->query($query,array());
	while ($res = $result->fetchRow()) $show[] = $res;
} elseif (isset($_REQUEST['themes'])) {
	$query = "select value as theme, count(*) as total from tiki_user_preferences where prefName='theme' group by prefName, value order by total desc";
	$result = $tikilib->query($query,array());
	while ($res = $result->fetchRow()) $show[] = $res;
} elseif (isset($_REQUEST['languages'])) {
	$query = "select value as language, count(*) as total from tiki_user_preferences where prefName='language' group by prefName, value order by total desc";
	$result = $tikilib->query($query,array());
	while ($res = $result->fetchRow()) $show[] = $res;
} elseif (isset($_REQUEST['timezones'])) {
	$query = "select value as timezone, count(*) as total from tiki_user_preferences where prefName='display_timezone' group by prefName, value order by total desc";
	$result = $tikilib->query($query,array());
	while ($res = $result->fetchRow()) $show[] = $res;
}

$smarty->assign('query',$query);
$smarty->assign('show',$show);

$smarty->assign('mid', 'stats.tpl');
$smarty->display("tiki.tpl");
?>
