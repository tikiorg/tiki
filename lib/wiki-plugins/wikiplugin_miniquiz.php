<?php
// $Id: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_miniquiz.php,v 1.10 2007-10-12 07:55:48 nyloth Exp $
/*
DEV NOTE
that plugin is not finished !! -- mose
\todo put message in an external file or source
\todo use smarty templates rather than hardcode html
*/

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
  global $tikilib, $user, $group, $prefs;
	global $trklib; include_once('lib/trackers/trackerlib.php');
	extract ($params,EXTR_SKIP);

	if ($prefs['feature_trackers'] != 'y' || !isset($trackerId) || !($tracker = $trklib->get_tracker($trackerId))) {
		return $smarty->fetch("wiki-plugins/error_tracker.tpl");
	}

	$items = $tikilib->list_tracker_items($trackerId,0,-1,'lastModif_desc','','o');
	foreach ($items['data'] as $it) {
		$id = $it['itemId'];
		foreach ($it['field_values'] as $val) {
			$field = $val['name'];
			$info["$id"]["$field"] = $val['value'];
		}
		$info["$id"]['qresult'] = 'n';
	}
	$back = '';
	$out = '';
	
	if ($tracker) {
		$success_mess[] = "Wow !";
		$success_mess[] = "Congratulation !";
		$success_mess[] = "Success !";
		$success_mess[] = "Excellent !";
		$success_mess[] = "Superb !";
		$success_mess[] = "Bravo !";
		$success_mess[] = "Well done !";
		$success_comment[] = "You found it right !";
		$success_comment[] = "This is correct.";
		$success_comment[] = "You are the best !";
		$success_comment[] = "The answer is correct.";
		$success_comment[] = "Your cleverness is amazing.";
		$success_comment[] = "Go on that way !";
		$failed_mess[] = "Wrong !";
		$failed_mess[] = "Too bad !";
		$failed_mess[] = "No luck !";
		$failed_mess[] = "Failed !";
		$failed_mess[] = "Argh !";
		$failed_mess[] = "Missed !";
		$failed_comment[] = "Please think before clicking.";
		$failed_comment[] = "Did you read the question before reading the answer ?";
		$failed_comment[] = "Try again.";
		$failed_comment[] = "Get another chance.";
		$failed_comment[] = "Think carefully that time.";
		$failed_comment[] = "Use the force !";
		$failed_comment[] = "You should concentrate a little more.";

		if (isset($_REQUEST['quizit']) and $_REQUEST['quizit']) {
			if (isset($_REQUEST['answer']) and is_array($_REQUEST['answer'])) {
				$out.= "[MiniQuiz]\n";
				$out.= "trackerId : $trackerId\n";
				$out.= "user : $user\n";
				$out.= "group : $group\n";
				foreach ($_REQUEST['answer'] as $q=>$a) {
					if ($info["$q"]['Answer'] == $a) {
						$out.= "$q : $a --> yeah !\n";
						$info["$q"]['qresult'] = 'y';
					} else {
						$out.= "$q : $a\n";
						$info["$q"]['qresult'] = 'b';
					}
				}
				$bout = "^$data^";
				$bout.= "~pp~$out~/pp~";
				//return $bout;
			} else{
				$back.= "!Please fill the quiz!\n";
			}
		}

		$back.= '~np~<form method="post"><input type="hidden" name="quizit" value="1" />';
		$back.= '<input type="hidden" name="page" value="'.$_REQUEST["page"].'" />';
		$back.= '<div class="titlebar"><a href="tiki-view_tracker.php?trackerId='.$trackerId.'">'.$tracker["name"].'</a></div>';
		$back.= '<div class="wikitext">'.$tracker["description"].'</div><br />';
		$back.= '<style>.q label { background-color: none; cursor: normal; border: 1px solid white; padding: 0 5px 0 5px; }';
		$back.= '.q label:hover { background-color: #efe0d0; cursor: pointer; border: 1px solid black; }</style>';
	
		foreach ($info as $id=>$item) {
			if (isset($item['valid']) and $item['valid'] == 'y') {
				$back.= '<div class="titlebar">'.$item['question'].'</div>';
				if ($item['qresult'] == 'y') {
					$back.= '<div class="wikitext" style="background-color:#ccffcc;">';
					if (!isset($_POST["$id"])) {
						$back.= '<b>'.$success_mess[array_rand($success_mess)].'</b> '. $success_comment[array_rand($success_comment)].'<br />';
					}
					$back.= 'The answer was: <b>'.$item['Answer'].'</b></div><br />';
					$back.= '<input type="hidden" name="answer['.$id.']" value="'. htmlspecialchars($item['Answer']).'" />';
					$back.= '<input type="hidden" name="'.$id.'" value="1" />';
				} else {
					if ($item['qresult'] == 'b') {
						$back.= '<div class="wikitext" style="background-color:#ffcccc;">';
						$back.= '<b>'.$failed_mess[array_rand($failed_mess)].'</b> '. $failed_comment[array_rand($failed_comment)].'</div>';
					}
					$answers = array($item['Answer'],$item['option a'],$item['option b'],$item['option c']);
					shuf($answers);
					$back.= '<div class="wikitext">';
					$i = 1;
					foreach ($answers as $aid=>$answer) {
						$back.= '<div class="q"><input type="radio" id="answer'.$id.'_'.++$i.'" name="answer['.$id.']" value="'. htmlspecialchars($answer).'" /> ';
						$back.= '<label for="answer'.$id.'_'.$i.'">'.$answer.'</label>';
						$back.= '</div>';
					}
					$back.= '</div><br />';
					$failed = true;
				}
			}
		}

		$back.= "<br /><div><input type='reset' name='reset' value='Start Over' /><input type='submit' name='action' value='Finish' />";
		$back.= '</div>';
		$back.= '<br /><div><b>Students</b>: <a href="tiki-view_tracker.php?trackerId='.$trackerId.'&amp;new">Suggest a new question</a></div>';
		
		$back.= "</form>~/np~";
	} else {
		$back = "No such id in trackers.";
	}
	return $back;
}

?>
