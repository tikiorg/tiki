<?php

// Includes an article field
// Usage:
// {ARTICLE(Id=>articleId, Field=>FieldName)}{ARTICLE}
// FieldName can be any field in the tiki_articles table, but title,heading, or body are probably the most useful.
function wikiplugin_tracker_help() {
	return tra("Displays an input form for tracker submit").":<br />~np~{TRACKER(trackerId=>1,fields=>login:email:-optionalfield)}Notice{TRACKER}~/np~";
}
function wikiplugin_tracker($data, $params) {
	global $tikilib, $dbTiki;
	
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

		if (isset($_REQUEST['trackit'])) {
			foreach ($_REQUEST['track'] as $fld=>$val) {
				$ins_fields["data"][] = array('fieldId' => $fld, 'value' => $val);
			}
			//$trklib->replace_item($trackerId,0,$ins_fields);
			return "<div>Done!</div>";
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
