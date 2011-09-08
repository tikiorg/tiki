<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

// this is an abstract class
class ObjectLib extends TikiLib
{

    function add_object($type, $itemId, $description = '', $name = '', $href = '') {

	$objectId = $this->get_object_id($type, $itemId);

	if ($objectId) {
		if (!empty($description) || !empty($name) || !empty($href)) {
			$description = strip_tags($description);
			$name = strip_tags($name);
			$query = "update `tiki_objects` set `description`=?,`name`=?,`href`=? where `objectId`=?";
			$this->query($query,array($description,$name,$href,$objectId));
	    }
	} else {
		if (empty($href) || empty($name)) { //DIRTY patch: these information are needed in browse freetag even if the freetag module does not provide them: fix->freetag module must provide them
			if ($type == 'wiki page') {
				$href = "tiki-index.php?page=".urlencode($itemId);
				$name = $itemId;
			}
		}
	    $objectId = $this->insert_object($type, $itemId, $description, $name, $href);
	}
    return $objectId;
    }

    function insert_object($type, $itemId, $description = '', $name = '', $href = '') {
		$description = strip_tags($description);
		$name = strip_tags($name);
	
	    $query = "insert into `tiki_objects`(`type`,`itemId`,`description`,`name`,`href`,`created`,`hits`,`comments_locked`) values(?,?,?,?,?,?,?,?)";
	    $result = $this->query($query,array($type,(string) $itemId,$description,$name,$href,(int) $this->now,0,'n'));
	    $query = "select `objectId` from `tiki_objects` where `created`=? and `type`=? and `itemId`=?";
	    $objectId = $this->getOne($query,array((int) $this->now,$type,(string) $itemId));
	    return $objectId;
    }

    function get_object_id($type, $itemId) {
		$query = "select `objectId` from `tiki_objects` where `type`=? and `itemId`=?";
		return $this->getOne($query, array($type, $itemId));
    }

	// Returns an array containing the object ids of objects of the same type. Each entry uses the item id as key and the object id as key. Items with no object id are ignored.
	function get_object_ids($type, $itemIds) {
		$query = "select `objectId`, `itemId` from `tiki_objects` where `type`=? and `itemId` IN (".implode(',', array_fill(0,count($itemIds),'?')).")";
	
		$result = $this->query($query, array_merge(array($type), $itemIds));
		$objectIds = array();
		
		while ($res = $result->fetchRow()) {
			$objectIds[$res["itemId"]] = $res["objectId"];
		}
		return $objectIds;
    }

	function get_needed_perm($objectType, $action) {
		switch ($objectType) {
		case 'wiki page': case 'wiki':
			switch ($action) {
			case 'view': case 'read': return 'tiki_p_view';
			case 'edit': return 'tiki_p_edit';
			}
		case 'article':
			switch ($action) {
			case 'view': case 'read': return 'tiki_p_read_article';
			case 'edit': return 'tiki_p_edit_article';
			}
		case 'post':
			switch ($action) {
			case 'view': case 'read': return 'tiki_p_read_blog';
			case 'edit': return 'tiki_p_create_blog';
			}
		case 'blog':
			switch ($action) {
			case 'view': case 'read': return 'tiki_p_read_blog';
			case 'edit': return 'tiki_p_create_blog';
			}
		case 'faq':
			switch ($action) {
			case 'view': case 'read': return 'tiki_p_view_faqs';
			case 'edit': return 'tiki_p_admin_faqs';
			}
		case 'file gallery':
			switch ($action) {
			case 'view': case 'read': return 'tiki_p_view_file_gallery';
			case 'edit': return 'tiki-admin_file_galleries';
			}
		case 'image gallery':
			switch ($action) {
			case 'view': case 'read': return 'tiki_p_view_image_gallery';
			case 'edit': return 'tiki_p_admin_galleries';
			}
		case 'poll':
			switch ($action) {
			case 'view': case 'read': return 'tiki_p_vote_poll';
			case 'edit': return 'tiki_p_admin';
			}
		case  'comment': case 'comments':
			switch ($action) {
			case 'view': case 'read': return 'tiki_p_read_comments';
			case 'edit': return 'tiki_p_edit_comments';
			}
		case  'trackeritem':
			switch ($action) {
				case 'view': case 'read': return 'tiki_p_view_trackers';
				case 'edit': return 'tiki_p_modify_tracker_items';
			}
		case  'trackeritem_closed':
			switch ($action) {
				case 'view': case 'read': return 'tiki_p_view_trackers';
				case 'edit': return 'tiki_p_modify_tracker_items_closed';
			}
		case  'trackeritem_pending':
			switch ($action) {
				case 'view': case 'read': return 'tiki_p_view_trackers';
				case 'edit': return 'tiki_p_modify_tracker_items_pending';
			}
		case  'tracker':
			switch ($action) {
				case 'view': case 'read': return 'tiki_p_list_trackers';
				case 'edit': return 'tiki_p_admin_trackers';
			}
		default : return '';
		}	
	}

	function get_info($objectType, $object) {
		switch ($objectType) {
			case 'wiki': case 'wiki page':
				global $tikilib; include_once('lib/tikilib.php');
				$info = $tikilib->get_page_info($object);
				return (array('title'=>$object, 'data'=>$info['data'], 'is_html'=>$info['is_html']));
			case 'article':
				global $artlib; require_once 'lib/articles/artlib.php';
				$info = $artlib->get_article($object);
				return (array('title'=>$info['title'], 'data'=>$info['body']));
			case 'file gallery':
				$info = TikiLib::lib('filegal')->get_file_gallery_info($object);
				return ( array('title' => $info['name']));
			case 'blog':
				$info = TikiLib::lib('blog')->get_blog($object);
				return ( array('title' => $info['title']));
			case 'forum':
				$info = TikiLib::lib('comments')->get_forum($object);
				return ( array('title' => $info['name']));
			case 'tracker':
				$info = TikiLib::lib('trk')->get_tracker($object);
				return ( array('title' => $info['name']));
		}
		return (array('error'=>'true'));
	}
	function set_data($objectType, $object, $data) {
		switch ($objectType) {
			case 'wiki': case 'wiki page':
				global $tikilib; include_once('lib/tikilib.php');
				global $user;
				$tikilib->update_page($object, $data, tra('section edit'), $user, $tikilib->get_ip_address());
				break;
		}
	}
	function delete_object($type, $itemId) {
		$query = 'delete from `tiki_objects` where `itemId`=?  and `type`=?';
		$this->query($query, array($itemId, $type));
	}
	
	function get_object($type, $itemId) {
		$query = 'select * from `tiki_objects` where `itemId`=?  and `type`=?';
		$result = $this->query($query,array($itemId, $type));
		return $result->fetchRow();
	}

	function get_object_via_objectid($objectId) {
		$query = 'select * from `tiki_objects` where `objectId`=?';
		$result = $this->query($query,array((int) $objectId));
		return $result->fetchRow();
	}	

	function get_title($type, $id)
	{
		switch ($type) {
		case 'trackeritem':
			return TikiLib::lib('trk')->get_isMain_value(null, $id);
		case 'category':
			return TikiLib::lib('categ')->get_category_name($id);
		}

		$title = $this->table('tiki_objects')->fetchOne('name', array(
			'type' => $type,
			'itemId' => $id,
		));
		
		if ($title) {
			return $title;
		}

		$info = $this->get_info($type, $id);

		if (isset($info['title'])) {
			return $info['title'];
		}
	}
	
	// Returns a hash indicating which permission is needed for viewing an object of desired type.
	static function map_object_type_to_permission() {
		return array(
			'wiki page' => 'tiki_p_view',
			'wiki' => 'tiki_p_view',
			'wiki' => 'tiki_p_view',
			'forum' => 'tiki_p_forum_read',
			'forum post' => 'tiki_p_forum_read',
			'image gallery' => 'tiki_p_view_image_gallery',
			'file gallery' => 'tiki_p_view_file_gallery',
			'tracker' => 'tiki_p_view_trackers',
			'blog' => 'tiki_p_read_blog',
			'blog post' => 'tiki_p_read_blog',
			'quiz' => 'tiki_p_take_quiz',

			// overhead - we are checking individual permission on types below, but they
			// can't have individual permissions, although they can be categorized.
			// should they have permissions too?
			'poll' => 'tiki_p_vote_poll',
			'survey' => 'tiki_p_take_survey',
			'directory' => 'tiki_p_view_directory',
			'faq' => 'tiki_p_view_faqs',
			'sheet' => 'tiki_p_view_sheet',

			// these ones are tricky, because permission type is for container, not object itself.
			// I think we need to refactor permission schemes for them to be wysiwyca - lfagundes
			//
			// by now they're not showing, list_category_objects needs support for ignoring permissions
			// for a type.
			'article' => 'tiki_p_read_article',
			'submission' => 'tiki_p_approve_submission',
			'image' => 'tiki_p_view_image_gallery',
			'calendar' => 'tiki_p_view_calendar',
			'file' => 'tiki_p_download_files',
			'trackeritem' => 'tiki_p_view_trackers',

			// newsletters can't be categorized, although there's some code in tiki-admin_newsletters.php
			// 'newsletter' => ?,
			// 'events' => ?,
		);
	}
}
$objectlib = new ObjectLib;
