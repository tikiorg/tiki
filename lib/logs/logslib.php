<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class LogsLib extends TikiLib
{

	function add_log($type, $message, $who='', $ip='', $client='', $time='')
	{
		global $user;
		if (empty($who)) {
			if (!empty($user)) {
				$who = $user;
			} else {
				$who = 'Anonymous';
			}
		}
		if (empty($ip)) {
			$ip = $this->get_ip_address();
		}
		if (empty($client)) {
			if (empty($_SERVER['HTTP_USER_AGENT'])) {
				$client = 'NO USER AGENT';
			} else {
				$client = $_SERVER['HTTP_USER_AGENT'];
			}
		}
		if (empty($time)) {
			$time = $this->now;
		}
		/*
		$query = "insert into `tiki_logs` (`logtype`,`logmessage`,`loguser`,`logip`,`logclient`,`logtime`) values (?,?,?,?,?,?)";
		$result = $this->query($query,array($type,$message,$who,$ip,$client,(int)$time));
		*/
		$this->add_action($type, 'system', 'system', $message, $who, $ip, $client, $time);
	}

	function list_logs($type='', $user='', $offset=0, $maxRecords=-1, $sort_mode='lastModif_desc', $find='', $min=0, $max=0)
	{
		$actions =  $this->list_actions($type, 'system', $user, $offset, $maxRecords, $sort_mode, $find, $min, $max,'', true);
		return $actions;
	}

	function old_list_logs($type='', $user='', $offset=0, $maxRecords=-1, $sort_mode='logtime_desc', $find='', $min=0, $max=0)
	{
		$bindvars = array();
		$amid = array();
		$mid = '';
		if ($find) {
			$findesc = '%'.$find.'%';
			$amid[] = "`logmessage` like ? or `loguser` like ? or 'logip' like ?";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		}
		if ($type) {
			$amid[] = "`logtype` = ?";
			$bindvars[] = $type;
		}
		if ($user) {
			if (is_array($user)) {
				$amid[] = '`loguser` in ('.implode(',',array_fill(0,count($user),'?')).')';
				foreach ($user as $u)
					$bindvars[] = $u;
			} else {
				$amid[] = "`loguser` = ?";
				$bindvars[] = $user ;
			}
		}

		if ($min) {
			$amid[] = "`logtime` > ?";
			$bindvars[] = $min;
		}
		if ($max) {
			$amid[] = "`logtime` < ?";
			$bindvars[] = $max;
		}
		if (count($amid)) {
			$mid = " where ".implode(" and ",$amid)." ";
		}
		$query = "select `logId`,`loguser`,`logtype`,`logmessage`,`logtime`,`logip`,`logclient` ";
		$query.= " from `tiki_logs` $mid order by ".$this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_logs` $mid";
		$ret = $this->fetchAll($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}
	function clean_logs($date)
	{
		$query = "delete from `tiki_actionlog` where `object`='system' and `objectType`='system' and `lastModif`<=?";
		$this->query($query, array((int)$date));
	}

	/* action = "Updated", "Created", "Removed", "Viewed", "Removed version $version", "Changed actual version to $version"
	 * type = 'wiki page', 'category', 'article', 'image gallery', 'tracker', 'forum thread'
	 * TODO: merge $param and $contributions together into a hash and but everything in actionlog_params
	*/
	function object_must_be_logged($action, $object, $objectType) {
		global $prefs;
		if ($objectType == 'wiki page' && $action != 'Viewed') {
			$logObject = true; // to have the tiki_my_edit, history and mod-last_modif_pages
		} else {
			$logObject = $this->action_must_be_logged($action, $objectType);
		}
		$logCateg = $prefs['feature_categories'] == 'y'? $this->action_must_be_logged('*', 'category'): false;
		if (!$logObject && !$logCateg) {
			return 0;
		}
		if ($logCateg) {
			global $categlib; include_once('lib/categories/categlib.php');
			if ($objectType == 'comment') {
				preg_match('/type=([^&]*)/', $param, $matches);
				$categs = $categlib->get_object_categories($matches[1], $object);
			} else {
				$categs = $categlib->get_object_categories($objectType, $object);
			}
		}
	}
	function add_action($action, $object, $objectType='wiki page', $param='', $who='', $ip='', $client='', $date='', $contributions='', $hash='')
	{
		global $user, $prefs;
		if ($objectType == 'wiki page' && $action != 'Viewed') {
			$logObject = true; // to have the tiki_my_edit, history and mod-last_modif_pages
		} else {
			$logObject = $this->action_must_be_logged($action, $objectType);
		}
		$logCateg = $prefs['feature_categories'] == 'y'? $this->action_must_be_logged('*', 'category'): false;
		if (!$logObject && !$logCateg) {
			return 0;
		}
		if ($date == '') {
			$date = $this->now;
		}
		if ($who == '') {
			global $tokenlib;
			if ($prefs['auth_token_access'] == 'y' && empty($user) && !empty($tokenlib) && $tokenlib->ok) {
				$user = '§TOKEN§';
			} else {
				$who = $user;
			}
		}
		if ($ip == '') {
			$ip = $this->get_ip_address();
		}
		if ($client == '') {
			$client = NULL;
		} else {
			$client = substr($client,0,200);
		}
		if ($logCateg) {
			global $categlib; include_once('lib/categories/categlib.php');
			if ($objectType == 'comment') {
				preg_match('/type=([^&]*)/', $param, $matches);
				$categs = $categlib->get_object_categories($matches[1], $object);
			} else {
				$categs = $categlib->get_object_categories($objectType, $object);
			}
		}
		$actions = array();
		if ($logObject && !$logCateg) {
			$query = "insert into `tiki_actionlog` (`action`, `object`, `lastModif`, `user`, `ip`, `comment`, `objectType`, `client`) values(?,?,?,?,?,?,?,?)";
			$this->query($query, array($action, $object, (int)$date, $who, $ip, $param, $objectType, $client));
			$actions[] = $this->lastInsertId();
		} elseif ($logObject) {
			if (count($categs) > 0) {
				foreach ($categs as $categ) {
					$query = "insert into `tiki_actionlog` (`action`, `object`, `lastModif`, `user`, `ip`, `comment`, `objectType`, `categId`, `client`) values(?,?,?,?,?,?,?,?,?)";
					$this->query($query, array($action, $object, (int)$date, $who, $ip, $param, $objectType, $categ, $client));
					$actions[] = $this->lastInsertId();
				}
			} else {
				$query = "insert into `tiki_actionlog` (`action`, `object`, `lastModif`, `user`, `ip`, `comment`, `objectType`, `client`) values(?,?,?,?,?,?,?,?)";
				$this->query($query, array($action, $object, (int)$date, $who, $ip, $param, $objectType, $client));
				$actions[] = $this->lastInsertId();
			}
		}
		if (!empty($contributions)) {
			foreach ($actions as $a) {
				$query = "insert into `tiki_actionlog_params` (`actionId`, `name`, `value`) values(?,?,?)";
				foreach ($contributions as $contribution) {
					$this->query($query, array($a, 'contribution', $contribution));			
				}
			}
		}
		if (!empty($hash)) {
			$query = "insert into `tiki_actionlog_params` (`actionId`, `name`, `value`) values(?,?,?)";
			foreach ($actions as $a) {
				foreach ($hash as $h) {
			  		foreach ($h as $param=>$val) {
						$this->query($query, array($a, $param, $val));
					}
				}
			}
		}
		return  isset($actions[0])? $actions[0]: 0;
	}

	function action_must_be_logged($action, $objectType)
	{
		global $prefs;

		return $this->action_is_viewed($action, $objectType, true);
	}

	function action_is_viewed($action, $objectType, $logged = false)
	{
		global $prefs;
		static $is_viewed;

		// for previous compatibility
		// the new action are added with a if ($feature..)
		if ($prefs['feature_actionlog'] != 'y') {
			return true;
		}

		if ( !isset($is_viewed) ) {
			$logActions = $this->get_all_actionlog_conf();
			$is_viewed = array();
			foreach ($logActions as $conf) {
				if ($logged) {
					$is_viewed[$conf['objectType']][$conf['action']] = $conf['status'] == 'v' || $conf['status'] == 'y';	
				} else {
					$is_viewed[$conf['objectType']][$conf['action']] = $conf['status'] == 'v';	
				}	
			}
		}

		if ( isset($is_viewed[$objectType][$action]) ) {
			return $is_viewed[$objectType][$action];
		} elseif ( isset($is_viewed[$objectType]['*']) ) {
			return $is_viewed[$objectType]['*'];
		} else {
			return false;
		}
	}

	function set_actionlog_conf($action, $objectType, $status)
	{
		global $actionlogConf;
		$this->delete_actionlog_conf($action, $objectType);
		$action = str_replace('*','%',$action);
		$query = "insert into `tiki_actionlog_conf` (`action`, `objectType`, `status`) values(?, ?, ?)";
		$this->query($query, array($action, $objectType, $status));
		unset($actionlogConf);
	}

	function delete_actionlog_conf($action, $objectType)
	{
		if ($action === '*') {
			$action = '%';
		}
		$query = "delete from `tiki_actionlog_conf` where `action`=? and `objectType`= ?";
		$this->query($query, array($action, $objectType));
	}

	function get_all_actionlog_conf()
	{
		global $actionlogConf;
		if (!isset($actionlogConf)) {
			$actionlogConf = self::get_actionlog_conf();
		}
		return $actionlogConf;
	}

	function get_actionlog_conf($type = '%', $action = '%')
	{
			$actionlogconf = array();
			$query = "select * from `tiki_actionlog_conf` where `objectType` like '$type' and `action` like '$action' order by `objectType` desc, `action` asc";
			$result = $this->query($query, array());
			while ($res = $result->fetchRow()) {
				if ( $res['action'] == '%' ) {
					 $res['action'] = '*';
				}
				$res['code'] = self::encode_actionlog_conf($res['action'], $res['objectType']);
				$actionlogconf[] = $res;
			}
		return $actionlogconf;
	}

	function get_actionlog_types()
	{
			$actionlogtype = array();
			$query = "select distinct `objectType` from `tiki_actionlog_conf` order by `objectType`";
			$result = $this->query($query, array());
			while ($res = $result->fetchRow()) {
				$actionlogtypes[] = $res['objectType'];
			}
		return $actionlogtypes;
	}

	function get_actionlog_actions()
	{
			$actionlogactions = array();
			$query = "select distinct `action` from `tiki_actionlog_conf` order by `action`";
			$result = $this->query($query, array());
			while ($res = $result->fetchRow()) {
				if ( $res['action'] != '%' ) {
					$actionlogactions[] = $res['action'];
				}
			}
		return $actionlogactions;
	}

	function encode_actionlog_conf($action, $objectType)
	{
		return str_replace(' ', '0', $action.'_'.$objectType);
	}

	function decode_actionlog_conf($string)
	{
		return explode('_', str_replace('0', ' ', $conf));
	}

	function list_actions($action='', $objectType='', $user='', $offset=0
		, $maxRecords=-1, $sort_mode='lastModif_desc', $find='', $start=0
		, $end=0, $categId='', $all=false
	)	{
		global $prefs, $section, $tikilib, $contributionlib;
		include_once('lib/contribution/contributionlib.php');

		$bindvars = array();
		$bindvarsU = array();
		$amid = array();
		$mid1 = '';
		$mid2 = '';
		if ($find) {
			$findesc = '%'.$find.'%';
			$amid[] = "(`comment` like ? or a.`action` like ? or `object` like ?)";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		}
		if ($action) {
			$amid[] = "a.`action` = ?";
			$bindvars[] = $action;
		}
		if ($objectType) {
			$amid[] = "c.`objectType` = ?";
			$bindvars[] = $objectType;
		}
		if ($user == 'Anonymous') {
			$amid[] = "`user` = ?";
			$bindvars[] = '' ;
		} elseif ($user == 'Registered') {
			$amid[] = "`user` != ?";
			$bindvars[] = '' ;
		} else if ($user) {
			if (is_array($user)) {
				$mid1 = '`user` in ('.implode(',',array_fill(0,count($user),'?')).')';
				$mid2 = 'ap.`value` in ('.implode(',',array_fill(0,count($user),'?')).') and ap.`name`=? and ap.`actionId`=a.`actionId`';
				foreach ($user as $u) {
					$bindvarsU[] = $u;
				}
				foreach ($user as $u) {
					$bindvarsU[] = $tikilib->get_user_id($u);
				}
				$bindvarsU[] = 'contributor';
			} else {
				$mid1 = '`user` = ?';
				$mid2 = 'ap.`value`=? and ap.`name`=? and ap.`actionId`=a.`actionId`';
				$bindvarsU[] = $user ;
				$bindvarsU[] = $tikilib->get_user_id($user) ;
				$bindvarsU[] = 'contributor';
			}
		}
		if ($start) {
			$amid[] = "`lastModif` > ?";
			$bindvars[] = $start;
		}
		if ($end) {
			$amid[] = "`lastModif` < ?";
			$bindvars[] = $end;
		}
		if ($categId && $categId != 0) {
			if (is_array($categId)) {
				$amid[] = "`categId`in (?)";
				$bindvars[] = implode(",", $categId);
			} else {
				$amid[] = "`categId` = ?";
				$bindvars[] = $categId;
			}
		}
		$amid[] = " a.`action` like c.`action` and a.`objectType` = c.`objectType`".($all? "":" and (c.`status` = 'v')");

		if (count($amid)) {
			$mid = implode(" and ",$amid);
		}
		if (!empty($bindvarsU)) {
			$bindvars = array_merge($bindvars, $bindvarsU, $bindvars);
			$query = "(select distinct a.* from `tiki_actionlog` a ,`tiki_actionlog_conf` c where $mid and $mid1)";
			$query .= "union (select distinct a.* from `tiki_actionlog` a ,`tiki_actionlog_conf` c,`tiki_actionlog_params` ap where $mid2 and $mid)";
			$query_cant = "select count(distinct `actionId`) from `tiki_actionlog` where `actionId` in (select distinct a.`actionId` from `tiki_actionlog` a ,`tiki_actionlog_conf` c where $mid and $mid1 union select distinct a.`actionId` from `tiki_actionlog` a ,`tiki_actionlog_conf` c,`tiki_actionlog_params` ap where $mid2 and $mid)";
		} else {
			$query = "select distinct a.* from `tiki_actionlog` a ,`tiki_actionlog_conf` c where $mid";
			$query_cant = "select count(distinct actionId) from `tiki_actionlog` a ,`tiki_actionlog_conf` c where $mid";
		}
		$query .= " order by ".$this->convertSortMode($sort_mode);
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = array();
		while ($res = $result->fetchRow()) {
				if ($prefs['feature_contribution'] == 'y' && ($res['action'] == 'Created' || $res['action'] == 'Updated' || $res['action'] == 'Posted' || $res['action'] == 'Replied')) {
					if  ($res['objectType'] == 'wiki page') {
						$res['contributions'] = $this->get_action_contributions($res['actionId']);
					} elseif ($id = $this->get_comment_action($res)) {
						$res['contributions'] = $this->get_action_contributions($res['actionId']);
					} else {
						$res['contributions'] = $contributionlib->get_assigned_contributions($res['object'], $res['objectType']); // todo: do a left join
					}
				}
				if ($prefs['feature_contributor_wiki'] == 'y' && $res['objectType'] == 'wiki page') {
					$res['contributors'] = $this->get_contributors($res['actionId']);
					$res['nbContributors'] = 1 + count($res['contributors']);
				}
 				// patch for xavi
				if ($res['objectType'] == 'comment' && empty($res['categId'])) {
					global $categlib; include_once('lib/categories/categlib.php');
					preg_match('/type=([^&]*)/', $res['comment'], $matches);
					$categs = $categlib->get_object_categories($matches[1], $res['object']);
					$i = 0;
					foreach ($categs as $categId) {
						$res['categId'] = $categId;
						if ($i++ > 0)
							$ret[] = $res;
					}
				} 
				// For tiki logs
				if ( $res['objectType'] === 'system' ) {
					$what = $res['object'] === 'system' ? '':$res['object'].' : ';
					$res['object'] = $res['action'];
					$res['action'] = $what.$res['comment'];
				}
				$ret[] = $res;
		}
		return array('data' => $ret, 'cant' => $cant);
	}

	function sort_by_date($action1, $action2)
	{
		return ($action1['lastModif'] -  $action2['lastModif']);
	}

	function get_login_time($logins, $startDate, $endDate, $actions)
	{
		//FIXME
		if ($endDate > $this->now) {
			$endDate = $this->now;
		}
		$logTimes = array();
		foreach ($logins as $login) {
			if (!array_key_exists($login['user'], $logTimes)) {
				if ($login['action'] == 'timeout' || $login['action'] == 'logged out') {
					$logTimes[$login['user']]['last'] = $startDate;
			}	else {
					$logTimes[$login['user']]['last'] = 0;
			}
				$logTimes[$login['user']]['time'] = 0;
				$logTimes[$login['user']]['nbLogins'] = 0;
			}
			if (strstr($login['action'], 'logged from') || $login['action'] == 'back') {
				if (strstr($login['action'], 'logged from')) {
					++$logTimes[$login['user']]['nbLogins'];
				}
 				// can be already log in
				if ($logTimes[$login['user']]['last'] == 0) {
					$logTimes[$login['user']]['last'] = $login['lastModif'];
				}
			} elseif (($login['action'] == 'timeout' || $login['action'] == 'logged out') && $logTimes[$login['user']]['last'] > 0) {
				$logTimes[$login['user']]['time'] += $login['lastModif'] - $logTimes[$login['user']]['last'];
				$logTimes[$login['user']]['last'] = 0;
			}
		}
		// update time for those still logged in
		foreach ($logTimes as $user=>$logTime) {
			if ($logTime['last']) {
				$logTimes[$user]['time'] += $endDate - $logTime['last'];
			}
		}
		// update time for those who were always logged in
		foreach ($actions as $action) {
			if ($action['user'] && !array_key_exists($action['user'], $logTimes)) {
				$logTimes[$action['user']]['time'] = $endDate - $startDate;
			}
		}
		foreach ($logTimes as $user=>$login) {
			$nbMin = floor($login['time']/60);
			$nbHour = floor($nbMin/60);
			$nbDay = floor($nbHour/24);
			$logTimes[$user]['secs'] = $login['time'] - $nbMin*60;
			$logTimes[$user]['mins'] = $nbMin - $nbHour*60;
			$logTimes[$user]['hours'] = $nbHour - $nbDay*24;
			$logTimes[$user]['days'] = $nbDay;
		}
	return $logTimes;
	}

	function get_volume_action($action)
	{
		$bytes = array();
		if (preg_match('/bytes=([0-9\-+]+)/', $action['comment'], $matches)) {//old syntax
			if (preg_match('/\+([0-9]+)/', $matches[1], $m)) {
				$bytes['add'] = $m[1];
			}
			if (preg_match('/\-([0-9]+)/', $matches[1], $m)) {
				$bytes['del'] = $m[1];
			}
		} else {
			if (preg_match('/add=([0-9\-+]+)/', $action['comment'], $matches)) {
				$bytes['add'] = $matches[1];
			}
			if (preg_match('/del=([0-9\-+]+)/', $action['comment'], $matches)) {
				$bytes['del'] = $matches[1];
			}
		}
		return $bytes;
	}

	function get_comment_action($action)
	{
		if (preg_match('/comments_parentId=([0-9\-+]+)/', $action['comment'], $matches)) {
			return $matches[1];
		} elseif (preg_match('/#threadId([0-9\-+]+)/', $action['comment'], $matches)) {
			return $matches[1];
		} elseif (preg_match('/sheetId=([0-9]+)/', $action['comment'], $matches)) {
			return $matches[1];
		} elseif (preg_match('/postId=([0-9]+)/', $action['comment'], $matches)) {
			return $matches[1];
		} else {
			return '';
		}
	}

	function get_stat_actions_per_user($actions)
	{
		$stats = $this->get_stat_actions_per_field($actions, 'user');
		return $stats;
	}

	function get_stat_actions_per_field($actions, $field='user')
	{
		$stats = array();
		$actions_name = array();
		
		$actionlogConf = $this->get_all_actionlog_conf();

		foreach ($actions as $action) {
			if ( strpos($action['action'],'logged from') === 0 ) {
        $action['action'] = 'login';
      }
			if ( strpos($action['action'],'logged out') === 0 ) {
        $action['action'] = 'login';
      }
			$name = $action['action'].'/'.$action['objectType'];
			$sort = $action['objectType'].'/'.$action['action'];
			if ( $this->action_is_viewed($action['action'],$action['objectType']) and !in_array($name, $actions_name)) {
				$actions_name[$sort] = $name;
			}
		}

		ksort($actions_name);

		foreach ($actions as $action) {
			$key = $action[$field];
			if ( !isset($stats[$key]) ) {
				$stats[$key] = array_fill_keys($actions_name,0);
				$stats[$key][$field] = $action[$field];
			}
			$name = $action['action'].'/'.$action['objectType'];
			if ( ($index = array_search($name,$actions_name)) !== false ) {
				if ($field == 'object') {
					$stats[$key]['link'] = isset($action['link']) ? $action['link'] : null;
				}
				++$stats[$key][$name];
			}
		}
		
		sort($stats, SORT_STRING); // will sort on the first field
		return $stats;
	}

	function get_stat_contributions_per_group($actions, $selectedGroups)
	{
		global $tikilib;
		$statGroups = array();
		foreach ($actions as $action) {
			if (!empty($previousAction) && $action['lastModif'] == $previousAction['lastModif'] && $action['user'] == $previousAction['user'] && $action['object'] == $previousAction['object'] && $action['objectType'] == $previousAction['objectType']) {
					// differ only by the categories
					continue;
			}
			if ( strpos($action['action'],'logged from') === 0 ) {
        $action['action'] = 'login';
      }
			if ( strpos($action['action'],'logged out') === 0 ) {
        $action['action'] = 'login';
      }
			$previousAction = $action;
			if (empty($action['user'])) {
				$groups = array('Anonymous');
			} else {
				$groups = $tikilib->get_user_groups($action['user']);
				$groups = array_diff($groups, array('Anonymous'));
			}
			foreach ($groups as $key=>$group) {
				if (isset($selectedGroups) && $selectedGroups[$group] != 'y') {
					continue;
				}
				if (empty($action['contributions'])) {
					continue;
				}
				foreach ($action['contributions'] as $contribution) {
					if (!isset($statGroups[$group])) {
						$statGroups[$group][$contribution['name']]['add'] = 0;
						$statGroups[$group][$contribution['name']]['del'] = 0;
						$statGroups[$group][$contribution['name']]['dif'] = 0;
					}
					$statGroups[$group][$contribution['name']]['add'] += $action['contributorAdd'];
					$statGroups[$group][$contribution['name']]['del'] += $action['contributorDel'];
					$statGroups[$group][$contribution['name']]['dif'] += $action['contributorAdd'] - $action['contributorDel'];
				}
			}
		}
		ksort($statGroups);
		return $statGroups;
	}

	function get_action_stat_categ($actions, $categNames)
	{
		$stats = array();
		$actionlogConf = $this->get_all_actionlog_conf();
		foreach ($actions as $action) {
			//if ($action['categId'] == 0) print also stat for non categ object
			//	continue;
			if ( strpos($action['action'],'logged from') === 0 ) {
        $action['action'] = 'login';
      }
			if ( strpos($action['action'],'logged out') === 0 ) {
        $action['action'] = 'login';
      }
			$key = $action['categId'];
			if (!array_key_exists($key, $stats)) {
				$stats[$key]['category'] = $key? $categNames[$key]: '';
				foreach ($actionlogConf as $conf) {
					// don't take category
					if ($conf['status'] == 'v' && $conf['action'] != '*') {
						$stats[$key][$conf['action'].'/'.$conf['objectType']] = 0;
					}
				}
			}
			++$stats[$key][$action['action'].'/'.$action['objectType']];
		}
		sort($stats); //sort on the first field category
		return $stats;
	}

	function get_action_vol_categ($actions, $categNames)
	{
		$stats = array();
		$actionlogConf = $this->get_all_actionlog_conf();
		foreach ($actions as $action) {
			//if ($action['categId'] == 0) print also stat for non categ object
			//	continue;
			if ( strpos($action['action'],'logged from') === 0 ) {
        $action['action'] = 'login';
      }
			if ( strpos($action['action'],'logged out') === 0 ) {
        $action['action'] = 'login';
      }
			if (!($bytes = $this->get_volume_action($action))) {
				continue;
			}
			$key = $action['categId'];
			if (!array_key_exists($key, $stats)) {
				$stats[$key]['category'] = $key? $categNames[$key]: '';
			}
			if (!isset($stats[$key][$action['objectType']]['add'])) {
				$stats[$key][$action['objectType']]['add'] = 0;
				$stats[$key][$action['objectType']]['del'] = 0;
				$stats[$key][$action['objectType']]['dif'] = 0;
			}
			$dif = 0;
			if (isset($bytes['add'])) {
				$stats[$key][$action['objectType']]['add'] += $bytes['add'];
				$dif = $bytes['add'];
			}
			if (isset($bytes['del'])) {
				$stats[$key][$action['objectType']]['del'] += $bytes['del'];
				$dif -= $bytes['del'];
			}
			$stats[$key][$action['objectType']]['dif'] += $dif;
		}
		sort($stats); //sort on the first field category
		return $stats;
	}

	function get_action_vol_user_categ($actions, $categNames)
	{
		$stats = array();
		$actionlogConf = $this->get_all_actionlog_conf();
		foreach ($actions as $action) {
			//if ($action['categId'] == 0) print also stat for non categ object
			//	continue;
			if ( strpos($action['action'],'logged from') === 0 ) {
        $action['action'] = 'login';
      }
			if ( strpos($action['action'],'logged out') === 0 ) {
        $action['action'] = 'login';
      }
			if ($action['user'] == '' 
					|| !($bytes = $this->get_volume_action($action))) {
				continue;
			}
			$key = $action['categId'].'/'.$action['user'];
			if (!array_key_exists($key, $stats)) {
				$stats[$key]['category'] = $action['categId']? $categNames[$action['categId']]: '';
				$stats[$key]['user'] = $action['user'];
			}
			if (!isset($stats[$key][$action['objectType']]['add'])) {
				$stats[$key][$action['objectType']]['add'] = 0;
				$stats[$key][$action['objectType']]['del'] = 0;
				$stats[$key][$action['objectType']]['dif'] = 0;
			}
			$dif = 0;
			if (isset($bytes['add'])) {
				$stats[$key][$action['objectType']]['add'] += $bytes['add'];
				$dif = $bytes['add'];
			}
			if (isset($bytes['del'])) {
				$stats[$key][$action['objectType']]['del'] += $bytes['del'];
				$dif -= $bytes['del'];
			}
			$stats[$key][$action['objectType']]['dif'] += $dif;
		}
		sort($stats); //sort on the first field category
		return $stats;
	}

	function get_action_vol_type($vols)
	{
		$types = array();
		foreach ($vols as $vol) {
			foreach ($vol as $key=>$value) {
				if ($key != 'category' && $key != 'user' && !in_array($key, $types)) {
					$types[] = $key;
				}
			}
		}
		return $types;
	}
	function get_actions_per_user_categ($actions, $categNames)
	{
		$stats = array();
		$actionlogConf = $this->get_all_actionlog_conf();
		foreach ($actions as $action) {
			if (empty($action['categId'])) {
				continue;
			}
			if ( strpos($action['action'],'logged from') === 0 ) {
        $action['action'] = 'login';
      }
			if ( strpos($action['action'],'logged out') === 0 ) {
        $action['action'] = 'login';
      }
			$key = $action['categId'].'/'.$action['user'];;
			if (!array_key_exists($key, $stats)) {
				$stats[$key]['category'] = $categNames[$action['categId']];
				$stats[$key]['user'] = $action['user'];
				foreach ($actionlogConf as $conf) {
					// don't take category
					if ($conf['status'] == 'v' && $conf['action'] != '*') {
						$stats[$key][$conf['action'].'/'.$conf['objectType']] = 0;
					}
				}
			}
			++$stats[$key][$action['action'].'/'.$action['objectType']];
		}
		sort($stats); // sort on the first fields categ , then user
		return $stats;
	}

	function in_kb($vol)
	{
		for ($i = count($vol) -1; $i >= 0; --$i) {
			foreach ($vol[$i] as $k=>$v) {
				if ($k != 'category' && $k != 'user') {
					$vol[$i][$k]['add'] = round($vol[$i][$k]['add']/1024);
					$vol[$i][$k]['del'] = round($vol[$i][$k]['del']/1024);
					$vol[$i][$k]['dif'] = round($vol[$i][$k]['dif']/1024);
				}
			}
		}
		return $vol;
	}

	function export($actionlogs, $unit = 'b')
	{
	$csv = "user,date,time,action,type,object,category,categId, unit,+,-,contribution\r\n";
	foreach ($actionlogs as $action) {
		if (!isset($action['object'])) {
			$action['object'] = '';
		}
		if (!isset($action['categName'])) {
			$action['categName'] = '';
			$action['categId'] = '';
		}
		if (!isset($action['add'])) {
			$action['add'] = '';
		}
		if (!isset($action['del'])) {
			$action['del'] = '';
		}

		$csv.= '"' . $action['user']
				 . '","' . $this->date_format("%y%m%d", $action['lastModif'])
				 . '","' . $this->date_format("%H:%M", $action['lastModif'])
				 . '","' . $action['action']
				 . '","' . $action['objectType']
				 . '","' . $action['object']
				 . '","' . $action['categName']
				 . '","' . $action['categId']
				 . '","' . $unit
				 . '","' . $action['add']
				 . '","' . $action['del']
				 .'","'
				 ;
		if (isset($action['contributions'])) {
			$i = 0;
			foreach ($action['contributions'] as $contribution) {
				if ($i++) {
					$csv .= ',';
				}
				$csv .= $contribution['name'];
			}
		}
		$csv .= "\"\n";
	}
	return $csv;
	}

	function get_action_params($actionId, $name='')
	{
		if (empty($name)) {
			$query = "select * from `tiki_actionlog_params` where `actionId`=?";
			$ret = $this->fetchAll($query, array($actionId));
		} else {
			$query = "select `value` from `tiki_actionlog_params` where `actionId`=? and `name`=?";
			$result = $this->query($query, array($actionId, $name));
			$ret = array();
			while ($res = $result->fetchRow()) {
				$ret[] = $res['value'];
			}
		}
		return $ret;
	}

	function get_action_contributions($actionId)
	{
		$query = "select tc.* from `tiki_contributions` tc, `tiki_actionlog_params` tp where tp.`actionId`=? and tp.`name`=? and tp.`value`=tc.`contributionId`";
		return $this->fetchAll($query, array($actionId, 'contribution'));
	}

	function rename($objectType, $oldName, $newName)
	{
		$query = "update `tiki_actionlog`set `comment`= concat(?, `comment`) where `object`=? and (`objectType`=? or `objectType`= ?) and `comment` not like ?";
		$this->query($query, array("old=$oldName&amp;", $oldName, $objectType, 'comment' , '%old=%'));
		$query = "update `tiki_actionlog`set `object`=? where `object`=? and (`objectType`=? or `objectType`= ?)";
		$this->query($query, array($newName, $oldName, $objectType, 'comment'));
	}

	function update_category($actionId, $categId)
	{
		$query = "update `tiki_actionlog` set `categId`=? where `actionId`=?";
		$this->query($query, array($categId, $actionId));
	}

	function get_info_action($actionId)
	{
		$query = "select * from `tiki_actionlog`where `actionId`= ?";
		$result = $this->query($query, array($actionId));
		if ($res = $result->fetchRow()) {
			return $res;
		} else {
			return NULL;
		}
	}

	function delete_params($actionId, $name='')
	{
		$bindvars = array($actionId);
		if (!empty($name)) {
			$mid = 'and `name`= ?';
			$bindvars[] = $name;
		}
		$query = "delete from `tiki_actionlog_params` where `actionId`=? $mid";
		$this->query($query, $bindvars);
	}

	function insert_params($actionId, $param, $values)
	{
		$query = "insert into `tiki_actionlog_params` (`actionId`, `name`, `value`) values(?,?,?)";
		foreach ($values as $val) {
			$this->query($query, array($actionId, $param, $val));
		}
	}

	function get_stat_contribution($actions, $startDate, $endDate, $unit='w')
	{
		$contributions = array();
		$nbCols = ceil(($endDate - $startDate) / (60*60*24));
		if ($unit != 'd') {
			$nbCols = ceil($nbCols/7);
		}
		foreach ($actions as $action) {
			if (isset($action['contributions'])) {
				if (!empty($previousAction) 
						&& $action['lastModif'] == $previousAction['lastModif'] 
						&& $action['user'] == $previousAction['user'] 
						&& $action['object'] == $previousAction['object'] 
						&& $action['objectType'] == $previousAction['objectType']
						) {
					// differ only by the categories
					continue;
				}
				$previousAction = $action;
				foreach ($action['contributions'] as $contrib) {
					$i = floor(($action['lastModif'] - $startDate) / (60*60*24));
					if ($unit != 'd') {
						$i = floor($i/7);
					}
					if (empty($contributions[$contrib['contributionId']])) {
						$contributions[$contrib['contributionId']]['name'] = $contrib['name'];
						for ($j = 0; $j < $nbCols; ++$j) {
							$contributions[$contrib['contributionId']]['stat'][$j]['add'] = 0;
							$contributions[$contrib['contributionId']]['stat'][$j]['del'] = 0;
							$contributions[$contrib['contributionId']]['stat'][$j]['nbAdd'] = 0;
							$contributions[$contrib['contributionId']]['stat'][$j]['nbDel'] = 0;
							$contributions[$contrib['contributionId']]['stat'][$j]['nbUpdate'] = 0;
						}
					}
					if (!empty($action['add'])) {
						$contributions[$contrib['contributionId']]['stat'][$i]['add'] += $action['add'];
						if (empty($action['del'])) {
							++$contributions[$contrib['contributionId']]['stat'][$i]['nbAdd'];
						}
					}
					if (!empty($action['del'])) {
						$contributions[$contrib['contributionId']]['stat'][$i]['del'] += $action['del'];
						if (empty($action['add'])) {
							++$contributions[$contrib['contributionId']]['stat'][$i]['nbDel'];
						}
					}
					if (!empty($action['add']) && !empty($action['del'])) {
						++$contributions[$contrib['contributionId']]['stat'][$i]['nbUpdate'];
					}
				}
			}
		}
		return (array('nbCols'=>$nbCols, 'data'=>$contributions));
	}

	function get_stat_contributions_per_user($actions)
	{
		$tab = array();
		foreach ($actions as $action) {
			if ( strpos($action['action'],'logged from') === 0 ) {
        $action['action'] = 'login';
      }
			if ( strpos($action['action'],'logged out') === 0 ) {
        $action['action'] = 'login';
      }
			if (isset($action['contributions'])) {
				if (!empty($previousAction) 
						&& $action['lastModif'] == $previousAction['lastModif']
						&& $action['object'] == $previousAction['object'] 
						&& $action['objectType'] == $previousAction['objectType'] 
						&& $action['categId'] != $previousAction['categId']
					) {
					// differ only by the categories
					continue;
				}
				$previousAction = $action;
				foreach ($action['contributions'] as $contrib) {
					if (empty($tab[$action['user']]) or empty($tab[$action['user']]['stat'][$contrib['contributionId']])) {
						$tab[$action['user']][$contrib['contributionId']]['name'] = $contrib['name'];
						$tab[$action['user']][$contrib['contributionId']]['stat']['add'] = 0;
						$tab[$action['user']][$contrib['contributionId']]['stat']['del'] = 0;
						$tab[$action['user']][$contrib['contributionId']]['stat']['nbAdd'] = 0;
						$tab[$action['user']][$contrib['contributionId']]['stat']['nbDel'] = 0;
						$tab[$action['user']][$contrib['contributionId']]['stat']['nbUpdate'] = 0;
					}
					if ($action['contributorAdd']) {
						$tab[$action['user']][$contrib['contributionId']]['stat']['add'] += $action['contributorAdd'];
						if (!$action['contributorDel']) {
							++$tab[$action['user']][$contrib['contributionId']]['stat']['nbAdd'];
						}
					}
					if ($action['contributorDel']) {
						$tab[$action['user']][$contrib['contributionId']]['stat']['del'] += $action['contributorDel'];
						if (!$action['contributorAdd']) {
							++$tab[$action['user']][$contrib['contributionId']]['stat']['nbDel'];
						}
					}
					if ($action['contributorAdd'] && $action['contributorDel']) {
						++$tab[$action['user']][$contrib['contributionId']]['stat']['nbUpdate'];
					}
				}				
			}
		}
		ksort($tab);
		return array('data'=>$tab, 'nbCols'=>count($tab));;
	}

	function get_colors($nb)
	{
		$colors[] = 'red';	if (!--$nb) return $colors;
		$colors[] = 'yellow';	if (!--$nb) return $colors;
		$colors[] = 'blue';	if (!--$nb) return $colors;
		$colors[] = 'gray';	if (!--$nb) return $colors;
		$colors[] = 'green';	if (!--$nb) return $colors;
		$colors[] = 'aqua';	if (!--$nb) return $colors;
		$colors[] = 'lime';	if (!--$nb) return $colors;
		$colors[] = 'maroon';	if (!--$nb) return $colors;
		$colors[] = 'navy';	if (!--$nb) return $colors;
		$colors[] = 'black';	if (!--$nb) return $colors;
		$colors[] = 'purple';	if (!--$nb) return $colors;
		$colors[] = 'silver';	if (!--$nb) return $colors;
		$colors[] = 'teal';	if (!--$nb) return $colors;
		if ( $nb > 0 ) {
			while (--$nb) {
				$colors[] = rand(1, 999999);
			} 
		}
		return $colors;
	}

	function draw_contribution_vol($contributionStat, $type='add', $contributions)
	{
		$ret = array();
		$ret['totalVol'] = 0;
		$ret['x'][] = tra('Contributions');
		$ret['color'] = $this->get_colors($contributions['cant']);
		$iy = 0;
		foreach ($contributions['data'] as $contribution) {
			$ret['label'][] = utf8_decode($contribution['name']);
			$vol = 0;
			for ($ix = 0; $ix < $contributionStat['nbCols']; ++$ix) {
				if (!empty($contributionStat['data'][$contribution['contributionId']]['stat'][$ix])) {
					$vol += $contributionStat['data'][$contribution['contributionId']]['stat'][$ix][$type];
				}
			}
			$ret["y$iy"][] = $vol;
			$ret['totalVol'] += $vol;
			++$iy;
		}
		return $ret;
	}

	function draw_week_contribution_vol($contributionStat, $type='add', $contributions)
	{
		$ret = array();
		$ret['totalVol'] = 0;
		for ($i = 1, $nb = $contributionStat['nbCols']; $nb; --$nb) {
			$ret['x'][] = $i++;
		}
		$ret['color'] = $this->get_colors($contributions['cant']);
		$iy = 0;
		foreach ($contributions['data'] as $contribution) {
			$ret['label'][] = utf8_decode($contribution['name']);
			for ($ix = 0; $ix < $contributionStat['nbCols']; ++$ix) {
				if (empty($contributionStat['data'][$contribution['contributionId']]) || empty($contributionStat['data'][$contribution['contributionId']]['stat'][$ix])) {
					$ret["y$iy"][] = 0;
				} else {
					$ret["y$iy"][] = $contributionStat['data'][$contribution['contributionId']]['stat'][$ix][$type];
					$ret['totalVol'] += $contributionStat['data'][$contribution['contributionId']]['stat'][$ix][$type];
				}
			}
			++$iy;
		}
		return $ret;
	}

	function draw_contribution_user($userStat, $type='add', $contributions)
	{
		$ret = array();
		$ret['totalVol'] = 0;
		foreach ($userStat['data'] as $user=>$stats) {
			$ret['x'][] = utf8_decode($user);
		}
		$ret['color'] = $this->get_colors($contributions['cant']);
		$iy = 0;
		foreach ($contributions['data'] as $contribution) {
			$ret['label'][] = utf8_decode($contribution['name']);
			foreach ($userStat['data'] as $user=>$stats) {
				if (empty($stats[$contribution['contributionId']])) {
					$ret["y$iy"][] = 0;
				} else {
					$ret["y$iy"][] = $stats[$contribution['contributionId']]['stat']["$type"];
					$ret['totalVol'] += $stats[$contribution['contributionId']]['stat']["$type"];
				}
			}
			++$iy;
		}
		return $ret;
	}

	function draw_contribution_group($groupContributions, $type='add', $contributions)
	{
		$ret = array();
		$ret['totalVol'] = 0;
		foreach ($groupContributions as $group=>$stats) {
			$ret['x'][] = utf8_decode($group);
		}
		$ret['color'] = $this->get_colors($contributions['cant']);
		$iy = 0;
		foreach ($contributions['data'] as $contribution) {
			$ret['label'][] = utf8_decode($contribution['name']);
			foreach ($groupContributions as $group=>$stats) {
				if (empty($stats[$contribution['name']])) {
					$ret["y$iy"][] = 0;
				} else {
					$ret["y$iy"][] = $stats[$contribution['name']][$type];
					$ret['totalVol'] += $stats[$contribution['name']][$type];
				}
			}
			++$iy;
		}
		return $ret;
	}

	function get_contributors($actionId)
	{
		$query = 'select uu.`login` from `tiki_actionlog_params` tap, `users_users` uu where tap.`actionId`=? and tap.`name`=? and uu.`userId`=tap.`value`';
		return $this->fetchAll($query, array($actionId, 'contributor'));
	}

	// get the contributors of the last update of a wiki page
	function get_wiki_contributors($page_info) 
	{
		$query = 'select distinct(uu.`login`), uu.`userId` 
							from `tiki_actionlog_params` tap, `users_users` uu , `tiki_actionlog` ta 
							where tap.`actionId`= ta.`actionId` 
										and tap.`name`=? 
										and uu.`userId`=tap.`value` 
										and ta.`object`=? 
										and ta.`objectType`=? 
										and ta.`lastModif`=? 
							order by `login` asc'
							;
		return $this->fetchAll($query, array('contributor', $page_info['pageName'], 'wiki page', $page_info['lastModif'])); 
	}
   
	function split_actions_per_contributors($actions, $users)
	{
		$contributorActions = array();
		foreach ($actions as $action) {
			$bytes = $this->get_volume_action($action);
			if ( strpos($action['action'],'logged from') === 0 ) {
        $action['action'] = 'login';
      }
			if ( strpos($action['action'],'logged out') === 0 ) {
        $action['action'] = 'login';
      }
			$nbC = isset($action['nbContributors'])? $action['nbContributors']:1;
			if (isset($bytes['add'])) {
				$action['add'] = $bytes['add'];
				$action['contributorAdd'] = round($bytes['add']/$nbC);
				$action['comment'] = 'add='.$action['contributorAdd'];
			}
			if (isset($bytes['del'])) {
				$action['del'] = $bytes['del'];
				$action['contributorDel'] = round($bytes['del']/$nbC);
				if (!empty($action['comment'])) {
					$action['comment'] .= '&del='.$action['contributorDel'];
				} else {
					$action['comment'] = 'del='.$action['contributorDel'];
				}
			}
			if (empty($users) || in_array($action['user'], $users)) {
				$contributorActions[] = $action;
			}
			if (isset($action['contributors'])) {
				foreach ($action['contributors'] as $contributor) {
					if (empty($users) || in_array($contributor['login'], $users)) {
						$action['user'] = $contributor['login'];
						$contributorActions[] = $action;
					}
				}
			}
		}
		return $contributorActions;
	}

	function list_logsql($sort_mode='created_desc', $offset=0, $maxRecords=-1, $find='')
	{
		global $prefs;
		$bindvars = array();
		if (!empty($find)) {
			$findesc = '%'.$find.'%';
			$amid = '`sql1` like ? or `params` like ? or `tracer` like ?';
			$bindvars[] = $findesc;$bindvars[] = $findesc;$bindvars[] = $findesc;
		}
		$query = 'select * from `adodb_logsql`'.($find?" where $amid":'').' order by '.$this->convertSortMode($sort_mode);
		$ret = $this->fetchAll($query, $bindvars, $maxRecords, $offset);
		$query_cant = 'select count(*) from `adodb_logsql`'.($find?" where $amid":'');
		$cant = $this->getOne($query_cant, $bindvars);
		$retval = array();
		$retval['data'] = $ret;
		$retval['cant'] = $cant;
		return $retval;
	}

	function clean_logsql()
	{
		$query = 'delete from  `adodb_logsql`';
		$this->query($query, array());
	}

	function graph_to_jpgraph(&$jpgraph, $series, $accumulated = false, $color='whitesmoke', $colorLegend='white')
	{
		$jpgraph->SetScale('textlin');
		$jpgraph->setMarginColor($color);
		$jpgraph->xaxis->SetTickLabels($series['x']);
		$plot = array();
		for ($i = 0; isset($series["y$i"]); ++$i) {
			$plot[$i] = new BarPlot($series["y$i"]);
			$plot[$i]->SetFillColor($series['color'][$i]);
			$plot[$i]->SetLegend($series['label'][$i]);
		}
		if ($accumulated) {
			$gbplot = new AccBarPlot($plot);
		} else {
			$gbplot = new GroupBarPlot($plot);
		}
		//$jpgraph ->legend->Pos( 0.5,0.5,"right" ,"center");
		$jpgraph->legend->SetFillColor($colorLegend);
		$jpgraph->Add( $gbplot);
	}

	function insert_image($galleryId, $graph, $ext, $title, $period)
	{
		global $prefs, $user;
		global $imagegallib; include_once('lib/imagegals/imagegallib.php');
		$filename = $prefs['tmpDir'].'/'.md5(rand().time()).'.'.$ext;
		$graph->Stroke($filename);
		$info = getimagesize($filename);
		$size = filesize($filename);
		$fp = fopen($filename, "rb");
		$data = fread($fp, $size);
		fclose($fp);
		$imagegallib->insert_image($_REQUEST['galleryId'], $title.$period, '', $title.$period.'.'.$ext, 'image/'.$ext, $data, $size, $info[0], $info[1], $user, '', '');
	}

	function get_more_info($actions, $categNames = array())
	{
		global $tikilib, $prefs;
	foreach($actions as &$action) {
		if ( empty($action['user']) ) {
			$action['user'] = 'Anonymous';
		}
		if ($action['categId'] && $categNames) {
			$action['categName'] = $categNames[$action['categId']];
		}
		if ($bytes = $this->get_volume_action($action)) {
			if (isset($bytes['add'] )) {
				$action['add'] = $bytes['add'];
			}
			if (isset($bytes['del'])) {
				$action['del'] = $bytes['del'];
			}
		}
		switch ($action['objectType']) {
		case 'wiki page':
			if (preg_match("/old=(.*)/", $action['comment'], $matches)) {
				$action['link'] = 'tiki-index.php?page='.$action['object'].'&amp;old='.$matches[1];
			} else {
				$action['link'] = 'tiki-index.php?page='.$action['object'];
			}
			break;
		case 'article':
			global $artlib; require_once 'lib/articles/artlib.php';
			$action['link'] = 'tiki-read_article.php?articleId='.$action['object'];
			if (!isset($articleNames)) {
				$objects = $artlib->list_articles(0, -1, 'title_asc', '', 0, 0, '');
				$articleNames = array();
				foreach ($objects['data'] as $object) {
					$articleNames[$object['articleId']] = $object['title'];
				}
			}
			if (!empty($articleNames[$action['object']]))
				$action['object'] = $articleNames[$action['object']];
			break;
		case 'category':
			$action['link'] = 'tiki-browse_categories.php?parentId='.$action['object'];
			if ($categNames && !empty($categNames[$action['object']])) {
				$action['object'] = $categNames[$action['object']];
			}
			break;
		case 'forum':
			if ($action['action'] == 'Removed') {
				$action['link'] = 'tiki-view_forum.php?forumId='.$action['object'].'&'.$action['comment'];// threadId dded for debug info
			} else {
				$action['link'] = 'tiki-view_forum_thread.php?forumId='.$action['object'].'&'.$action['comment'];
			}
			if (!isset($forumNames)) {
				global $commentslib; include_once('lib/comments/commentslib.php');
				$objects = $commentslib->list_forums(0, -1, 'name_asc', '');
				$forumNames = array();
				foreach ($objects['data'] as $object) {
					$forumNames[$object['forumId']] = $object['name'];
				}
			}
			if (!empty($forumNames[$action['object']]))
				$action['object'] = $forumNames[$action['object']];
			break;
		case 'image gallery':
			if ($action['action'] == 'Uploaded') {
				$action['link'] = 'tiki-browse_image.php?galleryId='.$action['object'].'&'.$action['comment'];
			} else {
				$action['link'] = 'tiki-browse_gallery.php?galleryId='.$action['object'];
			}
			if (!isset($imageGalleryNames)) {
				global $imagegallib; include_once('lib/imagegals/imagegallib.php');
				$objects = $imagegallib->list_galleries(0, -1, 'name_asc', 'admin');
				foreach ($objects['data'] as $object) {
					$imageGalleryNames[$object['galleryId']] = $object['name'];
				}
			}
			if (!empty($imageGalleryNames[$action['object']])) {
				$action['object'] = $imageGalleryNames[$action['object']];
			}
			break;
		case 'file gallery':
			if ($action['action'] == 'Uploaded' || $action['action'] == 'Downloaded') {
				$action['link'] = 'tiki-upload_file.php?galleryId='.$action['object'].'&'.$action['comment'];
			} else {
				$action['link'] = 'tiki-list_file_gallery.php?galleryId='.$action['object'];
			}
			if (!isset($fileGalleryNames)) {
				global $filegallib; include_once('lib/filegals/filegallib.php');
				$objects = $filegallib->list_file_galleries(0, -1, 'name_asc', 'admin', '', $prefs['fgal_root_id']);
				foreach ($objects['data'] as $object) {
					$fileGalleryNames[$object['galleryId']] = $object['name'];
				}
			}
			if (!empty($fileGalleryNames[$action['object']])) {
				$action['object'] = $fileGalleryNames[$action['object']];
			}
			break;
		case 'comment':
			preg_match('/type=([^&]*)(&.*)/', $action['comment'], $matches);
			switch ($matches[1]) {
			case 'wiki page': case 'wiki+page': case 'wiki%20page':
				$action['link'] = 'tiki-index.php?page='.$action['object'];
				if (preg_match("/old=(.*)&amp;/", $action['comment'], $ms)) {
					$action['link'] .= '&amp;old='.$ms[1];
				}
				$action['link'] .= $matches[2];
				break;
			case 'file gallery':
				$action['link'] = 'tiki-list_file_gallery.php?galleryId='.$action['object'].$matches[2];
				break;
			case 'image gallery':
				$action['link'] = 'tiki-browse_gallery.php?galleryId='.$action['object'].$matches[2];
				break;
			}
			break;
		case 'sheet':
			if (!isset($sheetNames)) {
				global $sheetlib; include_once('lib/sheet/grid.php');
				$objects = $sheetlib->list_sheets();
				foreach ($objects['data'] as $object) {
					$sheetNames[$object['sheetId']] = $object['title'];
				}
			}
			if (!empty($sheetNames[$action['object']])) {
				$action['object'] = $sheetNames[$action['object']];
			}
			$action['link'] = 'tiki-view_sheets.php?sheetId='.$action['object'];
			break;
		case 'blog':

			if (!isset($blogNames)) {
				global $bloglib; require_once('lib/blogs/bloglib.php');
				$objects = $bloglib->list_blogs();
				foreach ($objects['data'] as $object) {
					$blogNames[$object['blogId']] = $object['title'];
				}
		}
			$action['link'] = 'tiki-view_blog.php?'.$action['comment'];
			if (!empty($blogNames[$action['object']]))
				$action['object'] = $blogNames[$action['object']];
			break;
		}
	}
	return $actions;
	} // end of get_more_info($actions)

	function remove_action($actionId)
	{
		$query = 'delete from `tiki_actionlog` where `actionId`=?';
		$this->query($query, array($actionId));
		$query = 'delete from `tiki_actionlog_params` where `actionId`=?';
		$this->query($query, array($actionId));
	}
	
	function get_who_viewed($mystuff, $anonymous = true)
	{
		if (!$mystuff) {
			return false;
		}
		global $prefs;
		$bindvars = array();
		$mid = '';
		foreach ($mystuff as $obj) {
			// If changing type, compose rest of partial filter immediately
			if (isset($objectType) && $obj["objectType"] != $objectType) {
				$mid .= ' and `object` in ('.implode(',', array_fill(0, $thistype,'?')).')';
				// add comments filter if needed
				if ($comments) {
					$bindvars = array_merge($bindvars, $comments);
					$mid .= ' and `comment` in ('.implode(',', array_fill(0, count($comments),'?')).')';				
				}
			}
			// If starting out, or changing type, to start new sub filter
			if (!isset($objectType) || $obj["objectType"] != $objectType) {
				$objectType = $obj["objectType"];
				if ( !$mid ) {
					$mid .= ' (`objectType` = ?';
				} else {
					$mid .= ' or `objectType` = ?';
				}
				$bindvars[] = $objectType;
				// reset comment detection and counter
				$comments = array();
				$thistype = 0;
			}
			// Just keep adding while objectType remain unchanged			
			$bindvars[] = $obj["object"];
			if ($obj["comment"]) {
				// i.e. this objectType filters by comments also, not just on object (id)
				$comments[] = $obj["comment"]; 
			}
			$thistype++;			
		}
		// compose rest of filter for last type
		$mid .= ' and `object` in ('.implode(',', array_fill(0, $thistype,'?')).')';
		// add comments filter if needed
		if ($comments) {
			$bindvars = array_merge($bindvars, $comments);
			$mid .= ' and `comment` in ('.implode(',', array_fill(0, count($comments),'?')).')';				
		}
		// add date filter
		if ($prefs['user_who_viewed_my_stuff_days']) {
			$firsttime = $this->now - 3600*24*$prefs['user_who_viewed_my_stuff_days'];
			$mid .= ") and `lastModif` > $firsttime";
		}
		if (!$anonymous) {
			$mid .= " and `user` != 'Anonymous'"; 
		}
		$mid .= " and `action` = 'Viewed'";
		$mid .= " and `user` IS NOT NULL"; // just to avoid those strange null entries
		$query = "select *, max(`lastModif`) as `lastViewed` from `tiki_actionlog` where $mid group by `user`, `object`, `objectType`, `comment` order by `lastViewed` desc";
		$ret = $this->fetchAll($query, $bindvars);
		$ret = $this->get_more_info($ret);
		return $ret;
	}
	
}

$logslib = new LogsLib;
