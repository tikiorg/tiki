<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class ChartLib extends TikiLib {
	function ChartLib($db) {
		$this->TikiLib($db);
	}

	function add_chart_hit($chartId) {
		global $prefs, $user;

		if ($prefs['count_admin_pvs'] == 'y' || $user != 'admin') {
			$query = "update `tiki_charts` set `hits`=`hits`+1 where `chartId`=?";

			$this->query($query,array((int) $chartId));
		}
	}

	function clear_chart_votes($chartId) {
		$query = "update `tiki_chart_items` set `votes`=0, `average`=0, `points`=0 where `chartId`=$chartId";

		$this->query($query,array((int) $chartId));
	}

	function remove_chart_rankings($chartId) {
		$query = "delete from `tiki_charts_rankings` where `chartId`=?";

		$this->query($query,array((int) $chartId));
	}

	function user_vote($user, $itemId, $points = 0) {
		$chartId = $this->getOne("select `chartId` from `tiki_chart_items` where `itemId`=?",array((int) $itemId));

		// Register that the user has voted the item
		if ($user) {
			$query = "delete from `tiki_charts_votes`where `user`=? and `itemId`=?";
			$bindvars=array($user,(int) $itemId);
			$this->query($query,$bindvars,-1,-1,false);
			$query = "insert into `tiki_charts_votes`(`user`,`itemId`,`timestamp`,`chartId`)
    	values(?,?,?,?)";
			$bindvars[]=(int) $this->now;
			$bindvars[]=(int) $chartId;

			$this->query($query,$bindvars);
		} else {
			$_SESSION['chart_votes'][] = $chartId;

			$_SESSION['chart_item_votes'][] = $itemId;
		}

		// Update points and votes for the item
		$query = "update `tiki_chart_items` set `points`=`points`+? , `votes`=`votes`+1 where `itemId`=?";
		$this->query($query,array((int) $points,(int) $itemId));

		// Calculate average note that is the maxVoteValue is one average is the number of votes!
		if ($this->getOne("select `maxVoteValue` from `tiki_charts` where `chartId`=?",array((int) $chartId)) == 1) {
			$query = "update `tiki_chart_items` set `average`=`votes` where `itemId`=?";

			$this->query($query,array((int) $itemId));
		} else {
			$query = "update `tiki_chart_items` set `average`=`points`/`votes` where `itemId`=?";

			$this->query($query,array((int) $itemId));
		}
	}

	function ranking_exists($chartId) {
		return $this->getOne("select count(*) from `tiki_charts_rankings` where `chartId`=?",array((int) $chartId));
	}

	function generate_new_ranking($chartId) {

		$info = $this->get_chart($chartId);

		if ($info['frequency'] == 0)
			$this->drop_rankings($chartId);

		if ($info['lastChart'] + $info['frequency'] < $this->now) {
			$maxPeriod = $this->get_last_period($chartId);

			$newPeriod = $maxPeriod + 1;
			// Now just loop the items table and get the topN
			$topN = $info['topN'];
                        $query = "select * from `tiki_chart_items` where `chartId`=? order by `average` desc";
                        $result = $this->query($query,array((int) $chartId),$topN);
			$position = 1;

			while ($res = $result->fetchRow()) {
				$itemId = $res['itemId'];

				if ($maxPeriod) {
					$lastPosition
						= $this->getOne("select `position` from `tiki_charts_rankings` where `itemId`=? and `chartId`=? and `period`=?",array((int) $itemId,(int) $chartId, $maxPeriod));
				} else {
					$lastPosition = 0;
				}

				$rvotes = $res['votes'];
				$raverage = $res['average'];
				$query2 = "insert into `tiki_charts_rankings`(`chartId`,`itemId`,`position`,`lastPosition`,`period`,`timestamp`,`rvotes`,`raverage`)
	      values(?,?,?,?,?,?,?,?)";
				$this->query($query2,array((int) $chartId,(int) $itemId,(int) $position,(int) $lastPosition,$newPeriod,(int) $this->now,$rvotes,$raverage));
				$position++;
			}

			$query = "update `tiki_charts` set `lastChart`=? where `chartId`=?";
			$this->query($query,array((int) $this->now,(int) $chartId));
			$info = $this->get_chart($chartId);
		}
	}

	function drop_rankings($chartId) {
		$query = "delete from `tiki_charts_rankings` where `chartId`=?";

		$this->query($query,array((int) $chartId));
	}

	function get_ranking($chartId, $period) {
		global $user;

		$query = "select tcr.`timestamp`,tcr.`rvotes`,tcr.`raverage`,tci.`itemId`,tci.`title`,tci.`URL`,
			tci.`votes`,tci.`points`,tci.`average`,tcr.`position`,tcr.`lastPosition` 
			from `tiki_charts_rankings` tcr,`tiki_chart_items` tci 
			where tcr.`itemId` = tci.`itemId` and tcr.`chartId`=? and `period`=? order by `position` asc";
		$result = $this->query($query,array((int) $chartId,(int) $period));
		$ret = array();

		while ($res = $result->fetchRow()) {
			if ($res['lastPosition'] != 0) {
				$res['dif'] = $res['lastPosition'] - $res['position'];

				if ($res['dif'] == 0)
					$res['dif'] = '-';
			} else {
				$res['dif'] = 'new';
			}

			if ($this->user_has_voted_item($user, $res['itemId'])) {
				$res['voted'] = 'y';
			} else {
				$res['voted'] = 'n';
			}

			$res['perm'] = $this->getOne("select count(*) from `tiki_charts_rankings` where `itemId`=?",array((int) $res['itemId']));
			$ret[] = $res;
		}

		return $ret;
	}

	function max_dif($chartId, $period) {
		return
			$this->getOne("select max(`lastPosition`-`position`) from `tiki_charts_rankings` where `chartId`=? and `period`=?",array((int) $chartId,(int) $period));
	}

	function purge_user_votes($chartId, $again) {
		$query = "delete from `tiki_charts_votes` where `timestamp` + ? < ?";
		$this->query($query,array((int) $again,(int) $this->now));
	}

	function user_has_voted_chart($user, $chartId) {
		if ($user) {
			return $this->getOne("select count(*) from `tiki_charts_votes` where `user`=? and `chartId`=?",array($user,(int) $chartId));
		} else {
			return isset($_SESSION['chart_votes']) && in_array($chartId, $_SESSION['chart_votes']);
		}
	}

	function user_has_voted_item($user, $itemId) {
		if ($user) {
			return $this->getOne("select count(*) from `tiki_charts_votes` where `user`=? and `itemId`=?",array($user,(int) $itemId));
		} else {
			return isset($_SESSION['chart_item_votes']) && in_array($itemId, $_SESSION['chart_item_votes']);
		}
	}

	function get_last_period($chartId) {
		if ($this->ranking_exists($chartId)) {
			$maxPeriod = $this->getOne("select max(`period`) from `tiki_charts_rankings` where `chartId`=?",array((int) $chartId));
		} else {
			$maxPeriod = 0;
		}

		return $maxPeriod;
	}

	function get_first_period($chartId) {
		if ($this->ranking_exists($chartId)) {
			$maxPeriod = $this->getOne("select min(`period`) from `tiki_charts_rankings` where `chartId`=?",array($chartId));
		} else {
			$maxPeriod = 0;
		}

		return $maxPeriod;
	}

	function get_chart($chartId) {
		$query = "select * from `tiki_charts` where `chartId`=?";

		$result = $this->query($query,array((int) $chartId));
		$res = $result->fetchRow();
		return $res;
	}

	function get_chart_item($itemId) {
		$query = "select * from `tiki_chart_items` where `itemId`=?";

		$result = $this->query($query,array((int) $itemId));
		$res = $result->fetchRow();
		$period = $this->get_last_period($res['chartId']);

		if ($period) {
			// Permanency
			$res['perm'] = $this->getOne("select count(*) from `tiki_charts_rankings` where `itemId`=?",array((int) $itemId));

			// Current position
			$res['position'] = $this->getOne("select `position` from `tiki_charts_rankings` where `itemId`=? and `period`=?",array((int) $itemId,$period));
			// Last position
			$res['lastPosition']
				= $this->getOne("select `lastPosition` from `tiki_charts_rankings` where `itemId`=? and `period`=?",array((int) $itemId,$period));
			// Best position
			$res['best'] = $this->getOne("select min(`position`) from `tiki_charts_rankings` where `itemId`=?",array((int) $itemId));
			$res['bestdate']
				= $this->getOne("select `timestamp` from `tiki_charts_rankings` where `itemId`=? and `position`=?",array((int) $itemId,$res['best']));

			if ($res['lastPosition'] != 0) {
				$res['dif'] = $res['position'] - $res['position'];

				if ($res['dif'] == 0)
					$res['dif'] = '-';
			} else {
				$res['dif'] = 'new';
			}
		// Dif
		} else {
			$res['perm'] = 0;

			$res['position'] = 0;
			$res['lastPosition'] = 0;
			$res['best'] = 0;
			$res['dif'] = 0;
		}

		return $res;
	}

	function replace_chart($chartId, $vars) {
		$TABLE_NAME = 'tiki_charts';

		$vars['created'] = $this->now;

		foreach ($vars as $key => $value) {
			$vars[$key] = $value;
		}

		unset ($vars['hits']);

		if ($chartId) {
			// update mode
			$first = true;

			$query = "update `$TABLE_NAME` set";
			$bindvars=array();

			foreach ($vars as $key => $value) {
				if (!$first)
					$query .= ',';

				if (is_numeric($value))
					$value = (int) $value;

				$bindvars[]=$value;

				$query .= " `$key`=? ";
				$first = false;
			}

			$bindvars[]=(int) $chartId;

			$query .= " where `chartId`=?";
			$this->query($query,$bindvars);
		} else {
			unset ($vars['chartId']);

			$vars['hits'] = 0;
			// insert mode
			$first = true;
			$query = "insert into `$TABLE_NAME`(";

			foreach (array_keys($vars)as $key) {
				if (!$first)
					$query .= ',';

				$query .= "`$key`";
				$first = false;
			}

			$bindvars=array();
			$query .= ") values(";
			$first = true;

			foreach (array_values($vars)as $value) {
				if (!$first)
					$query .= ',';

				if (is_numeric($value))
					$value = (int) $value;

				$query .= "?";
				$bindvars[]=$value;
				$first = false;
			}

			$query .= ")";
			$this->query($query,$bindvars);
			$chartId = $this->getOne("select max(`chartId`) from `$TABLE_NAME` where `created`=?",array((int) $this->now));
		}

		// Get the id
		return $chartId;
	}

	function replace_chart_item($itemId, $vars) {
		$TABLE_NAME = 'tiki_chart_items';

		$vars['created'] = $this->now;

		if (!isset($vars['votes']))
			$vars['votes'] = 0;

		if (!isset($vars['points']))
			$vars['points'] = 0;

		$vars['average'] = $vars['votes'] ? $vars['points'] / $vars['votes'] : 0;

		foreach ($vars as $key => $value) {
			$vars[$key] = $value;
		}

		if ($itemId) {
			// update mode
			$first = true;

			$query = "update `$TABLE_NAME` set";
			$bindvars=array();
			foreach ($vars as $key => $value) {
				if (!$first)
					$query .= ',';

				if (is_numeric($value))
					$value = (int) $value;

				$query .= " `$key`=?";
				$bindvars[]=$value;
				$first = false;
			}

			$bindvars[]=(int) $itemId;
			$query .= " where `itemId`=?";
			$this->query($query,$bindvars);
		} else {
			unset ($vars['itemId']);

			// insert mode
			$first = true;
			$query = "insert into `$TABLE_NAME`(";

			foreach (array_keys($vars)as $key) {
				if (!$first)
					$query .= ',';

				$query .= "`$key`";
				$first = false;
			}

			$query .= ") values(";
			$first = true;
			$bindvars=array();

			foreach (array_values($vars)as $value) {
				if (!$first)
					$query .= ',';

				if (is_numeric($value))
					$value = (int) $value;

				$query .= "?";
				$bindvars[]=$value;
				$first = false;
			}

			$query .= ")";
			$this->query($query,$bindvars);
			$itemId = $this->getOne("select max(`itemId`) from `$TABLE_NAME` where `created`=?",array((int) $this->now));
		}

		// Get the id
		return $itemId;
	}

	function remove_chart($chartId) {
		$query = "delete from `tiki_charts` where `chartId`=?";

		$this->query($query,array((int) $chartId));
		$this->remove_chart_rankings($chartId);
		$query = "delete from `tiki_chart_items` where `chartId`=?";
		$this->query($query,array((int) $chartId));
	}

	function remove_chart_item($itemId) {
		$query = "delete from `tiki_chart_items` where `itemId`=?";

		$this->query($query,array((int) $itemId));
	}

	function list_charts($offset, $maxRecords, $sort_mode, $find, $where = '') {

		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where ((`title` like ?) or (`description` like ?))";
			$bindvars=array($findesc,$findesc);
		} else {
			$mid = "";
			$bindvars=array();
		}

		if ($where) {
			if ($mid) {
				$mid .= " and ($where) ";
			} else {
				$mid = "where ($where) ";
			}
		}

		$query = "select * from `tiki_charts` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_charts` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$res['items'] = $this->getOne("select count(*) from `tiki_chart_items` where `chartId`=?",array((int) $res['chartId']));

			$query2 = "select distinct(`period`) from `tiki_charts_rankings` where `chartId`=?";
			$result2 = $this->query($query2,array((int) $res['chartId']));
			$res['periods'] = $result2->numRows();
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function list_chart_items($offset, $maxRecords, $sort_mode, $find, $where = '',$whereval=0) {

		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where ((`title` like ?) or (`description` like ?))";
			$bindvars=array($findesc,$findesc);
		} else {
			$mid = "";
			$bindvars=array();
		}

		if ($where) {
			$bindvars[]=$whereval;
			if ($mid) {
				$mid .= " and (`$where` = ?) ";
			} else {
				$mid = "where (`$where` = ?) ";
			}
		}

		$query = "select * from `tiki_chart_items` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_chart_items` $mid";
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
}
global $dbTiki;
$chartlib = new ChartLib($dbTiki);

?>
