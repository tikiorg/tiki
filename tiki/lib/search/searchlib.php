<?php

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

	function &find($where,$words,$offset, $maxRecords) {
	  return($this->find_exact($where,$words,$offset, $maxRecords));
	}

	function &find_exact($where,$words,$offset, $maxRecords) {
	  $words=preg_split("/[\W]+/",$words,-1,PREG_SPLIT_NO_EMPTY);
	  switch($where) {
	    case "wikis":
	      return $this->find_exact_wiki($words,$offset, $maxRecords);
	      break;
	    case "forums":
	      return $this->find_exact_forums($words,$offset, $maxRecords);
	      break;
	    default:
	      return $this->find_exact_all($words,$offset, $maxRecords);
	      break;
	  }
	}

	function &find_exact_all($words,$offset, $maxRecords) {
	  echo "not implemented";
	}

	function &find_exact_wiki($words,$offset, $maxRecords) {
	  $query="select s.`page`, s.`location`, s.`last_update`, s.`count`,
	  	p.`data`, p.`hits`, p.`lastModif` from
	        `tiki_searchindex` s, `tiki_pages` p  where `searchword` in
		(".implode(',',array_fill(0,count($words),'?')).") and
		s.`page`=p.`pageName`";
	  $result=$this->query($query,$words,$maxRecords,$offset);

	  $querycant="select count(*) from `tiki_searchindex` where
	  	`searchword` in
		(".implode(',',array_fill(0,count($words),'?')).")";
	  $cant=$this->getOne($querycant,$words);

	  $ret=array();
          while ($res = $result->fetchRow()) {
            $href = "tiki-index.php?page=".urlencode($res["page"]);
            $ret[] = array(
              'pageName' => $res["page"],
              'data' => substr($res["data"],0,250),
              'hits' => $res["hits"],
              'lastModif' => $res["lastModif"],
              'href' => $href,
              'relevance' => $res["count"]
            );
          }

          return array('data' => $ret,'cant' => $cant);
        }


} # class SearchLib

?>
