<?php
// $Id: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_trackerlist.php,v 1.40.2.12 2008-03-22 12:13:54 sylvieg Exp $
//
// TODO : 
// ----------
// - filtrage avec expression exacte
//

function wikiplugin_trackerlist_help() {
	$help = tra("Displays the output of a tracker content, fields are indicated with numeric ids.").":\n";
	$help.= "~np~{TRACKERLIST(trackerId=1,fields=2:4:5, sort=y, popup=6:7, stickypopup=y, showtitle=y, showlinks=y, showdesc=y, showinitials=y, showstatus=y, showcreated=y, showlastmodif=y, showfieldname=n, status=o|p|c|op|oc|pc|opc, sort_mode=, max=, filterfield=1:2, filtervalue=x:y, exactvalue=x:y, checkbox=fieldId/name/title/submit/action/tpl,goIfOne=y,more=y,moreurl=,tpl=)}Notice{TRACKERLIST}~/np~";
	return $help;
}

function wikiplugin_trackerlist($data, $params) {
	global $smarty, $tikilib, $dbTiki, $userlib, $tiki_p_admin_trackers, $prefs, $_REQUEST, $tiki_p_view_trackers, $user, $page, $tiki_p_tracker_vote_ratings, $tiki_p_tracker_view_ratings, $trklib;
	require_once("lib/trackers/trackerlib.php");
	global $notificationlib;  include_once('lib/notifications/notificationlib.php');//needed if plugin tracker after plugin trackerlist
	extract ($params,EXTR_SKIP);
	if ($prefs['feature_trackers'] != 'y' || !isset($trackerId) || !($tracker_info = $trklib->get_tracker($trackerId))) {
		return $smarty->fetch("wiki-plugins/error_tracker.tpl");
	} else {

		$smarty->assign('trackerId', $trackerId);
		$tracker_info = $trklib->get_tracker($trackerId);
		if ($t = $trklib->get_tracker_options($trackerId)) {
			$tracker_info = array_merge($tracker_info, $t);
		}

		if (!isset($sort)) {
			$sort = 'n';
		}

		if ($tiki_p_admin_trackers != 'y') {
			$perms = $tikilib->get_perm_object($trackerId, 'tracker', $tracker_info, false);
			if ($perms['tiki_p_view_trackers'] != 'y' && $tracker_info['writerCanModify'] != 'y') {
				return;
			}
			$smarty->assign_by_ref('perms', $perms);
		}

		global $trklib; require_once("lib/trackers/trackerlib.php");
		$allfields = $trklib->list_tracker_fields($trackerId, 0, -1, 'position_asc', '');
		if (!empty($fields)) {
			$listfields = split(':',$fields);
			if ($sort == 'y') {
				$allfields = $trklib->sort_fields($allfields, $listfields);
			}
		} else {
			foreach($allfields['data'] as $f) {
				$listfields[] = $f['fieldId'];
			}
		}
		if (!empty($popup)) {
			$popupfields = split(':', $popup);
		} else {
			$popupfields = array();
		}
		if ($t = $trklib->get_tracker_options($trackerId))
			$tracker_info = array_merge($tracker_info, $t);
		$smarty->assign_by_ref('tracker_info', $tracker_info);
		
		//$query_array = array();
		//$quarray = array();
		//parse_str($_SERVER['QUERY_STRING'],$query_array);

		if (isset($stickypopup) && $stickypopup == 'y') {
			$stickypopup = true;
		} else {
			$stickypopup = false;
		}
		$smarty->assign_by_ref('stickypopup', $stickypopup);

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

		if (!isset($showfieldname)) {
			$showfieldname = "y";
		}
		$smarty->assign_by_ref('showfieldname', $showfieldname);

		if (!isset($status)) {
			$status = "o";
		}
		$tr_status = $status;
		$smarty->assign_by_ref('tr_status', $tr_status);

		if (!isset($showcreated)) {
			$showcreated = $tracker_info['showCreated'];
		}
		$smarty->assign_by_ref('showcreated', $showcreated);
		if (!isset($showlastmodif)) {
			$showlastmodif = $tracker_info['showLastModif'];
		}
		$smarty->assign_by_ref('showlastmodif', $showlastmodif);
		if (!isset($more))
			$more = 'n';
		$smarty->assign_by_ref('more', $more);
		if (!isset($moreurl))
			$moreurl = 'tiki-view_tracker.php';
		$smarty->assign_by_ref('moreurl', $moreurl);

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
				if ($tracker_info['defaultOrderKey'] == -1)
					$sort_mode = 'lastModif';
				elseif ($tracker_info['defaultOrderKey'] == -2)
					$sort_mode = 'created';
				elseif ($tracker_info['defaultOrderKey'] == -3)
					$sort_mode = 'itemId';
				else
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
			$max = $prefs['maxRecords'];
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

		if (!isset($filtervalue)) {
			$filtervalue = '';
		} elseif ($filtervalue == '#user') {
			$filtervalue = $user;
		}
		
		if (!isset($exactvalue)) {
			$exactvalue = '';
		}
		
		$rated = false;
		$status_types = $trklib->status_types();
		$smarty->assign('status_types', $status_types);

		if (!isset($filterfield)) {
			$filterfield = '';
		} else {
			if (is_string($filterfield) && strstr($filterfield, ':') !== false) {
				$filterfield = split(':', $filterfield);
				if (!empty($filtervalue)) {
					$fvs = split(':', $filtervalue);
					unset($filtervalue);
					for ($i = 0; $i < count($filterfield); ++$i) {
						$filtervalue[] = isset($fvs[$i])? $fvs[$i]:'';
					}
				}
				if (!empty($exactvalue)) {
					$evs = split(':', $exactvalue);
					unset($exactvalue);
					for ($i = 0; $i < count($filterfield); ++$i) {
						$exactvalue[] = isset($evs[$i])?$evs[$i]:'';
					}
				}
			}
			if (is_array($filterfield)) {
				foreach ($filterfield as $ff) {
					unset($filterfieldok);
					foreach ($allfields['data'] as $f) {
						if ($f['fieldId'] == $ff) {
							$filterfieldok=true;
							break;
						}
					}
					if (!isset($filterfieldok))
						break;
				}
			} else {
				foreach ($allfields['data'] as $f) {
					if ($f['fieldId'] == $filterfield) {
						$filterfieldok=true;
						break;
					}
				}
			}
			if (!isset($filterfieldok)) {
				return tra('incorrect filterfield');
			}
		}
		if ($tiki_p_admin_trackers != 'y' && $perms['tiki_p_view_trackers'] != 'y' && $tracker_info['writerCanModify'] == 'y' && $user && ($fieldId = $trklib->get_field_id_from_type($trackerId, 'u', '1%'))) { //patch this should be in list_items
			if ($filterfield != $fieldId || (is_array($filterfield) && !in_array($fieldId, $filterfield))) {
				if (is_array($filterfield))
					$filterfield[] = $fieldId;
				elseif (empty($filterfield))
					$filterfield = $fieldId;
				else
					$filterfield = array($filterfield, $fieldId);
				if (is_array($exactvalue))
					$exactvalue[] = $user;
				elseif (empty($exactvalue))
					$exactvalue = $user;
				else
					$exatvalue = array($exactvalue, $user);
			}
		}


		for ($i = 0; $i < count($allfields["data"]); $i++) {
			if ((in_array($allfields["data"][$i]['fieldId'],$listfields) or in_array($allfields["data"][$i]['fieldId'],$popupfields))and $allfields["data"][$i]['isPublic'] == 'y') {
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
		$smarty->assign_by_ref('listfields', $listfields);
		$smarty->assign_by_ref('popupfields', $popupfields);

		if (count($passfields)) {
			$items = $trklib->list_items($trackerId, $tr_offset, $max, $tr_sort_mode, $passfields, $filterfield, $filtervalue, $tr_status, $tr_initial, $exactvalue);

			if ($items['cant'] == 1 && isset($goIfOne) && ($goIfOne == 'y' || $goIfOne == 1)) {
				header('Location: tiki-view_tracker_item.php?itemId='.$items['data'][0]['itemId'].'&amp;trackerId='.$items['data'][0]['trackerId']);
				die;
			}
			
			if ($rated && !empty($items['data'])) {
				foreach ($items['data'] as $f=>$v) {
					$items['data'][$f]['my_rate'] = $tikilib->get_user_vote("tracker.".$trackerId.'.'.$items['data'][$f]['itemId'],$user);
				}
			}
			if ($tracker_info['useComments'] == 'y' && $tracker_info['showComments'] == 'y') {
				foreach ($items['data'] as $itkey=>$oneitem) {
					$items['data'][$itkey]['comments'] = $trklib->get_item_nb_comments($items['data'][$itkey]['itemId']);
				}
			}
			if ($tracker_info['useAttachments'] == 'y' && $tracker_info['showAttachments'] == 'y') {
				foreach ($items["data"] as $itkey=>$oneitem) {
					$res = $trklib->get_item_nb_attachments($items["data"][$itkey]['itemId']);
					$items["data"][$itkey]['attachments']  = $res['attachments'];
				}
			}
			if (!isset($tpl)) {
				$tpl = '';
			}
			$smarty->assign('tpl', $tpl);
			
			$smarty->assign_by_ref('max', $max);
			$smarty->assign_by_ref('count_item', $items['cant']);
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
