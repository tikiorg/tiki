<?php
// \todo extract HTML from here !!

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class DirLib extends TikiLib {
	function DirLib($db) {
		$this->TikiLib($db);
	}

	// Path functions
	function dir_get_category_path_admin($categId) {
		global $prefs;
		$info = $this->dir_get_category($categId);
		$path = '<a class="link" href="tiki-directory_admin_categories.php?parent=' . $info["categId"] . '">' . $info["name"] . '</a>';
		while ($info["parent"] != 0) {
			$info = $this->dir_get_category($info["parent"]);
			$path = '<a class="link" href="tiki-directory_admin_categories.php?parent=' . $info["categId"] . '">' . $info["name"] . '</a>' . $prefs['site_crumb_seper'] . $path;
		}
		return $path;
	}

	function dir_get_path_text($categId) {
		global $prefs;
		$info = $this->dir_get_category($categId);
		$path = $info["name"];
		while ($info["parent"] != 0) {
			$info = $this->dir_get_category($info["parent"]);
			$path = $info["name"] . $prefs['site_crumb_seper'] . $path;
		}
		return $path;
	}

	function dir_get_category_path_browse($categId) {
		global $prefs;
		$path = '';
		$info = $this->dir_get_category($categId);
		$path = '<a class="dirlink" href="tiki-directory_browse.php?parent=' . $info["categId"] . '">' . $info["name"] . '</a>';
		while ($info["parent"] != 0) {
			$info = $this->dir_get_category($info["parent"]);
			$path = '<a class="dirlink" href="tiki-directory_browse.php?parent=' . $info["categId"] . '">' . $info["name"] . '</a> ' . $prefs['site_crumb_seper'] . ' ' . $path;
		}

		return $path;
	}

	function dir_build_breadcrumb_trail($categId) {
		$crumbs = array();
		$info = $this->dir_get_category($categId);
		if(isset($info["name"])) {
			$crumbs[] = new Breadcrumb($info["name"],  
				'',            
				'tiki-directory_browse.php?parent=' . $info["categId"],
				'',
				'');
		}
		while ($info["parent"] != 0) {
			$info = $this->dir_get_category($info["parent"]);
			$crumbs[] = new Breadcrumb($info["name"],
				'',
				'tiki-directory_browse.php?parent=' . $info["categId"],
				'',               
				'');                    
		}
		return empty($crumbs) ? null : array_reverse($crumbs);
	}


	// Stats functions
	// get stats (valid sites, invalid sites, categories, searches)

	// Functions to manage categories
	function get_random_subcats($parent, $cant) {
		//Return an array of 'cant' random subcategories
		$count = $this->getOne("select count(*) from `tiki_directory_categories` where `parent`=?",array((int)$parent));
		if ($count < $cant) $cant = $count;
		$ret = array();
		while (count($ret) < $cant) {
			$x = rand(0, $count);
			if (!in_array($x, $ret)) {
				$ret[] = $x;
			}
		}

		$ret = array();
		foreach ($ret as $r) {
			$query = "select * from `tiki_directory_categories`";
			$result = $this->query($query,array(),1,$r);
			$ret[] = $result->fetchRow();
		}
		return $ret;
	}

	// List
	function dir_list_categories($parent, $offset, $maxRecords, $sort_mode, $find) {
		$bindvars = array((int)$parent);
		if ($find) {
			$findesc = '%'.$find.'%';
			$mid = " and (`name` like ? or `description` like ?)";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		} else {
			$mid = "";
		}
		$query = "select * from `tiki_directory_categories` where `parent`=? $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_directory_categories` where `parent`=? $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$res["sites"] = $this->getOne("select count(*) from `tiki_category_sites` where `categId`=?",array((int)$res["categId"]));
			//$res["path"]=$this->dir_get_path_text($res["categId"]);

		    $add = TRUE;
		    global $prefs, $userlib, $user, $tiki_p_admin;

		    if ($tiki_p_admin != 'y' && $prefs['feature_categories'] == 'y') {
		    	global $categlib;
				if (!is_object($categlib)) {
					include_once('lib/categories/categlib.php');
				}
		    	unset($tiki_p_view_categorized); // unset this var in case it was set previously
		    	$perms_array = $categlib->get_object_categories_perms($user, 'directory', $res['categId']);
		    	if ($perms_array) {
		    		$is_categorized = TRUE;
			    	foreach ($perms_array as $perm => $value) {
			    		$$perm = $value;
			    	}
		    	} else {
		    		$is_categorized = FALSE;
		    	}

		    	if ($is_categorized && isset($tiki_p_view_categorized) && $tiki_p_view_categorized != 'y') {
		    		$add = FALSE;
		    	}
		    }
		    if ($add) {
				$ret[] = $res;
		    }
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	// List all categories
	function dir_list_all_categories($offset, $maxRecords, $sort_mode, $find) {
		$bindvars = array();
		if ($find) {
			$findesc = '%'.$find.'%';
			$mid = " where (`name` like ? or `description` like ?)";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		} else {
			$mid = "";
		}
		$query = "select * from `tiki_directory_categories` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_directory_categories` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$res["sites"] = $this->getOne("select count(*) from `tiki_category_sites` where `categId`=?",array((int)$res["categId"]));
			//$res["path"]=$this->dir_get_path_text($res["categId"]);
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function dir_list_sites($parent, $offset=0, $maxRecords=-1, $sort_mode='hits_desc', $find='', $isValid='y') {
		$bindvars = array((int)$parent);
		if ($find) {
			$findesc = '%'.$find.'%';
			$mid = " and (`name` like ? or `description` like ?)";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		} else {
			$mid = "";
		}
		if ($isValid) {
			$mid .= " and `isValid`=? ";
			$bindvars[] = $isValid;
		}
		$query = "select * from `tiki_directory_sites` tds, `tiki_category_sites` tcs where tds.`siteId`=tcs.`siteId` and tcs.`categId`=? $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_directory_sites` tds, `tiki_category_sites` tcs where tds.`siteId`=tcs.`siteId` and tcs.`categId`=? $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$res["cats"] = $this->dir_get_site_categories($res["siteId"]);
			$res["description"] = $this->parse_data($res["description"]);
			$ret[] = $res;
		}
		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function dir_list_invalid_sites($offset, $maxRecords, $sort_mode, $find) {
		$bindvars = array("n");
		if ($find) {
			$findesc = '%'.$find.'%';
			$mid = " and (`name` like ? or `description` like ?)";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		} else {
			$mid = "";
		}
		$query = "select * from `tiki_directory_sites` where `isValid`=? $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_directory_sites` where `isValid`=? $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$res["cats"] = $this->dir_get_site_categories($res["siteId"]);
			$ret[] = $res;
		}
		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function dir_get_site_categories($siteId) {
		$query = "select tdc.`name`,tcs.`categId` from `tiki_category_sites` tcs,`tiki_directory_categories` tdc where tcs.`siteId`=? and tcs.`categId`=tdc.`categId`";
		$result = $this->query($query,array((int)$siteId));
		$ret = array();

		while ($res = $result->fetchRow()) {
			$res["path"] = $this->dir_get_path_text($res["categId"]);
			$ret[] = $res;
		}
		return $ret;
	}

	function dir_list_all_sites($offset, $maxRecords, $sort_mode, $find) {
		$bindvars = array();
		if ($find) {
			$findesc = '%'.$find.'%';
			$mid = " and (`name` like ? or `description` like ?)";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		} else {
			$mid = "";
		}
		$query = "select * from `tiki_directory_sites` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_directory_sites` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$res["cats"] = $this->dir_get_site_categories($res["siteId"]);
			$ret[] = $res;
		}
		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function dir_list_all_valid_sites($offset, $maxRecords, $sort_mode, $find) {
		$bindvars = array('y');
		$mid = " where `isValid`=? ";
		if ($find) {
			$findesc = '%'.$find.'%';
			$mid .= " and (`name` like ? or `description` like ?)";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		}

		$query = "select * from `tiki_directory_sites` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_directory_sites` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$res["cats"] = $this->dir_get_site_categories($res["siteId"]);
			$ret[] = $res;
		}
		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function dir_get_all_categories($offset, $maxRecords, $sort_mode, $find, $siteId = 0) {
		$bindvars = array();
		if ($find) {
			$findesc = '%'.$find.'%';
			$mid = " and (`title` like ? or `data` like ?)";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		} else {
			$mid = "";
		}
		$query = "select * from `tiki_directory_categories` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_directory_categories` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$res["path"] = $this->dir_get_path_text($res["categId"]);
			$res["belongs"] = 'n';
			if ($siteId) {
				$belongs = $this->db->getOne( "select count(*) from `tiki_category_sites` where `siteId`=? and `categId`=?",array((int)$siteId,(int)$res["categId"]));
				if ($belongs) {
					$res["belongs"] = 'y';
				}
			}
			$ret[] = $res;
		}
		usort($ret, 'compare_paths');
		return $ret;
	}

	function dir_get_all_categories_np($offset, $maxRecords, $sort_mode, $find, $parent) {
		$bindvars = array((int)$parent);
		if ($find) {
			$findesc = '%'.$find.'%';
			$mid = " and (`title` like ? or `data` like ?)";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		} else {
			$mid = "";
		}
		$query = "select * from `tiki_directory_categories` where `categId`<>? $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_directory_categories` where `categId`<>? $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$res["path"] = $this->dir_get_path_text($res["categId"]);
			$ret[] = $res;
		}
		usort($ret, 'compare_paths');
		return $ret;
	}

	function dir_get_all_categories_accept_sites($offset, $maxRecords, $sort_mode, $find, $siteId = 0) {
		$bindvars = array('y');
		if ($find) {
			$findesc = '%'.$find.'%';
			$mid = " and (`title` like ? or `data` like ?)";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		} else {
			$mid = "";
		}
	
		$query = "select * from `tiki_directory_categories` where `allowSites`=? $mid ";
		$query_cant = "select count(*) from `tiki_directory_categories` where `allowSites`=? $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$res["sites"] = $this->db->getOne("select count(*) from `tiki_category_sites` where `categId`=" . $res["categId"]);
			$res["path"] = $this->dir_get_path_text($res["categId"]);
			$res["belongs"] = 'n';

			if ($siteId) {
				$belongs = $this->db->getOne( "select count(*) from `tiki_category_sites` where `siteId`=? and `categId`=?",array((int)$siteId,(int)$res["categId"]));
				if ($belongs) {
					$res["belongs"] = 'y';
				}
			}
			$ret[] = $res;
		}
		usort($ret, 'compare_paths');
		return $ret;
	}

	function dir_validate_site($siteId) {
		$query = "update `tiki_directory_sites` set `isValid`=? where `siteId`=?";
		$this->query($query,array("y",(int)$siteId));
	}

	function dir_replace_site($siteId, $name, $description, $url, $country, $isValid) {
		global $prefs;

		make_clean($name);
		make_clean($description);
		make_clean($url);
		make_clean($country);

		if ($siteId) {
			$query = "update `tiki_directory_sites` set `name`=?, `description`=?, `url`=?, `country`=?, `isValid`=?, `lastModif`=?  where `siteId`=?";
			$this->query($query,array($name,$description,$url,$country,$isValid,(int)$this->now,(int)$siteId));
		} else {
			$query = "insert into `tiki_directory_sites`(`name`,`description`,`url`,`country`,`isValid`,`hits`,`created`,`lastModif`) values(?,?,?,?,?,?,?,?)";
			$this->query($query,array($name,$description,$url,$country,$isValid,0,(int)$this->now,(int)$this->now));
			$siteId = $this->db->getOne("select max(siteId) from `tiki_directory_sites` where `created`=? and `name`=?",array((int)$this->now,$name));

			if ($prefs['cachepages'] == 'y') {
				$this->cache_url($url);
			}
		}

		if ( $prefs['feature_search'] == 'y' && $prefs['feature_search_fulltext'] != 'y' && $prefs['search_refresh_index_mode'] == 'normal' ) {
			require_once('lib/search/refresh-functions.php');
			refresh_index('directory_sites', $siteId);
		}
		return $siteId;
	}

	// Replace
	function dir_replace_category($parent, $categId, $name, $description, $childrenType, $viewableChildren, $allowSites, $showCount, $editorGroup) {

		if ($categId) {
			$query = "update `tiki_directory_categories` set `name`=?, `parent`=?, `description`=?, `childrenType`=?, `viewableChildren`=?, `allowSites`=?, `showCount`=?, `editorGroup`=?  where `categId`=?";
			$this->query($query,array($name,(int)$parent,$description,$childrenType,(int)$viewableChildren,$allowSites,$showCount,$editorGroup,(int)$categId));
		} else {
			$query = "insert into `tiki_directory_categories`(`parent`,`hits`,`name`,`description`,`childrenType`,`viewableChildren`,`allowSites`,`showCount`,`editorGroup`,`sites`) values(?,?,?,?,?,?,?,?,?,?)";
			$this->query($query,array((int)$parent,0,$name,$description,$childrenType,(int)$viewableChildren,$allowSites,$showCount,$editorGroup,0));
			$categId = $this->getOne("select max(`categId`) from `tiki_directory_categories` where `name`=?",array($name));
		}

		global $prefs;
		if ( $prefs['feature_search'] == 'y' && $prefs['feature_search_fulltext'] != 'y' && $prefs['search_refresh_index_mode'] == 'normal' ) {
			require_once('lib/search/refresh-functions.php');
			refresh_index('directory_categories', $categId);
		}
		return $categId;
	}

	// Get
	function dir_get_site($siteId) {
		$query = "select * from `tiki_directory_sites` where `siteId`=?";
		$result = $this->query($query,array((int)$siteId));
		if (!$result->numRows()) return false;
		$res = $result->fetchRow();
		return $res;
	}

	function dir_get_category($categId) {
		$query = "select * from `tiki_directory_categories` where `categId`=?";
		$result = $this->query($query,array((int)$categId));
		if (!$result->numRows()) return false;
		$res = $result->fetchRow();
		return $res;
	}

	function dir_remove_site($siteId) {
		$query = "delete from `tiki_directory_sites` where `siteId`=?";
		$this->query($query,array((int)$siteId));
		$query = "delete from `tiki_category_sites` where `siteId`=?";
		$this->query($query,array((int)$siteId));
	}

	function dir_add_site_to_category($siteId, $categId) {
		$query = "delete from `tiki_category_sites` where `siteId`=? and `categId`=?";
		$this->query($query,array((int)$siteId,(int)$categId));
		$query = "insert into `tiki_category_sites`(`siteId`,`categId`) values(?,?)";
		$this->query($query,array((int)$siteId,(int)$categId));
	}

	function remove_site_from_categories($siteId) {
		$query = "delete from `tiki_category_sites` where `siteId`=?";
		$this->query($query,array((int)$siteId));
	}

	function remove_site_from_category($siteId, $categId) {
		$query = "delete from `tiki_category_sites` where `siteId`=? and `categId`=?";
		$this->query($query,array((int)$siteId,(int)$categId));
	}

	function dir_remove_category($categId) {
		$parent_categId = $categId;
		$query = "select * from `tiki_directory_categories` where `parent`=?";
		$result = $this->query($query,array((int)$categId));

		while ($res = $result->fetchRow()) {
			$categId = $res["categId"];
			$this->dir_remove_category($res["categId"]);

			$query2 = "select * from `tiki_category_sites` where `categId`=?";
			$result2 = $this->query($query2,array((int)$categId));

			while ($res2 = $result2->fetchRow()) {
				$siteId = $res2["siteId"];
				$query3 = "delete from `tiki_category_sites` where `siteId`=? and `categId`=?";
				$result3 = $this->query($query3,array((int)$siteId,(int)$categId));
				$cant = $this->db->getOne("select count(*) from `tiki_category_sites` where `siteId`=?",array((int)$siteId));
				if (!$cant) {
					$this->dir_remove_site($siteId);
				}
			}
			$query4 = "delete from `tiki_related_categories` where `categId`=? or `relatedTo`=?";
			$result4 = $this->query($query4,array((int)$categId,(int)$categId));
		}
		$query = "delete from `tiki_directory_categories` where `categId`=?";
		$result = $this->query($query,array((int)$parent_categId));
		$query = "delete from `tiki_category_sites` where `categId`=?";
		$result = $this->query($query,array((int)$parent_categId));
	}

	function dir_remove_related($parent, $related) {
		$query = "delete from `tiki_related_categories` where `categId`=? and `relatedTo`=?";
		$this->query($query,array((int)$parent,(int)$related));
	}

	function dir_list_related_categories($parent, $offset, $maxRecords, $soet_mode, $find) {
		$query = "select * from `tiki_related_categories` where `categId`=?";
		$query_cant = "select count(*) from `tiki_related_categories` where `categId`=?";
		$result = $this->query($query,array((int)$parent),$maxRecords,$offset);
		$cant = $this->getOne($query_cant,array((int)$parent));
		$ret = array();

		while ($res = $result->fetchRow()) {
			$res["path"] = $this->dir_get_path_text($res["relatedTo"]);
			$ret[] = $res;
		}
		$retval = array();
		usort($ret, 'compare_paths');
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function dir_add_categ_rel($parent, $categ) {
		$query = "delete from `tiki_related_categories` where `categId`=? and `relatedTo`=?";
		$this->query($query,array((int)$parent,(int)$categ));
		$query = "insert into `tiki_related_categories`(`categId`,`relatedTo`) values(?,?)";
		$this->query($query,array((int)$parent,(int)$categ));
	}

	function dir_url_exists($url) {
		$cant = $this->db->getOne("select count(*) from `tiki_directory_sites` where `url`=?",array($url));
		return $cant;
	}

	function dir_add_site_hit($siteId) {
		global $prefs, $user;
		if ($prefs['count_admin_pvs'] == 'y' || $user != 'admin') {
			$query = "update `tiki_directory_sites` set `hits`=`hits`+1 where `siteId`=?";
			$this->query($query,array((int)$siteId));
		}
	}

	function dir_add_category_hit($categId) {
		global $prefs, $user;
		if ($prefs['count_admin_pvs'] == 'y' || $user != 'admin') {
			$query = "update `tiki_directory_categories` set `hits`=`hits`+1 where `categId`=?";
			$this->query($query,array((int)$categId));
		}
	}

	function dir_search($words, $how = 'or', $offset = 0, $maxRecords = -1, $sort_mode = 'hits_desc') {
		// First of all split the words by whitespaces building the query string
		// we'll search by name, url, description and cache, the relevance will be calculated using hits
		$words = split(' ', $words);

		$bindvars = array('y');
		for ($i = 0; $i < count($words); $i++) {
			$words[$i] = trim($words[$i]);
			$word = $words[$i];
			if (!empty($word)) {
				// Check if the term is in the stats then add it or increment it
				if ($this->db->getOne("select count(*) from `tiki_directory_search` where `term`=?",array($word))) {
					$query = "update `tiki_directory_search` set `hits`=`hits`+1 where `term`=?";
					$this->query($query,array($word));
				} else {
					$query = "insert into `tiki_directory_search`(`term`,`hits`) values(?,?)";
					$this->query($query,array($word,1));
				}
			}
			$words[$i] = " ((`name` like ?) or (`description` like ?) or (`url` like ?) or (`cache` like ?)) ";
			$bindvars[] = "%$word%";
			$bindvars[] = "%$word%";
			$bindvars[] = "%$word%";
			$bindvars[] = "%$word%";
		}

		$words = implode($how, $words);
		$query = "select * from `tiki_directory_sites` where `isValid`=? and $words  order by ".$this->convert_sortmode($sort_mode);
		$cant = $this->db->getOne("select count(*) from tiki_directory_sites where `isValid`=? and $words", $bindvars);
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$ret = array();
		while ($res = $result->fetchRow()) {
			$res["cats"] = $this->dir_get_site_categories($res["siteId"]);
			$ret[] = $res;
		}
		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function dir_search_cat($parent, $words, $how = 'or', $offset = 0, $maxRecords = -1, $sort_mode = 'hits_desc') {
		// First of all split the words by whitespaces building the query string
		// we'll search by name, url, description and cache, the relevance will be calculated using hits
		$words = split(' ', $words);
		$bindvars = array('y',(int)$parent);
		for ($i = 0; $i < count($words); $i++) {
			$words[$i] = trim($words[$i]);
			$word = $words[$i];
			// Check if the term is in the stats then add it or increment it
			if ($this->db->getOne("select count(*) from `tiki_directory_search` where `term`=?",array($word))) {
				$query = "update `tiki_directory_search` set `hits`=`hits`+1 where `term`=?";
				$this->query($query,array($word));
			} else {
				$query = "insert into `tiki_directory_search`(`term`,`hits`) values(?,?)";
				$this->query($query,array($word,1));
			}
			$words[$i] = " ((tds.`name` like ?) or (tds.`description` like ?) or (tds.`url` like ?) or (`cache` like ?)) ";
			$bindvars[] = "%$word%";
			$bindvars[] = "%$word%";
			$bindvars[] = "%$word%";
			$bindvars[] = "%$word%";
		}

		$words = implode($how, $words);
		$query = "select distinct tds.`name`, tds.`siteId`, tds.`description`, tds.`url`, tds.`country`, tds.`hits`, ";
		$query.= " tds.`created`, tds.`lastModif` from `tiki_directory_sites` tds, `tiki_category_sites` tcs, `tiki_directory_categories` tdc ";
		$query.= " where tds.`siteId`=tcs.`siteId` and tcs.`categId`=tdc.`categId` and `isValid`=? and tdc.`categId`=? and $words order by ".$this->convert_sortmode($sort_mode);
		$cant = $this->db->getOne("select count(*) from `tiki_directory_sites` tds,`tiki_category_sites` tcs,`tiki_directory_categories` tdc 
			where tds.`siteId`=tcs.`siteId` and tcs.`categId`=tdc.`categId` and `isValid`=? and tdc.`categId`=? and $words",$bindvars);
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$ret = array();
		while ($res = $result->fetchRow()) {
			$res["cats"] = $this->dir_get_site_categories($res["siteId"]);
			$ret[] = $res;
		}
		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

}
global $dbTiki;
$dirlib = new DirLib($dbTiki);

function compare_paths($p1, $p2) {
		// must be case insentive to have the same than dir_mist_sites
	return strcasecmp($p1["path"], $p2["path"]);
}

?>
