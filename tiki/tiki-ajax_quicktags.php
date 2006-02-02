<?
//this script may only be included - so its better to die if called directly.

require_once("tiki-setup.php");

if ($tiki_p_admin != 'y') {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}

require_once('lib/cpaint/cpaint2.inc.php');
require_once ('lib/quicktags/quicktagslib.php');

function list_objects($category, $offset, $sort_mode = 'taglabel_asc', $find='') {
    global $quicktagslib, $cp;
    global $maxRecords;

	$objects = $quicktagslib->list_quicktags($offset, $maxRecords, $sort_mode, $find, $category);

    for ($i=0; $i < sizeof($objects['data']); $i++) {
	$obj = $objects['data'][$i];

	$ajaxObj =& $cp->add_node('object');
	$fields = array('tagId','taglabel','taginsert','tagicon','tagcategory');
	foreach ($fields as $f) {
	    $r =& $ajaxObj->add_node($f);
	    $r->set_id($f . '_' . $i);
	    $r->set_data($obj[$f]);
	}
    }
    $ajaxObj =& $cp->add_node('cant');
    $ajaxObj->set_data($objects['cant']);
}

$cp = new cpaint();
$cp->register('list_objects');
$cp->start();
$cp->return_data();

?>
