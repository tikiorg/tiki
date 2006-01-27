<?
//this script may only be included - so its better to die if called directly.

require_once("tiki-setup.php");

if ($feature_freetags != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_freetags");

	$smarty->display("error.tpl");
	die;
}

require_once('lib/cpaint/cpaint2.inc.php');
require_once ('lib/freetag/freetaglib.php');

function list_objects($tag, $type, $find='') {
    global $freetaglib, $cp;
    
    $objects = $freetaglib->get_objects_with_tag($tag, $type, '', 0, -1, $find);

    for ($i=0; $i < sizeof($objects['data']); $i++) {
	$obj = $objects['data'][$i];

	$ajaxObj =& $cp->add_node('object');
	$fields = array('type','description','name','href');
	foreach ($fields as $f) {
	    $r =& $ajaxObj->add_node($f);
	    $r->set_id($f . '_' . $i);
	    $r->set_data($obj[$f]);
	}
    }
}

$cp = new cpaint();
$cp->register('list_objects');
$cp->start();
$cp->return_data();

?>
