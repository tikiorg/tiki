<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-view_tracker_item.php,v 1.141.2.21 2008-01-30 20:38:35 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');
include_once('lib/categories/categlib.php');
include_once ("lib/filegals/filegallib.php");
include_once ('lib/trackers/trackerlib.php');
include_once ('lib/notifications/notificationlib.php');

if ($prefs['feature_trackers'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_trackers");
	$smarty->display("error.tpl");
	die;
}

$special = false;

if (!isset($_REQUEST['trackerId']) && $prefs['userTracker'] == 'y' && !isset($_REQUEST['user'])) {
	if (isset($_REQUEST['view']) and $_REQUEST['view'] == ' user') {
		$utid = $userlib->get_tracker_usergroup($user);
		if($utid['usersTrackerId']) {
			$_REQUEST['trackerId'] = $utid['usersTrackerId'];
			$_REQUEST["itemId"] = $trklib->get_item_id($_REQUEST['trackerId'],$utid['usersFieldId'],$user);
			if ($_REQUEST['itemId'] == NULL) {
				$addit['data'][0]['fieldId'] = $utid['usersFieldId'];
				$addit['data'][0]['type'] = 'u';
				$addit['data'][0]['value'] = $user;
				$i = 1;
				if ($f = $trklib->get_field_id_from_type($_REQUEST['trackerId'], "u", '1%')) {
				    if ($f != $utid['usersFieldId']) {
					$addit['data'][1]['fieldId'] = $f;
					$addit['data'][1]['type'] = 'u';
					$addit['data'][1]['value'] = $user;
					++$i;
			    	   }
				}
				if ($f = $trklib->get_field_id_from_type($_REQUEST['trackerId'], "g", 1)) {
					$addit['data'][$i]['fieldId'] = $f;
					$addit['data'][$i]['type'] = 'g';
					$addit['data'][$i]['value'] = $group;
				}
				$_REQUEST['itemId'] = $trklib->replace_item($_REQUEST["trackerId"], 0, $addit, 'o');
			}
			$special = 'user';
		}
	} elseif (isset($_REQUEST["usertracker"]) and $tiki_p_admin == 'y') {
		$utid = $userlib->get_tracker_usergroup($_REQUEST['usertracker'];
		$_REQUEST['trackerId'] = $utid['usersTrackerId'];
		$_REQUEST["itemId"] = $trklib->get_item_id($_REQUEST['trackerId'],$utid['usersFieldId'],$_REQUEST["usertracker"]);
	}
}

if (!isset($_REQUEST['trackerId']) && $prefs['groupTracker'] == 'y') {
	if (isset($_REQUEST['view']) and $_REQUEST['view'] == ' group') {
		$gtid = $userlib->get_grouptrackerid($group);
		if($gtid['groupTrackerId']) {
			$_REQUEST["trackerId"] = $gtid['groupTrackerId'];
			$_REQUEST["itemId"] = $trklib->get_item_id($_REQUEST['trackerId'],$gtid['groupFieldId'],$group);
			if ($_REQUEST['itemId'] == NULL) {
				$addit['data'][0]['fieldId'] = $gtid['groupFieldId'];
				$addit['data'][0]['type'] = 'g';
				$addit['data'][0]['value'] = $group;
				$_REQUEST['itemId'] = $trklib->replace_item($_REQUEST["trackerId"], 0, $addit, 'o');
			}
			$special = 'group';
		}
	} elseif (isset($_REQUEST["grouptracker"]) and $tiki_p_admin == 'y') {
		$gtid = $userlib->get_grouptrackerid($_REQUEST["grouptracker"]);
		$_REQUEST["trackerId"] = $gtid['groupTrackerId'];
		$_REQUEST["itemId"] = $trklib->get_item_id($_REQUEST['trackerId'],$gtid['groupFieldId'],$_REQUEST["grouptracker"]);
	}
}
$smarty->assign_by_ref('special', $special);

//url to a user user tracker tiki-view_tracker_item.php?user=yyyyy&view=+user or tiki-view_tracker_item.php?greoup=yyy&user=yyyyy&view=+user or tiki-view_tracker_item.php?trackerId=xxx&user=yyyyy&view=+user
if ($prefs['userTracker'] == 'y' && isset($_REQUEST['view']) && $_REQUEST['view'] = ' user' && !empty($_REQUEST['user'])) {
	if (empty($_REQUEST['trackerId']) && empty($_REQUEST['group'])) {
		$_REQUEST['group'] = $userlib->get_user_default_group($_REQUEST['user']);
	}
	if (empty($_REQUEST['trackerId']) && !empty($_REQUEST['group'])) {
		$utid = $userlib->get_usertrackerid($_REQUEST['group']);
		if (!empty($utid['usersTrackerId']) && !empty($utid['usersFieldId'])) {
			$_REQUEST['trackerId'] = $utid['usersTrackerId'];
			$fieldId = $utid['usersFieldId'];
		}
	}
	if (!empty($_REQUEST['trackerId']) && empty($fieldId)) {
		$fieldId = $trklib->get_field_id_from_type($_REQUEST['trackerId'], 'u', '1%');
	}
	if (!empty($_REQUEST['trackerId']) && !empty($fieldId)) {
		$_REQUEST['itemId'] = $trklib->get_item_id($_REQUEST['trackerId'], $fieldId, $_REQUEST['user']);
	}
}

if ((!isset($_REQUEST["trackerId"]) || !$_REQUEST["trackerId"]) && isset($_REQUEST["itemId"])) {
	$item_info = $trklib->get_tracker_item($_REQUEST["itemId"]);
	$_REQUEST['trackerId'] = $item_info['trackerId'];	
}
if (!isset($_REQUEST["trackerId"]) || !$_REQUEST["trackerId"]) {
	$smarty->assign('msg', tra("No tracker indicated"));
	$smarty->display("error.tpl");
	die;
}

if (!isset($utid) and !isset($gtid) and (!isset($_REQUEST["itemId"]) or !$_REQUEST["itemId"])) {
	$smarty->assign('msg', tra("No item indicated"));
	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'created_desc';
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

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}
$smarty->assign('find', $find);

// ************* previous/next **************
$urlquery = array();
foreach (array('status', 'filterfield', 'filtervalue', 'initial', 'exactvalue', 'reloff') as $reqfld) {
    $trynam = 'try'.$reqfld;
    if (isset($_REQUEST[$reqfld]) && is_string($_REQUEST[$reqfld])) {
        $$trynam = $urlquery[$reqfld] = $_REQUEST[$reqfld];
    } else {
        $$trynam = '';
    }
}
if (isset($_REQUEST["filtervalue"]) and is_array($_REQUEST["filtervalue"]) and isset($_REQUEST["filtervalue"]["$tryfilterfield"])) {
	$tryfiltervalue = $_REQUEST["filtervalue"]["$tryfilterfield"];
    $urlquery["filtervalue[".$tryfilterfield."]"] = $tryfiltervalue;
}



//Management of the field type 'User subscribe' (U)
//when user clic on (un)subscribe
if(isset($_REQUEST['user_subscribe']) || isset($_REQUEST['user_unsubscribe'])){
  $temp=$userlib->get_user_info($user);
  $id_user=$temp['userId'];
  $id_tiki_user=$temp['userId'];

  $U_query="SELECT value FROM `tiki_tracker_item_fields` WHERE `itemId`=? AND `fieldId`=?";
  $U_fieldId=$_REQUEST['U_fieldId'];
  $U_value=$trklib->getOne($U_query,array((int)$_REQUEST['itemId'], (int)$U_fieldId));

  $U_maxsubscriptions=substr($U_value,0,strpos($U_value,'#'));

  $pattern="/(\d+)\[(\d+)\]/";
  preg_match_all($pattern,$U_value,$match);
  $users_array2=array();
  $user_subscription=FALSE;

  foreach($match[1] as $i=>$id_user){
    $temp=$userlib->get_userId_info($id_user);
    if($id_user==$id_tiki_user){
      $user_subscription=TRUE;
    } else {
      array_push($users_array2,
		 array('id'=>$id_user,'login'=>$temp['login'],'friends'=>$match[2][$i])
		 );
    }
  }
  $match=NULL;
  if(isset($_REQUEST['user_subscribe'])){
    array_push($users_array2,
	       array('id'=>$id_tiki_user,'login'=>$user,'friends'=>intval($_POST['user_friends']))
	       );
  }

  $sql_value=$U_maxsubscriptions."#";
  $sql_value2="";
  foreach($users_array2 as $U){
    $sql_value2 .= $U['id']."[".$U['friends']."],";
  }
  $sql_value.=$sql_value2?substr($sql_value2,0,strlen($sql_value2)-1):"";
  
  $xfield = $trklib->list_tracker_fields($_REQUEST["trackerId"], 0, -1, 'position_asc', '', true, array('fieldId'=>array($U_fieldId)));
  $xfield['data'][0]['value'] = $sql_value;
  $trklib->replace_item($_REQUEST['trackerId'], $_REQUEST['itemId'], $xfield);
 }

//*********** handle prev/next links *****************

if ( isset($_REQUEST['reloff']) ) {
	if ( isset($_REQUEST['move']) ) {
		switch ( $_REQUEST['move'] ) {
			case 'prev': $tryreloff += -1; break;
			case 'next': $tryreloff += 1; break;
			default: $tryreloff = (int)$_REQUEST['move'];
		}
	}

	$cant = 0;
	$trymove = $trklib->list_items($_REQUEST['trackerId'], $offset + $tryreloff, 1, $sort_mode, array(), $tryfilterfield, $tryfiltervalue, $trystatus, $tryinitial, $tryexactvalue);

	if ( isset($trymove['data'][0]['itemId']) ) {
		$_REQUEST['itemId'] = $trymove['data'][0]['itemId'];
		unset($item_info);
		$cant = $trymove['cant'];
	} elseif ( isset($_REQUEST['cant']) ) {
		$cant = $_REQUEST['cant'];
	}

	$smarty->assign('cant', $cant);
}

$smarty->assign_by_ref('urlquery', $urlquery);

//*********** that's all for prev/next *****************

$smarty->assign('itemId', $_REQUEST["itemId"]);
if (!isset($item_info)) {
	$item_info = $trklib->get_tracker_item($_REQUEST["itemId"]);
	if (empty($item_info)) {
		$smarty->assign('msg', tra("No item indicated"));
		$smarty->display("error.tpl");
		die;
	}  
}
$smarty->assign('item_info', $item_info);

$smarty->assign('individual', 'n');
if ($userlib->object_has_one_permission($_REQUEST["trackerId"], 'tracker')) {
	$smarty->assign('individual', 'y');
	if ($tiki_p_admin != 'y') {
		$perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'trackers');
		foreach ($perms["data"] as $perm) {
			$permName = $perm["permName"];
			if ($userlib->object_has_permission($user, $_REQUEST["trackerId"], 'tracker', $permName)) {
				$$permName = 'y';
				$smarty->assign("$permName", 'y');
				if ($permName == 'tiki_p_admin_trackers') {
					$propagate = true;
				}
			} else {
				$$permName = 'n';
				$smarty->assign("$permName", 'n');
			}
		}
	}
}
if (!empty($propagate) && $propagate) { // if local set of tiki_p_admin_trackers, need to other perm
    $perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'trackers');
    foreach ($perms['data'] as $perm) {
        $perm = $perm['permName'];
        $smarty->assign("$perm", 'y');
        $$perm = 'y';
    }
}

$tracker_info = $trklib->get_tracker($_REQUEST["trackerId"]);
if ($t = $trklib->get_tracker_options($_REQUEST["trackerId"]))
	$tracker_info = array_merge($tracker_info,$t);
if (!isset($tracker_info["writerCanModify"]) or (isset($utid) and ($_REQUEST['trackerId'] != $utid['usersTrackerId']))) {
	$tracker_info["writerCanModify"] = 'n';
}
if (!isset($tracker_info["writerGroupCanModify"]) or (isset($gtid) and ($_REQUEST['trackerId'] != $gtid['groupTrackerId']))) {
	$tracker_info["writerGroupCanModify"] = 'n';
}

if ($tiki_p_view_trackers != 'y' and $tracker_info["writerCanModify"] != 'y' and $tracker_info["writerGroupCanModify"] != 'y'&& !$special) {
  if (!$user) {
    $smarty->assign('msg',$smarty->fetch('modules/mod-login_box.tpl'));
    $smarty->assign('errortitle',tra("Please login"));
  } else {
		$smarty->assign('msg', tra("You do not have permission to use this feature"));
	}
	$smarty->display("error.tpl");
	die;
}

$status_types = $trklib->status_types();
$smarty->assign('status_types', $status_types);

$xfields = $trklib->list_tracker_fields($_REQUEST["trackerId"], 0, -1, 'position_asc', '');
$fields = array();
$ins_fields = array();

$usecategs = false;
$ins_categs = array();
$textarea_options = false;
$tabi = 1;

foreach($xfields["data"] as $i=>$array) {
	$fid = $xfields["data"][$i]["fieldId"];
	
	$ins_id = 'ins_' . $fid;
	$xfields["data"][$i]["ins_id"] = $ins_id;
	
	$filter_id = 'filter_' . $fid;
	$xfields["data"][$i]["filter_id"] = $filter_id;

	if (!isset($mainfield) and $xfields["data"][$i]['isMain'] == 'y') {
		$mainfield = $i;
	}

	if ($xfields["data"][$i]['type'] == 's') {
		if ($tiki_p_tracker_view_ratings == 'y') {
			$ins_fields["data"][$i] = $xfields["data"][$i];
			$rateFieldId = $fid;
			//$fields["data"][$i] = $xfields["data"][$i];
		}
	} elseif ($xfields["data"][$i]['isHidden'] == 'n' or $xfields["data"][$i]['isHidden'] == 'p' or $tiki_p_admin_trackers == 'y' or ($xfields['data'][$i]['isHidden'] == 'c' && !empty($user) && $user == $trklib->get_item_creator($_REQUEST['trackerId'], $_REQUEST['itemId']))) {
		$ins_fields["data"][$i] = $xfields["data"][$i];
		$fields["data"][$i] = $xfields["data"][$i];

		if ($fields["data"][$i]["type"] == 'f') {
			$fields["data"][$i]["value"] = '';
			$ins_fields["data"][$i]["value"] = '';
			if (isset($_REQUEST["$ins_id" . "Day"])) {
				$ins_fields["data"][$i]["value"] = $tikilib->make_time($_REQUEST["$ins_id" . "Hour"], $_REQUEST["$ins_id" . "Minute"],
				0, $_REQUEST["$ins_id" . "Month"], $_REQUEST["$ins_id" . "Day"], $_REQUEST["$ins_id" . "Year"]); 
			} else {
				$ins_fields["data"][$i]["value"] = $tikilib->now;
			}
		
		} elseif ($fields["data"][$i]["type"] == 'e') {
			include_once('lib/categories/categlib.php');
			$k = $ins_fields["data"][$i]['options_array'][0];
			$fields["data"][$i]["$k"] = $categlib->get_child_categories($k);
			$categId = "ins_cat_$fid";
			if (isset($_REQUEST[$categId]) and is_array($_REQUEST[$categId])) {
				$ins_categs = array_merge($ins_categs,$_REQUEST[$categId]);
			}
			$ins_fields["data"][$i]["value"] = '';
		
		} elseif ($fields["data"][$i]["type"] == 'c') {
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

		} elseif ($fields["data"][$i]["type"] == 'u' and isset($fields["data"][$i]['options_array'][0]) and $user)	{
			if (isset($_REQUEST["$ins_id"]) and ($fields["data"][$i]['options_array'][0] < 1 or $tiki_p_admin_trackers == 'y')) {
				$ins_fields["data"][$i]["value"] = $_REQUEST["$ins_id"];
			} else {
				if ($fields["data"][$i]['options_array'][0] == 2) {
					$ins_fields["data"][$i]["value"] = $user;
				} elseif ($fields["data"][$i]['options_array'][0] == 1) {
					if (isset($tracker_info["writerCanModify"]) and $tracker_info["writerCanModify"] == 'y') {
						$tracker_info["authorfield"] = $fid;
					}
					unset($ins_fields["data"][$i]["fieldId"]);
				} else {
					$ins_fields["data"][$i]["value"] = '';
				}
			}
			if (isset($_REQUEST["$filter_id"])) {
				$fields["data"][$i]["value"] = $_REQUEST["$filter_id"];
			} else {
				$fields["data"][$i]["value"] = '';
			}
		
		} elseif ($fields["data"][$i]["type"] == 'I' and isset($fields["data"][$i]['options_array'][0]) and isset($IP))	{
			if (isset($_REQUEST["$ins_id"]) and ($fields["data"][$i]['options_array'][0] < 1 or $tiki_p_admin_trackers == 'y')) {
				$ins_fields["data"][$i]["value"] = $_REQUEST["$ins_id"];
			} else {
				if ($fields["data"][$i]['options_array'][0] == 2) {
					$ins_fields["data"][$i]["value"] = $IP;
				} elseif ($fields["data"][$i]['options_array'][0] == 1) {
				} else {
					$ins_fields["data"][$i]["value"] = '';
				}
			}
			if (isset($_REQUEST["$filter_id"])) {
				$fields["data"][$i]["value"] = $_REQUEST["$filter_id"];
			} else {
				$fields["data"][$i]["value"] = '';
			}

		} elseif ($fields["data"][$i]["type"] == 'g' and isset($fields["data"][$i]['options_array'][0]) and $group)	{
			if (isset($_REQUEST["$ins_id"])) {
				$ins_fields["data"][$i]["value"] = $_REQUEST["$ins_id"];
			} else {
				if ($fields["data"][$i]['options_array'][0] == 2) {
					$ins_fields["data"][$i]["value"] = $group;
				} elseif ($fields["data"][$i]['options_array'][0] == 1)  {
					if (isset($tracker_info["writerGroupCanModify"]) and $tracker_info["writerGroupCanModify"] == 'y') {
						$tracker_info["authorgroupfield"] = $fid;
					}
					unset($ins_fields["data"][$i]["fieldId"]);
				} else {
					$ins_fields["data"][$i]["value"] = '';
				}
			}
			if (isset($_REQUEST["$filter_id"])) {
				$fields["data"][$i]["value"] = $_REQUEST["$filter_id"];
			} else {
				$fields["data"][$i]["value"] = '';
			}
			
		} elseif ($fields["data"][$i]["type"] == 'a' )	{
		if (isset($_REQUEST["$ins_id"])) {
			$ins_fields["data"][$i]["value"] = $_REQUEST["$ins_id"];
		} else {
			$ins_fields["data"][$i]["value"] = '';
		}
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
				            //print_r($multi_languages);
				            $compteur=0;
				            foreach ($multi_languages as $num=>$tmplang){
				            //Case convert normal -> multilingual
				            if (!isset($_REQUEST[$ins_id."_".$tmplang]) && isset($_REQUEST["$fid"]))
				                $_REQUEST["$fid$lang"]=$_REQUEST["$fid"];
				            $ins_fields["data"][$i]["lingualvalue"][$num]["lang"] = $tmplang;
				            if (isset($_REQUEST[$ins_id."_".$tmplang]))
				                $ins_fields["data"][$i]["lingualvalue"][$num]["value"] =     $_REQUEST[$ins_id."_".$tmplang];
				            $ins_fields["data"][$i]["lingualpvalue"][$num]["lang"] = $tmplang;
				            if (isset($_REQUEST[$ins_id."_".$tmplang]))
				                $ins_fields["data"][$i]["lingualpvalue"][$num]["value"] =     $tikilib->parse_data(htmlspecialchars($_REQUEST[$ins_id."_".$tmplang]));
					    }
				        } 
			
		} elseif($fields["data"][$i]["type"] == 'y' ) { // country list			
			if (isset($_REQUEST["$ins_id"])) {			
				$ins_fields["data"][$i]["value"] = $_REQUEST["$ins_id"];				
			}
			// Get flags here
			$ins_fields["data"][$i]['flags'] = $trklib->get_flags();

		} else {

			if (isset($_REQUEST["$ins_id"])) {
				$ins_fields["data"][$i]["value"] = $_REQUEST["$ins_id"];
			} else {
				$ins_fields["data"][$i]["value"] = '';
			}
			if ($ins_fields['data'][$i]['type'] == 'D' && !empty($_REQUEST[$ins_id.'_other'])) { // drop down with other
				$ins_fields['data'][$i]['value'] = $_REQUEST[$ins_id.'_other'];
			}
			if (isset($_REQUEST["$filter_id"])) {
				$fields["data"][$i]["value"] = $_REQUEST["$filter_id"];
			} else {
				$fields["data"][$i]["value"] = '';
			}


			if ($fields["data"][$i]["type"] == 'M') {
			 if ($fields["data"][$i]["options_array"][0] >= '3' ) 	{
				if (isset($_FILES["$ins_id"]) && is_uploaded_file($_FILES["$ins_id"]['tmp_name'])) {	
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
				
			  }
 			}
		
			if ($fields["data"][$i]["type"] == 'i')	{
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
					$data = '';
					while (!feof($fp)) {
						$data .= fread($fp, 8192 * 16);
					}
					fclose ($fp);
					$ins_fields["data"][$i]["value"] = $data;
					
					//$ins_fields["data"][$i]["value"] = $_FILES["$ins_id"]['name'];					
					$ins_fields["data"][$i]["file_type"] = $_FILES["$ins_id"]['type'];//mime_content_type( $_FILES["$ins_id"]['tmp_name'] );
					$ins_fields["data"][$i]["file_size"] = $_FILES["$ins_id"]['size'];
					$ins_fields["data"][$i]["file_name"] = $_FILES["$ins_id"]['name'];
				}
			}
			 if (($fields["data"][$i]["isMultilingual"] == 'y') && $fields["data"][$i]["type"] == 't') {
			
		        
                                  global $multilinguallib;
                                  include_once('lib/multilingual/multilinguallib.php');
                                  $multi_languages=$multilinguallib->getSystemLanguage();
                                  $smarty->assign('multi_languages',$multi_languages); 
                                  $ins_fields["data"][$i]['isMultilingual']='y';
				            $compteur=0;
				            foreach ($multi_languages as $num=>$lang){
				            //Case convert normal -> multilingual
				            if (!isset($_REQUEST[$ins_id."_".$lang]) && isset($_REQUEST["$fid"]))
				                $_REQUEST["$fid$lang"]=$_REQUEST["$fid"];
				            $ins_fields["data"][$i]["lingualvalue"][$num]["lang"] = $lang;
				            if (isset($_REQUEST[$ins_id."_".$lang]))
				            $ins_fields["data"][$i]["lingualvalue"][$num]["value"] =     $_REQUEST[$ins_id."_".$lang];
				            $ins_fields["data"][$i]["lingualpvalue"][$num]["lang"] = $lang;
				            if (isset($_REQUEST[$ins_id."_".$lang]))
				            $ins_fields["data"][$i]["lingualpvalue"][$num]["value"] =     $tikilib->parse_data(htmlspecialchars($_REQUEST[$ins_id."_".$lang]));
					    }
				        } 
		}
	} elseif ($xfields["data"][$i]["type"] == "u" and isset($xfields["data"][$i]['options_array'][0]) and $user and $xfields["data"][$i]['options_array'][0] == 1 and isset($tracker_info["writerCanModify"]) and $tracker_info["writerCanModify"] == 'y') {
		// even if field is hidden need to pick up user for perm
		$tracker_info["authorfield"] = $fid;
	} elseif ($xfields["data"][$i]["type"] == "g" and isset($xfields["data"][$i]['options_array'][0]) and $group and $xfields["data"][$i]['options_array'][0] == 1 and isset($tracker_info["writerGroupCanModify"]) and $tracker_info["writerGroupCanModify"] == 'y') {
		// even if field hidden need to pick up the group for perm
		$tracker_info["authorgroupfield"] = $fid;
	}
}

if (isset($tracker_info["authorgroupfield"])) {
	$tracker_info['authorgroup'] = $trklib->get_item_value($_REQUEST["trackerId"],$_REQUEST["itemId"],$tracker_info["authorgroupfield"]);
	if ($tracker_info['authorgroup'] == $group) {
		$tiki_p_modify_tracker_items = 'y';
		$smarty->assign("tiki_p_modify_tracker_items","y");
		$tiki_p_attach_trackers = 'y';
		$smarty->assign("tiki_p_attach_trackers","y");
		$tiki_p_comment_trackers = 'y';
		$smarty->assign("tiki_p_comment_trackers","y");
		$tiki_p_view_trackers = 'y';
		$smarty->assign("tiki_p_view_trackers","y");
	}
}
if (isset($tracker_info["authorfield"])) {
	$tracker_info['authorindiv'] = $trklib->get_item_value($_REQUEST["trackerId"],$_REQUEST["itemId"],$tracker_info["authorfield"]);
	if ($tracker_info['authorindiv'] == $user or $tracker_info['authorindiv'] == '') {
		$tiki_p_modify_tracker_items = 'y';
		$smarty->assign("tiki_p_modify_tracker_items","y");
		$tiki_p_attach_trackers = 'y';
		$smarty->assign("tiki_p_attach_trackers","y");
		$tiki_p_comment_trackers = 'y';
		$smarty->assign("tiki_p_comment_trackers","y");
		$tiki_p_view_trackers = 'y';
		$smarty->assign("tiki_p_view_trackers","y");
	}
}
if ($tiki_p_view_trackers != 'y' && !$special) {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}

if (!isset($mainfield)) {
	$mainfield = 0;
}

if ($textarea_options) {
	include_once ('lib/quicktags/quicktagslib.php');
	$quicktags = $quicktagslib->list_quicktags(0,-1,'taglabel_desc','','wiki');
	$smarty->assign('quicktags', $quicktags["data"]);
}

if ($tiki_p_admin_trackers == 'y' or $tiki_p_modify_tracker_items == 'y') {
	if (isset($_REQUEST["remove"])) {
		check_ticket('view-trackers-items');
		$trklib->remove_tracker_item($_REQUEST["remove"]);
	}
}

if ($tiki_p_modify_tracker_items == 'y' || $special) {
	if (isset($_REQUEST["save"]) || isset($_REQUEST["save_return"])) {

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

		// values are OK, then lets save the item
		if (count($field_errors['err_mandatory']) == 0  && count($field_errors['err_value']) == 0 ) {

			$smarty->assign('input_err', '0'); // no warning to display
	
			check_ticket('view-trackers-items');
			if (!isset($_REQUEST["edstatus"]) or ($tracker_info["showStatus"] != 'y' and $tiki_p_admin_trackers != 'y')) {
				$_REQUEST["edstatus"] = $tracker_info["modItemStatus"];
			}
			$trklib->replace_item($_REQUEST["trackerId"], $_REQUEST["itemId"], $ins_fields, $_REQUEST["edstatus"], $ins_categs);
			if (isset($_REQUEST["newItemRate"])) {
				$trklib->replace_rating($_REQUEST["trackerId"],$_REQUEST["itemId"],$rateFieldId,$user,$_REQUEST["newItemRate"]);
			}
			$mainfield = $ins_fields["data"][$mainfield]["value"];

			$_REQUEST['show']  = 'view';

			foreach($fields["data"] as $i=>$array) {
				if (isset($fields["data"][$i])) {
					$fid = $fields["data"][$i]["fieldId"];
					$ins_id = 'ins_' . $fid;
					$ins_fields["data"][$i]["value"] = '';
				}
			}
			$item_info = $trklib->get_tracker_item($_REQUEST["itemId"]);
			$smarty->assign('item_info', $item_info);
			$trklib->categorized_item($_REQUEST["trackerId"], $_REQUEST["itemId"], $mainfield, $ins_categs);
		}
		else {
			$error = $ins_fields;
			$tabi = "2";
			$smarty->assign('input_err', '1'); // warning to display

			// can't go back if there are errors
			if(isset($_REQUEST['save_return'])) {
				$_REQUEST['save'] = 'save';
				unset($_REQUEST['save_return']);
			}
		}
		
		if (isset($_REQUEST['from'])) {
			header('Location: tiki-index.php?page='.urlencode($_REQUEST['from']));
			exit;
		}
	}
}

// remove image from an image field
if (isset($_REQUEST["removeImage"])) {
	$img_field = array('data' => array());
	$img_field['data'][] = array('fieldId' => $_REQUEST["fieldId"], 'type' => 'i', 'name' => $_REQUEST["fieldName"], 'value' => 'blank');
	$trklib->replace_item($_REQUEST["trackerId"], $_REQUEST["itemId"], $img_field);
	$_REQUEST['show'] = "mod";
}

// ************* return to list ***************************
if(isset($_REQUEST["returntracker"]) || isset($_REQUEST["save_return"])) {
	$urlreturn = "tiki-view_tracker.php?trackerId={$_REQUEST['trackerId']}&sort_mode={$_REQUEST['sort_mode']}&offset={$_REQUEST['offset']}";
	foreach ($urlquery as $fldkey=>$fldval) {if ($fldval) { $urlreturn .= "&{$fldkey}=".urlencode($fldval); } }			header("Location: $urlreturn");
	die;
}
// ********************************************************

if (isset($tracker_info['useRatings']) and $tracker_info['useRatings'] == 'y' and $tiki_p_tracker_view_ratings == 'y') {
	if ($user and $tiki_p_tracker_vote_ratings == 'y' and isset($_REQUEST['rate']) and isset($_REQUEST['fieldId'])) {
		$trklib->replace_rating($_REQUEST['trackerId'],$_REQUEST['itemId'],$_REQUEST['fieldId'],$user,$_REQUEST["rate"]);
		header('Location: tiki-view_tracker_item.php?trackerId='.$_REQUEST['trackerId'].'&itemId='.$_REQUEST['itemId']);
	}
	$item['my_rate'] = $tikilib->get_user_vote("tracker.".$_REQUEST['trackerId'].'.'.$_REQUEST['itemId'],$user);
	$item['itemId'] = $itemId;
	$item['trackerId'] = $trackerId;
	$smarty->assign('item',$item);
}

if ($_REQUEST["itemId"]) {
	$info = $trklib->get_tracker_item($_REQUEST["itemId"]);
	if (!isset($info['trackerId'])) $info['trackerId'] = $_REQUEST['trackerId'];
	if ((isset($info['status']) and $info['status'] == 'p' && !$tikilib->user_has_perm_on_object($user, $info['trackerId'], 'tracker', 'tiki_p_view_trackers_pending')) 
	||  (isset($info['status']) and $info['status'] == 'c' && !$tikilib->user_has_perm_on_object($user, $info['trackerId'], 'tracker', 'tiki_p_view_trackers_closed'))
	||  ($tiki_p_admin_trackers != 'y' && !$tikilib->user_has_perm_on_object($user, $info['trackerId'], 'tracker', 'tiki_p_view_trackers') &&
	  (!isset($utid) || $_REQUEST['trackerId'] != $utid['usersTrackerId']) &&
		(!isset($gtid) || $_REQUEST['trackerId'] != $utid['groupTrackerId']) &&
		 ($tracker_info['writerCanModify'] != 'y' || $user != $trklib->get_item_creator($info['trackerId'], $_REQUEST['itemId']))
	) ) {
		$smarty->assign('msg', tra('Permission denied'));
		$smarty->display('error.tpl');
		die;
	}
	$last = array();
	$lst = '';

	foreach($xfields["data"] as $i=>$array) {
		if ($xfields["data"][$i]['isHidden'] == 'n' or $xfields["data"][$i]['isHidden'] == 'p' or $tiki_p_admin_trackers == 'y' or ($xfields["data"][$i]['type'] == 's'and $tiki_p_tracker_view_ratings == 'y')or ($xfields['data'][$i]['isHidden'] == 'c' && !empty($user) && $user == $trklib->get_item_creator($_REQUEST['trackerId'], $_REQUEST['itemId']))) {
			$fields["data"][$i] = $xfields["data"][$i];
			if ($fields["data"][$i]["type"] != 'h') {
				$fid = $fields["data"][$i]["fieldId"];
				$ins_fields["data"][$i]["id"] = $fid;
				if ($fields["data"][$i]["type"] == 'c') {
					if (!isset($info["$fid"])) $info["$fid"] = 'n';
				} else {
					if (!isset($info["$fid"])) $info["$fid"] = '';
				}
				if ($fields["data"][$i]["type"] == 'e') {
					global $categlib; include_once('lib/categories/categlib.php');
					$k = $fields["data"][$i]['options_array'][0];
					$ins_fields["data"][$i]["$k"] = $categlib->get_child_categories($k);
					if (!isset($cat)) {
						$cat = $categlib->get_object_categories("tracker ".$_REQUEST["trackerId"],$_REQUEST["itemId"]);
					}
					if (isset($_REQUEST['save']) || isset($_REQUEST['save_return'])) {
						foreach ($ins_fields["data"][$i]["$k"] as $c) {
							if (in_array($c['categId'], $ins_categs)) {
								$ins_fields['data'][$i]['cat'][$c['categId']] = 'y';
								$ins_fields['data'][$i]['categs'][] = $categlib->get_category($c['categId']);
							}
						}
					} else {
						foreach ($ins_fields["data"][$i]["$k"] as $c) {
							if (in_array($c['categId'], $cat)) {
								$ins_fields['data'][$i]['cat'][$c['categId']] = 'y';
								$ins_fields['data'][$i]['categs'][] = $categlib->get_category($c['categId']);
							}
						}
					}
				} elseif ($fields["data"][$i]["type"] == 'l') {
					if (isset($fields["data"][$i]["options_array"][3])) {
						$l = split(':', $fields["data"][$i]["options_array"][1]);
						$ins_fields["data"][$i]['links'] = $trklib->get_join_values($_REQUEST['itemId'], array_merge(array($fields["data"][$i]["options_array"][2]), $l, array($fields["data"][$i]["options_array"][3])));

						if (count($ins_fields["data"][$i]['links']) == 1) {
							foreach ($ins_fields["data"][$i]['links'] as $linkItemId=>$linkValue) {
								if (is_numeric($ins_fields["data"][$i]['links'][$linkItemId])) { //if later a computed field use this field
									$info[$fields['data'][$i]['fieldId']] = $linkValue;
								}
							}
						}
						$ins_fields["data"][$i]['trackerId'] = $fields["data"][$i]["options_array"][0];
						if (!isset($tracker_options_l[$fields["data"][$i]["options_array"][0]])) {
							$tracker_options_l[$fields["data"][$i]["options_array"][0]] = $trklib->get_tracker_options($fields["data"][$i]["options_array"][0]);
						}
						$ins_fields["data"][$i]['tracker_options'] = $tracker_options_l[$fields["data"][$i]["options_array"][0]];
					}
					
				} elseif  ($fields["data"][$i]["type"] == 'r') {
					$ins_fields["data"][$i]["linkId"] = $trklib->get_item_id($fields["data"][$i]["options_array"][0],$fields["data"][$i]["options_array"][1],$info[$fid]);
					$ins_fields["data"][$i]["value"] = $info[$fid];
					$ins_fields["data"][$i]["list"] = $trklib->get_all_items($fields["data"][$i]["options_array"][0],$fields["data"][$i]["options_array"][1]);
					if (isset($fields["data"][$i]["options_array"][3]))
					{
					       $ins_fields["data"][$i]["displayedvalue"] =$trklib->concat_item_from_fieldslist($fields["data"][$i]["options_array"][0],$trklib->get_item_id($fields["data"][$i]["options_array"][0],$fields["data"][$i]["options_array"][1],$info[$fid]),$fields["data"][$i]["options_array"][3]) ;
					       $ins_fields["data"][$i]["listdisplay"] =$trklib->concat_all_items_from_fieldslist($fields["data"][$i]["options_array"][0],$fields["data"][$i]["options_array"][3]);
					 }
				} elseif ($fields["data"][$i]["type"] == 'u') {
					if (isset($fields["data"][$i]['options_array'][0]) && $fields["data"][$i]['options_array'][0] == 2 and !$info["$fid"]) {
						$ins_fields["data"][$i]["defvalue"] = $user;
					}
					$ins_fields["data"][$i]["value"] = $info["$fid"];
				} elseif ($fields["data"][$i]["type"] == 'G') {
					$ins_fields["data"][$i]["value"] = $info["$fid"];
					$first_comma=strpos($info["$fid"],',');
					$second_comma=strpos($info["$fid"],',',$first_comma+1);
					if(!$second_comma){
					  $second_comma=strlen($info["$fid"]);
					  $ins_fields["data"][$i]["value"].=",11";
					}
					$ins_fields["data"][$i]["x"] = substr($ins_fields["data"][$i]["value"],0,$first_comma);
					$ins_fields["data"][$i]["y"] = substr($ins_fields["data"][$i]["value"],$first_comma+1,$second_comma-$first_comma-1);
					$ins_fields["data"][$i]["z"] = substr($ins_fields["data"][$i]["value"],$second_comma+1);
				} elseif ($fields["data"][$i]["type"] == 'U') {
					$ins_fields["data"][$i]["value"]=$info["$fid"];
					$temp=$userlib->get_user_info($user);
					$id_user=$temp['userId'];
					$id_tiki_user=$temp['userId'];

					$pattern="/(\d+)\[(\d+)\]/";
					preg_match_all($pattern,$ins_fields["data"][$i]["value"],$match);
					$users_array=array();
					$ins_fields["data"][$i]["user_subscription"]=FALSE;
					$U_nb_users=0;
					$ins_fields["data"][$i]["user_nb_friends"]=0;
					foreach($match[1] as $j=>$id_user){
					  $temp=$userlib->get_userId_info($id_user);
					  array_push($users_array,
						     array('id'=>$id_user,'login'=>$temp['login'],'friends'=>$match[2][$j])
						     );
					  $U_nb_users+=$match[2][$j]+1;
					  if($id_user==$id_tiki_user){
					    $ins_fields["data"][$i]["user_subscription"]=TRUE;
					    $ins_fields["data"][$i]["user_nb_friends"]=$match[2][$j];
					  }
					}
					$ins_fields["data"][$i]["users_array"]=array();
					$ins_fields["data"][$i]["users_array"]=$users_array;

					$U_maxsubscriptions=substr($info["$fid"],0,strpos($info["$fid"],'#'));
					$ins_fields["data"][$i]["maxsubscriptions"]=$U_maxsubscriptions;

					$U_liste=NULL;
					$U_othersubscriptions=$ins_fields["data"][$i]["user_nb_friends"];
					if(!$ins_fields["data"][$i]["user_subscription"]){
					  $U_othersubscriptions--;
					}					  
					if($U_maxsubscriptions){
					  for($j=0 ; $j <= $U_maxsubscriptions-$U_nb_users+$U_othersubscriptions ; $j++){
					    $U_liste[$j]=$j;
					  }
					}
					$smarty->assign("U_liste",$U_liste);

				} elseif ($fields["data"][$i]["type"] == 'C') {
					$calc = preg_replace('/#([0-9]+)/','$info[\1]',$fields["data"][$i]['options_array'][0]);
					eval('$computed = '.$calc.';');
					$ins_fields["data"][$i]["value"] = $computed;
					$info[$fields['data'][$i]['fieldId']] = $computed; // in case a computed field use this one
				} elseif ($fields["data"][$i]["type"] == 'g') {
					if (isset($fields["data"][$i]['options_array'][0]) && $fields["data"][$i]['options_array'][0] == 2 and !$info["$fid"]) {
						$ins_fields["data"][$i]["defvalue"] = $group;
					}
					$ins_fields["data"][$i]["value"] = $info["$fid"];
				} elseif ($fields["data"][$i]["type"] == 'a' || $fields["data"][$i]["type"] == 't') {
				        if ($fields["data"][$i]["isMultilingual"] == 'y') {
                                  
                                  
                                            global $multilinguallib;
                                            include_once('lib/multilingual/multilinguallib.php');
                                            $multi_languages=$prefs['available_languages'];
                                            $smarty->assign('multi_languages',$multi_languages); 
                                            $ins_fields["data"][$i]['isMultilingual']='y';
				            $compteur=0;
				            foreach ($multi_languages as $num=>$lang){
				            //Case convert normal -> multilingual
				            if (!isset($info["$fid$lang"]) && isset($info["$fid"]))
				                $info["$fid$lang"]=$info["$fid"];
				            $ins_fields["data"][$i]["lingualvalue"][$num]["lang"] = $lang;
				            $ins_fields["data"][$i]["lingualvalue"][$num]["value"] =     $info["$fid$lang"];
				            $ins_fields["data"][$i]["lingualpvalue"][$num]["lang"] = $lang;
				            $ins_fields["data"][$i]["lingualpvalue"][$num]["value"] =     $tikilib->parse_data(htmlspecialchars($info["$fid$lang"]));
					    }
					    //For display only
					     $ins_fields["data"][$i]["value"] = $info[$fid.$prefs['language']];
					     $ins_fields["data"][$i]["pvalue"] = $tikilib->parse_data(htmlspecialchars($info[$fid.$prefs['language']]));
				        } else {
					     $ins_fields["data"][$i]["value"] = $info["$fid"];
					     $ins_fields["data"][$i]["pvalue"] = $tikilib->parse_data(htmlspecialchars($info["$fid"]));
					}
	
				} else {
					$ins_fields["data"][$i]["value"] = $info["$fid"];
				}
			if ($fields['data'][$i]['type'] == 'M' ) {
			global $filegallib, $prefs;
			if ( $prefs['URLAppend'] == '' ) { list ($val1,$val2)=split('=', $ins_fields["data"][$i]["value"]); }
			else { $val2=$ins_fields["data"][$i]["value"];}
			$res=$filegallib->get_file_info($val2);
			if ( $res["filetype"] == "video/x-flv" ) { $ModeVideo = 'y' ;}
			else { $ModeVideo = 'n' ;} ;
			$smarty->assign('ModeVideo', $ModeVideo);
			}

				if ($fields['data'][$i]['type'] == 'i' && !empty($ins_fields["data"][$i]['options_array'][2]) && !empty($ins_fields['data'][$i]['value'])) {
					global $imagegallib; include_once('lib/imagegals/imagegallib.php');
					if ($imagegallib->readimagefromfile($ins_fields['data'][$i]['value'])) {
						$imagegallib->getimageinfo();
						if (!isset($ins_fields["data"][$i]['options_array'][3]))
							$ins_fields["data"][$i]['options_array'][3] = 0;
						$t = $imagegallib->ratio($imagegallib->xsize, $imagegallib->ysize, $ins_fields["data"][$i]['options_array'][2], $ins_fields["data"][$i]['options_array'][3] );
						$ins_fields['data'][$i]['options_array'][2] = $t * $imagegallib->xsize;
						$ins_fields['data'][$i]['options_array'][3] = $t * $imagegallib->ysize;
					}
				}
				if (isset($ins_fields["data"][$i]["value"])) {
					$last[$fid] = $ins_fields["data"][$i]["value"];
				}
			}
			if ($fields['data'][$i]['isMain'] == 'y')
				$smarty->assign('tracker_item_main_value', $ins_fields['data'][$i]['value']);			
		}
	}

/* **************** seems it is only 1.8
	for ($i = 0; $i < count($fields["data"]); $i++) {
		$name = $fields["data"][$i]["name"];

		$ins_name = 'ins_' . $name;
        if ($fields["data"][$i]['type'] == 'f') {
            $ins_fields["data"][$i]["value"] = 
                    smarty_make_timestamp($info["$name"]);
        } else {
            $ins_fields["data"][$i]["value"] = $info["$name"];
        }
	}
******************* */
}
//restore types values if there is an error
if(isset($error)) {
	foreach($ins_fields["data"] as $i=>$array) {
		if (isset($error["data"][$i]["value"])) {
			$ins_fields["data"][$i]["value"] = $error["data"][$i]["value"];
		}
	}
}

// dynamic list process
$id_fields = array();
foreach ($xfields['data'] as $sid => $onefield) {
	$id_fields[$xfields['data'][$sid]['fieldId']] = $sid;
}
foreach ($ins_fields['data'] as $sid => $onefield) {
	if ($ins_fields['data'][$sid]['type'] == 'w') {
		if ( ! isset($ins_fields['data'][$id_fields[$ins_fields['data'][$sid]['options_array'][2]]]['http_request']))
			$ins_fields['data'][$id_fields[$ins_fields['data'][$sid]['options_array'][2]]]['http_request'] = array('','','','','','','','','');
		for($i = 0; $i < 5; $i++)
		{
			$ins_fields['data'][$id_fields[$ins_fields['data'][$sid]['options_array'][2]]]['http_request'][$i] .=
				($ins_fields['data'][$id_fields[$ins_fields['data'][$sid]['options_array'][2]]]['http_request'][$i] ? "," : "") .
				 $ins_fields['data'][$sid]['options_array'][$i];
		}
		$ins_fields['data'][$id_fields[$ins_fields['data'][$sid]['options_array'][2]]]['http_request'][5] .=
			($ins_fields['data'][$id_fields[$ins_fields['data'][$sid]['options_array'][2]]]['http_request'][5] ? "," : "") .
			 $ins_fields['data'][$sid]['fieldId'];
		$ins_fields['data'][$id_fields[$ins_fields['data'][$sid]['options_array'][2]]]['http_request'][6] .=
			($ins_fields['data'][$id_fields[$ins_fields['data'][$sid]['options_array'][2]]]['http_request'][6] ? "," : "") .
			 $ins_fields['data'][$sid]['isMandatory'];
		$ins_fields['data'][$id_fields[$ins_fields['data'][$sid]['options_array'][2]]]['http_request'][7] =
			$ins_fields['data'][$id_fields[$ins_fields['data'][$sid]['options_array'][2]]]['value'];
		$ins_fields['data'][$id_fields[$ins_fields['data'][$sid]['options_array'][2]]]['http_request'][8] .=
			($ins_fields['data'][$id_fields[$ins_fields['data'][$sid]['options_array'][2]]]['http_request'][8] ? "," : "") .
			($ins_fields['data'][$sid]['value'] ? $ins_fields['data'][$sid]['value'] : " ");
		$ins_fields['data'][$sid]['filter_value'] =
			$ins_fields['data'][$id_fields[$ins_fields['data'][$sid]['options_array'][2]]]['value'];
	}
}

$smarty->assign('id_fields', $id_fields);
$smarty->assign('trackerId', $_REQUEST["trackerId"]);
$smarty->assign('tracker_info', $tracker_info);
$smarty->assign_by_ref('info', $info);
$smarty->assign_by_ref('fields', $fields["data"]);
$smarty->assign_by_ref('ins_fields', $ins_fields["data"]);

$users = $userlib->list_all_users();
$smarty->assign_by_ref('users', $users);
$groups = $userlib->list_all_groups();
$smarty->assign_by_ref('groups', $groups);

if ($prefs['feature_user_watches'] == 'y' and $tiki_p_watch_trackers == 'y') {
  if ($user and isset($_REQUEST['watch'])) {
    check_ticket('view-trackers');
    if ($_REQUEST['watch'] == 'add') {
      $tikilib->add_user_watch($user, 'tracker_item_modified', $_REQUEST["itemId"], 'tracker '.$_REQUEST["trackerId"], $tracker_info['name'],"tiki-view_tracker_item.php?trackerId=".$_REQUEST["trackerId"]."&amp;itemId=".$_REQUEST["itemId"]);
    } else {
      $tikilib->remove_user_watch($user, 'tracker_item_modified', $_REQUEST["itemId"]);
    }
  }
  $smarty->assign('user_watching_tracker', 'n');
  $it = $tikilib->user_watches($user, 'tracker_item_modified', $_REQUEST['itemId'], 'tracker '.$_REQUEST["trackerId"]);
  if ($user and $tikilib->user_watches($user, 'tracker_item_modified', $_REQUEST['itemId'], 'tracker '.$_REQUEST["trackerId"])) {
    $smarty->assign('user_watching_tracker', 'y');
  }
    
    // Check, if the user is watching this trackers' item by a category.
	if ($prefs['feature_categories'] == 'y') {    			
	    $watching_categories_temp=$categlib->get_watching_categories($_REQUEST['trackerId'],'tracker',$user);	    
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

if ($tracker_info["useComments"] == 'y') {
	if ($tiki_p_admin_trackers == 'y' and isset($_REQUEST["remove_comment"])) {
		$area = 'deltrackercomment';
		if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
			key_check($area);
			$trklib->remove_item_comment($_REQUEST["remove_comment"]);
		} else {
  	  key_get($area);
	  }
	}
	if (isset($_REQUEST["commentId"])) {
		$comment_info = $trklib->get_item_comment($_REQUEST["commentId"]);
		$smarty->assign('comment_title', $comment_info["title"]);
		$smarty->assign('comment_data', $comment_info["data"]);
		$tabi = 2;
	} else {
		$_REQUEST["commentId"] = 0;
		$smarty->assign('comment_title', '');
		$smarty->assign('comment_data', '');
	}
	$smarty->assign('commentId', $_REQUEST["commentId"]);
	if ($_REQUEST["commentId"] && $tiki_p_admin_trackers != 'y') {
		$_REQUEST["commentId"] = 0;
	}
	if ($tiki_p_comment_tracker_items == 'y') {
		if (isset($_REQUEST["save_comment"])) {
			check_ticket('view-trackers-items');
			$trklib->replace_item_comment($_REQUEST["commentId"], $_REQUEST["itemId"], $_REQUEST["comment_title"], $_REQUEST["comment_data"], $user, $tracker_info);
			$smarty->assign('comment_title', '');
			$smarty->assign('comment_data', '');
			$smarty->assign('commentId', 0);
		}
	}
	$comments = $trklib->list_item_comments($_REQUEST["itemId"], 0, -1, 'posted_desc', '');
	$smarty->assign_by_ref('comments', $comments["data"]);
	$smarty->assign_by_ref('commentCount', $comments["cant"]);
}

if ($tracker_info["useAttachments"] == 'y') {
	if (isset($_REQUEST["removeattach"])) {
		check_ticket('view-trackers-items');
		$owner = $trklib->get_item_attachment_owner($_REQUEST["removeattach"]);
		if (($user && ($owner == $user)) || ($tiki_p_wiki_admin_attachments == 'y')) {
			$area = 'deltrackerattach';
			if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
				key_check($area);
				$trklib->remove_item_attachment($_REQUEST["removeattach"]);
			} else {
    		key_get($area);
		  }
		}
	$_REQUEST["show"] = "att";
	}
	if (isset($_REQUEST["editattach"])) {
		$att = $trklib->get_item_attachment($_REQUEST["editattach"]);
		$smarty->assign("attach_comment", $att['comment']);
		$smarty->assign("attach_version", $att['version']);
		$smarty->assign("attach_longdesc", $att['longdesc']);
		$smarty->assign("attach_file", $att["filename"]);
		$smarty->assign("attId", $att["attId"]);
		$_REQUEST["show"] = "att";
	}
	if (isset($_REQUEST["attach"]) && ($tiki_p_attach_trackers == 'y')) {
		// Process an attachment here
		if (isset($_FILES['userfile1']) && is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
			$fp = fopen($_FILES['userfile1']['tmp_name'], "rb");
			$data = '';
			$fhash = '';
			if ($prefs['t_use_db'] == 'n') {
				$fhash = md5($name = $_FILES['userfile1']['name']);
				$fw = fopen($prefs['t_use_dir'] . $fhash, "wb");
				if (!$fw) {
					$smarty->assign('msg', tra('Cannot write to this file:'). $fhash);
					$smarty->display("error.tpl");
					die;
				}
			}
			while (!feof($fp)) {
				if ($prefs['t_use_db'] == 'y') {
					$data .= fread($fp, 8192 * 16);
				} else {
					$data = fread($fp, 8192 * 16);
					fwrite($fw, $data);
				}
			}
			fclose ($fp);
			if ($prefs['t_use_db'] == 'n') {
				fclose ($fw);
				$data = '';
			}
			$size = $_FILES['userfile1']['size'];
			$name = $_FILES['userfile1']['name'];
			$type = $_FILES['userfile1']['type'];
		} else {
			$name = "";
			$size = "";
			$type = "";
			$data = "";
			$fhash="";
		}
		if (empty($_REQUEST["attId"]) || $_REQUEST["attId"] == 0) {
			$trklib->item_attach_file($_REQUEST["itemId"], $name, $type, $size, $data, $_REQUEST["attach_comment"], $user, $fhash,$_REQUEST["attach_version"],$_REQUEST["attach_longdesc"]);
		} else {
			$trklib->replace_item_attachment($_REQUEST["attId"], $name, $type, $size, $data, $_REQUEST["attach_comment"], $user, $fhash,$_REQUEST["attach_version"],$_REQUEST["attach_longdesc"]);
		}
		$_REQUEST["attId"] = 0;
		$_REQUEST['show'] = "att";
	}

	// If anything below here is changed, please change lib/wiki-plugins/wikiplugin_attach.php as well.
	$attextra = 'n';
	if (strstr($tracker_info["orderAttachments"],'|')) {
		$attextra = 'y';
	}
	$attfields = split(',',strtok($tracker_info["orderAttachments"],'|'));
	$atts = $trklib->list_item_attachments($_REQUEST["itemId"], 0, -1, 'comment_asc', '');
	$smarty->assign('atts', $atts["data"]);
	$smarty->assign('attCount', $atts["cant"]);
	$smarty->assign('attfields', $attfields);
	$smarty->assign('attextra', $attextra);
}
if (isset($_REQUEST['show'])) {
	if ($_REQUEST['show'] == 'view') {
		$tabi = 1;
	} elseif ($tracker_info["useComments"] == 'y' and $_REQUEST['show'] == 'com') {
		$tabi = 2;
	} elseif ($_REQUEST['show'] == "mod") {
		$tabi = 2;
		if ($tracker_info["useAttachments"] == 'y') $tabi++;
		if ($tracker_info["useComments"] == 'y') $tabi++;
	} elseif ($_REQUEST['show'] == "att") {
		$tabi = 2;
		if ($tracker_info["useComments"] == 'y') $tabi = 3;	
	}
}

setcookie("tab","$tabi");
$smarty->assign('cookietab',$tabi);

if (isset($_REQUEST['from'])) {
	$from = $_REQUEST['from'];
} else {
	$from = false;
}
$smarty->assign('from',$from);

if (isset($_REQUEST['status']))
	$smarty->assign_by_ref('status', $_REQUEST['status']);

$section = 'trackers';
include_once ('tiki-section_options.php');

$smarty->assign('uses_tabs', 'y');

if ($prefs['feature_jscalendar']) {
	$smarty->assign('uses_jscalendar', 'y');
}

ask_ticket('view-trackers-items');

if ( $prefs['feature_ajax'] == 'y' ) {
	require_once ("lib/ajax/ajaxlib.php");
	$ajaxlib->registerTemplate('tiki-view_tracker_item.tpl');
	$ajaxlib->processRequests();
}

// Display the template
$smarty->assign('mid', 'tiki-view_tracker_item.tpl');
$smarty->display("tiki.tpl");

?>
