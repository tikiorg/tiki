<?php
// Includes a miniquiz form

// fields to use in trackers to prepare miniquiz
// Question   the question
// Answer     correct answer
// Option a   false answer 
// Option b   false answer 
// Option c   false answer 
// Option d   false answer 
// Valid      indicates that the tracker item is to be used as a quiz item

function wikiplugin_miniquiz_help() {
	$help = tra("Displays an miniquiz").":\n";
	$help.= "~np~{MINIQUIZ(trackerId=>1)}Instructions::Feedback{MINIQUIZ}~/np~";
	return $help;
}

function rcmp($a, $b) { return mt_rand(-1, 1); }
function shuf(&$ar) { srand((double) microtime() * 10000000); uksort($ar, "rcmp"); }

function wikiplugin_miniquiz($data, $params) {
	global $tikilib, $userlib, $dbTiki, $user, $group;
	//var_dump($_REQUEST);
	extract ($params);

	
	if (!isset($trackerId)) {
		return ("<b>missing tracker ID for plugin TRACKER</b><br/>");
	}
	$tracker = $tikilib->get_tracker($trackerId);
	$items = $tikilib->list_tracker_items($trackerId,0,-1,'lastModif_desc','','o');
	foreach ($items['data'] as $it) {
		$id = $it['itemId'];
		foreach ($it['field_values'] as $val) {
			$field = $val['name'];
			$info["$id"]["$field"] = $val['value'];
		}
	}
	$back = '';
	
	if ($tracker) {
	
		if (isset($_REQUEST['quizit']) and $_REQUEST['quizit']) {
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
			
		$back.= '~np~<form><input type="hidden" name="quizit" value="1" />';
		$back.= '<input type="hidden" name="page" value="'.$_REQUEST["page"].'" />';
		$back.= '<div class="titlebar"><a href="tiki-view_tracker.php?trackerId='.$trackerId.'">'.$tracker["name"].'</a></div>';
		$back.= '<div class="wikitext">'.$tracker["description"].'</div><br />';
		$back.= '<style>.q label { background-color: none; cursor: normal; border: 1px solid white; padding: 0 5px 0 5px; }';
		$back.= '.q label:hover { background-color: #efe0d0; cursor: pointer; border: 1px solid black; }</style>';
		
		$back.= '<div class="wikitext">';
		foreach ($info as $id=>$item) {
			if ($item['valid'] == 'y') {
				$back.= '<div class="titlebar">'.$item['question'].'</div>';
				$answers = array($item['Answer'],$item['option a'],$item['option b'],$item['option c']);
				shuf($answers);
				$back.= '<div class="wikitext">';
				$i = 1;
				foreach ($answers as $answer) {
					$back.= '<div class="q"><input type="radio" id="answer'.$id.'_'.++$i.'" name="answer'.$id.'" value="'. htmlspecialchars($answer).'" /> ';
					$back.= '<label for="answer'.$id.'_'.$i.'">'.$answer.'</label>';
					$back.= '</div>';
				}
				$back.= '</div><br />';
			}
		}
		$back.= '<div class="titlebar">';

		$back.= '</div>';
		$back.= "<div><input type='reset' name='reset' value='Start Over' /><input type='submit' name='action' value='Finish' />";
		$back.= '</div>';
		$back.= '<br /><div><b>Students</b>: <a href="tiki-view_tracker.php?trackerId='.$trackerId.'&amp;new">Suggest a new question</a></div>';
		
		$back.= "</form>~/np~";
	} else {
		$back = "No such id in trackers.";
	}
	return $back;
}

?>
