<?php
// Includes a tracker field
// Usage:
// {TRACKER()}{TRACKER}

function wikiplugin_trackerlist_help() {
	$help = tra("Displays the output of a tracker content").":\n";
	$help.= "~np~{TRACKERLIST(trackerId=>1,fields=>login:email:name)}Notice{TRACKERLIST}~/np~";
	return $help;
}
function wikiplugin_trackerlist($data, $params) {
	global $smarty, $trklib, $tikilib, $dbTiki, $userlib, $tiki_p_admin, $maxRecords, $_REQUEST, $tiki_p_view_trackers, $user;

	extract ($params);

	if (!isset($trackerId)) {
		$smarty->assign('msg', tra("missing tracker ID for plugin TRACKER"));
		return $smarty->fetch("error_simple.tpl");
	} else {

		require_once("lib/trackers/trackerlib.php");
		if (!isset($fields)) {
			$smarty->assign('msg', tra("missing fields list"));
			return $smarty->fetch("error_simple.tpl");
		} else {
			$listfields = split(':',$fields);
		}

		$tracker_info = $trklib->get_tracker($trackerId);
		$tracker_info = array_merge($tracker_info,$trklib->get_tracker_options($trackerId));
		$smarty->assign_by_ref('tracker_info', $tracker_info);
		
		$query_array = array();
		$quarray = array();
		parse_str($_SERVER['QUERY_STRING'],$query_array);
		
		if (!isset($showtitle)) {
			$showtitle = "n";
		}
		$smarty->assign_by_ref('showtitle', $showtitle);
		
		if (!isset($showlinks)) {
			$showlinks = "n";
		}
		$smarty->assign_by_ref('showlinks', $showlinks);
		
		if (!isset($showdesc)) {
			$showdesc = "n";
		}
		$smarty->assign_by_ref('showdesc', $showdesc);
		
		if (!isset($status)) {
			$status = "o";
		}
		$tr_status = $status;
		$smarty->assign_by_ref('tr_status', $tr_status);
			
		if (isset($_REQUEST['tr_sort_mode'])) {
			$query_array['tr_sort_mode'] = $_REQUEST['tr_sort_mode'];
			$sort_mode = $_REQUEST['tr_sort_mode'];
		} elseif (!isset($sort_mode)) {
			if (isset($tracker_info['defaultOrderKey'])) {
				$sort_mode = 'f_'.$tracker_info['defaultOrderKey'];
				if (isset($tracker_info['defaultOrderDir'])) {
					$sort_mode.= "_".$tracker_info['defaultOrderDir'];
				} else {
					$sort_mode.= "_asc";
				}
			} else {
				$sort_mode = '';
			}
		} 
		$tr_sort_mode = $sort_mode;
		$smarty->assign_by_ref('sort_mode', $tr_sort_mode);
		
		if (!isset($max)) {
			$max = $maxRecords;
		}

		if (isset($_REQUEST['tr_offset'])) {
			$query_array['tr_offset'] = $_REQUEST['tr_offset'];
			$tr_offset = $_REQUEST['tr_offset'];
		} else {
			$tr_offset = 0;
			$query_array['tr_offset'] = 0;
		}
		$smarty->assign_by_ref('tr_offset',$tr_offset);

		if (isset($_REQUEST["tr_initial"])) {
			$query_array['tr_initial'] = $_REQUEST['tr_initial'];
			$tr_initial = $_REQUEST["tr_initial"];
		} else {
			$tr_initial = '';
		}
		$smarty->assign_by_ref('tr_initial', $tr_initial);
		$smarty->assign('initials', split(' ','a b c d e f g h i j k l m n o p q r s t u v w x y z'));

		if (!isset($filterfield)) {
			$filterfield = '';
		}
		$smarty->assign_by_ref('filterfield',$filterfield);

		if (!isset($filtervalue)) {
			$filtervalue = '';
		}
		$smarty->assign_by_ref('filterfield',$filtervalue);

		$status_types = $trklib->status_types();
		$smarty->assign('status_types', $status_types);

		$allfields = $trklib->list_tracker_fields($trackerId, 0, -1, 'position_asc', '');

		for ($i = 0; $i < count($allfields["data"]); $i++) {
			if (in_array($allfields["data"][$i]['fieldId'],$listfields) and $allfields["data"][$i]['isPublic'] == 'y') {
				$passfields["{$allfields["data"][$i]['fieldId']}"] = $allfields["data"][$i];
			}
		}
		$smarty->assign_by_ref('fields', $passfields);
		
		if (count($passfields)) {
			$items = $trklib->list_items($trackerId, $tr_offset, $max, $tr_sort_mode, $passfields, $filterfield, $filtervalue, $tr_status, $tr_initial);
			
			//var_dump($items);

			$cant_pages = ceil($items["cant"] / $max);
			$smarty->assign_by_ref('cant_pages', $cant_pages);
			$smarty->assign('actual_page', 1 + ($tr_offset / $max));

			if ($items["cant"] > ($tr_offset + $max)) {
				$smarty->assign('tr_next_offset', $tr_offset + $max);
			} else {
				$smarty->assign('tr_next_offset', -1);
			}
			if ($tr_offset > 0) {
				$smarty->assign('tr_prev_offset', $tr_offset - $max);
			} else {
				$smarty->assign('tr_prev_offset', -1);
			}
			$smarty->assign_by_ref('items', $items["data"]);
			$smarty->assign('daformat', $tikilib->get_long_date_format()." ".tra("at")." %H:%M"); 
			
			$tracker = $tikilib->get_tracker($trackerId,0,-1);

			foreach ($query_array as $k=>$v) {
				$quarray[] = urlencode($k) ."=". urlencode($v);
			}
			if (is_array($quarray)) {
				$query_string = implode("&amp;",$quarray);
			} else {
				$quering_string = '';
			}
			$smarty->assign('query_string', $query_string);
			
			if (!$tracker) {
				$smarty->assign('msg', tra("Error in tracker ID"));
				return "~np~".$smarty->fetch("error_simple.tpl")."~/np~";
			} else {
				return "~np~".$smarty->fetch('tiki-plugin_trackerlist.tpl')."~/np~";
			}
		} else {
			$smarty->assign('msg', tra("No field indicated"));
			return "~np~".$smarty->fetch("error_simple.tpl")."~/np~";
		}
	}
	return $back;
}

?>
