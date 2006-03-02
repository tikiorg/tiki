<?php
// CVS: $Id: objectlib.php,v 1.4 2006-03-02 15:15:52 sylvieg Exp $
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

// this is an abstract class
class ObjectLib extends TikiLib {
	function ObjectLib($db) {
		$this->db = $db;
	}

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
	    $objectId = $this->insert_object($type, $itemId, $description, $name, $href);
	}
    return $objectId;
    }

    function insert_object($type, $itemId, $description = '', $name = '', $href = '') {
		$description = strip_tags($description);
		$name = strip_tags($name);
		$now = date("U");
	
	    $query = "insert into `tiki_objects`(`type`,`itemId`,`description`,`name`,`href`,`created`,`hits`)
    values(?,?,?,?,?,?,?)";
	    $result = $this->query($query,array($type,(string) $itemId,$description,$name,$href,(int) $now,0));
	    $query = "select `objectId` from `tiki_objects` where `created`=? and `type`=? and `itemId`=?";
	    $objectId = $this->getOne($query,array((int) $now,$type,(string) $itemId));
	    return $objectId;
    }

    function get_object_id($type, $itemId) {
	$query = "select `objectId` from `tiki_objects` where `type`=? and `itemId`=?";
	return $this->getOne($query, array($type, $itemId));
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
				global $tikilib; include_once('lib/tikilib.php');
				$info = $artlib->$tikilib->get_article($object);
				return (array('title'=>$info['title'], 'data'=>$info['body']));
		}
		return (array('error'=>'true'));
	}
	function set_data($objectType, $object, $data) {
		switch ($objectType) {
			case 'wiki': case 'wiki page':
				global $tikilib; include_once('lib/tikilib.php');
				global $user;
				$tikilib->update_page($object, $data, tra('section edit'), $user, $_SERVER["REMOTE_ADDR"]);
				break;
			case 'article':
				global $artlib; include_once('lib/articles/artlib.php');
				$artlib->replace_article();
				break;
		}
	}
	function delete_object($type, $itemId) {
		$query = 'delete from `tiki_objects` where `itemId`=?  and `type`=?';
		$this->query($query, array($itemId, $type));
	}
}
global $dbTiki;
$objectlib = new ObjectLib($dbTiki);
?>