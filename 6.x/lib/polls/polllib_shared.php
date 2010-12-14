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

class PollLibShared extends TikiLib
{

	function get_poll($pollId) {
		$query = "select * from `tiki_polls` where `pollId`=?";
		$result = $this->query($query,array((int)$pollId));
		if (!$result->numRows()) return false;
		$res = $result->fetchRow();
		return $res;
	}

	function get_poll_voters( $optionId ) {
		$query = "select user from `tiki_user_votings` where `optionId`=?";
		$result = $this->query($query,array((int)$optionId));
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}
		return $ret;
	}

	function list_poll_options($pollId, $from=0, $to=0) {
		if ( empty($from) && empty($to) ) {
			$query = 'select * from `tiki_poll_options` where `pollId`=?';
			$bindVars = array((int) $pollId);
		} else {
			$query = 'select tpo.`pollId`, tpo.`optionId`, tpo.`title`, tpo.`position`, count(tuv.`id`) as votes from `tiki_poll_options` tpo
				left join `tiki_user_votings` tuv on (tpo.`optionId` = tuv.`optionId`)
				where `pollId`=? and ((tuv.`time` >= ? and tuv.`time` <= ?) or tuv.`time` = ?)
				group by `votes`';
			$bindVars = array((int)$pollId, (int)$from, (int)$to, 0);
		}
		$query .= ' order by `position`';
		$result = $this->query($query, $bindVars);
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}
		return $ret;
	}

  function get_random_poll($active="a") {
	$bindvars = array((int)$this->now, $active);
	if ($active == "a") {
		$bindvars[] = "c"; // current;
		$mid = "or `active`=?";
	}
	$result = $this->query("select `pollId` from `tiki_polls` where `publishDate`<=? and (`active`=? $mid) ",$bindvars);
	$ret = array();
	while ($res = $result->fetchRow()) {
		$ret[] = $res;
	}
	if (count($res)== 0)
		return 0;
	elseif (count($ret) == 1)
		return $ret[0]['pollId'];
	else {
		$bid = rand(0, count($ret) - 1);
		return $ret[$bid]['pollId'];
	}
  }

  function get_polls($type='a',$datestart=0,$dateend='',$find='') {
		if (!$dateend) $dateend = date('U');
		$bindvars = array($type,(int)$datestart,(int)$dateend);
		if ($find) {
			$mid = 'and `title`=?';
			$bindvars[] = '%'. $find .'%';
		} else {
			$mid = '';
		}
		$query = "select * from `tiki_polls` where `active`=? and `publishDate`>=? and `publishDate`<=? $mid";
		$query_cant = "select count(*) from `tiki_polls` where `active`=? and `publishDate`>=? and `publishDate`<=? $mid";
		$result = $this->query($query,$bindvars);
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

  function poll_vote($user, $pollId, $optionId, $previous_vote) {
		if (!$previous_vote || $previous_vote == 0) {
			$query = "update `tiki_polls` set `votes`=`votes`+1 where `pollId`=?";
			$result = $this->query($query,array((int)$pollId));
			$query = "update `tiki_poll_options` set `votes`=`votes`+1 where `optionId`=?";
			$result = $this->query($query,array((int)$optionId));
		} elseif ($previous_vote != $optionId) {
			$query = "update `tiki_poll_options` set `votes`=`votes`-1 where `optionId`=?";
			$result = $this->query($query,array((int)$previous_vote));
			$query = "update `tiki_poll_options` set `votes`=`votes`+1 where `optionId`=?";
			$result = $this->query($query,array((int)$optionId));
		}
	}

	function get_ratings( $cat_type, $cat_objid, $user = null ) {
		global $tikilib, $prefs;

		$out = array();

		$result = $this->fetchAll("select `pollId` from `tiki_poll_objects` INNER JOIN `tiki_objects` ON `tiki_objects`.`objectId` = `tiki_poll_objects`.`catObjectId` WHERE `tiki_objects`.`type`=? and `tiki_objects`.`itemId`=?",array($cat_type,$cat_objid));
		foreach( $result as $row ) {
			$poll = array();
			$poll['info'] = $this->get_poll($row['pollId']);

			if( $cat_type == 'wiki page' ) {
				$poll['info'] = $this->pollnameclean( $poll['info'], $cat_objid );
			}

			$poll['options'] = $this->list_poll_options($row['pollId']);
			$poll['title'] = $poll['info']['title'];

			if( $user ) {
				$poll['vote'] = $tikilib->get_user_vote( 'poll' . $row['pollId'], $user );
			} else {
				$poll['vote'] = false;
			}

			$out[] = $poll;

			// Unless multiple polls per object is enabled, end after the first
			if( $prefs['poll_multiple_per_object'] != 'y' ) {
				break;
			}
		}

		return $out;
	}

	private function pollnameclean($s, $page) {
		if (isset($s['title'])) 
			$s['title'] = substr($s['title'], strlen($page)+2); 

		return $s;
	}	
	function remove_poll($pollId) {
		$query = "delete from `tiki_poll_objects` where `pollId`=?";
		$result = $this->query($query,array((int) $pollId));
		$query = "delete from `tiki_polls` where `pollId`=?";
		$result = $this->query($query,array((int) $pollId));
		$query = "delete from `tiki_poll_options` where `pollId`=?";
		$result = $this->query($query,array((int) $pollId));
		$this->remove_object('poll', $pollId);

		$query = 'delete from `tiki_user_votings` where `id`=?';
		$this->query($query, array('poll'.(int)$pollId));

		return true;
	}

	function get_catObjectId($cat_type,$cat_objid) {
		return $this->getOne("select `objectId` from `tiki_objects` where `type`=? and `itemId`=?",array($cat_type,$cat_objid));
	}

	function has_object_polls($catObjectId) {
		$query = "select count(*) from `tiki_poll_objects` where `catObjectId`=?";
		return $this->getOne($query,array((int)$catObjectId));
	}

	function remove_object_poll($cat_type,$cat_objid, $pollId = null) {
		$catObjectId = $this->get_catObjectId($cat_type,$cat_objid);
		$query = "delete from `tiki_poll_objects` where `catObjectId`=?";
		$bindvars = array((int)$catObjectId);

		if( $pollId ) {
			$query .= ' AND `pollId` = ?';
			$bindvars[] = $pollId;
		}

		$this->query($query,$bindvars);
		return true;
	}

  function create_poll($template_id,$title) {
    $pollid = $this->replace_poll(0,$title,"o",date('U'));
    $options = $this->list_poll_options($template_id);
    foreach ($options as $op) {
      $this->replace_poll_option($pollid,0,$op['title'],$op['position']);
    }
    return $pollid;
  }

  function replace_poll_option($pollId, $optionId, $title, $position) {
    if ($optionId) {
      $query = "update `tiki_poll_options` set `title`=?,`position`=? where `optionId`=?";
      $result = $this->query($query,array($title,(int)$position,(int)$optionId));
    } else {
      $query = "insert into `tiki_poll_options`(`pollId`,`title`,`position`,`votes`) values(?,?,?,?)";
      $result = $this->query($query,array((int)$pollId,$title,(int)$position,0));
    }
    return true;
  }

  function replace_poll($pollId, $title, $active, $publishDate, $voteConsiderationSpan=0) {
    if ($pollId) {
      $query = "update `tiki_polls` set `title`=?,`active`=?,`publishDate`=?, `voteConsiderationSpan`=? where `pollId`=?";
      $result = $this->query($query,array($title,$active,$publishDate, $voteConsiderationSpan, $pollId));
    } else {
      $query = "insert into tiki_polls(`title`,`active`,`publishDate`,`votes`, `voteConsiderationSpan`) values(?,?,?,?,?)";
      $result = $this->query($query,array($title,$active,$publishDate,0, $voteConsiderationSpan));
      $pollId = $this->getOne("select max(`pollId`) from `tiki_polls` where `title`=? and `publishDate`=?",array($title,$publishDate));
    }
    return $pollId;
  }

  function poll_categorize($catObjectId,$pollId,$title='') {
    $query = "delete from `tiki_poll_objects` where `catObjectId`=? and `pollId`=?";
    $result = $this->query($query,array((int) $catObjectId,(int) $pollId),-1,-1,false);
    $query = "insert into `tiki_poll_objects`(`catObjectId`,`pollId`,`title`) values(?,?,?)";
    $result = $this->query($query,array((int) $catObjectId,(int) $pollId, $title));
  }
	function get_poll_categories($pollId) {
		global $categlib; include_once('lib/categories/categlib.php');
		$query = "select tco.`categId`, tc.`name` from `tiki_poll_objects` tpo, `tiki_category_objects` tco, `tiki_categories` tc where tpo.`pollId`=? and tpo.`catObjectId`=tco.`catObjectId` and tco.`categId`=tc.`categId`";
		$result = $this->query($query,array((int)$pollId));
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}
		return $ret;
	}
	function get_poll_objects($pollId) {
		$query = "select tob.* from `tiki_objects` tob, `tiki_poll_objects` tpo where tpo.`pollId`=? and tpo.`catObjectId`=tob.`objectId`";
		$result = $this->query($query,array((int)$pollId));
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}
		return $ret;
	}


	function clone_poll($pollId) {
		$poll=$this->get_poll($pollId);
		if (!is_array($poll)) return false;

		/* copy the poll */
		$poll['pollId']=0;
		$poll['publishDate']=$this->now;
		$pollId_new=$this->replace_poll(0, $poll['title'], $poll['active'], $poll['publishDate']);

		/* copy the poll options */
		$options=$this->list_poll_options($pollId);
		foreach($options as $option) {
			$this->replace_poll_option($pollId_new, 0, $option['title'], $option['position']);
		}

		return $pollId_new;
	}

}
$polllib = new PollLibShared;
