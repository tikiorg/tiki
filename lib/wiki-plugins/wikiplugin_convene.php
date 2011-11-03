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
	++$conveneI;
	$i = $conveneI;
	
	$params = array_merge(array(
		"type" => "replace",
	), $params);
	
	extract ($params,EXTR_SKIP);
	$dates = array();
	
	
	//start flat static text to prepared array
	$lines = explode("\n", trim($data));
	sort($lines);
	foreach($lines as $line) {
		$line = trim($line);
		
		$parts = explode(':', $line);
		$dates[trim($parts[0])] = trim($parts[1]);
	}
	
	$dates = TikiFilter_PrepareInput::delimiter('_')->prepare($dates);
	//end flat static text to prepared array
	
	
	//start get users from array
	$users = array();
	foreach(end($dates['dates']) as $user => $vote) {
		$users[] = $user;
	}
	//end get users from array
	
	
	//start votes summed together
	$votes = array();
	foreach($dates['dates'] as $stamp => $date) {
		foreach($date as $vote) {
			$votes[$stamp] += $vote;
		}
	}
	//end votes summed together
	
	
	//start find top vote stamp
	$topVoteStamp = 0;
	foreach($votes as $stamp => $vote) {
		$topVoteStamp = ($vote > $votes[$topVoteStamp] ? $stamp : $topVoteStamp);
	}
	//end find top vote stamp
	
	
	//start reverse array for easy listing as table
	$rows = array();
	foreach($dates['dates'] as $stamp => $date) {
		foreach($date as $user => $vote) {
			if (isset($rows[$user][$stamp])) $rows[$user][$stamp] = array();
			 
			$rows[$user][$stamp] = $vote;
		}
	}
	//end reverse array for easy listing as table
	
	$result = "";
	
	//start date header
	$dateHeader = "";
	foreach($votes as $stamp => $totals) {
		$dateHeader .= "<td>". $tikilib->get_short_datetime($stamp) ." <img src='pics/icons/delete.png' class='conveneDeleteDate$i' data-date='$stamp' /></td>";
	}
	$result .= <<<JQ
		<tr>
			<td />
			$dateHeader
			<td style='vertical-align: middle;'><img src='pics/icons/add.png' id='conveneAddDate$i' /></td>
		</tr>
JQ;
	//end date header
	
	
	//start user list and votes 
	$userList = "";
	foreach($rows as $user => $row) {
		$userList .= "<tr class='conveneUserList$i'>";
		$userList .= "<td>" . $user . "</td>";
		foreach($row as $stamp => $vote) {
			$class = 	($vote == 1 ? 'ui-state-highlight' : 'ui-state-error');
			$text = 	($vote  == 1 ? tr("OK") : "" );
			
			$userList .= "<td class='$class'>". ($vote  == 1 ? tr("OK") : "" )
				."<input type='hidden' name='dates_" . $stamp . "_" . $user . "' value='$vote' />"
				."</td>";
		}
		$userList .= "</tr>";
	}
	$result .= <<<JQ
		$userList
JQ;
	//end user list and votes
	
	
	//start add new user and votes
	$newVotes .= "";
	foreach(end($rows) as $stamp => $vote) {
		$newVotes .= "<td><input type='checkbox' name='dates_" . $stamp . "_new' /></td>";
	}
	$result .= <<<JQ
		<tr>
			<td><input name='user' type='text' style='width: 50px;' /></td>
			$newVotes
			<td><img src='pics/icons/accept.png' /></td>
		</tr>
JQ;
	//end add new user and votes
	
	
	//start last row with auto selected date(s)
	$lastRow = "";
	foreach($votes as $stamp => $total) {
		$pic = ($total == $votes[$topVoteStamp] ? "<a href='tiki-calendar_edit_item.php?todate=$stamp&calendarId=1'><img src='pics/icons/tick.png' /><img src='pics/icons/calendar_add.png' /></a>" : '');
		$lastRow .= "<td>". $total ."&nbsp;$pic</td>";
	}
	$result .= <<<JQ
		<tr id='pluginConveneLastRow$i'>
			<td />
			$lastRow
		</tr>
JQ;
	//end last row with auto selected date(s)
	
	
	$result = <<<JQ
		~np~
			<form id='pluginConvene$i'>
				<table cellpadding="0" cellspacing="0" border="0"> $result </table>
			</form>
		~/np~
JQ;
	;
	
	$conveneData = json_encode(array(
		"dates" => $dates,
		"users" => $users,
		"votes" => $votes,
		"topVote" => $votes[$topVoteStamp],
		"rows" =>	$rows,
		"data" => $data,
	));
	
	$n = '\n\r';
	
	$headerlib->add_jsfile("lib/jquery/jquery-ui-timepicker-addon.js");
	$headerlib->add_jq_onready(<<<JQ
		var convene$i = $conveneData;
		
		convene$i.addDate = function(date) {
			if (!date) return;
			date = unixDate(date);
			var addedData = '';
			
			for(user in convene$i.users) {
				addedData += 'dates_' + date + '_' + convene$i.users[user] + ' : 0$n';
			}
			
			convene$i.data = (convene$i.data + '$n' + addedData).split(/$n/).sort().join('$n');
			
			$.modal(tr("Loading..."));
			$(document)
				.unbind('plugin_convene_ready')
				.one('plugin_convene_ready', function(e) {
					var content = $(e.container).find('[name="content"]');
					content.val(convene$i.data);
					e.btns.Submit();
				});
			
			$('#plugin-edit-convene1').click();
		};
		
		convene$i.deleteDate = function(date) {
			if (!date) return;
			var addedData = '';
			
			for(user in convene$i.users) {
				addedData += 'dates_' + date + '_' + convene$i.users[user] + ' : 0$n';
			}
			
			var lines = convene$i.data.split(/$n/);
			var newData = [];
			for(line in lines) {
				if (!(lines[line] + '' ).match(date)) {
					 newData.push(lines[line]);
				}
			}
			
			convene$i.data = newData.join('$n');
			
			$.modal(tr("Loading..."));
			$(document)
				.unbind('plugin_convene_ready')
				.one('plugin_convene_ready', function(e) {
					var content = $(e.container).find('[name="content"]');
					content.val(convene$i.data);
					e.btns.Submit();
				});
			
			$('#plugin-edit-convene1').click();
		};
			
		$('#conveneAddDate$i')
			.click(function() {
				var picker = $('<input name="date" style="width: 50px;" />')
					.datetimepicker({
						onClose: function() {
							convene$i.addDate($(this).val());
						}
					});
					
				$(this).replaceWith(picker);
			});
			
		$('.conveneDeleteDate$i')
			.click(function() {
				convene$i.deleteDate($(this).data("date"));
			});
JQ
);
	
	return $result;
}