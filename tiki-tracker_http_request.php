<?php

require_once('tiki-setup.php');
include_once('lib/trackers/trackerlib.php');

if ($prefs['feature_categories'] == 'y') {
	global $categlib;
	if (!is_object($categlib)) {
		include_once('lib/categories/categlib.php');
	}
}

if ($prefs['feature_trackers'] != 'y') {
	die;
}

$arrayTrackerId		= explode(',',$_GET["trackerIdList"]);
$arrayMandatory		= explode(',',$_GET["mandatory"]);
if (isset($_GET['selected']))
	$arraySelected		= explode(',',utf8_encode(rawurldecode($_GET["selected"])));
$arrayFieldlist		= explode(',',$_GET["fieldlist"]);
$arrayFilterfield	= explode(',',$_GET["filterfield"]);
$arrayStatus		= explode(',',$_GET["status"]);

header('Cache-Control: no-cache');

for ($index = 0; $index < count($arrayTrackerId); $index++)
{
	if ( ! $userlib->object_has_one_permission($arrayTrackerId[$index], 'tracker') && ($tiki_p_admin != 'y' && $prefs['feature_categories'] == 'y')) {
		$perms_array = $categlib->get_object_categories_perms($user, 'tracker', $arrayTrackerId[$index]);
   		if ($perms_array) {
   			$is_categorized = TRUE;
    		foreach ($perms_array as $perm => $value) {
    			$$perm = $value;
    		}
   		} else {
   			$is_categorized = FALSE;
   		}
		if ($is_categorized && isset($tiki_p_view_categorized) && $tiki_p_view_categorized != 'y') {
			die;
		}
	}

	if ($arrayMandatory[$index] == 'y') {
		echo "sel[$index][0] = new Option('','');\n";
	}

	// behaviour differ between smarty encoding and javascript encoding
	if ( ! isset($_GET['selected'])) {
		$selected = '';
		$filtervalue = utf8_encode(rawurldecode($_GET["filtervalue"]));
	} else {
		$selected = $arraySelected[$index];
		$filtervalue = $_GET["filtervalue"];
	}

	if ($filtervalue) {
		$xfields = $trklib->list_tracker_fields($arrayTrackerId[$index],0,-1,'name_asc','');
		foreach ($xfields["data"] as $idfi => $val)
		{
			if ($xfields["data"][$idfi]["fieldId"] == $arrayFieldlist[$index]) {
				$fid = $xfields["data"][$idfi]["fieldId"];
				$dfid = $idfi;
				break;
			}
		}

		$listfields = array();
		$listfields[$fid]['type'] = $xfields["data"][$dfid]["type"];
		$listfields[$fid]['name'] = $xfields["data"][$dfid]["name"];
		$listfields[$fid]['options'] = $xfields["data"][$dfid]["options"];
		$listfields[$fid]['options_array'] = split(',',$xfields["data"][$dfid]["options"]);
		$listfields[$fid]['isMain'] = $xfields["data"][$dfid]["isMain"];
		$listfields[$fid]['isTblVisible'] = $xfields["data"][$dfid]["isTblVisible"];
		$listfields[$fid]['isHidden'] = $xfields["data"][$dfid]["isHidden"];
		$listfields[$fid]['isSearchable'] = $xfields["data"][$dfid]["isSearchable"];
		$items = $trklib->list_items($arrayTrackerId[$index], 0, -1, '', $listfields, $arrayFilterfield[$index], $filtervalue, $arrayStatus[$index]);

		$isSelected = false;
		for ($i = 0; $i < $items["cant"]; $i++) {
			if ($selected == $items["data"][$i]['field_values'][0]['value']) {
				$selbool = "true,true";
				$isSelected = true;
			} else {
				$selbool = "false,false";
			}
			echo "sel[$index][$i+1]= new Option('" . $items["data"][$i]['field_values'][0]['value'] .
				 "','" . $items["data"][$i]['field_values'][0]['value'] . "'," . $selbool . ");\n";
		}
		if ( $isSelected == false && $selected != '') {
			echo "sel[$index][$i+1]= new Option('" . $selected . "','" . $selected . "',true,true);\n";
		}
	}
}
?>
