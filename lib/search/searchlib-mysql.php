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

/**
 * SearchLib
 *
 * @uses TikiLib
 */
class SearchLib extends TikiLib
{
    /**
     * @param $words
     */
    function register_search($words)
	{
		$words = addslashes($words);

		$words = preg_split("/\s/", $words);

		$stats = TikiLib::lib('searchstats');
		foreach ($words as $word) {
			$stats->register_term_hit($term);
		}
	}

/**
 * \brief generic search function
 * \param $h the table containing the search parameter
			'from': the table or tables to be looked (ex: 'tiki_pages') (ex: 'tiki_comments c, tiki_pages p'
			'name': the column that contains the name
			'data': the column that contains the data that will be displayed as description (the first characters only)
			'hits': the column that contains the number that will be displayed as hits
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
	function _find(
					$h,
					$words = '',
					$offset = 0,
					$maxRecords = -1,
					$fulltext = false,
					$filter='',
					$boolean='n',
					$type='Tiki',
					$searchDate = 0,
					$categId = 0)
	{
		global $tiki_p_admin, $prefs, $user;
		$userlib = TikiLib::lib('user');

		if (!is_int($searchDate) && !ctype_digit($searchDate)) {
			exit("Error: searchDate not an integer");
		}

		$categlib = TikiLib::lib('categ');

		$words = trim($words);

		$sqlJoin = '';
		$sqlCategJoin = '';
		$sqlCategWhere = '';
		$sqlGroup = '';
		$sqlHaving = '';

		$bindFields = array();
		$bindCateg = array();
		$bindJoin = array();
		$bindHaving = array();

		if ( $categId ) {
			$jail = $categId;
		} else {
			$jail = $categlib->get_jail();
		}
		if ( $jail ) {
			$categlib->getSqlJoin($jail, $h['objectType'], $h['objectKey'], $sqlCategJoin, $sqlCategWhere, $bindCateg);
		}

		$sqlFields = sprintf(
			'SELECT DISTINCT
				%s AS name,
				' . (isset($h['parsed']) ? '%s' : 'LEFT(%s, 240) AS data') . ',
				%s AS hits,
				%s AS lastModif,
				%s AS pageName'
				. ($h['objectType'] == 'wiki page' ? ',outputType ' : ''),
			$h['name'],
			$h['data'],
			$h['hits'],
			$h['lastModif'],
			$h['pageName']
		);

		if (isset($h['cache'])) {
			$sqlFields .= sprintf(', %s AS cache', $h['cache']);
		}

		if (isset($h['is_html'])) {
			$sqlFields .= ', `is_html`';
		}
		if (!empty($h['parent']))
			$sqlFields .= ', '.$h['parent'];

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
			$groupStr = '?' . str_repeat(',?', count($groupList)-1);
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

		if (!empty($h['parentJoin']))
			$sqlJoin .= ' '.$h['parentJoin'];

		if ($h['objectType'] == 'wiki page') {
			$sqlJoin .= ' left join `tiki_output` on `tiki_output`.`entityId` = p.`pageName` ';
		}

		$sqlWhere = ' WHERE ';
		$sqlWhere .= (isset($h['filter']))? $h['filter'] : '1';

		$orderby = (isset($h['orderby']) ? $h['orderby'] : $h['hits']);

		if ( $searchDate >0 and !empty($h['lastModif']) ) {
			$sqlWhere .= ' AND '. $h['lastModif']. " >= unix_timestamp(date_sub(now(), interval ". $searchDate . " month)) ";
		}

		if ($fulltext) {
			$words = html_entity_decode($words); // to have the "
			$qwords = $this->qstr($words);

			$sqlft = 'MATCH(' . join(',', $h['search']). ') AGAINST (' . $qwords ;
			if ($boolean == 'y')
				$sqlft .= ' IN BOOLEAN MODE';
			$sqlft .= ')';
			$sqlWhere .= ' AND ' . $sqlft ;
			$sqlFields .= ', ' . $sqlft . ' AS relevance';
			$orderby = 'relevance desc, ' . $orderby;
		} else if ($words) {
			$sqlFields .= ', -1 AS relevance';

			$vwords = preg_split('/ /', $words);
			foreach ($vwords as $aword) {
				//$aword = $this->qstr('[[:<:]]' . strtoupper($aword) . '[[:>:]]');
				$aword = preg_replace('/([\*\.\?\^\$\+\(\]\|])/', '\\\\\1', $aword);
				$aword = $this->qstr('.*' . strtoupper($aword). '.*');

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

		$bindVars = array_merge($bindFields, $bindJoin, $bindCateg, $bindHaving);

		$sql = $sqlFields . $sqlFrom . $sqlJoin . $sqlCategJoin . $sqlWhere . $sqlCategWhere . $sqlGroup . $sqlHaving . ' ORDER BY ' . $orderby;

		$result = $this->query($sql, $bindVars);
		//echo $sql; print_r($bindvars);

		$cant = $result->numRows();

		if (!$cant && $boolean != 'y') { // no result

			if ($fulltext && $words) { // try a simple search
				return $this->_find($h, $words, $offset, $maxRecords, false, $filter, $boolean, $type, $searchDate, $categId);
			} else {

				return array(
					'data' => array(),
					'cant' => 0
				);
			}
		}

		$chkObjPerm = $prefs['feature_search_show_forbidden_obj'] != 'y' &&
									$tiki_p_admin != 'y' &&
									(!empty($permName) || (!empty($permNameGlobal) && !empty($permNameObj))) &&
									!empty($objType) &&
									!empty($objKeyPerm) &&
									!empty($objKeyGroup);

		$chkCatPerm = $prefs['feature_search_show_forbidden_cat'] != 'y' &&
									$tiki_p_admin != 'y' &&
									!empty($objType) &&
									!empty($objKeyCat) &&
									!empty($objKeyGroup) &&
									$prefs['feature_categories'] == 'y';


		$result = $this->query($sql, $bindVars, $maxRecords, $offset);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$href = sprintf(urldecode($h['href']), urlencode($res['id1']), $res['id2']);

			// taking first 240 chars of text can bring broken html tags, better remove all tags.
			global $tikilib, $user;


			//if user is null (anonymous) and there is cache, deliver that, otherwise lets get a parsed snippet
			//this cuts down on resource usage considerably when pages are cached
			$data = '';
			if ($user === null && !empty($res['cache'])) {
				$data = substr($tikilib->strip_tags($res['cache']), 0, 240);
			}
			else {
				$data = $tikilib->get_snippet($res['data'], $res['outputType'], ! empty($res['is_html']));
			}

			$r = array(
				'name' => $res['name'],
				'pageName' => $res["pageName"],
				'data' => $data,
				'hits' => $res["hits"],
				'lastModif' => $res["lastModif"],
				'href' => $href,
				'relevance' => round($res["relevance"], 3),
				'type' => $type,
				'location' => $type
			);

			if ($h['objectType'] == 'wiki page') {
				$r['outputType'] = $res['outputType'];
			}

			if (!empty($h['parent'])) {
				$r['parentName'] = $res['parentName'];
				$r['location'] .= "::" . $res['parentName'];
				$r['parentHref'] = str_replace('$', '?', $res['parentHref']);
			}

			/* New perms checks for 4.0 - by jonnyb Friday the 13th, 2009
			 * Ok on wiki pages and some others - problems with filegals (& probably others) TODO TODO TODO for 4.1 */

			if ($chkObjPerm || $chkCatPerm) {
				//if ($type == 'File Gallery') {
				//	$context = array( 'type' => $objType, 'object' => $res['parentName'] );
				//} else {
					$context = array('type' => $objType, 'object' => $res['id1']);
				//}

				$accessor = Perms::get($context);
				$accessor->setGroups($groupList);

				$ok = true;	// should default to ok?

				if (!empty($permName)) {
					$ok = $accessor->$permName;
				} else if (!empty($permNameObj)) {
					$ok = $accessor->$permNameObj;
				} else if (!empty($permNameGlobal)) {
					$ok = $accessor->$permNameGlobal;
				}

				if ($ok) {
					$ret[] = $r;
				}
			} else {
				$ret[] = $r;
			}
		}

		return array(
			'data' => $ret,
			'cant' => $cant
		);
	}

    /**
     * @param string $words
     * @param int $offset
     * @param $maxRecords
     * @param bool $fulltext
     * @param string $filter
     * @param string $boolean
     * @param int $searchDate
     * @param string $lang
     * @param int $categId
     * @return array
     */
    function find_wikis($words = '', $offset = 0, $maxRecords = -1, $fulltext = false, $filter='', $boolean='n', $searchDate = 0, $lang='', $categId = 0)
	{
		global $tikilib, $prefs;
		$rv = array();
		$search_wikis_comments = array(
			'from' => '`tiki_comments` c, `tiki_pages` p',
			'name' => 'c.`title`',
			'data' => 'c.`data`',
			'hits' => 'p.`hits`', // c.hits is always null for a page comment!!
			'lastModif' => 'c.`commentDate`',
			'id' => array('p.`pageName`', 'c.`threadId`'),
			'pageName' => $this->concat('p.`pageName`', "': '", 'c.`title`'),
			'search' => array('c.`title`', 'c.`data`'),
			'filter' => 'c.`objectType` = \'wiki page\' AND p.`pageName`=c.`object`',

			'permName' => 'tiki_p_view',
			'objectType' => 'wiki page',
			'objectKey' => 'p.`pageName`',
		);

		if (!empty($lang)) {
			$lang = addslashes($lang);
			$search_wikis_comments['filter'] .= " AND p.`lang`='$lang'";
		}

		$search_wikis_comments['href'] = $prefs['feature_sefurl'] == 'y'? '%s#comments': 'tiki-index.php?page=%s#comments';
		$rv = $this->_find(
			$search_wikis_comments,
			$words,
			$offset,
			$maxRecords,
			$fulltext,
			$filter,
			$boolean,
			tra('Wiki Comment'),
			$searchDate,
			$categId
		);

		static $search_wikis = array(
			'from' => '`tiki_pages` p',
			'name' => '`pageName`',
			'data' => '`data`',
			'cache' => '`cache`',
			'hits' => 'p.`hits`', //'pageRank', pageRank is updated not very often since the line below is in comment
			'lastModif' => '`lastModif`',
			'id' => array('`pageName`'),
			'pageName' => '`pageName`',
			'search' => array('p.`pageName`', 'p.`description`', '`data`'),

			'permName' => 'tiki_p_view',
			'objectType' => 'wiki page',
			'objectKey' => 'p.`pageName`',
		);
		if (!empty($lang)) {
			$lang = addslashes($lang);
			$search_wikis['filter'] = " p.`lang`='$lang'";
		}
		$search_wikis['href'] = $prefs['feature_sefurl'] == 'y'? '%s': 'tiki-index.php?page=%s';
		if ($prefs['search_parsed_snippet'] == 'y') {
			$search_wikis['is_html'] = 'is_html';
			$search_wikis['parsed'] = true;
		}

		// that pagerank re-calculation was speed handicap (timex30)
		//$this->pageRank();
		if (!$rv['cant'])
			$data = $this->_find($search_wikis, $words, $offset, $maxRecords, $fulltext, $filter, $boolean, tra('Wiki'), $searchDate, $categId);
		else {
			$data = array();
			$data = $this->_find($search_wikis, $words, $offset, $maxRecords, $fulltext, $filter, $boolean, tra('Wiki'), $searchDate, $categId);
			if (!$data['cant'])
				return $rv;
			// merge
			// todo - take away the double entries and sort but comment relevance must be lower)
			foreach ($rv['data'] as $a) {
				array_push($data['data'], $a);
			}
			$data['cant'] += $rv['cant'];
		}

		return $data;

	}

    /**
     * @param $a
     * @param $b
     * @return int
     */
    function find_relevance_cmp($a, $b)
	{
		return ($a['relevance'] > $b['relevance']) ? -1 : (($a['relevance'] < $b['relevance']) ? 1 : 0);
	}

    /**
     * @param string $words
     * @param int $offset
     * @param $maxRecords
     * @param bool $fulltext
     * @param string $filter
     * @param string $boolean
     * @param int $searchDate
     * @param int $categId
     * @return array
     */
    function find_calendars($words = '', $offset = 0, $maxRecords = -1, $fulltext = false, $filter='', $boolean='n', $searchDate = 0, $categId = 0)
	{
		static $search_calendar = array(
			'from' => '`tiki_calendar_items` c',
			'name' => 'c.`name`',
			'data' => 'c.`description`',
			'hits' => 'c.`priority`',
			'lastModif' => 'c.`lastmodif`',
			'href' => 'tiki-calendar_edit_item.php?viewcalitemId=%d',
			'id' => array('calitemId'),
			'pageName' => 'c.`name`',
			'search' => array('c.`name`', 'c.`description`'),

			'permName' => 'tiki_p_view_calendar',
			'objectType' => 'calendar',
			'objectKey' => 'c.`calendarId`',
			'parent' => 'tc.`name` as parentName, concat(\'tiki-calendar.php$calIds[]=\', tc.`calendarId`,\'&todate=\',c.`start`) as parentHref',
			'parentJoin' => 'LEFT JOIN `tiki_calendars` tc ON tc.`calendarId` = c.`calendarId`',
		);

		return $this->_find(
			$search_calendar,
			$words,
			$offset,
			$maxRecords,
			$fulltext,
			$filter,
			$boolean,
			tra('Calendar item'),
			$searchDate,
			$categId
		);
	}

    /**
     * @param string $words
     * @param int $offset
     * @param $maxRecords
     * @param bool $fulltext
     * @param string $filter
     * @param string $boolean
     * @param int $searchDate
     * @param int $categId
     * @return array
     */
    function find_galleries($words = '', $offset = 0, $maxRecords = -1, $fulltext = false, $filter='', $boolean='n', $searchDate = 0, $categId = 0)
	{
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

		return $this->_find($search_galleries, $words, $offset, $maxRecords, $fulltext, $filter, $boolean, tra('Gallery'), $searchDate, $categId);
	}

    /**
     * @param string $words
     * @param int $offset
     * @param $maxRecords
     * @param bool $fulltext
     * @param string $filter
     * @param string $boolean
     * @param int $searchDate
     * @param int $categId
     * @return array
     */
    function find_faqs($words = '', $offset = 0, $maxRecords = -1, $fulltext = false, $filter='', $boolean='n', $searchDate = 0, $categId = 0)
	{
		$search_faqs = array(
			'from' => '`tiki_faq_questions` q, `tiki_faqs` f',
			'name' => 'f.`title`',
			'data' => 'f.`description`',
			'hits' => 'f.`hits`',
			'lastModif' => 'f.`created`',
			'href' => 'tiki-view_faq.php?faqId=%d',
			'id' => array('f.`faqId`'),
			'pageName' => $this->concat('f.`title`', "': '", 'q.`question`'),
			'search' => array('q.`question`', 'q.`answer`'),
			'filter' => 'q.`faqId` = f.`faqId`',
			'permName' => 'tiki_p_view_faqs',
			'objectType' => 'faq',
			'objectKey' => 'f.`faqId`',
		);

		return $this->_find($search_faqs, $words, $offset, $maxRecords, $fulltext, $filter, $boolean, tra('FAQ'), $searchDate, $categId);
	}

    /**
     * @param string $words
     * @param int $offset
     * @param $maxRecords
     * @param bool $fulltext
     * @param string $filter
     * @param string $boolean
     * @param int $searchDate
     * @param int $categId
     * @return array
     */
    function find_directory($words = '', $offset = 0, $maxRecords = -1, $fulltext = false, $filter='', $boolean='n', $searchDate = 0, $categId = 0)
	{
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
			'permName' => 'tiki_p_view_directory',
			'objectType' => 'directory',
			'objectKey' => 'd.`siteId`',
		);
		global $tiki_p_admin;
		if ($tiki_p_admin != 'y') {
			$search_directory['filter'] = "d.`isValid` = 'y'";
		}

		return $this->_find(
			$search_directory,
			$words,
			$offset,
			$maxRecords,
			$fulltext,
			$filter,
			$boolean,
			tra('Directory'),
			$searchDate,
			$categId
		);
	}

    /**
     * @param string $words
     * @param int $offset
     * @param $maxRecords
     * @param bool $fulltext
     * @param string $filter
     * @param string $boolean
     * @param int $searchDate
     * @param int $categId
     * @return array
     */
    function find_images($words = '', $offset = 0, $maxRecords = -1, $fulltext = false, $filter='', $boolean='n', $searchDate = 0, $categId = 0)
	{
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

		return $this->_find($search_images, $words, $offset, $maxRecords, $fulltext, $filter, $boolean, tra('Image'), $searchDate, $categId);
	}

    /**
     * @param string $words
     * @param int $offset
     * @param $maxRecords
     * @param bool $fulltext
     * @param string $filter
     * @param string $boolean
     * @param int $searchDate
     * @param int $categId
     * @return array
     */
    function find_forums($words = '', $offset = 0, $maxRecords = -1, $fulltext = false, $filter='', $boolean='n', $searchDate = 0, $categId = 0)
	{
		$search_forums = array(
			'from' => '`tiki_comments` c, `tiki_forums` f',
			'name' => 'c.`title`',
			'data' => 'c.`data`',
			'hits' => 'c.`hits`',
			'lastModif' => 'c.`commentDate`',
			'href' => 'tiki-view_forum_thread.php?forumId=%d&amp;comments_parentId=%d',
			'id' => array('f.`forumId`', 'c.`threadId`'),
			'pageName' => $this->concat('f.`name`', "': '", '`title`'),
			'search' => array('c.`title`', 'c.`data`'),
			'filter' => 'c.`objectType` = \'forum\' AND f.`forumId` = c.`object`',

			'permName' => 'tiki_p_forum_read',
			'objectType' => 'forum',
			'objectKey' => 'f.`forumId`',
			'objectKeyGroup' => 'c.`threadId`'
		);
		if (!empty($filter) && !empty($filter['forumId'])) {
			$search_forums['filter'] .= ' AND f.forumId='.$filter['forumId'];
		}

		return $this->_find($search_forums, $words, $offset, $maxRecords, $fulltext, $filter, $boolean, tra('Forum'), $searchDate, $categId);
	}

    /**
     * @param string $words
     * @param int $offset
     * @param $maxRecords
     * @param bool $fulltext
     * @param string $filter
     * @param string $boolean
     * @param int $searchDate
     * @return array
     */
    function find_files($words = '', $offset = 0, $maxRecords = -1, $fulltext = false, $filter='', $boolean='n', $searchDate = 0)
	{
		static $search_files = array(
			'from' => '`tiki_files` f',
			'parent' => 'tfg.`name` as parentName, concat(\'tiki-list_file_gallery.php$galleryId=\', f.`galleryId`) as parentHref',
			'name' => 'f.`name`',
			'data' => 'f.`description`',
			'hits' => 'f.hits',
			'lastModif' => 'f.`created`',
			'href' => 'tiki-download_file.php?fileId=%d',
			'id' => array('`fileId`'),
			'pageName' => '`filename`',
			'search' => array('f.`name`', 'f.`description`', 'f.`search_data`', 'f.`filename`'),
			'filter' => '`archiveId` = 0',
			'permName' => 'tiki_p_download_files',
			'objectType' => 'file gallery',
			'objectKey' => 'f.`galleryId`',
			'objectKeyGroup' => 'f.`fileId`',
			'parentJoin' => 'LEFT JOIN `tiki_file_galleries` tfg ON tfg.`galleryId` = f.`galleryId`',
		);
		if (!empty($filter['galleryId'])) {
			$search_files['filter'] .= ' AND f.galleryId='.$filter['galleryId'];
		}

		return $this->_find($search_files, $words, $offset, $maxRecords, $fulltext, $filter, $boolean, tra('File Gallery'), $searchDate);
	}

    /**
     * @param string $words
     * @param int $offset
     * @param $maxRecords
     * @param bool $fulltext
     * @param string $filter
     * @param string $boolean
     * @param int $searchDate
     * @param int $categId
     * @return array
     */
    function find_blogs($words = '', $offset = 0, $maxRecords = -1, $fulltext = false, $filter='', $boolean='n', $searchDate = 0, $categId = 0)
	{
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
			'filter' => '`use_find` = \'y\'',

			'permName' => 'tiki_p_read_blog',
			'objectType' => 'blog',
			'objectKey' => '`blogId`',
		);
		$res = $this->_find($search_blogs, $words, $offset, $maxRecords, $fulltext, $filter, $boolean, tra('Blog'), $searchDate, $categId);
		global $user;
		include_once('tiki-sefurl.php');
		foreach ($res['data'] as $i=>$r) {
			$res['data'][$i]['href'] = filter_out_sefurl($r['href'], 'blog', $r['pageName']);
		}

		return $res;
	}

    /**
     * @param string $words
     * @param int $offset
     * @param $maxRecords
     * @param bool $fulltext
     * @param string $filter
     * @param string $boolean
     * @param int $searchDate
     * @param int $categId
     * @param string $lang
     * @return array
     */
    function find_articles($words = '', $offset = 0, $maxRecords = -1, $fulltext = false, $filter='', $boolean='n', $searchDate = 0, $categId = 0, $lang = '')
	{
		static $search_articles = array(
			'from' => '`tiki_articles` a',
			'name' => 'a.`topicId`',
			'data' => 'a.`heading`',
			'hits' => 'a.`nbreads`',
			'lastModif' => 'a.`publishDate`',
			'href' => 'tiki-read_article.php?articleId=%d',
			'id' => array('a.`articleId`'),
			'pageName' => 'a.`title`',
			'search' => array('a.`title`', 'a.`heading`', 'a.`body`'),
			'permName' => 'tiki_p_read_article',
			'objectType' =>'article',
			'objectKey'=>'`articleId`'
		);

		if (!empty($lang)) {
			$lang = addslashes($lang);
			$search_articles['filter'] = " a.`lang`='$lang'";
		}

		$res = $this->_find(
			$search_articles,
			$words,
			$offset,
			$maxRecords,
			$fulltext,
			$filter,
			$boolean,
			tra('Article'),
			$searchDate,
			$categId
		);

		$ret = array('cant'=>$res['cant'], 'data'=>array());
		global $user;
		include_once('tiki-sefurl.php');

		foreach ($res['data'] as $r) {
			$objperm = $this->get_perm_object($r['name'], 'article', '', false);
			if (empty($r['name']) || $objperm['tiki_p_topic_read'] == 'y') {
				$r['name'] = $r['pageName'];
				$r['href'] = filter_out_sefurl($r['href'], 'article', $r['pageName']);
				$ret['data'][] = $r;
			} else {
				--$ret['cant'];
			}
		}
		return $ret;
	}

    /**
     * @param string $words
     * @param int $offset
     * @param $maxRecords
     * @param bool $fulltext
     * @param string $filter
     * @param string $boolean
     * @param int $searchDate
     * @param int $categId
     * @return array
     */
    function find_posts($words = '', $offset = 0, $maxRecords = -1, $fulltext = false, $filter='', $boolean='n', $searchDate = 0, $categId = 0)
	{
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
			'filter' => 'b.`use_find` = \'y\' AND b.`blogId` = p.`blogId`',

			'permName' => 'tiki_p_read_blog',
			'objectType' => 'blog',
			'objectKey' => 'b.`blogId`',
		);

		return $this->_find($search_posts, $words, $offset, $maxRecords, $fulltext, $filter, $boolean, tra('Blog post'), $searchDate, $categId);
	}

    /**
     * @param string $words
     * @param int $offset
     * @param $maxRecords
     * @param bool $fulltext
     * @param string $filter
     * @param string $boolean
     * @param int $searchDate
     * @param int $categId
     * @return array
     */
    function find_trackers($words = '', $offset = 0, $maxRecords = -1, $fulltext = false, $filter='', $boolean='n', $searchDate = 0, $categId = 0)
	{
		$trklib = TikiLib::lib('trk');
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
			'filter' => 'ttf.`isSearchable` = \'y\'',
			'permName' => 'tiki_p_view_trackers',
			'objectType' => 'tracker',
			'objectKey' => 'tt.`trackerId`',
			'objectKeyPerm' => 'tt.`trackerId`',
			'objectKeyCat' => 'tt.`trackerId`',
			'objectKeyGroup' => 'ttif.`itemId`',
		);

		if ($tiki_p_view_trackers_closed != 'y')
			$search_trackers['filter'] .= " AND tti.`status` != 'c'";

		if ($tiki_p_view_trackers_pending != 'y')
			$search_trackers['filter'] .= " AND tti.`status` != 'p'";

		$ret = $this->_find($search_trackers, $words, $offset, $maxRecords, $fulltext, $filter, $boolean, tra('Tracker item'), $searchDate, $categId);
		$retFinal = array();
		$itemFinal = array();

		foreach ($ret['data'] as $i=>$res) {
			include_once('tiki-sefurl.php');
			$res['href'] = filter_out_sefurl($res['href'], 'trackeritem', $res['name']);
			if (($j = array_search($res['name'], $itemFinal)) === false) {
				$res['pageName'] = '(#'.$res['pageName'].') '.$trklib->get_isMain_value($res['hits'], $res['pageName']);
				$res['hits'] = 'Unknown';
				$itemFinal[] = $res['name'];
				$retFinal[] = $res;
			} else {
				$retFinal[$j]['relevance'] += $res['relevance'];
			}
		}
		return array('cant'=> count($retFinal), 'data'=> $retFinal);
	}

    /**
     * @param string $words
     * @param int $offset
     * @param $maxRecords
     * @param bool $fulltext
     * @param string $filter
     * @param string $boolean
     * @param int $searchDate
     * @param int $categId
     * @param string $lang
     * @return array
     */
    function find_pages($words = '', $offset = 0, $maxRecords = -1, $fulltext = false, $filter='', $boolean='n', $searchDate = 0, $categId = 0, $lang = '')
	{
		$data = array();
		$cant = 0;

		global $prefs, $tiki_p_view_directory, $tiki_p_read_article, $tiki_p_view_faqs, $tiki_p_view_trackers;

		if ($prefs['feature_wiki'] == 'y') {
			$rv = $this->find_wikis($words, $offset, $maxRecords, $fulltext, $filter, $boolean, $searchDate, $lang, $categId);

			$data = array_merge($data, $rv['data']);
			$cant += $rv['cant'];
		}

		if ($prefs['feature_galleries'] == 'y') {
			$rv = $this->find_galleries($words, $offset, $maxRecords, $fulltext, $filter, $boolean, $searchDate, $categId);

			$data = array_merge($data, $rv['data']);
			$cant += $rv['cant'];
		}

		if ($prefs['feature_faqs'] == 'y' && $tiki_p_view_faqs == 'y') {
			$rv = $this->find_faqs($words, $offset, $maxRecords, $fulltext, $filter, $boolean, $searchDate, $categId);

			$data = array_merge($data, $rv['data']);
			$cant += $rv['cant'];
		}

		if ($prefs['feature_galleries'] == 'y') {
			$rv = $this->find_images($words, $offset, $maxRecords, $fulltext, $filter, $boolean, $searchDate, $categId);

			$data = array_merge($data, $rv['data']);
			$cant += $rv['cant'];
		}

		if ($prefs['feature_forums'] == 'y') {
			$rv = $this->find_forums($words, $offset, $maxRecords, $fulltext, $filter, $boolean, $searchDate, $categId);

			$data = array_merge($data, $rv['data']);
			$cant += $rv['cant'];
		}

		if ($prefs['feature_file_galleries'] == 'y') {
			$rv = $this->find_files($words, $offset, $maxRecords, $fulltext, $filter, $boolean, $searchDate, $categId);

			$data = array_merge($data, $rv['data']);
			$cant += $rv['cant'];
		}

		if ($prefs['feature_blogs'] =='y') {
			$rv = $this->find_blogs($words, $offset, $maxRecords, $fulltext, $filter, $boolean, $searchDate, $categId);

			$data = array_merge($data, $rv['data']);
			$cant += $rv['cant'];
		}

		if ($prefs['feature_articles'] == 'y' && $tiki_p_read_article == 'y') {
			$rv = $this->find_articles($words, $offset, $maxRecords, $fulltext, $filter, $boolean, $searchDate, $categId, $lang);

			$data = array_merge($data, $rv['data']);
			$cant += $rv['cant'];
		}

		if ($prefs['feature_blogs'] == 'y') {
			$rv = $this->find_posts($words, $offset, $maxRecords, $fulltext, $filter, $boolean, $searchDate, $categId);

			$data = array_merge($data, $rv['data']);
			$cant += $rv['cant'];
		}

		if ($prefs['feature_directory'] == 'y' && $tiki_p_view_directory == 'y') {
			$rv = $this->find_directory($words, $offset, $maxRecords, $fulltext, $filter, $boolean, $searchDate, $categId);

			foreach ($rv['data'] as $a) {
				$a['relevance'] *= 0.7; // decrease artifically the relevance because as description is shorter than a wiki data, a directory is returned before wiki page
				array_push($data, $a);
			}
			$cant += $rv['cant'];
		}

		if ($prefs['feature_trackers'] == 'y' && $tiki_p_view_trackers == 'y') {
			$rv = $this->find_trackers($words, $offset, $maxRecords, $fulltext, $filter, $boolean, $searchDate, $categId);

			$data = array_merge($data, $rv['data']);
			$cant += $rv['cant'];
		}

		global $tiki_p_view_events, $tiki_p_view_calendar;
		if ($prefs['feature_calendar'] == 'y' && ($tiki_p_view_events == 'y' or $tiki_p_view_calendar == 'y') ) {
			$rv = $this->find_calendars($words, $offset, $maxRecords, $fulltext, $filter, $boolean, $searchDate, $categId);

			$data = array_merge($data, $rv['data']);
			$cant += $rv['cant'];
		}

		if ($fulltext) {
            /**
             * @param $a
             * @param $b
             * @return int
             */
            function find_pages_cmp($a, $b)
			{
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
