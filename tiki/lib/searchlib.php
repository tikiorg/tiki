<?php

//test

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
}

class SearchLib extends TikiLib {
	function SearchLib($db) {
		# this is probably uneeded now
		if (!$db) {
			die ("Invalid db object passed to SearchLib constructor");
		}

		$this->db = $db;
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
 * \param fulltext: if true a full text search is done, if no result, a simple search is done
 * \param $words the list of words
 * //todo: extract the short words from the list and do a simple search on them, them merge with the full search results on the remaining words
 * \return the nb of results + array('name', 'data', 'hits', 'lastModif', 'href', 'pageName', 'relevance'
**/
	function _find($h, $words = '', $offset = 0, $maxRecords = -1, $fulltext = false) {
		$words = trim($words);

		$sql = sprintf(
			'SELECT %s AS name, LEFT(%s, 240) AS data, %s AS hits, %s AS lastModif, %s AS pageName',
					$h['name'], $h['data'], $h['hits'], $h['lastModif'], $h['pageName']);
		
		$id = $h['id'];
		for ($i = 0; $i < count($id); ++$i)
			$sql .= ',' . $id[$i] . ' AS id' . ($i + 1);
		if (count($id) < 2)
			$sql .= ',1 AS id2';

		$sql2 = ' FROM ' . $h['from'] . ' WHERE ';
		$sql2 .= (isset($h['filter']))? $h['filter'] : '1';

		$orderby = (isset($h['orderby']) ? $h['orderby'] : $h['hits']);

		if ($fulltext) {
			$qwords = $this->db->quote($words);

			$sqlft = 'MATCH(' . join(',', $h['search']). ') AGAINST (' . $qwords . ')';
			$sql2 .= ' AND ' . $sqlft ;
			$sql .= ', ' . $sqlft . ' AS relevance';
			$orderby = 'relevance desc, ' . $orderby;
		} else if ($words) {
			$sql .= ', -1 AS relevance';

			$vwords = split(' ', $words);
			foreach ($vwords as $aword) {
				//$aword = $this->db->quote('[[:<:]]' . strtoupper($aword) . '[[:>:]]');
				$aword = $this->db->quote('.*' . strtoupper($aword). '.*');

				$sql2 .= ' AND (';

				for ($i = 0; $i < count($h['search']); ++$i) {
					if ($i)
						$sql2 .= ' OR ';

					$sql2 .= 'UPPER(' . $h['search'][$i] . ') REGEXP ' . $aword;
				}

				$sql2 .= ')';
			}
		} else {
			$sql .= ', -1 AS relevance';
		}
		$cant = $this->getOne('SELECT COUNT(*)' . $sql2);

		if (!$cant) { // no result
			if ($fulltext && $words) // try a simple search
				return $this->_find($h, $words, $offset, $maxRecords, false);
			else
				return array(
					'data' => array(),
					'cant' => 0
				);
		}

		$sql .= $sql2 . ' ORDER BY ' . $orderby . ' DESC LIMIT ' . $offset . ',' . $maxRecords;

		$result = $this->query($sql);
		$ret = array();

		while ($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
			$href = sprintf(urldecode($h['href']), $res['id1'], $res['id2']);

			$ret[] = array(
				'pageName' => $res["pageName"],
				'data' => $res["data"],
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
		$rv = array();
		static $search_wikis_comments = array(
			'from' => 'tiki_comments c, tiki_pages p',
			'name' => 'c.title',
			'data' => 'c.data',
			'hits' => 'p.hits', // c.hits is always null for a page comment!!
			'lastModif' => 'c.commentDate',
			'href' => 'tiki-index.php?page=%s#comments',
			'id' => array('p.pageName', 'c.threadId'),
			'pageName' => 'CONCAT(p.pageName, ": ", c.title)',
			'search' => array('c.title', 'c.data'),
			'filter' => 'c.objectType = "wiki page" AND p.pageName=c.object',
		);
		$rv = $this->_find($search_wikis_comments, $words, $offset, $maxRecords, $fulltext);

		static $search_wikis = array(
			'from' => 'tiki_pages',
			'name' => 'pageName',
			'data' => 'data',
			'hits' => 'hits', //'pageRank', pageRank is updated not very often since the line below is in comment
			'lastModif' => 'lastModif',
			'href' => 'tiki-index.php?page=%s',
			'id' => array('pageName'),
			'pageName' => 'pageName',
			'search' => array('pageName', 'description', 'data'),
		);

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
			'from' => 'tiki_galleries',
			'name' => 'name',
			'data' => 'description',
			'hits' => 'hits',
			'lastModif' => 'lastModif',
			'href' => 'tiki-browse_gallery.php?galleryId=%d',
			'id' => array('galleryId'),
			'pageName' => 'name',
			'search' => array('name', 'description'),
		);

		return $this->_find($search_galleries, $words, $offset, $maxRecords, $fulltext);
	}

	function find_faqs($words = '', $offset = 0, $maxRecords = -1, $fulltext = false) {
		static $search_faqs = array(
			'from' => 'tiki_faqs f , tiki_faq_questions q',
			'name' => 'f.title',
			'data' => 'f.description',
			'hits' => 'f.hits',
			'lastModif' => 'f.created',
			'href' => 'tiki-view_faq.php?faqId=%d',
			'id' => array('f.faqId'),
			'pageName' => 'CONCAT(f.title, ": ", q.question)',
			'search' => array('q.question', 'q.answer'),
			'filter' => 'q.faqId = f.faqId',
		);

		return $this->_find($search_faqs, $words, $offset, $maxRecords, $fulltext);
	}

	function find_directory($words = '', $offset = 0, $maxRecords = -1, $fulltext = false) {
		static $search_directory = array(
			'from' => 'tiki_directory_sites',
			'name' => 'name',
			'data' => 'description',
			'hits' => 'hits',
			'lastModif' => 'lastModif',
			'href' => 'tiki-directory_redirect.php?siteId=%d',
			'id' => array('siteId'),
			'pageName' => 'name',
			'search' => array('name', 'description'),
		);

		return $this->_find($search_directory, $words, $offset, $maxRecords, $fulltext);
	}

	function find_images($words = '', $offset = 0, $maxRecords = -1, $fulltext = false) {
		static $search_images = array(
			'from' => 'tiki_images',
			'name' => 'name',
			'data' => 'description',
			'hits' => 'hits',
			'lastModif' => 'created',
			'href' => 'tiki-browse_image.php?imageId=%d',
			'id' => array('imageId'),
			'pageName' => 'name',
			'search' => array('name', 'description'),
		);

		return $this->_find($search_images, $words, $offset, $maxRecords, $fulltext);
	}

	function find_forums($words = '', $offset = 0, $maxRecords = -1, $fulltext = false) {
		static $search_forums = array(
			'from' => 'tiki_comments c, tiki_forums f',
			'name' => 'c.title',
			'data' => 'c.data',
			'hits' => 'c.hits',
			'lastModif' => 'c.commentDate',
			'href' => 'tiki-view_forum_thread.php?forumId=%d&amp;comments_parentId=%d',
			'id' => array('f.forumId', 'c.threadId'),
			'pageName' => 'CONCAT(name, ": ", title)',
			'search' => array('c.title', 'c.data'),
			'filter' => 'c.objectType = "forum" AND f.forumId = c.object',
		);

		return $this->_find($search_forums, $words, $offset, $maxRecords, $fulltext);
	}

	function find_files($words = '', $offset = 0, $maxRecords = -1, $fulltext = false) {
		static $search_files = array(
			'from' => 'tiki_files',
			'name' => 'name',
			'data' => 'description',
			'hits' => 'downloads',
			'lastModif' => 'created',
			'href' => 'tiki-download_file.php?fileId=%d',
			'id' => array('fileId'),
			'pageName' => 'filename',
			'search' => array('name', 'description'),
		);

		return $this->_find($search_files, $words, $offset, $maxRecords, $fulltext);
	}

	function find_blogs($words = '', $offset = 0, $maxRecords = -1, $fulltext = false) {
		static $search_blogs = array(
			'from' => 'tiki_blogs',
			'name' => 'title',
			'data' => 'description',
			'hits' => 'hits',
			'lastModif' => 'lastModif',
			'href' => 'tiki-view_blog.php?blogId=%d',
			'id' => array('blogId'),
			'pageName' => 'title',
			'search' => array('title', 'description'),
			'filter' => 'use_find = "y"',
		);

		return $this->_find($search_blogs, $words, $offset, $maxRecords, $fulltext);
	}

	function find_articles($words = '', $offset = 0, $maxRecords = -1, $fulltext = false) {
		static $search_articles = array(
			'from' => 'tiki_articles',
			'name' => 'title',
			'data' => 'heading',
			'hits' => 'reads',
			'lastModif' => 'publishDate',
			'href' => 'tiki-read_article.php?articleId=%d',
			'id' => array('articleId'),
			'pageName' => 'title',
			'search' => array('title', 'heading', 'body'),
		);

		return $this->_find($search_articles, $words, $offset, $maxRecords, $fulltext);
	}

	function find_posts($words = '', $offset = 0, $maxRecords = -1, $fulltext = false) {
		global $user;

		$site_timezone_shortname = $this->get_site_timezone_shortname();
		$site_time_difference = $this->get_site_time_difference($user);
		# TODO localize?
		$pagename = "CONCAT(b.title, ' [', " . "DATE_FORMAT(FROM_UNIXTIME(p.created + $site_time_difference), " . "'%M %d %Y %h:%i'), ' $site_timezone_shortname', '] : ', p.user)";

		$search_posts = array(
			'from' => 'tiki_blog_posts p LEFT JOIN tiki_blogs b ON b.blogId = p.blogId',
			'name' => 'p.data', # to simplify the logic, won't hurt performance
			'data' => 'p.data',
			'hits' => '1',
			'orderby' => 'p.created',
			'lastModif' => 'p.created',
			# TODO double up %'s from urlencode() return value
			'href' => 'tiki-view_blog.php?blogId=%d&amp;find=' . urlencode($words),
			'id' => array('p.blogId'),
			'pageName' => $pagename,
			'search' => array('p.data','p.title'),
			'filter' => 'b.use_find = "y"',
		);

		return $this->_find($search_posts, $words, $offset, $maxRecords, $fulltext);
	}

	function find_pages($words = '', $offset = 0, $maxRecords = -1, $fulltext = false) {
		$data = array();

		$cant = 0;
		$rv = $this->find_wikis($words, $offset, $maxRecords, $fulltext);

		foreach ($rv['data'] as $a) {
			$a['type'] = tra('Wiki');

			array_push($data, $a);
		}

		$cant += $rv['cant'];
		$rv = $this->find_galleries($words, $offset, $maxRecords, $fulltext);

		foreach ($rv['data'] as $a) {
			$a['type'] = tra('Gallery');

			array_push($data, $a);
		}

		$cant += $rv['cant'];
		$rv = $this->find_faqs($words, $offset, $maxRecords, $fulltext);

		foreach ($rv['data'] as $a) {
			$a['type'] = tra('FAQ');

			array_push($data, $a);
		}

		$cant += $rv['cant'];
		$rv = $this->find_images($words, $offset, $maxRecords, $fulltext);

		foreach ($rv['data'] as $a) {
			$a['type'] = tra('Image');

			array_push($data, $a);
		}

		$cant += $rv['cant'];
		$rv = $this->find_forums($words, $offset, $maxRecords, $fulltext);

		foreach ($rv['data'] as $a) {
			$a['type'] = tra('Forum');

			array_push($data, $a);
		}

		$cant += $rv['cant'];
		$rv = $this->find_files($words, $offset, $maxRecords, $fulltext);

		foreach ($rv['data'] as $a) {
			$a['type'] = tra('File');

			array_push($data, $a);
		}

		$cant += $rv['cant'];
		$rv = $this->find_blogs($words, $offset, $maxRecords, $fulltext);

		foreach ($rv['data'] as $a) {
			$a['type'] = tra('Blog');

			array_push($data, $a);
		}

		$cant += $rv['cant'];
		$rv = $this->find_articles($words, $offset, $maxRecords, $fulltext);

		foreach ($rv['data'] as $a) {
			$a['type'] = tra('Article');

			array_push($data, $a);
		}

		$cant += $rv['cant'];
		$rv = $this->find_posts($words, $offset, $maxRecords, $fulltext);

		foreach ($rv['data'] as $a) {
			$a['type'] = tra('Blog post');

			array_push($data, $a);
		}

		$cant += $rv['cant'];

		$rv = $this->find_directory($words, $offset, $maxRecords, $fulltext);

		foreach ($rv['data'] as $a) {
			$a['type'] = tra('Directory');
			$a['relevance'] *= 0.7; // decrease artifically the relevance because as description is shorter than a wiki data, a directory is returned before wiki page

			array_push($data, $a);
		}

		$cant += $rv['cant'];

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
