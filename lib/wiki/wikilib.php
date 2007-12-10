<?php
// $Header: /cvsroot/tikiwiki/tiki/lib/wiki/wikilib.php,v 1.110.2.7 2007-12-10 15:18:38 sylvieg Exp $
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if( !defined( 'PLUGINS_DIR' ) ) {
	define('PLUGINS_DIR', 'lib/wiki-plugins');
}


class WikiLib extends TikiLib {
    function WikiLib($db) {
	$this->TikiLib($db);
    }

    //Special parsing for multipage articles
    function get_number_of_pages($data) {
	global $prefs;
	// Temporary remove <PRE></PRE> secions to protect
	// from broke <PRE> tags and leave well known <PRE>
	// behaviour (i.e. type all text inside AS IS w/o
	// any interpretation)
	$preparsed = array();

	preg_match_all("/(<[Pp][Rr][Ee]>)(.*?)(<\/[Pp][Rr][Ee]>)/s", $data, $preparse);
	$idx = 0;

	foreach (array_unique($preparse[2])as $pp) {
	    $key = md5($this->genPass());

	    $aux["key"] = $key;
	    $aux["data"] = $pp;
	    $preparsed[] = $aux;
	    $data = str_replace($preparse[1][$idx] . $pp . $preparse[3][$idx], $key, $data);
	    $idx = $idx + 1;
	}

	$parts = explode($prefs['wiki_page_separator'], $data);
	return count($parts);
    }

    function get_page($data, $i) {
	// Get slides
	global $prefs;
	$parts = explode($prefs['wiki_page_separator'], $data);
	$ret = $parts[$i - 1];

	if (substr($parts[$i - 1], 1, 5) == "<br/>") $ret = substr($parts[$i - 1], 6);
	if (substr($parts[$i - 1], 1, 6) == "<br />") $ret = substr($parts[$i - 1], 7);

	return $ret;
    }

    function get_creator($name) {
	return $this->getOne("select `creator` from `tiki_pages` where `pageName`=?", array($name));
    }

    /**
     *  Get the contributors for page
     *  the returned array does not contain the user $last (usually the current or last user)
     */
    function get_contributors($page, $last='') {
		static $cache_page_contributors;
		if ($cache_page_contributors['page'] == $page) {
			if (empty($last)) {
				return $cache_page_contributors['contributors'];
			}
			$ret = array();
			foreach ($cache_page_contributors['contributors'] as $res) {
				if ($res['user'] != $last) {
					$ret[] = $res;
				}
			}
			return $ret;
		}
		$query = "select DISTINCT `user` from `tiki_history` where `pageName`=? order by `version` desc";
		$result = $this->query($query,array($page));
		$cache_page_contributors = array();
		$cache_page_contributors['contributors'] = array();
		$ret = array();

		while ($res = $result->fetchRow()) {
			if ($res['user'] != $last) {
				$ret[] = $res['user'];
			}
			$cache_page_contributors['contributors'][] = $res['user'];
		}
		$cache_page_contributors['page'] = $page;
		return $ret;
    }

    // Returns all pages that links from here or to here, without distinction
    // This is used by wiki3d, to make the graph
    function wiki_get_neighbours($page) {

	   $neighbours = array();
	   $already = array();

           $query = "select `toPage` from `tiki_links` where `fromPage`=?";
	   $result = $this->query($query,array($page));
	   while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
	       $neighbour = $row['toPage'];
	       $neighbours[] = $neighbour;
	       $already[$neighbour] = 1;
	   }

           $query = "select `fromPage` from `tiki_links` where `toPage`=?";
	   $result = $this->query($query,array($page));
	   while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
	       $neighbour = $row['fromPage'];
	       if (!isset($already[$neighbour])) {
		   $neighbours[] = $neighbour;
	       }
	   }

	   return $neighbours;

    }


	// This method renames a wiki page
	// If you think this is easy you are very very wrong
	function wiki_rename_page($oldName, $newName) {
		global $prefs, $tikilib;
		// if page already exists, stop here
		if ($this->page_exists($newName)) {
			// if it is a case change of same page: allow it, else stop here
			if (strcasecmp($oldName, $newName) <> 0) return false;
		}

		// Since page link have HTML chars fixed;
		$newName = htmlspecialchars( $newName );
		$tmpName = "TmP".$newName."TmP";

		// 1st rename the page in tiki_pages, using a tmpname inbetween for
		// rename pages like ThisTestpage to ThisTestPage
		$query = "update `tiki_pages` set `pageName`=? where `pageName`=?";
		$this->query($query, array( $tmpName, $oldName ) );

		$query = "update `tiki_pages` set `pageName`=? where `pageName`=?";
		$this->query($query, array( $newName, $tmpName ) );

		// correct pageName in tiki_history, using a tmpname inbetween for
		// rename pages like ThisTestpage to ThisTestPage
		$query = "update `tiki_history` set `pageName`=? where `pageName`=?";
		$this->query($query, array( $tmpName, $oldName ) );

		$query = "update `tiki_history` set `pageName`=? where `pageName`=?";
		$this->query($query, array( $newName, $tmpName ) );

		// get pages linking to the old page
		$query = "select `fromPage` from `tiki_links` where `toPage`=?";
		$result = $this->query($query, array( $oldName ) );

		$linksToOld=array();
		while ($res = $result->fetchRow()) {
		    $page = $res['fromPage'];
		    $linksToOld[] = $res['fromPage'];
		    $info = $this->get_page_info($page);
		    //$data=addslashes(str_replace($oldName,$newName,$info['data']));
		    $data = $info['data'];
		    $oldName = quotemeta( $oldName );
 		    if (strstr($newName, " "))
		    	$data = preg_replace("/(?<= |\n|\t|\r|\,|\;|^)$oldName(?= |\n|\t|\r|\,|\;|$)/", "((".$newName."))", $data);
		    else
		    	$data = preg_replace("/(?<= |\n|\t|\r|\,|\;|^)$oldName(?= |\n|\t|\r|\,|\;|$)/", $newName, $data);
		    $data = preg_replace("/(?<=\(\()$oldName(?=\)\)|\|)/", $newName, $data);
		    $query = "update `tiki_pages` set `data`=?,`page_size`=? where `pageName`=?";
		    $this->query($query, array( $data,(int) strlen($data), $page));
		    $this->invalidate_cache($page);
		}

		// correct toPage and fromPage in tiki_links
		// before update, manage to avoid duplicating index(es) when B is renamed to C while page(s) points to both C (not created yet) and B
		$query = "select `fromPage` from `tiki_links` where `toPage`=?";
		$result = $this->query($query, array( $newName ) );
		$linksToNew = array();
		while ($res = $result->fetchRow()) {
			$linksToNew[] = $res['fromPage'];
		}
		if ($extra = array_intersect($linksToOld, $linksToNew)) {
			$query = "delete from `tiki_links` where `fromPage` in (".implode(',', array_fill(0,count($extra),'?')).") and `toPage`=?";
			$this->query($query,array_merge($extra,array($oldName)));
		}
		$query = "update `tiki_links` set `fromPage`=? where `fromPage`=?";
		$this->query($query, array( $newName, $oldName));

		$query = "update `tiki_links` set `toPage`=? where `toPage`=?";
		$this->query($query, array( $newName, $oldName));

		// tiki_footnotes change pageName
		$query = "update `tiki_page_footnotes` set `pageName`=? where `pageName`=?";
		$this->query($query, array( $newName, $oldName ));

		// in tiki_categorized_objects update objId
		$newcathref = 'tiki-index.php?page=' . urlencode($newName);
		$query = "update `tiki_objects` set `itemId`=?,`name`=?,`href`=? where `itemId`=? and `type`=?";
		$this->query($query, array( $newName, $newName, $newcathref, $oldName, 'wiki page'));

		// old code that doesn't seem to be working
		//	$query = "update tiki_categorized_objects set objId='$newId' where objId='$oldId'";
		//    $this->query($query);

		// in tiki_comments update object
		$query = "update `tiki_comments` set `object`=? where `object`=?";
		$this->query($query, array( $newName, $oldName ) );

		// Move email notifications
		$oldId = 'wikipage' . $oldName;
		$newId = 'wikipage' . $newName;
		$query = "update `tiki_user_watches` set `object`=? where `object`=?";
		$this->query($query, array( $newId, $oldId ) );

		// theme_control_objects(objId,name)
		$oldId = md5('wiki page' . $oldName);
		$newId = md5('wiki page' . $newName);
		$query = "update `tiki_theme_control_objects` set `objId`=?, `name`=? where `objId`=?";
		$this->query($query, array( $newId, $newName, $oldId ) );

		$query = "update `tiki_wiki_attachments` set `page`=? where `page`=?";
		$this->query($query, array( $newName, $oldName ) );

		// group home page
		$query = "update `users_groups` set `groupHome`=? where `groupHome`=?";
		$this->query($query, array( $newName, $oldName ) );

		//breadcrumb
		if (isset($_SESSION["breadCrumb"]) && in_array($oldName, $_SESSION["breadCrumb"])) {
			$pos = array_search($oldName, $_SESSION["breadCrumb"]);
			$_SESSION["breadCrumb"][$pos] = $newName;
		}

		// Move custom permissions
		$oldId = md5('wiki page' . strtolower($oldName));
		$newId = md5('wiki page' . strtolower($newName));
		$query = "update `users_objectpermissions` set `objectId`=? where `objectId`=?";
		$this->query($query, array( $newId, $oldId ) );

		global $prefs;
		if ($prefs['feature_actionlog'] == 'y') {
			global $logslib; include_once('lib/logs/logslib.php');
			$logslib->add_action('Renamed', $newName, 'wiki page', 'old='.$oldName.'&new='.$newName, '', '', '', '', '', array(array('rename'=>$oldName)));
			$logslib->rename('wiki page', $oldName, $newName);
		}
		global $user;
		global $tikilib;
		global $smarty;

		// first get all watches for this page ...
		if ($prefs['feature_user_watches'] == 'y') {
			$nots = $tikilib->get_event_watches('wiki_page_changed', $oldName);
		}

		// ... then update the watches table
		// user watches
		$query = "update `tiki_user_watches` set `object`=?, `title`=?, `url`=? where `object`=? and `type` = 'wiki page'";
		$this->query($query, array( $newName, $newName, 'tiki-index.php?page='.$newName, $oldName ) );

		// now send notification email to all on the watchlist:
		if ($prefs['feature_user_watches'] == 'y') {
			if (!isset($_SERVER["SERVER_NAME"])) {
				$_SERVER["SERVER_NAME"] = $_SERVER["HTTP_HOST"];
			}
			if (count($nots)) {
				include_once("lib/notifications/notificationemaillib.php");
				$smarty->assign('mail_site', $_SERVER["SERVER_NAME"]);
				$smarty->assign('mail_oldname', $oldName);
				$smarty->assign('mail_newname', $newName);
				$smarty->assign('mail_date', $this->now);
				$smarty->assign('mail_user', $user);
				$foo = parse_url($_SERVER["REQUEST_URI"]);
				$machine = $tikilib->httpPrefix(). $foo["path"];
				$smarty->assign('mail_machine', $machine);
				$parts = explode('/', $foo['path']);
				if (count($parts) > 1)
					unset ($parts[count($parts) - 1]);
				$smarty->assign('mail_machine_raw', $tikilib->httpPrefix(). implode('/', $parts));
				sendEmailNotification($nots, "watch", "user_watch_wiki_page_renamed_subject.tpl", $_SERVER["SERVER_NAME"], "user_watch_wiki_page_renamed.tpl");
			}
		}

		if ( $prefs['feature_search'] == 'y' && $prefs['feature_search_fulltext'] != 'y' && $prefs['search_refresh_index_mode'] == 'normal' ) {
			require_once('lib/search/refresh-functions.php');
			refresh_index('pages', $newName);
		}

		global $menulib; include_once('lib/menubuilder/menulib.php');
		$menulib->rename_wiki_page($oldName, $newName);

		if ($prefs['wikiHomePage'] == $oldName) {
			$tikilib->set_preference('wikiHomePage', $newName);
		}

		return true;
	}

	function set_page_cache($page,$cache) {
		$query = "update `tiki_pages` set `wiki_cache`=? where `pageName`=?";
		$this->query($query, array( $cache, $page));
	}

	// TODO: huho why that function is empty ?
	function save_notepad($user, $title, $data) {
	}

	// Methods to cache and handle the cached version of wiki pages
	// to prevent parsing large pages.
	function get_cache_info($page) {
		$query = "select `cache`,`cache_timestamp` from `tiki_pages` where `pageName`=?";

		$result = $this->query($query, array( $page ) );
		$res = $result->fetchRow();
		return $res;
	}

	function update_cache($page, $data) {

		$query = "update `tiki_pages` set `cache`=?, `cache_timestamp`=? where `pageName`=?";
		$result = $this->query($query, array( $data, $this->now, $page ) );
		return true;
	}

	function get_attachment_owner($attId) {
		return $this->getOne("select `user` from `tiki_wiki_attachments` where `attId`=$attId");
	}

	function remove_wiki_attachment($attId) {
		global $prefs;

		$path = $this->getOne("select `path` from `tiki_wiki_attachments` where `attId`=$attId");

		/* carefull a same file can be attached in different page */
		if ($path && $this->getOne("select count(*) from `tiki_wiki_attachments` where `path`='$path'") <= 1) {
			@unlink ($prefs['w_use_dir'] . $path);
		}

		$query = "delete from `tiki_wiki_attachments` where `attId`='$attId'";
		$result = $this->query($query);
	}

	function wiki_attach_file($page, $name, $type, $size, $data, $comment, $user, $fhash) {
		$comment = strip_tags($comment);
		$query = "insert into `tiki_wiki_attachments`(`page`,`filename`,`filesize`,`filetype`,`data`,`created`,`downloads`,`user`,`comment`,`path`) values(?,?,?,?,?,?,0,?,?,?)";
		$this->blob_encode($data);
		$result = $this->query($query,array("$page","$name", (int) $size,"$type","$data", (int) $this->now,"$user","$comment","$fhash"));

		global $prefs;
	        if ($prefs['feature_score'] == 'y') {
        	    $this->score_event($user, 'wiki_attach_file');
	        }
	}

	function list_wiki_attachments($page, $offset=0, $maxRecords=-1, $sort_mode='created_desc', $find='') {

	if ($find) {
	    $mid = " where `page`=? and (`filename` like ?)"; // why braces?
	    $bindvars=array($page,"%".$find."%");
	} else {
	    $mid = " where `page`=? ";
	    $bindvars=array($page);
	}

	$query = "select `user`,`attId`,`page`,`filename`,`filesize`,`filetype`,`downloads`,`created`,`comment` from `tiki_wiki_attachments` $mid order by ".$this->convert_sortmode($sort_mode);
	$query_cant = "select count(*) from `tiki_wiki_attachments` $mid";
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
	function list_all_attachements($offset=0, $maxRecords=-1, $sort_mode='created_desc', $find='') {
		if ($find) {
			$findesc = '%' . $find . '%';
			$mid = " where `filename` like ?";
			$bindvars=array($findesc);
		} else {
			$mid = "";
			$bindvars=array();
		}
		$query = "select `user`,`attId`,`page`,`filename`,`filesize`,`filetype`,`downloads`,`created`,`comment`,`path` ";
		$query.= " from `tiki_wiki_attachments` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_wiki_attachments` $mid";
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

	function file_to_db($path,$attId) {
		if (is_file($path)) {
			$fp = fopen($path,'rb');
			$data = '';
			while (!feof($fp)) {
				$data .= fread($fp, 8192 * 16);
			}
			fclose ($fp);
			$query = "update `tiki_wiki_attachments` set `data`=?,`path`=? where `attId`=?";
			if ($this->query($query,array($data,'',(int)$attId))) {
				unlink($path);
			}
		}
	}

	function db_to_file($path,$attId) {
		$fw = fopen($path,'wb');
		$data = $this->getOne("select `data` from `tiki_wiki_attachments` where `attId`=?",array((int)$attId));
		if ($data) {
			fwrite($fw, $data);
		}
		fclose ($fw);
		if (is_file($path)) {
			$query = "update `tiki_wiki_attachments` set `data`=?,`path`=? where `attId`=?";
			$this->query($query,array('',basename($path),(int)$attId));
		}
	}

	function get_item_attachment($attId) {
		$query = "select * from `tiki_wiki_attachments` where `attId`=?";
		$result = $this->query($query,array((int) $attId));
		if (!$result->numRows()) return false;
		$res = $result->fetchRow();
		return $res;
	}
	function get_item_attachement_data($att_info) {
		if ($att_info['path']) {
			return file_get_contents($att_info['filename']);
		} else {
			return $att_info['data'];
		}
	}


    // Functions for wiki page footnotes
    function get_footnote($user, $page) {

	$count = $this->getOne("select count(*) from `tiki_page_footnotes` where `user`=? and `pageName`=?",array($user,$page));

	if (!$count) {
	    return '';
	} else {
	    return $this->getOne("select `data` from `tiki_page_footnotes` where `user`=? and `pageName`=?",array($user,$page));
	}
    }

    function replace_footnote($user, $page, $data) {
	$querydel = "delete from `tiki_page_footnotes` where `user`=? and `pageName`=?";
	$this->query($querydel,array($user, $page),-1,-1,false);
	$query = "insert into `tiki_page_footnotes`(`user`,`pageName`,`data`) values(?,?,?)";
	$this->query($query,array($user,$page,$data));
    }

    function remove_footnote($user, $page) {
	if (empty($user)) {
		$query = "delete from `tiki_page_footnotes` where `pageName`=?";
		$this->query($query,array($page));
	} else {
		$query = "delete from `tiki_page_footnotes` where `user`=? and `pageName`=?";
		$this->query($query,array($user,$page));
	}
    }

    function wiki_link_structure() {
	$query = "select `pageName` from `tiki_pages` order by ".$this->convert_sortmode("pageName_asc");

	$result = $this->query($query);

	while ($res = $result->fetchRow()) {
	    print ($res["pageName"] . " ");

	    $page = $res["pageName"];
	    $query2 = "select `toPage` from `tiki_links` where `fromPage`=?";
	    $result2 = $this->query($query2, array( $page ) );
	    $pages = array();

	    while ($res2 = $result2->fetchRow()) {
		if (($res2["toPage"] <> $res["pageName"]) && (!in_array($res2["toPage"], $pages))) {
		    $pages[] = $res2["toPage"];
		    print ($res2["toPage"] . " ");
		}
	    }

	    print ("\n");
	}
    }

    // Removes last version of the page (from pages) if theres some
    // version in the tiki_history then the last version becomes the actual version
    function remove_last_version($page, $comment = '') {

	$this->invalidate_cache($page);
	$query = "select * from `tiki_history` where `pageName`=? order by ".$this->convert_sortmode("lastModif_desc");
	$result = $this->query($query, array( $page ) );

	if ($result->numRows()) {
	    // We have a version
	    $res = $result->fetchRow();

		global $histlib;
		if (!is_object($histlib)) {
			include_once('lib/wiki/histlib.php');
		}

	    $histlib->use_version($res["pageName"], $res["version"]);
	    if ($prefs['feature_contribution'] == 'y') {
		global $contributionlib; include_once('lib/contribution/contributionlib.php');
		$info = $tikilib->get_page_info($res['pageName']);
		$contributionlib->change_assigned_contributions($res['historyId'], 'history', $res['pageName'], 'wiki page', $info['description'], $res['pageName'], "tiki-index.php?page".urlencode($res['pageName']));
	    }
	    $histlib->remove_version($res['pageName'], $res['version']);
	} else {
	    $this->remove_all_versions($page);
	}
	global  $logslib; include_once('lib/logs/logslib.php');
	$logslib->add_action('Removed last version', $page, 'wiki page', $comment);
	//get_strings tra("Removed last version");
    }

    // Like pages are pages that share a word in common with the current page
    function get_like_pages($page) {
	global $user, $tikilib;
	preg_match_all("/([A-Z])([a-z]+)/", $page, $words);

	// Add support to ((x)) in either strict or full modes
	preg_match_all("/(([A-Za-z]|[\x80-\xFF])+)/", $page, $words2);
	$words = array_unique(array_merge($words[0], $words2[0]));
	$exps = array();
	$bindvars=array();
	foreach ($words as $word) {
	    $exps[] = " `pageName` like ?";
	    $bindvars[] = "%$word%";
	}

	$exp = implode(" or ", $exps);
	if ($exp) {
		$query = "select `pageName` from `tiki_pages` where $exp";
		$result = $this->query($query,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			if ($tikilib->user_has_perm_on_object($user, $page, 'wiki page', 'tiki_p_view'))
						$ret[] = $res["pageName"];
		}

		return $ret;
	} else {
		return array();
	}
    }

    function is_locked($page, $info=null) {
	if (!$info) {
		$query = "select `flag`, `user` from `tiki_pages` where `pageName`=?";
		$result = $this->query($query, array( $page ) );
		$info = $result->fetchRow();
	}
	return ($info["flag"] == 'L')? $info["user"] : null;
    }
    function is_editable($page, $user, $info=null) {
	global $tiki_p_admin, $tiki_p_admin_wiki, $prefs;
      if ($tiki_p_admin == 'y' || $tiki_p_admin_wiki == 'y')
            return true;
      else {
		if ($prefs['feature_wiki_userpage'] == 'y' and strcasecmp(substr($page, 0, strlen($prefs['feature_wiki_userpage_prefix'])), $prefs['feature_wiki_userpage_prefix']) == 0 and strcasecmp($page, $prefs['feature_wiki_userpage_prefix'].$user) != 0)
			return false;
		if (!$this->user_has_perm_on_object($user,$page,'wiki page','tiki_p_edit'))
			return false;
		return ($this->is_locked($page, $info) == null || $user == $this->is_locked($page, $info))? true : false;
	}
    }

    function lock_page($page) {
	global $user;

	$query = "update `tiki_pages` set `flag`=?, `lockedby`=? where `pageName`=?";
	$result = $this->query($query, array( "L", $user, $page ) );

	if (!empty($user)) {
		$query = "update `tiki_pages` set `user`=?  where `pageName`=?";

		$result = $this->query($query, array( $user, $page ) );
	}

	return true;
    }

    function unlock_page($page) {
	$query = "update `tiki_pages` set `flag`='' where `pageName`=?";
	$result = $this->query($query, array( $page ) );
	return true;
    }

    // Returns backlinks for a given page
    function get_backlinks($page) {
	global $user;
	$query = "select `fromPage` from `tiki_links` where `toPage` = ?";
	$result = $this->query($query, array( $page ));
	$ret = array();

	while ($res = $result->fetchRow()) {
	 	if ($this->user_has_perm_on_object($user, $res['fromPage'], 'wiki page', 'tiki_p_view')) {
			$aux["fromPage"] = $res["fromPage"];
			$ret[] = $aux;
		}
	}

	return $ret;
    }

    function list_plugins($with_help = false) {
	$files = array();
	
	if (is_dir(PLUGINS_DIR)) {
	    if ($dh = opendir(PLUGINS_DIR)) {
		while (($file = readdir($dh)) !== false) {
		    if (preg_match("/^wikiplugin_.*\.php$/", $file))
			array_push($files, $file);
		}
		closedir ($dh);
	    }
	}
	sort($files);
	if ($with_help) {
		global $cachelib;
		if (!$cachelib->isCached('plugindesc')) {
			$plugins = array();
			foreach ($files as $pfile) {
					$pinfo['file'] = $pfile;
				$pinfo["help"] = $this->get_plugin_description($pfile);
				$pinfo["name"] = strtoupper(str_replace(".php", "", str_replace("wikiplugin_", "", $pfile)));
				$plugins[] = $pinfo;
			}
			$cachelib->cacheItem("plugindesc",serialize($plugins));
		} else {
			$plugins = unserialize($cachelib->getCached("plugindesc"));
		}
		return $plugins;
	} else {
		return $files;
    }
	}

    //
    // Call 'wikiplugin_.*_description()' from given file
    //
    function get_plugin_description($file) {
    	global $tikilib;
        $data = '';
        $fp = fopen(PLUGINS_DIR . '/' . $file, 'r');
        while(!feof($fp)) {
              $data .= fread($fp,4096);
        }
        fclose($fp);
        $func_name = str_replace('.php', '', $file). '_help';
    	if (!preg_match('#.*?function\s+' . $func_name .
    	                '[\s|^]*\([\s|^]*\)[\s|^]*(.+)#msi', $data, $prematch)
    	    || !preg_match('#\{((?:(?R)|[^{}]+)+)}#ms',
    	            $prematch[1], $matches)) {
    	   return '';
    	}
        $fun = create_function('', $matches[1]);
        $ret = $tikilib->parse_data($fun());
        return $ret;
    }

	// get all modified pages for a user (if actionlog is not clean
	function get_user_all_pages($user, $sort_mode) {
		$query = "select  distinct(p.`pageName`), p.`user` as lastEditor, p.`creator`, max(a.`lastModif`) as date from `tiki_actionlog` as a, `tiki_pages` as p where a.`object`= p.`pageName` and a.`user`= ? group by p.`pagename` order by ".$this->convert_sortmode($sort_mode);
		$result = $this->query($query, array($user));
		$ret = array();
		while ($res = $result->fetchRow()) {
				$ret[] = $res;
		}
		return $ret;
	}

	function get_default_wiki_page() {
		global $user, $prefs;
	 	if ($prefs['useGroupHome'] == 'y') {
			global $user;
			global $userlib; include_once('lib/userslib.php');
			if ($groupHome = $userlib->get_user_default_homepage($user))
				return $groupHome;
			else
 				return $prefs['wikiHomePage'];
		}
		return $prefs['wikiHomePage'];
	}

	function save_draft($pageName, $pageDesc, $pageData, $pageComment) {
	    global $user;

	    if (!$user) return false;

	    $query = "delete from `tiki_page_drafts` where `user`=? and `pageName`=?";
	    $this->query($query, array($user, $pageName));

	    $query = "insert into `tiki_page_drafts` (`user`,`pageName`,`data`,`description`,`comment`,`lastModif`) values (?,?,?,?,?,?)";
	    $bindvals = array($user, $pageName, $pageData, $pageDesc, $pageComment, time());

	    return $this->query($query, $bindvals) ? true : false;
	}

}

global $wikilib;
global $dbTiki;
$wikilib = new WikiLib($dbTiki);

?>
