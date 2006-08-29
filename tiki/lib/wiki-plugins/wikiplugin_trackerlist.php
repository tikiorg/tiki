<?php

//
// TODO : 
// ----------
// - filtrage avec expression exacte
//

function wikiplugin_trackerlist_help() {
	$help = tra("Displays the output of a tracker content, fields are indicated with numeric ids.").":\n";
	$help.= "~np~{TRACKERLIST(trackerId=>1,fields=>2:4:5, showtitle=>y|n, showlinks=>y|n, showdesc=>y|n, showinitials=>y|n, showstatus=>y|n, status=>o|p|c|op|oc|pc|opc, sort_mode=>, max=>, filterfield=>, filtervalue=>, exactvalue=>, checkbox=>fieldId/name/title/submit/action/tpl)}Notice{TRACKERLIST}~/np~";
	return $help;
}

function wikiplugin_trackerlist($data, $params) {
	global $smarty, $trklib, $tikilib, $dbTiki, $userlib, $tiki_p_admin, $maxRecords, $_REQUEST, $tiki_p_view_trackers, $user, $page, $tiki_p_tracker_vote_ratings, $tiki_p_tracker_view_ratings;
	global $notificationlib; //needed if plugin tracker after plugin trackerlist
	extract ($params,EXTR_SKIP);

	if (!isset($trackerId)) {
		$smarty->assign('msg', tra("missing tracker ID for plugin TRACKER"));
		return $smarty->fetch("error_simple.tpl");
	} else {

		$smarty->assign('trackerId', $trackerId);
		
		require_once("lib/trackers/trackerlib.php");
		if (!isset($fields)) {
			$smarty->assign('msg', tra("missing fields list"));
			return $smarty->fetch("error_simple.tpl");
		} else {
			$listfields = split(':',$fields);
		}

		$tracker_info = $trklib->get_tracker($trackerId);
		if ($t = $trklib->get_tracker_options($trackerId))
			$tracker_info = array_merge($tracker_info, $t);
		$smarty->assign_by_ref('tracker_info', $tracker_info);
		
		//$query_array = array();
		//$quarray = array();
		//parse_str($_SERVER['QUERY_STRING'],$query_array);

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
		
		if (!isset($showinitials)) {
			$showinitials = "n";
		}
		$smarty->assign_by_ref('showinitials', $showinitials);
		
		if (!isset($showstatus)) {
			$showstatus = "n";
		}
		$smarty->assign_by_ref('showstatus', $showstatus);
		
		if (!isset($status)) {
			$status = "o";
		}
		$tr_status = $status;
		$smarty->assign_by_ref('tr_status', $tr_status);
		
		if (isset($checkbox)) {
			$cb = split('/', $checkbox);
			if (isset($cb[0]))
				$check['fieldId'] = $cb[0];
			if (isset($cb[1]))
				$check['name'] = $cb[1];
			if (isset($cb[2]))
				$check['title'] = $cb[2];
			if (isset($cb[3]))
				$check['submit'] = $cb[3];
			if (isset($cb[4]))
				$check['action'] = $cb[4];
			if (isset($cb[5]))
				$check['tpl'] = $cb[5];
			$smarty->assign_by_ref('checkbox', $check);
		}	

		if (isset($tracker_info['useRatings']) and $tracker_info['useRatings'] == 'y' 
				and $user and isset($_REQUEST['itemId']) and isset($_REQUEST["rate_$trackerId"]) and isset($_REQUEST['fieldId'])
				and in_array($_REQUEST["rate_$trackerId"],split(',',$tracker_info['ratingOptions']))) {
			if ($_REQUEST["rate_$trackerId"] == 'NULL') $_REQUEST["rate_$trackerId"] = NULL;
			$trklib->replace_rating($trackerId,$_REQUEST['itemId'],$_REQUEST['fieldId'],$user,$_REQUEST["rate_$trackerId"]);
			header('Location: tiki-index.php?page='.urlencode($page));
		}
		
		if (isset($_REQUEST['tr_sort_mode'])) {
		  //$query_array['tr_sort_mode'] = $_REQUEST['tr_sort_mode'];
			$sort_mode = $_REQUEST['tr_sort_mode'];
		} elseif (!isset($sort_mode)) {
			if (!empty($tracker_info['defaultOrderKey'])) {
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
		$smarty->assign_by_ref('tr_sort_mode', $tr_sort_mode);
		
		if (!isset($max)) {
			$max = $maxRecords;
		}

		if (isset($_REQUEST['tr_offset'])) {
		  //$query_array['tr_offset'] = $_REQUEST['tr_offset'];
			$tr_offset = $_REQUEST['tr_offset'];
		} else {
			$tr_offset = 0;
			//$query_array['tr_offset'] = 0;
		}
		$smarty->assign_by_ref('tr_offset',$tr_offset);

			
		$tr_initial = '';
		if ($showinitials == 'y') {
			if (isset($_REQUEST["tr_initial"])) {
			  //$query_array['tr_initial'] = $_REQUEST['tr_initial'];
				$tr_initial = $_REQUEST["tr_initial"];
			}
			$smarty->assign('initials', split(' ','a b c d e f g h i j k l m n o p q r s t u v w x y z'));
		}
		$smarty->assign_by_ref('tr_initial', $tr_initial);

		if (!isset($filterfield)) {
			$filterfield = '';
		}

		if (!isset($filtervalue)) {
			$filtervalue = '';
		}
		
		if (!isset($exactvalue)) {
			$exactvalue = '';
		}
		
		$rated = false;
		$status_types = $trklib->status_types();
		$smarty->assign('status_types', $status_types);

		$allfields = $trklib->list_tracker_fields($trackerId, 0, -1, 'position_asc', '');

		for ($i = 0; $i < count($allfields["data"]); $i++) {
			if (in_array($allfields["data"][$i]['fieldId'],$listfields) and $allfields["data"][$i]['isPublic'] == 'y') {
				$passfields["{$allfields["data"][$i]['fieldId']}"] = $allfields["data"][$i];
			}
			if (isset($check['fieldId']) && $allfields["data"][$i]['fieldId'] == $check['fieldId']) {
				$passfields["{$allfields["data"][$i]['fieldId']}"] = $allfields["data"][$i];
				if (!in_array($allfields["data"][$i]['fieldId'], $listfields))
					$allfields["data"][$i]['isPublic'] == 'n'; //don't show it
				$check['ix'] = sizeof($passfields) -1;
			}
			if ($allfields["data"][$i]['name'] == 'page' && empty($filterfield)) {
				$filterfield = $allfields["data"][$i]['fieldId'];
				$filtervalue = $_REQUEST['page'];
			}
			if (isset($tracker_info['useRatings']) and $tracker_info['useRatings'] == 'y' 
					and $allfields["data"][$i]['type'] == 's' and $allfields["data"][$i]['name'] == 'Rating') {
				$rated = true;
			}
		}
		$smarty->assign_by_ref('filterfield',$filterfield);
		$smarty->assign_by_ref('filterfield',$filtervalue);
		$smarty->assign_by_ref('fields', $passfields);
		$smarty->assign_by_ref('filterfield',$exactvalue);

		if (count($passfields)) {
			$items = $trklib->list_items($trackerId, $tr_offset, $max, $tr_sort_mode, $passfields, $filterfield, $filtervalue, $tr_status, $tr_initial, $exactvalue);
			
			if ($rated) {
				foreach ($items['data'] as $f=>$v) {
					$items['data'][$f]['my_rate'] = $tikilib->get_user_vote("tracker.".$trackerId.'.'.$items['data'][$f]['itemId'],$user);
				}
			}

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
			/*foreach ($query_array as $k=>$v) {
				if (!is_array($v)) { //only to avoid an error: eliminate the params that are not simple (ex: if you have in the same page a tracker list plugin and a tracker plugin, filling the tracker plugin interfers with the tracker list. In any case this is buggy if two tracker list plugins in the same page and if one needs the query value....
					$quarray[] = urlencode($k) ."=". urlencode($v);
				}
			}
			if (is_array($quarray)) {
				$query_string = implode("&amp;",$quarray);
			} else {
				$quering_string = '';
			}
			$smarty->assign('query_string', $query_string);
			*/
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
