<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}
class StructLib extends TikiLib
{
	public $displayLanguageOrder;

	public function __construct()
	{
		global $prefs;
		parent::__construct();

		$this->displayLanguageOrder = array();
	}
	public function s_export_structure($structure_id)
	{
		global $exportlib, $tikidomain;
		global $dbTiki;
		include_once ('lib/wiki/exportlib.php');
		include_once ('lib/tar.class.php');
		$page_info = $this->s_get_structure_info($structure_id);
		$page_name = $page_info['pageName'];
		$zipname   = $page_name . '.zip';
		$tar = new tar();
		$pages = $this->s_get_structure_pages($page_info['page_ref_id']);
		foreach ($pages as $page) {
			$data = $exportlib->export_wiki_page($page['pageName'], 0);
			$tar->addData($page['pageName'], $data, $this->now);
		}
		$dump = 'dump';
		if ($tikidomain) {
			$dump.= "/$tikidomain";
		}
		$tar->toTar("$dump/$page_name.tar", false);
		header("location: $dump/$page_name.tar");
		return '';
	}
	public function s_export_structure_tree($structure_id, $level = 0)
	{
		$structure_tree = $this->get_subtree($structure_id);
		$level = 0;
		$first = true;
		header('Content-type: text/plain; charset=utf-8');
		foreach ($structure_tree as $node) {
			//This special case indicates head of structure
			if ($node['first'] and $node['last']) {
				print (tra('Use this tree to copy the structure').': ' . $node['pageName'] . "\n\n");
			} elseif ($node['first'] or !$node['last']) {
				if ($node['first'] and !$first) {
			        $level++;
				}
				$first = false;
				for ($i = 0; $i < $level; $i++) {
					print (' ');
				}
				print ($node['pageName']);
				if (!empty($node['page_alias'])) {
					print("->" . $node['page_alias']);
				}
				print("\n");
			} else {
				//node is a place holder for last in level
				$level--;
			}
		}
	}
	public function s_remove_page($page_ref_id, $delete, $name='')
	{
		// Now recursively remove
		global $user, $prefs, $tiki_p_remove;
		if ($prefs['feature_user_watches'] == 'y') {
			include_once('lib/notifications/notificationemaillib.php');
			sendStructureEmailNotification(array('action'=>'remove', 'page_ref_id'=>$page_ref_id, 'name'=>$name));
		}
		$query = 'select `page_ref_id`, ts.`page_id`, `pageName` ';
		$query .= 'from `tiki_structures` ts, `tiki_pages` tp ';
		$query .= 'where tp.`page_id`=ts.`page_id` and `parent_id`=?';
		$result = $this->query($query, array((int) $page_ref_id));
		//Iterate down through the child nodes
		while ($res = $result->fetchRow()) {
			$this->s_remove_page($res['page_ref_id'], $delete);
		}
		//Only delete a page if other structures arent referencing it
		if ($delete && $tiki_p_remove == 'y') {
			$page_info = $this->s_get_page_info($page_ref_id);
  			$query = 'select count(*) from `tiki_structures` where `page_id`=?';
	  		$count = $this->getOne($query, array((int) $page_info['page_id']));
			$wikilib = TikiLib::lib('wiki');
			if ($count == 1 && $wikilib->is_editable($page_info['pageName'], $user)) {
				$this->remove_all_versions($page_info['pageName']);
			}
		}
		// Remove the space created by the removal
		$page_info = $this->s_get_page_info($page_ref_id);
		if (isset($page_info["parent_id"])) {
			$query = "update `tiki_structures` set `pos`=`pos`-1 where `pos`>? and `parent_id`=?";
			$this->query($query, array((int) $page_info["pos"], (int) $page_info["parent_id"]));
		}
		//Remove the structure node
		$query = 'delete from `tiki_structures` where `page_ref_id`=?';
		$result = $this->query($query, array((int) $page_ref_id));
		return true;
	}
	public function promote_node($page_ref_id)
	{
		global $prefs;
		$page_info = $this->s_get_page_info($page_ref_id);
		$parent_info = $this->s_get_parent_info($page_ref_id);
		//If there is a parent and the parent isnt the structure root node.
		if (isset($parent_info) && $parent_info["parent_id"]) {
			//Make a space for the node after its parent
			$query = 'update `tiki_structures` set `pos`=`pos`+1 where `pos`>? and `parent_id`=?';
			$this->query($query, array((int) $parent_info['pos'], (int) $parent_info['parent_id']));
			//Move the node up one level
			$query = 'update `tiki_structures` set `parent_id`=?, `pos`=(? + 1) where `page_ref_id`=?';
			$this->query($query, array((int) $parent_info['parent_id'], (int) $parent_info['pos'], (int) $page_ref_id));
			//Remove the space that was created by the promotion
  			$query = "update `tiki_structures` set `pos`=`pos`-1 where `pos`>? and `parent_id`=?";
  			$this->query($query, array((int) $page_info["pos"], (int) $page_info["parent_id"]));
			if ($prefs['feature_user_watches'] == 'y') {
				include_once('lib/notifications/notificationemaillib.php');
				sendStructureEmailNotification(array('action'=>'move_up', 'page_ref_id'=>$page_ref_id, 'parent_id'=>$page_info['parent_id']));
			}
		}
	}
	public function demote_node($page_ref_id)
	{
		$page_info = $this->s_get_page_info($page_ref_id);
		$parent_info = $this->s_get_parent_info($page_ref_id);
		$query = 'select `page_ref_id`, `pos` from `tiki_structures` where `pos`<? and `parent_id`=? order by `pos` desc';
		$result = $this->query($query, array((int) $page_info['pos'], (int) $page_info['parent_id']));
		if ($previous = $result->fetchRow()) {
			//Get last child nodes for previous sibling
			$query = 'select `pos` from `tiki_structures` where `parent_id`=? order by `pos` desc';
			$result = $this->query($query, array((int) $previous['page_ref_id']));
			if ($res = $result->fetchRow()) {
				$pos = $res['pos'];
			} else {
				$pos = 0;
			}
			$query = 'update `tiki_structures` set `parent_id`=?, `pos`=(? + 1) where `page_ref_id`=?';
			$this->query($query, array((int) $previous['page_ref_id'], (int) $pos, (int) $page_ref_id));
			//Remove the space created by the demotion
			$query = "update `tiki_structures` set `pos`=`pos`-1 where `pos`>? and `parent_id`=?";
			$this->query($query, array((int) $page_info["pos"], (int) $page_info["parent_id"]));
			global $prefs;
			if ($prefs['feature_user_watches'] == 'y') {
				include_once('lib/notifications/notificationemaillib.php');
				sendStructureEmailNotification(array('action'=>'move_down', 'page_ref_id'=>$page_ref_id, 'parent_id'=>$previous['page_ref_id']));
			}
		}
	}
	public function move_after_next_node($page_ref_id)
	{
		$page_info = $this->s_get_page_info($page_ref_id);
		$query = 'select `page_ref_id`, `pos` from `tiki_structures` where `pos`>? and `parent_id`=? order by `pos` asc';
		$result = $this->query($query, array((int) $page_info['pos'], (int) $page_info['parent_id']));
		$res = $result->fetchRow();
		if ($res) {
			//Swap position values
			$query = 'update `tiki_structures` set `pos`=? where `page_ref_id`=?';
			$this->query($query, array((int) $page_info['pos'], (int) $res['page_ref_id']));
			$this->query($query, array((int) $res['pos'], (int) $page_info['page_ref_id']));
		}
	}
	public function move_before_previous_node($page_ref_id)
	{
		$page_info = $this->s_get_page_info($page_ref_id);
		$query = 'select `page_ref_id`, `pos` from `tiki_structures` where `pos`<? and `parent_id`=? order by `pos` desc';
		$result = $this->query($query, array((int) $page_info['pos'], (int) $page_info['parent_id']));
		$res = $result->fetchRow();
		if ($res) {
			//Swap position values
			$query = 'update `tiki_structures` set `pos`=? where `page_ref_id`=?';
			$this->query($query, array((int) $res['pos'], (int) $page_info['page_ref_id']));
			$this->query($query, array((int) $page_info['pos'], (int) $res['page_ref_id']));
		} elseif ($page_info['pos'] > 1) { // a bug occurred - try to fix
			$query = 'update `tiki_structures` set `pos`=? where `page_ref_id`=?';
			$this->query($query, array($page_info['pos']-1, (int) $page_info['page_ref_id']));
		}
	}

	/**
	 * @param $data array - from from nestedSortable('toHierarchy')
	 */

	public function reorder_structure($data)
	{
		global $user;

		if (!empty($data)) {
			$parent_ref_id = $data[0]->structure_id;
			$structure_info = $this->s_get_structure_info($parent_ref_id);	// "root"

			if (TikiLib::lib('tiki')->user_has_perm_on_object($user, $structure_info['pageName'], 'wiki page', 'tiki_p_edit_structures')) {

				$structure_id = $structure_info['structure_id'];
				$tiki_structures = TikiDb::get()->table('tiki_structures');
				$orders = array();
				$conditions = array('structure_id' => (int) $structure_id);

				foreach ($data as $node) {
					if ($node->item_id != 'root') {
						if (!isset($orders[$node->depth])) {
							$orders[$node->depth] = 1;
						} else {
							$orders[$node->depth]++;
						}
						$node->parent_id = $node->parent_id == 'root' || empty($node->parent_id) ? $parent_ref_id : $node->parent_id;
						$fields = array(
							'parent_id' => $node->parent_id,
							'pos' => $orders[$node->depth],
							'page_alias' => $node->page_alias,
						);
						if ($node->item_id < 1000000) {
							$conditions['page_ref_id'] = (int) $node->item_id;
							$tiki_structures->update(
								$fields,
								$conditions
							);
						} else {
							// new nodes with id > 1000000
							$fields['page_id'] = TikiLib::lib('tiki')->get_page_id_from_name($node->page_name);
							$fields['structure_id'] = $structure_id;
							$tiki_structures->insert($fields);
						}
					}
				}

				return $structure_info;
			}
		}
		return false;
	}

	/** \brief Create a structure entry with the given name
      \param parent_id The parent entry to add this to.
           If NULL, create new structure.
      \param after_ref_id The entry to add this one after.
           If NULL, put it in position 0.
      \param name The wiki page to reference
      \param alias An alias for the wiki page name.
      \return the new entries page_ref_id or null if not created.
	*/
	public function s_create_page($parent_id, $after_ref_id, $name, $alias='', $structure_id=null, $options = array())
	{
		global $prefs;
		$ret = null;
		
		$hide_toc = isset($options['hide_toc']) ? $options['hide_toc'] : 'n';
		$creator = isset($options['creator']) ? $options['creator'] : tra('system');
		$creator_msg = isset($options['creator_msg']) ? $options['creator_msg'] : tra('created from structure');
		$ip_source = isset($options['ip_source']) ? $options['ip_source'] : '0.0.0.0';
		
		// If the page doesn't exist then create a new wiki page!
		$newpagebody = '';
		if ($hide_toc !== 'y') {
			$newpagebody = tra("Table of contents") . ":" . "{toc}";
		}
		$created = $this->create_page($name, 0, $newpagebody, $this->now, $creator_msg, $creator, $ip_source, '', false, '', array('parent_id'=>$parent_id));

		if (!empty($parent_id) || $created || ! $this->page_is_in_structure($name)) {
			// if were not trying to add a duplicate structure head
			$query = 'select `page_id` from `tiki_pages` where `pageName`=?';
			$page_id = $this->getOne($query, array($name));
			if (!empty($after_ref_id)) {
				$max = $this->getOne('select `pos` from `tiki_structures` where `page_ref_id`=?', array((int) $after_ref_id));
			} else {
				$max = 0;
			}
			if (!isset($after_ref_id)) {
				// after_ref_id		The entry to add this one after. If NULL, put it in position 0.
				$max = 0;
				$query = 'update `tiki_structures` set `pos`=`pos`+1 where `pos`>? and `parent_id`=?';
				$this->query($query, array((int) $max, (int) $parent_id));
				
			} elseif ($after_ref_id != 0) {
				if ($max > 0) {
					//If max is 5 then we are inserting after position 5 so we'll insert 5 and move all
					// the others
					$query = 'update `tiki_structures` set `pos`=`pos`+1 where `pos`>? and `parent_id`=?';
					$result = $this->query($query, array((int) $max, (int) $parent_id));
				}
			} else if (!$created) {
				$max = $this->getOne('select max(`pos`) from `tiki_structures` where `parent_id`=?', array((int) $parent_id));
			}
			//
            //Create a new structure entry
			$max++;
			$query = 'insert into `tiki_structures`(`parent_id`,`page_id`,`page_alias`,`pos`, `structure_id`) values(?,?,?,?,?)';
			$result = $this->query($query, array((int) $parent_id, (int) $page_id, $alias, (int) $max, (int) $structure_id));
			//Get the page_ref_id just created
			if (isset($parent_id)) {
				$parent_check = ' and `parent_id`=?';
				$attributes = array((int) $page_id,$alias,(int) $max, (int) $parent_id);
			} else {
				$parent_check = ' and (`parent_id` is null or `parent_id`=0)';
				$attributes = array((int) $page_id,$alias,(int) $max);
			}
			$query  = 'select `page_ref_id` from `tiki_structures` ';
			$query .= 'where `page_id`=? and `page_alias`=? and `pos`=?';
			$query .= $parent_check;
			$ret = $this->getOne($query, $attributes);
			if (empty($parent_id)) {
				$query = 'update `tiki_structures` set `structure_id`=? where `page_ref_id`=?';
				$this->query($query, array($ret, $ret));
			}

			if ($prefs['feature_wiki_categorize_structure'] == 'y') {
				$this->categorizeNewStructurePage($name, $this->s_get_structure_info($parent_id));
			}

			if ($prefs['feature_user_watches'] == 'y') {
				include_once('lib/notifications/notificationemaillib.php');
				sendStructureEmailNotification(array('action'=>'add', 'page_ref_id'=>$ret, 'name'=>$name));
			}
		}
		return $ret;
	}

	/**
	 * Categorizes a (new) page the same as the parent structure
	 * Called from s_create_page if feature_wiki_categorize_structure = y
	 *
	 * @param string $page				name of new page
	 * @param array $structure_info		structure info
	 */
	public function categorizeNewStructurePage ($page, $structure_info) {
		$categlib = TikiLib::lib('categ');

		$cat_type = 'wiki page';
		$cat_href = "tiki-index.php?page=" . urlencode($page);

		$structObjectId = $categlib->is_categorized($cat_type, $structure_info["pageName"]);
		if ($structObjectId) {
			// structure is categorized
			$pageObjectId = $categlib->is_categorized($cat_type, $page);
			$structure_cats = $categlib->get_object_categories($cat_type, $structure_info["pageName"]);
			if (!$pageObjectId) {
				// added page is not categorized
				$pageObjectId = $categlib->add_categorized_object($cat_type, $page, '', $page, $cat_href);
				foreach ($structure_cats as $cat_acat) {
					$categlib->categorize($pageObjectId, $cat_acat);
				}
			} else {
				// added page is already categorized (somehow?)
				$cats = $categlib->get_object_categories($cat_type, $page);
				foreach ($structure_cats as $cat_acat) {
					if (!in_array($cat_acat, $cats, true)) {
						$categlib->categorize($pageObjectId, $cat_acat);
					}
				}
			}
		}
	}

	public function get_subtree($page_ref_id, $level = 0, $parent_pos = '')
	{
		$tikilib = TikiLib::lib('tiki');
		$ret = array();
		$pos = 1;
		//The structure page is used as a title
		if ($level == 0) {
			$struct_info = $this->s_get_page_info($page_ref_id);
			$aux['first']       = true;
			$aux['last']        = true;
			$aux['pos']         = '';
			$aux['page_ref_id'] = $struct_info['page_ref_id'];
			$aux['pageName']    = $struct_info['pageName'];
			$aux['page_alias']  = $struct_info['page_alias'];
			$wikilib = TikiLib::lib('wiki');
			$is_locked = $wikilib->is_locked($struct_info['pageName']);
			if ($is_locked) {
				$aux['flag'] = 'L';
				$aux['user'] = $is_locked;
			}
			$perms = $tikilib->get_perm_object($struct_info['pageName'], 'wiki page', '', false);
			$aux['editable'] = $perms['tiki_p_edit'];
			$aux['viewable'] = $perms['tiki_p_view'];
			$ret[] = $aux;
			$level++;
		}
		//Get all child nodes for this page_ref_id
		$query = 'select `page_ref_id`, `page_alias`, `pageName`, `flag`, `user`, `pos` as db_pos ';
		$query .= 'from `tiki_structures` ts, `tiki_pages` tp ';
		$query .= 'where ts.`page_id` = tp.`page_id` and `parent_id`=? order by `pos` asc';
		$result = $this->query($query, array((int) $page_ref_id));
		$subs = array();
		$row_max = $result->numRows();
		while ($res = $result->fetchRow()) {
			//Add
			$aux['first']       = ($pos == 1);
			$aux['db_pos'] = $res['db_pos'];
			$aux['last']        = false;
			$aux['page_ref_id'] = $res['page_ref_id'];
			$aux['pageName']    = $res['pageName'];
			$aux['page_alias']  = $res['page_alias'];
			$aux["flag"]  = $res["flag"];
			$aux["user"]  = $res["user"];
			global $user;
			if ($this->user_has_perm_on_object($user, $res['pageName'], 'wiki page', 'tiki_p_edit')) {
				$aux['editable'] = 'y';
				$aux['viewable'] = 'y';
			} else {
				$aux['editable'] = 'n';
				if ($this->user_has_perm_on_object($user, $res['pageName'], 'wiki page', 'tiki_p_view')) {
					$aux['viewable'] = 'y';
			  	} else {
			  		$aux['viewable'] = 'n';
				}
			}
			if (strlen($parent_pos) == 0) {
				$aux['pos'] = "$pos";
			} else {
				$aux['pos'] = $parent_pos . '.' . "$pos";
			}
			$ret[] = $aux;
			//Recursively add any child nodes
			$subs = $this->get_subtree($res['page_ref_id'], ($level + 1), $aux['pos']);
			if (isset($subs)) {
				$ret = array_merge($ret, $subs);
			}
			// Insert a dummy entry to close table/list
			if ($pos == $row_max) {
				$aux['first'] = false;
				$aux['last']  = true;
				$ret[] = $aux;
			}
			$pos++;
		}
		return $ret;
	}
	/**Returns an array of page_info arrays
		This can be used to construct a path from the
		structure head to the requested page.
	*/
	public function get_structure_path($page_ref_id)
	{
		global $prefs;
		$structure_path = array();
		$page_info = $this->s_get_page_info($page_ref_id);
		if ($page_info['parent_id']) {
			$structure_path = $this->get_structure_path($page_info['parent_id']);
		}
		$structure_path[] = $page_info;
		foreach ($structure_path as $key => $value) {
			if ($prefs['namespace_indicator_in_structure'] === 'y' && !empty($prefs['namespace_separator'])
				&& strpos($value['pageName'], $prefs['namespace_separator']) !== false) {
					$arr = explode($prefs['namespace_separator'], $value['pageName']);
					$structure_path[$key]['stripped_pageName'] = end($arr);
			} else {
				$structure_path[$key]['stripped_pageName'] = $value['pageName'];
			}
		}
		return $structure_path;
	}
	/* get all the users that watches a page or a page above */
	public function get_watches($pageName='', $page_ref_id=0, $recurs=true)
	{
		global $tiki_p_watch_structure;
		if ($tiki_p_watch_structure != 'y') {
			return array();
		}
		$query = "SELECT ts.`parent_id`,tuw.`email`,tuw.`user`, tuw.`event`";
		$query .= " FROM `tiki_structures` ts";
		$query .= " LEFT JOIN (
			SELECT watchId, user, event, object, title, type, url, email FROM `tiki_user_watches`
			UNION DISTINCT
				SELECT watchId, uu.login as user, event, object, title, type, url, uu.email
				FROM
					`tiki_group_watches` tgw
					INNER JOIN users_usergroups ug ON tgw.`group` = ug.groupName
					INNER JOIN users_users uu ON ug.userId = uu.userId AND uu.email IS NOT NULL AND uu.email <> ''
			) tuw ON (tuw.`object`=ts.`page_ref_id` AND tuw.`event`=?)";
		if (empty($page_ref_id)) {
			$query .= " LEFT JOIN `tiki_pages` tp ON ( tp.`page_id`=ts.`page_id`)";
			$query .= " WHERE tp.`pageName`=?";
			$result = $this->query($query, array('structure_changed', $pageName));
		} else {
			$query .= " WHERE ts.`page_ref_id`=?";
			$result = $this->query($query, array('structure_changed', $page_ref_id));
		}
		$ret = array();
		while ($res = $result->fetchRow()) {
			$parent_id = $res['parent_id'];
			unset($res['parent_id']);
			if (!empty($res['email']) || !empty($res['user'])) {
				$ret[] = $res;
			}
		}
		if (!empty($parent_id) && $recurs) {
			$ret2 = $this->get_watches('', $parent_id);
			if (!empty($ret2)) {
				$ret = array_merge($ret2, $ret);
			}
		}
		return $ret;
	}
	/**Returns a structure_info array
		See get_page_info for details of array
	*/
	public function s_get_structure_info($page_ref_id)
	{
		$parent_id = $this->getOne('select `parent_id` from `tiki_structures` where `page_ref_id`=?', array((int) $page_ref_id));
		if (!$parent_id) {
			return $this->s_get_page_info($page_ref_id);
		}
		return $this->s_get_structure_info($parent_id);
	}
	/**Returns an array of info about the parent
	   page_ref_id
	   See get_page_info for details of array
	*/
	public function s_get_parent_info($page_ref_id)
	{
		// Try to get the parent of this page
		$parent_id = $this->getOne('select `parent_id` from `tiki_structures` where `page_ref_id`=?', array((int) $page_ref_id));
		if (!$parent_id) {
			return null;
		}
		return ($this->s_get_page_info($parent_id));
	}

	public function use_user_language_preferences( $langContext = null )
	{
		global $prefs;
		if ( $prefs['feature_multilingual'] != 'y' ) {
			return;
		}
		if ( $prefs['feature_multilingual_structures'] != 'y' ) {
			return;
		}

		$multilinguallib = TikiLib::lib('multilingual');

		$this->displayLanguageOrder = $multilinguallib->preferredLangs($langContext);
	}

	public function build_language_order_clause( &$args, $pageTable = 'tp', $structTable = 'ts' )
	{
		$query = " CASE\n";


		// Languages in preferences go first
		foreach ($this->displayLanguageOrder as $key => $lang) {
			$query .= "\tWHEN $pageTable.lang = ? THEN ?\n";
			$args[] = $lang;
			$args[] = $key;
		}

		// If nothing in preferences, use structure default
		$query .= "\tWHEN $structTable.page_id = $pageTable.page_id THEN ?\n";
		$args[] = count($this->displayLanguageOrder);

		// Else should never be required
		$query .= "\tELSE ?\nEND\n";
		$args[] = count($this->displayLanguageOrder) + 1;

		return $query;
	}

	/** Return an array of page info
	*/
	public function s_get_page_info($page_ref_id)
	{
		if ( empty( $this->displayLanguageOrder ) ) {
			$query =  'select `pos`, `page_ref_id`, `parent_id`, ts.`page_id`, `pageName`, `page_alias`, `structure_id` ';
			$query .= 'from `tiki_structures` ts, `tiki_pages` tp ';
			$query .= 'where ts.`page_id`=tp.`page_id` and `page_ref_id`=?';
			$result = $this->query($query, array((int) $page_ref_id));
		} else {
			$args = array( (int) $page_ref_id );

			$query = "
				SELECT
					`pos`,
					`page_ref_id`,
					`parent_id`,
					ts.`page_id`,
					`pageName`,
					`page_alias`,
					`structure_id`
				FROM
					`tiki_structures` ts
					LEFT JOIN tiki_translated_objects a ON a.type = 'wiki page' AND a.objId = ts.page_id
					LEFT JOIN tiki_translated_objects b ON b.type = 'wiki page' AND a.traId = b.traId
					LEFT JOIN `tiki_pages` tp ON b.`objId` = tp.`page_id` OR ts.page_id = tp.page_id
				WHERE
					`page_ref_id` = ?
				ORDER BY " . $this->build_language_order_clause($args)
				. " LIMIT 1";

			$result = $this->query($query, $args);
		}

		if ($res = $result->fetchRow()) {
			return $res;
		} else {
			return null;
		}
	}
	// that is intended to replace the get_subtree_toc and get_subtree_toc_slide
	// it's used only in {toc} thing hardcoded in parse tikilib->parse -- (mose)
	// the $tocPrefix can be used to Prefix a subtree as it would start from a given number (e.g. 2.1.3)
	public function build_subtree_toc($id,$slide=false,$order='asc',$tocPrefix='')
	{
		global $user, $tikilib, $prefs;
		$ret = array();
		$cant = $this->getOne('select count(*) from `tiki_structures` where `parent_id`=?', array((int) $id));
		if ($cant) {
			// TODO : FIX
			$args = array();
			if ( ! $this->displayLanguageOrder ) {
				$query = 'select `page_ref_id`, `pageName`, `page_alias`, tp.`description` from `tiki_structures` ts, `tiki_pages` tp ';
				$query.= 'where ts.`page_id`=tp.`page_id` and `parent_id`=? order by '.$this->convertSortMode('pos_'.$order);
				$args[] = (int) $id;

			} else {
				$query = "
				SELECT
					`page_ref_id`,
					`pageName`,
					`page_alias`,
					tp.`description`
				FROM
					`tiki_structures` ts
					INNER JOIN tiki_pages tp ON tp.page_id = (
						SELECT tp.page_id
						FROM
							`tiki_pages` tr
							LEFT JOIN tiki_translated_objects a ON tr.page_id = a.objId AND a.type = 'wiki page'
							LEFT JOIN tiki_translated_objects b ON b.type = 'wiki page' AND a.traId = b.traId
							LEFT JOIN tiki_pages tp ON b.objId = tp.page_id OR tr.page_id = tp.page_id
						WHERE
							tr.page_id = ts.page_id
						ORDER BY " . $this->build_language_order_clause($args) . "
						LIMIT 1
					)
				WHERE
					parent_id = ?
				order by ".$this->convertSortMode('pos_'.$order);
				$args[] = (int) $id;
			}
			$result = $this->query($query, $args);
			$prefix=1;
			while ($res = $result->fetchRow()) {
				if (!$tikilib->user_has_perm_on_object($user, $res['pageName'], 'wiki page', 'tiki_p_view') ) {
					continue;
				}

				if ($prefs['namespace_indicator_in_structure'] === 'y'
					&& !empty($prefs['namespace_separator'])
					&& !empty($res['pageName'])
					&& strpos($res['pageName'], $prefs['namespace_separator']) !== false) {
					$arr = explode($prefs['namespace_separator'], $res['pageName']);
					$res['short_pageName'] = end($arr);
				} else {
					$res['short_pageName'] =  $res['pageName'];
				}
				$res['prefix']=($tocPrefix=='')?'':"$tocPrefix.";
				$res['prefix'].=$prefix;
				$prefix++;
				if ($res['page_ref_id'] != $id) {
					$sub = $this->build_subtree_toc($res['page_ref_id'], $slide, $order, $res['prefix']);
					if (is_array($sub)) {
						$res['sub'] = $sub;
					}
				}
				//if ($res['page_alias']<>'') $res['pageName']=$res['page_alias'];
				$back[] = $res;
			}
		} else {
			return false;
		}
		return $back;
	}
	public function get_toc($page_ref_id,$order='asc',$showdesc=false,$numbering=true,$numberPrefix='',$type='plain',$page='',$maxdepth=0, $structurePageName='')
	{
		global $user, $prefs;

		$structure_tree = $this->build_subtree_toc($page_ref_id, false, $order, $numberPrefix);

		if ($type === 'admin') {
			// check perms here as we still have $page_ref_id
			$structure_info = $this->s_get_structure_info($page_ref_id);

			$perms = Perms::get('wiki page', $structure_info["pageName"]);

			if ($prefs['lock_wiki_structures'] === 'y') {
				$lockedby = TikiLib::lib('attribute')->get_attribute('wiki structure', $_REQUEST['page_ref_id'], 'tiki.object.lock');
				if ($lockedby && $lockedby === $user && $perms->lock_structures || ! $lockedby || $perms->admin_structures) {
					$editable = $perms->edit_structures;
				} else {
					$editable = false;
				}
			} else {
				$editable = $perms->edit_structures;
			}

			if (! $editable) {
				$type = 'plain';
			} else {
				TikiLib::lib('smarty')->assign('structure_name', $structure_info["pageName"]);
				$json_params = json_encode(
					array(
						'page_ref_id' => $page_ref_id,
						'order' => $order,
						'showdesc' => $showdesc,
						'numbering' => $numbering,
						'numberPrefix' => $numberPrefix,
						'type' => $type,
						'page' => $page,
						'maxdepth' => $maxdepth,
						'structurePageName' => $structurePageName
					)
				);
				TikiLib::lib('smarty')->assign('json_params', $json_params);

			}
		}

		$nodelist = $this->fetch_toc($structure_tree, $showdesc, $numbering, $type, $page, $maxdepth, 0, $structurePageName);
		if ($type === 'admin' && empty($nodelist)) {
			$nodelist = "<ol class='admintoc' style='min-height: 4em;' data-params='$json_params'></ol>";
		}
		return $nodelist ."\n";
	}
	public function fetch_toc($structure_tree,$showdesc,$numbering,$type='plain',$page='',$maxdepth=0,$cur_depth=0,$structurePageName='')
	{
		$smarty = TikiLib::lib('smarty');
		global $user;
		$ret='';
		if ($structure_tree != '') {
			if (($maxdepth <= 0) || ($cur_depth < $maxdepth)) {

				$smarty->assign('toc_type', $type);
				$ret.= $smarty->fetch('structures_toc-startul.tpl')."\n";

				foreach ($structure_tree as $leaf) {

					if (is_numeric($page)) {
						$smarty->assign('hilite', $leaf["page_ref_id"] == $page);
					} else {
						$smarty->assign('hilite', $leaf["pageName"] == $page);
					}

					if ($type === 'admin') {
						if ($this->user_has_perm_on_object($user, $leaf["pageName"], 'wiki page', 'tiki_p_edit')) {
							$leaf['editable'] = true;
						} else {
							$leaf['editable'] = false;
						}
						if (TikiLib::lib('tiki')->user_watches($user, 'structure_changed', $leaf['page_ref_id'], 'structure')) {
							$leaf['event'] = true;
						} else {
							$leaf['event'] = false;
						}
					}

					$smarty->assign('structurePageName', $structurePageName);
					$smarty->assign_by_ref('structure_tree', $leaf);
					$smarty->assign('showdesc', $showdesc);
					$smarty->assign('numbering', $numbering);
					$ret.=$smarty->fetch('structures_toc-leaf.tpl');
					if (isset($leaf['sub']) && is_array($leaf['sub'])) {
						$ret.=$this->fetch_toc($leaf['sub'], $showdesc, $numbering, $type, $page, $maxdepth, $cur_depth+1, $structurePageName)."</li>\n";
					} else {
						$ret.=str_repeat("\t", ($cur_depth*2)+1)."</li>\n";
					}
				}
				$ret.=$smarty->fetch('structures_toc-endul.tpl')."\n";
			}
		}
		return $ret;
	}
	// end of replacement
	public function page_is_in_structure($pageName)
	{
		$query  = 'select count(*) ';
		$query .= 'from `tiki_structures` ts, `tiki_pages` tp ';
		$query .= 'where ts.`page_id`=tp.`page_id` and `pageName`=?';
		$cant = $this->getOne($query, array($pageName));
		return $cant;
	}
	public function page_id_is_in_structure($pageId)
	{
		$query  = 'select count(*) ';
		$query .= 'from `tiki_structures` ts, `tiki_pages` tp ';
		$query .= 'where ts.`page_id`=tp.`page_id` and `page_id`=?';
		$cant = $this->getOne($query, array($pageId));
		return $cant;
	}
	//Is this page the head page for a structure?
	public function get_struct_ref_if_head($pageName)
	{
		$query =  'select `page_ref_id` ';
		$query .= 'from `tiki_structures` ts, `tiki_pages` tp ';
		$query .= 'where ts.`page_id`=tp.`page_id` and (`parent_id` is null or `parent_id`=0) and `pageName`=?';
		$page_ref_id = $this->getOne($query, array($pageName));
		if ( $page_ref_id ) {
			return $page_ref_id;
		}

		if ( !$this->displayLanguageOrder ) {
			return null;
		}

		$query = "
			SELECT
				page_ref_id
			FROM
				tiki_structures ts
				INNER JOIN tiki_translated_objects a ON ts.page_id = a.objId AND a.type = 'wiki page'
				INNER JOIN tiki_translated_objects b ON a.traId = b.traId AND b.type = 'wiki page'
				INNER JOIN tiki_pages tp ON b.objId = tp.page_id
			WHERE
				(parent_id IS NULL or parent_id = 0)
				AND pageName = ?";

		$page_ref_id = $this->getOne($query, array($pageName));
		return $page_ref_id;
	}
	//Get reference id for a page
	public function get_struct_ref_id($pageName)
	{
		$query =  'select `page_ref_id` ';
		$query .= 'from `tiki_structures` ts, `tiki_pages` tp ';
		$query .= 'where ts.`page_id`=tp.`page_id` and `pageName`=?';
		$page_ref_id = $this->getOne($query, array($pageName));
		return $page_ref_id;
	}
	public function get_next_page($page_ref_id, $deep = true)
	{
		// If we have children then get the first child
		if ($deep) {
			$query  = 'select `page_ref_id` ';
			$query .= 'from `tiki_structures` ts ';
			$query .= 'where `parent_id`=? ';
			$query .= 'order by '.$this->convertSortMode('pos_asc');
			$result1 = $this->query($query, array((int) $page_ref_id));
			if ($result1->numRows()) {
				$res = $result1->fetchRow();
				return $res['page_ref_id'];
			}
		}
		// Try to get the next page with the same parent as this
		$page_info = $this->s_get_page_info($page_ref_id);
		$parent_id = $page_info['parent_id'];
		$page_pos = $page_info['pos'];
		if (!$parent_id) {
			return null;
		}
		$query  = 'select `page_ref_id` ';
        $query .= 'from `tiki_structures` ts ';
		$query .= 'where `parent_id`=? and `pos`>? ';
		$query .= 'order by '.$this->convertSortMode('pos_asc');
		$result2 = $this->query($query, array((int) $parent_id, (int) $page_pos));
		if ($result2->numRows()) {
			$res = $result2->fetchRow();
			return $res['page_ref_id'];
		} else {
			return $this->get_next_page($parent_id, false);
		}
	}
	public function get_prev_page($page_ref_id, $deep = false)
	{
		//Drill down to last child for this tree node
		if ($deep) {
			$query  = 'select `page_ref_id` ';
			$query .= 'from `tiki_structures` ts ';
			$query .= 'where `parent_id`=? ';
			$query .= 'order by '.$this->convertSortMode('pos_desc');
			$result = $this->query($query, array($page_ref_id));
			if ($result->numRows()) {
				//There are more children
				$res = $result->fetchRow();
				$page_ref_id = $this->get_prev_page($res['page_ref_id'], true);
			}
			return $page_ref_id;
		}
		// Try to get the previous page with the same parent as this
		$page_info = $this->s_get_page_info($page_ref_id);
		$parent_id = $page_info['parent_id'];
		$pos       = $page_info['pos'];
		//At the top of the tree
		if (empty($parent_id)) {
			return null;
		}
		$query  = 'select `page_ref_id` ';
		$query .= 'from `tiki_structures` ts ';
		$query .= 'where `parent_id`=? and `pos`<? ';
		$query .= 'order by '.$this->convertSortMode('pos_desc');
		$result =  $this->query($query, array((int) $parent_id, (int) $pos));
		if ($result->numRows()) {
			//There is a previous sibling
			$res = $result->fetchRow();
			$page_ref_id = $this->get_prev_page($res['page_ref_id'], true);
		} else {
			//No previous siblings, just the parent
			$page_ref_id = $parent_id;
		}
		return $page_ref_id;
	}
	public function get_navigation_info($page_ref_id)
	{
		$struct_nav_pages = array(
			'prev'   => $this->get_neighbor_info($page_ref_id, 'get_prev_page'),
			'next'   => $this->get_neighbor_info($page_ref_id, 'get_next_page'),
			'parent' => $this->get_neighbor_info($page_ref_id, 's_get_parent_info'),
			'home'   => $this->s_get_structure_info($page_ref_id),
		);

 		return $struct_nav_pages;
	}

	/**
	 * Get structure info for a page's neighbour respecting view perms
	 * @param int $page_ref_id
	 * @param string $fn		function to find neighbour (get_prev_page|get_next_page|s_get_parent_info)
	 * @return null | array		neighbour page info
	 */
	private function get_neighbor_info($page_ref_id, $fn)
	{
		if (method_exists($this, $fn)) {
			$neighbor = $this->$fn($page_ref_id);
			if ($neighbor) {
				if (is_array($neighbor)) {	// s_get_parent_info() returns the info array
					$info = $neighbor;
				} else {
					$info = $this->s_get_page_info($neighbor);
				}
				if ($info && Perms::get(array( 'type' => 'wiki page', 'object' => $info['pageName'] ))->view) {
					return $info;
				} else {
					return $this->get_neighbor_info($neighbor, $fn);
				}
			} else {
				return null;
			}
		} else {
			trigger_error('No structlib method found: ' . $fn);
			return null;
		}
	}
	/** Return an array of subpages
      Used by the 'After Page' select box
	*/
	public function s_get_pages($parent_id)
	{
		$ret = array();
		$query =  'select `pos`, `page_ref_id`, `parent_id`, ts.`page_id`, `pageName`, `page_alias` ';
		$query .= 'from `tiki_structures` ts, `tiki_pages` tp ';
		$query .= 'where ts.`page_id`=tp.`page_id` and `parent_id`=? ';
		$query .= 'order by '.$this->convertSortMode('pos_asc');
		$result = $this->query($query, array((int) $parent_id));
		while ($res = $result->fetchRow()) {
			//$ret[] = $this->populate_page_info($res);
			$ret[] = $res;
		}
		return $ret;
	}
    /** Get a list of all structures this page is a member of
	*/
	public function get_page_structures($pageName,$structure='')
	{
		$ret = array();
		$structures_added = array();
		if ( empty( $this->displayLanguageOrder ) ) {
			$query = 'select `page_ref_id` ';
			$query .= 'from `tiki_structures` ts, `tiki_pages` tp ';
			$query .= 'where ts.`page_id`=tp.`page_id` and `pageName`=?';
		} else {
			$query = "
				SELECT DISTINCT
					`page_ref_id`
				FROM
					tiki_structures ts
					LEFT JOIN tiki_translated_objects a ON a.objId = ts.page_id AND a.type = 'wiki page'
					LEFT JOIN tiki_translated_objects b ON a.traId = b.traId AND b.type = 'wiki page'
					LEFT JOIN tiki_pages tp ON ts.page_id = tp.page_id OR b.objId = tp.page_id
				WHERE
					pageName = ?";
		}

		$result = $this->query($query, array($pageName));
		while ($res = $result->fetchRow()) {
			$next_page = $this->s_get_structure_info($res['page_ref_id']);
			//Add each structure head only once
			if (!in_array($next_page['page_ref_id'], $structures_added)) {
				if (empty($structure) || $structure == $next_page['pageName']) {
					$structures_added[] = $next_page['page_ref_id'];
					$next_page['req_page_ref_id'] = $res['page_ref_id'];
					$ret[] = $next_page;
				}
			}
		}
		return $ret;
	}
	public function get_max_children($page_ref_id)
	{
		$query = 'select `page_ref_id` from `tiki_structures` where `parent_id`=?';
		$result = $this->query($query, array((int) $page_ref_id));
		if (!$result->numRows()) {
			return '';
		}
		$res = $result->fetchRow();
		return $res;
	}
	/** Return a unique list of pages belonging to the structure
	  \return An array of page_info arrays
	*/
	public function s_get_structure_pages_unique($page_ref_id)
	{
		$ret = array();
		// Add the structure page as well
		$ret[] = $this->s_get_page_info($page_ref_id);
		$ret2  = $this->s_get_structure_pages($page_ref_id);
		return array_unique(array_merge($ret, $ret2));
	}
	/** Return all the pages belonging to a structure
	 \param  Page reference ID in struct table
	 \return An array of page_info arrays
	*/
	public function s_get_structure_pages($page_ref_id)
	{
		$ret = array();
		if ($page_ref_id) {
		        $ret[0] = $this->s_get_page_info($page_ref_id);
	 		$query =  'select `pos`, `page_ref_id`, `parent_id`, ts.`page_id`, `pageName`, `page_alias` ';
			$query .= 'from `tiki_structures` ts, `tiki_pages` tp ';
	    		$query .= 'where ts.`page_id`=tp.`page_id` and `parent_id`=? ';
			$query .= 'order by '.$this->convertSortMode('pos_asc');
	   		$result = $this->query($query, array((int) $page_ref_id));
			while ($res = $result->fetchRow()) {
				$ret = array_merge($ret, $this->s_get_structure_pages($res['page_ref_id']));
			}
		}
		return $ret;
	}
	public function list_structures($offset, $maxRecords, $sort_mode, $find='', $exact_match = true, $filter = array())
	{
		global $prefs;

		if ($find) {
			if (!$exact_match && $find) {
				$find = preg_replace("/(\w+)/", "%\\1%", $find);
				$find = preg_split("/[\s]+/", $find, -1, PREG_SPLIT_NO_EMPTY);
				$mid = " where (`parent_id` is null or `parent_id`=0) and (tp.`pageName` like " . implode(' or tp.`pageName` like ', array_fill(0, count($find), '?')) . ")";
				$bindvars = $find;
			} else {
				$mid = ' where (`parent_id` is null or `parent_id`=0) and (tp.`pageName` like ?)';
				$findesc = '%' . $find . '%';
				$bindvars = array($findesc);
			}
		} else {
			$mid = ' where (`parent_id` is null or `parent_id`=0) ';
			$bindvars = array();
		}

		// If language is set to '', assume that no language filtering should be done.
		if (isset($filter['lang']) && $filter['lang'] == '') {
			unset($filter['lang']);
		}

		if ($prefs['feature_wiki_categorize_structure'] == 'y') {
			$category_jails = TikiLib::lib('categ')->get_jail();
			if ( ! isset( $filter['andCategId'] ) && ! isset( $filter['categId'] ) && empty( $filter['noCateg'] ) && ! empty( $category_jails ) ) {
				$filter['categId'] = $category_jails;
			}
		}

		$join_tables = ' inner join `tiki_pages` tp on (tp.`page_id`= ts.`page_id`)';
		$join_bindvars = array();
		$distinct = '';
		if (!empty($filter)) {
			foreach ($filter as $type => $val) {
				if ($type == 'categId') {
					$categories = TikiLib::lib('categ')->get_jailed((array) $val);
					$categories[] = -1;

					$cat_count = count($categories);
					$join_tables .= " inner join `tiki_objects` as tob on (tob.`itemId`= tp.`pageName` and tob.`type`= ?) inner join `tiki_category_objects` as tc on (tc.`catObjectId`=tob.`objectId` and tc.`categId` IN(" . implode(', ', array_fill(0, $cat_count, '?')) . ")) ";

					if ( $cat_count > 1 ) {
						$distinct = ' DISTINCT ';
					}

					$join_bindvars = array_merge(array('wiki page'), $categories);
				} elseif ($type == 'lang') {
					$mid .= empty($mid) ? ' where ' : ' and ';
					$mid .= '`lang`=? ';
					$bindvars[] = $val;
				}
			}
		}

		if (!empty($join_bindvars)) {
			$bindvars = empty($bindvars) ? $join_bindvars : array_merge($join_bindvars, $bindvars);
		}

		$query = "select $distinct `page_ref_id`,`structure_id`,`parent_id`,ts.`page_id`,`page_alias`,`pos`,
			`pageName`,tp.`hits`,`data`,tp.`description`,`lastModif`,`comment`,`version`,
			`user`,`ip`,`flag`,`points`,`votes`,`cache`,`wiki_cache`,`cache_timestamp`,
			`pageRank`,`creator`,`page_size` from `tiki_structures` as ts $join_tables $mid order by ".$this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_structures` ts $join_tables $mid";
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = array();
		while ($res = $result->fetchRow()) {
			global $user;
			if ( $this->user_has_perm_on_object($user, $res['pageName'], 'wiki page', 'tiki_p_view') ) {

				if (file_exists('whelp/'.$res['pageName'].'/index.html')) {
					$res['webhelp']='y';
				} else {
					$res['webhelp']='n';
				}
				if ( $this->user_has_perm_on_object($user, $res['pageName'], 'wiki page', 'tiki_p_edit') ) {
					$res['editable']='y';
				} else {
					$res['editable']='n';
				}
				$ret[] = $res;

			} // end check for perm if
		}
		$retval = array();
		$retval['data'] = $ret;
		$retval['cant'] = $cant;
		return $retval;
	}
	public function get_page_alias($page_ref_id)
	{
		$query = 'select `page_alias` from `tiki_structures` where `page_ref_id`=?';
		$res = $this->getOne($query, array((int) $page_ref_id));
		return $res;
	}
	public function set_page_alias($page_ref_id, $pageAlias)
	{
		$query = 'update `tiki_structures` set `page_alias`=? where `page_ref_id`=?';
		$this->query($query, array($pageAlias, (int) $page_ref_id));
	}
	//This nifty function creates a static WebHelp version using a TikiStructure as
	//the base.
	public function structure_to_webhelp($page_ref_id, $dir, $top)
	{
	  	global $style_base;
		global $prefs;
	    //The first task is to convert the structure into an array with the
	    //proper format to produce a WebHelp project.
		//We have to create something in the form
		//$pages=Array('root'=>Array('pag1'=>'','pag2'=>'','page3'=>Array(...)));
		//Where the name is the pageName|description and the other side is either ''
		//when the page is a leaf or an Array of pages when the page is a folder
		//Folders that are not TikiPages are known for having only a name instead
		//of name|description
		$tree = '$tree=Array('.$this->structure_to_tree($page_ref_id).');';
		eval($tree);
		//Now we have the tree in $tree!
		$menucode="foldersTree = gFld(\"Contents\", \"content.html\")\n";
		$menucode.=$this->traverse($tree);
		$base = "whelp/$dir";
		copy("$base/menu/options.cfg", "$base/menu/menuNodes.js");
		$fw = fopen("$base/menu/menuNodes.js", 'a+');
		fwrite($fw, $menucode);
		fclose($fw);
		$docs = Array();
		$words = Array();
		$index = Array();
		$first=true;
		$pages = $this->traverse2($tree);
		// Now loop the pages
		foreach ($pages as $page) {
			$query = 'select * from `tiki_pages` where `pageName`=?';
	  		$result = $this->query($query, array($page));
			$res = $result->fetchRow();
	  		$docs[] = $res['pageName'];
	  		if (empty($res['description'])) {
				$res['description']=$res['pageName'];
			}
	  		$pageName=$res['pageName'].'|'.$res['description'];
	  		$dat = $this->parse_data($res['data']);
	  		//Now dump the page
	  		$dat = preg_replace("/tiki-index.php\?page=([^\'\" ]+)/", "$1.html", $dat);
	  		$dat = str_replace('?nocache=1', '', $dat);
	  		$cs = '';
	  		$data = "<html><head><script src=\"../js/highlight.js\"></script><link rel=\"StyleSheet\"  href=\"../../../styles/$style_base.css\" type=\"text/css\" /><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /> <title>".$res["pageName"]."</title></head><body style=\"padding:10px\" onload=\"doProc();\">$cs<div id='tiki-center'><div class='wikitext'>".$dat.'</div></div></body></html>';
	  		$fw=fopen("$base/pages/".$res['pageName'].'.html', 'wb+');
	  		fwrite($fw, $data);
	  		fclose($fw);
	  		unset($dat);
	  		$page_words = preg_split("/[^A-Za-z0-9\-_]/", $res['data']);
	  		foreach ($page_words as $word) {
	    		$word=strtolower($word);
	    		if (strlen($word)>3 && preg_match("/^[A-Za-z][A-Za-z0-9\_\-]*[A-Za-z0-9]$/", $word)) {
					if (!in_array($word, $words)) {
						$words[] = $word;
						$index[$word]=Array();
					}
					if (!in_array($res['pageName'].'|'.$res['description'], $index[$word])) {
						$index[$word][] = $res['pageName'].'|'.$res['description'];
					}
	    		}
	  		}
		}
		sort($words);
		$i=0;
		$fw = fopen("$base/js/searchdata.js", 'w');
		fwrite($fw, "keywords = new Array();\n");
		foreach ($words as $word) {
			fwrite($fw, "keywords[$i] = Array(\"$word\",Array(");
			$first=true;
			foreach ($index[$word] as $doc) {
				if (!$first) {
					fwrite($fw, ',');
				} else {
					$first=false;
				}
	    		fwrite($fw, '"'.$doc.'"');
			}
	  		fwrite($fw, "));\n");
	  		$i++;
		}
		fclose($fw);

		// write the title page, using:
		// Browser Title, Logo, Site title, Site subtitle
		$fw = fopen("$base/content.html", 'w+');
		$titlepage = "<h1>". $prefs['browsertitle'] . "</h1><p><img src='../../".$prefs['sitelogo_src']."' alt='".$prefs['sitelogo_alt']."' align='center' /></p><h2>". $prefs['sitetitle'] ."</h2><h3>".  $prefs['sitesubtitle']  ."</h3>";
		fwrite($fw, $titlepage);
		fclose($fw);
	}

	public function structure_to_tree($page_ref_id)
	{
		$query = 'select * from `tiki_structures` ts,`tiki_pages` tp where tp.`page_id`=ts.`page_id` and `page_ref_id`=?';
		$result = $this->query($query, array((int) $page_ref_id));
		$res = $result->fetchRow();
		if (empty($res['description'])) {
			$res['description']=$res['pageName'];
		}
		$name = str_replace("'", "\'", $res['description'].'|'.$res['pageName']);
		$code = '';
		$code.= "'$name'=>";
		$query = 'select * from `tiki_structures` ts, `tiki_pages` tp  where tp.`page_id`=ts.`page_id` and `parent_id`=?';
		$result = $this->query($query, array((int) $page_ref_id));
		if ($result->numRows()) {
			$code.='Array(';
			$first = true;
			while ($res=$result->fetchRow()) {
				if (!$first) {
					$code.=',';
				} else {
					$first = false;
				}
				$code.=$this->structure_to_tree($res['page_ref_id']);
			}
			$code.=')';
		} else {
			$code.="''";
		}
		return $code;
	}
	public function traverse($tree,$parent='')
	{
		$code='';
		foreach ($tree as $name => $node) {
			list($name,$link) = explode('|', $name);
			if (is_array($node)) {
				//New folder node is parent++ folder parent is paren
				$new = $parent . 'A';
				$code.='foldersTree'.$new."=insFld(foldersTree$parent,gFld(\"$name\",\"pages/$link.html\"));\n";
				$code.=$this->traverse($node, $new);
			} else {
				$code.="insDoc(foldersTree$parent,gLnk(\"R\",\"$name\",\"pages/$link.html\"));\n";
			}
		}
		return $code;
	}
	public function traverse2($tree)
	{
		$pages = Array();
		foreach ($tree as $name => $node) {
			list($name,$link) = explode('|', $name);
			if (is_array($node)) {
				if (isset($name) && isset($link)) {
					$pageName = $link;
					$pages[] = $pageName;
				}
				$pages2 = $this->traverse2($node);
				foreach ($pages2 as $elem) {
					$pages[] = $elem;
				}
			} else {
				$pages[] = $link;
			}
		}
		return $pages;
	}
	public function move_to_structure($page_ref_id, $structure_id, $begin=true)
	{
		$page_info = $this->s_get_page_info($page_ref_id);
		$query = "update `tiki_structures` set `pos`=`pos`-1 where `pos`>? and `parent_id`=?";
		$this->query($query, array((int) $page_info["pos"], (int) $page_info["parent_id"]));
		if ($begin) {
			$query = "update `tiki_structures` set `pos`=`pos`+1 where `parent_id`=?";
			$this->query($query, array($structure_id));
			$pos = 1;
			$query = "update `tiki_structures` set `structure_id`=?, `parent_id`=?, `pos`=? where `page_ref_id`=?";
			$this->query($query, array($structure_id, $structure_id, $pos+1, $page_ref_id));
		} else {
			$query = "select max(`pos`) from `tiki_structures` where `parent_id`=?";
			$pos = $this->getOne($query, array($structure_id));
			$query = "update `tiki_structures` set `structure_id`=?, `parent_id`=?, `pos`=? where `page_ref_id`=?";
			$this->query($query, array($structure_id, $structure_id, $pos+1, $page_ref_id));
		}
	}
	public function move_to_structure_child($parent_ref_id, $structure_id, $first=true)
	{
		$query = "update `tiki_structures` set `pos`=`pos`-1 where `pos`>? and `parent_id`=?";
		$this->query($query, array((int) $page_info["pos"], (int) $parent_ref_id));
		if ($first) {
			$query = "update `tiki_structures` set `pos`=`pos`+1 where `parent_id`=?";
			$this->query($query, array($structure_id));
			$pos = 1;
			$query = "update `tiki_structures` set `structure_id`=?, `parent_id`=?, `pos`=? where `page_ref_id`=?";
			$this->query($query, array($structure_id, $structure_id, $pos+1, $page_ref_id));
		} else {
			$query = "select max(`pos`) from `tiki_structures` where `parent_id`=?";
			$pos = $this->getOne($query, array($structure_id));
			$query = "update `tiki_structures` set `structure_id`=?, `parent_id`=?, `pos`=? where `page_ref_id`=?";
			$this->query($query, array($structure_id, $structure_id, $pos+1, $page_ref_id));
		}
		return true;
	}
	/* transform a structure into a menu */
	public function to_menu($channels, $structure, $sectionLevel=0, $cumul=0, $params=array())
	{
		$smarty = TikiLib::lib('smarty');
		include_once('lib/smarty_tiki/function.sefurl.php');
		$options = array();
		$cant = 0;
		if (empty($channels)) {
			return array('cant'=>0, 'data'=>array());
		}
		foreach ($channels as $channel) {
			if (empty($channel['sub'])) {
				if (isset($options[$cant-1]['sectionLevel'])) {
					$level = $options[$cant-1]['sectionLevel'];
					while ($level-- > $sectionLevel) {
						$options[]= array('type' => '-', 'sectionLevel'=>$level);
						++$cant;
					}
				}
			}
			$pageName = $channel['pageName'];
			if (isset($params['show_namespace']) && $params['show_namespace'] === 'n') {
				$pageName = !empty($channel['short_pageName']) ? $channel['short_pageName'] : $channel['pageName'];
			}
			$option['name'] = empty($channel['page_alias'])? $pageName: $channel['page_alias'];	
			$option['type'] = empty($channel['sub'])? 'o': ($sectionLevel?$sectionLevel:'s');
			$option['url'] = smarty_function_sefurl(array('page'=>$channel['pageName'], 'structure'=>$structure, 'page_ref_id'=>$channel['page_ref_id'], 'sefurl'=>'n'), $smarty);
			$option['canonic'] = '(('.$channel['pageName'].'))';
			$option['sefurl'] = smarty_function_sefurl(array('page'=>$channel['pageName'], 'structure'=>$structure, 'page_ref_id'=>$channel['page_ref_id']), $smarty);
			$option['position'] = $cant + $cumul;
			$option['sectionLevel'] = $sectionLevel;

			$option['url'] = str_replace('&amp;', '&', $option['url']);			// as of Tiki 7 menu items get encoded later
			$option['sefurl'] = str_replace('&amp;', '&', $option['sefurl']);
			$option['optionId'] = $channel['page_ref_id'];

			++$cant;
			$options[] = $option;
			if (!empty($channel['sub'])) {
				$oSub =  $this->to_menu($channel['sub'], $structure, $sectionLevel+1, $cant+$cumul, $params);
				$cant += $oSub['cant'];
				$options = array_merge($options, $oSub['data']);
			}
		}
		return array('data'=>$options, 'cant'=>$cant);
	}
}

