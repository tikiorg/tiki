<?php
// Includes a tracker field
// Usage:
// {TRACKER()}{TRACKER}

function wikiplugin_tracker_help() {
	$help = tra("Displays an input form for tracker submit").":\n";
	$help.= "~np~{TRACKER(trackerId=>1,fields=>login:email:-optionalfield,action=>Name of submit button)}Notice{TRACKER}~/np~";
	return $help;
}
function wikiplugin_tracker($data, $params) {
	global $tikilib, $dbTiki;
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
		include "lib/trackers/trackerlib.php";

		if (isset($_REQUEST['trackit']) and $_REQUEST['trackit']) {
			foreach ($_REQUEST['track'] as $fld=>$val) {
				$ins_fields["data"][] = array('fieldId' => $fld, 'value' => $val);
			}
			$trklib->replace_item($trackerId,0,$ins_fields);
			return "<div>Done!</div><pre>".print_r($ins_fields)."</pre>";
		}
		$flds = $trklib->list_tracker_fields($trackerId,0,-1,"position_asc","");
		$optionnal = array();
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
		$back = '~np~<form><input type="hidden" name="trackit" value="1" />';
		$back.= '<input type="hidden" name="page" value="'.$_REQUEST["page"].'" />';
		$back.= '<div class="titlebar">'.$tracker["name"].'</div>';
		$back.= '<div class="wikitext">'.$tracker["description"].'</div>';
		$back.= '<table>';
		foreach ($flds['data'] as $f) {
			if (in_array($f['name'],$outf)) {
				if (in_array($f['name'],$optional)) {
					$f['name'] = "<i>".$f['name']."</i>";
				}
				if ($f['type'] == 't') {
					$back.= "<tr><td>".$f['name']."</td><td>";
					$back.= '<input type="text" size="30" name="track['.$f["fieldId"].']" value=""/>';
				} elseif ($f['type'] == 'a') {
					$back.= "<tr><td>".$f['name']."</td><td>";
					$back.= '<textarea cols="29" rows="7" name="track['.$f["fieldId"].']"></textarea>';
				} elseif ($f['type'] == 'd') {
					$list = split(',',$f['options']);
					$back.= "<tr><td>".$f['name']."</td><td>";
					$back.= '<select name="track['.$f["name"].']">';
					foreach ($list as $item) {
						$back.= '<option value="'.$item.'">'.$item.'</option>';
					}
					$back.= "</select>";
				}
				$back.= "</td></tr>";
			}
		}
		$back.= "<tr><td></td><td><input type='submit' name='action' value='".$action."'></td></tr>";
		$back.= "</table>";
		$back.= "</form>~/np~";
	} else {
		$back = "No such id in trackers.";
	}
	return $back;
}

?>
