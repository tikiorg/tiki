<?php

class StructLib extends TikiLib {
	function StructLib($db) {
		# this is probably uneeded now
		if (!$db) {
			die ("Invalid db object passed to StructLib constructor");
		}

		$this->db = $db;
	}

	function s_export_structure($structure) {
		global $exportlib, $tikidomain;

		global $dbTiki;
		include_once ('lib/wiki/exportlib.php');
		$zipname = "$structure.zip";
		include_once ("lib/tar.class.php");
		$tar = new tar();
		$pages = $this->s_get_structure_pages($structure);

		foreach ($pages as $page) {
			$data = $exportlib->export_wiki_page($page, 0);

			$tar->addData($page, $data, date("U"));
		}

		$tar->toTar("dump/$tikidomain" . $structure . ".tar", FALSE);
		header ("location: dump/$tikidomain$structure.tar");
		return '';
	}

	function s_get_structure_pages($structure) {
		$ret = array($structure);

		$query = "select `page` from `tiki_structures` where `parent`=? order by ".$this->convert_sortmode("pos_asc");
		$result = $this->query($query,array($structure));

		while ($res = $result->fetchRow()) {
			$page = $res['page'];

			$ret[] = $page;
			$ret2 = $this->s_get_structure_pages($page);

			if (count($ret2) > 0) {
				$ret = array_merge($ret, $ret2);
			}
		}

		return $ret;
	}

	function s_export_structure_tree($structure, $level = 0) {

		$query = "select `page` from `tiki_structures` where `parent`=? order by ".$this->convert_sortmode("pos_asc");
		$result = $this->query($query,array($structure));

		if ($level == 0) {
			print ($structure);

			print ("\n");
			$this->s_export_structure_tree($structure, $level + 1);
		} else {
			while ($res = $result->fetchRow()) {
				for ($i = 0; $i < $level; $i++) {
					print (" ");
				}

				$page = $res['page'];
				print ($page);
				print ("\n");
				$this->s_export_structure_tree($page, $level + 1);
			}
		}
	}

	function s_remove_page($page, $delete) {
		// Now recursively remove

		$query = "select `page` from `tiki_structures` where `parent`=?";
		$result = $this->query($query,array($page));

		while ($res = $result->fetchRow()) {
			$this->s_remove_page($res["page"], $delete);
		}

		$query = "delete from `tiki_structures` where `page`=?";
		$result = $this->query($query,array($page));

		if ($delete) {
			$this->remove_all_versions($page);
		}

		return true;
	}

	function s_create_page($parent, $after, $name, $structID, $alias) {

		if (!$this->page_exists($name)) {
			$now = date("U");

			$this->create_page($name, 0, '', $now, 'created from stucture', 'system', '0.0.0.0', '');
		}

		if ($after) {
			$max = $this->getOne("select `pos` from `tiki_structures` where `structID` =? and `page`=?",array($structID, $after));
		} else {
			$max = 0;
		}

		if ($max > 0) {
			//If max is 5 then we are inserting after position 5 so we'll insert 5 and move all
			// the others
			$query = "update `tiki_structures` set `pos`=`pos`+1 where `structID` =? and `pos`>? and `parent`=?";
			$result = $this->query($query,array($structID, $max,$parent));
		}
//		$cant = $this->getOne("select count(*) from `tiki_structures` where `page`=?",array($name));

//		if ($cant)
//			return false;

		$max++;
		$query = "insert into `tiki_structures`(`structID`,`parent`,`page`,`page_alias`,`pos`) values(?,?,?,?,?)";

		$result = $this->query($query,array($structID,$parent,$name,$alias,$max));
	// If the page doesn't exist then create the page!
	}


  /// \todo there should bet no html here !!
	// we have to rewritte that function
	function get_subtree($structID, $page, &$html, $level = '') {

		$ret = array();
		$first = true;
		//$level++;
		$sublevel = 0;
		$query = "select `page`, `page_alias` from `tiki_structures` where `structID`=? and `parent`=? order by `pos` asc";
		$result = $this->query($query,array($structID, $page));
		$subs = array();

		while ($res = $result->fetchRow()) {
			if ($first) {
				$html .= '<ul>';
				$first = false;
			}
			$sublevel++;
			$upage = urlencode($res["page"]);

			if ($level) {
				$plevel = $level . '.' . $sublevel;
			} else {
				$plevel = $sublevel;
			}

			$html .= "<li style='list-style:disc outside;'><a class='link' href='tiki-edit_structure.php?structID=" . urlencode($structID). "&amp;page=$upage'>$plevel&nbsp;" . $res["page"];
			$pageAlias = $res["page_alias"];
      		if (!empty($pageAlias)) {
        		$html .= "&nbsp(" . $pageAlias . ")";
      		}
      		$html .= "</a>&nbsp;[<a class='link' href='tiki-edit_structure.php?structID=" . urlencode($structID). "&amp;remove=$upage'>x</a>]";
			$html .= "&nbsp;[<a class='link' href='tiki-index.php?page=$upage&amp;structID=$structID'>" . tra("view"). "</a>|<a  class='link' href='tiki-editpage.php?page=$upage&structID=$structID'>" . tra("edit"). "</a>]";
			//$prev = $this->get_prev_page($res["page"]);
			//$next = $this->get_next_page($res["page"]);
			//$html.=" prev: $prev next: $next ";
			$html .= "</li>";

			$subs[] = $this->get_subtree($structID, $res["page"], $html, $plevel);
		}

		if (!$first) {
			$html .= '</ul>';
		}

		$aux["name"] = $page;
		$aux["cant"] = count($subs);
		$aux["pages"] = $subs;
		$ret[] = $aux;
		return $ret;
	}

	function get_structure($page) {
		$page_sl = addslashes($page);

		$parent = $this->getOne("select `parent` from `tiki_structures` where `page`='$page_sl'");

		if (!$parent)
			return $page;

		return $this->get_structure($parent);
	}

	// no html ! to rewritte !
	function get_subtree_toc($structure, $page, &$html, $level = '') {

		$ret = array();
		$first = true;
		//$level++;
		$sublevel = 0;
		$query = "select `page`, `page_alias` from `tiki_structures` where `parent`=? order by ".$this->convert_sortmode("pos_asc");
		$result = $this->query($query,array($page));
		$subs = array();

		while ($res = $result->fetchRow()) {
			if ($first) {
				$html .= '<ul>';

				$first = false;
			}

			$sublevel++;

			if ($level) {
				$plevel = $level . '.' . $sublevel;
			} else {
				$plevel = $sublevel;
			}

			$upage = urlencode($res["page"]);
			$html .= "<li style='list-style:disc outside;'><a class='link' href='tiki-index.php?page=$upage'>$plevel&nbsp;";
      $pageAlias = $res["page_alias"];
      if (empty($pageAlias)) {
        $html .= $res["page"];
      }
      else {
        $html .= $pageAlias;
      }
			//$html.="&nbsp;[<a class='link' href='tiki-index.php?page=${res["page"]}'>view</a>|<a  class='link' href='tiki-editpage.php?page=${res["page"]}'>edit</a>]";
			$html .= "</a></li>";

			$subs[] = $this->get_subtree_toc($structure, $res["page"], $html, $plevel);
		}

		if (!$first) {
			$html .= '</ul>';
		}

		$aux["name"] = $page;
		$aux["cant"] = count($subs);
		$aux["pages"] = $subs;
		$ret[] = $aux;
		return $ret;
	}

	function get_subtree_toc_slide($structure, $page, &$html, $level = '') {

		$ret = array();
		$first = true;
		//$level++;
		$sublevel = 0;
		$query = "select `page`, `page_alias` from `tiki_structures` where `parent`=? order by ".$this->convert_sortmode("pos_asc");
		$result = $this->query($query,array($page));
		$subs = array();

		while ($res = $result->fetchRow()) {
			if ($first) {
				$html .= '<ul>';

				$first = false;
			}

			$sublevel++;

			if ($level) {
				$plevel = $level . '.' . $sublevel;
			} else {
				$plevel = $sublevel;
			}

			$upage = urlencode($res["page"]);
			$html .= "<li style='list-style:disc outside;'><a class='link' href='tiki-slideshow2.php?page=$upage'>$plevel&nbsp;";
			$pageAlias = $res["page_alias"];
      if (empty($pageAlias)) {
        $html .= $res["page"];
      }
      else {
        $html .= $pageAlias;
      }
//$html.="&nbsp;[<a class='link' href='tiki-index.php?page=${res["page"]}'>view</a>|<a  class='link' href='tiki-editpage.php?page=${res["page"]}'>edit</a>]";
			$html .= "</a></li>";

			$subs[] = $this->get_subtree_toc($structure, $res["page"], $html, $plevel);
		}

		if (!$first) {
			$html .= '</ul>';
		}

		$aux["name"] = $page;
		$aux["cant"] = count($subs);
		$aux["pages"] = $subs;
		$ret[] = $aux;
		return $ret;
	}

	function page_is_in_structure($page) {
		$page = addslashes($page);

		$cant = $this->getOne("select count(*) from `tiki_structures` where `page`=?",array($page));
		return $cant;
	}
	
	function get_next_page($structID, $page, $deep = 1) {
		
		// If we have children then get the first children
		if ($deep) {
			$query = "select `page`, `page_alias` from `tiki_structures` where `structID`=? and `parent`=? order by ".$this->convert_sortmode("pos_asc");
			$result = $this->query($query,array($structID, $page));

			if ($result->numRows()) {
				$res = $result->fetchRow();
				$next_page["page"] = $res["page"];
				$next_page["page_alias"] = $res["page_alias"];

				return $next_page;
			}
		}

		// Try to get the next page with the same parent as this
		$parent = $this->getOne("select `parent` from `tiki_structures` where `structID`=? and `page`=?",array($structID, $page));
		$pos = $this->getOne("select `pos` from `tiki_structures` where `structID`=? and `page`=?",array($structID, $page));

		if (!$parent)
			return '';

		$query = "select `page`, `page_alias` from `tiki_structures` where `structID`=? and `parent`=? and `pos`>? order by ".$this->convert_sortmode("pos_asc");
		$result = $this->query($query,array($structID, $parent,$pos));

		if ($result->numRows()) {
			$res = $result->fetchRow();
			$next_page["page"] = $res["page"];
			$next_page["page_alias"] = $res["page_alias"];
			return $next_page;
		} else {
			return $this->get_next_page($structID, $parent, 0);
		}
	}
	
	function get_prev_page($structID, $page) {
		// Try to get the next page with the same parent as this

		$parent = $this->getOne("select `parent` from `tiki_structures` where `structID`=? and `page`=?",array($structID,$page));
		$pos = $this->getOne("select `pos` from `tiki_structures` where `structID`=? and `page`=?",array($structID,$page));

		if (!$parent)
			return '';

		$query = "select `page`, `page_alias` from `tiki_structures` where `structID`=? and `parent`=? and `pos`<? order by ".$this->convert_sortmode("pos_asc");
		$result = $this->query($query,array($structID,$parent,$pos));

		if ($result->numRows()) {
			$res = $result->fetchRow();

			$prev_page["page"] = $res["page"];
			$prev_page["page_alias"] = $res["page_alias"];

			return $prev_page;
		} else {
			$prev_page["page"] = $parent;
			$prev_page["page_alias"] = $this->get_page_alias($structID,$parent);
			return $prev_page;
		}
	}

/*
	
	function get_prev_page($structID, $page) {
		// Try to get the next page with the same parent as this
		
		$query = "select `pos`, `parent` from `tiki_structures` where `structID`=? and `page`=?";
		$result = $this->query($query,array($structID, $page));
		
		$prev_page = array();
		
		if ($result->numRows()) {
			// This page does have a parent		
			$res = $result->fetchRow();
			$parent = $res["parent"];
			$pos = $res["pos"];
		} else {
			print_r('NO PARENT PAGE/POS OF THIS PAGE='.$pos);
			return '';
		}
		
		$prev_page["page"] = $parent;
		$prev_page["page_alias"] = $this->getOne("select `page_alias` from `tiki_structures` where `structID`=? and `page`=?",array($structID, $parent));
		
		$query = "select `page`, `page_alias` from `tiki_structures` where `structID`=? and `parent`=? and `pos`<? order by ".$this->convert_sortmode("pos_asc");
		$result = $this->query($query,array($structID, $parent,$pos));

		if ($result->numRows()) {
			$res = $result->fetchRow();
	
			$prev_page["page"] = $res["page"];
			$prev_page["page_alias"] = $res["page_alias"];
			return $prev_page;
		} 
		else {
			return $prev_page;
		}
	}
*/
	function get_prev_next_pages($page, $structID='') {
		// Get structure info for this page
		
		if ($structID) {
			$prev_pages = $this->get_prev_page($structID, $page);
//			print_r('PREV_PAGES[page] = '.$prev_pages['page'].'          ');
//			print_r('PREV_PAGES[page_alias] = '.$prev_pages['page_alias'].'          ');
			if ($prev_pages) {
				$struct_nav_pages[$structID]["prev_page"] = $prev_pages["page"];
				$struct_nav_pages[$structID]["prev_page_alias"] = $prev_pages["page_alias"];
			}
			
			$next_pages = $this->get_next_page($structID, $page);
			if ($next_pages) {
				$struct_nav_pages[$structID]["next_page"] = $next_pages["page"];
				$struct_nav_pages[$structID]["next_page_alias"] = $next_pages["page_alias"];
			}
			
			return $struct_nav_pages;			
		}		
		
		$query = "select `structID` from `tiki_structures` where `page`=?";
		$result = $this->query($query,array($page));
				
		while ($res = $result->fetchRow()) {
			$structs[] = $res["structID"];
		}
		
		foreach ($structs as $structID) {
			$prev_pages = $this->get_prev_page($structID, $page);
			if ($prev_pages) {
				$struct_nav_pages[$structID]["prev_page"] = $prev_pages["page"];
				$struct_nav_pages[$structID]["prev_page_alias"] = $prev_pages["page_alias"];
			}
			
			$next_pages = $this->get_next_page($structID, $page);
			if ($next_pages) {
				$struct_nav_pages[$structID]["next_page"] = $next_pages["page"];
				$struct_nav_pages[$structID]["next_page_alias"] = $next_pages["page_alias"];
			}
		}
		
		return $struct_nav_pages;
	}
	
	function get_first_page($structID) {
		$parent = '';
		$first_page = $this->getOne("select `page` from `tiki_structures` where `structID`=? and `parent`=?",array($structID,$parent));
		return ($first_page);
	}

	function get_parent_page($page) {
		// Try to get the parent of this page
		$parent = $this->getOne("select `parent` from `tiki_structures` where `page`=?",array($page));
		return ($parent);
	}

	// Return an array of subpages
	function get_pages($structID, $page) {
		$ret = array();
		$query = "select `page` from `tiki_structures` where `structID`=? and `parent`=? order by ".$this->convert_sortmode("pos_asc");
		$result = $this->query($query,array($structID,$page));
		while ($res = $result->fetchRow()) {
			$ret[] = $res["page"];
		}
		return $ret;
	}

	function get_showstructs($page) {
		$ret = array();
		$query = "select `structID`, `page_alias` from `tiki_structures` where `page`=?";
		$result = $this->query($query,array($page));
		while ($res = $result->fetchRow()) {
			$ret[$res["structID"]] = $res["page_alias"];
		}
		return $ret;
	}

	function get_max_children($structID,$page) {

		$query = "select `page` from `tiki_structures` where `structID`=? and `parent`=?";
		$result = $this->query($query,array($structID,$page));
		if (!$result->numRows()) {
			return '';
		}
		$res = $result->fetchRow();
		return $res;
	}

	// Return all the pages belonging to the structure in an array
	function get_structure_pages($structID, $page) {
		$ret = array($page);
		$query = "select `page` from `tiki_structures` where `structID`=? and `parent`=?";
		$result = $this->query($query,array($structID,$page));
		while ($res = $result->fetchRow()) {
			$ret[] = $res["page"];
			$ret2 = $this->get_structure_pages($structID, $res["page"]);
			$ret = array_unique(array_merge($ret, $ret2));
		}
		$ret = array_unique($ret);
		return $ret;
	}

	// Return all the pages belonging to the structure in an array ordered from first 
	// to last page.
	function get_structure_pages_ordered($page) {
		$ret = array($page);
		$query = "select `page` from `tiki_structures` where `parent`=? order by pos asc";
		$result = $this->query($query,array($page));
		while ($res = $result->fetchRow()) {
			$ret[] = $res["page"];
			$ret2 = $this->get_structure_pages($res["page"]);
			$ret = array_unique(array_merge($ret, $ret2));
		}
		$ret = array_unique($ret);
		return $ret;
	}
	
	function list_structures($offset, $maxRecords, $sort_mode, $find) {

		if ($find) {
			$findesc = '%' . $find . '%';
			$mid = " where `parent`=? and (page like ? or parent like ?)";
			$bindvars=array('',$findesc,$findesc);
		} else {
			$mid = " where `parent`=?";
			$bindvars=array('');
		}

		$query = "select * from `tiki_structures` $mid group by `structID` order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_structures` $mid";
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
  
  function get_page_alias($structID,$page) {
		$query = "select `page_alias` from `tiki_structures` where `structID`=? and `page` like ?";
		$res = $this->getOne($query, array($structID,$page));
    return $res;
  }
  
  function set_page_alias($structID, $page, $pageAlias) {
		$query = "update `tiki_structures` set `page_alias`=? where `structID` =? and `page` like ?";
		$this->query($query, array($pageAlias, $structID, $page));
  }
}

$structlib = new StructLib($dbTiki);

?>
