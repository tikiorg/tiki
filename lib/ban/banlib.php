<?php

class BanLib extends TikiLib {
	function BanLib($db) {
		# this is probably uneeded now
		if (!$db) {
			die ("Invalid db object passed to BanLib constructor");
		}

		$this->db = $db;
	}

	function get_rule($banId) {
		$query = "select * from tiki_banning where banId=$banId";

		$result = $this->query($query);
		$res = $result->fetchRow(DB_FETCHMODE_ASSOC);
		$aux = array();
		$query2 = "select section from tiki_banning_sections where banId=$banId";
		$result2 = $this->query($query2);
		$aux = array();

		while ($res2 = $result2->fetchRow(DB_FETCHMODE_ASSOC)) {
			$aux[] = $res2['section'];
		}

		$res['sections'] = $aux;
		return $res;
	}

	function remove_rule($banId) {
		$query = "delete from tiki_banning where banId=$banId";

		$this->query($query);
		$query = "delete from tiki_banning_sections where banId=$banId";
		$this->query($query);
	}

	function list_rules($offset, $maxRecords, $sort_mode, $find, $where = '') {
		$sort_mode = str_replace("_", " ", $sort_mode);

		if ($find) {
			$findesc = $this->qstr('%' . $find . '%');

			$mid = " where ((message like $findesc) or (title like $findesc))";
		} else {
			$mid = "";
		}

		if ($where) {
			if ($mid) {
				$mid .= " and ($where) ";
			} else {
				$mid = "where ($where) ";
			}
		}

		$query = "select * from tiki_banning $mid order by $sort_mode limit $offset,$maxRecords";
		$query_cant = "select count(*) from tiki_banning $mid";
		$result = $this->query($query);
		$cant = $this->getOne($query_cant);
		$ret = array();

		while ($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
			$aux = array();

			$query2 = "select * from tiki_banning_sections where banId=" . $res['banId'];
			$result2 = $this->query($query2);

			while ($res2 = $result2->fetchRow(DB_FETCHMODE_ASSOC)) {
				$aux[] = $res2;
			}

			$res['sections'] = $aux;
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		$now = date("U");
		$query = "select banId from tiki_banning where use_dates='y' and date_to < $now";
		$result = $this->query($query);

		while ($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
			$this->remove_rule($res['banId']);
		}

		return $retval;
	}

	/*
	banId integer(12) not null auto_increment,
	  mode enum('user','ip'),
	  title varchar(200),
	  ip1 integer(3),
	  ip2 integer(3),
	  ip3 integer(3),
	  ip4 integer(3),
	  user varchar(200),
	  date_from timestamp,
	  date_to timestamp,
	  use_dates char(1),
	  message text,
	  primary key(banId)
	  */
	function replace_rule($banId, $mode, $title, $ip1, $ip2, $ip3, $ip4, $user, $date_from, $date_to, $use_dates, $message,
		$sections) {
		$message = addslashes($message);

		$title = addslashes($title);
		$user = addslashes($user);

		if ($banId) {
			$query = " update tiki_banning set
  			title='$title',
  			ip1='$ip1',
  			ip2='$ip2',
  			ip3='$ip3',
  			ip4='$ip4',
  			user='$user',
  			date_from = $date_from,
  			date_to = $date_to,
  			user_dates = '$use_dates',
  			message = '$message'
  			where banId=$banId	
  		";

			$this->query($query);
		} else {
			$now = date("U");

			$query = "insert into tiki_banning(mode,title,ip1,ip2,ip3,ip4,user,date_from,date_to,use_dates,message,created)
		values('$mode','$title','$ip1','$ip2','$ip3','$ip4','$user',$date_from,$date_to,'$use_dates','$message',$now)";
			$this->query($query);
			$banId = $this->getOne("select max(banId) from tiki_banning where created=$now");
		}

		$query = "delete from tiki_banning_sections where banId=$banId";
		$this->query($query);

		foreach ($sections as $section) {
			$query = "insert into tiki_banning_sections(banId,section) values($banId,'$section')";

			$this->query($query);
		}
	}
}

$banlib = new BanLib($dbTiki);

?>