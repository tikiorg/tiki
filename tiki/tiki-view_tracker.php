<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-view_tracker.php,v 1.141.2.11 2008-01-28 12:36:07 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once('tiki-setup.php');

include_once('lib/trackers/trackerlib.php');
global $notificationlib; include_once('lib/notifications/notificationlib.php');

$auto_query_args = array('offset','trackerId','reloff','itemId','maxRecords','status','sort_mode','initial','filterfield','filtervalue');

if ($prefs['feature_categories'] == 'y') {
	global $categlib;
	if (!is_object($categlib)) {
		include_once('lib/categories/categlib.php');
	}
}

if ($prefs['feature_trackers'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_trackers");
	$smarty->display("error.tpl");
	die;
}

$_REQUEST["itemId"] = 0;
$smarty->assign('itemId', $_REQUEST["itemId"]);

if (!isset($_REQUEST["trackerId"])) {
	$smarty->assign('msg', tra("No tracker indicated"));
	$smarty->display("error.tpl");
	die;
}

$tracker_info = $trklib->get_tracker($_REQUEST["trackerId"]);
if (empty($tracker_info)) {
	$smarty->assign('msg', tra("No tracker indicated"));
	$smarty->display("error.tpl");
	die;
}
if ($t = $trklib->get_tracker_options($_REQUEST["trackerId"])) {
	$tracker_info = array_merge($tracker_info,$t);
 }

$tikilib->get_perm_object($_REQUEST['trackerId'], 'tracker', $tracker_info);

if (!empty($_REQUEST['show']) && $_REQUEST['show'] == 'view') {
	$cookietab = '1';
} elseif (!empty($_REQUEST['show']) && $_REQUEST['show'] == 'mod') {
	$cookietab = '2';
} elseif (empty($_REQUEST['cookietab'])) {
	if ($tracker_info['writerCanModify'] == 'y' && $user)
		$cookietab = '1';
	elseif (!($tiki_p_view_trackers == 'y' || $tiki_p_admin == 'y' || $tiki_p_admin_trackers == 'y') && $tiki_p_create_tracker_items  == 'y')
		$cookietab = "2";
	else
		$cookietab = "1";
} else {
	$cookietab = $_REQUEST['cookietab'];
}
$defaultvalues = array();

if (isset($_REQUEST['vals']) and is_array($_REQUEST['vals'])) {
	$defaultvalues = $_REQUEST['vals'];
	$cookietab = "2";
} elseif (isset($_REQUEST['new'])) {
	$cookietab = "2";
}
$smarty->assign('defaultvalues', $defaultvalues);

$my = '';
$ours = '';
if (isset($_REQUEST['my'])) {
	if ($tiki_p_admin_trackers == 'y') {
		$my = $_REQUEST['my'];
	} elseif ($user) {
		$my = $user;
	}
} elseif (isset($_REQUEST['ours'])) {
	if ($tiki_p_admin_trackers == 'y') {
		$ours = $_REQUEST['ours'];
	} elseif ($group) {
		$ours = $group;
	}
}

if ($tiki_p_create_tracker_items == 'y' && !empty($t['start'])) {
	if ($tikilib->now < $t['start']) {
		$tiki_p_create_tracker_items = 'n';
		$smarty->assign('tiki_p_create_tracker_items', 'n');
	}
}
if ($tiki_p_create_tracker_items == 'y' && !empty($t['end'])) {
	if ($tikilib->now > $t['end']) {
		$tiki_p_create_tracker_items = 'n';
		$smarty->assign('tiki_p_create_tracker_items', 'n');
	}
}

if ($tiki_p_view_trackers != 'y') {
	if (!$my and isset($tracker_info['writerCanModify']) and $tracker_info['writerCanModify'] == 'y') {
		$my = $user;
	} elseif (!$ours and isset($tracker_info['writergroupCanModify']) and $tracker_info['writergroupCanModify'] == 'y') {
		$ours = $group;
	} elseif ($tiki_p_create_tracker_items != 'y') {
		if (!isset($user)){
			$smarty->assign('msg',$smarty->fetch('modules/mod-login_box.tpl'));
			$smarty->assign('errortitle',tra("Please login"));
		} else {
			$smarty->assign('msg', tra("You do not have permission to use this feature"));
		}
		$smarty->display("error.tpl");
		die;
	}
}
$smarty->assign('my', $my);
$smarty->assign('ours', $ours);

$field_types = $trklib->field_types();
$smarty->assign('field_types', $field_types);

$status_types = array();
$status_raw = $trklib->status_types();

if (isset($_REQUEST['status'])) {
	$sts = preg_split('//', $_REQUEST['status'], -1, PREG_SPLIT_NO_EMPTY);
} elseif (isset($tracker_info["defaultStatus"])) {
	$sts = preg_split('//', $tracker_info["defaultStatus"], -1, PREG_SPLIT_NO_EMPTY);
	$_REQUEST['status'] = $tracker_info["defaultStatus"];
} else {
	$sts = array('o');
	$_REQUEST['status'] = 'o';
}

foreach ($status_raw as $let=>$sta) {
	if ((isset($$sta['perm']) and $$sta['perm'] == 'y') or ($my or $ours)) {
		if (in_array($let,$sts)) {
			$sta['class'] = 'statuson';
			$sta['statuslink'] = str_replace($let,'',implode('',$sts));
		} else {
			$sta['class'] = 'statusoff';
			$sta['statuslink'] = implode('',$sts).$let;
		}
		$status_types["$let"] = $sta;
	}
}
$smarty->assign('status_types', $status_types);

if (count($status_types) == 0) {
	$tracker_info["showStatus"] = 'n';
}

$xfields = $trklib->list_tracker_fields($_REQUEST["trackerId"], 0, -1, 'position_asc', '');
if (!empty($tracker_info['showPopup'])) {
	$popupFields = explode(',', $tracker_info['showPopup']);
	$smarty->assign_by_ref('popupFields', $popupFields);
} else {
	$popupFields = array();
}

$writerfield = '';
$writergroupfield = '';
$mainfield = '';
$orderkey = false;
$listfields = array();

$usecategs = false;
$ins_categs = array();
$textarea_options = false;

$counter=0;
$temp_max = count($xfields["data"]);
for ($i = 0; $i < $temp_max; $i++) {
	$fid = $xfields["data"][$i]["fieldId"];
	
	$ins_id = 'ins_' . $fid;
	$xfields["data"][$i]["ins_id"] = $ins_id;
	$xfields["data"][$i]["id"] = $fid;
	
	$filter_id = 'filter_' . $fid;
	$xfields["data"][$i]["filter_id"] = $filter_id;
	
	if (!empty($tracker_info['defaultOrderKey']) and $tracker_info['defaultOrderKey'] == $xfields["data"][$i]['fieldId']) {
		$orderkey = true;
	}
	if (($xfields['data'][$i]['type'] == 'u' || $xfields['data'][$i]['type'] == 'g' || $xfields['data'][$i]['type'] == 'I') && isset($xfields['data'][$i]['options_array'][0]) && $xfields['data'][$i]['options_array'][0] == 1) {
		$creatorSelector = true;
	} else {
		$creatorSelector = false;
	}
	if (($xfields["data"][$i]['isTblVisible'] == 'y' or $xfields["data"][$i]['isSearchable'] == 'y' or in_array($fid, $popupFields)) 
		and ($xfields["data"][$i]['isHidden'] == 'n' or $xfields["data"][$i]['isHidden'] == 'p' or $tiki_p_admin_trackers == 'y' or ($xfields["data"][$i]['type'] == 's'and $tiki_p_tracker_view_ratings == 'y'))
		) {
		
		$listfields[$fid]['type'] = $xfields["data"][$i]["type"];
		$listfields[$fid]['name'] = $xfields["data"][$i]["name"];
		$listfields[$fid]['options'] = $xfields["data"][$i]["options"];
		$listfields[$fid]['options_array'] = split(',',$xfields["data"][$i]["options"]);
		$listfields[$fid]['isMain'] = $xfields["data"][$i]["isMain"];
		$listfields[$fid]['isTblVisible'] = $xfields["data"][$i]["isTblVisible"];
		$listfields[$fid]['isHidden'] = $xfields["data"][$i]["isHidden"];
		$listfields[$fid]['isSearchable'] = $xfields["data"][$i]["isSearchable"];
		$listfields[$fid]['isMandatory'] = $xfields["data"][$i]["isMandatory"];

		if ($listfields[$fid]['type'] == 'e') { //category
		    $parentId = $listfields[$fid]['options_array'][0];
		    $listfields[$fid]['categories'] = $categlib->get_child_categories($parentId);
		}

	} 

	if ($creatorSelector or $xfields["data"][$i]['isHidden'] == 'n' or $xfields["data"][$i]['isHidden'] == 'c' or $xfields["data"][$i]['isHidden'] == 'p' or $tiki_p_admin_trackers == 'y' or ($xfields["data"][$i]['type'] == 's'and $tiki_p_tracker_view_ratings == 'y')) {
		$ins_fields["data"][$i] = $xfields["data"][$i];
		$fields["data"][$i] = $xfields["data"][$i];
		if ($fields["data"][$i]["type"] == 'f') { // date and time
			$fields["data"][$i]["value"] = '';
			$ins_fields["data"][$i]["value"] = '';
			if (isset($_REQUEST["$ins_id" . "Day"])) {
				$ins_fields["data"][$i]["value"] = $tikilib->make_time($_REQUEST["$ins_id" . "Hour"], $_REQUEST["$ins_id" . "Minute"],
				0, $_REQUEST["$ins_id" . "Month"], $_REQUEST["$ins_id" . "Day"], $_REQUEST["$ins_id" . "Year"]);
			} else {
				$ins_fields["data"][$i]["value"] = $tikilib->now;
			}
		} elseif ($fields["data"][$i]["type"] == 'e') { // category
			include_once('lib/categories/categlib.php');
			$parentId = $fields["data"][$i]['options_array'][0];
			$fields["data"][$i]['categories'] = $categlib->get_child_categories($parentId);
			$categId = "ins_cat_$fid";
			if (isset($_REQUEST[$categId])) {
				if (is_array($_REQUEST[$categId])) {
					foreach ($_REQUEST[$categId] as $c)
						$fields["data"][$i]['cat'][$c] = 'y';
					$ins_categs = array_merge($ins_categs, $_REQUEST[$categId]);
				} else {
					$fields["data"][$i]['cat'][$_REQUEST[$categId]] = 'y';
					$ins_categs[] = $_REQUEST[$categId];
				}
			}
			$ins_fields["data"][$i]["value"] = '';

		} elseif ($fields["data"][$i]["type"] == 'u') { // user selection
			if (isset($_REQUEST["$ins_id"]) and $_REQUEST["$ins_id"] and (!$fields["data"][$i]['options_array'][0] or $tiki_p_admin_trackers == 'y')) {
				$ins_fields["data"][$i]["value"] = $_REQUEST["$ins_id"];
			} else {
				if ($fields["data"][$i]['options_array'][0] == 1 and $user) {
					$ins_fields["data"][$i]["value"] = $user;
				} else {
					$ins_fields["data"][$i]["value"] = '';
				}
			}
			if ($fields["data"][$i]['options_array'][0] == 1 and !$writerfield) {
				$writerfield = $fid;
			} elseif (isset($_REQUEST["$filter_id"])) {
				$fields["data"][$i]["value"] = $_REQUEST["$filter_id"];
			} else {
				$fields["data"][$i]["value"] = '';
			}

		} elseif ($fields["data"][$i]["type"] == 'I') { // IP selection
			if (isset($_REQUEST["$ins_id"]) and $_REQUEST["$ins_id"] and (!$fields["data"][$i]['options_array'][0] or $tiki_p_admin_trackers == 'y')) {
				$ins_fields["data"][$i]["value"] = $_REQUEST["$ins_id"];
			} else {
				if ($fields["data"][$i]['options_array'][0] == 1 and $_SERVER['REMOTE_ADDR']) {
					$ins_fields["data"][$i]["value"] = $_SERVER['REMOTE_ADDR'];
				} else {
					$ins_fields["data"][$i]["value"] = '';
				}
			}
			if ($fields["data"][$i]['options_array'][0] == 1 and !$writerfield) {
				$writerfield = $fid;
			} elseif (isset($_REQUEST["$filter_id"])) {
				$fields["data"][$i]["value"] = $_REQUEST["$filter_id"];
			} else {
				$fields["data"][$i]["value"] = '';
			}

		} elseif ($fields["data"][$i]["type"] == 'g') { // group selection
			if (isset($_REQUEST["$ins_id"]) and $_REQUEST["$ins_id"] and (!$fields["data"][$i]['options_array'][0] or $tiki_p_admin_trackers == 'y')) {
				$ins_fields["data"][$i]["value"] = $_REQUEST["$ins_id"];
			} else {
				if ( $fields["data"][$i]['options_array'][0] == 1 and $group) {
					$ins_fields["data"][$i]["value"] = $group;
				} else {
					$ins_fields["data"][$i]["value"] = '';
				}
			}
			if ($fields["data"][$i]['options_array'][0] == 1 and !$writergroupfield) {
				$writergroupfield = $fid;
			} elseif (isset($_REQUEST["$filter_id"])) {
				$fields["data"][$i]["value"] = $_REQUEST["$filter_id"];
			} else {
				$fields["data"][$i]["value"] = '';
			}

		} elseif ($fields["data"][$i]["type"] == 'c') { // checkbox
			if (isset($_REQUEST["$ins_id"]) && $_REQUEST["$ins_id"] == 'on') {
				$ins_fields["data"][$i]["value"] = 'y';
			} else {
				$ins_fields["data"][$i]["value"] = 'n';
			}
			if (isset($_REQUEST["$filter_id"])) {
				$fields["data"][$i]["value"] = $_REQUEST["$filter_id"];
			} else {
				$fields["data"][$i]["value"] = '';
			}

		} elseif ($fields["data"][$i]["type"] == 'a') { // textarea
			if (isset($_REQUEST["$ins_id"])) {
					$ins_fields["data"][$i]["value"] = $_REQUEST["$ins_id"];
			} else {
				$ins_fields["data"][$i]["value"] = '';
			}
			if (isset($_REQUEST["$filter_id"])) {
				$fields["data"][$i]["value"] = $_REQUEST["$filter_id"];
			} else {
				$fields["data"][$i]["value"] = '';
			}
			if ($fields["data"][$i]["options_array"][0])	{
				$textarea_options = true;
			} 
			 if ($fields["data"][$i]["isMultilingual"]=='y') {
		        
                                  global $multilinguallib;
                                  include_once('lib/multilingual/multilinguallib.php');
                                  $multi_languages=$multilinguallib->getSystemLanguage();
                                  $smarty->assign('multi_languages',$multi_languages);
                                  
                                  $ins_fields["data"][$i]['isMultilingual']='y';
				            $compteur=0;
				            foreach ($multi_languages as $num=>$tmplang){
				            //Case convert normal -> multilingual
				            if (!isset($_REQUEST[$ins_id."_".$tmplang]) && isset($_REQUEST["$ins_id"]))
				                $_REQUEST[$ins_id."_".$tmplang]=$_REQUEST["$ins_id"];
				            $fields["data"][$i]["lingualvalue"][$num]["lang"] = $tmplang;
				            if (isset($_REQUEST[$ins_id."_".$tmplang]))
				                $fields["data"][$i]["lingualvalue"][$num]["value"] =     $_REQUEST[$ins_id."_".$tmplang];
				            $fields["data"][$i]["lingualpvalue"][$num]["lang"] = $tmplang;
				            if (isset($_REQUEST[$ins_id."_".$tmplang]))
				                $fields["data"][$i]["lingualpvalue"][$num]["value"] =     $tikilib->parse_data(htmlspecialchars($_REQUEST[$ins_id."_".$tmplang]));
					    }
					 $ins_fields["data"][$i]["lingualpvalue"]=$fields["data"][$i]["lingualpvalue"];
					     $ins_fields["data"][$i]["lingualvalue"]=$fields["data"][$i]["lingualvalue"];
				        } 

		} elseif($fields["data"][$i]["type"] == 's') { // rating
			if (isset($_REQUEST["$ins_id"])) {
				$newItemRate = $_REQUEST["$ins_id"];
				$newItemRateField = $fields["data"]["$i"]["fieldId"];
			} else {
				$newItemRate = NULL;
			}
		
		} elseif(  $fields["data"][$i]["type"] == 'y' ) { // country list
			if (isset($_REQUEST["$ins_id"])) {		
				$ins_fields["data"][$i]["value"] = $_REQUEST["$ins_id"];	
			}
			// Get flags here
			$fields["data"][$i]['flags'] = $trklib->get_flags();
			$fields["data"][$i]['defaultvalue'] = 'None';
			
		} else {
			if (isset($_REQUEST["$ins_id"])) {
				$ins_fields["data"][$i]["value"] = $_REQUEST["$ins_id"];
			} else {
				$ins_fields["data"][$i]["value"] = '';
			}
			if ($fields['data'][$i]['type'] == 'D' && !empty($_REQUEST[$ins_id.'_other'])) { // drop down with other
				$ins_fields['data'][$i]['value'] = $_REQUEST[$ins_id.'_other'];
			}
			if (isset($_REQUEST["$filter_id"])) {
				$fields["data"][$i]["value"] = $_REQUEST["$filter_id"];
			} else {
				$fields["data"][$i]["value"] = '';
			}
			if ($fields["data"][$i]["type"] == 'r')	{ // item link
				if ($tiki_p_admin_trackers == 'y') {
					$stt = 'poc';
				} else {
					$stt = 'o';
				}
				$fields["data"][$i]["list"] = $trklib->get_all_items($fields["data"][$i]["options_array"][0],$fields["data"][$i]["options_array"][1],$stt);
				
				if (isset($fields["data"][$i]["options_array"][3]))
									       $fields["data"][$i]["listdisplay"] =$trklib->concat_all_items_from_fieldslist($fields["data"][$i]["options_array"][0],$fields["data"][$i]["options_array"][3]);
			}	
			elseif (($fields["data"][$i]["type"] == 'M') && ($fields["data"][$i]["options_array"][0] >= '3' ) )	{
				if (isset($_FILES["$ins_id"]) &&  is_uploaded_file($_FILES["$ins_id"]['tmp_name'])) {	
					$fp = fopen( $_FILES["$ins_id"]['tmp_name'], 'rb' );
					$data = '';
					while (!feof($fp)) {
						$data .= fread($fp, 8192 * 16);
					}
					fclose ($fp);
					$ins_fields["data"][$i]["value"] = $data;					
					$ins_fields["data"][$i]["file_type"] = $_FILES["$ins_id"]['type'];
					$ins_fields["data"][$i]["file_size"] = $_FILES["$ins_id"]['size'];
					$ins_fields["data"][$i]["file_name"] = $_FILES["$ins_id"]['name'];
				}

			} elseif ($fields["data"][$i]["type"] == 'i')	{ // image
				if (isset($_FILES["$ins_id"]) && is_uploaded_file($_FILES["$ins_id"]['tmp_name'])) {
					if (!empty($prefs['gal_match_regex'])) {
						if (!preg_match('/'.$prefs['gal_match_regex'].'/', $_FILES["$ins_id"]['name'], $reqs)) {
							$smarty->assign('msg', tra('Invalid imagename (using filters for filenames)'));
							$smarty->display("error.tpl");
							die;
						}
					}
					if (!empty($prefs['gal_nmatch_regex'])) {
						if (preg_match('/'.$prefs['gal_nmatch_regex'].'/', $_FILES["$ins_id"]['name'], $reqs)) {
							$smarty->assign('msg', tra('Invalid imagename (using filters for filenames)'));
							$smarty->display("error.tpl");
							die;
						}
					}
					$fp = fopen( $_FILES["$ins_id"]['tmp_name'], 'rb' );
					//$fhash = md5($name = $_FILES["$ins_id"]['name']);
					$data = '';
					while (!feof($fp)) {
						$data .= fread($fp, 8192 * 16);
					}
					fclose ($fp);
					$ins_fields["data"][$i]["value"] = $data;
					$ins_fields['data'][$i]['file_type'] = $_FILES["$ins_id"]['type'];
					//$ins_fields["data"][$i]["value"] = $_FILES["$ins_id"]['name'];
					$ins_fields["data"][$i]["file_size"] = $_FILES["$ins_id"]['size'];
					$ins_fields["data"][$i]["file_name"] = $_FILES["$ins_id"]['name'];
				}

			} elseif (($fields["data"][$i]["type"] == 't')&& ($fields["data"][$i]["isMultilingual"]=='y')) {
			          global $multilinguallib;
                                  include_once('lib/multilingual/multilinguallib.php');
                                  $multi_languages=$multilinguallib->getSystemLanguage();
                                  $smarty->assign('multi_languages',$multi_languages);
                                  
                                  $ins_fields["data"][$i]['isMultilingual']='y';
				            $compteur=0;
				            foreach ($multi_languages as $num=>$lang){
				            //Case convert normal -> multilingual
				            if (!isset($_REQUEST[$ins_id."_".$lang]) && isset($_REQUEST["$ins_id"]))
				                $_REQUEST[$ins_id."_".$lang]=$_REQUEST["$ins_id"];
				            $fields["data"][$i]["lingualvalue"][$num]["lang"] = $lang;
				            if (isset($_REQUEST[$ins_id."_".$lang]))
				                $fields["data"][$i]["lingualvalue"][$num]["value"] =     $_REQUEST[$ins_id."_".$lang];
				            $fields["data"][$i]["lingualpvalue"][$num]["lang"] = $lang;
				            if (isset($_REQUEST[$ins_id."_".$lang]))
				                $fields["data"][$i]["lingualpvalue"][$num]["value"] =     $tikilib->parse_data(htmlspecialchars($_REQUEST[$ins_id."_".$lang]));
				                
					    }
					    $ins_fields["data"][$i]["lingualpvalue"]=$fields["data"][$i]["lingualpvalue"];
					     $ins_fields["data"][$i]["lingualvalue"]=$fields["data"][$i]["lingualvalue"];
			}
			
		}
	}
	// store values to have them available when there is 
	// an error in the values typed by an user for a field type.
	if (isset( $fields['data'][$i]['fieldId']))
		$fields['data'][$i]['value'] = isset($ins_fields['data'][$i]['value'])?$ins_fields['data'][$i]['value']: '';

	if (empty($mainfield) and isset($fields['data'][$i]['isMain']) && $fields["data"][$i]["isMain"] == 'y' and !empty($fields["data"][$i]["value"])) {
		$mainfield = $fields["data"][$i]["value"];
}
	
}
if ($textarea_options) {
	include_once ('lib/quicktags/quicktagslib.php');
	$quicktags = $quicktagslib->list_quicktags(0,-1,'taglabel_desc','','trackers');
	$smarty->assign_by_ref('quicktags', $quicktags["data"]);
}

if (($tiki_p_admin_trackers == 'y' or $tiki_p_modify_tracker_items == 'y') and isset($_REQUEST["remove"])) {
  $area = 'deltrackeritem';
  if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
    key_check($area);
		$trklib->remove_tracker_item($_REQUEST["remove"]);
  } else {
    key_get($area);
  }
} elseif (($tiki_p_admin_trackers == 'y' or $tiki_p_modify_tracker_items == 'y') and isset($_REQUEST["batchaction"]) and $_REQUEST["batchaction"] == 'delete') {
	foreach ($_REQUEST['action'] as $batchid) {
		$trklib->remove_tracker_item($batchid);
	}
}


$smarty->assign('mail_msg', '');
$smarty->assign('email_mon', '');

if ($prefs['feature_user_watches'] == 'y' and $tiki_p_watch_trackers == 'y') {
	if ($user and isset($_REQUEST['watch'])) {
		check_ticket('view-trackers');
		if ($_REQUEST['watch'] == 'add') {
			$tikilib->add_user_watch($user, 'tracker_modified', $_REQUEST["trackerId"], 'tracker', $tracker_info['name'],"tiki-view_tracker.php?trackerId=".$_REQUEST["trackerId"]); 
		} else {
			$tikilib->remove_user_watch($user, 'tracker_modified', $_REQUEST["trackerId"]);
		}
	}
	$smarty->assign('user_watching_tracker', 'n');
	$it = $tikilib->user_watches($user, 'tracker_modified', $_REQUEST['trackerId'], 'tracker');
	if ($user and $tikilib->user_watches($user, 'tracker_modified', $_REQUEST['trackerId'], 'tracker')) {
		$smarty->assign('user_watching_tracker', 'y');
	}

    // Check, if the user is watching this tracker by a category.    
	if ($prefs['feature_categories'] == 'y') {
	    $watching_categories_temp=$categlib->get_watching_categories($_REQUEST["trackerId"],'tracker',$user);	    
	    $smarty->assign('category_watched','n');
	 	if (count($watching_categories_temp) > 0) {
	 		$smarty->assign('category_watched','y');
	 		$watching_categories=array();	 			 	
	 		foreach ($watching_categories_temp as $wct ) {
	 			$watching_categories[]=array("categId"=>$wct,"name"=>$categlib->get_category_name($wct));
	 		}		 		 	
	 		$smarty->assign('watching_categories', $watching_categories);
	 	}    
	} 		
}

if (isset($_REQUEST['import'])) {
	if (isset($_FILES['importfile']) && is_uploaded_file($_FILES['importfile']['tmp_name'])) {
		$fp = fopen($_FILES['importfile']['tmp_name'], "rb");
		$trklib->import_items($_REQUEST["trackerId"],$_REQUEST["indexfield"],$fp);
		fclose($fp);
	}
} elseif (isset($_REQUEST["save"])) {
	
	if ($tiki_p_create_tracker_items == 'y') {

		// Check field values for each type and presence of mandatory ones
		$mandatory_missing = array();
		$err_fields = array();
		$ins_categs = array();
		$categorized_fields = array();
		while (list($postVar, $postVal) = each($_REQUEST)) {
			if(preg_match("/^ins_cat_([0-9]+)/", $postVar, $m)) {
    				foreach ($postVal as $v)
					$ins_categs[] = $v;
				$categorized_fields[] = $m[1];
			}
 		}
		$field_errors = $trklib->check_field_values($ins_fields, $categorized_fields);
		$smarty->assign('err_mandatory', $field_errors['err_mandatory']);
		$smarty->assign('err_value', $field_errors['err_value']);

		// values are OK, then lets add a new item
		if( count($field_errors['err_mandatory']) == 0  && count($field_errors['err_value']) == 0 ) {

			$smarty->assign('input_err', '0'); // no warning to display

			check_ticket('view-trackers');
			if (!isset($_REQUEST["status"]) or ($tracker_info["showStatus"] != 'y' and $tiki_p_admin_trackers != 'y')) {
				$_REQUEST["status"] = '';
			}
			if (empty($_REQUEST["itemId"])) { // test if one item per user
				$_REQUEST['itemId'] = $trklib->get_user_item($_REQUEST['trackerId'], $tracker_info);
			}
			$itemid = $trklib->replace_item($_REQUEST["trackerId"], $_REQUEST["itemId"], $ins_fields, $_REQUEST['status'], $ins_categs);
			$cookietab = "1";
			$smarty->assign('itemId', '');
			$trklib->categorized_item($_REQUEST["trackerId"], $itemid, $mainfield, $ins_categs);
			if (isset($newItemRate)) {
				$trackerId = $_REQUEST["trackerId"];
				$trklib->replace_rating($trackerId,$itemid,$newItemRateField,$user,$newItemRate);
			}
			if(isset($_REQUEST["viewitem"])) {
				header('location: '.preg_replace('#[\r\n]+#', '',"tiki-view_tracker_item.php?trackerId=".$_REQUEST["trackerId"]."&itemId=".$itemid));
				die;
			}
			if (isset($tracker_info["defaultStatus"])) {
				$_REQUEST['status'] = $tracker_info["defaultStatus"];
			}
		} else {
			$cookietab = "2";
			$smarty->assign('input_err', '1'); // warning to display
		}
		if (isset($newItemRate)) {
			$trackerId = $_REQUEST["trackerId"];
			$trklib->replace_rating($trackerId,$itemid,$newItemRateField,$user,$newItemRate);
		}
	}
}

if (!isset($_REQUEST["sort_mode"])) {
	if (isset($tracker_info['defaultOrderKey'])) {
		if ($tracker_info['defaultOrderKey'] == -1)
			$sort_mode = 'lastModif';
		elseif ($tracker_info['defaultOrderKey'] == -2)
			$sort_mode = 'created';
		elseif ($orderkey) {
			$sort_mode = 'f_'.$tracker_info['defaultOrderKey'];
		} else {
			$sort_mode = 'lastModif';
		}
		if (isset($tracker_info['defaultOrderDir'])) {
			$sort_mode.= "_".$tracker_info['defaultOrderDir'];
		} else {
			$sort_mode.= "_asc";
		}
	} else {
		$sort_mode = '';
	}
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}

$smarty->assign_by_ref('sort_mode', $sort_mode);

if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}
$smarty->assign_by_ref('offset', $offset);

if (!empty($_REQUEST["maxRecords"])) {
	$maxRecords = $_REQUEST['maxRecords'];
}

if (isset($_REQUEST["initial"])) {
	$initial = $_REQUEST["initial"];
} else {
	$initial = '';
}
$smarty->assign('initial', $initial);
$smarty->assign('initials', split(' ','a b c d e f g h i j k l m n o p q r s t u v w x y z'));

if ($my and $writerfield) {
	$filterfield = $writerfield;
} elseif ($ours and $writergroupfield) {
	$filterfield = $writergroupfield;
} else {
	if (isset($_REQUEST["filterfield"])) {
		$filterfield = $_REQUEST["filterfield"];
	} else {
		$filterfield = '';
	}
}
$smarty->assign('filterfield', $filterfield);

if ($my and $writerfield) {
	$exactvalue = $my;
	$filtervalue = '';
	$_REQUEST['status'] = 'opc';
} elseif ($ours and $writergroupfield) {
	$exactvalue = $ours;
	$filtervalue = '';
	$_REQUEST['status'] = 'opc';
} else {
	if (isset($_REQUEST["filtervalue"]) and is_array($_REQUEST["filtervalue"]) and isset($_REQUEST["filtervalue"]["$filterfield"])) {
		$filtervalue = $_REQUEST["filtervalue"]["$filterfield"];
	} else if (isset($_REQUEST["filtervalue"])) {
		$filtervalue = $_REQUEST["filtervalue"];
	} else {
		$filtervalue = '';
	}
	if (!empty($_REQUEST['filtervalue_other'])) {
		$filtervalue = $_REQUEST['filtervalue_other'];
	}
	$exactvalue = '';
}
$smarty->assign('filtervalue', $filtervalue);


$smarty->assign('status', $_REQUEST["status"]);

// this isn't too beautiful, but works and doesn't break anything existing - amette
if (isset($_REQUEST["trackerId"])) $trackerId = $_REQUEST["trackerId"];
if (isset($_REQUEST["rateitemId"])) $rate_itemId = $_REQUEST["rateitemId"];

if ( isset($tracker_info['useRatings'])
	and $tracker_info['useRatings'] == 'y' and $user
	and isset($_REQUEST['rateitemId']) and isset($_REQUEST["rate_$trackerId"])
	and isset($_REQUEST['fieldId']) and isset($_REQUEST["trackerId"])
	and ( $_REQUEST["rate_$trackerId"] == 'NULL' || in_array($_REQUEST["rate_$trackerId"], split(',',$tracker_info['ratingOptions'])) )
) {
	if ( $_REQUEST["rate_$trackerId"] == 'NULL' ) {
		$_REQUEST["rate_$trackerId"] = NULL;
	}
	$trklib->replace_rating($trackerId, $rate_itemId, $_REQUEST['fieldId'], $user, $_REQUEST["rate_$trackerId"]);
}

$items = $trklib->list_items($_REQUEST["trackerId"], $offset, $maxRecords, $sort_mode, $listfields, $filterfield, $filtervalue, $_REQUEST["status"],$initial,$exactvalue);

$urlquery['status'] = $_REQUEST['status'];
$urlquery['initial'] = $initial;
$urlquery['trackerId'] = $_REQUEST["trackerId"];
$urlquery['sort_mode'] = $sort_mode;
$urlquery['exactvalue'] = $exactvalue;
$urlquery['filterfield'] = $filterfield;

if (is_array($filtervalue)) {
	foreach ($filtervalue as $fil) {
		$urlquery["filtervalue[".$filterfield."][]"] = $fil;
	}
} else {
	$urlquery["filtervalue[".$filterfield."]"] = $filtervalue;
}
$smarty->assign_by_ref('urlquery', $urlquery);
$cant = $items["cant"];
include "tiki-pagination.php";

if ($tracker_info['useComments'] == 'y' && $tracker_info['showComments'] == 'y') {
	foreach ($items['data'] as $itkey=>$oneitem) {
		$items['data'][$itkey]['comments'] = $trklib->get_item_nb_comments($items['data'][$itkey]['itemId']);
	}
}
if ($tracker_info['useAttachments'] == 'y' && $tracker_info['showAttachments'] == 'y') {
	foreach ($items["data"] as $itkey=>$oneitem) {
		$res = $trklib->get_item_nb_attachments($items["data"][$itkey]['itemId']);
		$items["data"][$itkey]['attachments']  = $res['attachments'];
		$items["data"][$itkey]['downloads'] = $res['downloads'];
	}
}

/* ************** not merge needed from 1.8
foreach ($items["data"] as $itkey=>$oneitem) {
    foreach ($oneitem['field_values'] as $ifld=>$valfld) {
        if ($valfld['type'] == 'f') {
            $items["data"][$itkey]['field_values'][$ifld]['value'] =
                smarty_make_timestamp($valfld['value']);
        }
    }
}
******************** */

// dynamic list process
foreach ($listfields as $sfid => $oneitem) {
	if ($listfields[$sfid]['type'] == 'w') {
		if ( ! isset($listfields[$listfields[$sfid]['options_array'][2]]['http_request']))
			$listfields[$listfields[$sfid]['options_array'][2]]['http_request'] = array('','','','','','','','','');
		for($i = 0; $i < 5; $i++)
		{
			$listfields[$listfields[$sfid]['options_array'][2]]['http_request'][$i] .=
				($listfields[$listfields[$sfid]['options_array'][2]]['http_request'][$i] ? "," : "") .
				 $listfields[$sfid]['options_array'][$i];
		}
		$listfields[$listfields[$sfid]['options_array'][2]]['http_request'][5] .=
			($listfields[$listfields[$sfid]['options_array'][2]]['http_request'][5] ? "," : "") .
			 $sfid;
		$listfields[$listfields[$sfid]['options_array'][2]]['http_request'][6] .=
			($listfields[$listfields[$sfid]['options_array'][2]]['http_request'][6] ? "," : "") .
			 $listfields[$sfid]['isMandatory'];
	}
}

$smarty->assign('trackerId', $_REQUEST["trackerId"]);
$smarty->assign('tracker_info', $tracker_info);
$smarty->assign('fields', $fields['data']);
$smarty->assign_by_ref('items', $items["data"]);
$smarty->assign_by_ref('item_count', $items['cant']);
$smarty->assign_by_ref('listfields', $listfields);

$users = $userlib->list_all_users();
$groups = $userlib->list_all_groups();
$smarty->assign('users', $users);
$smarty->assign('groups', $groups);

$section = 'trackers';
include_once('tiki-section_options.php');

$smarty->assign('uses_tabs', 'y');
if ($prefs['feature_jscalendar']) {
	$smarty->assign('uses_jscalendar', 'y');
}
$smarty->assign('show_filters', 'n');
if(count($fields['data'])>0) {
foreach ($fields['data'] as $it) {
	if ($it['isSearchable'] == 'y' and $it['isTblVisible'] == 'y'){
		$smarty->assign('show_filters', 'y');
		break;
	}
}
}

if ($items['data']) {
	foreach ($items['data'] as $f=>$v) {
		$items['data'][$f]['my_rate'] = $tikilib->get_user_vote("tracker.".$_REQUEST["trackerId"].'.'.$items['data'][$f]['itemId'],$user);
	}
}

setcookie('tab',$cookietab);
$smarty->assign('cookietab',$cookietab);

ask_ticket('view-trackers');

// Display the template
$smarty->assign('mid', 'tiki-view_tracker.tpl');
$smarty->display("tiki.tpl");
//echo "<!-- ";var_dump($filtervalue); echo " -->";
?>
