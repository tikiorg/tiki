<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

class PollLibShared extends TikiLib {
	function PollLibShared($db) {
		$this->TikiLib($db);
	}

	function get_poll($pollId) {
		$query = "select * from `tiki_polls` where `pollId`=?";
		$result = $this->query($query,array((int)$pollId));
		if (!$result->numRows()) return false;
		$res = $result->fetchRow();
		return $res;
	}

	function list_poll_options($pollId) {
		$query = "select * from `tiki_poll_options` where `pollId`=? order by `position`";
		$result = $this->query($query,array((int) $pollId));
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

	function poll_vote($user, $pollId, $optionId) {
		global $smarty;
		$previous_vote = $this->get_user_vote("poll$pollId",$user);
		$poll = $this->get_poll($pollId);
		if( $poll['active'] == 'x' )
		{
		    $smarty->assign('msg', tra("This poll is closed."));
		    $smarty->display("error.tpl");
		    die;
		} else {
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
	}
  
	function get_rating($cat_type,$cat_objid) {
		$catObjectId = $this->getOne("select `objectId` from `tiki_objects` where `type`=? and `itemId`=?",array($cat_type,$cat_objid));
    if ($catObjectId and $catObjectId > 0) {
      $result = $this->query("select * from `tiki_poll_objects` where `catObjectId`=?",array((int)$catObjectId));
      $res = $result->fetchRow();
      $poll['info'] = $this->get_poll($res['pollId']);
			$poll['options'] = $this->list_poll_options($res['pollId']);
			$poll['title'] = $res['title'];
			return $poll;
		}
		return false;
  }
	
	function remove_poll($pollId) {
    $query = "delete from `tiki_poll_objects` where `pollId`=?";
    $result = $this->query($query,array((int) $pollId));
    $query = "delete from `tiki_polls` where `pollId`=?";
    $result = $this->query($query,array((int) $pollId));
    $query = "delete from `tiki_poll_options` where `pollId`=?";
    $result = $this->query($query,array((int) $pollId));
    $this->remove_object('poll', $pollId);
    return true;
  }

	function get_catObjectId($cat_type,$cat_objid) {
		return $this->getOne("select `objectId` from `tiki_objects` where `type`=? and `itemId`=?",array($cat_type,$cat_objid));
	}

  function has_object_polls($catObjectId) {
    $query = "select count(*) from `tiki_poll_objects` where `catObjectId`=?";
    return $this->getOne($query,array((int)$catObjectId));
  }
  
  function remove_object_poll($cat_type,$cat_objid) {
		$catObjectId = $this->get_catObjectId($cat_type,$cat_objid);
    $this->query("delete from `tiki_poll_objects` where `catObjectId`=?",array((int)$catObjectId));
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

  function replace_poll($pollId, $title, $active, $publishDate) {
    if ($pollId) {
      $query = "update `tiki_polls` set `title`=?,`active`=?,`publishDate`=? where `pollId`=?";
      $result = $this->query($query,array($title,$active,$publishDate,$pollId));
    } else {
      $query = "insert into tiki_polls(`title`,`active`,`publishDate`,`votes`) values(?,?,?,?)";
      $result = $this->query($query,array($title,$active,$publishDate,0));
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
global $dbTiki;
$polllib = new PollLibShared($dbTiki);

?>
