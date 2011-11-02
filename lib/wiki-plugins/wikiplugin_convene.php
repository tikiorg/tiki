<?php                                                                                  
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project           
//                                                                                     
// All Rights Reserved. See copyright.txt for details and a complete list of authors.  
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.  
// $Id$

function wikiplugin_convene_info() {
	return array(
		'name' => tra('Convene'),
		'documentation' => 'PluginConvene',
		'description' => tra('Convene an event with schedule and members'),
		'prefs' => array('wikiplugin_convene'),
		'body' => tra('Convene Data'),
		'icon' => 'pics/icons/arrow_in.png',
		'filter' => 'rawhtml_unsafe',
		'tags' => array( 'basic' ),	
		'params' => array(
			'style' => array(
				'required' => false,
				'name' => tra('Style of content'),
				'options' => array(
					array('text' => tra('None'), 'value' => ''),
					array('text' => tra('Highlight'), 'value' => 'highlight'),
					array('text' => tra('Asterisk'), 'value' => 'asterisk'),
				),
			),
		),
	);
}

function wikiplugin_convene($data, $params) {
	global $tikilib, $headerlib, $page, $caching;
	static $htmlFeedLinkI = 0;
	++$htmlFeedLinkI;
	$i = $htmlFeedLinkI;
	
	$params = array_merge(array(
		"type" => "replace",
	), $params);
	
	extract ($params,EXTR_SKIP);
	$dates = array();
	
	foreach(explode("\n", trim($data)) as $line) {
		$line = trim($line);
		
		$parts = explode(':', $line);
		$dates[trim($parts[0])] = trim($parts[1]);
	}
	
	$dates = TikiFilter_PrepareInput::delimiter('_')->prepare($dates);
	
	$users = array();
	foreach(end($dates['dates']) as $user => $vote) {
		$users[] = $user;
	}
	
	$votes = array();
	foreach($dates['dates'] as $stamp => $date) {
		foreach($date as $vote) {
			$votes[$stamp] += $vote;
		}
	}
	
	$topVoteStamp = 0;
	foreach($votes as $stamp => $vote) {
		$topVoteStamp = ($vote > $votes[$topVoteStamp] ? $stamp : $topVoteStamp);
	}

	$rows = array();
	foreach($dates['dates'] as $stamp => $date) {
		foreach($date as $user => $vote) {
			if (isset($rows[$user][$stamp])) $rows[$user][$stamp] = array();
			 
			$rows[$user][$stamp] = $vote;
		}
	}
	
	$result = "~np~<table>";
	
	$result .= "<tr><td />";
	
	foreach($votes as $stamp => $totals) {
		$result .= "<td>". $tikilib->get_short_datetime($stamp) ."</td>";
	}
	
	$result .= "</tr>";
	
	foreach($rows as $user => $row) {
		$result .= "<tr>";
		$result .= "<td>" . $user . "</td>";
		foreach($row as $stamp => $vote) {
			$style = 	($vote == 1 ? 'color: green; background-color: #99FF66;' : 'color: red; background-color: #FFCCCC;');
			$text = 	($vote  == 1 ? tr("OK") : "" );
			
			$result .= "<td style='$style'>". ($vote  == 1 ? tr("OK") : "" ) ."</td>";
		}
		$result .= "</tr>";
	}
	
	$result .= "<tr><td />";

	foreach($votes as $total) {
		$pic = ($total == $votes[$topVoteStamp] ? '<img src="pics/icons/tick.png" /><img src="pics/icons/calendar_add.png" />' : '');
		$result .= "<td>". $total ."&nbsp;$pic</td>";
	}
	$result .= "</tr>";
	
	$result .= "</table>~/np~";
	
	return $result;
}