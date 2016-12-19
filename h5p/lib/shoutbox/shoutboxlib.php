<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

require_once 'lib/socialnetworkslib.php';

class ShoutboxLib extends TikiLib
{
	public function list_shoutbox($offset, $maxRecords, $sort_mode, $find)
	{
		global $prefs;
		$parserlib = TikiLib::lib('parser');

		if ($find) {
			$mid = " where (`message` like ?)";
			$bindvars = array('%'.$find.'%');
		} else {
			$mid = "";
			$bindvars = array();
		}

		$query = "select * from `tiki_shoutbox` $mid order by ".$this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_shoutbox` $mid";
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			if (!$res["user"]) {
				$res["user"] = tra('Anonymous');
			}
			// convert ampersands and other stuff to xhtml compliant entities
			$res["message"] = htmlspecialchars($res["message"]);

			if ($prefs['shoutbox_autolink'] == 'y') {
				// we replace urls starting with http(s)|ftp(s) to active links
				$res["message"] = preg_replace("/((http|ftp)+(s)?:\/\/[^<>\s]+)/i", "<a href=\"\\0\">\\0</a>", $res["message"]);
				// we replace also urls starting with www. only to active links
				$res["message"] = preg_replace("/(?<!http|ftp)(?<!s)(?<!:\/\/)(www\.[^ )\s\r\n]+)/i", "<a href=\"http://\\0\">\\0</a>", $res["message"]);
				// we replace also urls longer than 30 chars with translantable string as link description instead the URL itself to prevent breaking the layout in some browsers (e.g. Konqueror)
				$res["message"] = preg_replace("/(<a href=\")((http|ftp)+(s)?:\/\/[^\"]+)(\">)([^<]){30,}<\/a>/i", "<a href=\"\\2\">[".tra('Link')."]</a>", $res["message"]);
			}

			// if not in html tag (e.g. autolink), place after every '*;' the empty span too to prevent e.g. '&amp;&amp;...'
			//$res["message"] = preg_replace('/(\s*)([^>]+)(<|$)/e', "'\\1'.str_replace(';', ';<span></span>','\\2').'\\3'", $res["message"]);
			$res["message"] = preg_replace_callback('/(\s*)([^>]+)(<|$)/', function($mat) {
				return $mat[1].str_replace(';', ';<span></span>',$mat[2]).$mat[3];
			}, $res["message"]);
			// if not in tag or on a space or doesn't contain a html entity we split all plain text strings longer than 25 chars using the empty span tag again
			$wrap_at = 25;
			// $res["message"] = preg_replace('e', "'\\1'.wordwrap('\\2', '".$wrap_at."', '<span></span>', 1).'\\3'", $res["message"]);
			$res["message"] = preg_replace_callback('/(\s*)([^\;>\s]{'.$wrap_at.',})([^&]<|$)/', function($m, $wrap_at) {
				return $m[1].wordwrap($m[2], $wrap_at, '<span></span>', 1).$m[3];
			}, $res["message"]);
			// emoticons support
			$res["message"] = $parserlib->parse_smileys($res["message"]);
			$ret[] = $res;
		}
		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	public function tweet($message, $user, $msgId)
	{
		global $prefs, $socialnetworkslib;

		$id=$socialnetworkslib->tweet($message, $user);
		if ($id>0) {
		    $query="update `tiki_shoutbox` set `tweetId`=? where `user`=? and `msgId`=?";
		    $bindvars=array($id,$user,$msgId);
		    $this->query($query, $bindvars);
		}
		return $id;
	}

	public function replace_shoutbox($msgId, $user, $message, $tweet=false, $facebook=false)
	{
		$message = strip_tags($message);

		// Check Message for containing bad/banned words
		$words = $this->get_bad_words();
		$badmsg = false;
		foreach ($words["data"] as $word) {
			if (preg_match("/".$word["word"]."/i", $message)) {
				$badmsg = true;
				break;
			}
		}

		//Die if badmsg with suitable error screen
		if ($badmsg) {
			return false;
		}

		// Back on track for normal shoutbox posting

		$hash = md5($message);  // this checks for the same message already existing
		$cant = $this->getOne("select count(*) from `tiki_shoutbox` where `hash`=? and `user`=?", array($hash,$user));

		if ($cant) {
			// at least update  the timestamp - can be convenient if message is thanks or hello - we can see the last post
			$query = "update `tiki_shoutbox` set `timestamp`=? where `user`=? and `hash`=?";
			$bindvars = array((int) $this->now, $user, $hash);
		} else if ($msgId) {
			$query = "update `tiki_shoutbox` set `user`=?, `message`=?, `hash`=? where `msgId`=?";
			$bindvars = array($user,$message,$hash,(int) $msgId);
		} else {
			$query = "delete from `tiki_shoutbox` where `user`=? and `timestamp`=? and `hash`=?";
			$bindvars = array($user,(int) $this->now,$hash);
			$this->query($query, $bindvars);
			$query = "insert into `tiki_shoutbox`(`message`,`user`,`timestamp`,`hash`) values(?,?,?,?)";
			$bindvars = array($message,$user,(int) $this->now,$hash);
		}

		$result = $this->query($query, $bindvars);
		if ($tweet) {
			$msgId=$this->lastInsertId();
			$this->tweet($message, $user, $msgId);
		}
		if ($facebook) {
			global $socialnetworkslib; require_once('lib/socialnetworkslib.php');
			$fbreply=$socialnetworkslib->facebookWallPublish($user, $message);
		}
		return true;
	}

	public function remove_shoutbox($msgId)
	{
		global $socialnetworkslib, $user;
		$tweetId=$this->getOne("select `tweetId` from `tiki_shoutbox` where `msgId`=?", array($msgId));
		if ($tweetId>0) {
			$socialnetworkslib->destroyTweet($tweetId, $user);
		}
		$query = "delete from `tiki_shoutbox` where `msgId`=?";
		$result = $this->query($query, array((int) $msgId));
		return true;
	}

	public function get_shoutbox($msgId)
	{
		$query = "select * from `tiki_shoutbox` where `msgId`=?";
		$result = $this->query($query, array((int) $msgId));
		if (!$result->numRows()) {
			return false;
		}
		$res = $result->fetchRow();
		return $res;
	}

	public function get_bad_words($offset = 0, $maxRecords = -1, $sort_mode = 'word_asc', $find = '')
	{
		if ($find) {
			$findesc = "%$find%";
			$mid = " where `word` like ?";
			$bindvars = array($findesc);
		} else {
			$mid = '';
			$bindvars = array();
		}

		$query = "select * from `tiki_shoutbox_words` $mid order by ".$this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_shoutbox_words` $mid";
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
		        $ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	public function add_bad_word($word)
	{
		$word = addslashes($word);

		$query = "delete from `tiki_shoutbox_words` where `word`=?";
		$result = $this->query($query, array($word));
		$query = "insert into `tiki_shoutbox_words` (`word`) values(?)";
		$result = $this->query($query, array($word));
		return true;
	}

	public function remove_bad_word($word)
	{
		$query = "delete from `tiki_shoutbox_words` where `word`=?";
		$result = $this->query($query, array($word));
	}
}
$shoutboxlib = new ShoutboxLib;
