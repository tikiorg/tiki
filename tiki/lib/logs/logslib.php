<?php
// $Header: /cvsroot/tikiwiki/tiki/lib/logs/logslib.php,v 1.15 2005-12-19 17:27:20 sylvieg Exp $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class LogsLib extends TikiLib {
	
	function LogsLib($db) {
		$this->db = $db;
	}
	
	function add_log($type,$message,$who='',$ip='',$client='',$time='') {
		global $user;
		if (!$who) {
			if ($user) {
				$who = $user;
			} else {
				$who = 'Anonymous';
			}
		}
		if (!$ip) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		if (!$client) {
			$client = $_SERVER['HTTP_USER_AGENT'];
		}
		if (!$time) {
			$time = date("U");
		}
		$query = "insert into `tiki_logs` (`logtype`,`logmessage`,`loguser`,`logip`,`logclient`,`logtime`) values (?,?,?,?,?,?)";
		$result = $this->query($query,array($type,$message,$who,$ip,$client,(int)$time));
	}

	function list_logs($type='',$user='',$offset=0,$maxRecords=-1,$sort_mode='logtime_desc',$find='',$min=0,$max=0) {
		$bindvars = array();
		$amid = array();
		$mid = '';
		if ($find) {
			$findesc = '%'.$find.'%';
			$amid[] = "`logmessage` like ?";
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
		$query.= " from `tiki_logs` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_logs` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}
		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}
	function clean_logs($date) {
		$query = "delete from `tiki_logs` where `logtime`<=?";
		$this->query($query, array((int)$date));
	}

	/* action = "Updated", "Created", "Removed", "Viewed", "Removed version $version", "Changed actual version to $version"
	 * type = 'wiki page', 'category', 'article', 'image gallery', 'tracker', 'forum thread'
	*/
	function add_action($action, $object, $objectType='wiki page', $param='', $who='', $ip='', $client='', $date='') {
		global $user, $feature_categories;
		if ($objectType == 'wiki page' && $action != 'Viewed')
			$logObject = true; // to have the tiki_my_edit, history and mod-last_modif_pages
		else
			$logObject = $this->action_must_be_logged($action, $objectType);
		$logCateg = $feature_categories == 'y'? $this->action_must_be_logged('*', 'category'): false;
		if (!$logObject && !$logCateg)
			return false;
		if ($date == '')
			$date = date('U');
		if ($who == '')
			$who = $user;
		if ($ip == '')
			$ip = $_SERVER['REMOTE_ADDR'];;
		if ($client == '')
			$client = $_SERVER['HTTP_USER_AGENT'];
		if ($logCateg) {
			global $categlib; include_once('lib/categories/categlib.php');
			$categs = $categlib->get_object_categories($objectType, $object);
		}
		if ($logObject && !$logCateg) {
			$query = "insert into `tiki_actionlog` (`action`,`object`,`lastModif`,`user`,`ip`,`comment`, `objectType`) values(?,?,?,?,?,?,?)";
			$this->query($query, array($action, $object, (int)$date, $who, $ip, $param, $objectType));
		} elseif ($logObject) {
			if (sizeof($categs) > 0) {
				foreach ($categs as $categ) {
					$query = "insert into `tiki_actionlog` (`action`,`object`,`lastModif`,`user`,`ip`,`comment`, `objectType`, `categId`) values(?,?,?,?,?,?,?,?)";
					$this->query($query, array($action, $object, (int)$date, $who, $ip, $param, $objectType, $categ));
				}
			} else {
				$query = "insert into `tiki_actionlog` (`action`,`object`,`lastModif`,`user`,`ip`,`comment`, `objectType`) values(?,?,?,?,?,?,?)";
				$this->query($query, array($action, $object, (int)$date, $who, $ip, $param, $objectType));
			}
		}
		
		return  true;
	}
	function action_must_be_logged($action, $objectType) {
		global $feature_actionlog;
		if ($feature_actionlog != 'y')
			return true; // for previous compatibility - the new action are added with a if ($feature..)
		$logActions = $this->get_all_actionlog_conf();
		foreach ($logActions as $conf) {
			if ($conf['action'] == $action && $conf['objectType'] == $objectType && ($conf['status'] == 'y' || $conf['status'] == 'v'))
				return true;
		}
		return false;
	}
	function action_is_viewed($action, $objectType) {
		global $feature_actionlog;
		if ($feature_actionlog != 'y')
			return true; // for previous compatibility - the new action are added with a if ($feature..)
		$logActions = $this->get_all_actionlog_conf();
		foreach ($logActions as $conf) {
			if ($conf['action'] == $action && $conf['objectType'] == $objectType && $conf['status'] == 'v')
				return true;
		}
		return false;
	}
	function set_actionlog_conf($action, $objectType, $status) {
		global $actionlogConf;
		$this->delete_actionlog_conf($action, $objectType);
		$query = "insert into `tiki_actionlog_conf` (`action`, `objectType`, `status`) values(?, ?, ?)";
		$this->query($query, array($action, $objectType, $status));
		unset($actionlogConf);
	}
	function delete_actionlog_conf($action, $objectType) {
		$query = "delete from `tiki_actionlog_conf` where `action`=? and `objectType`= ?";
		$this->query($query, array($action, $objectType));
	}
	function get_all_actionlog_conf() {
		global $actionlogConf;
		if (!isset($actionlogConf)) {
			$actionlogConf = array();
			$query = "select * from `tiki_actionlog_conf` order by `objectType` desc, `action` asc";
			$result = $this->query($query, array());
			while ($res = $result->fetchRow()) {
				$res['code'] = $this->encode_actionlog_conf($res['action'], $res['objectType']);
				$actionlogConf[] = $res;
			}
		}
		return $actionlogConf;
	}
	function encode_actionlog_conf($action, $objectType) {
		return str_replace(' ', '0', $action.'_'.$objectType);
	}
	function decode_actionlog_conf($string) {
		return split('_', str_replace('0', ' ', $conf));
	}
	function list_actions($action='', $objectType='',$user='',$offset=0,$maxRecords=-1,$sort_mode='lastModif_desc',$find='',$start=0,$end=0, $categId='') {

		$bindvars = array();
		$amid = array();
		$mid = '';
		if ($find) {
			$findesc = '%'.$find.'%';
			$amid[] = "`comment` like ?";
			$bindvars[] = $findesc;
		}
		if ($action) {
			$amid[] = "`action` = ?";
			$bindvars[] = $action;
		}
		if ($objectType) {
			$amid[] = "`objectType` = ?";
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
				$amid[] = '`user` in ('.implode(',',array_fill(0,count($user),'?')).')';
				foreach ($user as $u)
					$bindvars[] = $u;
			} else {
				$amid[] = "`user` = ?";
				$bindvars[] = $user ;
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
		$amid[] = "a.`action` = c.`action` and a.`objectType` = c.`objectType` and (c.`status` = 'y' or c.`status` = 'v')";

		if (count($amid)) {
			$mid = " where ".implode(" and ",$amid)." ";
		}
		$query = "select a.* ";
		$query.= " from `tiki_actionlog` a ,`tiki_actionlog_conf` c $mid order by ".$this->convert_sortmode($sort_mode);
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$ret = array();
		while ($res = $result->fetchRow()) {
			if ($this->action_is_viewed($res['action'], $res['objectType']))
				$ret[] = $res;
		}
		return $ret;
	}
	function sort_by_date($action1, $action2) {
		return ($action1['lastModif'] -  $action2['lastModif']);
	}
	function get_login_time($logins, $startDate, $endDate, $actions) {
		if ($endDate > date('U'))
			$endDate = date('U');
		$logTimes = array();
		foreach ($logins as $login) {
			if (!array_key_exists($login['loguser'], $logTimes)) {
				if ($login['logmessage'] == 'timeout' || $login['logmessage'] == 'logged out')
					$logTimes[$login['loguser']]['last'] = $startDate;
				else
					$logTimes[$login['loguser']]['last'] = 0;
				$logTimes[$login['loguser']]['time'] = 0;
				$logTimes[$login['loguser']]['nbLogins'] = 0;
			}
			if (strstr($login['logmessage'], 'logged from') || $login['logmessage'] == 'back') {
				if (strstr($login['logmessage'], 'logged from'))
					++$logTimes[$login['loguser']]['nbLogins'];
				if ($logTimes[$login['loguser']]['last'] == 0) // can be already log in
					$logTimes[$login['loguser']]['last'] = $login['logtime'];
			} elseif (($login['logmessage'] == 'timeout' || $login['logmessage'] == 'logged out') && $logTimes[$login['loguser']]['last'] > 0) {
				$logTimes[$login['loguser']]['time'] += $login['logtime'] - $logTimes[$login['loguser']]['last'];
				$logTimes[$login['loguser']]['last'] = 0;
			}
		}
		foreach ($logTimes as $user=>$logTime) {// update time for those still logged in
			if ($logTime['last'])
				$logTimes[$user]['time'] += $endDate - $logTime['last'];
		}
		foreach ($actions as $action) { // update time for those who were always logged in
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
	function get_volume_action($action) {
		$bytes = array();
		if (preg_match('/bytes=([0-9\-+]+)/', $action['comment'], $matches)) {//old syntax
			if (preg_match('/\+([0-9]+)/', $matches[1], $m))
				$bytes['add'] = $m[1];
			if (preg_match('/\-([0-9]+)/', $matches[1], $m))
				$bytes['del'] = $m[1];
		} else {
			if (preg_match('/add=([0-9\-+]+)/', $action['comment'], $matches))
				$bytes['add'] = $matches[1];
			if (preg_match('/del=([0-9\-+]+)/', $action['comment'], $matches))
				$bytes['del'] = $matches[1];
		}
		return $bytes;
	}
	function get_action_stat_user($actions) {
		$stats = array();
		$actions2 = array();
		foreach ($actions as $action) {
			unset($action['categId']);
			if (!in_array($action, $actions2))
				$actions2[] = $action;
		}
		$actionlogConf = $this->get_all_actionlog_conf();
		foreach ($actions2 as $action) {
			$key = $action['user'];
			if (!array_key_exists($action['user'], $stats)) {
				$stats[$key]['user'] = $action['user'];
				foreach ($actionlogConf as $conf) {
					if ($conf['action'] != '*')// don't take category
						$stats[$key][$conf['action'].'/'.$conf['objectType']] = 0;
				}
			}
			++$stats[$key][$action['action'].'/'.$action['objectType']];
		}
		sort($stats); // will sort on the first field user
		return $stats;
	}
	function get_action_stat_categ($actions, $categNames) {
		$stats = array();
		$actionlogConf = $this->get_all_actionlog_conf();
		foreach ($actions as $action) {
			//if ($action['categId'] == 0) print also stat for non categ object
			//	continue;
			$key = $action['categId'];
			if (!array_key_exists($key, $stats)) {
				$stats[$key]['category'] = $key? $categNames[$key]: '';
				foreach ($actionlogConf as $conf) {
					if ($conf['action'] != '*')// don't take category
						$stats[$key][$conf['action'].'/'.$conf['objectType']] = 0;
				}
			}
			++$stats[$key][$action['action'].'/'.$action['objectType']];
		}
		sort($stats); //sort on the first field category
		return $stats;
	}
	function get_action_vol_categ($actions, $categNames) {
		$stats = array();
		$actionlogConf = $this->get_all_actionlog_conf();
		foreach ($actions as $action) {
			//if ($action['categId'] == 0) print also stat for non categ object
			//	continue;
			if (!($bytes = $this->get_volume_action($action)))
				continue;
			$key = $action['categId'];
			if (!array_key_exists($key, $stats))
				$stats[$key]['category'] = $key? $categNames[$key]: '';
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
	function get_action_vol_user_categ($actions, $categNames) {
		$stats = array();
		$actionlogConf = $this->get_all_actionlog_conf();
		foreach ($actions as $action) {
			//if ($action['categId'] == 0) print also stat for non categ object
			//	continue;
			if ($action['user'] == '' || !($bytes = $this->get_volume_action($action)))
				continue;
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
	function get_action_vol_type($vols) {
		$types = array();
		foreach ($vols as $vol) {
			foreach ($vol as $key=>$value) {
				if ($key != 'category' && $key != 'user' && !in_array($key, $types))
					$types[] = $key;
			}
		}
		return $types;
	}
	function get_action_stat_user_categ($actions, $categNames) {
		$stats = array();
		$actionlogConf = $this->get_all_actionlog_conf();
		foreach ($actions as $action) {
			if ($action['categId'] == 0)
				continue;
			$key = $action['categId'].'/'.$action['user'];;
			if (!array_key_exists($key, $stats)) {
				$stats[$key]['category'] = $categNames[$action['categId']];
				$stats[$key]['user'] = $action['user'];
				foreach ($actionlogConf as $conf) {
					if ($conf['action'] != '*')// don't take category
						$stats[$key][$conf['action'].'/'.$conf['objectType']] = 0;
				}
			}
			++$stats[$key][$action['action'].'/'.$action['objectType']];
		}
		sort($stats); // sort on the first fields categ , then user
		return $stats;
	}
	function in_kb($vol) {
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

}
global $dbTiki;
$logslib = new LogsLib($dbTiki);

?>
