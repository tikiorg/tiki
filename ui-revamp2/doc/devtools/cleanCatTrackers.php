<?php
/* This batch rebuilds the value set in the database for a tracker item.
 * The value will be the value of the first main field of the item
 * This corrects a previous bug that was putting the field name
 ****** This batch needs to be copy in the tiki root to work
 */
require_once('tiki-setup.php');
include_once ('lib/trackers/trackerlib.php');
include_once ('lib/categories/categlib.php');

if (isset($_REQUEST['debug']) and $_REQUEST['debug'] == 'n')
	$debug = 'n';
else {
	$debug = 'y';
	echo "Debug mode: to run the program give the parameter debug=n<br />";
}
if ($tiki_p_admin !="y") {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}

// collect the Ismain field for each tracker
$mainFields = array();
$query = "select * from `tiki_tracker_fields`where `isMain`=? order by `position`desc";
$result = $tikilib->query($query, array('y'));
while ($res = $result->fetchRow()) {
	if (empty($mainFields[$res['trackerId']])) {
		$mainFields[$res['trackerId']] = $res['fieldId'];
		echo "Tracker: ".$res['trackerId']." - main field: ".$res['fieldId']."<br>";
	} else {
		echo "This tracker ".$res['trackerId']." has 2 main fields. Please check that the field ".$mainFields[$res['trackerId']]." is the one you want to populate the categories instead of ".$res['fieldId']."<br>";
	}
}


$query = "select * from `tiki_categorized_objects` where `type` like ?";
$result = $tikilib->query($query, array("tracker %"));
while ($res = $result->fetchRow()) {
	$trackerId = str_replace("tracker ", "", $res['type']);
	$itemId = $res["objId"];
	$query = "select `value` from `tiki_tracker_item_fields` where `itemId`=? and `fieldId`=?";
	if (empty($mainFields[$trackerId])) {
		echo "Please choose a main value for tracker $trackerId<br>";
		continue;
	}
	$mainValue = trim($tikilib->getOne($query, array($itemId, $mainFields[$trackerId])));
	if (!empty($mainValue) && $mainValue != trim($res["name"])) {
		$query = "update `tiki_categorized_objects` set `name`=? where objId=? and type=?";
		if ($debug == 'n') {
			$result2 = $tikilib->query($query, array($mainValue, $itemId, "tracker ".$trackerId));
		}
		echo "Update tracker $trackerId for item $itemId with value $mainValue instead of ".$res['name']."<br>";
	} elseif (empty($mainValue)) {
		if ($trklib->get_tracker_item($itemId))
			echo "No value for tracker: $trackerId, item: <a href='tiki-view_tracker_item.php?trackerId=$trackerId&itemId=$itemId'>$itemId</a><br>";
		else {
			echo "Obsolete categorised object $trackerId - $itemId<br>";
			if ($debug == 'n')
				$categlib->uncategorize_object("tracker $trackerId", $itemId);
		}
	}
	if (preg_match('/itemId=0$/', $res['href'])) {
		$href = str_replace('itemId=0', 'itemId='.$itemId, $res['href']);
		echo "href:$trackerId - $itemId<br>";
		if ($debug == 'n') {
			$query = "update `tiki_categorized_objects` set `href`=? where `catObjectId`=?";
			$tikilib->query($query, array($href, $res['catObjectId']));
		}
	}
}

?>
