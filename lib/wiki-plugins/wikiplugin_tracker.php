<?php
// $Id: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_tracker.php,v 1.85.2.26 2008-03-19 13:32:42 sylvieg Exp $
// Includes a tracker field
// Usage:
// {TRACKER()}{TRACKER}

function wikiplugin_tracker_help() {
	$help = tra("Displays an input form for tracker submit").":\n";
	$help.= "~np~{TRACKER(trackerId=1, fields=id1:id2:id3, action=Name of submit button, showtitle=n, showdesc=n, showmandatory=n, embedded=n, url=\"http://site.com\", values=val1:val2:val3, sort=n, preview=preview, view=user, tpl=x.tpl,wiki=page,newstatus=o|p|c)}Notice{TRACKER}~/np~";
	return $help;
}
function wikiplugin_tracker_name($fieldId, $name, $field_errors) {
	foreach($field_errors['err_mandatory'] as $f) {
		if ($fieldId == $f['fieldId'])
			return '<span class="highlight">'.$name.'</span>';
	}
	foreach($field_errors['err_value'] as $f) {
		if ($fieldId == $f['fieldId'])
			return '<span class="highlight">'.$name.'</span>';
	}
	return $name;
}
function wikiplugin_tracker($data, $params) {
	global $tikilib, $userlib, $dbTiki, $user, $group, $page, $tiki_p_admin, $tiki_p_create_tracker_items, $smarty, $prefs, $trklib, $tiki_p_tracker_vote_ratings;
	include_once('lib/trackers/trackerlib.php');
	
	//var_dump($_REQUEST);
	extract ($params,EXTR_SKIP);

	if ($prefs['feature_trackers'] != 'y') {
		return $smarty->fetch("wiki-plugins/error_tracker.tpl");
	}
	if (empty($trackerId) || !($tracker = $trklib->get_tracker($trackerId))) {
		return $smarty->fetch("wiki-plugins/error_tracker.tpl");
	}
	if ($t = $trklib->get_tracker_options($trackerId)) {
		$tracker = array_merge($tracker,$t);
	}
	if (empty($trackerId) && !empty($view) && $view == 'user' && $prefs['userTracker'] == 'y') { // the user tracker item
		$utid = $userlib->get_tracker_usergroup($user);
		if (!empty($utid) && !empty($utid['usersTrackerId'])) {
			$itemId = $trklib->get_item_id($utid['usersTrackerId'],$utid['usersFieldId'],$user);
			$trackerId = $utid['usersTrackerId'];
		}
	} elseif (!empty($trackerId) && !empty($view) && $view == 'user') {// the user item of a tracker
		$itemId = $trklib->get_user_item($trackerId, $tracker);
	}

	if (!isset($trackerId)) {
		return $smarty->fetch("wiki-plugins/error_tracker.tpl");
	}
	if (!isset($embedded)) {
		$embedded = "n";
	}
	if (!isset($showtitle)) {
		$showtitle = "n";
	}
	if (!isset($showdesc)) {
		$showdesc = "n";
	}
	if (!isset($sort)) {
		$sort = 'n';
	}
	if (!isset($action)) {
		$action = 'Save';
	}
	if (isset($preview)) {
		if (empty($preview)) {
			$preview = 'Preview';
		}
	} else {
		unset($_REQUEST['tr_preview']);
	}
	if (!isset($showmandatory)) {
		$showmandatory = 'y';
	}
		$smarty->assign('showmandatory', $showmandatory); 

	if (!isset($permMessage)) {
		$permMessage = tra("You do not have permission to insert an item");
	}
	if (isset($values)) {
		if (!is_array($values)) {
			$values = explode(':', $values);
		}
	}
	if (isset($_REQUEST['values'])) {
		if (is_array($_REQUEST['values'])) {
			foreach ($_REQUEST['values'] as $i=>$k) {
				$_REQUEST['values'][$i] = urldecode($k);
			}
		} else {
			$_REQUEST['values'] = urldecode($_REQUEST['values']);
		}
	}

	if (isset($_SERVER['SCRIPT_NAME']) && !strstr($_SERVER['SCRIPT_NAME'],'tiki-register.php') && empty($itemId)) {
		if (!$tikilib->user_has_perm_on_object($user, $trackerId, 'tracker', 'tiki_p_create_tracker_items')) {
			return '<b>'.$permMessage.'</b>';
		}
	}

	if (isset($_REQUEST['removeattach']) && $tracker['useAttachments'] == 'y') {
		$owner = $trklib->get_item_attachment_owner($_REQUEST['removeattach']);
		if ($tiki_p_wiki_admin_attachments == 'y' || ($user && $user == $owner)) {
			$trklib->remove_item_attachment($_REQUEST["removeattach"]);
			unset($_REQUEST['removeattach']);
		}
	}
	if (isset($_REQUEST['removeImage']) && !empty($_REQUEST['trackerId']) && !empty($_REQUEST['itemId']) && !empty($_REQUEST['fieldId']) && !empty($_REQUEST['fieldName'])) {
		$img_field = array('data' => array());
		$img_field['data'][] = array('fieldId' => $_REQUEST['fieldId'], 'type' => 'i', 'name' => $_REQUEST['fieldName'], 'value' => 'blank');
		$trklib->replace_item($_REQUEST['trackerId'], $_REQUEST['itemId'], $img_field);
	}
	$back = '';

	$thisIsThePlugin = isset($_REQUEST['trackit']) && $_REQUEST['trackit'] == $trackerId && ((isset($_REQUEST['fields']) && isset($params['fields']) && $_REQUEST['fields'] == $params['fields']) || (!isset($_REQUEST['fields']) && !isset($params['fields'])));

	if (!isset($_REQUEST["ok"]) || $_REQUEST["ok"]  == "n" || !$thisIsThePlugin || isset($_REQUEST['tr_preview'])) {
	
		$field_errors = array('err_mandatory'=>array(), 'err_value'=>array());
	
			global $notificationlib; include_once('lib/notifications/notificationlib.php');
			$tracker = $trklib->get_tracker($trackerId);
			$tracker = array_merge($tracker,$trklib->get_tracker_options($trackerId));
			if ((!empty($tracker['start']) && $tikilib->now < $tracker['start']) || (!empty($tracker['end']) && $tikilib->now > $tracker['end']))
				return;
			$flds = $trklib->list_tracker_fields($trackerId,0,-1,"position_asc","");
			$bad = array();
			$embeddedId = false;
			$onemandatory = false;
			$full_fields = array();
			$mainfield = '';

			if ($thisIsThePlugin) {
				/* ------------------------------------- Recup all values from REQUEST -------------- */
				$cpt = 0;
				if (isset($fields)) {
					$fields_plugin = split(':', $fields);
				}
				foreach ($flds['data'] as $fl) {
					// store value to display it later if form
					// isn't fully filled.
					if ($flds['data'][$cpt]['type'] == 'f') {
						$ins_id = 'track_'.$fl['fieldId'];
						if (isset($_REQUEST[$ins_id.'Day'])) {
							if (empty($_REQUEST['$ins_id'.'Hour'])) {
								$_REQUEST['$ins_id'.'Hour'] = 0;
							}
							if (empty($_REQUEST['$ins_id'.'Minute'])) {
								$_REQUEST['$ins_id'.'Minute'] = 0;
							}
							$_REQUEST['track'][$fl['fieldId']] = $tikilib->make_time($_REQUEST["$ins_id" . "Hour"], $_REQUEST["$ins_id" . "Minute"], 0, $_REQUEST["$ins_id" . "Month"], $_REQUEST["$ins_id" . "Day"], $_REQUEST["$ins_id" . "Year"]);
						} else {
							$_REQUEST['track'][$fl['fieldId']] = $tikilib->now;
						}
					}
					if (isset($_REQUEST['ins_cat_'.$fl['fieldId']])) { // to remember if error
						$_REQUEST['track'][$fl['fieldId']] = $_REQUEST['ins_cat_'.$fl['fieldId']];
					}
					if(isset($_REQUEST['track'][$fl['fieldId']])) {
						$flds['data'][$cpt]['value'] = $_REQUEST['track'][$fl['fieldId']];
					} else {
						$flds['data'][$cpt]['value'] = '';
						if ($fl['type'] == 'c' && (empty($fields_plugin) || in_array($fl['fieldId'], $fields_plugin))) {
							$_REQUEST['track'][$fl['fieldId']] = 'n';
						} elseif ($fl['type'] == 'R' && $fl['isMandatory'] == 'y' && !isset($_REQUEST['track'][$fl['fieldId']])) {
							// if none radio is selected, there will be no value and no error if mandatory
							if (empty($fields_plugin) || in_array($fl['fieldId'], $fields_plugin)) {
								$_REQUEST['track'][$fl['fieldId']] = '';
							}
						}
					}
					if (!empty($_REQUEST['other_track'][$fl['fieldId']])) {
						$flds['data'][$cpt]['value'] = $_REQUEST['other_track'][$fl['fieldId']];
					}
					$full_fields[$fl['fieldId']] = $fl;
					
					if ($embedded == 'y' and $fl['name'] == 'page') {
						$embeddedId = $fl['fieldId'];
					}
					if ($fl['isMain'] == 'y')
						$mainfield = $flds['data'][$cpt]['value'];
					$cpt++;
				} /*foreach */

				if (isset($_REQUEST['track'])) {
					foreach ($_REQUEST['track'] as $fld=>$val) {
						//$ins_fields["data"][] = array('fieldId' => $fld, 'value' => $val, 'type' => 1);
						if (!empty($_REQUEST['other_track'][$fld])) {
							$val = $_REQUEST['other_track'][$fld];
						}
						$ins_fields["data"][] = array_merge(array('value' => $val), $full_fields[$fld]);
					}
				}
				if (isset($_FILES['track'])) {// image or attachment fields
					foreach ($_FILES['track'] as $label=>$w) {
						foreach ($w as $fld=>$val) {
							if ($label == 'tmp_name' && is_uploaded_file($val)) {
								$fp = fopen( $val, 'rb' );
								$data = '';
								while (!feof($fp)) {
									$data .= fread($fp, 8192 * 16);
								}
								fclose ($fp);
								$files[$fld]['value'] = $data;
							} else {
								$files[$fld]['file_'.$label] = $val;
							}
						}
					}
					foreach ($files as $fld=>$file) {
						$ins_fields['data'][] = array_merge($file, $full_fields[$fld]);
					}
				}

				if (isset($_REQUEST['authorfieldid']) and $_REQUEST['authorfieldid']) {
					$val = !empty($user)? $user: (isset($_REQUEST['name'])? $_REQUEST['name']: '');
					$ins_fields["data"][] = array('fieldId' => $_REQUEST['authorfieldid'], 'value' => $val, 'type' => 'u', 'options' => 1);
				}
				if (isset($_REQUEST['authoripid']) and $_REQUEST['authoripid']) {
					$val = !empty($_SERVER['REMOTE_ADDR'])? $_SERVER['REMOTE_ADDR']: '';
					$ins_fields["data"][] = array('fieldId' => $_REQUEST['authoripid'], 'value' => $val, 'type' => 'I', 'options' => 1);
				}
				if (isset($_REQUEST['authorgroupfieldid']) and $_REQUEST['authorgroupfieldid']) {
					$ins_fields["data"][] = array('fieldId' => $_REQUEST['authorgroupfieldid'], 'value' => $group, 'type' => 'g', 'options' => 1);
				}
				if ($embedded == 'y' && isset($_REQUEST['page'])) {
					$ins_fields["data"][] = array('fieldId' => $embeddedId, 'value' => $_REQUEST['page']);
				}
				$ins_categs = array();
				$categorized_fields = array();
				while (list($postVar, $postVal) = each($_REQUEST)) {
					if(preg_match("/^ins_cat_([0-9]+)/", $postVar, $m)) {
						foreach ($postVal as $v)
 	   						$ins_categs[] = $v;
						$categorized_fields[] = $m[1];
					}
		 		}
				/* ------------------------------------- End recup all values from REQUEST -------------- */

				/* ------------------------------------- Check field values for each type and presence of mandatory ones ------------------- */
				$field_errors = $trklib->check_field_values($ins_fields, $categorized_fields);

				if (empty($user) && $prefs['feature_antibot'] == 'y') {
					if((!isset($_SESSION['random_number']) || $_SESSION['random_number'] != $_REQUEST['antibotcode'])) {
						$field_errors['err_antibot'] = 'y';
					}
				}

				if( count($field_errors['err_mandatory']) == 0  && count($field_errors['err_value']) == 0 && empty($field_errors['err_antibot']) && !isset($_REQUEST['tr_preview'])) {
					/* ------------------------------------- save the item ---------------------------------- */
					if (!isset($itemId)) {
						$itemId = $trklib->get_user_item($trackerId, $tracker);
					}
					if (isset($_REQUEST['status'])) {
						$status = $_REQUEST['status'];
					} elseif (isset($newstatus) && ($newstatus == 'o' || $newstatus == 'c'|| $newstatus == 'p')) {
						$status = $newstatus;
					} elseif (empty($itemId) && isset($tracker['newItemStatus'])) {
						$status = $tracker['newItemStatus'];
					} else {
						$status = '';
					}
					$rid = $trklib->replace_item($trackerId,$itemId,$ins_fields, $status, $ins_categs);
					$trklib->categorized_item($trackerId, $rid, $mainfield, $ins_categs);
					if (!empty($email)) {
						$emailOptions = split("\|", $email);
						if (is_numeric($emailOptions[0])) {
							$emailOptions[0] = $trklib->get_item_value($trackerId, $rid, $emailOptions[0]);
						}
						if (empty($emailOptions[0])) { // from
							$emailOptions[0] = $prefs['sender_email'];
						}
						if (empty($emailOptions[1])) { // to
							$emailOptions[1][0] = $prefs['sender_email'];
						} else {
							$emailOptions[1] = split(',', $emailOptions[1]);
							foreach ($emailOptions[1] as $key=>$email) {
								if (is_numeric($email))
									$emailOptions[1][$key] = $trklib->get_item_value($trackerId, $rid, $email);
							}
						}
						if (!empty($emailOptions[2])) { //tpl
							if (!preg_match('/\.tpl$/', $emailOptions[2]))
								$emailOptions[2] .= '.tpl';
							$tplSubject = str_replace('.tpl', '_subject.tpl', $emailOptions[2]);
						} else {
							$emailOptions[2] = 'tracker_changed_notification.tpl';
						}
						if (empty($tplSubject)) {
							$tplSubject = 'tracker_changed_notification_subject.tpl';
						}							
						include_once('lib/webmail/tikimaillib.php');
						$mail = new TikiMail();
						@$mail_data = $smarty->fetch('mail/'.$tplSubject);
						if (empty($mail_data))
							$mail_data = tra('Tracker was modified at '). $_SERVER["SERVER_NAME"];
						$mail->setSubject($mail_data);
						$mail_data = $smarty->fetch('mail/'.$emailOptions[2]);
						$mail->setText($mail_data);
						$mail->setHeader('From', $emailOptions[0]);
						$mail->send($emailOptions[1]);
					}
					if (empty($url)) {
						if (!empty($page)) {
							$url = "tiki-index.php?page=".urlencode($page)."&ok=y&trackit=$trackerId";
							if (!empty($params['fields']))
								$url .= "&fields=".urlencode($params['fields']);
							$url .= "#wikiplugin_tracker$trackerId";
							header("Location: $url");
							die;
						} else {
							return '';
						}
					} else {
						header("Location: $url");
						die;
					}
					/* ------------------------------------- end save the item ---------------------------------- */
				} elseif (isset($_REQUEST['trackit']) and $_REQUEST['trackit'] == $trackerId) {
					$smarty->assign('wikiplugin_tracker', $trackerId);//used in vote plugin
				}

			} else if (!empty($values) || (!empty($_REQUEST['values']) and empty($_REQUEST['prefills']))) { // assign default values for each filedId specify
				if (empty($values)) { // url with values[]=x&values[] witouth the list of fields
					$values = $_REQUEST['values'];
				}
				if (!is_array($values)) {
					$values = array($values);
				}
				if (isset($fields)) {
					$fl = split(':', $fields);
					for ($j = 0; $j < count($fl); $j++) {
						for ($i = 0; $i < count($flds['data']); $i++) {
							if ($flds['data'][$i]['fieldId'] == $fl[$j]) { 
								$flds['data'][$i]['value'] = $values[$j];
							}	
						}
					}
				} else { // values contains all the fields value in the default order
					$i = 0;
					foreach ($values as $value) {
						$flds['data'][$i++]['value'] = $value;
					}
				}
			
			} elseif (!empty($itemId)) {
				if (isset($fields)) {
					$fl = split(':', $fields);
					foreach ($flds['data'] as $f) {
						if (in_array($f['fieldId'], $fl))
							$filter[] = $f;
					}
				} else {
					$filter = &$flds['data'];
				}
				$flds['data'] = $trklib->get_item_fields($trackerId, $itemId, $filter, $itemUser);

			} else {
				if (isset($_REQUEST['values']) && isset($_REQUEST['prefills'])) { //url:prefields=1:2&values[]=x&values[]=y
					if (!is_array($_REQUEST['values']))
						$_REQUEST['values'] = array($_REQUEST['values']);
					$fl = split(':', $_REQUEST['prefills']);
				} else {
					unset($fl);
				}
				for($i = 0; $i < count($flds['data']); $i++) {
					if (isset($fl) && ($j = array_search($flds['data'][$i]['fieldId'], $fl)) !== false) {
						$flds['data'][$i]['value'] = $_REQUEST['values'][$j];
					} else {
						$flds['data'][$i]['value'] = ''; // initialize fields with blank values
					}
				}
			}
			
			$optional = array();
			$outf = array();
			if (isset($fields) && !empty($fields)) {
				$fl = split(":",$fields);
				if ($sort == 'y')
					$flds = $trklib->sort_fields($flds, $fl);		
				foreach ($fl as $l) {
					if (substr($l,0,1) == '-') {
						$l = substr($l,1);
						$optional[] = $l;
					}
					$ok = false;
					foreach ($flds['data'] as $f) {
						if ($f['fieldId'] == $l) {
							$ok = true;
							break;
						}
					}
					if (!$ok) {
						$back .= tra('Incorrect fieldId:').' '.$l;
					}
					$outf[] = $l;
				}
			} elseif (!isset($fields)) {
				foreach ($flds['data'] as $f) {
					if ($f['isMandatory'] == 'y')
						$optional[] = $f['fieldId'];
					$outf[] = $f['fieldId'];
				}
			}

			// Display warnings when needed
			if(count($field_errors['err_mandatory']) > 0) {
				$back.= '<div class="simplebox highlight"><img src="pics/icons/exclamation.png" alt=" '.tra('Error').'" style="vertical-align:middle" /> ';
				$back.= tra('Following mandatory fields are missing').'&nbsp;:<br/>';
				$coma_cpt = count($field_errors['err_mandatory']);
				foreach($field_errors['err_mandatory'] as $f) {
					$back.= $f['name'];
					$back.= --$coma_cpt > 0 ? ',&nbsp;' : '';
				}
				$back.= '</div><br />';
				$_REQUEST['error'] = 'y';
			}

			if(count($field_errors['err_value']) > 0) {
				$back.= '<div class="simplebox highlight">';
				$back.= tra('Following fields are incorrect').'&nbsp;:<br/>';
				$coma_cpt = count($field_errors['err_value']);
				foreach($field_errors['err_value'] as $f) {
					$back.= $f['name'];
					$back.= --$coma_cpt > 0 ? ',&nbsp;' : '';
				}
				$back.= '</div><br />';
				$_REQUEST['error'] = 'y';
			}
			if (isset($field_errors['err_antibot'])) {
				$back.= '<div class="simplebox highlight"><img src="pics/icons/exclamation.png" alt=" '.tra('Error').'" style="vertical-align:middle" /> ';
				$back .= tra('You have mistyped the anti-bot verification code; please try again.');
				$back.= '</div><br />';
				$_REQUEST['error'] = 'y';
			}
			if (count($field_errors['err_mandatory']) > 0 || count($field_errors['err_value']) > 0 || isset($field_errors['err_antibot'])) {
				$smarty->assign('input_err', 'y');
			}
			if (!empty($page))
				$back .= '~np~';
			$back.= '<form enctype="multipart/form-data" method="post"><input type="hidden" name="trackit" value="'.$trackerId.'" />';
			if (isset($fields))
				$back .= '<input type="hidden" name="fields" value="'.$params['fields'].'" />';//if plugin inserted twice with the same trackerId
			if (!empty($_REQUEST['page']))
				$back.= '<input type="hidden" name="page" value="'.$_REQUEST["page"].'" />';
			$back.= '<input type="hidden" name="refresh" value="1" />';
			if (isset($_REQUEST['page']))
				$back.= '<input type="hidden" name="page" value="'.$_REQUEST["page"].'" />';
			 // for registration
			if (isset($_REQUEST['name']))
				$back.= '<input type="hidden" name="name" value="'.$_REQUEST["name"].'" />';
			if (isset($_REQUEST['pass'])) {
				$back.= '<input type="hidden" name="pass" value="'.$_REQUEST["pass"].'" />';
				$back.= '<input type="hidden" name="passAgain" value="'.$_REQUEST["pass"].'" />';
			}
			if (isset($_REQUEST['email']))
				$back.= '<input type="hidden" name="email" value="'.$_REQUEST["email"].'" />';
			if (isset($_REQUEST['regcode']))
				$back.= '<input type="hidden" name="regcode" value="'.$_REQUEST["regcode"].'" />';
			if (isset($_REQUEST['chosenGroup'])) // for registration
				$back.= '<input type="hidden" name="chosenGroup" value="'.$_REQUEST["chosenGroup"].'" />';
			if (isset($_REQUEST['register']))
				$back.= '<input type="hidden" name="register" value="'.$_REQUEST["register"].'" />';
			if ($showtitle == 'y') {
				$back.= '<div class="titlebar">'.$tracker["name"].'</div>';
			}
			if ($showdesc == 'y' && $tracker['description']) {
				$back.= '<div class="wikitext">'.$tracker["description"].'</div><br />';
			}
			if (isset($_REQUEST['tr_preview'])) { // use for the computed and join fields
				$assocValues = array();
				$assocNumerics = array();
				foreach ($flds['data'] as $f) {
					if (empty($f['value']) && ($f['type'] == 'u' || $f['type'] == 'g' || $f['type'] == 'I') && ($f['options_array'][0] == '1' || $f['options_array'][0] == '2')) { //need to fill the selector fields for the join
						$f['value'] = ($f['type'] == 'I')? $_SERVER['REMOTE_ADDR']: (($f['type'] == 'g')? $group: $user);
					}
					$assocValues[$f['fieldId']] = $f['value'];
					$assocNumerics[$f['fieldId']] = preg_replace('/[^0-9\.\+]/', '', $f['value']); // get rid off the $ and such unit
				}
			}

			if (!empty($itemId)) {
				$item = array('itemId'=>$itemId, 'trackerId'=>$trackerId);
			}
			foreach ($flds['data'] as $i=>$f) { // collect additional infos
				if (in_array($f['fieldId'],$outf)) {
					$flds['data'][$i]['ins_id'] = ($f['type'] == 'e')?'ins_cat_'.$f['fieldId']: (($f['type'] == 'f')?'track_'.$f['fieldId']: 'track['.$f['fieldId'].']');
					if ($f['isHidden'] == 'c' && !empty($itemId) && !isset($item['creator'])) {
						$item['creator'] = $trklib->get_item_creator($trackerId, $itemId);
					}
					if ($f['type'] == 's' && ($f['name'] == 'Rating' || $f['name'] == tra('Rating')) && $tiki_p_tracker_vote_ratings == 'y' && isset($item)) {
						$item['my_rate'] = $tikilib->get_user_vote("tracker$trackerId.$itemId", $user);
					}
					if ($f['type'] == 'r') {
						$flds['data'][$i]['list'] = array_unique($trklib->get_all_items($f['options_array'][0],$f['options_array'][1],'o'));
						if (isset($f['options_array'][3])) {
							$flds['data'][$i]['displayedList'] = array_unique($trklib->concat_all_items_from_fieldslist($f['options_array'][0],$f['options_array'][3]));
						}
					} elseif ($f['type'] == 'y') {
						$flds['data'][$i]['flags'] = $tikilib->get_flags();
						if ($prefs['language'] != 'en') {
							foreach ($flags as $flag) {
								$flagsTranslated[] = $tikilib->take_away_accent(tra($flag));
							}
							array_multisort($flagsTranslated, $flds['data'][$i]['flags']);
						}
					} elseif ($f['type'] == 'u') {
						if ($tiki_p_admin == 'y' || ($f['options_array'][0] != 1 && $f['options_array'][0] != 2))
							$flds['data'][$i]['list'] = $userlib->list_all_users();
					} elseif ($f['type'] == 'g') {
						if ($tiki_p_admin == 'y' || $f['options_array'][0] != 1 && $f['options_array'][0] != 2)
							$flds['data'][$i]['list'] = $userlib->list_all_groups();
					} elseif ($f['type'] == 'e') {
						global $categlib; include_once('lib/categories/categlib.php');
						$flds['data'][$i]['list'] = $categlib->get_child_categories($f["options_array"][0]);
					} elseif ($f['type'] == 'A') {
						if (!empty($f['value'])) {
							$flds['data'][$i]['info'] = $trklib->get_item_attachment($f['value']);
						}
					} elseif ($f['type'] == 'a') {
						if ($f['options_array'][0] == 1 && empty($quicktags)) {
							global $quicktagslib; include_once ('lib/quicktags/quicktagslib.php');
							$quicktags = $quicktagslib->list_quicktags(0, -1, 'taglabel_desc', '', 'trackers');
							$smarty->assign_by_ref('quicktags', $quicktags['data']);
						}
					}
				}
			}

			// Loop on tracker fields and display form
			if (empty($tpl) && empty($wiki)) {
				$back.= '<table class="wikiplugin_tracker">';
			}
			$backLength0 = strlen($back);
			foreach ($flds['data'] as $f) {
				if ($f['type'] == 'u' and $f['options_array'][0] == '1') {
					$back.= '<input type="hidden" name="authorfieldid" value="'.$f['fieldId'].'" />';
				}
				if ($f['type'] == 'I' and $f['options_array'][0] == '1') {
					$back.= '<input type="hidden" name="authoripid" value="'.$f['fieldId'].'" />';
				}
				if ($f['type'] == 'g' and $f['options_array'][0] == '1') {
					$back.= '<input type="hidden" name="authorgroupfieldid" value="'.$f['fieldId'].'" />';
				}
				if ($f['type'] == 'q') {
					$back .= '<input type="hidden" name="track['.$f['fieldId'].']" />';
				}
				if (in_array($f['fieldId'],$outf)) {

					if (!empty($tpl) || !empty($wiki)) {
						$smarty->assign_by_ref('field_value', $f);
						$smarty->assign('f_'.$f['fieldId'], $smarty->fetch('tracker_item_field_input.tpl'));
					} elseif (true) { // comment this block in problem
						if (in_array($f['fieldId'], $optional)) {
							$f['name'] = "<i>".$f['name']."</i>";
						}
						$back.= "<tr><td>".wikiplugin_tracker_name($f['fieldId'], $f['name'], $field_errors);
						if ($showmandatory == 'y' and $f['isMandatory'] == 'y') {
							$back.= "&nbsp;<b>*</b>&nbsp;";
							$onemandatory = true;
						}
						$back.= "</td><td>";
						$smarty->assign_by_ref('field_value', $f);
						if (isset($item)) {
							$smarty->assign_by_ref('item', $item);
						}
						$back .= $smarty->fetch('tracker_item_field_input.tpl');
					} else {//old
					// numeric or text field
					if ($f['type'] == 't' or $f['type'] == 'n' and $f["fieldId"] != $embeddedId or $f['type'] == 'm') {
						$back.= "<tr><td>".wikiplugin_tracker_name($f['fieldId'], $f['name'], $field_errors);
						if ($showmandatory == 'y' and $f['isMandatory'] == 'y') {
							$back.= "&nbsp;<b>*</b>&nbsp;";
							$onemandatory = true;
						}
						$back.= "</td><td>";
						$back.= '<input type="text" name="track['.$f["fieldId"].']" value="'.$f['value'].'"';
						if (isset($f['options_array'][1])) {
							$back.= 'size="'.$f['options_array'][1].'" maxlength="'.$f['options_array'][1].'"';
						} else {
							$back.= 'size="30"';
						}
						$back.= '/>';
					// item link
					} elseif ($f['type'] == 'r') {
						$back.= "<tr><td>".wikiplugin_tracker_name($f['fieldId'], $f['name'], $field_errors);
						if ($showmandatory == 'y' and $f['isMandatory'] == 'y') {
							$back.= "&nbsp;<b>*</b>&nbsp;";
							$onemandatory = true;
						}
						$back.= "</td><td>";
						$back.= '<select name="track['.$f["fieldId"].']">';
						$back.= '<option value=""></option>';
						foreach ($f['list'] as $key=>$item) {
							$selected = $f['value'] == $item ? 'selected="selected"' : '';
							$back .= '<option value="'.$item.'" '.$selected.'>';
							$back .= isset($f['displayedList'][$key])? $f['displayedList'][$key]: $item;
							$back .= '</option>';
						}
						$back.= "</select>";
					// country
					} elseif ($f['type'] == 'y') {
						$back.= "<tr><td>".wikiplugin_tracker_name($f['fieldId'], $f['name'], $field_errors);
						if ($showmandatory == 'y' and $f['isMandatory'] == 'y') {
							$back.= "&nbsp;<b>*</b>&nbsp;";
							$onemandatory = true;
						}
						$back.= "</td><td>";
						$back.= '<select name="track['.$f["fieldId"].']">';
						$back .= '<option value=""></option>';
						foreach ($f['flags'] as $flag) {
							$selected = $f['value'] == $flag ? 'selected="selected"' : '';
							if (!isset($f['options_array'][0]) ||  $f['options_array'][0] != '1')
								$selected .= ' style="background-image:url(\'img/flags/'.$flag.'.gif\');background-repeat:no-repeat;padding-left:25px;padding-bottom:3px;"';
							$back.= '<option value="'.$flag.'" '.$selected.'>'.tra($flag).'</option>';
						}
						$back.= "</select>";
					// textarea
					} elseif ($f['type'] == 'a') {
						$back.= "<tr><td>".wikiplugin_tracker_name($f['fieldId'], $f['name'], $field_errors);
						if ($showmandatory == 'y' and $f['isMandatory'] == 'y') {
							$back.= "&nbsp;<b>*</b>&nbsp;";
							$onemandatory = true;
						}
						$back.= "</td><td>";
						if( isset($f['options_array'][1]) ) {
							$back.= '<textarea cols="'.$f['options_array'][1].'" rows="'.$f['options_array'][2].'" name="track['.$f["fieldId"].']" wrap="soft">'.$f['value'].'</textarea>';
						} else {
							$back.= '<textarea cols="29" rows="7" name="track['.$f["fieldId"].']" wrap="soft">'.$f['value'].'</textarea>';
						}
					// user selector
					} elseif (($f['type'] == 'u' or $f['type'] == 'g' or $f['type'] == 'I') and ($f['options_array'][0] == '1' or $f['options_array'][0] == '2')) {
						$back.= '<tr><td>'.wikiplugin_tracker_name($f['fieldId'], $f['name'], $field_errors).'</td><td>';
						$back .= ($f['type'] == 'I')? $_SERVER['REMOTE_ADDR']: (($f['type'] == 'g')? $group: $user);
					// drop down, user selector or group selector
					} elseif ($f['type'] == 'd' or $f['type'] == 'D' or $f['type'] == 'u' or $f['type'] == 'g' or $f['type'] == 'r' or $f['type'] == 'R') {
						if ($f['type'] == 'd'  or $f['type'] == 'D' or $f['type'] == 'R') {
							$list = $f['options_array'];
						} elseif ($f['type'] == 'u') {
							$list = $f['list'];
						} elseif ($f['type'] == 'g') {
							$list = $f['list'];
						}
						if ($list) {
							$back.= "<tr><td>".wikiplugin_tracker_name($f['fieldId'], $f['name'], $field_errors);
							if ($showmandatory == 'y' and $f['isMandatory'] == 'y') {
								$back.= "&nbsp;<b>*</b>&nbsp;";
								$onemandatory = true;
							}
							$back.= "</td><td>";
							if ($f['type'] == 'R') {
								foreach ($list as $item) {
									$selected = ($f['value'] == $item || (empty($f['value']) && !empty($f['defaultvalue']) && $item == $f['defaultvalue'])) ? 'checked="checked"' : '';
									$back .= '<div class="radio"><input type="radio" name="track['.$f["fieldId"].']" value="'.$item.'" '.$selected.' />'.$item.'</div>';
								}
							} else {
								$back.= '<select name="track['.$f["fieldId"].']">';
								$back .= '<option value=""></option>';
								$otherValue = $f['value'];
								foreach ($list as $item) {
									if ($f['value'] == $item || (empty($f['value']) && !empty($f['defaultvalue']) && $item == $f['defaultvalue'])) {
										$selected = 'selected="selected"';
										$otherValue = '';
									} else {
										$selected = '';
									}
									$back.= '<option value="'.$item.'" '.$selected.'>'.tra($item).'</option>';
								}
							$back.= "</select>";
							}

							if ($f['type'] == 'D') {
								$back .= '<br />'.tra('Other:').' <input type="text" name="track_other['.$f["fieldId"].']" value="'.$otherValue.'" />';
							}
						} else {
							$back.= '<input type="hidden" name="track['.$f["fieldId"].']" value="'.$user.'" />';
						}
					} elseif ($f['type'] == 'h') {
						if (strlen($back) != $backLength0) {
							$back .= '</td></tr>';
						}
						$back .= "<tr><td colspan=\"2\" class=\"trackerheader\"><h2>";
						$n = wikiplugin_tracker_name($f['fieldId'], $f['name'], $field_errors);
						$back .= $n? $n : ' '.'</h2>';
						if (!empty($f['description']))
							$back .= '<i>'.$f['description'].'</i>';
					} elseif ($f['type'] == 'e') {
						$back .="<tr><td>".wikiplugin_tracker_name($f['fieldId'], $f['name'], $field_errors);
						if ($showmandatory == 'y' and $f['isMandatory'] == 'y') {
							$back.= "&nbsp;<b>*</b>&nbsp;";
							$onemandatory = true;
						}
						$back .= "</td><td>";
						$i = 0;
						if (!empty($f['options_array'][2]) && ($f['options_array'][2] == '1' || $f['options_array'][2] == 'y')) { 
							$back .= '<script type="text/javascript"> /* <![CDATA[ */';
							$back .= "document.write('<div class=\"categSelectAll\"><input type=\"checkbox\" onclick=\"switchCheckboxes(this.form,\'ins_cat_{$f['fieldId']}[]\',this.checked)\"/>";
							$back .= tra('Select All');
							$back .= "</div>')/* ]]> */</script>";
						}
						if (isset($f['options_array'][1]) && ($f['options_array'][1] == 'd' || $f['options_array'][1] == 'm')) {
							$back .= '<select name="ins_cat_'.$f['fieldId'].'[]"';
							if ($f['options_array'][1] == 'm') {
								$back .= ' multiple="multiple"';
							}
							$back .= '>';
							if ($f['options_array'][1] == 'd') {
								$back .= '<option value=""></option>"';
							}
							foreach ($f['list'] as $cat) {
								$checked = ($f['value'] == $cat['categId']) ? 'selected="selected"' : '';
								$back .= '<option value="'.$cat['categId'].'" '.$checked.'>'.$cat['name'].'</option>';
							}
							$back .= '</select>';
						} else {
							$t = (isset($f["options_array"][1]) && $f["options_array"][1] == 'radio')? 'radio': 'checkbox';
							foreach ($f['list'] as $cat) {
								$checked = ($f['value'] == $cat['categId']) ? 'checked="checked"' : '';
								$back .= '<input type="'.$t.'" name="ins_cat_'.$f['fieldId'].'[]" value="'.$cat["categId"].'" '.$checked.'>'.$cat['name'].'</input><br />';
							}
						}
					} elseif ($f['type'] == 'c') {
						$back .="<tr><td>".wikiplugin_tracker_name($f['fieldId'], $f['name'], $field_errors);
						if ($showmandatory == 'y' and $f['isMandatory'] == 'y') {
							$back.= "&nbsp;<b>*</b>&nbsp;";
							$onemandatory = true;
						}
						$checked = $f['value'] == 'y' ? 'checked="checked"' : '';
						$back .= '</td><td><input type="checkbox" name="track['.$f["fieldId"].']" value="y" '.$checked.'/>';
					} elseif ($f['type'] == 'i') {
						$back.= "<tr><td>".wikiplugin_tracker_name($f['fieldId'], $f['name'], $field_errors);
						if ($showmandatory == 'y' and $f['isMandatory'] == 'y') {
							$back.= "&nbsp;<b>*</b>&nbsp;";
							$onemandatory = true;
						}
						$back .= "</td><td>";
						$back .= '<input type="file" name="track['.$f["fieldId"].']" />';
					} elseif ($f['type'] == 'f') {
						$back.= "<tr><td>".wikiplugin_tracker_name($f['fieldId'], $f['name'], $field_errors);
						if ($showmandatory == 'y' and $f['isMandatory'] == 'y') {
							$back.= "&nbsp;<b>*</b>&nbsp;";
							$onemandatory = true;
						}
						$back .= "</td><td>";
						include_once('lib/smarty_tiki/function.html_select_date.php');
						include_once('lib/smarty_tiki/function.html_select_time.php');
						$params['prefix'] = 'track_'.$f['fieldId'];
						if (isset($f['options_array'][1])) {
							$params['start_year'] = $f['options_array'][1];
						}
						if (isset($f['options_array'][2])) {
							$params['end_year'] = $f['options_array'][2];
						}
						if (isset($f['value'])) {
							$params['time'] = $f['value'];
						}
						$back .= smarty_function_html_select_date($params, $smarty);
						if (empty($f['options_array'][0]) || $f['options_array'][0] != 'd') {
							$params['display_seconds'] = false;
							$back .= smarty_function_html_select_time($params, $smarty);
						}
					} elseif ($f['type'] == 'j') {
						$back.= "<tr><td>".wikiplugin_tracker_name($f['fieldId'], $f['name'], $field_errors);
						if ($showmandatory == 'y' and $f['isMandatory'] == 'y') {
							$back.= "&nbsp;<b>*</b>&nbsp;";
							$onemandatory = true;
						}
						$back .= "</td><td>";
						include_once('lib/smarty_tiki/function.jscalendar.php');
						$params['id'] = 'track['.$f['fieldId'].']';
						$params['fieldname'] = 'track['.$f['fieldId'].']';
						if (empty($f['options_array'][0]) || $f['options_array'][0] != 'd') {
							$params['showtime'] = 'y';
						} else {
							$params['showtime'] = 'n';
						}
						if (isset($f['value'])) {
							$params['date'] = $f['value'];
						}
						$back .= smarty_function_jscalendar_body($params,$smarty);
					} elseif ($f['type'] == 'C' && isset($_REQUEST['tr_preview'])) { // computed
						$back .= "<tr><td>".wikiplugin_tracker_name($f['fieldId'], $f['name'], $field_errors)."</td><td>";
						$calc = preg_replace('/#([0-9]+)/','$assocNumerics[\1]',$f['options_array'][0]);
						eval('$computed = '.$calc.';');
						$back .= $computed;
					} elseif ($f['type'] ==  'l'  && isset($_REQUEST['tr_preview'])) { // itemlist
						$back .= "<tr><td>".wikiplugin_tracker_name($f['fieldId'], $f['name'], $field_errors)."</td><td>";
						$items = $trklib->get_items_list($f['options_array'][0], $f['options_array'][1], $assocValues[$f['options_array'][2]]);
						$i = 0;
						foreach ($items as $id) {
							$value =  $trklib->get_item_value($f['options_array'][0], $id, $f['options_array'][3]);
							$assocValues[$f['fieldId']] = $value; // can be used in another computed field
							$assocNumerics[$f['fieldId']] = preg_replace('/[^0-9\.\+]/', '', $value);
							if ($i++ > 0)
								$back .= ',';
							$back .= $value;
						}
					} else {
					}
}//old

					if (!empty($f['description']) && $f['type'] != 'h')
						$back .= '<br /><i>'.$f['description'].'</i>';
					if (empty($tpl) && empty($wiki)) {
						$back.= "</td></tr>";
					}
				}
			}
			if (!empty($tpl)) {
				$back .= $smarty->fetch($tpl);
			} elseif (!empty($wiki)) {
				$back .= $smarty->fetch('wiki:'.$wiki);
			}
			if ($prefs['feature_antibot'] == 'y' && empty($user)) {
				$back .= $smarty->fetch('antibot.tpl');
			}
			if (empty($tpl) && empty($wiki)) {
				$back.= "<tr><td></td><td>";
			}
			if (!empty($preview)) {
				$back .= "<input type='submit' name='tr_preview' value='".tra($preview)."' />";
			}
			$back .= "<input type='submit' name='action' value='".tra($action)."' />";
			if ($showmandatory == 'y' and $onemandatory) {
				$back.= "<br /><i>".tra("Fields marked with a * are mandatory.")."</i>";
			}
			if (empty($tpl) && empty($wiki)) {
				$back.= "</td></tr>";
				$back.= "</table>";
			}
			$back.= '</form>';
			if (!empty($page))
				$back .= '~/np~';
		return $back;
	}
	else {
		if (isset($_REQUEST['trackit']) and $_REQUEST['trackit'] == $trackerId)
			$smarty->assign('wikiplugin_tracker', $trackerId);//used in vote plugin
		$id = ' id="wikiplugin_tracker'.$trackerId.'"';
		if ($showtitle == 'y') {
			$back.= '<div class="titlebar"'.$id.'>'.$tracker["name"].'</div>';
			$id = '';
		}
		if ($showdesc == 'y') {
			$back.= '<div class="wikitext"'.$id.'>'.$tracker["description"].'</div><br />';
			$id = '';
		}
		$back.= "<div$id>".$data.'</div>';
		return $back;
	}
}

?>
