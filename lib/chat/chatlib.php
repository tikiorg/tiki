<?php

class ChatLib extends TikiLib {
	function ChatLib($db) {
		# this is probably uneeded now
		if (!$db) {
			die ("Invalid db object passed to ChatLib constructor");
		}
		$this->db = $db;
	}

	function send_message($user, $channelId, $data) {
		$now = date("U");
		$info = $this->get_channel($channelId);
		$name = $info["name"];
		// Check if the user is registered in the channel or update the user timestamp
		$query = "delete from `tiki_chat_users` where `nickname`=? and `channelId`=?";
		$result = $this->query($query,array($user,(int)$channelId),-1,-1,false);
		$query = "insert into `tiki_chat_users`(`nickname`,`channelId`,`timestamp`) values(?,?,?)";
		$result = $this->query($query,array($user,(int)$channelId,(int)$now));

		// :TODO: If logging is used then log the message
		//$log = fopen("logs/${name}.txt","a");
		//fwrite($log,"$posterName: $data\n");
		//fclose($log);
		$query = "insert into `tiki_chat_messages`(`channelId`,`poster`,`timestamp`,`data`) values(?,?,?,?)";
		$result = $this->query($query,array((int)$channelId,$user,(int)$now,$data));
		return true;
	}

	function get_channel($channelId) {
		$query = "select * from `tiki_chat_channels` where `channelId`=?";
		$result = $this->query($query,array((int)$channelId));
		$res = $result->fetchRow();
		return $res;
	}

	function send_private_message($user, $toNickname, $data) {
		$now = date("U");
		// :TODO: If logging is used then log the message
		//$log = fopen("logs/${name}.txt","a");
		//fwrite($log,"$posterName: $data\n");
		//fclose($log);
		$query = "insert into `tiki_private_messages`(`poster`,`timestamp`,`data`,`toNickname`) values(?,?,?,?)";
		$result = $this->query($query,array($user,(int)$now,$data,$toNickname));
		return true;
	}

	function user_to_channel($user, $channelId) {
		$now = date("U");
		$query = "delete from `tiki_chat_users` where `nickname`=?";
		$result = $this->query($query,array($user));
		$query = "insert into `tiki_chat_users`(`nickname`,`channelId`,`timestamp`) values(?,?,?)";
		$result = $this->query($query,array($user,(int)$channelId,(int)$now));
	}

	function get_chat_users($channelId) {
		$now = date("U") - (5 * 60);
		$query = "delete from `tiki_chat_users` where `timestamp` < ?";
		$result = $this->query($query,array((int)$now));
		$query = "select `nickname` from `tiki_chat_users` where `channelId`=?";
		$result = $this->query($query,array((int)$channelId));
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}
		return $ret;
	}

	function get_messages($channelId, $last, $from) {
		$query = "select `messageId`, `poster`, `data` from `tiki_chat_messages` ";
		$query.= " where `timestamp`>? and `channelId`=? and `messageId`>? order by ".$this->convert_sortmode("timestamp_asc");
		$result = $this->query($query,array((int)$from,(int)$channelId,(int)$last));
		$ret = array();

		while ($res = $result->fetchRow()) {
			$aux = array();
			$aux["poster"] = $res["poster"];
			$aux["posterName"] = $res["poster"];
			$aux["data"] = $res["data"];
			$aux["messageId"] = $res["messageId"];
			$ret[] = $aux;
		}

		$num = count($ret);
		return $ret;
	}

	function get_private_messages($user) {
		$query = "select `messageId`, `poster`, `data` from `tiki_private_messages` where `toNickname`=? order by ".$this->convert_sortmode("timestamp_asc");
		$result = $this->query($query,array($user));
		$ret = array();
		while ($res = $result->fetchRow()) {
			$aux = array();
			$aux["poster"] = $res["poster"];
			$aux["posterName"] = $res["poster"];
			$aux["data"] = $res["data"];
			$aux["messageId"] = $res["messageId"];
			$ret[] = $aux;
		}
		$query = "delete from `tiki_private_messages` where `toNickname`=?";
		$result = $this->query($query,array($user));
		$num = count($ret);
		return $ret;
	}

	function purge_messages($minutes) {
		// :TODO: pass old messages to the message log table
		$secs = $minutes * 60;
		$last = date("U") - $secs;
		$query = "delete from `tiki_chat_messages` where `timestamp`<?";
		$result = $this->query($query,array((int)$last));
		// :TODO: delete from modMessages y privateMessages
		return true;
	}

	function list_channels($offset, $maxRecords, $sort_mode, $find) {
		$bindvars = array();
		if ($find) {
			$findesc = '%' . $find . '%';
			$mid = " where (`name` like ? or `description` like ?)";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		} else {
			$mid = "";
		}

		$query = "select * from `tiki_chat_channels` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_chat_channels` $mid";
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

	function list_active_channels($offset, $maxRecords, $sort_mode, $find) {
		$bindvars = array('y');
		if ($find) {
			$findesc = '%' . $find . '%';
			$mid = " where `active`=? and (`name` like ? or `description` like ?)";
		} else {
			$mid = " where `active`=? ";
		}
		$query = "select * from `tiki_chat_channels` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_chat_channels` $mid";
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

	function replace_channel($channelId, $name, $description, $max_users, $mode, $active, $refresh) {
		$bindvars = array($name,$description,(int)$refresh,(int)$max_users,$mode,$active);
		if ($channelId) {
			$query = "update `tiki_chat_channels` set `name`=?, `description`=?, `refresh`=?, `max_users`=?, `mode`=?, `active`=? where `channelId`=?";
			$bindvars[] = (int) $channelId;
			$result = $this->query($query,$bindvars);
		} else {
			$query = "delete from `tiki_chat_channels` where `name`=?";
			$this->query($query,array($name),-1,-1,false);
			$query = "insert into `tiki_chat_channels`(`name`,`description`,`refresh`,`max_users`,`mode`,`active`) values(?,?,?,?,?,?)";
			$result = $this->query($query,$bindvars);
		}
		return true;
	}

	function remove_channel($channelId) {
		$query = "delete from `tiki_chat_channels` where `channelId`=?";
		$result = $this->query($query,array((int)$channelId));
		return true;
	}
}

$chatlib = new ChatLib($dbTiki);

?>
