<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_miniquiz_info()
{
	return array(
		'name' => tra('Mini Quiz'),
		'documentation' => 'PluginMiniQuiz',
		'description' => tra('Create a quiz using a tracker'),
		'prefs' => array( 'feature_trackers', 'wikiplugin_miniquiz' ),
		'body' => tra('Instructions::Feedback'),
		'iconname' => 'help',
		'introduced' => 1,
		'format' => 'html',
		'params' => array(
			'trackerId' => array(
				'required' => true,
				'name' => tra('Tracker ID'),
				'description' => tra('Numeric value representing the miniquiz tracker ID'),
				'since' => '1',
				'default' => '',
				'profile_reference' => 'tracker',
			),
		),
	);
}

function rcmp($a, $b)
{
	return mt_rand(-1, 1);
}
function shuf(&$ar)
{
	srand((double) microtime() * 10000000); uksort($ar, "rcmp");
}

function wikiplugin_miniquiz($data, $params)
{
	global $prefs;
	$trklib = TikiLib::lib('trk');
	
	if ($prefs['feature_trackers'] != 'y' || !isset($params['trackerId']) || !($tracker = $trklib->get_tracker($params['trackerId']))) {
		$smarty = TikiLib::lib('smarty');
		return $smarty->fetch("wiki-plugins/error_tracker.tpl");
	}

	$items = $trklib->list_tracker_items($params['trackerId'], 0, -1, 'lastModif_desc', '', 'o');
	$info = array();

	foreach ($items['data'] as $it) {
		$id = $it['itemId'];
		foreach ($it['field_values'] as $val) {
			$field = $val['name'];
			$info["$id"]["$field"] = $val['value'];
		}
		$info["$id"]['qresult'] = 'n';
	}
	$back = '';

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
				foreach ($_REQUEST['answer'] as $q=>$a) {
					if ($info["$q"]['answer'] == $a) {
						$info["$q"]['qresult'] = 'y';
					} else {
						$info["$q"]['qresult'] = $a;
					}
				}
			} else {
				$back.= '<div class="text-warning">Please fill the quiz!<div>';
			}
		}

		$back.= '<form method="post"><input type="hidden" name="quizit" value="1" />';
		$back.= '<input type="hidden" name="page" value="'.$_REQUEST["page"].'" />';
		$back.= '<div class="titlebar"><a href="tiki-view_tracker.php?trackerId='.$params['trackerId'].'">'.$tracker["name"].'</a></div>';
		$back.= '<div class="wikitext">'.$tracker["description"].'</div><br />';
		$back.= '<style>.q label { background-color: none; cursor: normal; border: 1px solid white; padding: 0 5px 0 5px; }';
		$back.= '.q label:hover { background-color: #efe0d0; cursor: pointer; border: 1px solid black; }</style>';
	
		foreach ($info as $id=>$item) {
			if (isset($item['valid']) and $item['valid'] == 'y') {
				$back.= '<div class="titlebar">'.$item['question'].'</div>';
				if ($item['qresult'] !== 'n') {
					if ($item['qresult'] == 'y') {
						$back .= '<div class="wikitext" style="background-color:#ccffcc;">';
						if (!isset($_POST["$id"])) {
							$back .= '<b>' . $success_mess[array_rand($success_mess)] . '</b> ' . $success_comment[array_rand($success_comment)] . '<br />';
						}
						$back .= 'The answer was: <b>' . $item['answer'] . '</b></div><br />';
						$back .= '<input type="hidden" name="answer[' . $id . ']" value="' . htmlspecialchars($item['answer']) . '" />';
						$back .= '<input type="hidden" name="' . $id . '" value="1" />';
					} else {
						$back .= '<div class="wikitext" style="background-color:#ffcccc;">';
						$back .= '<b>' . $failed_mess[array_rand($failed_mess)] . '</b> ' . $failed_comment[array_rand($failed_comment)] . '</div>';
					}
				}
				$option_base = 'option ';
				$options = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j');
				$answers = array($item['answer']);
				foreach ($options as $option) {
					if (isset($item[$option_base . $option])) {
						$answers[] = $item[$option_base . $option];
					}
				}

				shuf($answers);
				$back.= '<div class="wikitext">';
				$i = 1;
				foreach ($answers as $aid=>$answer) {
					$back.= '<div class="q"><input type="radio" id="answer'.$id.'_'.++$i.'" name="answer['.$id.']" value="'. htmlspecialchars($answer) . '"';
					if (!empty($item['qresult']) && $item['qresult'] == $answer) {
						$back .= ' checked="checked"';
					}
					$back .= ' /> ';
					$back.= '<label for="answer'.$id.'_'.$i.'">'.$answer.'</label>';
					$back.= '</div>';
				}
				$back.= '</div><br />';

			}
		}

		$back.= "<br /><div><input type='reset' name='reset' value='Start Over' class='btn btn-info' /><input type='submit' name='action' value='Finish'  class='btn btn-default' />";
		$back.= '</div>';
		$back.= '<br /><div><b>Students</b>: <a href="tiki-view_tracker.php?trackerId='.$params['trackerId'].'&amp;new">Suggest a new question</a></div>';
		
		$back.= "</form>";
	} else {
		$back = "No such id in trackers.";
	}
	return $back;
}
