<?php

class SearchLib Extends TikiLib {

  function SearchLib($db) 
  {
  	# this is probably uneeded now
    if(!$db) {
      die("Invalid db object passed to SearchLib constructor");  
    }
    $this->db = $db;  
  }
  
  function register_search($words)
  {
   $words=addslashes($words);
   $words = preg_split("/\s/",$words);
   foreach($words as $word) {
     $word=trim($word);
     $cant = $this->getOne("select count(*) from tiki_search_stats where term='$word'");
     if($cant) {
       $query = "update tiki_search_stats set hits=hits+1 where term='$word'";
     } else {
       $query = "insert into tiki_search_stats(term,hits) values('$word',1)";
     }

     $result = $this->query($query);
   }
  }

  function _find($h, $words = '', $offset = 0, $maxRecords = -1, $fulltext = false) {
    $words = trim($words);
    $sql = sprintf('SELECT %s AS name, LEFT(%s, 240) AS data, %s AS hits, %s AS lastModif, %s	AS pageName',
      $h['name'], $h['data'], $h['hits'], $h['lastModif'], $h['pageName']);
    $id = $h['id'];
    for ($i = 0; $i < count($id); ++$i)
      $sql .= ',' . $id[$i] . ' AS id' . ($i + 1);
    if (count($id) < 2)
      $sql .= ',1 AS id2';
    $sql2 = ' FROM ' . $h['from'] . ' WHERE 1';
    $search_fields = array($h['name']);
   	if ($h['data'] && $h['name'] != $h['data'])
	    array_push($search_fields, $h['data']);
    $orderby = (isset($h['orderby']) ? $h['orderby'] : $h['hits']);
    if ($fulltext) {
	   	if (count($h['search']))
    		if (!preg_match('/\./', $h['search'][0]))
    			$search_fields = array_merge($search_fields, $h['search']);
		$qwords = $this->db->quote($words);
    	$sqlft = 'MATCH(' . join(',', $search_fields) . ') AGAINST (' . $qwords . ')';
    	$sql2 .= ' AND ' . $sqlft . ' >= 0';
    	$sql .= ', ' . $sqlft . ' AS relevance';
	    $orderby = 'relevance desc, ' . $orderby;
#		if (count($h['search'])) {
#	    	$sqlft = ' MATCH(' . join(',', $h['search']) . ') AGAINST (' . $qwords . ')';
#	    	$sql2 .= ' OR ' . $sqlft . ' > 0';
#	    	$sql .= ', ' . $sqlft . ' AS score2';
#		}
	} else if ($words) {
	  $sql .= ', -1 AS relevance';
      $vwords = split(' ',$words);
      foreach ($vwords as $aword) {
        //$aword = $this->db->quote('[[:<:]]' . strtoupper($aword) . '[[:>:]]');
        $aword = $this->db->quote('.*'.strtoupper($aword).'.*');
        $sql2 .= ' AND (';
        for ($i = 0; $i < count($search_fields); ++$i) {
          if ($i)
            $sql2 .= ' OR ';
          $sql2 .= 'UPPER(' . $search_fields[$i] . ') REGEXP ' . $aword;
        }
        $sql2 .= ')';
      }
    } else {
    	$sql .= ', -1 AS relevance';
    }

    $cant = $this->getOne('SELECT COUNT(*)' . $sql2);
    if (!$cant) {
      return array('data' => array(), 'cant' => 0);
    }
    $sql .= $sql2 . ' ORDER BY ' . $orderby . ' DESC LIMIT ' . $offset . ',' . $maxRecords;
    $result = $this->query($sql);
    $ret = Array();
    while ($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $href = sprintf($h['href'], $res['id1'], $res['id2']);
      $ret[] = array(
        'pageName'  => $res["pageName"],
        'data'      => $res["data"],
        'hits'      => $res["hits"],
        'lastModif' => $res["lastModif"],
        'href'      => $href,
        'relevance' => round($res["relevance"], 3),
      );
    }
    return array('data' => $ret, 'cant' => $cant);
  }

  function find_wikis($words='',$offset=0,$maxRecords=-1, $fulltext = false) 
  {
    static $search_wikis = array(
      'from'      => 'tiki_pages',
      'name'      => 'pageName',
      'data'      => 'data',
      'hits'      => 'pageRank',
      'lastModif'	=> 'lastModif',
      'href'      => 'tiki-index.php?page=%s',
      'id'        => array('pageName'),
      'pageName'	=> 'pageName',
      'search'    => array(),
    );
    $this->pageRank();
    return $this->_find($search_wikis, $words, $offset, $maxRecords, $fulltext);
  }

  function find_galleries($words='',$offset=0,$maxRecords=-1, $fulltext = false) 
  {
    static $search_galleries = array(
      'from'      => 'tiki_galleries',
      'name'      => 'name',
      'data'      => 'description',
      'hits'      => 'hits',
      'lastModif' => 'lastModif',
      'href'      => 'tiki-browse_gallery.php?galleryId=%d',
      'id'        => array('galleryId'),
      'pageName'  => 'name',
      'search'    => array(),
    );
    return $this->_find($search_galleries, $words, $offset, $maxRecords, $fulltext);
  }

  function find_faqs($words='',$offset=0,$maxRecords=-1, $fulltext = false) 
  {
    static $search_faqs = array(
      'from'      => 'tiki_faqs f LEFT JOIN tiki_faq_questions q ON q.faqId = f.faqId',
      'name'      => 'f.title',
      'data'      => 'f.description',
      'hits'      => 'f.hits',
      'lastModif' => 'f.created',
      'href'      => 'tiki-view_faq.php?faqId=%d',
      'id'        => array('f.faqId'),
      'pageName'  => 'CONCAT(f.title, ": ", q.question)',
      'search'    => array('q.question', 'q.answer'),
    );
    return $this->_find($search_faqs, $words, $offset, $maxRecords, $fulltext);
  }
  
  function find_directory($words='',$offset=0,$maxRecords=-1, $fulltext = false) 
  {
    static $search_directory = array(
      'from'      => 'tiki_directory_sites',
      'name'      => 'name',
      'data'      => 'description',
      'hits'      => 'hits',
      'lastModif' => 'lastModif',
      'href'      => 'tiki-directory_redirect.php?siteId=%d',
      'id'        => array('siteId'),
      'pageName'  => 'name',
      'search'    => array(),
    );
    return $this->_find($search_directory, $words, $offset, $maxRecords, $fulltext);
  }

  function find_images($words='',$offset=0,$maxRecords=-1, $fulltext = false) 
  {
    static $search_images = array(
      'from'      => 'tiki_images',
      'name'      => 'name',
      'data'      => 'description',
      'hits'      => 'hits',
      'lastModif' => 'created',
      'href'      => 'tiki-browse_image.php?imageId=%d',
      'id'        => array('imageId'),
      'pageName'  => 'name',
      'search'    => array(),
    );
    return $this->_find($search_images, $words, $offset, $maxRecords, $fulltext);
  }

  function find_forums($words='',$offset=0,$maxRecords=-1, $fulltext = false) 
  {
    static $search_forums = array(
      'from'      => 'tiki_comments c LEFT JOIN tiki_forums f ON md5(concat("forum",f.forumId))=c.object',
      'name'      => 'c.title',
      'data'      => 'c.data',
      'hits'      => 'c.hits',
      'lastModif' => 'c.commentDate',
      'href'      => 'tiki-view_forum_thread.php?forumId=%d&amp;comments_parentId=%d',
      'id'        => array('f.forumId', 'c.threadId'),
      'pageName'  => 'CONCAT(name, ": ", title)',
      'search'    => array(),
    );
    return $this->_find($search_forums, $words, $offset, $maxRecords, $fulltext);
  }

  function find_files($words='',$offset=0,$maxRecords=-1, $fulltext = false) 
  {
    static $search_files = array(
      'from'      => 'tiki_files',
      'name'      => 'name',
      'data'      => 'description',
      'hits'      => 'downloads',
      'lastModif' => 'created',
      'href'      => 'tiki-download_file.php?fileId=%d',
      'id'        => array('fileId'),
      'pageName'  => 'filename',
      'search'    => array(),
    );
    return $this->_find($search_files, $words, $offset, $maxRecords, $fulltext);
  }

  function find_blogs($words='',$offset=0,$maxRecords=-1, $fulltext = false) 
  {
    static $search_blogs = array(
      'from'      => 'tiki_blogs',
      'name'      => 'title',
      'data'      => 'description',
      'hits'      => 'hits',
      'lastModif' => 'lastModif',
      'href'      => 'tiki-view_blog.php?blogId=%d',
      'id'        => array('blogId'),
      'pageName'  => 'title',
      'search'    => array(),
    );
    return $this->_find($search_blogs, $words, $offset, $maxRecords, $fulltext);
  }

  function find_articles($words='',$offset=0,$maxRecords=-1, $fulltext = false) 
  {
    static $search_articles = array(
      'from'      => 'tiki_articles',
      'name'      => 'title',
      'data'      => 'heading',
      'hits'      => 'reads',
      'lastModif' => 'publishDate',
      'href'      => 'tiki-read_article.php?articleId=%d',
      'id'        => array('articleId'),
      'pageName'  => 'title',
      'search'    => array('body'),
    );
    return $this->_find($search_articles, $words, $offset, $maxRecords, $fulltext);
  }

  function find_posts($words='',$offset=0,$maxRecords=-1, $fulltext = false) 
  {
  	$site_timezone_shortname = $this->get_site_timezone_shortname();
  	$site_time_difference = $this->get_site_time_difference(/* $user */);
    # TODO localize?
    $pagename = "CONCAT(b.title, ' [', ".
    	"DATE_FORMAT(FROM_UNIXTIME(p.created + $site_time_difference), ".
    	"'%M %d %Y %h:%i'), ' $site_timezone_shortname', '] by: ', p.user)";

    $search_posts = array(
      'from'      => 'tiki_blog_posts p LEFT JOIN tiki_blogs b ON b.blogId = p.blogId',
      'name'      => 'p.data',	# to simplify the logic, won't hurt performance
      'data'      => 'p.data',
      'hits'      => '1',
      'orderby'   => 'p.created',
      'lastModif' => 'p.created',
      # TODO double up %'s from urlencode() return value
      'href'      => 'tiki-view_blog.php?blogId=%d&amp;find='.urlencode($words),

      'id'        => array('p.blogId'),
      'pageName'  => $pagename,
      'search'    => array(),
    );
    return $this->_find($search_posts, $words, $offset, $maxRecords, $fulltext);
  }

  function find_pages($words='',$offset=0,$maxRecords=-1, $fulltext = false) 
  {
    $data = array();
    $cant = 0;
    $rv = $this->find_wikis($words,     $offset, $maxRecords, $fulltext);
    foreach($rv['data'] as $a) {
      $a['type'] = tra('Wiki');
      array_push($data, $a);
    }
    $cant += $rv['cant'];
    $rv = $this->find_galleries($words, $offset, $maxRecords, $fulltext);
    foreach($rv['data'] as $a) {
      $a['type'] = tra('Gallery');
      array_push($data, $a);
    }
    $cant += $rv['cant'];
    $rv = $this->find_faqs($words,      $offset, $maxRecords, $fulltext);
    foreach($rv['data'] as $a) {
      $a['type'] = tra('FAQ');
      array_push($data, $a);
    }
    $cant += $rv['cant'];
    $rv = $this->find_images($words,    $offset, $maxRecords, $fulltext);
    foreach($rv['data'] as $a) {
      $a['type'] = tra('Image');
      array_push($data, $a);
    }
    $cant += $rv['cant'];
    $rv = $this->find_forums($words,    $offset, $maxRecords, $fulltext);
    foreach($rv['data'] as $a) {
      $a['type'] = tra('Forum');
      array_push($data, $a);
    }
    $cant += $rv['cant'];
    $rv = $this->find_files($words,     $offset, $maxRecords, $fulltext);
    foreach($rv['data'] as $a) {
      $a['type'] = tra('File');
      array_push($data, $a);
    }
    $cant += $rv['cant'];
    $rv = $this->find_blogs($words,     $offset, $maxRecords, $fulltext);
    foreach($rv['data'] as $a) {
      $a['type'] = tra('Blog');
      array_push($data, $a);
    }
    $cant += $rv['cant'];
    $rv = $this->find_articles($words,  $offset, $maxRecords, $fulltext);
    foreach($rv['data'] as $a) {
      $a['type'] = tra('Article');
      array_push($data, $a);
    }
    $cant += $rv['cant'];
    $rv = $this->find_posts($words,     $offset, $maxRecords, $fulltext);
    foreach($rv['data'] as $a) {
      $a['type'] = tra('Post');
      array_push($data, $a);
    }
    $cant += $rv['cant'];
    
    $rv = $this->find_directory($words,     $offset, $maxRecords, $fulltext);
    foreach($rv['data'] as $a) {
      $a['type'] = tra('Directory');
      array_push($data, $a);
    }
    $cant += $rv['cant'];
    
    if ($fulltext) {
      function find_pages_cmp ($a, $b) {
        return ($a['relevance'] > $b['relevance']) ? -1 : (($a['relevance'] < $b['relevance']) ? 1 : 0);
      }

      usort ($data, 'find_pages_cmp');
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
