<?php
// $Header: /cvsroot/tikiwiki/tiki/lib/searchlib.php,v 1.48.2.6 2008-02-27 15:18:43 nyloth Exp $
//test

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class SearchLib extends TikiLib {
	function SearchLib($db) {
		$this->TikiLib($db);
	}

	function register_search($words) {
		$words = addslashes($words);

		$words = preg_split("/\s/", $words);

		foreach ($words as $word) {
			$word = trim($word);

			$cant = $this->getOne("select count(*) from `tiki_search_stats` where `term`=?",array($word));

			if ($cant) {
				$query = "update `tiki_search_stats` set `hits`= `hits` + 1 where `term`=?";
			} else {
				$query = "insert into `tiki_search_stats` (`term`,`hits`) values (?,1)";
			}

			$result = $this->query($query,array($word));
		}
	}

/**
 * \brief generic search function
 * \param $h the table containing the search parameter
			'from': the table or tables to be looked (ex: 'tiki_pages') (ex: 'tiki_comments c, tiki_pages p'
			'name': the column that contains the name
			'data': the column that contains the data that will be displayed as description (the first characters only)
			'hits': the column that contains the number that will be displyed as hits
			'lastModif': the column that contains the date that will be displayed
			'href': the link that will be displayed (each parameter is in the array id
			'id': the list of parameters used in href 
			'pageName': the value that will displayed as title
			'search': the columns taht are searched (the index columns)
			'orderby': a ordereing (will be added to relevance ordering)
			'permName': the permission the user needs to see each result
			'objectKey': the field corresponding to objectId according to users_objectpermissions table
			'objectType': the object type according to users_objectpermissions
			'parsed': true data needs to be parsed - optional
			'is_html': if switch html or not - optional
 * \param fulltext: if true a full text search is done, if no result, a simple search is done
 * \param $words the list of words
 * //todo: extract the short words from the list and do a simple search on them, them merge with the full search results on the remaining words
 * \return the nb of results + array('name', 'data', 'hits', 'lastModif', 'href', 'pageName', 'relevance'
**/
	function _find($h, $words = '', $offset = 0, $maxRecords = -1, $fulltext = false) {
		global $tiki_p_admin, $prefs, $userlib, $user, $categlib;
		    
		if (!is_object($categlib)) {
			require_once('lib/categories/categlib.php');
		}

		$words = trim($words);

		$sqlJoin = '';
		$sqlGroup = '';
		$sqlHaving = '';

		$bindFields = array();
		$bindJoin = array();
		$bindHaving = array();

		$sqlFields = sprintf('SELECT %s AS name, '.(isset($h['parsed'])? '%s':'LEFT(%s, 240) AS data').', %s AS hits, %s AS lastModif, %s AS pageName',
					$h['name'], $h['data'], $h['hits'], $h['lastModif'], $h['pageName']);
		if (isset($h['is_html'])) {
			$sqlFields .= ', `is_html`';
		}
		
		$id = $h['id'];
		$temp_max = count($id);
		for ($i = 0; $i < $temp_max; ++$i)
			$sqlFields .= ',' . $id[$i] . ' AS id' . ($i + 1);
		if (count($id) < 2)
			$sqlFields .= ',1 AS id2';

		$sqlFrom = ' FROM ' . $h['from'];

		$groupList = $userlib->get_user_groups($user);
		$groupStr = '';
		if (count($groupList) > 0) {
		    $groupStr = '?' . str_repeat(',?',count($groupList)-1);
		}
		
		$permName = isset($h['permName']) ? $h['permName'] : '';
		$permNameGlobal = isset($h['permNameGlobal']) ? $h['permNameGlobal'] : '';
		$permNameObj = isset($h['permNameObj']) ? $h['permNameObj'] : '';
		$objType = isset($h['objectType']) ? $h['objectType'] : '';
		$objKey = isset($h['objectKey']) ? $h['objectKey'] : '';
		$objKeyPerm = isset($h['objectKeyPerm']) ? $h['objectKeyPerm'] : '';
		$objKeyGroup = isset($h['objectKeyGroup']) ? $h['objectKeyGroup'] : '';
		$objKeyCat = isset($h['objectKeyCat']) ? $h['objectKeyCat'] : '';

		if (!empty($permName)) {
		    global $$permName;
		    $globalPerm = $$permName;
		} elseif (!empty($permNameGlobal)) {
		    global $$permNameGlobal;
		    $globalPerm = $$permNameGlobal;
		} else {
		    $globalPerm = '';
		}

		if (empty($permNameObj)) {
		    $permNameObj = $permName;
		}

		if (empty($objKeyPerm)) {
		    $objKeyPerm = $objKey;
		}
		if (empty($objKeyGroup)) {
		    $objKeyGroup = $objKey;
		}
		if (empty($objKeyCat)) {
		    $objKeyCat = $objKey;
		}
		    
		    
		$chkObjPerm = $prefs['feature_search_show_forbidden_obj'] != 'y' && $tiki_p_admin != 'y' && (!empty($permName) || (!empty($permNameGlobal) && !empty($permNameObj))) && !empty($objType) && !empty($objKeyPerm) && !empty($objKeyGroup);

		if ($chkObjPerm) {

		    $sqlJoin .= " JOIN `users_objectpermissions` u ON u.`objectId` = md5(" . $this->db->concat("'$objType'", "lower($objKeyPerm)") . ") AND u.`objectType`= ? ";
		    $bindJoin[] = $objType;
		      
			$sqlJoin = ' LEFT ' . $sqlJoin;
			$sqlFields .= ", count(u.`objectId`) as perms, max(u.`permName`=? and u.`groupName` IN ($groupStr)) as allow ";
			$bindFields[] = $permNameObj;
			$bindFields = array_merge($bindFields, $groupList);

			$sqlGroup = " GROUP BY $objKeyGroup ";
			$sqlHaving = " HAVING perms=?";
			if ($globalPerm == 'y') {
				$sqlHaving .= " or ";
			} else {
				$sqlHaving .= " and ";
			}
			$sqlHaving .= "allow=? ";

			$bindHaving = array(0,1);
	 	}

		$chkCatPerm = $prefs['feature_search_show_forbidden_cat'] != 'y' && $tiki_p_admin != 'y' && !empty($objType) && !empty($objKeyCat) && !empty($objKeyGroup) && $prefs['feature_categories'] == 'y';

		if ($chkCatPerm) {

		    $sqlJoin .= " LEFT JOIN `tiki_objects` o ON o.`type`=? AND o.`itemId`=$objKeyCat ";
		    $sqlJoin .= " LEFT JOIN `tiki_categorized_objects` co ON co.`catObjectId`=o.`objectId` ";
		    $sqlJoin .= " LEFT JOIN `tiki_category_objects` cat ON co.`catObjectId`=cat.`catObjectId` ";

		    $bindJoin[] = $objType;

		    $forbiddenCatList = $categlib->list_forbidden_categories();
		    $forbiddenCatStr = '';
		    if (count($forbiddenCatList) > 0) {
			$forbiddenCatStr = '?' . str_repeat(',?',count($forbiddenCatList)-1);
		    }
                    if( $forbiddenCatStr == "" ) $forbiddenCatStr = '\'\'';

		    $sqlFields .= ', o.`itemId` IS NOT NULL as categorized, MAX(cat.`categId` IN ('.$forbiddenCatStr.')) as forbidden ';
		    $bindFields = array_merge($bindFields, $forbiddenCatList);

		    $sqlGroup = " GROUP BY $objKeyGroup ";

		    if ($chkObjPerm) {
			$sqlHaving = " HAVING (perms=? AND (NOT categorized OR NOT forbidden OR forbidden IS NULL))";
			if ($globalPerm == 'y') {
				$sqlHaving .= " or ";
			} else {
				$sqlHaving .= " and ";
			}
			$sqlHaving .= "allow=? ";
			$bindHaving = array(0, 1);
		    } else {
			$sqlHaving = " HAVING NOT categorized OR NOT forbidden OR forbidden IS NULL ";
			$bindHaving = array();
		    }
		}

		$sqlWhere = ' WHERE ';
		$sqlWhere .= (isset($h['filter']))? $h['filter'] : '1';

		$orderby = (isset($h['orderby']) ? $h['orderby'] : $h['hits']);

		if ($fulltext) {
			$qwords = $this->db->quote($words);

			$sqlft = 'MATCH(' . join(',', $h['search']). ') AGAINST (' . $qwords . ')';
			$sqlWhere .= ' AND ' . $sqlft ;
			$sqlFields .= ', ' . $sqlft . ' AS relevance';
			$orderby = 'relevance desc, ' . $orderby;
		} else if ($words) {
			$sqlFields .= ', -1 AS relevance';

			$vwords = split(' ', $words);
			foreach ($vwords as $aword) {
				//$aword = $this->db->quote('[[:<:]]' . strtoupper($aword) . '[[:>:]]');
				$aword = preg_replace('/([\*\.\?\^\$\+\(\]\|])/', '\\\\\1', $aword);
				$aword = $this->db->quote('.*' . strtoupper($aword). '.*');

				$sqlWhere .= ' AND (';

				$temp_max = count($h['search']);
				for ($i = 0; $i < $temp_max; ++$i) {
					if ($i)
						$sqlWhere .= ' OR ';

					$sqlWhere .= 'UPPER(' . $h['search'][$i] . ') REGEXP ' . $aword;
				}

				$sqlWhere .= ')';
			}
		} else {
			$sqlFields .= ', -1 AS relevance';
		}

		$bindVars = array_merge($bindFields, $bindJoin, $bindHaving);

		$sql = $sqlFields . $sqlFrom . $sqlJoin . $sqlWhere . $sqlGroup . $sqlHaving . ' ORDER BY ' . $orderby;

		$result = $this->query($sql, $bindVars);
		$cant = $result->numRows();

		if (!$cant) { // no result
			if ($fulltext && $words) // try a simple search
				return $this->_find($h, $words, $offset, $maxRecords, false);
			else
				return array(
					'data' => array(),
					'cant' => 0
				);
		}

		$result = $this->query($sql, $bindVars, $maxRecords, $offset);
		$ret = array();

		while ($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
			//if (($prefs['feature_search_show_forbidden_cat'] == 'y' || $prefs['feature_search_show_forbidden_obj'] == 'y') && !$this->user_has_perm_on_object($user,$res['id1'],$objType,$permName) continue;
			$href = sprintf(urldecode($h['href']), urlencode($res['id1']), $res['id2']);

			// taking first 240 chars of text can bring broken html tags, better remove all tags.
			global $tikilib;
			$ret[] = array(
				'pageName' => $res["pageName"],
				'data' => $tikilib->get_snippet($res['data'], $res['is_html']),
				'hits' => $res["hits"],
				'lastModif' => $res["lastModif"],
				'href' => $href,
				'relevance' => round($res["relevance"], 3),
			);
		}

		return array(
			'data' => $ret,
			'cant' => $cant
		);
	}

	function find_wikis($words = '', $offset = 0, $maxRecords = -1, $fulltext = false) {
		global $tikilib, $prefs;
		$rv = array();
		$search_wikis_comments = array(
			'from' => '`tiki_comments` c, `tiki_pages` p',
			'name' => 'c.`title`',
			'data' => 'c.`data`',
			'hits' => 'p.`hits`', // c.hits is always null for a page comment!!
			'lastModif' => 'c.`commentDate`',
			'href' => 'tiki-index.php?page=%s#comments',
			'id' => array('p.`pageName`', 'c.`threadId`'),
			'pageName' => $this->db->concat('p.`pageName`', "': '", 'c.`title`'),
			'search' => array('c.`title`', 'c.`data`'),
			'filter' => 'c.`objectType` = "wiki page" AND p.`pageName`=c.`object`',

			'permName' => 'tiki_p_view',
			'objectType' => 'wiki page',
			'objectKey' => 'p.`pageName`',
		);
		$rv = $this->_find($search_wikis_comments, $words, $offset, $maxRecords, $fulltext);

		static $search_wikis = array(
			'from' => '`tiki_pages` p',
			'name' => '`pageName`',
			'data' => '`data`',
			'hits' => 'p.`hits`', //'pageRank', pageRank is updated not very often since the line below is in comment
			'lastModif' => '`lastModif`',
			'href' => 'tiki-index.php?page=%s',
			'id' => array('`pageName`'),
			'pageName' => '`pageName`',
			'search' => array('p.`pageName`', 'p.`description`', '`data`'),

			'permName' => 'tiki_p_view',
			'objectType' => 'wiki page',
			'objectKey' => 'p.`pageName`',
		);
		if ($prefs['search_parsed_snippet'] == 'y') {
			$search_wikis['is_html'] = 'is_html';
			$search_wikis['parsed'] = true;
		}

		// that pagerank re-calculation was speed handicap (timex30)
		//$this->pageRank();
		if (!$rv['cant'])
			return $this->_find($search_wikis, $words, $offset, $maxRecords, $fulltext);
		else {
			$data = array();
			$data = $this->_find($search_wikis, $words, $offset, $maxRecords, $fulltext);
			if (!$data['cant'])
				return $rv;
			// merge
			// todo - take away the double entries and sort but comment relevance must be lower)
			foreach ($rv['data'] as $a) {
				array_push($data['data'], $a);
			}
			$data['cant'] += $rv['cant'];
			return $data;
		}

	}
	function find_relevance_cmp($a, $b) {
		return ($a['relevance'] > $b['relevance']) ? -1 : (($a['relevance'] < $b['relevance']) ? 1 : 0);
	}

	function find_galleries($words = '', $offset = 0, $maxRecords = -1, $fulltext = false) {
		static $search_galleries = array(
			'from' => '`tiki_galleries` g',
			'name' => 'g.`name`',
			'data' => 'g.`description`',
			'hits' => 'g.`hits`',
			'lastModif' => '`lastModif`',
			'href' => 'tiki-browse_gallery.php?galleryId=%d',
			'id' => array('galleryId'),
			'pageName' => 'g.`name`',
			'search' => array('g.`name`', 'g.`description`'),

			'permName' => 'tiki_p_view_image_gallery',
			'objectType' => 'image gallery',
			'objectKey' => '`galleryId`',
		);

		return $this->_find($search_galleries, $words, $offset, $maxRecords, $fulltext);
	}

	function find_faqs($words = '', $offset = 0, $maxRecords = -1, $fulltext = false) {
		$search_faqs = array(
			'from' => '`tiki_faqs` f , `tiki_faq_questions` q',
			'name' => 'f.`title`',
			'data' => 'f.`description`',
			'hits' => 'f.`hits`',
			'lastModif' => 'f.`created`',
			'href' => 'tiki-view_faq.php?faqId=%d',
			'id' => array('f.`faqId`'),
			'pageName' => $this->db->concat('f.`title`', "': '", 'q.`question`'),
			'search' => array('q.`question`', 'q.`answer`'),
			'filter' => 'q.`faqId` = f.`faqId`',
		);

		return $this->_find($search_faqs, $words, $offset, $maxRecords, $fulltext);
	}

	function find_directory($words = '', $offset = 0, $maxRecords = -1, $fulltext = false) {
		static $search_directory = array(
			'from' => '`tiki_directory_sites` d',
			'name' => 'd.`name`',
			'data' => 'd.`description`',
			'hits' => 'd.`hits`',
			'lastModif' => 'd.`lastModif`',
			'href' => 'tiki-directory_redirect.php?siteId=%d',
			'id' => array('`siteId`'),
			'pageName' => 'd.`name`',
			'search' => array('d.`name`', 'd.`description`'),
		);

		return $this->_find($search_directory, $words, $offset, $maxRecords, $fulltext);
	}

	function find_images($words = '', $offset = 0, $maxRecords = -1, $fulltext = false) {
		static $search_images = array(
			'from' => '`tiki_images` i',
 			'name' => 'i.`name`',
	 		'data' => 'i.`description`',
		 	'hits' => 'i.`hits`',
			'lastModif' => 'i.`created`',
			'href' => 'tiki-browse_image.php?imageId=%d',
			'id' => array('`imageId`'),
		        'pageName' => 'i.`name`',
			'search' => array('i.`name`', 'i.`description`'),

			'permName' => 'tiki_p_view_image_gallery',
			'objectType' => 'image gallery',
			'objectKey' => '`galleryId`',
			
		);

 		return $this->_find($search_images, $words, $offset, $maxRecords, $fulltext);
	}

	function find_forums($words = '', $offset = 0, $maxRecords = -1, $fulltext = false, $filter='') {
		$search_forums = array(
			'from' => '`tiki_comments` c, `tiki_forums` f',
			'name' => 'c.`title`',
			'data' => 'c.`data`',
			'hits' => 'c.`hits`',
			'lastModif' => 'c.`commentDate`',
			'href' => 'tiki-view_forum_thread.php?forumId=%d&amp;comments_parentId=%d',
			'id' => array('f.`forumId`', 'c.`threadId`'),
			'pageName' => $this->db->concat('f.`name`', "': '", '`title`'),
			'search' => array('c.`title`', 'c.`data`'),
			'filter' => 'c.`objectType` = "forum" AND f.`forumId` = c.`object`',

			'permName' => 'tiki_p_forum_read',
			'objectType' => 'forum',
			'objectKey' => 'f.`forumId`',
			'objectKeyGroup' => 'c.`threadId`'
		);
		if (!empty($filter) && !empty($filter['forumId'])) {
			$search_forums['filter'] .= ' AND f.forumId='.$filter['forumId'];
		}

		return $this->_find($search_forums, $words, $offset, $maxRecords, $fulltext);
	}

	function find_files($words = '', $offset = 0, $maxRecords = -1, $fulltext = false) {
		static $search_files = array(
			'from' => '`tiki_files` f',
			'name' => 'f.`name`',
			'data' => 'f.`description`',
			'hits' => 'hits',
			'lastModif' => 'f.`created`',
			'href' => 'tiki-download_file.php?fileId=%d',
			'id' => array('`fileId`'),
			'pageName' => '`filename`',
			'search' => array('f.`name`', 'f.`description`', 'f.`search_data`'),
   			'filter' => '`archiveId` = 0',
			'permName' => 'tiki_p_download_files',
			'objectType' => 'file gallery',
			'objectKey' => '`galleryId`',
		);

		return $this->_find($search_files, $words, $offset, $maxRecords, $fulltext);
	}

	function find_blogs($words = '', $offset = 0, $maxRecords = -1, $fulltext = false) {
		static $search_blogs = array(
			'from' => '`tiki_blogs` b',
			'name' => '`title`',
			'data' => 'b.`description`',
			'hits' => 'b.`hits`',
			'lastModif' => '`lastModif`',
			'href' => 'tiki-view_blog.php?blogId=%d',
			'id' => array('`blogId`'),
			'pageName' => '`title`',
			'search' => array('`title`', 'b.`description`'),
			'filter' => '`use_find` = "y"',

			'permName' => 'tiki_p_read_blog',
			'objectType' => 'blog',
			'objectKey' => '`blogId`',
		);

		return $this->_find($search_blogs, $words, $offset, $maxRecords, $fulltext);
	}

	function find_articles($words = '', $offset = 0, $maxRecords = -1, $fulltext = false) {
		static $search_articles = array(
			'from' => '`tiki_articles` a',
			'name' => 'a.`title`',
			'data' => 'a.`heading`',
			'hits' => 'a.`nbreads`',
			'lastModif' => 'a.`publishDate`',
			'href' => 'tiki-read_article.php?articleId=%d',
			'id' => array('a.`articleId`'),
			'pageName' => 'a.`title`',
			'search' => array('a.`title`', 'a.`heading`', 'a.`body`'),

			'permNameGlobal' => 'tiki_p_read_article',
			'permNameObj' => 'tiki_p_topic_read',
			'objectType' => 'topic',
			'objectKeyPerm' => 'a.`topicId`',
			'objectKeyGroup' => 'a.`articleId`',
			'objectKeyCat' => 'a.`articleId`',
		);

		return $this->_find($search_articles, $words, $offset, $maxRecords, $fulltext);
	}

	function find_posts($words = '', $offset = 0, $maxRecords = -1, $fulltext = false) {
	  global $user;

		# TODO localize?
		$pagename = "CONCAT(b.`title`, ' - ', p.`user`)";

		$search_posts = array(
				      // why using left join here?
				      //'from' => 'tiki_blog_posts p LEFT JOIN tiki_blogs b ON b.blogId = p.blogId',
			'from' => '`tiki_blog_posts` p, `tiki_blogs` b',
			'name' => 'p.`data`', # to simplify the logic, won't hurt performance
			'data' => 'p.`data`',
			'hits' => '1',
			'orderby' => 'p.`created` DESC',
			'lastModif' => 'p.`created`',
			# TODO double up %'s from urlencode() return value
			'href' => 'tiki-view_blog.php?blogId=%d&amp;find=' . urlencode($words),
			'id' => array('p.`blogId`'),
			'pageName' => $pagename,
			'search' => array('p.`data`','p.`title`'),
			'filter' => 'b.`use_find` = "y" AND b.`blogId` = p.`blogId`',

			'permName' => 'tiki_p_read_blog',
			'objectType' => 'blog',
			'objectKey' => 'b.`blogId`',
		);

		return $this->_find($search_posts, $words, $offset, $maxRecords, $fulltext);
	}
	function find_trackers($words = '', $offset = 0, $maxRecords = -1, $fulltext = false) {
		global $trklib; require_once('lib/trackers/trackerlib.php');
		global $tiki_p_view_trackers_pending; global $tiki_p_view_trackers_closed;
		static $search_trackers = array(
			'from' => '`tiki_tracker_item_fields` ttif LEFT JOIN `tiki_tracker_items` tti ON (ttif.`itemId`=tti.`itemId`) LEFT JOIN `tiki_trackers` tt ON (tti.`trackerId`= tt.`trackerId`) LEFT JOIN `tiki_tracker_fields` ttf ON (ttf.`fieldId`= ttif.`fieldId`)',
			'name' => 'ttif.`itemId`',
			'data' => 'tt.`name`',
			'hits' => 'tt.`trackerId`',
			'lastModif' => 'tti.`lastModif`',
			'href' => 'tiki-view_tracker_item.php?itemId=%d',
			'id' => array('tti.`itemId`'),
			'pageName' => 'tti.`itemId`',
			'search' => array('`value`'),
			'filter' => 'ttf.`isSearchable` = "y"',
			'permName' => 'tiki_p_view_trackers',
			'objectType' => 'tracker',
			'objectKey' => 'tt.`trackerId`',
			'objectKeyPerm' => 'tt.`trackerId`',
			'objectKeyCat' => 'tt.`trackerId`',
			'objectKeyGroup' => 'tt.`trackerId`',
		);
		if ($tiki_p_view_trackers_closed != 'y')
			$search_trackers['filter'] .= " AND tti.`status` != 'c'";
		if ($tiki_p_view_trackers_pending != 'y')
			$search_trackers['filter'] .= " AND tti.`status` != 'p'";
		$ret = $this->_find($search_trackers, $words, $offset, $maxRecords, $fulltext);
		foreach ($ret['data'] as $i=>$res) {
			$ret['data'][$i]['pageName'] = '(#'.$res['pageName'].')'.$trklib->get_isMain_value($res['hits'], $res['pageName']);
			$ret['data'][$i]['hits'] = 'Unknown'; 
		}
		return $ret;
	}

	function find_pages($words = '', $offset = 0, $maxRecords = -1, $fulltext = false) {
		$data = array();

		$cant = 0;
		
		global $prefs, $tiki_p_view_directory, $tiki_p_read_article, $tiki_p_view_faqs, $tiki_p_view_trackers;
		
		if ($prefs['feature_wiki'] == 'y') {
		$rv = $this->find_wikis($words, $offset, $maxRecords, $fulltext);
		foreach ($rv['data'] as $a) {
			$a['type'] = tra('Wiki');

			array_push($data, $a);
		}
		
		$cant += $rv['cant'];
		}
		
		if ($prefs['feature_galleries'] == 'y') {
		$rv = $this->find_galleries($words, $offset, $maxRecords, $fulltext);

		foreach ($rv['data'] as $a) {
			$a['type'] = tra('Gallery');

			array_push($data, $a);
		}

		$cant += $rv['cant'];
		}
		
		if ($prefs['feature_faqs'] == 'y' && $tiki_p_view_faqs == 'y') {
		$rv = $this->find_faqs($words, $offset, $maxRecords, $fulltext);

		foreach ($rv['data'] as $a) {
			$a['type'] = tra('FAQ');

			array_push($data, $a);
		}

		$cant += $rv['cant'];
		}
		
		if ($prefs['feature_galleries'] == 'y') {
		$rv = $this->find_images($words, $offset, $maxRecords, $fulltext);

		foreach ($rv['data'] as $a) {
			$a['type'] = tra('Image');

			array_push($data, $a);
		}

		$cant += $rv['cant'];
		}
		
		if ($prefs['feature_forums'] == 'y') {
		$rv = $this->find_forums($words, $offset, $maxRecords, $fulltext);

		foreach ($rv['data'] as $a) {
			$a['type'] = tra('Forum');

			array_push($data, $a);
		}

		$cant += $rv['cant'];
		}
		
		if ($prefs['feature_file_galleries'] == 'y') {
		$rv = $this->find_files($words, $offset, $maxRecords, $fulltext);

		foreach ($rv['data'] as $a) {
			$a['type'] = tra('File');

			array_push($data, $a);
		}

		$cant += $rv['cant'];
		}
		
		if ($prefs['feature_blogs'] =='y') {
		$rv = $this->find_blogs($words, $offset, $maxRecords, $fulltext);

		foreach ($rv['data'] as $a) {
			$a['type'] = tra('Blog');

			array_push($data, $a);
		}

		$cant += $rv['cant'];
		}
		
		if ($prefs['feature_articles'] == 'y' && $tiki_p_read_article == 'y') {
		$rv = $this->find_articles($words, $offset, $maxRecords, $fulltext);

		foreach ($rv['data'] as $a) {
			$a['type'] = tra('Article');

			array_push($data, $a);
		}

		$cant += $rv['cant'];
		}
		
		if ($prefs['feature_blogs'] == 'y') {
		$rv = $this->find_posts($words, $offset, $maxRecords, $fulltext);

		foreach ($rv['data'] as $a) {
			$a['type'] = tra('Blog post');

			array_push($data, $a);
		}

		$cant += $rv['cant'];
		}

		if ($prefs['feature_directory'] == 'y' && $tiki_p_view_directory == 'y') {
		$rv = $this->find_directory($words, $offset, $maxRecords, $fulltext);

		foreach ($rv['data'] as $a) {
			$a['type'] = tra('Directory');
			$a['relevance'] *= 0.7; // decrease artifically the relevance because as description is shorter than a wiki data, a directory is returned before wiki page

			array_push($data, $a);
		}

		$cant += $rv['cant'];
		}

		if ($prefs['feature_trackers'] == 'y' && $tiki_p_view_trackers == 'y') {
		$rv = $this->find_trackers($words, $offset, $maxRecords, $fulltext);

		foreach ($rv['data'] as $a) {
			$a['type'] = tra('Tracker item');
			array_push($data, $a);
		}

		$cant += $rv['cant'];
		}

		if ($fulltext) {
			function find_pages_cmp($a, $b) {
				return ($a['relevance'] > $b['relevance']) ? -1 : (($a['relevance'] < $b['relevance']) ? 1 : 0);
			}

			usort($data, 'find_pages_cmp');
		/*	# this doesn't work, because 'hits' aren't the same across different sections, right?
	} else {
	  function find_pages_cmp ($a, $b) {
		return ($a['hits'] > $b['hits']) ? -1 : (($a['hits'] < $b['hits']) ? 1 : 0);
	  }

	  usort ($data, 'find_pages_cmp');
*/
		}

		return array(
			'data' => $data,
			'cant' => $cant,
		);
	}
} # class SearchLib

?>
