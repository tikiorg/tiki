<?php
// Includes a tracker field
// Usage:
// {TRACKER()}{TRACKER}

function wikiplugin_tracker_help() {
	$help = tra("Displays an input form for tracker submit").":\n";
	$help.= "~np~{TRACKER(trackerId=>1,fields=>id1:id2:id3,action=>Name of submit button)}Notice{TRACKER}~/np~";
	return $help;
}
function wikiplugin_tracker($data, $params) {
	global $tikilib, $userlib, $dbTiki, $notificationlib, $user, $group;
	//var_dump($_REQUEST);
	extract ($params);

	if (!isset($trackerId)) {
		return ("<b>missing tracker ID for plugin TRACKER</b><br/>");
	}
	if (!isset($action)) {
		$action = tra("Save");
	}
	$tracker = $tikilib->get_tracker($trackerId);
	
	if ($tracker) {
		include_once('lib/trackers/trackerlib.php');
		include_once('lib/notifications/notificationlib.php');	
		$tracker = array_merge($tracker,$trklib->get_tracker_options($trackerId));
		$flds = $trklib->list_tracker_fields($trackerId,0,-1,"position_asc","");
		$back = '';
		$bad = array();
		$onemandatory = false;
		if (isset($_REQUEST['trackit']) and $_REQUEST['trackit']) {
			foreach ($flds['data'] as $fl) {
				if ($fl['isMandatory'] == 'y') {
					if (!isset($_REQUEST['track']["{$fl['fieldId']}"]) or !$_REQUEST['track']["{$fl['fieldId']}"]) {
						$bad[] = $fl['name'];
					}
				}
			}
			if (count($bad) == 0) {
				foreach ($_REQUEST['track'] as $fld=>$val) {
					$ins_fields["data"][] = array('fieldId' => $fld, 'value' => $val, 'type' => 1);
				}
				if (isset($_REQUEST['authorfieldid']) and $_REQUEST['authorfieldid']) {
					$ins_fields["data"][] = array('fieldId' => $_REQUEST['authorfieldid'], 'value' => $user, 'type' => 'u', 'options' => 1);
				}
				if (isset($_REQUEST['authorgroupfieldid']) and $_REQUEST['authorgroupfieldid']) {
					$ins_fields["data"][] = array('fieldId' => $_REQUEST['authorgroupfieldid'], 'value' => $group, 'type' => 'g', 'options' => 1);
				}
				$rid = $trklib->replace_item($trackerId,0,$ins_fields,$tracker['newItemStatus']);
				return "<div>$data</div>";
			}
		}
		$optional = array();
		if (isset($fields)) {
			$outf = array();
			$fl = split(":",$fields);
			
			foreach ($fl as $l) {
				if (substr($l,0,1) == '-') {
					$l = substr($l,1);
					$optional[] = $l;
				}
				$outf[] = $l;
			}
		}
		if (count($bad)) {
			$back.= "<div class='simplebox'>".tra("You need to supply information for : ").implode(', ',$bad)."</div>";
		}
		$back.= '~np~<form><input type="hidden" name="trackit" value="1" />';
		$back.= '<input type="hidden" name="page" value="'.$_REQUEST["page"].'" />';
		$back.= '<div class="titlebar">'.$tracker["name"].'</div>';
		$back.= '<div class="wikitext">'.$tracker["description"].'</div><br />';
		$back.= '<table>';
		foreach ($flds['data'] as $f) {
			if ($f['type'] == 'u' and $f['options'] == '1') {
				$back.= '<input type="hidden" name="authorfieldid" value="'.$f['fieldId'].'" />';
			}
			if ($f['type'] == 'g' and $f['options'] == '1') {
				$back.= '<input type="hidden" name="authorgroupfieldid" value="'.$f['fieldId'].'" />';
			}
			if (in_array($f['fieldId'],$outf)) {
				if (in_array($f['fieldId'],$optional)) {
					$f['name'] = "<i>".$f['name']."</i>";
				}
				if ($f['type'] == 't' or $f['type'] == 'n') {
					$back.= "<tr><td>".$f['name'];
					if ($f['isMandatory'] == 'y') {
						$back.= "&nbsp;<b>*</b>&nbsp;";
						$onemandatory = true;
					}
					$back.= "</td><td>";
					$back.= '<input type="text" size="30" name="track['.$f["fieldId"].']" value=""';
					if (isset($f['options_array'][1])) {
						$back.= 'size="'.$f['options_array'][1].'" maxlength="'.$f['options_array'][1].'"';
					} else {
						$back.= 'size="30"';
					}
					$back.= '/>';
				} elseif ($f['type'] == 'r') {
					$list = $trklib->get_all_items($f['options_array'][0],$f['options_array'][1],'o');
					$back.= "<tr><td>".$f['name'];
					if ($f['isMandatory'] == 'y') {
						$back.= "&nbsp;<b>*</b>&nbsp;";
						$onemandatory = true;
					}
					$back.= "</td><td>";
					$back.= '<select name="track['.$f["fieldId"].']">';
					$back.= '<option value=""></option>';
					foreach ($list as $key=>$item) {
						$back.= '<option value="'.$item.'">'.$item.'</option>';
					}
					$back.= "</select>";
				} elseif ($f['type'] == 'a') {
					$back.= "<tr><td>".$f['name'];
					if ($f['isMandatory'] == 'y') {
						$back.= "&nbsp;<b>*</b>&nbsp;";
						$onemandatory = true;
					}
					$back.= "</td><td>";
					$back.= '<textarea cols="29" rows="7" name="track['.$f["fieldId"].']" wrap="soft"></textarea>';
				} elseif ($f['type'] == 'd' or $f['type'] == 'u' or $f['type'] == 'g' or $f['type'] == 'r') {
					if ($f['type'] == 'd') {
						$list = split(',',$f['options']);
					} elseif ($f['type'] == 'u') {
						$list = $userlib->list_all_users();
					} elseif ($f['type'] == 'g') {
						$list = $userlib->list_all_groups();
					}
					$back.= "<tr><td>".$f['name'];
					if ($f['isMandatory'] == 'y') {
						$back.= "&nbsp;<b>*</b>&nbsp;";
						$onemandatory = true;
					}
					$back.= "</td><td>";
					$back.= '<select name="track['.$f["fieldId"].']">';
					foreach ($list as $item) {
						$back.= '<option value="'.$item.'">'.$item.'</option>';
					}
					$back.= "</select>";
				}
				$back.= "</td></tr>";
			}
		}
		$back.= "<tr><td></td><td><input type='submit' name='action' value='".$action."'>";
		if ($onemandatory) {
			$back.= "<br /><i>".tra("Fields marked with a * are mandatory.")."</i>";
		}
		$back.= "</td></tr>";
		$back.= "</table>";
		$back.= "</form>~/np~";
	} else {
		$back = "No such id in trackers.";
	}
	return $back;
}

?>
