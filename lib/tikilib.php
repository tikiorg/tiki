<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

//if ( ! defined('DATE_FORMAT_UNIXTIME') ) define('DATE_FORMAT_UNIXTIME', 5);

// performance collecting:
//require_once ('lib/tikidblib-debug.php');

// This class is included by all the Tiki php scripts, so it's important
// to keep the class as small as possible to improve performance.
// What goes in this class:
// * generic functions that MANY scripts must use
// * shared functions (marked as /*shared*/) are functions that are
//   called from Tiki modules.

require_once('lib/core/TikiDb/Bridge.php');

class TikiLib extends TikiDb_Bridge
{
	var $buffer;
	var $flag;
	var $parser;
	var $pre_handlers = array();
	var $pos_handlers = array();
	var $postedit_handlers = array();
	var $usergroups_cache = array();

	var $num_queries = 0;
	var $now;

	var $cache_page_info;
	var $sessionId = null;

	// DB param left for interface compatibility, although not considered
	function __construct( $db = null ) {
		$this->now = time();
	}


	function httprequest($url, $reqmethod = "GET") {
		global $prefs;
		// test url :
		// rewrite url if sloppy # added a case for https urls
		if ( (substr($url,0,7) <> "http://") and
				(substr($url,0,8) <> "https://")
			 ) {
			$url = "http://" . $url;
		}
		// (cdx) params for HTTP_Request.
		// The timeout may be defined by a DEFINE("HTTP_TIMEOUT",5) in some file...
		$aSettingsRequest=array("method"=>$reqmethod,"timeout"=>5);

		if (substr_count($url, "/") < 3) {
			$url .= "/";
		}

		//handle url embedded user:pass
		$spliturl=parse_url($url);
		if(!empty($spliturl['user']) && !empty($spliturl['pass'])) {
			$aSettingsRequest["pass"]=$spliturl['pass'];
			$aSettingsRequest["user"]=$spliturl['user'];
			$url=str_replace($spliturl['user'].":".$spliturl['pass']."@", null, $url);
		}

		if (!preg_match("/^[-_a-zA-Z0-9:\/\.\?&;=\+~%,]*$/",$url)) return false;

		// Proxy settings
		if ($prefs['use_proxy'] == 'y') {
			$aSettingsRequest["proxy_host"]=$prefs['proxy_host'];
			$aSettingsRequest["proxy_port"]=$prefs['proxy_port'];
		}
		include_once ('lib/pear/HTTP/Request.php');
		$aSettingsRequest['allowRedirects'] = true;
		$req = new HTTP_Request($url, $aSettingsRequest);
		$data="";
		// (cdx) return false when can't connect
		// I prefer throw a PEAR_Error. You decide ;)
		if (PEAR::isError($oError=$req->sendRequest())) {
			return(false);
			/* Please people, don't use fopen. It's potentially unsafe
			 * because if any form does not check the url, you can upload
			 * /etc/passwd and other file from the local host.
			 * it's also more safe to use httprequest, because the admin can set
			 * a proxy so that noone can upload files in tiki from behind a
			 * firewall (safes the net where tiki runs in).
			 $fp = fopen($url, "r");

			 if ($fp) {
			 $data = '';
			 while(!feof($fp)) {
			 $data .= fread($fp,4096);
			 }
			 fclose ($fp);
			 }
			 if ($data =="") return false;
			 */
		} else {
			$data = $req->getResponseBody();
		}
		return $data;
	}

	/*shared*/
	function get_dsn_by_name($name) {
		if ($name == 'local') {
			return true;
		}
		return $this->getOne("select `dsn`  from `tiki_dsn` where `name`='$name'");
	}

	function get_dsn_info($name) {
		$info = array();

		$dsnsqlplugin = $this->get_dsn_by_name($name);

		$parsedsn = $dsnsqlplugin;
		$info['driver'] = strtok( $parsedsn, ":" );
		$parsedsn = substr( $parsedsn, strlen($info['driver']) + 3 );
		$info['user'] = strtok( $parsedsn, ":" );
		$parsedsn = substr( $parsedsn, strlen($info['user']) + 1 );
		$info['password'] = strtok( $parsedsn, "@" );
		$parsedsn = substr( $parsedsn, strlen($info['password']) + 1 );
		$info['host'] = strtok( $parsedsn, "/" );
		$parsedsn = substr( $parsedsn, strlen($info['host']) + 1 );
		$info['database'] = $parsedsn;

		return $info;
	}

	function get_db_by_name( $name ) {
		include_once ('tiki-setup.php');
		if( $name == 'local' || empty($name) ) {
			return TikiDb::get();
		} else {
			static $connectionMap = array();

			if( ! isset( $connectionMap[$name] ) ) {
				$connectionMap[$name] = false;

				$info = $this->get_dsn_info( $name );
				$dbdriver = $info['driver'];
				$dbuserid = $info['user'];
				$dbpassword = $info['password'];
				$dbhost = $info['host'];
				$database = $info['database'];
				
				$api_tiki = null;
				require 'db/local.php';				
				if (isset($api_tiki) &&  $api_tiki == 'adodb') {
					require_once ('lib/adodb/adodb.inc.php');
					$dbsqlplugin = ADONewConnection($dbdriver);
					if( $dbsqlplugin->NConnect( $dbhost, $dbuserid, $dbpassword, $database ) ) {
						require_once ('lib/core/TikiDb/Adodb.php');
						$connectionMap[$name] = new TikiDb_AdoDb( $dbsqlplugin );
					}
				} else {
					require_once ('lib/core/TikiDb/Pdo.php');
					$dbsqlplugin = new PDO("$dbdriver:host=$dbhost;dbname=$database", $dbuserid, $dbpassword);
					$connectionMap[$name] = new TikiDb_Pdo( $dbsqlplugin );
				}
			}
			return $connectionMap[$name];
		}
	}

	/* convert data to iso-8601 format */
	// used for atom export. date() use is okay, as we use server timezone in such case
	function iso_8601 ($timestamp) {
		$main_date = $this->date_format("%Y-%m-%d\T%H:%M:%S", $timestamp);

		$tz = $this->date("%O", $timestamp);

		$return = $main_date . $tz;

		return $return;
	}

	/*shared*/
    // Returns IP address or IP address forwarded by the proxy if feature load balancer is set
	function get_ip_address() {
        global $prefs;
        if (isset($prefs['feature_loadbalancer']) && $prefs['feature_loadbalancer'] == "y") {
            $ip = null;

            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $fwips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $ip = $fwips[0];
            }
            if ((empty($ip) || strtolower($ip) == 'unknown') && isset($_SERVER['REMOTE_ADDR'])) {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
            return $ip;
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
			return $_SERVER['REMOTE_ADDR'];
		} else {
			return '0.0.0.0';
		}
	}

	/*shared*/
	function check_rules($user, $section) {
		// Admin is never banned
		if ($user == 'admin')
			return false;

		$ips = $this->get_ip_address();
		$query = "select tb.`message`,tb.`user`,tb.`ip1`,tb.`ip2`,tb.`ip3`,tb.`ip4`,tb.`mode` from `tiki_banning` tb, `tiki_banning_sections` tbs where tbs.`banId`=tb.`banId` and tbs.`section`=? and ( (tb.`use_dates` = ?) or (tb.`date_from` <= ? and tb.`date_to` >= ?))";
		$result = $this->fetchAll($query,array($section,'n',(int)$this->now,(int)$this->now));

		foreach ( $result as $res ) {
			if (!$res['message']) {
				$res['message'] = tra('You are banned from'). ':' . $section;
			}

			if ($user && $res['mode'] == 'user') {
				// check user
				$pattern = '/' . $res['user'] . '/';

				if (preg_match($pattern, $user)) {
					return $res['message'];
				}
			} else {
				// check ip
				if (count($ips) == 4) {
					if (($ips[0] == $res['ip1'] || $res['ip1'] == '*') && ($ips[1] == $res['ip2'] || $res['ip2'] == '*')
							&& ($ips[2] == $res['ip3'] || $res['ip3'] == '*') && ($ips[3] == $res['ip4'] || $res['ip4'] == '*')) {
						return $res['message'];
					}
				}
			}
		}
		return false;
	}

	// $noteId 0 means create a new note
	function replace_note($user, $noteId, $name, $data, $parse_mode = null) {
		$size = strlen($data);

		$queryData = array(
			'user' => $user,
			'name' => $name,
			'data' => $data,
			'created' => $this->now,
			'lastModif' => $this->now,
			'size' => (int) $size,
			'parse_mode' => $parse_mode,
		);

		$userNotes = $this->table('tiki_user_notes');
		if ($noteId) {
			$userNotes->update($queryData, array(
				'noteId' => (int) $noteId,
			));
		} else {
			$noteId = $userNotes->insert($queryData);
		}

		return $noteId;
	}

	function list_watches($offset, $maxRecords, $sort_mode, $find) {
		$mid = '';
		$mid2 = '';
		$bindvars = $bindvars1 = $bindvars2 = array();
		if ($find) {	
			$mid = ' where `event` like ? or `email` like ? or `user` like ? or `object` like ? or `type` like ?';
			$mid2 = ' where `event` like ? or `group` like ? or `object` like ? or `type` like ?';
			$bindvars1 = array("%$find%", "%$find%", "%$find%", "%$find%", "%$find%");
			$bindvars2 = array("%$find%", "%$find%", "%$find%", "%$find%");
		}
		$query = "select 'user' as watchtype, `watchId`, `user`, `event`, `object`, `title`, `type`, `url`, `email` from `tiki_user_watches` $mid 
			UNION ALL
				select 'group' as watchtype, `watchId`, `group`, `event`, `object`, `title`, `type`, `url`, '' as `email`
				from `tiki_group_watches` $mid2
			order by ".$this->convertSortMode($sort_mode);
		$query_cant = 'select count(*) from `tiki_user_watches` '.$mid;
		$query_cant2 = 'select count(*) from `tiki_group_watches` '. $mid2;
		$ret = $this->fetchAll($query, array_merge($bindvars1, $bindvars2), $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars1) + $this->getOne($query_cant2, $bindvars2);
		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval; 
	}


	/*shared*/
	function add_user_watch($user, $event, $object, $type = NULL, $title = NULL, $url = NULL, $email = NULL) {
		// Allow a warning when the watch won't be effective
		if (empty($email)) {
			global $userlib;
			$email = $userlib->get_user_email($user);
			if (empty($email)) {
				return false;
			}
		}
		
		$this->remove_user_watch( $user, $event, $object, $type );

		$userWatches = $this->table('tiki_user_watches');
		$userWatches->insert(array(
			'user' => $user,
			'event' => $event,
			'object' => $object,
			'email' => $email,
			'type' => $type,
			'title' => $title,
			'url' => $url,
		));
		return true;
	}

	function add_group_watch($group, $event, $object, $type = NULL, $title = NULL, $url = NULL) {
		
		if ($type == 'Category' && $object == 0) {
			return false;
		} else {
			$this->remove_group_watch( $group, $event, $object, $type );
			$groupWatches = $this->table(array(
				'group' => $group,
				'event' => $event,
				'object' => $object,
				'type' => $type,
				'title' => $title,
				'url' => $url,
			));
			return true;
		}
	}

	/**
	 * get_user_notification: returns the owner (user) related to a watchId 
	 * 
	 * @param mixed $id watchId 
	 * @access public
	 * @return the user login related to the watchId
	 */
	function get_user_notification($id) {

		$query = "select `user` from `tiki_user_watches` where `watchId`=?";
		return $this->getOne($query, array($id));

	}
	/*shared*/
	function remove_user_watch_by_id($id) {
		global $tiki_p_admin_notifications, $user;
		if ( $tiki_p_admin_notifications === 'y' or $user === $this->get_user_notification($id) ) {
			$this->table('tiki_user_watches')->delete(array(
				'watchId' => (int) $id,
			));

			return true;
		}

		return false;
	}

	function remove_group_watch_by_id($id) {
		$this->table('tiki_group_watches')->delete(array(
			'watchId' => (int) $id,
		));
	}

	/*shared*/
	function remove_user_watch($user, $event, $object, $type = 'wiki page') {
		$conditions = array(
			'user' => $user,
			'event' => $event,
			'object' => $object,
		);
		if (isset($type)) {
			$conditions['type'] = $type;
		}

		$this->table('tiki_user_watches')->deleteMultiple($conditions);
	}

	function remove_group_watch($group, $event, $object, $type = 'wiki page') {
		$conditions = array(
			'group' => $group,
			'event' => $event,
			'object' => $object,
		);
		if (isset($type)) {
			$conditions['type'] = $type;
		}

		$this->table('tiki_group_watches')->deleteMultiple($conditions);
	}

	/*shared*/
	function get_user_watches($user, $event = '') {
		$mid = '';
		$bindvars=array($user);
		if ($event) {
			$mid = " and `event`=? ";
			$bindvars[]=$event;
		}

		$query = "select * from `tiki_user_watches` where binary `user`=? $mid";
		return $this->fetchAll($query,$bindvars);
	}

	/*shared*/
	function get_watches_events() {
		$query = "select distinct `event` from `tiki_user_watches`";
		$result = $this->fetchAll($query,array());
		$ret = array();
		foreach ( $result as $res ) {
			$ret[] = $res['event'];
		}
		return $ret;
	}

	/*shared*/
	function user_watches($user, $event, $object, $type = NULL) {
		if (is_array($event)) {
			$query = "select `event` from `tiki_user_watches` where `user`=? and `object`=? and `event` in (".implode(',',array_fill(0, count($event),'?')).")";
			$bindvars = array_merge(array($user, $object), $event);
			if ($type) {
				$query .= " and `type`=?";
				$bindvars[] = $type;
			}
			$result = $this->fetchAll($query, $bindvars);
			if ( count($result) === 0 ) {
				return false;
			}
			$ret = array();
			foreach ( $result as $res ) {
				$ret[] = $res['event'];
			}
			return $ret;
		} else {
			$query = "select count(*) from `tiki_user_watches` where `user`=? and `object`=? and `event`=?";
			$bindvars = array($user, $object, $event);
			if ($type) {
				$query .= " and `type`=?";
				$bindvars[] = $type;
			}
			return $this->getOne($query, $bindvars);
		}
	}

	function get_groups_watching( $object, $event, $type = NULL ) {
		$query = 'SELECT `group` FROM `tiki_group_watches` WHERE `object` = ? AND `event` = ?';
		$bindvars = array( $object, $event);
		if ($type) {
			$query .= ' AND `type` = ?';
			$bindvars[]= $type;
		}
		$result = $this->fetchAll( $query, $bindvars );

		$groups = array();
		foreach( $result as $row ) {
			$groups[] = $row['group'];
		}
		return $groups;
	}

	/*shared*/
	function get_user_event_watches($user, $event, $object) {
		$query = "select * from `tiki_user_watches` where `user`=? and `event`=? and `object`=?";
		$result = $this->query($query,array($user,$event,$object));
		if (!$result->numRows())
			return false;
		$res = $result->fetchRow();
		return $res;
	}

	/*shared*/
	function get_event_watches($event, $object, $info=null) {
		global $prefs;
		$ret = array();

		$where = array();
		$mid = '';
		if( $prefs['feature_user_watches_translations'] == 'y'  && $event == 'wiki_page_changed') {
			// If $prefs['feature_user_watches_translations'] is turned on, also look for
			// pages in a translation group.
			$mid = "`event`=?";
			$bindvars[] = $event;
			global $multilinguallib;
			include_once("lib/multilingual/multilinguallib.php");
			$page_info = $this->get_page_info( $object );
			$pages = $multilinguallib->getTranslations('wiki page', $page_info['page_id'], $object, '' );
			foreach ($pages as $page) {
				$mids[] = "`object`=?";
				$bindvars[] = $page['objName'];
			}
			$mid .= ' and ('.implode(' or ', $mids).')';
		} elseif ( $prefs['feature_user_watches_translations'] == 'y' 
			&& $event == 'wiki_page_created' ) {
			$page_info = $this->get_page_info( $object );
			$mid = "`event`='wiki_page_in_lang_created' and `object`=? and `type`='lang'";
			$bindvars[] = $page_info['lang'];
		} elseif ( $prefs['feature_user_watches_languages'] == 'y' && $event == 'category_changed' ) {
			$mid = "`object`=? and ((`event`='category_changed_in_lang' and `type`=? ) or (`event`='category_changed'))";
			$bindvars[] = $object;
			$bindvars[] = $info['lang'];
		} elseif ($event == 'forum_post_topic') {
			$mid = "(`event`=? or `event`=?) and `object`=?";
			$bindvars[] = $event;
			$bindvars[] = 'forum_post_topic_and_thread';
			$bindvars[] = $object;
		} elseif ($event == 'forum_post_thread') {
			$mid = "(`event`=? and `object`=?) or ( `event`=? and `object`=?)";
			$bindvars[] = $event;
			$bindvars[] = $object;
			$bindvars[] = 'forum_post_topic_and_thread';
			$forumId = $info['forumId'];
			$bindvars[] = $forumId;
		} else {
			$extraEvents = "";
			if (substr_count($event, 'article_')) {
				$extraEvents = " or `event`='article_*'";
			} elseif ($event == 'wiki_comment_changes') {
				$extraEvents = " or `event`='wiki_page_changed'";
			}
			$mid = "(`event`=?$extraEvents) and (`object`=? or `object`='*')";
			$bindvars[] = $event;
			$bindvars[] = $object;
		}

		// Obtain the list of watches on event/object for user watches
		// Union obtains all users member of groups being watched
		// Distinct union insures there are no duplicates
		$query = "select tuw.`watchId`, tuw.`user`, tuw.`event`, tuw.`object`, tuw.`title`, tuw.`type`, tuw.`url`, tuw.`email`, 
				tup1.`value` as language, tup2.`value` as mailCharset
			from 
				`tiki_user_watches` tuw 
				left join `tiki_user_preferences` tup1 on (tup1.`user`=tuw.`user` and tup1.`prefName`='language') 
				left join `tiki_user_preferences` tup2 on (tup2.`user`=tuw.`user` and tup2.`prefName`='mailCharset')
				where $mid
			UNION DISTINCT
			select tgw.`watchId`, uu.`login`, tgw.`event`, tgw.`object`, tgw.`title`, tgw.`type`, tgw.`url`, uu.`email`,
				tup1.`value` as language, tup2.`value` as mailCharset
			from
				`tiki_group_watches` tgw
				inner join `users_usergroups` ug on tgw.`group` = ug.`groupName`
				inner join `users_users` uu on ug.`userId` = uu.`userId` and uu.`email` is not null and uu.`email` <> ''
				left join `tiki_user_preferences` tup1 on (tup1.`user`=uu.`login` and tup1.`prefName`='language') 
				left join `tiki_user_preferences` tup2 on (tup2.`user`=uu.`login` and tup2.`prefName`='mailCharset')
				where $mid
				";
		$result = $this->fetchAll($query,array_merge( $bindvars, $bindvars ));

		if ( count($result) > 0 ) {

			foreach ( $result as $res ) {
				if (empty($res['language'])) {
					$res['language'] = $this->get_preference('site_language');
				}
				switch($event) {
				case 'wiki_page_changed':
				case 'wiki_page_created':
					$res['perm']=($this->user_has_perm_on_object($res['user'],$object,'wiki page','tiki_p_view') ||
							$this->user_has_perm_on_object($res['user'],$object,'wiki page','tiki_p_admin_wiki'));
					break;
				case 'tracker_modified':
					$res['perm'] = $this->user_has_perm_on_object($res['user'],$object,'tracker','tiki_p_view_trackers');
					break;
				case 'tracker_item_modified':
					$res['perm'] = $this->user_has_perm_on_object($res['user'],$info['trackerId'],'tracker','tiki_p_view_trackers');
					break;
				case 'blog_post':
					$res['perm']=($this->user_has_perm_on_object($res['user'],$object,'blog','tiki_p_read_blog') ||
							$this->user_has_perm_on_object($res['user'],$object,'blog','tiki_p_admin_blog'));
					break;
				case 'map_changed':
					$res['perm']=$this->user_has_perm_on_object($res['user'],$object,'map','tiki_p_map_view');
					break;
				case 'forum_post_topic':
					$res['perm']=($this->user_has_perm_on_object($res['user'],$object,'forum','tiki_p_forum_read') ||
							$this->user_has_perm_on_object($res['user'],$object,'forum','tiki_p_admin_forum'));
					break;
				case 'forum_post_thread':
					$res['perm']=($this->user_has_perm_on_object($res['user'],$forumId,'forum','tiki_p_forum_read') ||
							$this->user_has_perm_on_object($res['user'],$object,'forum','tiki_p_admin_forum'));
					break;
				case 'file_gallery_changed':
					$res['perm']=($this->user_has_perm_on_object($res['user'],$object,'file gallery','tiki_p_view_file_gallery') ||
							$this->user_has_perm_on_object($res['user'],$object,'file gallery','tiki_p_download_files'));                    	
					break;
				case 'article_submitted':
				case 'topic_article_created':
				case 'article_edited':
				case 'topic_article_edited':
				case 'article_deleted':
				case 'topic_article_deleted':
					global $userlib;
					$res['perm']= ($userlib->user_has_permission($res['user'],'tiki_p_read_article') &&
							(empty($object) || $this->user_has_perm_on_object($res['user'], $object,'topic','tiki_p_topic_read')));
					break;
				case 'calendar_changed':
					$res['perm']= $this->user_has_perm_on_object($res['user'],$object,'calendar','tiki_p_view_calendar');
					break;
				case 'image_gallery_changed':
					$res['perm'] = $this->user_has_perm_on_object($res['user'],$object,'image gallery','tiki_p_view_image_gallery');
					break;
				case 'category_changed':
					global $categlib; include_once ('lib/categories/categlib.php');
					$res['perm']= $categlib->has_view_permission($res['user'],$object);
					break;
				case 'fgal_quota_exceeded':
					global $tiki_p_admin_file_galleries;
					$res['perm'] = ($tiki_p_admin_file_galleries == 'y');
					break;
				case 'article_commented':
				case 'wiki_comment_changes':
					$res['perm'] = $this->user_has_perm_on_object($res['user'],$object,'comments','tiki_p_read_comments');
					break;
				case 'user_registers':
					global $userlib;
					$res['perm'] = $userlib->user_has_permission($res['user'], 'tiki_p_admin');
					break;
				default:
					// for security we deny all others.
					$res['perm']=FALSE;
					break;
				}

				if($res['perm']) {
					$ret[] = $res;
				}
			}			
		}

		// Also include users that are watching a category to which this object belongs to.
		if ( $event != 'category_changed' )  {    	
			if ($prefs['feature_categories'] == 'y') {
				global $categlib; require_once('lib/categories/categlib.php');
				$objectType="";
				switch($event) {
				case 'wiki_page_changed': $objectType="wiki page"; break;
				case 'wiki_page_created': $objectType="wiki page"; break;
				case 'blog_post': $objectType="blog"; break;
				case 'map_changed': $objectType="map_changed"; break;
				case 'forum_post_topic': $objectType="forum"; break;
				case 'forum_post_thread': $objectType="forum"; break;
				case 'file_gallery_changed': $objectType="file gallery"; break;
				case 'article_submitted': $objectType="topic"; break;			
				case 'image_gallery_changed': $objectType="image gallery"; break;
				case 'tracker_modified': $objectType="tracker"; break; 	
				case 'tracker_item_modified': $objectType="tracker"; break;
				case 'calendar_changed': $objectType="calendar"; break;
				}
				if ( $objectType != "") {

					// If a forum post was changed, check the categories of the forum.  
					if ( $event == "forum_post_thread" ) {
						include_once ("lib/comments/commentslib.php");
						global $commentslib;            	
						$object = $commentslib->get_comment_forum_id($object);
					}

					// If a tracker item was changed, check the categories of the tracker.  
					if ( $event == "tracker_item_modified" ) {
						include_once ("lib/trackers/trackerlib.php");
						global $trklib;            	
						$object = $trklib->get_tracker_for_item($object);
					}

					$categs = $categlib->get_object_categories($objectType, $object);

					foreach ($categs as $category) {           		                 
						$watching_users = $this->get_event_watches('category_changed', $category, $info);

						// Add all users that are not already included
						foreach ($watching_users as $wu) {
							$included = false;
							foreach ($ret as $item) {
								if ($item['user'] == $wu['user']) {
									$included = true;
								}
							}
							if (!$included) {
								$ret[] = $wu;
							}
						}
					}
				}
			}
		}
		return $ret;
	}

	/*shared*/
	function dir_stats() {
		$aux = array();
		$aux["valid"] = $this->getOne("select count(*) from `tiki_directory_sites` where `isValid`=?",array('y'));
		$aux["invalid"] = $this->getOne("select count(*) from `tiki_directory_sites` where `isValid`=?",array('n'));
		$aux["categs"] = $this->getOne("select count(*) from `tiki_directory_categories`",array());
		$aux["searches"] = $this->getOne("select sum(`hits`) from `tiki_directory_search`",array());
		$aux["visits"] = $this->getOne("select sum(`hits`) from `tiki_directory_sites`",array());
		return $aux;
	}

	/*shared*/
	function dir_list_all_valid_sites2($offset, $maxRecords, $sort_mode, $find) {

		if ($find) {
			$mid = " where `isValid`=? and (`name` like ? or `description` like ?)";
			$bindvars=array('y','%'.$find.'%','%'.$find.'%');
		} else {
			$mid = " where `isValid`=? ";
			$bindvars=array('y');
		}

		$query = "select * from `tiki_directory_sites` $mid order by ".$this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_directory_sites` $mid";
		$result = $this->fetchAll($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);

		$retval = array();
		$retval["data"] = $result;
		$retval["cant"] = $cant;
		return $retval;
	}

	/*shared*/
	function get_directory($categId) {
		$query = "select * from `tiki_directory_categories` where `categId`=?";
		$result = $this->query($query,array($categId));
		if (!$result->numRows()) return false;
		$res = $result->fetchRow();
		return $res;
	}

	/*shared*/
	function user_unread_messages($user) {
		$cant = $this->getOne("select count(*) from `messu_messages` where `user`=? and `isRead`=?",array($user,'n'));
		return $cant;
	}

	/*shared*/
	function get_online_users() {
		if ( ! isset($this->online_users_cache) ) {
			$this->update_session();
			$this->online_users_cache=array();
			$query = "select s.`user`, p.`value` as `realName`, `timestamp`, `tikihost` from `tiki_sessions` s left join `tiki_user_preferences` p on s.`user`<>? and s.`user` = p.`user` and p.`prefName` = 'realName' where s.`user` is not null;";
			$result = $this->fetchAll($query,array(''));
			$ret = array();
			foreach ($result as $res ) {
				$res['user_information'] = $this->get_user_preference($res['user'], 'user_information', 'public');
				$res['allowMsgs'] = $this->get_user_preference($res['user'], 'allowMsgs', 'y');
				$this->online_users_cache[$res['user']] = $res;
			}
		}
		return $this->online_users_cache;
	}

	/*shared*/
	function is_user_online($whichuser) {
		if(!isset($this->online_users_cache)) {
			$this->get_online_users();
		}

		return(isset($this->online_users_cache[$whichuser]));
	}

	/*shared*/
	function get_user_items($user) {
		$items = array();

		$query = "select ttf.`trackerId`, tti.`itemId` from `tiki_tracker_fields` ttf, `tiki_tracker_items` tti, `tiki_tracker_item_fields` ttif";
		$query .= " where ttf.`fieldId`=ttif.`fieldId` and ttif.`itemId`=tti.`itemId` and `type`=? and tti.`status`=? and `value`=?";
		$result = $this->fetchAll($query,array('u','o',$user));
		$ret = array();

		//FIXME Perm:filter ?
		foreach ( $result as $res ) {
			if (!$this->user_has_perm_on_object($user, $res['trackerId'], 'tracker', 'tiki_p_view_trackers')) {
				continue;
			}
			$itemId = $res["itemId"];

			$trackerId = $res["trackerId"];
			// Now get the isMain field for this tracker
			$fieldId = $this->getOne("select `fieldId`  from `tiki_tracker_fields` ttf where `isMain`=? and `trackerId`=?",array('y',(int)$trackerId));
			// Now get the field value
			$value = $this->getOne("select `value`  from `tiki_tracker_item_fields` where `fieldId`=? and `itemId`=?",array((int)$fieldId,(int)$itemId));
			$tracker = $this->getOne("select `name`  from `tiki_trackers` where `trackerId`=?",array((int)$trackerId));
			$aux["trackerId"] = $trackerId;
			$aux["itemId"] = $itemId;
			$aux["value"] = $value;
			$aux["name"] = $tracker;

			if (!in_array($itemId, $items)) {
				$ret[] = $aux;
				$items[] = $itemId;
			}
		}

		$groups = $this->get_user_groups($user);

		foreach ($groups as $group) {
			$query = "select ttf.`trackerId`, tti.`itemId` from `tiki_tracker_fields` ttf, `tiki_tracker_items` tti, `tiki_tracker_item_fields` ttif ";
			$query .= " where ttf.`fieldId`=ttif.`fieldId` and ttif.`itemId`=tti.`itemId` and `type`=? and tti.`status`=? and `value`=?";
			$result = $this->fetchAll($query,array('g','o',$group));

			foreach ( $result as $res ) {
				$itemId = $res["itemId"];

				$trackerId = $res["trackerId"];
				// Now get the isMain field for this tracker
				$fieldId = $this->getOne("select `fieldId`  from `tiki_tracker_fields` ttf where `isMain`=? and `trackerId`=?",array('y',(int)$trackerId));
				// Now get the field value
				$value = $this->getOne("select `value`  from `tiki_tracker_item_fields` where `fieldId`=? and `itemId`=?",array((int)$fieldId,(int)$itemId));
				$tracker = $this->getOne("select `name`  from `tiki_trackers` where `trackerId`=?",array((int)$trackerId));
				$aux["trackerId"] = $trackerId;
				$aux["itemId"] = $itemId;
				$aux["value"] = $value;
				$aux["name"] = $tracker;

				if (!in_array($itemId, $items)) {
					$ret[] = $aux;
					$items[] = $itemId;
				}
			}
		}
		return $ret;
	}

	/*shared*/
	function get_quiz($quizId) {
		$query = "select * from `tiki_quizzes` where `quizId`=?";

		$result = $this->query($query,array((int) $quizId));

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow();
		return $res;
	}

	function compute_quiz_stats() {
		$query = "select `quizId`  from `tiki_user_quizzes`";

		$result = $this->fetchAll($query,array());

		$quizStatsSum = $this->table('tiki_quiz_stats_sum');

		foreach ( $result as $res ) {
			$quizId = $res["quizId"];

			$quizName = $this->getOne("select `name`  from `tiki_quizzes` where `quizId`=?",array((int)$quizId));
			$timesTaken = $this->getOne("select count(*) from `tiki_user_quizzes` where `quizId`=?",array((int)$quizId));
			$avgpoints = $this->getOne("select avg(`points`) from `tiki_user_quizzes` where `quizId`=?",array((int)$quizId));
			$maxPoints = $this->getOne("select max(`maxPoints`) from `tiki_user_quizzes` where `quizId`=?",array((int)$quizId));
			$avgavg = ($maxPoints != 0) ? $avgpoints / $maxPoints * 100 : 0.0;
			$avgtime = $this->getOne("select avg(`timeTaken`) from `tiki_user_quizzes` where `quizId`=?",array((int)$quizId));

			$quizStatsSum->delete(array(
				'quizId' => (int) $quizId,
			));
			$quizStatsSum->insert(array(
				'quizId' => (int) $quizId,
				'quizName' => $quizName,
				'timesTaken' => (int) $timesTaken,
				'avgpoints' => (float) $avgpoints,
				'avgtime' => $avgtime,
				'avgavg' => $avgavg,
			));
		}
	}

	function list_quizzes($offset, $maxRecords, $sort_mode = 'name_desc', $find = null) {
		$bindvars = array();
		$mid = '';
		if ( ! empty($find) ) {
			$findesc = '%' . $find . '%';
			$mid = " where (`name` like ? or `description` like ?)";
			$bindvars = array($findesc, $findesc);
		}

		$query = "select `quizId` from `tiki_quizzes` $mid";
		$result = $this->fetchAll($query, $bindvars);
		$res = $ret = $retids = array();
		$n = 0;

		//FIXME Perm:filter ?
		foreach ( $result as $res ) {
			global $user;
			$objperm = $this->get_perm_object($res['quizId'], 'quizzes', '', false);

			if ( $objperm['tiki_p_take_quiz'] == 'y' ) {
				if ( ($maxRecords == -1) || (($n >= $offset) && ($n < ($offset + $maxRecords))) ) {
					$retids[] = $res['quizId'];
				}
				$n++;
			}
		}

		if ($n > 0) {
		  $query = 'select * from `tiki_quizzes` where `quizId` in (' . implode(',', $retids) . ') order by ' . $this->convertSortMode($sort_mode);
		  $result = $this->fetchAll($query);
		  foreach ( $result as $res ) {
				$res['questions'] = $this->getOne('select count(*) from `tiki_quiz_questions` where `quizId`=?', array( (int) $res['quizId'] ));
				$res['results'] = $this->getOne('select count(*) from `tiki_quiz_results` where `quizId`=?', array( (int) $res['quizId'] ));
				$ret[] = $res;
			}
		}
		$retval['data'] = $ret;
		$retval['cant'] = $n;
		return $retval;
	}

	/*shared*/
	function list_quiz_sum_stats($offset, $maxRecords, $sort_mode, $find) {
		$this->compute_quiz_stats();
		if ($find) {
			$findesc = '%' . $find . '%';
			$mid = ' where `quizName` like ? ';
			$bindvars=array($findesc);
		} else {
			$mid = "  ";
			$bindvars=array();
		}

		$query = "select * from `tiki_quiz_stats_sum` $mid order by " . $this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_quiz_stats_sum` $mid";
		$ret = $this->fetchAll($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function get_tracker($trackerId) {
		$query = "select * from `tiki_trackers` where `trackerId`=?";
		$result = $this->query($query,array((int) $trackerId));
		if (!$result->numRows()) return false;
		$res = $result->fetchRow();
		return $res;
	}
	/*shared*/

	function list_trackers($offset=0, $maxRecords=-1, $sort_mode='name_asc', $find='') {
		global $categlib; require_once('lib/categories/categlib.php');
		$join = '';
		$where = '';
		$bindvars = array();
		if( $jail = $categlib->get_jail() ) {
			$categlib->getSqlJoin($jail, 'tracker', '`tiki_trackers`.`trackerId`', $join, $where, $bindvars);
		}	
		if ($find) {
			$findesc = '%' . $find . '%';
			$where .= ' and (`tiki_trackers`.`name` like ? or `tiki_trackers`.`description` like ?)';
			$bindvars = array_merge($bindvars, array($findesc, $findesc));
		}
		$query = "select * from `tiki_trackers` $join where 1=1 $where order by `tiki_trackers`.".$this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_trackers` $join where 1=1 $where";
		$result = $this->fetchAll($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();
		$list = array();
		//FIXME Perm:filter ?
		foreach ( $result as $res ) {
			global $user;
			$add=$this->user_has_perm_on_object($user,$res['trackerId'],'tracker','tiki_p_view_trackers');
			if ($add) {
				$ret[] = $res;
				$list[$res['trackerId']] = $res['name'];
			}
		}
		$retval = array();
		$retval["list"] = $list;
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function list_surveys($offset, $maxRecords, $sort_mode, $find) {
		if ($find) {
		  $findesc = '%' . $find . '%';
		  $mid = " where (`name` like ? or `description` like ?)";
		  $bindvars=array($findesc, $findesc);
		} else {
		  $mid = '';
		  $bindvars=array();
		}

		$query = "select `surveyId` from `tiki_surveys` $mid";
		$result = $this->fetchAll($query, $bindvars);
		$res = $ret = $retids = array();
		$n = 0;

		//FIXME Perm:filter ?
		foreach ( $result as $res ) {
		  global $user;
		  $objperm = $this->get_perm_object( $res['surveyId'], 'survey', '', false );
		  if ( $objperm['tiki_p_take_survey'] ) {
			if ( ($maxRecords == -1) || (($n >= $offset) && ($n < ($offset + $maxRecords))) ) {
			  $retids[] = $res['surveyId'];
			}
			$n++;
		  }
		}
		if ( $n > 0 ) {
		  $query = 'select * from `tiki_surveys` where `surveyId` in (' . implode(',',$retids) . ') order by ' . $this->convertSortMode($sort_mode);
		  $result = $this->fetchAll($query);
		  foreach ( $result as $res ) {
			$res["questions"] = $this->getOne( 'select count(*) from `tiki_survey_questions` where `surveyId`=?', array( (int) $res['surveyId']) );
			$ret[] = $res;
		  }
		} 
		
		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $n;
		return $retval;
	}

	/* experimental shared */
	function get_item_id($trackerId,$fieldId,$value) {
		$query = "select ttif.`itemId` from `tiki_tracker_items` tti, `tiki_tracker_fields` ttf, `tiki_tracker_item_fields` ttif ";
		$query.= " where tti.`trackerId`=ttf.`trackerId` and ttif.`fieldId`=ttf.`fieldId` and ttf.`trackerId`=? and ttf.`fieldId`=? and ttif.`value`=?";
		$itemId = $this->getOne($query,array((int) $trackerId,(int)$fieldId,$value));
		return $itemId;
	}

	/*shared*/
	function list_tracker_items($trackerId, $offset, $maxRecords, $sort_mode, $fields, $status = '', $initial = '') {

		$filters = array();
		if ($fields) {
			$temp_max = count($fields["data"]);
			for ($i = 0; $i < $temp_max; $i++) {
				$fieldId = $fields["data"][$i]["fieldId"];
				$filters[$fieldId] = $fields["data"][$i];
			}
		}
		$csort_mode = '';
		if (substr($sort_mode,0,2) == "f_") {
			list($a,$csort_mode,$corder) = explode('_',$sort_mode, 3);
		}
		$trackerId = (int) $trackerId;
		if ($trackerId == -1) {
			$mid = " where 1=1 ";
			$bindvars = array();
		} else {
			$mid = " where tti.`trackerId`=? ";
			$bindvars = array($trackerId);
		}
		if ($status) {
			$mid.= " and tti.`status`=? ";
			$bindvars[] = $status;
		}
		if ($initial) {
			$mid.= "and ttif.`value` like ?";
			$bindvars[] = $initial.'%';
		}
		if (!$sort_mode) {
			$temp_max = count($fields["data"]);
			for ($i = 0; $i < $temp_max; $i++) {
				if ($fields['data'][$i]['isMain'] == 'y') {
					$csort_mode = $fields['data'][$i]['name'];
					break;
				}
			}
		}
		if ($csort_mode) {
			$sort_mode = $csort_mode."_desc";
			$bindvars[] = $csort_mode;
			$query = "select tti.*, ttif.`value` from `tiki_tracker_items` tti, `tiki_tracker_item_fields` ttif, `tiki_tracker_fields` ttf  ";
			$query.= " $mid and tti.`itemId`=ttif.`itemId` and ttf.`fieldId`=ttif.`fieldId` and ttf.`name`=? order by ttif.`value`";
			$query_cant = "select count(*) from `tiki_tracker_items` tti, `tiki_tracker_item_fields` ttif, `tiki_tracker_fields` ttf  ";
			$query_cant.= " $mid and tti.`itemId`=ttif.`itemId` and ttf.`fieldId`=ttif.`fieldId` and ttf.`name`=? ";
		} else {
			if (!$sort_mode) {
				$sort_mode = "lastModif_desc";
			}
			$query = "select * from `tiki_tracker_items` tti $mid order by ".$this->convertSortMode($sort_mode);
			$query_cant = "select count(*) from `tiki_tracker_items` tti $mid ";
		}
		$result = $this->fetchAll($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();
		foreach ( $result as $res ) {
			$fields = array();
			$itid = $res["itemId"];
			$query2 = "select ttif.`fieldId`,`name`,`value`,`type`,`isTblVisible`,`isMain`,`position`
				from `tiki_tracker_item_fields` ttif, `tiki_tracker_fields` ttf
				where ttif.`fieldId`=ttf.`fieldId` and `itemId`=? order by `position` asc";
			$result2 = $this->fetchAll($query2,array((int) $res["itemId"]));
			$pass = true;
			$kx = "";
			foreach ( $result2 as $res2 ) {
				// Check if the field is visible!
				$fieldId = $res2["fieldId"];
				if (count($filters) > 0) {
					if (isset($filters["$fieldId"]["value"]) and $filters["$fieldId"]["value"]) {
						if ($filters["$fieldId"]["type"] == 'a' || $filters["$fieldId"]["type"] == 't') {
							if (!stristr($res2["value"], $filters["$fieldId"]["value"]))
								$pass = false;
						} else {
							if (strtolower($res2["value"]) != strtolower($filters["$fieldId"]["value"])) {
								$pass = false;
							}
						}
					}
					if (preg_replace("/[^a-zA-Z0-9]/","",$res2["name"]) == $csort_mode) {
						$kx = $res2["value"].$itid;
					}
				}
				$fields[] = $res2;
			}
			$res["field_values"] = $fields;
			$res["comments"] = $this->getOne("select count(*) from `tiki_tracker_item_comments` where `itemId`=?",array((int) $itid));
			if ($pass) {
				$kl = $kx.$itid;
				$ret["$kl"] = $res;
			}
		}
		ksort($ret);
		//$ret=$this->sort_items_by_condition($ret,$sort_mode);
		$retval = array();
		$retval["data"] = array_values($ret);
		$retval["cant"] = $cant;
		return $retval;
	}

	/*
	 * Score methods begin
	 */
	// All information about an event type
	// shared
	function get_event($event) {
		$query = "select * from `tiki_score` where `event`=?";
		$result = $this->query($query,array($event));
		return $result->fetchRow();
	}

	/*
	 * Checks if an event should be scored and grants points to proper user
	 * $multiplier is for rating events, in which the score will
	 * be multiplied by other user's rating. Not yet used
	 *
	 * shared
	 */
	function score_event($user, $event_type, $id = '', $multiplier=false) {
		global $scorelib,$prefs;
		if (!is_object($scorelib)) {
			include_once("lib/score/scorelib.php");
		}

		if ($user == 'admin' || !$user) { 
			return true;
		 }

		$event = $scorelib->get_event($event_type);
		if (!$event || !$event['score']) {
			return true;
		}
		$score = $event['score'];
		if ($multiplier) {
			$score *= $multiplier;
		}
		if ($id || $event['expiration']) {
			$expire = $event['expiration'];
			$event_id = $event_type . '_' . $id;

			$query = "select count(*) from `tiki_users_score` where `user`=? and `event_id`=?";
			$bindvars = array($user, $event_id);
			if ($expire) {
				$query .= " and `expire` > ?";
				$bindvars[] = time();
			}
			if ($this->getOne($query, $bindvars)) {
				return true;
			}

			$usersScore = $this->table('tiki_users_score');
			$usersScore->delete(array(
				'user' => $user,
				'event_id' => $event_id,
			));
			$usersScore->insert(array(
				'user' => $user,
				'event_id' => $event_id,
				'expire' => time() + ($expire * 60),
			));
		}
		// Perform check to make sure score does not go below 0 with negative scores
		if( $prefs['fgal_prevent_negative_score'] == 'y' && strpos( $event_type, 'fgallery' ) === 0 ) {
			$result = $this->query( "select `userId` from `users_users` where `score` + ? >= 0 and `login` = ?",
					array( $score, $user ) );
			if( ! $row = $result->fetchRow( $result ) )
				return false;
		}

		$event['id'] = $id; // just for debug

		$table = $this->table('users_users');
		$table->update(array(
			'score' => $table->increment($score),
		), array(
			'login' => $user,
		));

		return true;
	}

	// List users by best scoring
	// shared
	function rank_users($limit = 10, $start = 0) {
		if (!$start) {
			$start = "0";
		}
		// admin doesn't go on ranking
		$query = "select `userId`, `login`, `score` from `users_users` where `login` <> 'admin' order by `score` desc";

		$result = $this->fetchAll($query,array(),$limit,$start);
		$ranking = array();
		foreach ( $result as $res ) {
			$res['position'] = ++$start;
			$ranking[] = $res;
		}
		return $ranking;
	}

	// Returns html <img> tag to star corresponding to user's score
	// shared
	function get_star($score) {
		$star = '';
		$star_colors = array(0 => 'grey',
				100 => 'blue',
				500 => 'green',
				1000 => 'yellow',
				2500 => 'orange',
				5000 => 'red',
				10000 => 'purple');
		foreach ($star_colors as $boundary => $color) {
			if ($score >= $boundary) {
				$star = 'star_'.$color.'.gif';
			}
		}
		if (!empty($star)) {
			$alt = sprintf(tra("%d points"), $score);
			$star = "<img src='img/icons/$star' height='11' width='11' alt='$alt' />&nbsp;";
		}
		return $star;
	}

	/*
	 * Score methods end
	 */
	//shared
	// \todo remove all hardcoded html in get_user_avatar()
	function get_user_avatar($user, $float = "") {
		global $userlib, $prefs;

		if (empty($user))
			return '';

		if( is_array( $user ) ) {
			$res = $user;
			$user = $user['login'];
		} else {
			$query = "select `login`,`avatarType`,`avatarLibName` from `users_users` where `login`=?";
			$result = $this->query($query,array($user));
			$res = $result->fetchRow();
		}

		if (!$res) {
			return '';
		}

		$type = $res["avatarType"];
		$libname = $res["avatarLibName"];
		$ret = '';
		$style = '';

		if (strcasecmp($float, "left") == 0) {
			$style = "style='float:left;margin-right:5px;'";
		} else if (strcasecmp($float, "right") == 0) {
			$style = "style='float:right;margin-left:5px;'";
		}
		switch ($type) {
			case 'n':
				$ret = '';
				break;
			case 'l':
				$ret = "<img border='0' width='45' height='45' src='" . $libname . "' " . $style . " alt='$user' />";
				break;
			case 'u':
				$path = "tiki-show_user_avatar.php?user=$user";

				if( $prefs['users_serve_avatar_static'] == 'y' ) {
					global $tikidomain;
					$files = glob( "temp/public/$tikidomain/avatar_$user.*" );

					if( !empty( $files[0] ) ) {
						$path = $files[0];
					}
				}

				$ret = "<img border='0' src='$path' " . $style . " alt='$user' />";
				break;
		}
		return $ret;
	}

	/*shared*/
	function get_forum_sections() {
		$query = "select distinct `section` from `tiki_forums` where `section`<>?";
		$result = $this->fetchAll($query,array(''));
		$ret = array();
		foreach ( $result as $res ) {
			$ret[] = $res["section"];
		}
		return $ret;
	}

	/* Referer stats */
	/*shared*/
	function register_referer($referer) {
		$cant = $this->getOne("select count(*) from `tiki_referer_stats` where `referer`=?",array($referer));

		$refererStats = $this->table('tiki_referer_stats');

		if ($cant) {
			$refererStats->update(array(
				'hits' => $refererStats->increment(1),
				'last' => $this->now,
			), array(
				'referer' => $referer,
			));
		} else {
			$refererStats->insert(array(
				'last' => $this->now,
				'referer' => $referer,
				'hits' => 1,
			));
		}
	}

	// File attachments functions for the wiki ////
	/*shared*/
	function add_wiki_attachment_hit($id) {
		global $prefs, $user;
		if ($prefs['count_admin_pvs'] == 'y' || $user != 'admin') {
			$wikiAttachments = $this->table('tiki_wiki_attachments');
			$wikiAttachments->update(array(
				'hits' => $wikiAttachments->increment(1),
			), array(
				'attId' => (int) $id,
			));
		}
		return true;
	}

	/*shared*/
	function get_wiki_attachment($attId) {
		$query = "select * from `tiki_wiki_attachments` where `attId`=?";
		$result = $this->query($query,array((int)$attId));
		if (!$result->numRows()) return false;
		$res = $result->fetchRow();
		return $res;
	}

	/*shared*/
	/*
		 function get_random_image($galleryId = -1) {
		 $whgal = "";
		 $bindvars = array();
		 if (((int)$galleryId) != -1) {
		 $whgal = " where `galleryId`=? ";
		 $bindvars[] = (int) $galleryId;
		 }

		 $query = "select count(*) from `tiki_images` $whgal";
		 $cant = $this->getOne($query,$bindvars);
		 $ret = array();

		 if ($cant) {
		 $pick = rand(0, $cant - 1);

		 $query = "select `imageId` ,`galleryId`,`name` from `tiki_images` $whgal";
		 $result = $this->query($query,$bindvars,1,$pick);
		 $res = $result->fetchRow();
		 $ret["galleryId"] = $res["galleryId"];
		 $ret["imageId"] = $res["imageId"];
		 $ret["name"] = $res["name"];
		 $query = "select `name`  from `tiki_galleries` where `galleryId` = ?";
		 $ret["gallery"] = $this->getOne($query,array((int)$res["galleryId"]));
		 } else {
		 $ret["galleryId"] = 0;

		 $ret["imageId"] = 0;
		 $ret["name"] = tra("No image yet, sorry.");
		 }

		 return ($ret);
		 }
	 */ /* Sept */

	/*shared*/
	function get_gallery($id) {
		$query = "select * from `tiki_galleries` where `galleryId`=?";
		$result = $this->query($query,array((int) $id));
		$res = $result->fetchRow();
		return $res;
	}

	// Last visit module ////
	/*shared*/
	function get_news_from_last_visit($user) {
		if (!$user) return false;

		$last = $this->getOne("select `lastLogin`  from `users_users` where `login`=?",array($user));
		$ret = array();
		if (!$last) {
			$last = time();
		}
		$ret["lastVisit"] = $last;
		$ret["images"] = $this->getOne("select count(*) from `tiki_images` where `created`>?",array((int)$last));
		$ret["pages"] = $this->getOne("select count(*) from `tiki_pages` where `lastModif`>?",array((int)$last));
		$ret["files"] = $this->getOne("select count(*) from `tiki_files` where `created`>?",array((int)$last));
		$ret["comments"] = $this->getOne("select count(*) from `tiki_comments` where `commentDate`>?",array((int)$last));
		$ret["users"] = $this->getOne("select count(*) from `users_users` where `registrationDate`>? and `provpass`=?",array((int)$last, ''));
		$ret["trackers"] = $this->getOne("select count(*) from `tiki_tracker_items` where `lastModif`>?",array((int)$last));
		$ret["calendar"] = $this->getOne("select count(*) from `tiki_calendar_items` where `lastmodif`>?",array((int)$last));
		return $ret;
	}

	function pick_cookie() {
		$cant = $this->getOne("select count(*) from `tiki_cookies`",array());
		if (!$cant) return '';

		$bid = rand(0, $cant - 1);
		//$cookie = $this->getOne("select `cookie`  from `tiki_cookies` limit $bid,1"); getOne seems not to work with limit
		$result = $this->query("select `cookie`  from `tiki_cookies`",array(),1,$bid);
		if ($res = $result->fetchRow()) {
			$cookie = str_replace("\n", "", $res['cookie']);
			return preg_replace('/^(.+?)(\s*--.+)?$/','<em>"$1"</em>$2',$cookie);
		} else {
			return "";
		}
	}

	function get_pv_chart_data($days) {
		$now = $this->make_time(0, 0, 0, $this->date_format("%m"), $this->date_format("%d"), $this->date_format("%Y"));
		$dfrom = 0;
		if ($days != 0) $dfrom = $now - ($days * 24 * 60 * 60);

		$query = "select `day`, `pageviews` from `tiki_pageviews` where `day`<=? and `day`>=?";
		$result = $this->fetchAll($query,array((int)$now,(int)$dfrom));
		$ret = array();
		$n = ceil(count($result) / 10);
		$i = 0;
		$xdata=array();
		$ydata=array();
		foreach ( $result as $res ) {
			if ($i % $n == 0) {
				$xdata[] = $this->date_format("%e %b", $res["day"]);
			} else {
				$xdata = '';
			}
			$ydata[] = $res["pageviews"];
		}
		$ret['xdata']=$xdata;
		$ret['ydata']=$ydata;
		return $ret;
	}

	function add_pageview() {
		$dayzero = $this->make_time(0, 0, 0, $this->date_format("%m",$this->now), $this->date_format("%d",$this->now), $this->date_format("%Y",$this->now));
		$cant = $this->getOne("select count(*) from `tiki_pageviews` where `day`=?",array((int)$dayzero));

		$pageviews = $this->table('tiki_pageviews');

		if ($cant) {
			$pageviews->update(array(
				'pageviews' => $pageviews->increment(1),
			), array(
				'day' => (int) $dayzero,
			));
		} else {
			$pageviews->insert(array(
				'day' => (int) $dayzero,
				'pageviews' => 1,
			));
		}
	}

	function get_usage_chart_data() {
		$this->compute_quiz_stats();
		$data['xdata'][] = tra('wiki');
		$data['ydata'][] = $this->getOne('select sum(`hits`) from `tiki_pages`',array());
		$data['xdata'][] = tra('img-g');
		$data['ydata'][] = $this->getOne('select sum(`hits`) from `tiki_galleries`',array());

		$data['xdata'][] = tra('file-g');
		$data['ydata'][] = $this->getOne('select sum(`hits`) from `tiki_file_galleries`',array());

		$data['xdata'][] = tra('faqs');
		$data['ydata'][] = $this->getOne('select sum(`hits`) from `tiki_faqs`',array());

		$data['xdata'][] = tra('quizzes');
		$data['ydata'][] = $this->getOne('select sum(`timesTaken`) from `tiki_quiz_stats_sum`',array());

		$data['xdata'][] = tra('arts');
		$data['ydata'][] = $this->getOne('select sum(`nbreads`) from `tiki_articles`',array());

		$data['xdata'][] = tra('blogs');
		$data['ydata'][] = $this->getOne('select sum(`hits`) from `tiki_blogs`',array());

		$data['xdata'][] = tra('forums');
		$data['ydata'][] = $this->getOne('select sum(`hits`) from `tiki_forums`',array());

		return $data;
	}

	// User assigned modules ////
	/*shared*/
	function get_user_login($id) {
		$login = $this->getOne("select `login` from `users_users` where `userId`=?", array((int)$id));
		return $login;
	}

	function get_user_id($u) {
		// Anonymous is not in db
		if ( $u == '' ) return -1;

		// If we ask for the current user id and if we already know it in session
		$current = ( isset($_SESSION['u_info']) && $u == $_SESSION['u_info']['login'] );
		if ( isset($_SESSION['u_info']['id']) && $current ) return $_SESSION['u_info']['id'];

		// In other cases, we look in db
		$id = $this->getOne("select `userId` from `users_users` where `login`=?", array($u));
		$id = ($id === NULL) ? -1 : $id;
		if ( $current ) $_SESSION['u_info']['id'] = $id;
		return $id;
	}

	/*shared*/
	function get_groups_all($group) {
		$query = "select `groupName`  from `tiki_group_inclusion` where `includeGroup`=?";
		$result = $this->fetchAll($query, array($group));
		$ret = array();
		foreach ( $result as $res ) {
			$ret[] = $res["groupName"];
			$ret2 = $this->get_groups_all($res["groupName"]);
			$ret = array_merge($ret, $ret2);
		}
		return array_unique($ret);
	}

	/*shared*/
	function get_included_groups($group) {
		$query = "select `includeGroup`  from `tiki_group_inclusion` where `groupName`=?";
		$result = $this->fetchAll($query, array($group));
		$ret = array();
		foreach ( $result as $res ) {
			$ret[] = $res["includeGroup"];
			$ret2 = $this->get_included_groups($res["includeGroup"]);
			$ret = array_merge($ret, $ret2);
		}
		return array_unique($ret);
	}

	/*shared*/
	function get_user_groups($user) {
		global $prefs, $userlib;
		if (empty($user) || $user === 'Anonymous') {
			$ret = array();
			$ret[] = "Anonymous";
			return $ret;
		}
		if ($prefs['feature_intertiki'] == 'y' and empty($prefs['feature_intertiki_mymaster']) and strstr($user,'@')) {
			$realm = substr($user,strpos($user,'@')+1);
			$user = substr($user,0,strpos($user,'@'));
			if (isset($prefs['interlist'][$realm])) {
				$groups = $prefs['interlist'][$realm]['groups'].',Anonymous';
				return explode(',',$prefs['interlist'][$realm]['groups']);
			}
		}
		if (!isset($this->usergroups_cache[$user])) {
			$userid = $this->get_user_id($user);
			$query = "select `groupName`  from `users_usergroups` where `userId`=?";
			$result=$this->fetchAll($query,array((int) $userid));
			$ret = array();
			foreach ( $result as $res ) {
				$ret[] = $res["groupName"];
				$included = $userlib->get_included_groups($res["groupName"]);
				$ret = array_merge($ret, $included);
			}
			$ret[] = "Registered";

// The line below seems to govern whether Anonymous is 'included' in the Registered group
// removing this for 4.0 but leaving commented code here for future reference - jonnyb
//			$ret[] = "Anonymous";

			if (isset($_SESSION["groups_are_emulated"]) && $_SESSION["groups_are_emulated"]=="y"){
				$ret = array_intersect($ret,unserialize($_SESSION['groups_emulated']));
			}
			$ret = array_values(array_unique($ret));
			$this->usergroups_cache[$user] = $ret;
			return $ret;
		} else {
			return $this->usergroups_cache[$user];
		}
	}

	function invalidate_usergroups_cache($user) {
		unset($this->usergroups_cache[$user]);
	}

	function get_user_cache_id($user) {
		$groups = $this->get_user_groups($user);
		sort($groups, SORT_STRING);
		$cacheId = implode(":", $groups);
		if ($user == 'admin') {
			// in this case user get permissions from no group
			$cacheId = 'ADMIN:'.$cacheId;
		}
		return $cacheId;
	}

	// Functions for FAQs ////
	function list_faqs($offset, $maxRecords, $sort_mode, $find) {
	  $mid = '';
	  if ( $find ) {
		$findesc = '%' . $find . '%';
		$mid = ' where (`title` like ? or `description` like ?)';
		$bindvars = array($findesc, $findesc);
	  } else $bindvars = array();

	  $query = "select `faqId` from `tiki_faqs` $mid";
	  $result = $this->fetchAll($query, $bindvars);
	  $res = $ret = $retids = array();
	  $n=0;

		//FIXME Perm:filter ?
	  foreach ( $result as $res ) {
		global $user;
		$objperm = $this->get_perm_object($res['faqId'], 'faq', '', false);
		if ($objperm['tiki_p_view_faqs'] == 'y') {
		  if (($maxRecords == -1) || (($n>=$offset) && ($n < ($offset + $maxRecords)))) {
			$retids[] = $res['faqId'];
			$n++;
		  }
		}
	  }

	  if ($n > 0) {
		$query = "select  * from `tiki_faqs` where faqId in (" . implode(',',$retids) . ") order by " . $this->convertSortMode($sort_mode);
		$result = $this->fetchAll($query);
		foreach ( $result as $res ) {
		  $res['suggested'] = $this->getOne('select count(*) from `tiki_suggested_faq_questions` where `faqId`=?', array((int) $res['faqId']));
		  $res['questions'] = $this->getOne('select count(*) from `tiki_faq_questions` where `faqId`=?', array((int) $res['faqId']));
		  $ret[] = $res;
		}
	  }

	  $retval['data'] = $ret;
	  $retval['cant'] = $n;
	  return $retval;
	}

	/*shared */
	function get_faq($faqId) {
		$query = "select * from `tiki_faqs` where `faqId`=?";
		$result = $this->query($query,array((int)$faqId));
		if (!$result->numRows()) return false;
		$res = $result->fetchRow();
		return $res;
	}
	// End Faqs ////

	/*shared*/
	function genPass() {
		global $prefs;
		$length = max($prefs['min_pass_length'], 8);
		$list = array('aeiou', 'AEIOU', 'bcdfghjklmnpqrstvwxyz', 'BCDFGHJKLMNPQRSTVWXYZ', '0123456789');
		$list[] = $prefs['pass_chr_special'] == 'y'? '_*&+!*-=$@':'_';
		shuffle($list);
		$r = '';
		for ($i = 0; $i < $length; $i++) {
			$ch = $list[$i % count($list)];
			$r .= $ch{rand(0, strlen($ch) - 1)};
		}
		return $r;
	}

	// generate a random string (for unsubscription code etc.)
	function genRandomString($base="") {
		if ($base == "") $base = $this->genPass();
		$base .= microtime();
		return md5($base);
	}

	// This function calculates the pageRanks for the tiki_pages
	// it can be used to compute the most relevant pages
	// according to the number of links they have
	// this can be a very interesting ranking for the Wiki
	// More about this on version 1.3 when we add the pageRank
	// column to tiki_pages
	function pageRank($loops = 16) {
		$query = "select `pageName`  from `tiki_pages`";
		//FIXME
		$result = $this->fetchAll($query,array());
		$ret = array();

		foreach ( $result as $res ) {
			$ret[] = $res["pageName"];
		}

		// Now calculate the loop
		$pages = array();

		$pagesTable = $this->table('tiki_pages');

		foreach ($ret as $page) {
			$val = 1 / count($ret);

			$pages[$page] = $val;

			$pagesTable->update(array(
				'pageRank' => (int) $val
			), array(
				'pageName' => $page,
			));
		}

		for ($i = 0; $i < $loops; $i++) {
			foreach ($pages as $pagename => $rank) {
				// Get all the pages linking to this one
				// Fixed query.  -rlpowell
				$query = "select `fromPage`  from `tiki_links` where `toPage` = ? and `fromPage` not like 'objectlink:%'";
				// page rank does not count links from non-page objects TODO: full feature allowing this with options 
				$result = $this->fetchAll($query, array( $pagename ) );
				$sum = 0;

				foreach ( $result as $res ) {
					$linking = $res["fromPage"];

					if (isset($pages[$linking])) {
						// Fixed query.  -rlpowell
						$q2 = "select count(*) from `tiki_links` where `fromPage`= ? and `fromPage` not like 'objectlink:%'";
						// page rank does not count links from non-page objects TODO: full feature allowing this with options
						$cant = $this->getOne($q2, array($linking) );
						if ($cant == 0) $cant = 1;
						$sum += $pages[$linking] / $cant;
					}
				}

				$val = (1 - 0.85) + 0.85 * $sum;
				$pages[$pagename] = $val;

				$pagesTable->update(array(
					'pageRank' => (int) $val
				), array(
					'pageName' => $pagename,
				));
			}
		}
		arsort ($pages);
		return $pages;
	}

	function list_all_forum_topics($offset, $maxRecords, $sort_mode, $find) {
		$bindvars = array('forum', 0);
		if ($find) {
		  $findesc = '%' . $find . '%';
		  $mid = " and (`title` like ? or `data` like ?)";
		  $bindvars[] = $findesc;
		  $bindvars[] = $findesc;
		} else {
		  $mid = '';
		}

		$query = 'select `threadId`, `forumId` from `tiki_comments`,`tiki_forums`'
			  . " where `object`=`forumId` and `objectType`=? and `parentId`=? $mid order by " . $this->convertSortMode($sort_mode);
		$result = $this->fetchAll($query, $bindvars);
		$res = $ret = $retids = array();
		$n = 0;

		//FIXME Perm:filter ?
		foreach ( $result as $res ) {
			global $user;
			$objperm = $this->get_perm_object($res['forumId'], 'forums', '', false);
			if ($objperm['tiki_p_forum_read'] == 'y') {
				if (($maxRecords == -1) || (($n >= $offset) && ($n < ($offset + $maxRecords)))) {
					$retids[] = $res['threadId'];
				}
				$n++;
			}
		}
		
		if ( $n > 0 ) {
		  $query = 'select * from `tiki_comments`'
			  . ' where `threadId` in (' . implode(',', $retids) . ') order by ' . $this->convertSortMode($sort_mode);
		  $ret = $this->fetchAll($query);
		}

		$retval = array();
		$retval['data'] = $ret;
		$retval['cant'] = $n;
		return $retval;
	}

	/*shared*/
	function list_forum_topics($forumId, $offset, $maxRecords, $sort_mode, $find) {
		$bindvars = array($forumId,$forumId,'forum',0);
		if ($find) {
			$findesc = '%'.$find.'%';
			$mid = " and (`title` like ? or `data` like ?)";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		} else {
			$mid = "";
		}

		$query = "select * from `tiki_comments`,`tiki_forums` where ";
		$query.= " `forumId`=? and `object`=? and `objectType`=? and `parentId`=? $mid order by ".$this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_comments`,`tiki_forums` where ";
		$query_cant.= " `forumId`=? and `object`=? and `objectType`=? and `parentId`=? $mid";
		$ret = $this->fetchAll($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	/*shared*/
	function remove_object($type, $id) {
		global $categlib, $dbTiki, $prefs;

		if (!is_object($categlib)) {
			require_once ("lib/categories/categlib.php");
		}
		global $objectlib;require_once('lib/objectlib.php');
		$categlib->uncategorize_object($type, $id);
		// Now remove comments
		$query = "select * from `tiki_comments` where `object`=?  and `objectType`=?";
		$result = $this->fetchAll($query, array( $id, $type ));
		if ( !empty($result) ) {		
			include_once ("lib/comments/commentslib.php");
			$commentslib = new Comments($dbTiki);
		}
		foreach ( $result as $res ) {
			$commentslib->remove_comment($res['threadId']);
		}
		// Remove individual permissions for this object if they exist
		$object = $type . $id;
		$this->table('users_objectpermissions')->deleteMultiple(array(
			'objectId' => md5($object),
			'objectType' => $type,
		));
		// remove links from this object to pages
		$linkhandle = "objectlink:$type:$id";
		$this->table('tiki_links')->deleteMultiple(array(
			'fromPage' => $linkhandle,
		));
		// remove fgal backlinks
		if ( $prefs['feature_file_galleries'] == 'y') {
			global $filegallib; require_once 'lib/filegals/filegallib.php';
			$filegallib->deleteBacklinks(array('type'=>$type, 'object'=>$id));
		}
		// remove object
		$objectlib->delete_object($type, $id);

		$objectAttributes = $this->table('tiki_object_attributes');
		$objectAttributes->deleteMultiple(array(
			'type' => $type,
			'itemId' => $id,
		));

		$objectRelations = $this->table('tiki_object_relations');
		$objectRelations->deleteMultiple(array(
			'source_type' => $type,
			'source_itemId' => $id,
		));
		$objectRelations->deleteMultiple(array(
			'target_type' => $type,
			'target_itemId' => $id,
		));

		return true;
	}

	/*shared*/
	// function enhancing php in_array() function
	function in_multi_array($needle, $haystack) {
		$in_multi_array = false;

		if (in_array($needle, $haystack)) {
			$in_multi_array = true;
		} else {
			while (list($tmpkey, $tmpval) = each($haystack)) {
				if (is_array($haystack[$tmpkey])) {
					if ($this->in_multi_array($needle, $haystack[$tmpkey])) {
						$in_multi_array = true;
						break;
					}
				}
			}
		}
		return $in_multi_array;
	}

	/*shared*/
	function list_received_pages($offset, $maxRecords, $sort_mode, $find='', $type='', $structureName='') {
		$bindvars = array();
		if ($type == 's')
			$mid = ' `trp`.`structureName` is not null ';
			if (!$sort_mode) 
				$sort_mode = '`structureName_asc';
		elseif ($type == 'p')
			$mid = ' `trp`.`structureName` is null ';
			if (!$sort_mode) 
				$sort_mode = '`pageName_asc';
		else
			$mid = '';

		if ($find) {
			$findesc = '%'.$find.'%';
			if ($mid)
				$mid .= ' and ';
			$mid .= '(`trp`.`pageName` like ? or `trp`.`structureName` like ? or `trp`.`data` like ?)';
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		}
		if ($structureName) {
			if ($mid)
				$mid .= ' and ';
			$mid .= ' `trp`.`structureName`=? ';
			$bindvars[] = $structureName;
		}
		if ($mid)
			$mid = "where $mid";

		$query = "select trp.*, tp.`pageName` as pageExists from `tiki_received_pages` trp left join `tiki_pages` tp on (tp.`pageName`=trp.`pageName`) $mid order by `structureName` asc, `pos` asc," . $this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_received_pages` trp $mid";
		$ret = $this->fetchAll($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	// Functions for the menubuilder and polls////
	/*Shared*/
	function get_menu($menuId) {
		$query = "select * from `tiki_menus` where `menuId`=?";
		$result = $this->query($query,array((int)$menuId));
		if ( ! $result->numRows() ) return false;
		$res = $result->fetchRow();
		if ( empty($res['icon']) ) {
			$res['oicon'] = null;
		} else {
			$res['oicon'] = dirname($res['icon']).'/o'.basename($res['icon']);
		}
		return $res;
	}

	/*shared*/
	function list_menu_options($menuId, $offset=0, $maxRecords=-1, $sort_mode='position_asc', $find='', $full=false, $level=0) {
		global $smarty,$user, $tiki_p_admin, $prefs;
		global $wikilib; include_once('lib/wiki/wikilib.php');
		$ret = array();
		$retval = array();
		$bindvars = array((int)$menuId);
		if ($find) {
			$mid = " where `menuId`=? and (`name` like ? or `url` like ?)";
			$bindvars[] = '%'. $find . '%';
			$bindvars[] = '%'. $find . '%';
		} else {
			$mid = " where `menuId`=? ";
		}
		if ($level && $prefs['feature_userlevels'] == 'y') {
			$mid.= " and `userlevel`<=?";
			$bindvars[] = $level;
		}
		$query = "select * from `tiki_menu_options` $mid order by ".$this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_menu_options` $mid";
		$result = $this->fetchAll($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		foreach ( $result as $res ) {
			$res['canonic'] = $res['url'];
			if (preg_match('|^\(\((.+?)\)\)$|', $res['url'], $matches)) {
				$res['url'] = 'tiki-index.php?page=' . rawurlencode($matches[1]);
				$res['sefurl'] = $wikilib->sefurl($matches[1]);
				$perms = Perms::get(array('type'=>'wiki page', 'object'=>$matches[1]));
				if (!$perms->view && !$perms->wiki_view_ref) {
					continue;
				}
			}
			if (!$full) {
				$display = true;
				if (isset($res['section']) and $res['section']) {
					if (strstr($res['section'], '|')) {
						$display = false;
						$sections = preg_split('/\s*\|\s*/',$res['section']);
						foreach ($sections as $sec) {
							if (!isset($prefs[$sec]) or $prefs[$sec] != 'y') {
								$display = true;
								break;
							}
						}
					} else {
						$display = true;
						$sections = preg_split('/\s*,\s*/',$res['section']);
						foreach ($sections as $sec) {
							if (!isset($prefs[$sec]) or $prefs[$sec] != 'y') {
								$display = false;
								break;
							}
						}
					}
				}
				if ($display && $tiki_p_admin != 'y') {
					if (isset($res['perm']) and $res['perm']) {
						if (strstr($res['perm'], '|')) {
							$display = false;
							$sections = preg_split('/\s*\|\s*/',$res['perm']);
							foreach ($sections as $sec) {
								if (isset($GLOBALS[$sec]) && $GLOBALS[$sec] == 'y') {
									$display = true;
									break;
								}
							}
						} else {
							$sections = preg_split('/\s*,\s*/',$res['perm']);
							$display = true;
							foreach ($sections as $sec) {
								if (!isset($GLOBALS[$sec]) or $GLOBALS[$sec] != 'y') {
									$display = false;
									break;
								}
							}
						}
					}
				}
				if ($display && $tiki_p_admin != 'y') {
					$usergroups = $this->get_user_groups($user);
					if (isset($res['groupname']) and $res['groupname']) {
						$sections = preg_split('/\s*,\s*/',$res['groupname']);
						foreach ($sections as $sec) {
							if ($sec and !in_array($sec,$usergroups)) {
								$display = false;
							}
						}
					}
				}
				if ($display) {
					$pos = $res['position'];
					if (empty($ret[$pos]) || empty($ret[$pos]['url']))
						$ret[$pos] = $res;
				}
			} else {
				$ret[] = $res;
			}
		}
		$retval["data"] = array_values($ret);
		$retval["cant"] = $cant;
		return $retval;
	}
	/* shared
	 * gets result from list_menu_options and sorts "sorted section" sections.
	 */
	function sort_menu_options($channels) {

		$sorted_channels = array();

		if (!isset($channels['data']) || $channels['cant'] == 0) {
			return $channels;
		}
		$cant = $channels['cant'];
		$channels = $channels['data'];

		$temp_max = count($channels);
		for ($i=0; $i < $temp_max; $i++) {
			$sorted_channels[$i] = $channels[$i];
			if ($sorted_channels[$i]['type'] == 'r') { // sorted section
				$sorted_channels[$i]['type'] = 's'; // common section, let's make it transparent
				$i++;
				$section = array();
				while ($i < count($channels) && $channels[$i]['type'] == 'o') {
					$section[] = $channels[$i];
					$i++;
				}
				$i--;
				include_once('lib/smarty_tiki/function.menu.php');
				usort($section, "compare_menu_options");
				$sorted_channels = array_merge($sorted_channels, $section);
			}
		}

		if (isset($cant)) {
			$sorted_channels = array ('data' => $sorted_channels,
					'cant' => $cant);
		}

		return $sorted_channels;
	}

	// Menubuilder ends ////

	// User voting system ////
	// Used to vote everything (polls,comments,files,submissions,etc) ////
	// Checks if a user has voted
	/*shared*/
	function user_has_voted($user, $id) {
		global $prefs;
		if (!isset($_SESSION['votes'])) {
			return false;
		}

		$ret = false;
		$votes = $_SESSION['votes'];
		if (is_array($votes) && in_array($id, $votes)) { // has already voted in the session (logged or not)
			$ret = true;
		}
		if (!$user) {
			if ($prefs['ip_can_be_checked'] != 'y' && !isset($_COOKIE[ session_name() ])) {// cookie has not been activated too bad for him
				$ret = true;
			} elseif (isset($_COOKIE[md5("tiki_wiki_poll_$id")])) {
				$ret = true;
			}
			// we have no idea if cookie was deleted  or if really he has not voted
		} else {
			$query = "select count(*) from `tiki_user_votings` where `user`=? and `id`=?";
			if ($this->getOne($query,array($user,(string) $id)) > 0) {
				$ret = true;
			}
		}
		if ($prefs['ip_can_be_checked'] == 'y') {
			$query = 'select count(*) from `tiki_user_votings` where `ip`=? and `id`=?';
			if ($this->getOne($query, array($this->get_ip_address(), $id)) > 0) {
				return true; // IP has already voted logged or not
			}
		}
		return $ret;
	}

	// Registers a user vote
	/*shared*/
	function register_user_vote($user, $id, $optionId=false, array $valid_options = array(), $allow_revote = false ) {
		global $prefs;

		// If an option is specified and the valid options are specified, skip the vote entirely if not valid
		if( false !== $optionId && count( $valid_options ) > 0 && ! in_array( $optionId, $valid_options ) ) {
			return false;
		}

		if( $user && ! $allow_revote && $this->user_has_voted( $user, $id ) ) {
			return false;
		}

		$userVotings = $this->table('tiki_user_votings');

		$ip = $this->get_ip_address();
		$_SESSION['votes'][] = $id;
		setcookie(md5("tiki_wiki_poll_$id"), $ip, time()+60*60*24*300);
		if (!$user) {
			if ($prefs['ip_can_be_checked'] == 'y') {
				$userVotings->delete(array(
					'ip' => $ip,
					'id' => $id,
				));
				if ( $optionId !== false && $optionId != 'NULL' ) {
					$userVotings->insert(array(
						'user' => '',
						'ip' => $ip,
						'id' => (string) $id,
						'optionId' => (int) $optionId,
						'time' => $this->now,
					));
				}
			} elseif (isset($_COOKIE[md5("tiki_wiki_poll_$id")])) {
				return false;
			} elseif ($optionId !== false && $optionId != 'NULL' ) {
				$userVotings->insert(array(
					'user' => '',
					'ip' => $ip,
					'id' => (string) $id,
					'optionId' => (int) $optionId,
					'time' => $this->now,
				));
			}
		} else {
			if ($prefs['ip_can_be_checked'] == 'y') {
				$userVotings->delete(array(
					'user' => $ip,
					'id' => $id,
				));
				$userVotings->delete(array(
					'ip' => $ip,
					'id' => $id,
				));
			} else {
				$userVotings->delete(array(
					'user' => $ip,
					'id' => $id,
				));
			}
			if ( $optionId !== false  && $optionId != 'NULL' ) {
				$userVotings->insert(array(
					'user' => $user,
					'ip' => $ip,
					'id' => (string) $id,
					'optionId' => (int) $optionId,
					'time' => $this->now,
				));
			}
		}

		return true;
	}

	function get_user_vote($id,$user) {
		global $prefs;
		$vote = null;
		if ($user) {
			$vote = $this->getOne("select `optionId` from `tiki_user_votings` where `user` = ? and `id` = ?",array( $user, $id));
		}
		if ($vote == null && $prefs['ip_can_be_checked'] == 'y') {
			$vote = $this->getOne("select `optionId` from `tiki_user_votings` where `ip` = ? and `id` = ?",array( $user, $id));
		}
		return $vote;
	}
	// end of user voting methods

	// FILE GALLERIES ////
	/*shared*/
	function list_files($offset=0, $maxRecords=-1, $sort_mode='created_desc', $find='') {
		global $prefs;
		return $this->get_files($offset, $maxRecords, $sort_mode, $find, $prefs['fgal_root_id'], false, false, true, true, false, false, true, true);
	}

	/*shared*/
	function get_file($id, $randomGalleryId='') {
		if (empty($randomGalleryId)) {
			$where = '`fileId`=?';
			$bindvars[] = (int)$id;
		} else {
			$where = 'tf.`galleryId`=? order by '.$this->convertSortMode('random'). ' limit 1 ';
			$bindvars[] = (int)$randomGalleryId;
		}
		$query = "select tf.*, tfg.`backlinkPerms` from `tiki_files` tf left join `tiki_file_galleries` tfg on (tfg.`galleryId`=tf.`galleryId`) where $where";
		$result = $this->query($query, $bindvars);
		return $result ? $result->fetchRow() : array();
	}

	/**
	 * Retrieve file draft
	 *
	 * @param int $id
	 */
	function get_file_draft($id) {
		global $user;

		$file = $this->get_file($id);

		if (!$file || empty($file)) {
			return array();
		}

		$query = "select tfd.* from `tiki_file_drafts` tfd where `fileId`=? and `user`=?";
		$result = $this->query($query, array((int)$id, $user));

		if (!($draft = $result->fetchRow())) {
			return $file;
		}

		$file['filename'] = $draft['filename'];
		$file['filesize'] = $draft['filesize'];
		$file['filetype'] = $draft['filetype'];
		$file['data'] = $draft['data'];
		$file['user'] = $draft['user'];
		$file['path'] = $draft['path'];
		$file['hash'] = $draft['hash'];
		$file['lastModif'] = $draft['lastModif'];
		$file['lockedby'] = $draft['lockedby'];

		return $file;
	}

	/*shared: added by AW*/
	function get_file_by_name($galleryId, $name, $column='name') {
		$query = "select `path`,`galleryId`,`filename`,`filetype`,`data`,`filesize`,`name`,`description`, `created` from `tiki_files` where `galleryId`=? AND `$column`=? ORDER BY created DESC LIMIT 1";
		$result = $this->query($query,array((int) $galleryId, $name));
		$res = $result->fetchRow();
		return $res;
	}

	/**
	 * Get files and/or subgals list with additional data from one or all file galleries
	 *
	 * @param int $offset
	 * @param int $maxRecords
	 * @param string $sort_mode
	 * @param string $find
	 * @param int $galleryId (-1 = all galleries (default))
	 * @param bool $with_archive give back the number of archives
	 * @param bool $with_subgals include subgals in the listing
	 * @param bool $with_subgals_size calculate the size of subgals
	 * @param bool $with_files include files in the listing
	 * @param bool $with_files_data include files data in the listing
	 * @param bool $with_parent_name include parent names in the listing
	 * @param bool $recursive include all subgals recursively (yet only implemented for galleryId == -1)
	 * @param string $my_user use another user than the current one
	 * @param bool $keep_subgals_together do not mix files and subgals when sorting (if true, subgals will always be at the top)
	 * @param bool $parent_is_file use $galleryId param as $fileId (to return only archives of the file)
	 * @param array filter: creator, categId, lastModif, lastDownload, fileId
	 * @param string wiki_syntax: text to be inserted in editor onclick (from fgal manager)
	 * @return array of found files and subgals
	 */
	function get_files($offset, $maxRecords, $sort_mode, $find, $galleryId=-1, $with_archive=false, $with_subgals=false, 
						$with_subgals_size=true, $with_files=true, $with_files_data=false, $with_parent_name=false, $with_files_count=true,
						$recursive=false, $my_user='', $keep_subgals_together=true, $parent_is_file=false, $with_backlink=false, $filter='',
						$wiki_syntax = '') {

		global $user, $tiki_p_admin_file_galleries, $prefs;
		global $filegallib; require_once('lib/filegals/filegallib.php');

		$f_jail_bind = array();
		$g_jail_bind = array();
		$f_where = '';

		if ( ( ! $with_files && ! $with_subgals ) || ( $parent_is_file && $galleryId <= 0 ) ) return array();

		$fileId = -1;
		if ( $parent_is_file ) {
			$fileId = $galleryId;
			$galleryId = -2;
		}

		if ( $recursive && ! is_array($galleryId) ) {
			$idTree = array();
			$filegallib->getGalleryIds( $idTree, $galleryId, 'list' );
			$galleryId =& $idTree;
		} else {
			// recursive mode is only available for one parent gallery (i.e. not implemented when $galleryId is an array of multiple ids)
			$recursive = false;
		}

		$with_subgals_size = ( $with_subgals && $with_subgals_size );
		if ( $my_user == '' ) $my_user = $user;

		$f_table = '`tiki_files` as tf';
		$g_table = '`tiki_file_galleries` as tfg';
		$f_group_by = '';
		$orderby = $this->convertSortMode($sort_mode);

		global $categlib; require_once 'lib/categories/categlib.php';
		$f2g_corresp = array(
				'0 as `isgal`' => '1 as `isgal`',
				'tf.`fileId` as `id`' => 'tfg.`galleryId` as `id`',
				'tf.`galleryId` as `parentId`' => 'tfg.`parentId`',
				'tf.`name`' => 'tfg.`name`',
				'tf.`description`' => 'tfg.`description`',
				'tf.`filesize` as `size`' => "0 as `size`",
				'tf.`created`' => 'tfg.`created`',
				'tf.`filename`' => 'tfg.`name` as `filename`',
				'tf.`filetype` as `type`' => "tfg.`type`",
				'tf.`user` as `creator`' => 'tfg.`user` as `creator`',
				'tf.`author`' => "'' as `author`",
				'tf.`hits`' => "tfg.`hits`",
				'tf.`lastDownload`' => "0 as `lastDownload`",
				'tf.`votes`' => 'tfg.`votes`',
				'tf.`points`' => 'tfg.`points`',
				'tf.`path`' => "'' as `path`",
				'tf.`reference_url`' => "'' as `reference_url`",
				'tf.`is_reference`' => "'' as `is_reference`",
				'tf.`hash`' => "'' as `hash`",
				'tf.`search_data`' => 'tfg.`name` as `search_data`',
				'tf.`lastModif` as `lastModif`' => 'tfg.`lastModif` as `lastModif`',
				'tf.`lastModifUser` as `last_user`' => "'' as `last_user`",
				'tf.`lockedby`' => "'' as `lockedby`",
				'tf.`comment`' => "'' as `comment`",
				'tf.`archiveId`' => '0 as `archiveId`',
				"'' as `visible`" => 'tfg.`visible`',
				"'' as `public`" => 'tfg.`public`',

				/// Below are obsolete fields that will be removed soon (they have their new equivalents above)
				'tf.`fileId`' => 'tfg.`galleryId` as `fileId`', /// use 'id' instead
				'tf.`galleryId`' => 'tfg.`parentId` as `galleryId`', /// use 'parentId' instead
				'tf.`filesize`' => "0 as `filesize`", /// use 'size' instead
				'tf.`filetype`' => "tfg.`type` as `filetype`", /// use 'type' instead
				'tf.`user`' => 'tfg.`user`', /// use 'creator' instead	
				'tf.`lastModifUser`' => "'' as `lastModifUser`" /// use 'last_user' instead
		);
		if ( $with_files_data ) {
			$f2g_corresp['tf.`data`'] = "'' as `data`";
		}
		if ( $with_files_count ) {
			$f2g_corresp["'' as `files`"] = 'count(distinct tfc.`fileId`) as `files`';
		}
		if ( $with_archive ) {
			$f2g_corresp['count(tfh.`fileId`) as `nbArchives`'] = '0 as `nbArchives`';
			$f_table .= ' LEFT JOIN `tiki_files` tfh ON (tf.`fileId` = tfh.`archiveId`)';
			$f_group_by = ' GROUP BY tf.`fileId`';
		}
		if ( $with_files && $prefs['feature_file_galleries_save_draft'] == 'y' ) {
			$f2g_corresp['count(tfd.`fileId`) as `nbDraft`'] = '0 as `nbDraft`';
			$f_table .= ' LEFT JOIN `tiki_file_drafts` tfd ON (tf.`fileId` = tfd.`fileId` and tfd.`user`=?)';
			$f_group_by = ' GROUP BY tf.`fileId`';
		}
		if ( $with_backlink ) {
			$f2g_corresp['count(tfb.`fileId`) as `nbBacklinks`'] = '0 as `nbBacklinks`';
			$f_table .= ' LEFT JOIN `tiki_file_backlinks` tfb ON (tf.`fileId` = tfb.`fileId`)';
			$f_group_by = ' GROUP BY tf.`fileId`';
		}
		if ( !empty($filter['orphan']) && $filter['orphan'] == 'y' ) {
			$f_where .= ' AND tfb.`objectId` IS NULL';
			if (!$with_backlink) {
				$f_table .= 'LEFT JOIN `tiki_file_backlinks` tfb ON (tf.`fileId`=tfb.`fileId`)';
			}
		}

		if( !empty($filter['categId']) ) {
			$jail = $filter['categId'];
		} else {
			$jail = $categlib->get_jail();
		}
			
		if( $jail ) {
			$categlib->getSqlJoin( $jail, 'file', 'tf.`fileId`', $f_jail_join, $f_jail_where, $f_jail_bind );
		} else {
			$f_jail_join = '';
			$f_jail_where = '';
			$f_jail_bind = array();
		}

		$f_query = 'SELECT '.implode(', ', array_keys($f2g_corresp)).' FROM '.$f_table.$f_jail_join.' WHERE tf.`archiveId`='.( $parent_is_file ? $fileId : '0' ) . $f_jail_where . $f_where;
		$bindvars = array();

		$mid = '';
		$midvars = array();
		if ( $find ) {
			$findesc = '%'.$find.'%';
			$tab = $with_subgals?'tab':'tf';
			$mid = " (upper($tab.`name`) LIKE upper(?) OR upper($tab.`description`) LIKE upper(?) OR upper($tab.`filename`) LIKE upper(?))";
			$midvars = array($findesc, $findesc, $findesc);
		}
		if ( !empty($filter['creator']) ) {
			$f_query .= ' AND tf.`user` = ? ';
			$bindvars[] = $filter['creator'];
		}
		if ( !empty($filter['lastModif']) ) {
			$f_query .= ' AND tf.`lastModif` < ? ';
			$bindvars[] = $filter['lastModif'];
		}
		if ( !empty($filter['lastDownload']) ) {
			$f_query .= ' AND (tf.`lastDownload` < ? or tf.`lastDownload` is NULL)';
			$bindvars[] = $filter['lastDownload'];
		}
		if ( $with_files && $prefs['feature_file_galleries_save_draft'] == 'y' ) {
			$bindvars[] = $user;
		}
		if (!empty($filter['fileId'])) {
			$f_query .= ' AND tf.`fileId` in ('.implode(',',array_fill(0, count($filter['fileId']),'?')).')';
			$bindvars = array_merge($bindvars, $filter['fileId']);
		}
		$galleryId_str = '';
		if ( is_array($galleryId) ) {
			$galleryId_str = ' in ('.implode(',', array_fill(0, count($galleryId),'?')).')';
			$bindvars = array_merge($bindvars, $galleryId);
		} elseif ( $galleryId >= -1 ) {
			$galleryId_str = '=?';
			if ( $with_files ) $bindvars[] = $galleryId;
			if ( $with_subgals ) $bindvars[] = $galleryId;
		}
		if ( $galleryId_str != '' ) {
			$f_query .= ' AND tf.`galleryId`'.$galleryId_str;
		}
		
		if ( $with_subgals ) {

			$g_mid = '';
			$g_join = '';
			$g_group_by = '';

			$join = '';
			$select = 'tab.*';

			if ( $with_files_count ) {
				$g_join = ' LEFT JOIN `tiki_files` tfc ON (tfg.`galleryId` = tfc.`galleryId`)';
				$g_group_by = ' GROUP BY tfg.`galleryId`'; 
			}

			// If $user is admin then get ALL galleries, if not only user galleries are shown
			// If the user is not admin then select it's own galleries or public galleries
			if ( $tiki_p_admin_file_galleries != 'y' && $my_user != 'admin' && empty($parentId) ) {
				$g_mid = " AND (tfg.`user`=? OR tfg.`visible`='y' OR tfg.`public`='y')";
				$bindvars[] = $my_user;
			}

			if( $jail ) {
				$categlib->getSqlJoin( $jail, 'file gallery', '`tfg`.`galleryId`', $g_jail_join, $g_jail_where, $g_jail_bind );
			} else {
				$g_jail_join = '';
				$g_jail_where = '';
				$g_jail_bind = array();
			}
			
			$g_query = 'SELECT '.implode(', ', array_values($f2g_corresp)).' FROM '.$g_table.$g_join.$g_jail_join;
			$g_query .= " WHERE 1=1 ";

			if ( $galleryId_str != '' ) {
				$g_query .= ' AND tfg.`parentId`'.$galleryId_str;
			}
			$g_query .= $g_mid;

			$g_query .= $g_jail_where;
			$bindvars = array_merge( $bindvars, $g_jail_bind );

			if ( $with_parent_name ) {
				$select .= ', tfgp.`name` as `parentName`';
				$join .= ' LEFT OUTER JOIN `tiki_file_galleries` tfgp ON (tab.`parentId` = tfgp.`galleryId`)';
			}

			if ( $with_files ) {
				$query = "SELECT $select FROM (($f_query $f_group_by) UNION ($g_query $g_group_by)) as tab".$join;
				$bindvars = array_merge( $f_jail_bind, $bindvars );
			} else {
				$query = "SELECT $select FROM ($g_query $g_group_by) as tab".$join;
			}
			if ( $mid != '' ){
				$query .= ' WHERE'.$mid;
				$bindvars = array_merge( $bindvars, $midvars );
			}
			if ( $orderby != '' ) $orderby = 'tab.'.$orderby;

		} else {
			$query = $f_query;
			$bindvars = array_merge( $f_jail_bind, $bindvars );
			if ( $mid != '' ) {
				$query .= ' AND'.$mid;
				$bindvars = array_merge( $bindvars, $midvars );
			}
			$query .= $f_group_by;
		}

		if ( $keep_subgals_together ) {
			$query .= ' ORDER BY `isgal` desc'.($orderby == '' ? '' : ', '.$orderby);
		} elseif ( $orderby != '' ) {
			$query .= ' ORDER BY '.$orderby;
		}
		$result = $this->fetchAll($query, $bindvars);
		$ret = array();
		$gal_size_order = array();
		$cant = 0;
		$n = -1;
		$need_everything = ( $with_subgals_size && ( $sort_mode == 'size_asc' || $sort_mode == 'filesize_asc' ) );
		global $cachelib; include_once('lib/cache/cachelib.php');
		//TODO: perms cache for file perms (now we are using cache only for file gallery perms)
		$cacheName = md5("group:".implode("\n", $this->get_user_groups($user)));
		$cacheType = 'fgals_perms_'.$galleryId."_";
		if ($galleryId > 0 && $cachelib->isCached($cacheName, $cacheType)) {
			$fgal_perms = unserialize($cachelib->getCached($cacheName, $cacheType));
		} else {
			$fgal_perms = array();
		}
		foreach( $result as $res ) {
			$object_type = ( $res['isgal'] == 1 ? 'file gallery' : 'file');
			$galleryId = $res['isgal'] == 1 ? $res['id'] : $res['galleryId'];

			// if file is categorized uses category permisions, otherwise uses parent file gallery permissions
			// note that the file will not be displayed if categorized but its categories has no file gallery related permissions
			if ($object_type == 'file' && $categlib->is_categorized($object_type, $res['id'])) {
				$res['perms'] = $this->get_perm_object($res['id'], 'file', array(), false);
			} else if (isset($fgal_perms[$galleryId])) {
				$res['perms'] = $fgal_perms[$galleryId];
			} else {
				$fgal_perms[$galleryId] = $res['perms'] = $this->get_perm_object($galleryId, 'file gallery', array(), false);
			}
			
			if ($galleryId <=0) {
				$cachelib->cacheItem($cacheName, serialize($fgal_perms), 'fgals_perms_'.$galleryId.'_');
			}

			// Don't return the current item, if :
			//  the user has no rights to view the file gallery AND no rights to list all galleries (in case it's a gallery)
			if ( ( $res['perms']['tiki_p_view_file_gallery'] != 'y' && ! $this->user_has_perm_on_object($user,$res['id'], $object_type, 'tiki_p_view_file_gallery') )
					&& ( $res['isgal'] == 0 || ( $res['perms']['tiki_p_list_file_gallery'] != 'y' && ! $this->user_has_perm_on_object($user,$res['id'], $object_type, 'tiki_p_list_file_gallery') ) ) 
				 ) {
				continue;
			}
			if (empty($backlinkPerms[$res['galleryId']])) {
				$info = $filegallib->get_file_gallery_info($res['galleryId']);
				$backlinkPerms[$res['galleryId']] = $info['backlinkPerms'];
			}
			if ($backlinkPerms[$res['galleryId']] == 'y' && $filegallib->hasOnlyPrivateBacklinks($res['id'])) {
				continue;
			}
			// add markup to be inserted onclick
			if ($object_type === 'file') {
				$res['wiki_syntax'] = $this->process_fgal_syntax($wiki_syntax, $res);
			}
				
			$n++;
			if ( ! $need_everything && $offset != -1 && $n < $offset ) continue;

			if ( $need_everything || $maxRecords == -1 || $cant < $maxRecords ) {
				$ret[$cant] = $res;
				if ( $with_subgals_size && $res['isgal'] == 1 ) {
					$ret[$cant]['size'] = (string)$filegallib->getUsedSize($res['id']);
					$ret[$cant]['filesize'] = $ret[$cant]['size']; /// Obsolete
					if ( $keep_subgals_together ) {
						$gal_size_order[$cant] = $ret[$cant]['size'];
					}
				}
				if ( $with_subgals_size && ! $keep_subgals_together ) {
					$gal_size_order[$cant] = $ret[$cant]['size'];
				}
				// generate link for podcasts
				$ret[$cant]['podcast_filename'] = $res['path'];
			}

			$cant++;
		}
		if ($galleryId > 0)
			$cachelib->cacheItem($cacheName, serialize($fgal_perms), $cacheType);
		if ( ! $need_everything ) $cant += $offset;

		if ( count($gal_size_order) > 0 ) {
			if ( $sort_mode == 'size_asc' || $sort_mode == 'filesize_asc' ) {
				asort($gal_size_order, SORT_NUMERIC);
			} elseif ( $sort_mode == 'size_desc' || $sort_mode == 'filesize_desc' ) {
				arsort($gal_size_order, SORT_NUMERIC);
			}
			$ret2 = array();
			foreach ( $gal_size_order as $k => $v ) {
				$ret2[] = $ret[$k];
				unset($ret[$k]);
			}
			if ( count($ret) > 0 ) {
				foreach ( $ret as $k => $v ) {
					$ret2[] = $v;
				}
			}
			unset($ret);
			$ret =& $ret2;
		}

		if ( $need_everything && ( $offset > 0 || $maxRecords != -1 ) ) {
			if ( $maxRecords == -1 ) {
				$ret = array_slice($ret, $offset);
			} else {
				$ret = array_slice($ret, $offset, $maxRecords);
			}
		}

		return array('data' => $ret, 'cant' => $cant);
	}
	
	// convert markup to be inserted onclick - replace: %fileId%, %name%, %description% etc
	function process_fgal_syntax($syntax, $file) {
		$replace_keys = array('fileId', 'name', 'filename', 'description', 'hits', 'author', 'filesize', 'filetype');
		foreach($replace_keys as $k) {
			if (isset($file[$k])) {
				$syntax = preg_replace("/%$k%/", $file[$k], $syntax);
			}
		}
		return $syntax;
	}

	function list_file_galleries($offset = 0, $maxRecords = -1, $sort_mode = 'name_desc', $user='', $find='', $parentId=-1, $with_archive=false, $with_subgals=true, $with_subgals_size=false, $with_files=false, $with_files_data=false, $with_parent_name=true, $with_files_count=true,$recursive=true) {
		return $this->get_files($offset, $maxRecords, $sort_mode, $find, $parentId, $with_archive, $with_subgals, $with_subgals_size, $with_files, $with_files_data, $with_parent_name, $with_files_count, $recursive, $user);
	}

	/*shared*/
	function add_file_hit($id) {
		global $prefs, $user, $filegallib;

		$files = $this->table('tiki_files');

		if ($prefs['count_admin_pvs'] == 'y' || $user != 'admin') {
			// Enforce max download per file
			if( $prefs['fgal_limit_hits_per_file'] == 'y' ) {
				$limit = $filegallib->get_download_limit( $id );
				if( $limit > 0 ) {
					$result = $this->query( "select `fileId` from `tiki_files` where `fileId` = ? and `hits` < ?",
							array( $id, $limit ) );

					if( ! $result->fetchRow() )
						return false;
				}
			}

			$files->update(array(
				'hits' => $files->increment(1),
				'lastDownload' => $this->now,
			), array(
				'fileId' => (int) $id,
			));
		} else {
			$files->update(array(
				'lastDownload' => $this->now,
			), array(
				'fileId' => (int) $id,
			));
		}			

		if ($prefs['feature_score'] == 'y') {
			if( ! $this->score_event($user, 'fgallery_download', $id) )
				return false;

			$query = "select `user` from `tiki_files` where `fileId`=?";
			$owner = $this->getOne($query, array((int)$id));
			if( ! $this->score_event($owner, 'fgallery_is_downloaded', "$user:$id") )
				return false;
		}

		return true;
	}

	/*shared*/
	function add_file_gallery_hit($id) {
		global $prefs, $user;
		if ($prefs['count_admin_pvs'] == 'y' || $user != 'admin') {
			$fileGalleries = $this->table('tiki_file_galleries');
			$fileGalleries->update(array(
				'hits' => $fileGalleries->increment(1),
			), array(
				'galleryId' => (int) $id,
			));
		}
		return true;
	}

	/*shared*/
	function get_file_gallery($id = -1, $defaultsFallback = true) {
		static $defaultValues = null;

		if ( $defaultValues === null && $defaultsFallback ) {
			global $prefs;
			global $filegallib; require_once 'lib/filegals/filegallib.php';
			$defaultValues = $filegallib->default_file_gallery();
		}

		if ( $id > 0 ) {
			$query = "select * from `tiki_file_galleries` where `galleryId`=?";
			$result = $this->query($query,array((int) $id));
			$res = $result->fetchRow();
		} else {
			$res = array();
		}

		// Use default values if some values are not specified
		if ( $res !== false && $defaultsFallback ) {
			foreach ( $defaultValues as $k => $v ) {
				if ( !isset($res[$k]) || $res[$k] === null ) {
					$res[$k] = $v;
				}
			}
		}

		return $res;
	}

	/*shared*/
	function list_visible_file_galleries($offset = 0, $maxRecords = -1, $sort_mode = 'name_desc', $user = '', $find = null) {
		// If $user is admin then get ALL galleries, if not only user galleries are shown

		$old_sort_mode = '';
		$bindvars = array('y');
		$whuser = "";

		if (in_array($sort_mode, array( 'files_desc', 'files_asc'))) {
			$old_offset = $offset;
			$old_maxRecords = $maxRecords;
			$old_sort_mode = $sort_mode;
			$sort_mode = 'user_desc';
			$offset = 0;
			$maxRecords = -1;
		}

		// If the user is not admin then select `it` 's own galleries or public galleries
		global $tiki_p_admin_files_galleries;
		if ($tiki_p_admin_files_galleries != 'y') {
			$whuser.= " and (`user`=? or `public`=?)";
			$bindvars[] = $user;
			$bindvars[] = "y";
		}

		if (! empty($find) ) {
			$findesc = '%' . $find . '%';
			$whuser .= " and (`name` like ? or `description` like ?)";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		}

		$query = "select * from `tiki_file_galleries` where `visible`=? $whuser order by ".$this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_file_galleries` where `visible`=? $whuser";
		$result = $this->fetchAll($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		foreach ( $result as $res ) {
			$aux = array();

			$aux["name"] = $res["name"];
			$gid = $res["galleryId"];
			$aux["id"] = $gid;
			$aux["visible"] = $res["visible"];
			$aux["galleryId"] = $res["galleryId"];
			$aux["description"] = $res["description"];
			$aux["created"] = $res["created"];
			$aux["lastModif"] = $res["lastModif"];
			$aux["user"] = $res["user"];
			$aux["hits"] = $res["hits"];
			$aux["public"] = $res["public"];
			//  The file count is not needed by any caller, so save the query. GG
			//	    $aux["files"] = $this->getOne("select count(*) from `tiki_files` where `galleryId`=?",array((int)$gid));
			$ret[] = $aux;
		}
		if ($old_sort_mode == 'files_asc') {
			usort($ret, 'compare_files');
		}
		if ($old_sort_mode == 'files_desc') {
			usort($ret, 'r_compare_files');
		}

		if (in_array($old_sort_mode, array( 'files_desc', 'files_asc'))) {
			$ret = array_slice($ret, $old_offset, $old_maxRecords);
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	// Semaphore functions ////
	function get_semaphore_user($semName, $objectType='wiki page') {
		global $user;
		// the old semaphores have been deleted by semaphore_is_set - this function must be called before
		$query = "select `user` from `tiki_semaphores` where `semName`=? and `objectType`=?";
		$result = $this->fetchAll($query, array($semName, $objectType));
		$user_is_in = false;
		foreach ( $result as $res ) {
			if ($res['user'] != $user || (!$user && $res['user'] == 'anonymous')) {
				return $res['user']; // return the other users if exist
			} else {
				$user_is_in = true;
			}
		}
		if ($user_is_in)
			return $user;
		else
			return '';
	}

	function semaphore_is_set($semName, $limit, $objectType='wiki page') {
		$lim = $this->now - $limit;
		$query = "delete from `tiki_semaphores` where `timestamp`<?"; // clean all the old semaphores even if it is not on the object
		$result = $this->query($query,array((int)$lim));
		$query = "select `semName`  from `tiki_semaphores` where `semName`=? and `objectType`=?";
		$result = $this->query($query,array($semName, $objectType));
		return $result->numRows();
	}

	function semaphore_set($semName, $objectType='wiki page') {
		global $user;

		if ($user == '') {
			$user = 'anonymous';
		}

		$semaphores = $this->table('tiki_semaphores');
		$semaphores->delete(array(
			'semName' => $semName,
			'objectType' => $objectType,
		));
		$semaphores->insert(array(
			'semName' => $semName,
			'timestamp' => $this->now,
			'user' => $user,
			'objectType' => $objectType,
		));
		return $this->now;
	}

	function semaphore_unset($semName, $lock, $objectType='wiki page') {
		$semaphores = $this->table('tiki_semaphores');
		$semaphores->delete(array(
			'semName' => $semName,
			'timestamp' => (int) $lock,
			'objectType' => $objectType,
		));
	}

	// Hot words methods ////
	/*shared*/
	function get_hotwords() {
		static $cache_hotwords;
		if ( isset($cache_hotwords) ) {
			return $cache_hotwords;
		}
		$query = "select * from `tiki_hotwords`";
		$result = $this->fetchAll($query, array(),-1,-1, false);
		$ret = array();
		foreach ($result as $res ) {
			$ret[$res["word"]] = $res["url"];
		}
		$cache_hotwords = $ret;
		return $ret;
	}

	// FRIENDS METHODS //
	function list_user_friends($user, $offset = 0, $maxRecords = -1, $sort_mode = 'login_asc', $find = '')
	{
		global $userlib;

		$sort_mode = $this->convertSortMode($sort_mode);

		if($find) {
			$findesc = '%'.$find.'%';
			$mid=" and (u.`login` like ? or p.`value` like ?) ";
			$bindvars=array($user,$findesc,$findesc);
		} else {
			$mid='';
			$bindvars=array($user);
		}

		// TODO: same as list_users
		$query = "select u.*, p.`value` as realName from `tiki_friends` as f, `users_users` as u left join `tiki_user_preferences` p on u.`login`=p.`user` and p.`prefName` = 'realName' where u.`login`=f.`friend` and f.`user`=? and f.`user` <> f.`friend` $mid order by $sort_mode";
		$query_cant = "select count(*) from `tiki_friends` as f, `users_users` as u left join `tiki_user_preferences` p on u.`login`=p.`user` and p.`prefName` = 'realName' where u.`login`=f.`friend` and f.`user`=? $mid";
		$result = $this->fetchAll($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = Array();
		foreach ( $result as $res ) {
			$res['realname'] = $this->get_user_preference($res['login'], 'realName');
			$ret[] = $res;
		}
		$retval = Array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;

	}

	function list_online_friends($user)
	{
		global $userlib;
		$this->update_session();

		$bindvars = array($user);

		// TODO: same as list_users
		$query = "select u.*, p.`value` as realName from `tiki_friends` as f, `users_users` as u, `tiki_sessions` s left join `tiki_user_preferences` p on u.`login`=p.`user` and p.`prefName` = 'realName' where u.`login`=f.`friend` and s.`user`=u.`login` and f.`user`=? and f.`user` <> f.`friend`";

		return  $this->fetchAll($query,$bindvars);
	}


	function verify_friendship($user, $friend)
	{
		if ($user == $friend) {
			return 0;
		}

		$query = "select count(*) from `tiki_friends` where `user`=? and `friend`=?";
		return $this->getOne($query, array($user, $friend));
	}

	// Check if there's already a friendship request from userwatched to userwatching
	function verify_friendship_request($userwatched, $userwatching){
		if ($userwatched == $userwatching) {
			return 0;
		}

		$query = "select count(*) from `tiki_friendship_requests` where `userTo`=? and `userFrom`=?";
		return $this->getOne($query, array($userwatching, $userwatched));
	}

	function get_friends_count($user) {
		global $cachelib;
		$cacheKey = 'friends_count_'.$user;

		if ($cachelib->isCached($cacheKey)) {
			return $cachelib->getCached($cacheKey);
		} else {
			$query = "select count(*) from `tiki_friends` where `user`=?";
			$count = $this->getOne($query, array($user));
			$cachelib->cacheItem($cacheKey, $count);
			return $count;
		}
	}

	function list_users($offset = 0, $maxRecords = -1, $sort_mode = 'pref:realName', $find = '', $include_prefs = false) {
		global $user, $prefs, $userprefslib;
		include_once('lib/userprefs/userprefslib.php');

		$bindvars = array();
		if ($prefs['feature_friends'] == 'y' && !$include_prefs) {
			$bindvars[] = $user;
		}
		if ( $find ) {
			$findesc = '%'.$find.'%';
			$mid = 'where (`login` like ? or p1.`value` like ?)';
			$mid_cant = $mid;
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
			$bindvars2 = array($findesc, $findesc);
			$find_join = " left join `tiki_user_preferences` p1 on (u.`login` = p1.`user` and p1.`prefName` = 'realName')";
			$find_join_cant = $find_join;
		} else {
			$mid = '';
			$bindvars2 = array();
			$find_join = '';
			$find_join_cant = '';
			$mid_cant = '';
		}

		// This allows to use a sort_mode by prefs
		// In this case, sort_mode must have this syntax :
		//   pref:PREFERENCE_NAME[_asc|_desc]
		// e.g. to sort on country :
		//   pref:country  OR  pref:country_asc  OR  pref:country_desc

		if ( $ppos = strpos($sort_mode, ':') ) {

			$sort_value = substr($sort_mode, $ppos + 1);
			$sort_by_pref = true;
			$sort_way = 'asc';

			if ( preg_match('/^(.+)_(asc|desc)$/i', $sort_value, $regs) ) {
				$sort_value = $regs[1];
				$sort_way = $regs[2];
				unset($regs);
			}

			if ( $find_join != '' && $sort_value == 'realName' ) {
				// Avoid two joins if we can do only one
				$find_join = '';
				$mid = 'where (`login` like ? or p.`value` like ?)';
			}
			$sort_mode = "p.`value` $sort_way";
			$pref_where = ( ( $mid == '' ) ? 'where' : $mid.' and' )." p.`prefName` = '$sort_value'";
			$pref_join = 'left join `tiki_user_preferences` p on (u.`login` = p.`user`)';
			$pref_field = ', p.`value` as sf';

		} else {

			$sort_mode = $this->convertSortMode($sort_mode);
			$pref_where = $mid;
			$pref_join = '';
			$pref_field = '';
		}

		if ( $sort_mode != '' ) $sort_mode = 'order by '.$sort_mode;

		// Need to use a subquery to avoid bad results when using a limit and an offset, with at least MySQL
		if ($prefs['feature_friends'] == 'y' && !$include_prefs) {
			$query = "select * from (select u.* $pref_field, f.`friend` from `users_users` u $pref_join $find_join left join `tiki_friends` as f on (u.`login` = f.`friend` and f.`user`=?) $pref_where $sort_mode) as tab";
		} else {
			$query = "select u.* $pref_field  from `users_users` u $pref_join $find_join $pref_where $sort_mode";
		}

		$query_cant = "select count(distinct u.`login`) from `users_users` u $find_join_cant $mid_cant";
		$result = $this->fetchAll($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars2);

		$ret = array();
		foreach ( $result as $res ) {
			if ($prefs['feature_friends'] == 'y') {
				$res['friend'] = !empty($res['friend'] );
			}
			if ( $include_prefs ) $res['preferences'] = $userprefslib->get_userprefs($res['login']);
			$ret[] = $res;
		}

		return array('data' => $ret, 'cant' => $cant);
	}

	// CMS functions -ARTICLES- & -SUBMISSIONS- ////
	/*shared*/
	function get_featured_links($max = 10) {
		$query = "select * from `tiki_featured_links` where `position` > ? order by ".$this->convertSortMode("position_asc");
		return  $this->fetchAll($query, array(0), (int)$max, 0 );
	}

	function setSessionId($sessionId) {
		$this->sessionId = $sessionId;
	}

	function getSessionId() {
		return $this->sessionId;
	}

	function update_session() {
		static $uptodate = false;
		if ( $uptodate === true || $this->sessionId === null ) return true;

		global $user, $prefs;
		global $logslib; include_once("lib/logs/logslib.php");

		if ($user === false) $user = '';
		$delay = 5*60; // 5 minutes
		$oldy = $this->now - $delay;
		if ($user != '') { // was the user timeout?
			$query = "select count(*) from `tiki_sessions` where `sessionId`=?";
			$cant = $this->getOne($query, array($this->sessionId));
			if ($cant == 0)
				$logslib->add_log("login", "back", $user, '', '', $this->now);
		}
		$query = "select * from `tiki_sessions` where `timestamp`<?";
		$result = $this->fetchAll($query, array($oldy));
		foreach ( $result as $res ) {
			if ($res['user'] && $res['user'] != $user)
				$logslib->add_log('login', 'timeout', $res['user'], ' ', ' ', $res['timestamp']+ $delay);
		}
		$query = "delete from `tiki_sessions` where `sessionId`=? or `timestamp`<?";
		$bindvars = array($this->sessionId, $oldy);
		if ($user) {
			$query .= " or `user`=?";
			$bindvars[] = $user;
		}
		$this->query($query, $bindvars, -1, -1, false);

		$sessions = $this->table('tiki_sessions');
		$sessions->insert(array(
			'sessionId' => $this->sessionId,
			'timestamp' => $this->now,
			'user' => $user,
			'tikihost' => isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost',
		));
		if ($prefs['session_storage'] == 'db') {
			// clean up adodb sessions as well in case adodb session garbage collection not working
			$query = "delete from `sessions` where `expiry`<?";
			$this->query($query, array($oldy));
		}

		$uptodate = true;
		return true;
	}

	// Returns the number of registered users which logged in or were active in the last 5 minutes.
	function count_sessions() {
		$this->update_session();
		$query = "select count(*) from `tiki_sessions`";
		$cant = $this->getOne($query,array());
		return $cant;
	}

	// Returns a string-indexed array with all the hosts/servers active in the last 5 minutes. Keys are hostnames. Values represent the number of registered users which logged in or were active in the last 5 minutes on the host.
	function count_cluster_sessions() {
		$this->update_session();
		$query = "select `tikihost`, count(`tikihost`) as cant from `tiki_sessions` group by `tikihost`";
		$result = $this->fetchAll($query, array());
		$ret = array();
		foreach ( $result as $res ) {
			$ret[$res["tikihost"]]=$res["cant"];
		}
		return $ret;
	}

	/*shared*/
	/* Returns all module assignations for a certain position, or all positions (by default). A module assignation
	is represented by an array similar to a tiki_modules record. The groups field is unserialized in the module_groups key, a spaces-separated list of groups.
	If asking for a specific position, returns an array of module assignations. If not, returns an array of arrays of modules assignations indexed by positions. For example: array("l" -> array("module assignation"))
	TODO: Document $displayed's effect */
	function get_assigned_modules($position = null, $displayed="n") {
		$filter = '';
		$bindvars = array();

		if ( $position !== null ) {
			$filter .= 'where `position`=?';
			$bindvars[] = $position;
		}

		if ( $displayed != 'n' ) {
			$filter .= ( $filter == '' ? 'where' : 'and' ) . " (`type` is null or `type` != ?)";
			$bindvars[] = 'y';
		}

		$query = "select * from `tiki_modules` $filter order by ".$this->convertSortMode("ord_asc");

		$result = $this->fetchAll($query, $bindvars);

		$ret = array();
		foreach ( $result as $res ) {
			if ($res["groups"] && strlen($res["groups"]) > 1) {
				$grps = @unserialize($res["groups"]);

				$res["module_groups"] = '';
				if (is_array($grps)) {
					foreach ($grps as $grp) {
						$res["module_groups"] .= " $grp ";
					}
				}
			} else {
				$res["module_groups"] = '&nbsp;';
			}
			if ( $position === null ) {
				if ( ! isset($ret[$res['position']]) ) {
					$ret[$res['position']] = array();
				}
				$ret[$res['position']][] = $res;
			} else {
				$ret[] = $res;
			}
		}
		return $ret;
	}

	/*shared*/
	function is_user_module($name) {
		$query = "select `name`  from `tiki_user_modules` where `name`=?";
		$result = $this->query($query,array($name));
		return $result->numRows();
	}

	/*shared*/
	function get_user_module($name) {
		global $cachelib;
		$cacheKey = "user_modules_$name";

		if ( $cachelib->isCached($cacheKey) ) {
			$return = unserialize($cachelib->getCached($cacheKey));
		} else {
			$query = "select * from `tiki_user_modules` where `name`=?";
			if( $result = $this->query($query, array($name)) ) {
				$return = $result->fetchRow();
				$cachelib->cacheItem($cacheKey, serialize($return));
			}
		}

		return $return;
	}

	function cache_links($links) {
		global $prefs;
		if ($prefs['cachepages'] != 'y') return false;
		foreach ($links as $link) {
			if (!$this->is_cached($link)) {
				$this->cache_url($link);
			}
		}
	}

	function get_links($data) {
		$links = array();

		/// Prevent the substitution of link [] inside a <tag> ex: <input name="tracker[9]" ... >
		$data = preg_replace("/<[^>]*>/","",$data);

		/// Match things like [...], but ignore things like [[foo].
		// -Robin
		if (preg_match_all("/(?<!\[)\[([^\[\|\]]+)(?:\|?[^\[\|\]]+){0,2}\]/", $data, $r1)) {
			$res = $r1[1];
			$links = array_unique($res);
		}

		return $links;
	}

	function get_links_nocache($data) {
		$links = array();

		if (preg_match_all("/\[([^\]]+)/", $data, $r1)) {
			$res = array();

			foreach ($r1[1] as $alink) {
				$parts = explode('|', $alink);

				if (isset($parts[1]) && $parts[1] == 'nocache') {
					$res[] = $parts[0];
				} elseif (isset($parts[2]) && $parts[2] == 'nocache') {
					$res[] = $parts[0];
				} else {
					if (isset($parts[3]) && $parts[3] == 'nocache') {
						$res[] = $parts[0];
					}
				}
				/// avoid caching URLs with common binary file extensions
				$extension = substr($parts[0], -4);
				$binary = array(
						'.arj',
						'.asf',
						'.avi',
						'.bz2',
						'.com',
						'.dat',
						'.doc',
						'.exe',
						'.hqx',
						'.mid',
						'.mov',
						'.mp3',
						'.mpg',
						'.ogg',
						'.pdf',
						'.ram',
						'.rar',
						'.rpm',
						'.rtf',
						'.sea',
						'.sit',
						'.tar',
						'.tgz',
						'.wav',
						'.wmv',
						'.xls',
						'.zip',
						'ar.Z', // .tar.Z
						'r.gz'  // .tar.gz
							);
				if (in_array($extension, $binary)) {
					$res[] = $parts[0];
				}

			}

			$links = array_unique($res);
		}

		return $links;
	}

	function is_cacheable($url) {
		// simple implementation: future versions should analyse
		// if this is a link to the local machine
		if (strstr($url, 'tiki-')) {
			return false;
		}

		if (strstr($url, 'messu-')) {
			return false;
		}

		return true;
	}

	function is_cached($url) {
		$query = "select `cacheId`  from `tiki_link_cache` where `url`=?";
		$result = $this->query($query, array($url) );
		$cant = $result->numRows();
		return $cant;
	}

	function list_cache($offset, $maxRecords, $sort_mode, $find) {

		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where (`url` like ?) ";
			$bindvars=array($findesc);
		} else {
			$mid = "";
			$bindvars=array();
		}

		$query = "select `cacheId` ,`url`,`refresh` from `tiki_link_cache` $mid order by ".$this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_link_cache` $mid";
		$ret = $this->fetchAll($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function refresh_cache($cacheId) {
		$query = "select `url`  from `tiki_link_cache`
			where `cacheId`=?";

		$url = $this->getOne($query, array( $cacheId ) );
		$data = $this->httprequest($url);

		$this->table('tiki_link_cache')->update(array(
			'data' => $data,
			'refresh' => $this->now,
		), array(
			'cacheId' => $cacheId,
		));
		return true;
	}

	function remove_cache($cacheId) {
		$linkCache = $this->table('tiki_link_cache');
		$linkCache->delete(array(
			'cacheId' => $cacheId,
		));

		return true;
	}

	function get_cache($cacheId) {
		$query = "select * from `tiki_link_cache`
			where `cacheId`=?";

		$result = $this->query($query, array( $cacheId ) );
		$res = $result->fetchRow();
		return $res;
	}

	function get_cache_id($url) {
		if (!$this->is_cached($url))
			return false;

		$query = "select `cacheId`  from `tiki_link_cache`
			where `url`=?";
		$id = $this->getOne($query, array( $url ) );
		return $id;
	}
	/* cachetime = 0 => no cache, otherwise duration cache is valid */
	function get_cached_url($url, &$isFresh, $cachetime=0) {
		$query = 'select * from `tiki_link_cache` where `url`=?';
		$result = $this->query($query, $url);
		$res = $result->fetchRow();
		$now =  $this->now;
		if (empty($res) || ($now - $res['refresh']) > $cachetime) { // no cache or need to refresh
			$res['data'] = $this->httprequest($url);
			$isFresh = true;
			//echo '<br />Not cached:'.$url.'/'.strlen($res['data']);
			$res['refresh'] = $now;
			if ($cachetime > 0) {
				$linkCache = $this->table('tiki_link_cache');

				if (empty($res['cacheId'])) {
					$linkCache->insert(array(
						'url' => $url,
						'data' => $res['data'],
						'refresh' => $res['refresh'],
					));

					$result = $this->query($query, $url);
					$res = $result->fetchRow();
				} else {
					$linkCache->update(array(
						'data' => $res['data'],
						'refresh' => $res['refresh'],
					), array(
						'cacheId' => $res['cacheId'],
					));
				}
			}
		} else {
			//echo '<br />Cached:'.$url;
			$isFresh = false;
		}
		return $res;
	}

	// This funcion return the $limit most accessed pages
	// it returns pageName and hits for each page
	function get_top_pages($limit) {
		$query = "select `pageName` , `hits`
			from `tiki_pages`
			order by `hits` desc";

		$result = $this->fetchAll($query, array(),$limit);
		$ret = array();

		foreach ( $result as $res ) {
			$aux["pageName"] = $res["pageName"];

			$aux["hits"] = $res["hits"];
			$ret[] = $aux;
		}

		return $ret;
	}

	// Returns the name of all pages
	function get_all_pages() {

		$query = "select `pageName` from `tiki_pages`";
		return $this->fetchAll($query,array());
	}

	/**
	 * \brief Cache given url
	 * If \c $data present (passed) it is just associated \c $url and \c $data.
	 * Else it will request data for given URL and store it in DB.
	 * Actualy (currently) data may be proviced by TIkiIntegrator only.
	 */
	function cache_url($url, $data = '') {
		// Avoid caching internal references... (only if $data not present)
		// (cdx) And avoid other protocols than http...
		// 03-Nov-2003, by zaufi
		// preg_match("_^(mailto:|ftp:|gopher:|file:|smb:|news:|telnet:|javascript:|nntp:|nfs:)_",$url)
		// was removed (replaced to explicit http[s]:// detection) bcouse
		// I now (and actualy use in my production Tiki) another bunch of protocols
		// available in my konqueror... (like ldap://, ldaps://, nfs://, fish://...)
		// ... seems like it is better to enum that allowed explicitly than all
		// noncacheable protocols.
		if (((strstr($url, 'tiki-') || strstr($url, 'messu-')) && $data == '')
				|| (substr($url, 0, 7) != 'http://' && substr($url, 0, 8) != 'https://'))
			return false;
		// Request data for URL if nothing given in parameters
		// (reuse $data var)
		if ($data == '') $data = $this->httprequest($url);

		// If stuff inside [] is *really* malformatted, $data
		// will be empty.  -rlpowell
		if ($data)
		{
			$linkCache = $this->table('tiki_link_cache');
			$linkCache->insert(array(
				'url' => $url,
				'data' => $data,
				'refresh' => $this->now,
			));
			return true;
		}
		else return false;
	}

	// Removes all the versions of a page and the page itself
	/*shared*/
	function remove_all_versions($page, $comment = '') {
		global $dbTiki, $user, $prefs;
		if ($prefs['feature_actionlog'] == 'y') {
			$info= $this->get_page_info($page);
			$params = 'del='.strlen($info['data']);
		} else {
			$params = '';
		}
		//  Deal with mail notifications.
		include_once('lib/notifications/notificationemaillib.php');
		$foo = parse_url($_SERVER["REQUEST_URI"]);
		$machine = $this->httpPrefix( true ). dirname( $foo["path"] );
		$page_info = $this->get_page_info($page);
		sendWikiEmailNotification('wiki_page_deleted', $page, $user, $comment, 1, $page_info['data'], $machine);
		
		global $wikilib; include_once('lib/wiki/wikilib.php');
		global $multilinguallib;
		if (!is_object($multilinguallib)) {
			include_once('lib/multilingual/multilinguallib.php');// must be done even in feature_multilingual not set
		}
		$multilinguallib->detachTranslation('wiki page', $multilinguallib->get_page_id_from_name($page));
		$this->invalidate_cache($page);
		//Delete structure references before we delete the page
		$query  = "select `page_ref_id` ";
		$query .= "from `tiki_structures` ts, `tiki_pages` tp ";
		$query .= "where ts.`page_id`=tp.`page_id` and `pageName`=?";
		$result = $this->fetchAll($query, array( $page ) );
		foreach ( $result as $res ) {
			$this->remove_from_structure($res["page_ref_id"]);
		}

		$this->table('tiki_pages')->delete(array(
			'pageName' => $page,
		));
		if ($prefs['feature_contribution'] == 'y') {
			global $contributionlib; include_once('lib/contribution/contributionlib.php');
			$contributionlib->remove_page($page);
		}
		$this->table('tiki_history')->deleteMultiple(array(
			'pageName' => $page,
		));
		$this->table('tiki_links')->deleteMultiple(array(
			'fromPage' => $page,
		));
		global $logslib; include_once('lib/logs/logslib.php');
		$logslib->add_action('Removed', $page, 'wiki page', $params);
		//get_strings tra("Removed");
		$this->table('users_groups')->updateMultiple(array(
			'groupHome' => null,
		), array(
			'groupHome' => $page,
		));

		$this->table('tiki_theme_control_objects')->deleteMultiple(array(
			'name' => $page,
			'type' => 'wiki page',
		));

		$this->remove_object('wiki page', $page);

		$this->table('tiki_user_watches')->deleteMultiple(array(
			'event' => 'wiki_page_changed',
			'object' => $page,
		));
		$this->table('tiki_group_watches')->deleteMultiple(array(
			'event' => 'wiki_page_changed',
			'object' => $page,
		));

		$atts = $wikilib->list_wiki_attachments($page, 0, -1, 'created_desc', '');
		foreach ($atts["data"] as $at) {
			$wikilib->remove_wiki_attachment($at["attId"]);
		}

		$wikilib->remove_footnote('', $page);

		return true;
	}

	/*shared*/
	function remove_from_structure($page_ref_id) {
		// Now recursively remove
		$query  = "select `page_ref_id` ";
		$query .= "from `tiki_structures` as ts, `tiki_pages` as tp ";
		$query .= "where ts.`page_id`=tp.`page_id` and `parent_id`=?";
		$result = $this->fetchAll($query, array( $page_ref_id ) );

		foreach ( $result as $res ) {
			$this->remove_from_structure($res["page_ref_id"]);
		}

		global $structlib;include_once('lib/structures/structlib.php');
		$page_info = $structlib->s_get_page_info($page_ref_id);
		$query = "update `tiki_structures` set `pos`=`pos`-1 where `pos`>? and `parent_id`=?";
		$this->query($query ,array((int)$page_info['pos'], (int)$page_info['parent_id']));
		$this->table('tiki_structures')->delete(array(
			'page_ref_id' => $page_ref_id,
		));
		return true;
	}

	/*shared*/
	function list_galleries($offset = 0, $maxRecords = -1, $sort_mode = 'name_desc', $user = '', $find = null) {
		// If $user is admin then get ALL galleries, if not only user galleries are shown
		global $tiki_p_admin_galleries, $tiki_p_admin;

		$old_sort_mode = '';

		if (in_array($sort_mode, array(
						'images desc',
						'images asc'
						))) {
			$old_offset = $offset;

			$old_maxRecords = $maxRecords;
			$old_sort_mode = $sort_mode;
			$sort_mode = 'user desc';
			$offset = 0;
			$maxRecords = -1;
		}

		// If the user is not admin then select `it` 's own galleries or public galleries
		if ( $tiki_p_admin_galleries === 'y' or $tiki_p_admin === 'y') {
			$whuser = "";
			$bindvars = array();
		} else {
			$whuser = "where `user`=? or public=?";
			$bindvars = array($user,'y');
		}

		if ( ! empty($find) ) {
			$findesc = '%' . $find . '%';

			if (empty($whuser)) {
				$whuser = "where `name` like ? or `description` like ?";
				$bindvars=array($findesc,$findesc);
			} else {
				$whuser .= " and `name` like ? or `description` like ?";
				$bindvars[]=$findesc;
				$bindvars[]=$findesc;
			}
		}

		// If sort mode is versions then offset is 0, maxRecords is -1 (again) and sort_mode is nil
		// If sort mode is links then offset is 0, maxRecords is -1 (again) and sort_mode is nil
		// If sort mode is backlinks then offset is 0, maxRecords is -1 (again) and sort_mode is nil
		$query = "select * from `tiki_galleries` $whuser order by ".$this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_galleries` $whuser";
		$result = $this->fetchAll($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		foreach ( $result as $res ) {

			global $user;
			$add=$this->user_has_perm_on_object($user,$res['galleryId'],'image gallery','tiki_p_view_image_gallery');
			if ($add) {
				$aux = array();

				$aux["name"] = $res["name"];
				$gid = $res["galleryId"];
				$aux["visible"] = $res["visible"];
				$aux["id"] = $gid;
				$aux["galleryId"] = $res["galleryId"];
				$aux["description"] = $res["description"];
				$aux["created"] = $res["created"];
				$aux["lastModif"] = $res["lastModif"];
				$aux["user"] = $res["user"];
				$aux["hits"] = $res["hits"];
				$aux["public"] = $res["public"];
				$aux["theme"] = $res["theme"];
				$aux["geographic"] = $res["geographic"];
				$aux["images"] = $this->getOne("select count(*) from `tiki_images` where `galleryId`=?",array($gid));
				$ret[] = $aux;
			}
		}

		if ($old_sort_mode == 'images asc') {
			usort($ret, 'compare_images');
		}

		if ($old_sort_mode == 'images desc') {
			usort($ret, 'r_compare_images');
		}

		if (in_array($old_sort_mode, array(
						'images desc',
						'images asc'
						))) {
			$ret = array_slice($ret, $old_offset, $old_maxRecords);
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	// Deprecated in favor of list_pages
	function last_pages($maxRecords = -1, $categories='') {
		if (is_array($categories))
			$filter=array("categId" => $categories);
		else
			$filter=array();

		return $this->list_pages(0, $maxRecords, "lastModif_desc", '', '', true, true, false, false, $filter);
	}

	// Broken. Equivalent to last_pages($maxRecords)
	function last_major_pages($maxRecords = -1) {
		return $this->list_pages(0, $maxRecords, "lastModif_desc");
	}
	// use this function to speed up when pagename is only needed (the 3 getOne can killed tikiwith more that 3000 pages)
	function list_pageNames($offset = 0, $maxRecords = -1, $sort_mode = 'pageName_asc', $find = '') {
		return $this->list_pages($offset, $maxRecords, $sort_mode, $find, '', true, true);
	}

	function list_pages($offset = 0, $maxRecords = -1, $sort_mode = 'pageName_desc', $find = '', $initial = '', $exact_match = true, $onlyName=false, $forListPages=false, $only_orphan_pages = false, $filter='', $onlyCant=false, $ref='') {
		global $prefs, $user;

		$join_tables = '';
		$join_bindvars = array();
		$old_sort_mode = '';
		if ($sort_mode == 'size_desc') $sort_mode = 'page_size_desc';
		if ($sort_mode == 'size_asc') $sort_mode = 'page_size_asc';
		$select = '';

		// If sort mode is versions, links or backlinks then offset is 0, maxRecords is -1 (again) and sort_mode is nil
		$need_everything = false;
		if ( in_array($sort_mode, array(
			'versions_desc',
			'versions_asc',
			'links_asc',
			'links_desc',
			'backlinks_asc',
			'backlinks_desc'
		))) {
			$old_sort_mode = $sort_mode;
			$sort_mode = 'user_desc';
			$need_everything = true;
		}

		if (is_array($find)) { // you can use an array of pages
			$mid = " where LOWER(`pageName`) IN (".implode(',',array_fill(0,count($find),'LOWER(?)')).")";
			$bindvars = $find;
		} elseif (is_string($find) && !empty($find)) { // or a string
			if (!$exact_match && $find) {
				$find = preg_replace("/([^\s]+)/","%\\1%",$find);
				$f = preg_split("/[\s]+/",$find,-1,PREG_SPLIT_NO_EMPTY);
				if (empty($f)) {//look for space...
					$mid = " where LOWER(`pageName`) like LOWER('%$find%')";
				} else {
					$mid = " where LOWER(`pageName`) like ".implode(' or LOWER(`pageName`) like ',array_fill(0,count($f),'LOWER(?)'));
					$bindvars = $f;
				}
			} else {
				$mid = " where LOWER(`pageName`) like LOWER(?) ";
				$bindvars = array($find);
			}
		} else {
			$bindvars = array();
			$mid = '';
		}

		global $categlib; require_once( 'lib/categories/categlib.php' );
		$category_jails = $categlib->get_jail();

		if( ! isset( $filter['andCategId'] ) && ! isset( $filter['categId'] ) && ! empty( $category_jails ) ) {
			$filter['categId'] = $category_jails;
		}
		
		// If language is set to '', assume that no language filtering should be done.
		if (isset($filter['lang']) && $filter['lang'] == '') {
			unset($filter['lang']);
		}

		$distinct = '';
		if (!empty($filter)) {
			$tmp_mid = array();
			foreach ($filter as $type=>$val) {
				if ($type == 'andCategId') {
					$categories = $categlib->get_jailed( (array) $val );
					$join_tables .= " inner join `tiki_objects` as tob on (tob.`itemId`= tp.`pageName` and tob.`type`= ?) ";
					$join_bindvars[] = 'wiki page';
					foreach ($categories as $i=>$categId) {
						$join_tables .= " inner join `tiki_category_objects` as tc$i on (tc$i.`catObjectId`=tob.`objectId` and tc$i.`categId` =?) ";
						$join_bindvars[] = $categId;
					}
				} elseif ($type == 'categId') {
					$categories = $categlib->get_jailed( (array) $val );
					$categories[] = -1;

					$cat_count = count( $categories );
					$join_tables .= " inner join `tiki_objects` as tob on (tob.`itemId`= tp.`pageName` and tob.`type`= ?) inner join `tiki_category_objects` as tc on (tc.`catObjectId`=tob.`objectId` and tc.`categId` IN(" . implode(', ', array_fill(0, $cat_count, '?')) . ")) ";

					if( $cat_count > 1 ) {
						$distinct = ' DISTINCT ';
					}

					$join_bindvars = array_merge(array('wiki page'), $categories);
				} elseif ($type == 'noCateg') {
					$join_tables .= ' left join `tiki_objects` as tob on (tob.`itemId`= tp.`pageName` and tob.`type`= ?) left join `tiki_categorized_objects` as tcdo on (tcdo.`catObjectId`=tob.`objectId`) left join `tiki_category_objects` as tco on (tcdo.`catObjectId`=tco.`catObjectId`)';
					$join_bindvars[] = 'wiki page';
					$tmp_mid[] = '(tco.`categId` is null)';
				} elseif ($type == 'notCategId') {
					foreach ($val as $v) {
						$tmp_mid[] = '(tp.`pageName` NOT IN(SELECT itemId FROM tiki_objects INNER JOIN tiki_category_objects ON catObjectId = objectId WHERE type = "wiki page" AND categId = ?))';
						$bindvars[] = $v;
					}
				} elseif ($type == 'lang') {
					$tmp_mid[] = 'tp.`lang`=?';
					$bindvars[] = $val;
				} elseif ($type == 'structHead') {
					$join_tables .= " inner join `tiki_structures` as ts on (ts.`page_id` = tp.`page_id` and ts.`parent_id` = 0) ";
					$select .= ',ts.`page_alias`';
				} elseif ($type == 'langOrphan') {
					$join_tables .= " left join `tiki_translated_objects` tro on (tro.`type` = 'wiki page' AND tro.`objId` = tp.`page_id`) ";
					$tmp_mid[] = "( (tro.`traId` IS NULL AND tp.`lang` != ?) OR tro.`traId` NOT IN(SELECT `traId` FROM `tiki_translated_objects` WHERE `lang` = ?))";
					$bindvars[] = $val;
					$bindvars[] = $val;
				} elseif ($type == 'structure_orphans') {
					$join_tables .= " left join `tiki_structures` as tss on (tss.`page_id` = tp.`page_id`) ";
					$tmp_mid[] = "(tss.`page_ref_id` is null)";
				} elseif ($type == 'translationOrphan') {
					global $multilinguallib; include_once('lib/multilingual/multilinguallib.php');
					$multilinguallib->sqlTranslationOrphan('wiki page', 'tp', 'page_id', $val, $join_tables, $midto, $bindvars);
					$tmp_mid[] = $midto;
				}
			}
			if (!empty($tmp_mid)) {
				$mid .= empty($mid) ? ' where (' : ' and (';
				$mid .= implode( ' and ', $tmp_mid ) . ')';
			}
		}
		if (!empty($initial)) {
			$mid .= empty($mid) ? ' where (' : ' and (';
			$tmp_mid = '';
			if (is_array($initial)) {
				foreach($initial as $i) {
					if ( ! empty($tmp_mid) ) $tmp_mid .= ' or ';
					$tmp_mid .= ' `pageName` like ? ';
					$bindvars[] = $i.'%';
				}
			} else {
				$tmp_mid = " `pageName` like ? ";
				$bindvars[] = $initial.'%';
			}
			$mid .= $tmp_mid.')';
			$mmid = $mid;
		}

		if ( $only_orphan_pages ) {
			$join_tables .= ' left join `tiki_links` as tl on tp.`pageName` = tl.`toPage` left join `tiki_structures` as ts on tp.`page_id` = ts.`page_id`';
			$mid .= ( $mid == '' ) ? ' where ' : ' and ';
			$mid .= 'tl.`toPage` IS NULL and ts.`page_id` IS NULL';
		}

		if ( $prefs['rating_advanced'] == 'y' ) {
			global $ratinglib; require_once 'lib/rating/ratinglib.php';
			$join_tables .= $ratinglib->convert_rating_sort($sort_mode, 'wiki page', '`page_id`');
		}


		if (!empty($join_bindvars)) {
			$bindvars = empty($bindvars)? $join_bindvars : array_merge($join_bindvars, $bindvars);
		}

		$query = "select $distinct"
			.( $onlyCant ? "tp.`pageName`" : "tp.* ".$select )
			." from `tiki_pages` as tp $join_tables $mid order by ".$this->convertSortMode($sort_mode);
		$countquery = "select count($distinct tp.`pageName`) from `tiki_pages` as tp $join_tables $mid";
		$pageCount = $this->getOne($countquery,$bindvars);


		// HOTFIX (svn Rev. 22969 or near there)
		// Chunk loading. Because we cannot know what pages are visible, we load chunks of pages 
		// and use Perms::filter to see what remains. Stop, if we have enough.
		$cant = 0;
		$n = -1;
		$ret = array();
		$raw = array();

		$offset_tmp = 0;
		$haveEnough=FALSE;
		$filterPerms = empty($ref)? 'view': array('view', 'wiki_view_ref');
		while (!$haveEnough) {
			$rawTemp = $this->fetchAll($query, $bindvars, $maxRecords , $offset_tmp);
			$offset_tmp+=$maxRecords; // next offset
	
			if (count($rawTemp) == 0) $haveEnough = TRUE; // end of table

			$rawTemp = Perms::filter( array( 'type' => 'wiki page' ), 'object', $rawTemp, array( 'object' => 'pageName', 'creator' => 'creator' ), $filterPerms );

			$raw = array_merge($raw, $rawTemp);
			if( (count($raw) >= $offset + $maxRecords) || $maxRecords == -1 ) $haveEnough = TRUE; // now we have enough records
		} // prbably this brace has to include the next foreach??? I am unsure.
		// but if yes, the next lines have to be reviewed.


		foreach( $raw as $res ) {
			if( $initial ) {
				$valid = false;
				foreach( (array) $initial as $candidate ) {
					if( stripos( $res['pageName'], $candidate ) === 0 ) {
						$valid = true;
						break;
					}
				}

				if( ! $valid )
					continue;
			}
			//WYSIWYCA
			$res['perms'] = $this->get_perm_object($res['pageName'], 'wiki page', $res, false);

			$n++;
			if ( ! $need_everything && $offset != -1 && $n < $offset ) continue;

			if ( ! $onlyCant && ( $need_everything || $maxRecords == -1 || $cant < $maxRecords ) ) {
				if ( $onlyName ) $res = array('pageName' => $res['pageName']);
				else {
					$page = $res['pageName'];
					$res['len'] = $res['page_size'];
					unset($res['page_size']);
					$res['flag'] = $res['flag'] == 'L' ? 'locked' : 'unlocked';
					if ($forListPages && $prefs['wiki_list_versions'] == 'y')
						$res['versions'] = $this->getOne("select count(*) from `tiki_history` where `pageName`=?",array($page));
					if ($forListPages && $prefs['wiki_list_links'] == 'y')
						$res['links'] = $this->getOne("select count(*) from `tiki_links` where `fromPage`=?",array($page));
					if ($forListPages && $prefs['wiki_list_backlinks'] == 'y')
						$res['backlinks'] = $this->getOne("select count(*) from `tiki_links` where `toPage`=? and `fromPage` not like 'objectlink:%'",array($page));
					// backlinks do not include links from non-page objects TODO: full feature allowing this with options
				}
				$ret[] = $res;
			}
			$cant++;
		}
		if ( ! $need_everything ) $cant += $offset;

		// If sortmode is versions, links or backlinks sort using the ad-hoc function and reduce using old_offset and old_maxRecords
		if ( $need_everything ) {
			switch ( $old_sort_mode ) {
				case 'versions_asc': usort($ret, 'compare_versions'); break;
				case 'versions_desc': usort($ret, 'r_compare_versions'); break;
				case 'links_desc': usort($ret, 'compare_links'); break;
				case 'links_asc': usort($ret, 'r_compare_links'); break;
				case 'backlinks_desc': usort($ret, 'compare_backlinks'); break;
				case 'backlinks_asc': usort($ret, 'r_compare_backlinks'); break;
			}
		}

		$retval = array();
		$retval['data'] = $ret;
		$retval['cant'] = $pageCount; // this is not exact. Workaround.
		return $retval;
	}


	// Function that checks for:
	// - tiki_p_admin
	// - the permission itself
	// - individual permission
	// - category permission
	// if O.K. this function shall replace similar constructs in list_pages and other functions above.
	// $categperm is the category permission that should grant $perm. if none, pass 0
	function user_has_perm_on_object($user,$object,$objtype,$perm) {
		$groups = $this->get_user_groups( $user );
		$context = array( 'type' => $objtype, 'object' => $object );

		$accessor = Perms::get( $context );
		$accessor->setGroups( $groups );

		return $accessor->$perm;
	}

	/* get all the perm of an object either in a table or global+smarty set
	 * OPTIMISATION: better to test tiki_p_admin outside for global=false
	 * TODO: all the objectTypes
	 * TODO: replace switch with object
	 * global = true set the global perm and smarty var, otherwise return an array of perms
	 */
	function get_perm_object($objectId, $objectType, $info='', $global=true) {
		global $smarty, $userlib, $user;
		$perms = Perms::get( array( 'type' => $objectType, 'object' => $objectId ) );
		$permDescs = $userlib->get_permissions(0, -1, 'permName_desc', '', $this->get_permGroup_from_objectType($objectType));

		$ret = array();
		foreach( $permDescs['data'] as $perm ) {
			$perm = $perm['permName'];

			$ret[$perm] = $perms->$perm ? 'y' : 'n';

			if( $global ) {
				$smarty->assign( $perm, $ret[$perm] );
				$GLOBALS[ $perm ] = $ret[$perm];
			}
		}

		// Skip those 'local' permissions for admin users and when global is not requested.
		if ($global && ! Perms::get()->admin) {
			$ret2 = $this->get_local_perms($user, $objectId, $objectType, $info, true);
			if ($ret2) {
				$ret = $ret2;
			}
		}
		
		return $ret;
	}

	function get_permGroup_from_objectType($objectType) {

		switch ($objectType) {
			case 'tracker':
				return 'trackers';
			case 'image gallery':
			case 'image':
				return 'image galleries';
			case 'file gallery':
			case 'file':
				return 'file galleries';
			case 'article':
			case 'submission':
				return 'cms';
			case 'forum':
				return 'forums';
			case 'blog':
			case 'blog post':
				return 'blogs';
			case 'wiki page':
			case 'history':
				return 'wiki';
			case 'faq':
				return 'faqs';
			case 'survey':
				return 'surveys';
			case 'newsletter':
				return 'newsletters';
				/* TODO */
			default:
				return $objectType;
		}
	}

	function get_adminPerm_from_objectType($objectType) {

		switch ($objectType) {
			case 'tracker':
				return 'tiki_p_admin_trackers';
			case 'image gallery':
			case 'image':
				return 'tiki_p_admin_galleries';
			case 'file gallery':
			case 'file':
				return 'tiki_p_admin_file_galleries';
			case 'article':
			case 'submission':
				return 'tiki_p_admin_cms';
			case 'forum':
				return 'tiki_p_admin_forum';
			case 'blog':
			case 'blog post':
				return 'tiki_p_blog_admin';
			case 'wiki page':
			case 'history':
				return 'tiki_p_admin_wiki';
			case 'faq':
				return 'tiki_p_admin_faqs';
			case 'survey':
				return 'tiki_p_admin_surveys';
			case 'newsletter':
				return 'tiki_p_admin_newsletters';
				/* TODO */
			default:
				return "tiki_p_admin_$objectType";
		}
	}

	/* deal all the special perm */
	function get_local_perms($user, $objectId, $objectType, $info, $global) {
		global $userlib, $smarty, $prefs;
		$ret = array();
		switch ($objectType) {
			case 'wiki page': case 'wiki':
				if ( $prefs['wiki_creator_admin'] == 'y' && !empty($user) && isset($info) && $info['creator'] == $user ) { //can admin his page
					$perms = $userlib->get_permissions(0, -1, 'permName_desc', '', $this->get_permGroup_from_objectType($objectType));
					foreach ($perms['data'] as $perm) {
						$perm = $perm['permName'];
						$ret[$perm] = 'y';
						if ($global) {
							global $$perm;
							$$perm = 'y';
							$smarty->assign("$perm", 'y');
						}
					}
					return $ret;
				}
				// Enabling userpage is not enough, the prefix must be present, otherwise, permissions will be messed-up on new page creation
				if ($prefs['feature_wiki_userpage'] == 'y' && !empty($prefs['feature_wiki_userpage_prefix']) && !empty($user) && strcasecmp($prefs['feature_wiki_userpage_prefix'], substr($objectId, 0, strlen($prefs['feature_wiki_userpage_prefix']))) == 0) {
					if (strcasecmp($objectId, $prefs['feature_wiki_userpage_prefix'].$user) == 0) { //can edit his page
						if (!$global) {
							$perms = $userlib->get_permissions(0, -1, 'permName_desc', '', $this->get_permGroup_from_objectType($objectType));
							foreach ($perms['data'] as $perm) {
								global $$perm['permName'];
								if ($perm['permName'] == 'tiki_p_view' || $perm['permName'] == 'tiki_p_edit') {
									$ret[$perm['permName']] = 'y';
								} else {
									$ret[$perm['permName']] = $$perm['permName'];
								}
							}
						} else {
							global $tiki_p_edit, $tiki_p_view;
							$tiki_p_view = 'y';
							$smarty->assign('tiki_p_view', 'y');
							$tiki_p_edit = 'y';
							$smarty->assign('tiki_p_edit', 'y');
						}
					} else {
						if (!$global) {
							$ret['tiki_p_edit'] = 'n';
						} else {
							global $tiki_p_edit;
							$tiki_p_edit = 'n';
							$smarty->assign('tiki_p_edit', 'n');
						}
					}
					if (!$global) {
						$ret['tiki_p_rename'] = 'n';
						$ret['tiki_p_rollback'] = 'n';
						$ret['tiki_p_lock'] = 'n';
						$ret['tiki_p_assign_perm_wiki_page'] = 'n';
					} else {
						global $tiki_p_rename, $tiki_p_rollback, $tiki_p_lock, $tiki_p_assign_perm_wiki_page;
						$tiki_p_rename = $tiki_p_rollback = $tiki_p_lock = $tiki_p_assign_perm_wiki_page = 'n';
						$smarty->assign('tiki_p_rename', 'n');
						$smarty->assign('tiki_p_rollback', 'n');
						$smarty->assign('tiki_p_lock', 'n');
						$smarty->assign('tiki_p_assign_perm_wiki_page', 'n');
					}
				}
				break;
			default:
				break;
		}
		return false;
	}

	// This method overrides the prefs with those specified in database
	//   and should only be used when populating the prefs array in session vars (during tiki-setup.php process)
	function get_db_preferences() {

		$needLoading = false;
		$needCache = false;

		// modified to cache for non-logged in users (case where logged out users have no session)
		if (isset($_SESSION['s_prefs'])) {
			$needLoading = true;
		} else {
			//logged out
			global $cachelib; require_once("lib/cache/cachelib.php");
			if ( $data = $cachelib->getSerialized("tiki_preferences_cache")) {
				return $data;
			}
			$needLoading = true;
			$needCache = true;
		}	

		if( $needLoading ) {
			$defaults = get_default_prefs();
			$modified = array();

			// logged in
			$result = $this->fetchAll("select `name` ,`value` from `tiki_preferences`");
			foreach ( $result as $res ) {
				$name = $res['name'];
				$value = $res['value'];

				if( !isset($defaults[$name]) || (string) $defaults[$name] != (string) $value )
					$modified[$name] = $value;
			}

			$modified['lastReadingPrefs'] = isset($modified['lastUpdatePrefs']) ? $modified['lastUpdatePrefs'] : -1;		
		}

		if( $needCache ) {
			$cachelib->cacheItem("tiki_preferences_cache",serialize($modified));
		}

		return $modified;
	}

	function get_preferences( $names, $exact_match = false, $no_return = false ) {
		global $prefs;

		$preferences = array();
		if ( $exact_match ) {
			if ( is_array($names) ) {
				$this->_get_values('tiki_preferences', 'name', $names, $prefs);
				if ( ! $no_return ) foreach ( $names as $name ) $preferences[$name] = $prefs[$name];
			} else {
				$this->get_preference($names);
				if ( ! $no_return ) $preferences = array( $names => $prefs[$names] );
			}
		} else {
			if ( is_array($names) ) {
				//Only handle $filtername as array with exact_matches
				return false;
			} else {
				$query = "select `name`, `value` from `tiki_preferences` where `name` like ?";
				$result = $this->fetchAll($query, array($names));
				foreach ( $result as $res ) {
					$preferences[$res["name"]] = $res["value"];
				}
			}
		}
		return $preferences;
	}

	function get_preference($name, $default = '', $expectArray = false ) {
		global $prefs;
		$value = isset($prefs[$name]) ? $prefs[$name] : $default;

		if( $expectArray && is_string( $value ) ) {
			return unserialize( $value );
		} else {
			return $value;
		}
	}

	function delete_preference($name) {
		global $prefs;
		$this->table('tiki_preferences')->delete(array(
			'name' => $name,
		));
		$this->set_lastUpdatePrefs();
	}

	function set_preference($name, $value) {
		global $user_overrider_prefs, $user_preferences, $user, $prefs;

		global $cachelib; require_once("lib/cache/cachelib.php");
		$cachelib->invalidate('tiki_preferences_cache');

		global $menulib; include_once('lib/menubuilder/menulib.php');
		$menulib->empty_menu_cache();

		$this->set_lastUpdatePrefs();

		$preferences = $this->table('tiki_preferences');
		$preferences->delete(array(
			'name' => $name,
		));
		$preferences->insert(array(
			'name' => $name,
			'value' => is_array($value) ? serialize($value) : $value,
		));

		if ( isset($prefs) ) {
			if ( in_array($name, $user_overrider_prefs) ) {
				$prefs['site_'.$name] = $value;
				$_SESSION['s_prefs']['site_'.$name] = $value;
			} elseif ( isset($user_preferences[$user][$name] ) ) {
				$prefs[$name] = $user_preferences[$user][$name];
				$_SESSION['s_prefs'][$name] = $user_preferences[$user][$name];
			} else {
				$prefs[$name] = $value;
				$_SESSION['s_prefs'][$name] = $value;
			}
			++$prefs['lastUpdatePrefs'];
			$_SESSION['s_prefs']['lastUpdatePrefs'] = $prefs['lastUpdatePrefs'];
		}
		return true;
	}

	function set_lastUpdatePrefs() {
		$preferences = $this->table('tiki_preferences');
		$preferences->update(array(
			'value' => $preferences->increment(1),
		), array(
			'name' => 'lastUpdatePrefs',
		));
	}

	function _get_values($table, $field_name, $var_names = null, &$global_ref, $query_cond = '', $bindvars = null) {
		if ( empty($table) || empty($field_name) ) return false; 

		$needed = array();
		$defaults = null;

		if ( is_array($var_names) ) {

			// Detect if var names are specified as keys (then values are considered as var defaults)
			//   by looking at the type of the first key
			$defaults = ! is_integer(key($var_names));

			// Check if we need to get the value from DB by looking in the global $user_preferences array
			// (this is able to handle more than one var at a time)
			//   ... and store the default values as well, just in case we needs them later
			if ( $defaults ) {
				foreach ( $var_names as $var => $default ) {
					if ( ! isset($global_ref[$var]) ) $needed[$var] = $default;
				}
			} else {
				foreach ( $var_names as $var ) {
					if ( ! isset($global_ref[$var]) ) $needed[$var] = null;
				}
			}

		} elseif ( $var_names !== null ) {
			return false;
		}

		$cond_query = '';
		if (empty($query_cond) && empty($needed)) {
			$query_cond = 'TRUE';
		}
		$result = null;
		if ( is_null($bindvars) ) $bindvars = array();
		if ( count($needed) > 0 ) {
			foreach ( $needed as $var => $def ) {
				if ( $cond_query != '' ) {
					$cond_query .= ' or ';
				} elseif ( $query_cond != '' ) {
					$cond_query = ' and ';
				}
				$cond_query .= "`$field_name`=?";
				$bindvars[] = $var;
			}
		}
		$query = "select `$field_name`, `value` from `$table` where $query_cond $cond_query";
		$result = $this->fetchAll($query, $bindvars);
		
		foreach ( $result as $res ) {
			// store the db value in the global array
			$global_ref[$res[$field_name]] = $res['value'];
			// remove vars that have a value in db from the $needed array to avoid affecting them a default value
			unset($needed[$res[$field_name]]);
		}

		// set defaults values if needed and if there is no value in database and if it's default was not null
		if ( $defaults ) {
			foreach ( $needed as $var => $def ) {
				if ( ! is_null($def) ) $global_ref[$var] = $def;
			}
		}
		return true;
	}


	function get_user_preferences($my_user, $names = null) {
		global $user_preferences;

		// $my_user must be specified
		if ( ! is_string($my_user) || $my_user == '' ) return false;

		global $user_preferences;
		$global_ref =& $user_preferences[$my_user];
		$return = $this->_get_values('tiki_user_preferences', 'prefName', $names, $global_ref, '`user`=?', array($my_user));

		// Handle special display_timezone values
		if ( isset($user_preferences[$my_user]['display_timezone']) && $user_preferences[$my_user]['display_timezone'] != 'Site' && $user_preferences[$my_user]['display_timezone'] != 'Local'
				&& ! TikiDate::TimezoneIsValidId($user_preferences[$my_user]['display_timezone'])
			 ) {
			unset($user_preferences[$my_user]['display_timezone']);
		}
		return $return;
	}

	function get_user_preference($my_user, $name, $default = null) {
		global $user_preferences, $user;
		if ($user != $my_user && !isset($user_preferences[$my_user])) {
			$this->get_user_preferences($my_user);
		}
		if ( isset($user_preferences) && isset($user_preferences[$my_user]) && isset($user_preferences[$my_user][$name]) ) {
			return $user_preferences[$my_user][$name];
		}
		return $default;
	}

	function set_user_preference($my_user, $name, $value) {
		global $user_preferences, $cachelib, $prefs, $user, $user_overrider_prefs;

		require_once("lib/cache/cachelib.php");
		$cachelib->invalidate('user_details_'.$my_user);

		if ($name == "realName") {
			// attempt to invalidate userlink cache (does not cover all options - only the default)
			$cachelib->invalidate('userlink.'.$user.'.'.$my_user.'0');
			$cachelib->invalidate('userlink.'.$my_user.'0');
		}
		$user_preferences[$my_user][$name] = $value;

		if ( $my_user == $user ) {
			$prefs[$name] = $value;
			$_SESSION['s_prefs'][$name] = $value;
			if ( $name == 'theme' && $prefs['change_theme'] == 'y' ) { // FIXME: Remove this exception
				$prefs['style'] = $value;
				$_SESSION['s_prefs']['style'] = $value;
				if ( $value == '' ) {
					$prefs['style'] = $prefs['site_style'];
					$_SESSION['s_prefs']['style'] = $prefs['site_style'];
				}
			} elseif ( $name == 'theme-option' && $prefs['change_theme'] == 'y' ) { // FIXME: Remove this exception as well?
				$prefs['style_option'] = $value;
				$_SESSION['s_prefs']['style_option'] = $value;
				if ( $value == '' ) {
					$prefs['style_option'] = $prefs['site_style_option'];
					$_SESSION['s_prefs']['style_option'] = $prefs['site_style_option'];
				} else if ( $value == 'None' ) {
					$prefs['style_option'] = '';
					$_SESSION['s_prefs']['style_option'] = '';
				}
			} elseif ( $value == '' ) {
				if ( in_array($name, $user_overrider_prefs) ) {
					$prefs[$name] = $prefs['site_'.$name];
					$_SESSION['s_prefs'][$name] = $prefs['site_'.$name];
				} else {
					$_SESSION['need_reload_prefs'] = true;
				}
			}
		}

		if (!empty($my_user)) {
			$userPreferences = $this->table('tiki_user_preferences');
			$userPreferences->delete(array(
				'user' => $my_user,
				'prefName' => $name,
			));
			$userPreferences->insert(array(
				'user' => $my_user,
				'prefName' => $name,
				'value' => $value,
			));
		}

		return true;
	}

	// similar to set_user_preference, but set all at once.
	function set_user_preferences($my_user, &$preferences) {
		global $user_preferences, $cachelib, $prefs, $user;

		require_once("lib/cache/cachelib.php");
		$cachelib->invalidate('user_details_'.$my_user);

		$userPreferences = $this->table('tiki_user_preferences');
		$userPreferences->deleteMultiple(array(
			'user' => $my_user,
		));

		foreach ($preferences as $prefName => $value) {
			$userPreferences->insert(array(
				'user' => $my_user,
				'prefName' => $prefName,
				'value' => $value,
			));
		}
		$user_preferences[$my_user] =& $preferences;

		if ( $my_user == $user ) {
			$prefs =array_merge($prefs, $preferences);
			$_SESSION['s_prefs']=array_merge($_SESSION['s_prefs'], $preferences);
			$_SESSION['need_reload_prefs'] = true;
		}
		return true;
	}

	// This implements all the functions needed to use Tiki
	/*shared*/
	// Returns whether a page named $pageName exists. Unless $casesensitive is set to true, the check is case-insensitive.
	function page_exists($pageName, $casesensitive = false) {
		$page_info = $this->get_page_info($pageName, false);
		return ( $page_info !== false && ( ! $casesensitive || $page_info['pageName'] == $pageName ) ) ? 1 : 0;
	}

	function page_exists_desc( &$pageName ) {
	
		$page_info = $this->get_page_info($pageName, false);
		
		return empty($page_info['description']) ? $pageName : $page_info['description'];
	}

	function page_exists_modtime($pageName) {
		$page_info = $this->get_page_info($pageName, false);
		if ( $page_info === false ) return false;
		return empty($page_info['lastModif']) ? 0 : $page_info['lastModif'];
	}

	function add_hit($pageName) {
		$pages = $this->table('tiki_pages');
		$pages->update(array(
			'hits' => $pages->increment('hits'),
		), array(
			'pageName' => $pageName,
		));
		return true;
	}

	/** Create a wiki page
		@param array $hash- lock_it,contributions, contributors
	 **/
	function create_page($name, $hits, $data, $lastModif, $comment, $user = 'admin', $ip = '0.0.0.0', $description = '', $lang='', $is_html = false, $hash=null, $wysiwyg=NULL, $wiki_authors_style='', $minor=0, $created='') {
		global $smarty, $prefs, $dbTiki, $quantifylib;
		include_once ("lib/comments/commentslib.php");

		$commentslib = new Comments($dbTiki);

		if( ! $is_html ) {
			$data = str_replace( '<x>', '', $data );
		}
		$name = trim($name); // to avoid pb with trailing space http://dev.mysql.com/doc/refman/5.1/en/char.html

		if (!$user) $user = 'anonymous';
		if (empty($wysiwyg)) $wysiwyg = $prefs['wysiwyg_default'];
		// Collect pages before modifying data
		$pages = $this->get_pages($data, true);

		// This *really* shouldn't be necessary now that the
		// query itself has been fixed up, and it causes much
		// badness to the phpwiki import.  -rlpowell
		//  $name = addslashes($name);
		//  $description = addslashes($description);
		//  $data = addslashes($data);
		//  $comment = addslashes($comment);

		if (!isset($_SERVER["SERVER_NAME"])) {
			$_SERVER["SERVER_NAME"] = $_SERVER["HTTP_HOST"];
		}

		if ($this->page_exists($name))
			return false;

		$html=$is_html?1:0;
		if ($html && $prefs['feature_purifier'] != 'n') {
			require_once('lib/htmlpurifier_tiki/HTMLPurifier.tiki.php');
			$data = HTMLPurifier($data);
		}
		
		$insertData = array(
			'pageName' => $name,
			'hits' => (int) $hits,
			'data' => $data,
			'lastModif' => (int) $lastModif,
			'comment' => $comment,
			'version' => 1,
			'version_minor' => $minor,
			'user' => $user,
			'page_size' => strlen($data),
			'is_html' => $html,
			'created' => empty($created) ? $this->now : $created,
			'wysiwyg' => $wysiwyg,
			'wiki_authors_style' => $wiki_authors_style,
		);
		if ($lang) {
			$insertData['lang'] = $lang;
		}
		if (!empty($hash['lock_it']) && ($hash['lock_it'] == 'y' || $hash['lock_it'] == 'on')) {
			$insertData['flag'] = 'L';
			$insertData['lockedby'] = $user;
		} elseif (empty($hash['lock_it']) || $hash['lock_it'] == 'n') {
			$insertData['flag'] = '';
			$insertData['lockedby'] = '';
		}
		if ($prefs['wiki_comments_allow_per_page'] != 'n') {
			if (!empty($hash['comments_enabled']) && $hash['comments_enabled'] == 'y') {
				$insertData['comments_enabled'] = 'y';
			} else if (empty($hash['comments_enabled']) || $hash['comments_enabled'] == 'n') {
				$insertData['comments_enabled'] = 'n';
			}
		}
		if (empty($hash['contributions'])) {
			$hash['contributions'] = '';
		}
		if (empty($hash['contributors'])) {
			$hash2 = '';
		} else {
			foreach ($hash['contributors'] as $c) {
				$hash3['contributor'] = $c;
				$hash2[] = $hash3;
			}
		}
		$pages = $this->table('tiki_pages');
		$page_id = $pages->insert($insertData);

		$this->replicate_page_to_history($name);

		if( $prefs['quantify_changes'] == 'y' && $prefs['feature_multilingual'] == 'y' ) {
			include_once 'lib/wiki/quantifylib.php';
			$quantifylib->recordChangeSize( $page_id, 1, '', $data );
		}

		$this->clear_links($name);

		// Pages are collected before adding slashes
		foreach ($pages as $a_page => $types) {
			$this->replace_link($name, $a_page, $types);
		}
		
		// Update the log
		if (strtolower($name) != 'sandbox') {
			global $logslib; include_once("lib/logs/logslib.php");
			$logslib->add_action("Created", $name, 'wiki page', 'add='.strlen($data), '', '', '', '', $hash['contributions'], $hash2);
			//get_strings tra("Created");

			//  Deal with mail notifications.
			include_once('lib/notifications/notificationemaillib.php');

			$foo = parse_url($_SERVER["REQUEST_URI"]);
			$machine = $this->httpPrefix( true ). dirname( $foo["path"] );
			sendWikiEmailNotification('wiki_page_created', $name, $user, $comment, 1, $data, $machine, '', false, $hash['contributions']);
			if ($prefs['feature_contribution'] == 'y') {
				global $contributionlib; include_once('lib/contribution/contributionlib.php');
				$contributionlib->assign_contributions($hash['contributions'], $name, 'wiki page', $description, $name, "tiki-index.php?page=".urlencode($name));
			}
		}

		//if there are links to this page, clear cache to avoid linking to edition
		$result = $this->fetchAll("select `fromPage` from `tiki_links` where `toPage`=?",array($name));
		foreach ( $result as $res ) {
			$this->invalidate_cache($res['fromPage']);
		}

		if ($prefs['feature_score'] == 'y') {
			$this->score_event($user, 'wiki_new');
		}

		require_once('lib/search/refresh-functions.php');
		refresh_index('pages', $name);

		$this->object_post_save( array( 'type'=> 'wiki page', 'object'=> $name, 'description'=> $description, 'name'=>$name, 'href'=>"tiki-index.php?page=$name" ), array(
			'content' => $data,
		) );

		// Update HTML wanted links when wysiwyg is in use - this is not an elegant fix
		// but will do for now until the "use wiki syntax in WYSIWYG" feature is ready 
		if ($prefs['feature_wysiwyg'] == 'y' && $prefs['wysiwyg_htmltowiki'] != 'y') {
			global $wikilib; include_once('lib/wiki/wikilib.php');
			$temppage = md5($this->now . $name);
			$wikilib->wiki_rename_page($name, $temppage);
			$wikilib->wiki_rename_page($temppage, $name);
		}
		
		return true;
	}

	private function replicate_page_to_history($pageName) {
		if (strtolower($pageName) == 'sandbox') {
			return false;
		}

		$query = "INSERT IGNORE INTO `tiki_history`(`pageName`, `version`, `version_minor`, `lastModif`, `user`, `ip`, `comment`, `data`, `description`,`is_html`)
			SELECT `pageName`, `version`, `version_minor`, `lastModif`, `user`, `ip`, `comment`, `data`, `description`,`is_html`
			FROM tiki_pages
			WHERE pageName = ?
			LIMIT 1";

		$this->query($query, array($pageName));
		return $this->lastInsertId();
	}

	function get_user_pages($user, $max, $who='user') {
		$query = "select `pageName` from `tiki_pages` where `$who`=?";
		return $this->fetchAll($query,array($user),$max);
	}

	function get_user_galleries($user, $max) {
		$query = "select `name` ,`galleryId`  from `tiki_galleries` where `user`=? order by `name` asc";

		$result = $this->fetchAll($query,array($user),$max);
		$ret = array();

		foreach ( $result as $res ) {
			//FIXME Perm::filter ?
			if ($this->user_has_perm_on_object($user, $res['galleryId'], 'image gallery', 'tiki_p_view_image_gallery')) {
				$ret[] = $res;
			}
		}
		return $ret;
	}

	function get_page_print_info($pageName) {
		$query = "SELECT `pageName`, `data` as `parsed`, `is_html` FROM `tiki_pages` WHERE `pageName`=?";
		$result = $this->query($query, array($pageName));
		if ( ! $result->numRows() ) {
			return false;
		} else {
			$page_info = $result->fetchRow();
			$page_info['parsed'] = $this->parse_data($page_info['parsed'], array('is_html' => $page_info['is_html'], 'print'=>'y', 'page'=>$pageName));
		}
		return $page_info;
	}

	function get_page_info($pageName, $retrieve_datas = true, $skipCache = false) {
		$pageNameEncode = urlencode($pageName);
		if ( !$skipCache && isset($this->cache_page_info[$pageNameEncode])
			&& ( ! $retrieve_datas || isset($this->cache_page_info[$pageNameEncode]['data']) )
		) {
			return $this->cache_page_info[$pageNameEncode];
		}

		if ( $retrieve_datas ) {
			$query = "SELECT * FROM `tiki_pages` WHERE `pageName`=?";
		} else {
			$query = "SELECT `page_id`, `pageName`, `hits`, `description`, `lastModif`, `comment`, `version`, `version_minor`, `user`, `ip`, `flag`, `points`, `votes`, `wiki_cache`, `cache_timestamp`, `pageRank`, `creator`, `page_size`, `lang`, `lockedby`, `is_html`, `created`, `wysiwyg`, `wiki_authors_style`, `comments_enabled` FROM `tiki_pages` WHERE `pageName`=?";
		}
		$result = $this->query($query, array($pageName));

		if ( ! $result->numRows() ) {
			return false;
		} else {
			$row = $result->fetchRow();

			// Be sure to have the correct character case (because DB is caseinsensitive)
			$pageNameEncode = urlencode($row['pageName']);

			// Limit memory usage of the page cache.  No 
			// intelligence is attempted here whatsoever.  This was 
			// done because a few thousand ((page)) links would blow 
			// up memory, even with the limit at 128MiB.  
			// Information on 128 pages really should be plenty.
			while( count($this->cache_page_info) >= 128 )
			{
				// Need to delete something; pick at random
				$keys=array_keys($this->cache_page_info);
				$num=rand(0,count($keys));
				if (isset($keys[$num])) {
					unset($this->cache_page_info[$keys[$num]]);
				}
			}

			$this->cache_page_info[$pageNameEncode] = $row;
			return $this->cache_page_info[$pageNameEncode];
		}
	}

	function get_page_info_from_id($page_id) {
		$query = "select * from `tiki_pages` where `page_id`=?";
		$result = $this->query($query, array($page_id));

		if (!$result->numRows())
			return false;
		else
			return $result->fetchRow();
	}


	function get_page_name_from_id($page_id) {
		$query = "select `pageName`  from `tiki_pages` where `page_id`=?";
		return $this->getOne($query, array((int)$page_id));
	}

	function get_page_id_from_name($page) {
		$query = "select `page_id` from `tiki_pages` where `pageName`=?";
		return $this->getOne($query, array($page));
	}

	function how_many_at_start($str, $car) {
		$cant = 0;
		$i = 0;
		while (($i < strlen($str)) && (isset($str{$i})) && ($str{$i}== $car)) {
			$i++;
			$cant++;
		}
		return $cant;
	}

	function parse_data_raw($data) {
		$data = $this->parse_data($data);
		$data = str_replace("tiki-index", "tiki-index_raw", $data);
		return $data;
	}

	function add_pre_handler($name) {
		if (!in_array($name, $this->pre_handlers)) {
			$this->pre_handlers[] = $name;
		}
	}

	function add_pos_handler($name) {
		if (!in_array($name, $this->pos_handlers)) {
			$this->pos_handlers[] = $name;
		}
	}

	// add a post edit filter which is called when a wiki page is edited and before
	// it is committed to the database (see tiki-handlers.php on its usage)
	function add_postedit_handler($name)
	{
		if(!in_array($name,$this->postedit_handlers)) {
			$this->postedit_handlers[]=$name;
		}
	}

	// apply all the post edit handlers to the wiki page data
	function apply_postedit_handlers($data) {
		// Process editpage_handlers here
		foreach($this->postedit_handlers as $handler) {
			$data = $handler($data);
		}
		return $data;
	}

	// This function handles wiki codes for those special HTML characters
	// that textarea won't leave alone.
	function parse_htmlchar(&$data) {
		// cleaning some user input
		$data = preg_replace('/&(?![a-z]+;|#\d+;)/i', '&amp;', $data);

		// oft-used characters (case insensitive)
		$data = preg_replace("/~bs~/i", "&#92;", $data);
		$data = preg_replace("/~hs~/i", "&nbsp;", $data);
		$data = preg_replace("/~amp~/i", "&amp;", $data);
		$data = preg_replace("/~ldq~/i", "&ldquo;", $data);
		$data = preg_replace("/~rdq~/i", "&rdquo;", $data);
		$data = preg_replace("/~lsq~/i", "&lsquo;", $data);
		$data = preg_replace("/~rsq~/i", "&rsquo;", $data);
		$data = preg_replace("/~c~/i", "&copy;", $data);
		$data = preg_replace("/~--~/", "&mdash;", $data);
		$data = preg_replace("/ -- /", " &mdash; ", $data);
		$data = preg_replace("/~lt~/i", "&lt;", $data);
		$data = preg_replace("/~gt~/i", "&gt;", $data);

		// HTML numeric character entities
		$data = preg_replace("/~([0-9]+)~/", "&#$1;", $data);
	}

	// Reverses parse_first.
	function replace_preparse(&$data, &$preparsed, &$noparsed) {
		$data1 = $data;
		$data2 = "";

		// Cook until done.  Handles nested cases.
		while( $data1 != $data2 ) {
			$data1 = $data;
			if (isset($noparsed["key"]) and count($noparsed["key"]) and count($noparsed["key"]) == count($noparsed["data"])) {
				$data = str_replace($noparsed["key"], $noparsed["data"], $data);
			}

			if (isset($preparsed["key"]) and count($preparsed["key"]) and count($preparsed["key"]) == count($preparsed["data"])) {
				$data = str_replace($preparsed["key"], $preparsed["data"], $data);
			}
			$data2 = $data;
		}
	}

	function plugin_match(&$data, &$plugins) {
		global $pluginskiplist;
		if( !is_array( $pluginskiplist ) )
			$pluginskiplist = array();

		$matcher_fake = array("~pp~","~np~","&lt;pre&gt;");
		$matcher = "/\{([A-Z0-9_]+) *\(|\{([a-z]+)(\s|\})|~pp~|~np~|&lt;[pP][rR][eE]&gt;/";

		$plugins = array();
		preg_match_all( $matcher, $data, $tmp, PREG_SET_ORDER );
		foreach ( $tmp as $p ) {
			if ( in_array(strtolower($p[0]), $matcher_fake)
				|| ( isset($p[1]) && ( in_array($p[1], $matcher_fake) || $this->plugin_exists($p[1]) ) )
				|| ( isset($p[2]) && ( in_array($p[2], $matcher_fake) || $this->plugin_exists($p[2]) ) )
			) {
				$plugins = $p;
				break;
			}
		}

		// Check to make sure there was a match.
		if( count( $plugins ) > 0 && count( $plugins[0] )  > 0 ) {
			$pos = 0;
			while( in_array( $plugins[0], $pluginskiplist ) ) {
				$pos = strpos( $data, $plugins[0], $pos ) + 1;
				if( ! preg_match( $matcher, substr($data, $pos), $plugins ) )
					return;
			}

			// If it is a true plugin
			if( $plugins[0]{0} == "{" ) {
				$pos = strpos( $data, $plugins[0] ); // where plugin starts
				$pos_end = $pos+strlen($plugins[0]); // where character after ( is

				// Here we're going to look for the end of the arguments for the plugin.

				$i = $pos_end;
				$last_data = strlen($data);

				// We start with one open curly brace, and one open paren.
				$curlies = 1;

				// If model with (
				if( strlen( $plugins[1] ) ) {
					$parens = 1;
					$plugins['type'] = 'long';
				} else {
					$parens = 0;
					$plugins[1] = $plugins[2];
					unset($plugins[3]);
					$plugins['type'] = 'short';
				}

				// While we're not at the end of the string, and we still haven't found both closers
				while( $i < $last_data ) {
					$char = substr($data, $i, 1);
					//print "<pre>Data char: $i, $char, $curlies, $parens\n.</pre>\n";
					if( $char == "{" ) {
						$curlies++;
					} elseif( $char == "(" && $plugins['type'] == 'long' ) {
						$parens++;
					} elseif( $char == "}" ) {
						$curlies--;
						if( $plugins['type'] == 'short' )
							$lastParens = $i;
					} elseif( $char == ")"  && $plugins['type'] == 'long' ) {
						$parens--;
						$lastParens = $i;
					}

					// If we found the end of the match...
					if( $curlies == 0 && $parens == 0 ) {
						break;
					}

					$i++;
				}

				if( $curlies == 0 && $parens == 0 ) {
					$plugins[2] = (string) substr($data, $pos_end, $lastParens - $pos_end);
					$plugins[0] = $plugins[0] . (string) substr($data, $pos_end, $i - $pos_end + 1);
					/*
						 print "<pre>Match found: ";
						 print( $plugins[2] );
						 print "</pre>";
					 */
				}

				$plugins['arguments'] = isset($plugins[2]) ? $this->plugin_split_args( $plugins[2] ) : array();
			} else {
				$plugins[1] = $plugins[0];
				$plugins[2] = "";
			}
		}

		/*
			 print "<pre>Plugin match end:";
			 print_r( $plugins );
			 print "</pre>";
		 */
	}

	function plugin_split_args( $params_string ) {
		require_once 'WikiParser/PluginArgumentParser.php';
		$parser = new WikiParser_PluginArgumentParser;

		return $parser->parse( $params_string );
	}
	// get all the plugins of a text- can be limitted only to some
	function getPlugins($data, $only=null) {
		$plugins = array();
		for (; ;) {
			$this->plugin_match($data, $plugin);
			if (empty($plugin)) {
				break;
			}
			if (empty($only) || in_array($plugin[1], $only) || in_array(strtoupper($plugin[1]), $only) || in_array(strtolower($plugin[1]), $only)) {
				$plugins[] = $plugin;
			}
			$pos = strpos( $data, $plugin[0] );
			$data = substr_replace($data, '', $pos, strlen($plugin[0]));
			}
		return $plugins;
	}
	// This recursive function handles pre- and no-parse sections and plugins
	function parse_first(&$data, &$preparsed, &$noparsed, $options=null, $real_start_diff='0') {
		global $dbTiki, $smarty, $tiki_p_edit, $prefs, $pluginskiplist;
		if( ! is_array( $pluginskiplist ) )
			$pluginskiplist = array();

		require_once 'WikiParser/PluginMatcher.php';
		require_once 'WikiParser/PluginArgumentParser.php';
		$matches = WikiParser_PluginMatcher::match($this->htmldecode($data));
		$argumentParser = new WikiParser_PluginArgumentParser;

		if (!isset($options['parseimgonly'])) {
			$options['parseimgonly'] = false;
		}

		foreach ($matches as $match) {
			if ($options['parseimgonly'] && $this->getName() != 'img') {
				continue;
			}

			$plugin_name = $match->getName();
			$plugin_data = $match->getBody();
			$arguments = $argumentParser->parse($match->getArguments());
			$start = $match->getStart();

			$pluginOutput = null;
			if( $this->plugin_enabled( $plugin_name, $pluginOutput ) ) {

				static $plugin_indexes = array();

				if( ! array_key_exists( $plugin_name, $plugin_indexes ) )
					$plugin_indexes[$plugin_name] = 0;

				$current_index = ++$plugin_indexes[$plugin_name];

				// get info to test for preview with auto_save
				$status = $this->plugin_can_execute( $plugin_name, $plugin_data, $arguments, $options['preview_mode'] || $options['ck_editor'] );
				global $tiki_p_plugin_viewdetail, $tiki_p_plugin_preview, $tiki_p_plugin_approve;
				$details = $tiki_p_plugin_viewdetail == 'y' && $status != 'rejected';
				$preview = $tiki_p_plugin_preview == 'y' && $details && ! $options['preview_mode'];
				$approve = $tiki_p_plugin_approve == 'y' && $details && ! $options['preview_mode'];
							
				if( $status === true || ($tiki_p_plugin_preview == 'y' && $details && $options['preview_mode'] && $prefs['ajax_autosave'] === 'y') ) {
					if (isset($options['stripplugins']) && $options['stripplugins']) {
						$ret = $plugin_data;
					} else {
						$ret = $this->plugin_execute( $plugin_name, $plugin_data, $arguments, $start, false, $options);
					}
				} else {

					if( $status != 'rejected' ) {
						$smarty->assign( 'plugin_fingerprint', $status );
						$status = 'pending';
					}

					if ($options['ck_editor']) {
						$ret = $this->convert_plugin_for_ckeditor( $plugin_name, $arguments, tra('Plugin execution pending approval'), $plugin_data, array('icon' => 'pics/icons/error.png') );
					} else {
						$smarty->assign( 'plugin_name', $plugin_name );
						$smarty->assign( 'plugin_index', $current_index );

						$smarty->assign( 'plugin_status', $status );
						$smarty->assign( 'plugin_details', $details );
						$smarty->assign( 'plugin_preview', $preview );
						$smarty->assign( 'plugin_approve', $approve );

						$smarty->assign( 'plugin_body', $plugin_data );
						$smarty->assign( 'plugin_args', $arguments );

						$ret = '~np~' . $smarty->fetch('tiki-plugin_blocked.tpl') . '~/np~';
					}
				}
			} else {
				$ret = $pluginOutput->toWiki();
			}

			if ($ret === false) {
				continue;
			}

			global $headerlib;
			$headerlib->add_jsfile( 'tiki-jsplugin.php?language='.$prefs['language'], 'dynamic' );
			if( $this->plugin_is_editable( $plugin_name ) && (empty($options['preview_mode']) || !$options['preview_mode']) && (empty($options['print']) || !$options['print']) && !$options['suppress_icons'] ) {
				include_once('lib/smarty_tiki/function.icon.php');
				global $page;
				$id = 'plugin-edit-' . $plugin_name . $current_index;
		
				$headerlib->add_js( "
\$(document).ready( function() {
if( \$('#$id') ) {
\$('#$id').click( function(event) {
	popup_plugin_form("
		. json_encode('editwiki')
		. ', '
		. json_encode($plugin_name) 
		. ', ' 
		. json_encode($current_index) 
		. ', ' 
		. json_encode($page) 
		. ', ' 
		. json_encode($arguments) 
		. ', ' 
		. json_encode(TikiLib::htmldecode($plugin_data)) 
		. ", event.target);
} );
}
} );
" );

				$iconDisplayStyle = '';
				if ($prefs['wiki_edit_icons_toggle'] == 'y' && ($prefs['wiki_edit_plugin'] == 'y' || $prefs['wiki_edit_section'] == 'y')) {
					if (!isset($_COOKIE['wiki_plugin_edit_view'])) {
						$iconDisplayStyle = ' style="display:none;"';
					}
				}

				$ret = $ret.'~np~<a id="' .$id. '" href="javascript:void(1)" class="editplugin"'.$iconDisplayStyle.'>'.smarty_function_icon(array('_id'=>'wiki_plugin_edit', 'alt'=>tra('Edit Plugin').':'.$plugin_name), $smarty)."</a>~/np~";
			}

			// End plugin handling

			$match->replaceWith($ret);
		}

		$data = $matches->getText();

		while (false !== $start = strpos($data, '~np~')) {
			$end = strpos($data, '~/np~', $start);
			$content = substr($data, $start + 4, $end - $start - 4);

			// ~pp~ type "plugins"
			$key = "§".md5($this->genPass())."§";
			$noparsed["key"][] = preg_quote($key);
			$noparsed["data"][] = $content;

			$data = substr($data, 0, $start) . $key . substr($data, $end + 5);
		}

		// ~pp~
		while (false !== $start = strpos($data, '~pp~')) {
			$end = strpos($data, '~/pp~', $start);
			$content = substr($data, $start + 4, $end - $start - 4);

			// ~pp~ type "plugins"
			$key = "§".md5($this->genPass())."§";
			$noparsed["key"][] = preg_quote($key);
			$noparsed["data"][] = '<pre>'.$content.'</pre>';

			$data = substr($data, 0, $start) . $key . substr($data, $end + 5);
		}	}

	function plugin_get_list( $includeReal = true, $includeAlias = true ) {
		$real = array();
		$alias = array();

		foreach( glob( 'lib/wiki-plugins/wikiplugin_*.php' ) as $file )
		{
			$base = basename( $file );
			$plugin = substr( $base, 11, -4 );

			$real[] = $plugin;
		}

		global $prefs;
		if( isset($prefs['pluginaliaslist']) ) {
			$alias = @unserialize($prefs['pluginaliaslist']);
			$alias = array_filter($alias);
		}

		if( $includeReal && $includeAlias )
			$plugins = array_merge( $real, $alias );
		elseif( $includeReal )
			$plugins = $real;
		elseif( $includeAlias )
			$plugins = $alias;
		else
			$plugins = array();

		sort(array_filter($plugins));
		
		return $plugins;
	}

	function plugin_exists( $name, $include = false ) {
		$php_name = 'lib/wiki-plugins/wikiplugin_';
		$php_name .= strtolower($name) . '.php';

		$exists = file_exists( $php_name );

		if( $include && $exists )
			include_once $php_name;

		if( $exists )
			return true;
		elseif( $info = $this->plugin_alias_info( $name ) ) {
			// Make sure the underlying implementation exists

			return $this->plugin_exists( $info['implementation'], $include );
		}
	}

	function plugin_info( $name ) {
		static $known = array();

		if( isset( $known[$name] ) ) {
			return $known[$name];
		}

		if( ! $this->plugin_exists( $name, true ) )
			return $known[$name] = false;

		$func_name_info = "wikiplugin_{$name}_info";

		if( ! function_exists( $func_name_info ) ) {
			if( $info = $this->plugin_alias_info( $name ) )
				return $known[$name] = $info['description'];
			else
				return $known[$name] = false;
		}

		return $known[$name] = $func_name_info();
	}

	function plugin_alias_info( $name ) {
		global $prefs;
		$name = strtolower($name);
		$prefName = "pluginalias_$name";

		if( ! isset( $prefs[$prefName] ) )
			return false;

		return @unserialize( $prefs[$prefName] );
	}

	function plugin_alias_store( $name, $data ) {
		/*
			Input data structure:
			
			implementation: other plugin_name
			description:
				** Equivalent of plugin info function here **
			body:
				input: use|ignore
				default: body content to use
				params:
					token_name:
						input: token_name, default uses same name above
						default: value to use if missing
						encoding: none|html|url - default to none
			params:
				; Use input parameter directly
				token_name: default value

				; Custom input parameter replacement
				token_name:
					pattern: body content to use
					params:
						token_name:
							input: token_name, default uses same name above
							default: value to use if missing
							encoding: none|html|url - default to none
		*/
		if (empty($name)) {
			return;
		}

		$name = strtolower( $name );
		$data['plugin_name'] = $name;

		$prefName = "pluginalias_$name";
		$this->set_preference( $prefName, serialize( $data ) );
		
		global $prefs;
		$list = array();
		if( isset($prefs['pluginaliaslist']) )
			$list = unserialize($prefs['pluginaliaslist']);
		
		if( ! in_array( $name, $list ) ) {
			$list[] = $name;
			$this->set_preference( 'pluginaliaslist', serialize($list) );
		}

		foreach( glob( 'temp/cache/wikiplugin_*' ) as $file )
			unlink( $file );

		global $cachelib;
		$cachelib->invalidate('plugindesc');
	}

	function plugin_alias_delete( $name ) {
		$name = strtolower( $name );
		$prefName = "pluginalias_$name";

		// Remove from list
		$list = $this->get_preference( 'pluginaliaslist', array(), true );
		$list = array_diff( $list, array( $name ) );
		$this->set_preference( 'pluginaliaslist', serialize($list) );

		// Remove the definition
		$this->delete_preference( $prefName );

		// Clear cache
		global $cachelib;
		$cachelib->invalidate('plugindesc');
		foreach( glob( 'temp/cache/wikiplugin_*' ) as $file )
			unlink( $file );
	}

	function plugin_enabled( $name, & $output ) {
		if( ! $meta = $this->plugin_info( $name ) )
			return true; // Legacy plugins always execute

		global $prefs;

		$missing = array();

		if( isset( $meta['prefs'] ) )
			foreach( $meta['prefs'] as $pref )
				if( $prefs[$pref] != 'y' )
					$missing[] = $pref;
		
		if( count( $missing ) > 0 ) {
			require_once 'lib/core/WikiParser/PluginOutput.php';
			$output = WikiParser_PluginOutput::disabled( $name, $missing );
			return false;
		}

		return true;
	}

	function plugin_is_inline( $name ) {
		if( ! $meta = $this->plugin_info( $name ) )
			return true; // Legacy plugins always inline 

		global $prefs;

		$inline = false;
		if( isset( $meta['inline'] ) && $meta['inline'] ) 
			return true; 

		$inline_pref = 'wikiplugininline_' .  $name;
		if( isset( $prefs[ $inline_pref ] ) && $prefs[ $inline_pref ] == 'y' )
			return true;	

		return false;
	}

	/**
	 * Check if possible to execute a plugin
	 * 
	 * @param string $name
	 * @param string $data
	 * @param array $args
	 * @param bool $dont_modify
	 * @return bool|string Boolean true if can execute, string 'rejected' if can't execute and plugin fingerprint if pending
	 */
	function plugin_can_execute( $name, $data = '', $args = array(), $dont_modify = false ) {
		global $prefs;

		// If validation is disabled, anything can execute
		if( $prefs['wiki_validate_plugin'] != 'y' )
			return true;

		$meta = $this->plugin_info( $name );
		if( ! isset( $meta['validate'] ) )
			return true;

		$fingerprint = $this->plugin_fingerprint( $name, $meta, $data, $args );

		$val = $this->plugin_fingerprint_check( $fingerprint, $dont_modify );
		if( strpos( $val, 'accept' ) === 0 )
			return true;
		elseif( strpos( $val, 'reject' ) === 0 )
			return 'rejected';
		else {
			global $tiki_p_plugin_approve, $tiki_p_plugin_preview, $user;
			if( 
				isset($_SERVER['REQUEST_METHOD'])
				&& $_SERVER['REQUEST_METHOD'] == 'POST'
				&& isset( $_POST['plugin_fingerprint'] ) 
				&& $_POST['plugin_fingerprint'] == $fingerprint
			) {
				if( $tiki_p_plugin_approve == 'y' ) {
					if( isset( $_POST['plugin_accept'] ) ) {
						global $page;
						$this->plugin_fingerprint_store( $fingerprint, 'accept' );
						$this->invalidate_cache( $page );
						return true;
					} elseif( isset( $_POST['plugin_reject'] ) ) {
						global $page;
						$this->plugin_fingerprint_store( $fingerprint, 'reject' );
						$this->invalidate_cache( $page );
						return 'rejected';
					}
				} 

				if( $tiki_p_plugin_preview == 'y' 
					&& isset( $_POST['plugin_preview'] ) ) {
					return true;
				}
			}

			return $fingerprint;
		}
	}

	function plugin_fingerprint_check( $fp, $dont_modify = false ) {
		global $user;
		$limit = date( 'Y-m-d H:i:s', time() - 15*24*3600 );
		$result = $this->query( "SELECT `status`, IF(`status`='pending' AND `last_update` < ?, 'old', '') flag FROM `tiki_plugin_security` WHERE `fingerprint` = ?",
			array( $limit, $fp ) );

		$needUpdate = false;

		if( $row = $result->fetchRow() ) {
			$status = $row['status'];
			$flag = $row['flag'];

			if( $status == 'accept' || $status == 'reject' )
				return $status;

			if( $flag == 'old' )
				$needUpdate = true;
		} else {
			$needUpdate = true;
		}

		if( $needUpdate && !$dont_modify ) {
			global $page;
			if( $page ) {
				$objectType = 'wiki page';
				$objectId = $page;
			} else {
				$objectType = '';
				$objectId = '';
			}

			if (!$user) {
				$user = tra('Anonymous');
			}
			$this->query( "DELETE FROM `tiki_plugin_security` WHERE `fingerprint` = ?", array( $fp ) );

			$pluginSecurity = $this->table('tiki_plugin_security');
			$pluginSecurity->insert(array(
				'fingerprint' => $fp,
				'status' => 'pending',
				'added_by' => $user,
				'last_objectType' => $objectType,
				'last_objectId' => $objectId,
			));
		}

		return '';
	}

	function plugin_fingerprint_store( $fp, $type ) {
		global $prefs, $user, $page;
		if( $page ) {
			$objectType = 'wiki page';
			$objectId = $page;
		} else {
			$objectType = '';
			$objectId = '';
		}

		$this->query( "DELETE FROM `tiki_plugin_security` WHERE `fingerprint` = ?", array( $fp ) );

		$pluginSecurity = $this->table('tiki_plugin_security');
		$pluginSecurity->insert(array(
			'fingerprint' => $fp,
			'status' => $type,
			'added_by' => $user,
			'last_objectType' => $objectType,
			'last_objectId' => $objectId,
		));
	}

	function plugin_clear_fingerprint( $fp ) {
		$this->query( "DELETE FROM `tiki_plugin_security` WHERE `fingerprint` = ?", array( $fp ) );
	}

	function list_plugins_pending_approval() {
		return $this->fetchAll("SELECT `fingerprint`, `added_by`, `last_update`, `last_objectType`, `last_objectId` FROM `tiki_plugin_security` WHERE `status` = 'pending' ORDER BY `last_update` DESC");
	}

	function approve_all_pending_plugins() {
	// Update all pending plugins to accept
	$this->query("UPDATE `tiki_plugin_security` SET `status`='accept', `approval_by`='admin' WHERE `status`='pending'");  
	}

	function approve_selected_pending_plugings($fp) {
	// Update selected pending plugins to accept
	$this->query("UPDATE `tiki_plugin_security` SET `status`='accept', `approval_by`='admin' WHERE `fingerprint` = ?", array( $fp ));  
	}

	function plugin_fingerprint( $name, $meta, $data, $args ) {
		$validate = $meta['validate'];
		if( $validate == 'all' || $validate == 'body' )
			$validateBody = str_replace('<x>', '', $data);	// de-sanitize plugin body to make fingerprint consistant with 5.x
		else
			$validateBody = '';

		if( $validate == 'all' || $validate == 'arguments' ) {
			$validateArgs = $args;

			// Remove arguments marked as safe from the fingerprint
			foreach( $meta['params'] as $key => $info )
				if( isset( $validateArgs[$key] ) 
					&& isset( $info['safe'] ) 
					&& $info['safe']
				)
					unset( $validateArgs[$key] );

			// Parameter order needs to be stable
			ksort( $validateArgs );
		} else
			$validateArgs = array();

		$bodyLen = str_pad( strlen( $validateBody ), 6, '0', STR_PAD_RIGHT );
		$serialized = serialize( $validateArgs );
		$argsLen = str_pad( strlen( $serialized ), 6, '0', STR_PAD_RIGHT );

		$bodyHash = md5( $validateBody );
		$argsHash = md5( $serialized );

		return "$name-$bodyHash-$argsHash-$bodyLen-$argsLen";
	}

	function plugin_execute( $name, $data = '', $args = array(), $offset = 0, $validationPerformed = false, $parseOptions = array() ) {
		require_once 'lib/core/WikiParser/PluginOutput.php';

		global $prefs;
		$outputFormat = 'wiki';
		if( isset($parseOptions['context_format']) ) {
			$outputFormat = $parseOptions['context_format'];
		}

		if( ! $this->plugin_exists( $name, true ) ) {
			return false;
		}

		if( ! $validationPerformed && ! $this->plugin_enabled( $name, $output ) ) {
			return $this->convert_plugin_output( $output, '', $outputFormat, $parseOptions );
		}

		if (isset($parseOptions['inside_pretty']) && $parseOptions['inside_pretty'] === true) {
			global $trklib; require_once('lib/trackers/trackerlib.php');
			$trklib->replace_pretty_tracker_refs($args);
		}
		
		$func_name = 'wikiplugin_' . $name;
		
		if( ! $validationPerformed ) {
			$this->plugin_apply_filters( $name, $data, $args );
		}

		if( function_exists( $func_name ) ) {
			$pluginFormat = 'wiki';
			$info = $this->plugin_info( $name );
			if( isset( $info['format'] ) ) {
				$pluginFormat = $info['format'];
			}

			$output = $func_name( $data, $args, $offset, $parseOptions );

			$plugin_result =  $this->convert_plugin_output( $output, $pluginFormat, $outputFormat, $parseOptions );
			if (isset($parseOptions['ck_editor']) && $parseOptions['ck_editor']) {
				return $this->convert_plugin_for_ckeditor( $name, $args, $plugin_result, $data, $info );
			} else {
				return $plugin_result;
			}
		} elseif( $this->plugin_find_implementation( $name, $data, $args ) ) {
			return $this->plugin_execute( $name, $data, $args, $offset, $validationPerformed, $parseOptions );
		}
	}
	
	private function convert_plugin_for_ckeditor( $name, $args, $plugin_result, $data, $info = array() ) {
		$ck_editor_plugin = '{' . (empty($data) ? $name : strtoupper($name) . '(') . ' ';
		$arg_str = '';		// not using http_build_query() as it converts spaces into +
		if (!empty($args)) {
			foreach( $args as $argKey => $argValue ) {
				if (is_array($argValue)) {
					if (isset($info['params'][$argKey]['separator'])) { $sep = $info['params'][$argKey]['separator']; } else { $sep = ','; }
					$ck_editor_plugin .= $argKey.'="'.implode($sep, $argValue).'" ';	// process array
					$arg_str .= $argKey.'='.implode($sep, $argValue).'&';
				} else {
					$ck_editor_plugin .= $argKey.'="'.$argValue.'" ';
					$arg_str .= $argKey.'='.$argValue.'&';
				}
			}
		}
		if (substr($ck_editor_plugin, -1) === ' ') {
			$ck_editor_plugin = substr($ck_editor_plugin, 0, -1);
		}
		if (!empty($data)) {
			$ck_editor_plugin .= ')}' . $data . '{' . strtoupper($name) . '}';
		} else {
			$ck_editor_plugin .= '}';
		}
		// work out if I'm a nested plugin and return empty if so
		$stack = debug_backtrace(true);
		$plugin_nest_level = 0;
		foreach ($stack as $st) {
			if ($st['function'] === 'parse_first') {
				$plugin_nest_level ++;
				if ($plugin_nest_level > 1) {
					return '';
				}
			}
		}
		$arg_str = rtrim($arg_str, '&');
		$icon = isset($info['icon']) ? $info['icon'] : 'pics/icons/wiki_plugin_edit.png';

		// some plugins are just too flakey to do wysiwyg, so show the "source" for them ;(
		if (in_array($name, array('trackerlist', 'kaltura', 'toc', 'freetagged'))) {
			$plugin_result = str_replace(array('{', '}'), array('%7B' , '%7D'), $ck_editor_plugin);
		} else {
			// pre-parse the output so nested plugins don't fall out all over the place
			$plugin_result = $this->parse_data($plugin_result, array('is_html' => false, 'suppress_icons' => true, 'ck_editor' => true, 'noparseplugins' => true));
			// remove hrefs and onclicks
			$plugin_result = preg_replace('/\shref\=/i', ' tiki_href=', $plugin_result);
			$plugin_result = preg_replace('/\sonclick\=/i', ' tiki_onclick=', $plugin_result);
			$plugin_result = preg_replace('/<script.*?<\/script>/mi', '', $plugin_result);
		}
		if (!in_array($name, array('html'))) {		// remove <p> and <br>s from non-html
			$data = str_replace(array('<br />', '<p>', '</p>', "\t"), '', $data);
		}
		
		if ($this->contains_html_block($plugin_result)) {
			$elem = 'div';
		} else {
			$elem = 'span';
		}
		$elem_style = 'position:relative;';
		if (in_array($name, array('img', 'div')) && preg_match('/<'.$name.'[^>]*style="(.*?)"/i', $plugin_result, $m)) {
			if (count($m)) {
				$elem_style .= $m[1];
			}
		}
		$ret = '~np~<'.$elem.' class="tiki_plugin" plugin="' . $name . '" style="' . $elem_style . '"' .
				' syntax="' . htmlentities( $ck_editor_plugin, ENT_QUOTES, 'UTF-8' ) . '"' .
				' args="' . htmlentities($arg_str, ENT_QUOTES, 'UTF-8') . '"' .
				' body="' . htmlentities( $data, ENT_QUOTES, 'UTF-8') . '">'.	// not <!--{cke_protected}
				'<img src="'.$icon.'" width="16" height="16" style="float:left;position:absolute;z-index:10001" />' .
				$plugin_result.'<!-- end tiki_plugin --></'.$elem.'>~/np~';
		
		return 	$ret;
	}

	private function plugin_apply_filters( $name, & $data, & $args ) {
		$info = $this->plugin_info( $name );
		$default = TikiFilter::get( isset( $info['defaultfilter'] ) ? $info['defaultfilter'] : 'xss');

		// Apply filters on the body
		$filter = isset($info['filter']) ? TikiFilter::get($info['filter']) : $default;
		$data = $this->htmldecode($data);
		$data = $filter->filter($data);

		// Make sure all arguments are declared
		$params = $info['params'];

		if( ! isset( $info['extraparams'] ) && is_array($params) ) {
			$args = array_intersect_key( $args, $params );
		}

		// Apply filters on values individually
		if (!empty($args)) {
			foreach( $args as $argKey => &$argValue ) {
				if (!isset($params[$argKey])) {
					continue;// extra params
				}
				$paramInfo = $params[$argKey];
				$filter = isset($paramInfo['filter']) ? TikiFilter::get($paramInfo['filter']) : $default;
				$argValue = $this->htmldecode($argValue);

				if( isset($paramInfo['separator']) ) {
					$vals = array();

					foreach( explode( $paramInfo['separator'], $argValue ) as $val ) {
						$vals[] = $filter->filter($val);
					}

					$vals = array_map( 'trim', $vals );
					//$vals = array_filter( $vals );

					$argValue = array_values( $vals );
				} else {
					$argValue = $filter->filter($argValue);
				}
			}
		}
	}
	
	private function convert_plugin_output( $output, $from, $to, $parseOptions ) {
		if( ! $output instanceof WikiParser_PluginOutput ) {
			if( $from === 'wiki' ) {
				$output = WikiParser_PluginOutput::wiki( $output );
			} elseif( $from === 'html' ) {
				$output = WikiParser_PluginOutput::html( $output );
			}
		}

		if( $to === 'html' ) {
			return $output->toHtml( $parseOptions );
		} elseif( $to === 'wiki' ) {
			return $output->toWiki();
		}
	}

	function plugin_replace_args( $content, $rules, $args ) {
		$patterns = array();
		$replacements = array();

		foreach( $rules as $token => $info ) {
			$patterns[] = "%$token%";
			if( isset( $info['input'] ) && ! empty( $info['input'] ) )
				$token = $info['input'];

			if( isset( $args[$token] ) ) {
				$value = $args[$token];
			} else {
				$value = isset($info['default']) ? $info['default'] : '';
			}

			switch( isset($info['encoding']) ? $info['encoding'] : 'none' )
			{
			case 'html': $replacements[] = htmlentities( $value, ENT_QUOTES, 'UTF-8' ); break;
			case 'url': $replacements[] = rawurlencode( $value ); break;
			default: $replacements[] = $value;
			}
		}

		return str_replace( $patterns, $replacements, $content );
	}

	function plugin_is_editable( $name ) {
		global $tiki_p_edit, $prefs, $section;
		$info = $this->plugin_info( $name );
		// note that for 3.0 the plugin editor only works in wiki pages, but could be extended later
		return $section == 'wiki page' && $info && $tiki_p_edit == 'y' && $prefs['wiki_edit_plugin'] == 'y'
			&& !$this->plugin_is_inline( $name ) ;
	}

	function quotesplit( $splitter = ',', $repl_string = '' ) {
		$matches = preg_match_all( '/"[^"]*"/', $repl_string, $quotes );

		$quote_keys = array();
		if( $matches ) {
			foreach( array_unique( $quotes ) as $quote ) {
				$key = '§'.md5( $this->genPass() ).'§';
				$aux["key"] = $key;
				$aux["data"] = $quote;
				$quote_keys[] = $aux;
				$repl_string = str_replace( $quote[0], $key, $repl_string );
			}
		}

		$result = explode($splitter, $repl_string);

		if( $matches ) {
			// Loop through the result sections
			while(list($rarg, $rval) = each($result)) {
				// Replace all stored strings
				foreach( $quote_keys as $qval ) {
					$replacement = $qval["data"][0];
					$result[$rarg] = str_replace( $qval["key"], $replacement, $rval );
				}
			}
		}
		return $result;
	}


	// Replace hotwords in given line
	function replace_hotwords($line, $words) {
		global $prefs;
		$hotw_nw = ($prefs['feature_hotwords_nw'] == 'y') ? "target='_blank'" : '';

		// Replace Hotwords
		if ($prefs['feature_hotwords'] == 'y') {
			$sep =  " \n\t\r\,\;\(\)\.\:\[\]\{\}\!\?\"";
			foreach ($words as $word => $url) {
				// \b is a word boundary, \s is a space char
				$pregword = preg_replace("/\//","\/",$word);
				$line = preg_replace("/(=(\"|')[^\"']*[$sep'])$pregword([$sep][^\"']*(\"|'))/i","$1:::::$word,:::::$3",$line);
				$line = preg_replace("/([$sep']|^)$pregword($|[$sep])/i","$1<a class=\"wiki\" href=\"$url\" $hotw_nw>$word</a>$2",$line);
				$line = preg_replace("/:::::$pregword,:::::/i","$word",$line);
			}
		}
		return $line;
	}

	// Make plain text URIs in text into clickable hyperlinks
	function autolinks($text) {
		global $prefs, $smarty;
		//	check to see if autolinks is enabled before calling this function
		//		if ($prefs['feature_autolinks'] == "y") {
		$attrib = '';
		if ($prefs['popupLinks'] == 'y')
			$attrib .= 'target="_blank" ';
		if ($prefs['feature_wiki_ext_icon'] == 'y') {
			$attrib .= 'class="wiki external" ';
			include_once('lib/smarty_tiki/function.icon.php');
			$ext_icon = smarty_function_icon(array('_id'=>'external_link', 'alt'=>tra('(external link)'), '_class' => 'externallink', '_extension' => 'gif', '_defaultdir' => 'img/icons', 'width' => 15, 'height' => 14), $smarty);
									
		} else {
			$attrib .= 'class="wiki" ';
			$ext_icon = "";
		}

		// add a space so we can match links starting at the beginning of the first line
		$text = " " . $text;
		// match prefix://suffix, www.prefix.suffix/optionalpath, prefix@suffix
		$patterns = array();
		$replacements = array();
		$patterns[] = "#([\n ])([a-z0-9]+?)://([^<, \n\r]+)#i";
		$replacements[] = "\\1<a $attrib href=\"\\2://\\3\">\\2://\\3$ext_icon</a>";
		$patterns[] = "#([\n ])www\.([a-z0-9\-]+)\.([a-z0-9\-.\~]+)((?:/[^,< \n\r]*)?)#i";
		$replacements[] = "\\1<a $attrib href=\"http://www.\\2.\\3\\4\">www.\\2.\\3\\4$ext_icon</a>";
		$patterns[] = "#([\n ])([a-z0-9\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i";
		if ($prefs['feature_wiki_protect_email'] == 'y')
			$replacements[] = "\\1" . $this->protect_email("\\2", "\\3");
		else
			$replacements[] = "\\1<a class='wiki' href=\"mailto:\\2@\\3\">\\2@\\3</a>";
		$patterns[] = "#([\n ])magnet\:\?([^,< \n\r]+)#i";
		$replacements[] = "\\1<a class='wiki' href=\"magnet:?\\2\">magnet:?\\2</a>";
		$text = preg_replace($patterns, $replacements, $text);
		// strip the space we added
		$text = substr($text, 1);
		return $text;

		//		} else {
		//			return $text;
		//		}
	}

	function protect_email($name, $domain, $sep = '@') {
		$sep_encode = $mt_encode = '';
		for ($x=0, $csep = strlen($sep); $x < $csep ; $x++) {
			$sep_encode .= '%' . bin2hex($sep[$x]);
		}
		$mt = "mailto:";
		for ($x=0, $cmt = strlen($mt); $x < $cmt ; $x++) {
			$mt_encode .= '%' . bin2hex($mt[$x]);
		}
		return "<script language=\"Javascript\" type=\"text/javascript\">document.write('<a class=\"wiki\" href=\"'+unescape('$mt_encode')+'$name'+unescape('$sep_encode')+'$domain\">$name'+unescape('$sep_encode')+'$domain</a>');</script><noscript>$name ".tra("at","",true)." $domain</noscript>";
	}
	
	//Updates a dynamic variable found in some object
	/*Shared*/
	function update_dynamic_variable($name,$value, $lang = null) {
		$dynamicVariables = $this->table('tiki_dynamic_variables');
		$dynamicVariables->delete(array(
			'name' => $name,
			'lang' => $lang,
		));
		$dynamicVariables->insert(array(
			'name' => $name,
			'data' => $value,
			'lang' => $lang,
		));
		return true;
	}

	// split string into a list of
	function split_tag( $string, $cleanup = TRUE ) {
		$_splts = explode('&quot;', $string);
		$inside = FALSE;
		$parts = array();
		$index=0;

		foreach ($_splts as $i)  {
			if ($cleanup) {
				$i = str_replace('}', '', $i);
				$i = str_replace('{', '', $i);
				$i = str_replace('\'', '', $i);
				$i = str_replace('"', '', $i);
				// IE silently removes null-byte html char \0, so let's remove it anyways
				$i = str_replace('\\0', '', $i);
			}

			if ($inside) {  // inside "foo bar" - append
				if ($index>0) {
					$parts[$index-1] .= $i;
				} else {    // else: first element (should never happen)
					$parts[] = $i;
				}
			} else {        //
				$_spl = explode(" ", $i);
				foreach($_spl as $j) {
					$parts[$index++] = $j;
				}
			}
			$inside = ! $inside;
		}
		return $parts;
	}

	function split_assoc_array($parts, $assoc) {
		//$assoc = array();
		foreach($parts as $part) {
			$res=array();
			$assoc[$part] = '';
			preg_match("/(\w+)\s*=\s*(.*)/", $part, $res);
			if ($res) {
				$assoc[$res[1]] = $res[2];
			}
		}
		return $assoc;
	}

	/**
	 * close_blocks - Close out open paragraph, lists, and div's
	 *
	 * During parse_data, information is kept on blocks of text (paragraphs, lists, divs)
	 * that need to be closed out. This function does that, rather than duplicating the
	 * code inline.
	 *
	 * @param	$data			- Output data
	 * @param	$in_paragraph		- TRUE if there is an open paragraph
	 * @param	$listbeg		- array of open list terminators
	 * @param	$divdepth		- array indicating how many div's are open
	 * @param	$close_paragraph	- TRUE if open paragraph should be closed.
	 * @param	$close_lists		- TRUE if open lists should be closed.
	 * @param	$close_divs		- TRUE if open div's should be closed.
	 */
	/* private */
	function close_blocks(&$data, &$in_paragraph, &$listbeg, &$divdepth, $close_paragraph, $close_lists, $close_divs) {

		$closed = 0;	// Set to non-zero if something has been closed out
		// Close the paragraph if inside one.
		if ($close_paragraph && $in_paragraph) {
			$data .= "</p>\n";
			$in_paragraph = 0;
			$closed++;
		}
		// Close open lists
		if ($close_lists) {
			while (count($listbeg)) {
				$data .= array_shift($listbeg);
				$closed++;
			}
		}

		// Close open divs
		if ($close_divs) {
			$temp_max = count($divdepth);
			for ($i = 1; $i <= $temp_max; $i++) {
				$data .= '</div>';
				$closed++;
			}
		}

		return $closed;
	}

	//PARSEDATA
	// options defaults : is_html => false, absolute_links => false, language => ''
	function parse_data($data, $options = null) {
		// Don't bother if there's nothing...
		if (function_exists('mb_strlen')) {
			if( mb_strlen( $data ) < 1 ) {
				return;
			}
		}

		global $page_regex, $slidemode, $prefs, $ownurl_father, $tiki_p_upload_picture, $page, $page_ref_id, $rsslib, $dbTiki, $structlib, $user, $tikidomain, $tikiroot;
		global $wikilib; include_once('lib/wiki/wikilib.php');

		// Handle parsing options
		if ( $options == null ) $options = array();
		$options['is_html'] = $is_html = isset($options['is_html']) ? $options['is_html'] : false;
		$options['absolute_links'] = $absolute_links = isset($options['absolute_links']) ? $options['absolute_links'] : false;
		$options['language'] = $language = isset($options['language']) ? $options['language'] : '';
		$options['noparseplugins'] = $noparseplugins = isset($options['noparseplugins']) ? $options['noparseplugins'] : false;
		$options['stripplugins'] = $stripplugins = isset($options['stripplugins']) ? $options['stripplugins'] : false;
		$options['noheaderinc'] = $noheaderinc = isset($options['noheaderinc']) ? $options['noheaderinc'] : false;
		$options['page'] = isset($options['page']) ? $options['page'] : $page;
		$options['print'] = isset($options['print']) ? $options['print'] : false;
		$options['parseimgonly'] = isset($options['parseimgonly']) ? $options['parseimgonly'] : false;
		$options['preview_mode'] = isset($options['preview_mode']) ? (bool)$options['preview_mode'] : false;
		$options['suppress_icons'] = isset($options['suppress_icons']) ? (bool)$options['suppress_icons'] : false;
		$options['parsetoc'] = isset($options['parsetoc']) ? (bool)$options['parsetoc'] : true;
		$options['inside_pretty'] = isset($options['inside_pretty']) ? $options['inside_pretty'] : false;
		$options['process_wiki_paragraphs'] = isset($options['process_wiki_paragraphs']) ? $options['process_wiki_paragraphs'] : true;
		
		if (empty($options['ck_editor'])) $options['ck_editor'] = false;
		
		$old_wysiwyg_parsing = null;
		if ($options['ck_editor']) {
			global $headerlib;
			$old_wysiwyg_parsing = $headerlib->wysiwyg_parsing;
			$headerlib->wysiwyg_parsing = true;
		}
		// if simple_wiki is true, disable some wiki syntax
		// basically, allow wiki plugins, wiki links and almost
		// everything between {}
		$simple_wiki = false;
		if ($prefs['feature_wysiwyg'] == 'y' and $is_html) {
			if ($prefs['wysiwyg_wiki_semi_parsed'] == 'y') {
				$simple_wiki = true;
			} elseif ($prefs['wysiwyg_wiki_parsed'] == 'n') {
				return $data;
			}
		}

		$this->parse_wiki_argvariable($data, $options);

		/* <x> XSS Sanitization handling */

		// Converts &lt;x&gt; (<x> tag using HTML entities) into the tag <x>. This tag comes from the input sanitizer (XSS filter).
		// This is not HTML valid and avoids using <x> in a wiki text,
		//   but hide '<x>' text inside some words like 'style' that are considered as dangerous by the sanitizer.
		$data = str_replace( array( '&lt;x&gt;', '~np~', '~/np~' ), array( '<x>', ' ~np~', '~/np~ ' ), $data );

		// Fix false positive in wiki syntax
		//   It can't be done in the sanitizer, that can't know if the input will be wiki parsed or not
		$data = preg_replace('/(\{img [^\}]+li)<x>(nk[^\}]+\})/i', '\\1\\2', $data);

		// Process pre_handlers here
		if (is_array($this->pre_handlers)) {
			foreach ($this->pre_handlers as $handler) {
				$data = $handler($data);
			}
		}

		// Handle pre- and no-parse sections and plugins
		$preparsed = array('data'=>array(),'key'=>array());
		$noparsed = array('data'=>array(),'key'=>array());
		if (!$noparseplugins || $stripplugins) {
			$this->parse_first($data, $preparsed, $noparsed, $options);
			$this->parse_wiki_argvariable($data, $options);
		}

		// Handle |# anchor links by turning them into ALINK module calls.
		preg_match_all("/\(\(([^)]*\|#[^)]*)\)\)/", $data, $anchors);

		foreach( array_unique($anchors[1]) as $anchor_line ) {
			$parts1 = explode( "|#", $anchor_line );
			$anchor_page = "";
			$anchor_desc = "";
			$anchor = "";

			// Break out |desc bits from whatever section they happen to be in
			if( strpos( $parts1[0], "|" ) ) {
				$parts2 = explode( "|", $parts1[0] );
				$anchor_page = $parts2[0];
				$anchor_desc = $parts2[1];
				$anchor = $parts1[1];
			} elseif( strpos( $parts1[1], "|" ) ) {
				$parts2 = explode( "|", $parts1[1] );
				$anchor_page = $parts1[0];
				$anchor = $parts2[0];
				$anchor_desc = $parts2[1];
			} else {
				// No |desc bit
				$anchor_page = $parts1[0];
				$anchor_desc = $parts1[0];
				$anchor = $parts1[1];
			}

			if ( !$anchor_page && $options['page'] ) {
				$anchor_page = $options['page'];
				if ( !$anchor_desc )  $anchor_desc = $options['page'];
			}
			$repl = "{ALINK(pagename=>".$anchor_page.",aname=>".$anchor.")}".$anchor_desc."{ALINK}";
			$data = str_replace( "((".$anchor_line."))", $repl, $data);
		}

		if ($noparseplugins) {
			$data = preg_replace('/\{(.*?)\}/', '~np~{$1}~/np~', $data);
		}
		$this->parse_first($data, $preparsed, $noparsed, $options);
		
		// Handle ~pre~...~/pre~ sections
		$data = preg_replace(';~pre~(.*?)~/pre~;s', '<pre>$1</pre>', $data);

		// Strike-deleted text --text-- (but not in the context <!--[if IE]><--!> or <!--//--<!CDATA[//><!--
		if (!$simple_wiki) {
			// FIXME produces false positive for strings contining html comments. e.g: --some text<!-- comment -->
			$data = preg_replace("#(?<!<!|//)--([^\s>].+?)--#", "<del>$1</del>", $data);
		}

		// Handle comment sections
		$data = preg_replace(';~tc~(.*?)~/tc~;s', '', $data);
		$data = preg_replace(';~hc~(.*?)~/hc~;s', '<!-- $1 -->', $data);
		// Replace special characters
		// done after url catching because otherwise urls of dyn. sites will be modified
		// not done in wysiwyg mode, i.e. $prefs['feature_wysiwyg'] set to something other than 'no' or not set at all
		//			if (!$simple_wiki and $prefs['feature_wysiwyg'] == 'n') 
		//above line changed by mrisch - special functions were not parsing when wysiwyg is set but wysiswyg is not enabled
		// further changed by nkoth - why not parse in wysiwyg mode as well, otherwise it won't parse for display/preview?
		// must be done before color as we can have ~hs~~hs
		if (!$simple_wiki) {
			$this->parse_htmlchar($data);
		}

		if (!$simple_wiki) {
			// Replace colors ~~foreground[,background]:text~~
			// must be done before []as the description may contain color change
			$data = preg_replace("/\~\~([^\:\,]+)(,([^\:]+))?:(.*)\~\~/Ums", "<span style=\"color:$1; background:$3\">$4</span>", $data);
		}

		// Extract [link] sections (to be re-inserted later)
		$noparsedlinks = array();

		// This section matches [...].
		// Added handling for [[foo] sections.  -rlpowell
		if (!$simple_wiki) {
			preg_match_all("/(?<!\[)(\[[^\[][^\]]+\])/", $data, $noparseurl);

			foreach (array_unique($noparseurl[1])as $np) {
				$key = '§'.md5($this->genPass()).'§';

				$aux["key"] = $key;
				$aux["data"] = $np;
				$noparsedlinks[] = $aux;
				$data = preg_replace('/(^|[^a-zA-Z0-9])'.preg_quote($np,'/').'([^a-zA-Z0-9]|$)/', '\1'.$key.'\2', $data);
			}
		}

		// BiDi markers
		$bidiCount = 0;
		$bidiCount = preg_match_all("/(\{l2r\})/", $data, $pages);
		$bidiCount += preg_match_all("/(\{r2l\})/", $data, $pages);

		$data = preg_replace("/\{l2r\}/", "<div dir='ltr'>", $data);
		$data = preg_replace("/\{r2l\}/", "<div dir='rtl'>", $data);
		$data = preg_replace("/\{lm\}/", "&lrm;", $data);
		$data = preg_replace("/\{rm\}/", "&rlm;", $data);
		// smileys
		$data = $this->parse_smileys($data);

		// linebreaks using %%%
		$data = str_replace("%%%", "<br />", $data);

		$data = $this->parse_data_dynamic_variables( $data, $options['language'] );

		if (!$simple_wiki) {
			// Replace boxes
			$data = preg_replace("/\^([^\^]+)\^/", "<div class=\"simplebox\">$1</div>", $data);

			// Underlined text
			$data = preg_replace("/===(.+?)===/", "<span style=\"text-decoration:underline;\">$1</span>", $data);
			// Center text
			if ($prefs['feature_use_three_colon_centertag'] == 'y') {
				$data = preg_replace("/:::(.+?):::/", "<div style=\"text-align: center;\">$1</div>", $data);
			} else {
				$data = preg_replace("/::(.+?)::/", "<div style=\"text-align: center;\">$1</div>", $data);
			}
		}

		$data = $this->parse_data_wikilinks( $data, $simple_wiki );

		// reinsert hash-replaced links into page
		foreach ($noparsedlinks as $np) {
			$data = str_replace($np["key"], $np["data"], $data);
		}

		$data = $this->parse_data_externallinks( $data, $options );

		$data = $this->parse_data_tables( $data, $simple_wiki );

		if (!$simple_wiki && $options['parsetoc']) {
			$this->parse_data_process_maketoc( $data, $options, $noparsed);

		} else {
			$data = $this->parse_data_simple( $data );
		}

		// Close BiDi DIVs if any
		for ($i = 0; $i < $bidiCount; $i++) {
			$data .= "</div>";
		}

		// Put removed strings back.
		$this->replace_preparse($data, $preparsed, $noparsed);

		// Process pos_handlers here
		foreach ($this->pos_handlers as $handler) {
			$data = $handler($data);
		}
		if ($old_wysiwyg_parsing !== null) {
			$headerlib->wysiwyg_parsing = $old_wysiwyg_parsing;
		}
		return $data;
	}

	function parse_data_simple( $data ) {
		global $prefs;
		$words = array();

		if ( $prefs['feature_hotwords'] == 'y' ) {
			// Get list of HotWords
			$words = $this->get_hotwords();
		}

		$data = $this->parse_data_wikilinks( $data, true );
		$data = $this->parse_data_externallinks( $data, array( 'suppress_icons' => true ) );
		$data = $this->parse_data_inline_syntax( $data, $words );

		return $data;
	}

	private function parse_data_wikilinks( $data, $simple_wiki ) {
		global $page_regex, $prefs;

		// definitively put out the protected words ))protectedWord((
		if ($prefs['feature_wikiwords'] == 'y' ) {
			preg_match_all("/\)\)(\S+?)\(\(/", $data, $matches);
			$noParseWikiLinksK = array();
			$noParseWikiLinksT = array();
			foreach ($matches[0] as $mi=>$match) {
				do {
					$randNum = chr(0xff).rand(0, 1048576).chr(0xff);
				} while (strstr($data, $randNum));
				$data = str_replace($match, $randNum, $data);
				$noParseWikiLinksK[] = $randNum;
				$noParseWikiLinksT[] = $matches[1][$mi];
			}
		}

		// Links with description
		preg_match_all("/\(([a-z0-9-]+)?\(($page_regex)\|([^\)]*?)\)\)/", $data, $pages);

		$temp_max = count($pages[1]);
		for ($i = 0; $i < $temp_max; $i++) {
			$exactMatch = $pages[0][$i];
			$replacement = $this->get_wiki_link_replacement( $pages[2][$i], array( 
				'description' => $pages[6][$i], 
				'reltype' => $pages[1][$i] ) );

			$data = str_replace($exactMatch, $replacement, $data);
		}

		// Wiki page syntax without description
		preg_match_all("/\(([a-z0-9-]+)?\( *($page_regex) *\)\)/", $data, $pages);

		foreach ($pages[2] as $idx => $page_parse) {
			$exactMatch = $pages[0][$idx];
			$replacement = $this->get_wiki_link_replacement( $page_parse, array( 'reltype' => $pages[1][$idx] ) );

			$data = str_replace($exactMatch, $replacement, $data);
		}

		// Links to internal pages
		// If they are parenthesized then don't treat as links
		// Prevent ))PageName(( from being expanded \"\'
		//[A-Z][a-z0-9_\-]+[A-Z][a-z0-9_\-]+[A-Za-z0-9\-_]*
		if ( ! $simple_wiki && $prefs['feature_wiki'] == 'y' && $prefs['feature_wikiwords'] == 'y' ) {
			// The first part is now mandatory to prevent [Foo|MyPage] from being converted!
			if ($prefs['feature_wikiwords_usedash'] == 'y') {
				preg_match_all("/(?<=[ \n\t\r\,\;]|^)([A-Z][a-z0-9_\-\x80-\xFF]+[A-Z][a-z0-9_\-\x80-\xFF]+[A-Za-z0-9\-_\x80-\xFF]*)(?=$|[ \n\t\r\,\;\.])/", $data, $pages);
			} else {
				preg_match_all("/(?<=[ \n\t\r\,\;]|^)([A-Z][a-z0-9\x80-\xFF]+[A-Z][a-z0-9\x80-\xFF]+[A-Za-z0-9\x80-\xFF]*)(?=$|[ \n\t\r\,\;\.])/", $data, $pages);
			}
			//TODO to have a real utf8 Wikiword where the capitals can be a utf8 capital
			$words = ( $prefs['feature_hotwords'] == 'y' ) ? $this->get_hotwords() : array();
			foreach ( array_unique($pages[1]) as $page_parse ) {
				if ( ! array_key_exists($page_parse, $words) ) {
					$repl = $this->get_wiki_link_replacement( $page_parse, array(
						'plural' => $prefs['feature_wiki_plurals'] == 'y' ) );

					$data = preg_replace("/(?<=[ \n\t\r\,\;]|^)$page_parse(?=$|[ \n\t\r\,\;\.])/", "$1" . $repl . "$2", $data);
				}
			}
		}

		// Reinsert ))Words((
		if ($prefs['feature_wikiwords'] == 'y' ) {
			$data = str_replace($noParseWikiLinksK, $noParseWikiLinksT, $data);
		}

		return $data;
	}

	private function parse_data_externallinks( $data, $options ) {
		global $prefs;

		// *****
		// This section handles external links of the form [url] and such.
		// *****

		$links = $this->get_links($data);
		$notcachedlinks = $this->get_links_nocache($data);
		$cachedlinks = array_diff($links, $notcachedlinks);
		$this->cache_links($cachedlinks);

		// Note that there're links that are replaced
		foreach ($links as $link) {
			$target = '';
			$class = 'class="wiki"';
			$ext_icon = '';
			$rel='';

			if ($prefs['popupLinks'] == 'y') {
				$target = 'target="_blank"';
			}

			if (!isset($_SERVER['SERVER_NAME']) && isset($_SERVER['HTTP_HOST'])) {
				$_SERVER['SERVER_NAME'] = $_SERVER['HTTP_HOST'];
			}
			if (empty($_SERVER['SERVER_NAME']) || strstr($link, $_SERVER["SERVER_NAME"]) || !strstr($link, '://')) {
				$target = '';
			} else {
				$class = 'class="wiki external"';
				if ($prefs['feature_wiki_ext_icon'] == 'y' && !$options['suppress_icons']) {
					global $smarty;
					include_once('lib/smarty_tiki/function.icon.php');
					$ext_icon = smarty_function_icon(array('_id'=>'external_link', 'alt'=>tra('(external link)'), '_class' => 'externallink', '_extension' => 'gif', '_defaultdir' => 'img/icons', 'width' => 15, 'height' => 14), $smarty);
				}
				$rel='external';
				if ($prefs['feature_wiki_ext_rel_nofollow'] == 'y') {
					$rel .= ' nofollow';
				}
			}

			// The (?<!\[) stuff below is to give users an easy way to
			// enter square brackets in their output; things like [[foo]
			// get rendered as [foo]. -rlpowell

			if ($prefs['cachepages'] == 'y' && $this->is_cached($link)) {
				//use of urlencode for using cached versions of dynamic sites
				$cosa = "<a class=\"wikicache\" target=\"_blank\" href=\"tiki-view_cache.php?url=".urlencode($link)."\">(cache)</a>";

				$link2 = str_replace("/", "\/", preg_quote($link));
				$pattern = "/(?<!\[)\[$link2\|([^\]\|]+)\|([^\]\|]+)\|([^\]]+)\]/"; //< last param expected here is always nocache
				$data = preg_replace($pattern, "<a $class $target href=\"$link\" rel=\"$2 $rel\">$1</a>$ext_icon", $data);
				$pattern = "/(?<!\[)\[$link2\|([^\]\|]+)\|([^\]]+)\]/";//< last param here ($2) is used for relation (rel) attribute (e.g. shadowbox) or nocache
				preg_match($pattern, $data, $matches);
				if (isset($matches[2]) && $matches[2]=='nocache') {
					$data = preg_replace($pattern, "<a $class $target href=\"$link\" rel=\"$rel\">$1</a>$ext_icon", $data);
				} else {
					$data = preg_replace($pattern, "<a $class $target href=\"$link\" rel=\"$2 $rel\">$1</a>$ext_icon $cosa", $data);
				}
				$pattern = "/(?<!\[)\[$link2\|([^\]\|]+)\]/";
				$data = preg_replace($pattern, "<a $class $target href=\"$link\" rel=\"$rel\">$1</a>$ext_icon $cosa", $data);
				$pattern = "/(?<!\[)\[$link2\]/";
				$data = preg_replace($pattern, "<a $class $target href=\"$link\" rel=\"$rel\">$link</a>$ext_icon $cosa", $data);
			} else {
				$link2 = str_replace("/", "\/", preg_quote($link));
				$data = str_replace("|nocache", "", $data);

				$pattern = "/(?<!\[)\[$link2\|([^\]\|]+)\|([^\]]+)\]/";
				$data = preg_replace($pattern, "<a $class $target href=\"$link\" rel=\"$2 $rel\">$1</a>$ext_icon", $data);
				$pattern = "/(?<!\[)\[$link2\|([^\]\|]+)([^\]])*\]/";
				$data = preg_replace($pattern, "<a $class $target href=\"$link\" rel=\"$rel\">$1</a>$ext_icon", $data);
				$pattern = "/(?<!\[)\[$link2\]/";
				$data = preg_replace($pattern, "<a $class $target href=\"$link\" rel=\"$rel\">$link</a>$ext_icon", $data);
			}
		}

		// Handle double square brackets. to display [foo] use [[foo] -rlpowell. Improved by sylvieg to avoid replacing them in [[code]] cases.
		$data = preg_replace( "/\[\[([^\]]*)\](?!\])/", "[$1]", $data );
		$data = preg_replace( "/\[\[([^\]]*)$/", "[$1", $data );

		return $data;
	}

	private function parse_data_inline_syntax( $line, $words = array() ) {
		global $prefs;

		if ($prefs['feature_hotwords'] == 'y') {
			// Replace Hotwords before begin
			$line = $this->replace_hotwords($line, $words);
		}

		// Make plain URLs clickable hyperlinks
		if ($prefs['feature_autolinks'] == 'y') {
			$line = $this->autolinks($line);
		}

		// Replace monospaced text
		$line = preg_replace("/(^|\s)-\+(.*?)\+-/", "$1<code>$2</code>", $line);
		// Replace bold text
		$line = preg_replace("/__(.*?)__/", "<strong>$1</strong>", $line);
		// Replace italic text
		$line = preg_replace("/\'\'(.*?)\'\'/", "<em>$1</em>", $line);
		// Replace definition lists
		$line = preg_replace("/^;([^:]*):([^\/\/].*)/", "<dl><dt>$1</dt><dd>$2</dd></dl>", $line);
		$line = preg_replace("/^;(<a [^<]*<\/a>):([^\/\/].*)/", "<dl><dt>$1</dt><dd>$2</dd></dl>", $line);

		return $line;
	}

	private function parse_data_tables( $data, $simple_wiki ) {
		global $prefs;

		/*
		 * Wiki Tables syntax
		 */
		// tables in old style
		if ($prefs['feature_wiki_tables'] != 'new') {
			if (preg_match_all("/\|\|(.*)\|\|/", $data, $tables)) {
				$maxcols = 1;
				$cols = array();
				$temp_max = count($tables[0]);
				for ($i = 0; $i < $temp_max; $i++) {
					$rows = explode('||', $tables[0][$i]);
					$temp_max2 = count($rows);
					for ($j = 0; $j < $temp_max2; $j++) {
						$cols[$i][$j] = explode('|', $rows[$j]);
						if (count($cols[$i][$j]) > $maxcols)
							$maxcols = count($cols[$i][$j]);
					}
				} // for ($i ...

				$temp_max3 = count($tables[0]);
				for ($i = 0; $i < $temp_max3; $i++) {
					$repl = '<table class="wikitable">';

					$temp_max4 = count($cols[$i]);
					for ($j = 0; $j < $temp_max4; $j++) {
						$ncols = count($cols[$i][$j]);

						if ($ncols == 1 && !$cols[$i][$j][0])
							continue;

						$repl .= '<tr>';

						for ($k = 0; $k < $ncols; $k++) {
							$repl .= '<td class="wikicell" ';

							if ($k == $ncols - 1 && $ncols < $maxcols)
								$repl .= ' colspan="' . ($maxcols - $k).'"';

							$repl .= '>' . $cols[$i][$j][$k] . '</td>';
						} // // for ($k ...

						$repl .= '</tr>';
					} // for ($j ...

					$repl .= '</table>';
					$data = str_replace($tables[0][$i], $repl, $data);
				} // for ($i ...
			} // if (preg_match_all("/\|\|(.*)\|\|/", $data, $tables))
		} else {
			// New syntax for tables
			// REWRITE THIS CODE
			if (!$simple_wiki) {
				if (preg_match_all("/\|\|(.*?)\|\|/s", $data, $tables)) {
					$maxcols = 1;
					$cols = array();
					$temp_max5 = count($tables[0]);
					for ($i = 0; $i < $temp_max5; $i++) {
						$rows = preg_split("/(\n|\<br\/\>)/", $tables[0][$i]);
						$col[$i] = array();
						$temp_max6 = count($rows);
						for ($j = 0; $j < $temp_max6; $j++) {
							$rows[$j] = str_replace('||', '', $rows[$j]);
							$cols[$i][$j] = explode('|', $rows[$j]);
							if (count($cols[$i][$j]) > $maxcols)
								$maxcols = count($cols[$i][$j]);
						}
					}

					$temp_max7 = count($tables[0]);
					for ($i = 0; $i < $temp_max7; $i++) {
						$repl = '<table class="wikitable">';
						$temp_max8 = count($cols[$i]);
						for ($j = 0; $j < $temp_max8; $j++) {
							$ncols = count($cols[$i][$j]);

							if ($ncols == 1 && !$cols[$i][$j][0])
								continue;

							$repl .= '<tr>';

							for ($k = 0; $k < $ncols; $k++) {
								$repl .= '<td class="wikicell" ';
								if ($k == $ncols - 1 && $ncols < $maxcols)
									$repl .= ' colspan="' . ($maxcols - $k).'"';

								$repl .= '>' . $cols[$i][$j][$k] . '</td>';
							}
							$repl .= '</tr>';
						}
						$repl .= '</table>';
						$data = str_replace($tables[0][$i], $repl, $data);
					}
				}
			}
		}

		return $data;
	}

	function parse_wiki_argvariable(&$data, $options=null) {
		global $prefs, $user;
		if( $prefs['feature_wiki_argvariable'] == 'y' ) {
			if (preg_match_all("/\\{\\{((\w+)(\\|([^\\}]*))?)\\}\\}/",$data,$args, PREG_SET_ORDER)) {
				$needles = array();
				$replacements = array();

				foreach( $args as $arg ) {
					$value = isset($arg[4])?$arg[4]:'';
					$name = $arg[2];
					switch( $name ) {
					case 'user':
						$value = $user;
						break;
					case 'page':
						$value = $options['page'];
						break;
					default:
						if( isset($_GET[$name]) )
							$value = $_GET[$name];
						break;
					}

					if( ! empty( $value ) || isset( $arg[4] ) ) {
						$needles[] = $arg[0];
						$replacements[] = $value;
					}
				}
				$data = str_replace( $needles, $replacements, $data );
			}
		}
	}
	private function parse_data_dynamic_variables( $data, $lang = null ) {
		global $tiki_p_edit_dynvar, $prefs;

		$enclose = '%';
		if( $prefs['wiki_dynvar_style'] == 'disable' ) {
			return $data;
		} elseif( $prefs['wiki_dynvar_style'] == 'double' ) {
			$enclose = '%%';
		}

		// Replace dynamic variables
		// Dynamic variables are similar to dynamic content but they are editable
		// from the page directly, intended for short data, not long text but text
		// will work too
		//     Now won't match HTML-style '%nn' letter codes and some special utf8 situations...
		if (preg_match_all("/$enclose([^% 0-9A-Z][^% 0-9A-Z][^% ]*)$enclose/",$data,$dvars)) {
			// remove repeated elements
			$dvars = array_unique($dvars[1]);
			// Now replace each dynamic variable by a pair composed of the
			// variable value and a text field to edit the variable. Each
			foreach($dvars as $dvar) {
				$value = $this->get_dynamic_variable( $dvar, $lang );
				// Now build 2 divs
				$id = 'dyn_'.$dvar;

				if(isset($tiki_p_edit_dynvar)&& $tiki_p_edit_dynvar=='y') {
					$span1 = "<span  style='display:inline;' id='dyn_".$dvar."_display'><a class='dynavar' onclick='javascript:toggle_dynamic_var(\"$dvar\");' title='".tra('Click to edit dynamic variable','',true).": $dvar'>$value</a></span>";
					$span2 = "<span style='display:none;' id='dyn_".$dvar."_edit'><input type='text' name='dyn_".$dvar."' value='".$value."' />".'<input type="submit" name="_dyn_update" value="'.tra('Update variables','',true).'"/></span>';
				} else {
					$span1 = "<span class='dynavar' style='display:inline;' id='dyn_".$dvar."_display'>$value</span>";
					$span2 = '';
				}
				$html = $span1.$span2;
				//It's important to replace only once
				$dvar_preg = preg_quote( $dvar );
				$data = preg_replace("+$enclose$dvar_preg$enclose+",$html,$data,1);
				//Further replacements only with the value
				$data = str_replace("$enclose$dvar$enclose",$value,$data);
			}
			//At the end put an update button
			//<br /><div align="center"><input type="submit" name="dyn_update" value="'.tra('Update variables','',true).'"/></div>
			$data='<form method="post" name="dyn_vars">'."\n".$data.'</form>';
		}

		return $data;
	}

	private function get_dynamic_variable( $name, $lang = null ) {
		$query = "select `data`, `lang` from `tiki_dynamic_variables` where `name`=?";
		$result = $this->fetchAll( $query, array( $name ) );

		$value = "NaV";

		foreach( $result as $row ) {
			if( $row['lang'] == $lang ) {
				// Exact match
				return $row['data'];
			} elseif( empty( $row['lang'] ) ) {
				// Universal match, keep in case no exact match
				$value = $row['data'];
			}
		}

		return $value;
	}

	private function parse_data_process_maketoc( &$data, $options, $noparsed) {

		global $prefs;

		if ( $options['ck_editor'] ) {
			$need_maketoc = false ;
		} else {
			$need_maketoc = strpos($data, "{maketoc");
		}
		
		// Wysiwyg {maketoc} handling when not in editor mode (i.e. viewing)
		if ($need_maketoc && $prefs["feature_wysiwyg"] == 'y' && $prefs["wysiwyg_htmltowiki"] != 'y') {
			// Header needs to start at beginning of line (wysiwyg does not necessary obey)
			$data = preg_replace('/<\/([a-z]+)><h([1-6])>/im', "</\\1>\n<h\\2>", $data);
			$htmlheadersearch = '/<h([1-6])>\s*([^<]+)\s*<\/h[1-6]>/im';
			preg_match_all($htmlheadersearch, $data, $htmlheaders);
			for ($i = 0; $i < count($htmlheaders[1]); $i++) {
				$htmlheaderreplace = '';
				for ($j = 0; $j < $htmlheaders[1][$i]; $j++) {
					$htmlheaderreplace .= '!';
				}
				$htmlheaderreplace .= $htmlheaders[2][$i];
				$data = str_replace($htmlheaders[0][$i], $htmlheaderreplace, $data);
			}
		}

		$need_autonumbering = ( preg_match('/^\!+[\-\+]?#/m', $data) > 0 );

		$anch = array();
		global $anch;
		$pageNum = 1;

		// 08-Jul-2003, by zaufi
		// HotWords will be replace only in ordinal text
		// It looks __really__ goofy in Headers or Titles

		$words = array();
		if ( $prefs['feature_hotwords'] == 'y' ) {
			// Get list of HotWords
			$words = $this->get_hotwords();
		}

		// Now tokenize the expression and process the tokens
		// Use tab and newline as tokenizing characters as well  ////
		$lines = explode("\n", $data);
		if (empty($lines[count($lines)-1]) && empty($lines[count($lines)-2])) {
			array_pop($lines);
		}
		$data = '';
		$listbeg = array();
		$divdepth = array();
		$hdr_structure = array();
		$show_title_level = array();
		$last_hdr = array();
		$nb_last_hdr = 0;
		$nb_hdrs = 0;
		$inTable = 0;
		$inPre = 0;
		$inComment = 0;
		$inTOC = 0;
		$inScript = 0;
		$title_text = '';

		// loop: process all lines
		$in_paragraph = 0;
		$in_empty_paragraph = 0;
		foreach ($lines as $line) {
			$current_title_num = '';
			$numbering_remove = 0;

			$line = rtrim($line); // Trim off trailing white space
			// Check for titlebars...
			// NOTE: that title bar should start at the beginning of the line and
			//	   be alone on that line to be autoaligned... otherwise, it is an old
			//	   styled title bar...
			if (substr(ltrim($line), 0, 2) == '-=' && substr($line, -2, 2) == '=-') {
				// Close open paragraph and lists, but not div's
				$this->close_blocks($data, $in_paragraph, $listbeg, $divdepth, 1, 1, 0);
				//
				$align_len = strlen($line) - strlen(ltrim($line));

				// My textarea size is about 120 space chars.
				//define('TEXTAREA_SZ', 120);

				// NOTE: That strict math formula (split into 3 areas) gives
				//	   bad visual effects...
				// $align = ($align_len < (TEXTAREA_SZ / 3)) ? "left"
				//		: (($align_len > (2 * TEXTAREA_SZ / 3)) ? "right" : "center");
				//
				// Going to introduce some heuristic here :)
				// Visualy (remember that space char is thin) center starts at 25 pos
				// and 'right' from 60 (HALF of full width!) -- thats all :)
				//
				// NOTE: Guess align only if more than 10 spaces before -=title=-
				if ($align_len > 10) {
					$align = ($align_len < 25) ? "left" : (($align_len > 60) ? "right" : "center");

					$align = ' style="text-align: ' . $align . ';"';
				} else {
					$align = '';
				}

				//
				$line = trim($line);
				$line = '<div class="titlebar"' . $align . '>' . substr($line, 2, strlen($line) - 4). '</div>';
				$data .= $line . "\n";
				// TODO: Case is handled ...  no need to check other conditions
				//	   (it is apriori known that they are all false, moreover sometimes
				//	   check procedure need > O(0) of compexity)
				//	   -- continue to next line...
				//	   MUST replace all remaining parse blocks to the same logic...
				continue;
			}

			// Replace old styled titlebars
			if (strlen($line) != strlen($line = preg_replace("/-=(.+?)=-/", "<div class='titlebar'>$1</div>", $line))) {
				// Close open paragraph, but not lists (why not?) or div's
				$this->close_blocks($data, $in_paragraph, $listbeg, $divdepth, 1, 0, 0);
				$data .= $line . "\n";

				continue;
			}

			// check if we are inside a ~hc~ block and, if so, ignore
			// monospaced and do not insert <br />
			$inComment += substr_count(strtolower($line), "<!--");
			$inComment -= substr_count(strtolower($line), "-->");

			// check if we are inside a ~pre~ block and, if so, ignore
			// monospaced and do not insert <br />
			$inPre += substr_count(strtolower($line), "<pre");
			$inPre -= substr_count(strtolower($line), "</pre");

			// check if we are inside a table, if so, ignore monospaced and do
			// not insert <br />
			$inTable += substr_count(strtolower($line), "<table");
			$inTable -= substr_count(strtolower($line), "</table");

			// check if we are inside an ul TOC list, if so, ignore monospaced and do
			// not insert <br />
			$inTOC += substr_count(strtolower($line), "<ul class=\"toc");
			$inTOC -= substr_count(strtolower($line), "</ul><!--toc-->");

			// check if we are inside a script not insert <br />
			$inScript += substr_count(strtolower($line), "<script ");
			$inScript -= substr_count(strtolower($line), "</script>");

			// If the first character is ' ' and we are not in pre then we are in pre
			if (substr($line, 0, 1) == ' ' && $prefs['feature_wiki_monosp'] == 'y' && $inTable == 0 && $inPre == 0 && $inComment == 0 && !$options['is_html']) {
				// Close open paragraph and lists, but not div's
				$this->close_blocks($data, $in_paragraph, $listbeg, $divdepth, 1, 1, 0);

				// If the first character is space then make font monospaced.
				// For fixed formatting, use ~pp~...~/pp~
				$line = '<tt>' . $line . '</tt>';
			}

			if (!$options['ck_editor']) {
				$line = $this->parse_data_inline_syntax( $line, $words );
			}

			// This line is parseable then we have to see what we have
			if (substr($line, 0, 3) == '---') {
				// This is not a list item --- close open paragraph and lists, but not div's
				$this->close_blocks($data, $in_paragraph, $listbeg, $divdepth, 1, 1, 0);
				$line = '<hr />';
			} else {
				$litype = substr($line, 0, 1);
				if (($litype == '*' || $litype == '#') && !(strlen($line)-count($listbeg)>4 && preg_match('/^\*+$/', $line))) {
					// Close open paragraph, but not lists or div's
					$this->close_blocks($data, $in_paragraph, $listbeg, $divdepth, 1, 0, 0);
					$listlevel = $this->how_many_at_start($line, $litype);
					$liclose = '</li>';
					$addremove = 0;
					if ($listlevel < count($listbeg)) {
						while ($listlevel != count($listbeg)) $data .= array_shift($listbeg);
						if (substr(current($listbeg), 0, 5) != '</li>') $liclose = '';
					} elseif ($listlevel > count($listbeg)) {
						$listyle = '';
						while ($listlevel != count($listbeg)) {
							array_unshift($listbeg, ($litype == '*' ? '</ul>' : '</ol>'));
							if ($listlevel == count($listbeg)) {
								$listate = substr($line, $listlevel, 1);
								if (($listate == '+' || $listate == '-') && !($litype == '*' && !strstr(current($listbeg), '</ul>') || $litype == '#' && !strstr(current($listbeg), '</ol>'))) {
									$thisid = 'id' . microtime() * 1000000;
									if ( !$options['ck_editor'] ) {
										$data .= '<br /><a id="flipper' . $thisid . '" class="link" href="javascript:flipWithSign(\'' . $thisid . '\')">[' . ($listate == '-' ? '+' : '-') . ']</a>';
									}
									$listyle = ' id="' . $thisid . '" style="display:' . ($listate == '+' || $options['ck_editor'] ? 'block' : 'none') . ';"';
									$addremove = 1;
								}
							}
							$data.=($litype=='*'?"<ul$listyle>":"<ol$listyle>");
						}
						$liclose='';
					}
					if ($litype == '*' && !strstr(current($listbeg), '</ul>') || $litype == '#' && !strstr(current($listbeg), '</ol>')) {
						$data .= array_shift($listbeg);
						$listyle = '';
						$listate = substr($line, $listlevel, 1);
						if (($listate == '+' || $listate == '-')) {
							$thisid = 'id' . microtime() * 1000000;
							if ( !$options['ck_editor'] ) {
								$data .= '<br /><a id="flipper' . $thisid . '" class="link" href="javascript:flipWithSign(\'' . $thisid . '\')">[' . ($listate == '-' ? '+' : '-') . ']</a>';
							}
							$listyle = ' id="' . $thisid . '" style="display:' . ($listate == '+' || $options['ck_editor'] ? 'block' : 'none') . ';"';
							$addremove = 1;
						}
						$data .= ($litype == '*' ? "<ul$listyle>" : "<ol$listyle>");
						$liclose = '';
						array_unshift($listbeg, ($litype == '*' ? '</li></ul>' : '</li></ol>'));
					}
					$line = $liclose . '<li>' . substr($line, $listlevel + $addremove);
					if (substr(current($listbeg), 0, 5) != '</li>') array_unshift($listbeg, '</li>' . array_shift($listbeg));
				} elseif ($litype == '+') {
					// Close open paragraph, but not list or div's
					$this->close_blocks($data, $in_paragraph, $listbeg, $divdepth, 1, 0, 0);
					$listlevel = $this->how_many_at_start($line, $litype);
					// Close lists down to requested level
					while ($listlevel < count($listbeg)) $data .= array_shift($listbeg);

					// Must append paragraph for list item of given depth...
					$listlevel = $this->how_many_at_start($line, $litype);
					if (count($listbeg)) {
						if (substr(current($listbeg), 0, 5) != '</li>') {
							array_unshift($listbeg, '</li>' . array_shift($listbeg));
							$liclose = '<li>';
						} else $liclose = '<br />';
					} else $liclose = '';
					$line = $liclose . substr($line, count($listbeg));
				} else {
					// This is not a list item - close open lists,
					// but not paragraph or div's. If we are
					// closing a list, there really shouldn't be a
					// paragraph open anyway.
					$this->close_blocks($data, $in_paragraph, $listbeg, $divdepth, 0, 1, 0);
					// Get count of (possible) header signs at start
					$hdrlevel = $this->how_many_at_start($line, '!');
					// If 1st char on line is '!' and its count less than 6 (max in HTML)
					if ($litype == '!' && $hdrlevel > 0 && $hdrlevel <= 6) {

						/*
						 * Handle headings autonumbering syntax (i.e. !#Text, !!#Text, ...)
						 * Note :
						 *    this needs to be done even if the current header has no '#'
						 *    in order to generate the right numbers when they are not specified for every headers.
						 *    This is the case, for example, when you want to add numbers to headers of level 2 but not to level 1
						 */

						$line_lenght = strlen($line);

						// Generate an array containing the squeleton of maketoc (based on headers levels)
						//   i.e. hdr_structure will contain something lile this :
						//     array( 1, 2, 2.1, 2.1.1, 2.1.2, 2.2, ... , X.Y.Z... )
						//

						$hdr_structure[$nb_hdrs] = '';

						// Generate the number (e.g. 1.2.1.1) of the current title, based on the previous title number :
						//   - if the current title deepest level is lesser than (or equal to)
						//     the deepest level of the previous title : then we increment the last level number,
						//   - else : we simply add new levels with value '1' (only if the previous level number was shown),
						//
						if ( $nb_last_hdr > 0 && $hdrlevel <= $nb_last_hdr ) {
							$hdr_structure[$nb_hdrs] = array_slice($last_hdr, 0, $hdrlevel);
							if ( !empty($show_title_level[$hdrlevel]) || ! $need_autonumbering ) {
								//
								// Increment the level number only if :
								//     - the last title of the same level number has a displayed number
								//  or - no title has a displayed number (no autonumbering)
								//
								$hdr_structure[$nb_hdrs][$hdrlevel - 1]++;
							}
						} else {
							if ( $nb_last_hdr > 0 ) {
								$hdr_structure[$nb_hdrs] = $last_hdr;
							}
							for ( $h = 0 ; $h < $hdrlevel - $nb_last_hdr ; $h++ ) {
								$hdr_structure[$nb_hdrs][$h + $nb_last_hdr] = '1';
							}
						}
						$show_title_level[$hdrlevel] = preg_match('/^!+[\+\-]?#/', $line);

						// Update last_hdr info for the next header
						$last_hdr = $hdr_structure[$nb_hdrs];
						$nb_last_hdr = count($last_hdr);

						$current_title_real_num = implode('.', $hdr_structure[$nb_hdrs]).'. ';

						// Update the current title number to hide all parents levels numbers if the parent has no autonumbering
						$hideall = false;
						for ( $j = $hdrlevel ; $j > 0 ; $j-- ) {
							if ( $hideall || ! $show_title_level[$j] ) {
								unset($hdr_structure[$nb_hdrs][$j - 1]);
								$hideall = true;
							}
						}

						// Store the title number to use only if it has to be shown (if the '#' char is used)
						$current_title_num = $show_title_level[$hdrlevel] ? implode('.', $hdr_structure[$nb_hdrs]).'. ' : '';

						$nb_hdrs++;


						// Close open paragraph (lists already closed above)
						$this->close_blocks($data, $in_paragraph, $listbeg, $divdepth, 1, 0, 0);
						// Close lower level divs if opened
						for (;current($divdepth) >= $hdrlevel; array_shift($divdepth)) $data .= '</div>';

						// Remove possible hotwords replaced :)
						//   Umm, *why*?  Taking this out lets page
						//   links in headers work, which can be nice.
						//   -rlpowell
						// $line = strip_tags($line);

						// OK. Parse headers here...
						$anchor = '';
						$aclose = '';
						$aclose2 = '';
						$addremove = $show_title_level[$hdrlevel] ? 1 : 0; // If needed, also remove '#' sign from title beginning

						// May be special signs present after '!'s?
						$divstate = substr($line, $hdrlevel, 1);
						if (($divstate == '+' || $divstate == '-') && !$options['ck_editor']) {
							// OK. Must insert flipper after HEADER, and then open new div...
							$thisid = 'id' . preg_replace('/[^a-zA-z0-9]/', '',urlencode($options['page'])) .$nb_hdrs;
							$aclose = '<a id="flipper' . $thisid . '" class="link" href="javascript:flipWithSign(\'' . $thisid . '\')">[' . ($divstate == '-' ? '+' : '-') . ']</a>';
							$aclose2 = '<div id="' . $thisid . '" class="showhide_heading" style="display:' . ($divstate == '+' ? 'block' : 'none') . ';">';
							global $headerlib;
							$headerlib->add_jq_onready( "setheadingstate('$thisid');" );
							array_unshift($divdepth, $hdrlevel);
							$addremove += 1;
						}

						// Generate the final title text
						$title_text_base = substr($line, $hdrlevel + $addremove);
						$title_text = $current_title_num.$title_text_base;

						// create stable anchors for all headers
						// use header but replace non-word character sequences
						// with one underscore (for XHTML 1.0 compliance)
						// Workaround pb with plugin replacement and header id
						//  first we remove hash from title_text for headings beginning
						//  with images and HTML tags
						$thisid = preg_replace('/§[a-z0-9]{32}§/', '', $title_text);
						$thisid = preg_replace('#</?[^>]+>#', '', $thisid);
						$thisid = preg_replace('/[^a-zA-Z0-9\:\.\-\_]+/', '_', $thisid);
						$thisid = preg_replace('/^[^a-zA-Z]*/', '', $thisid);
						if (empty($thisid)) $thisid = 'a'.md5($title_text);

						// Add a number to the anchor if it already exists, to avoid duplicated anchors
						if ( isset($all_anchors[$thisid]) ) {
							$all_anchors[$thisid]++;
							$thisid .= '_'.$all_anchors[$thisid];
						} else {
							$all_anchors[$thisid] = 1;
						}

						// Collect TOC entry if any {maketoc} is present on the page
						//if ( $need_maketoc !== false ) {
						$anch[] =  array(
										'id' => $thisid,
										'hdrlevel' => $hdrlevel,
										'pagenum' => $pageNum,
										'title' => $title_text_base,
										'title_displayed_num' => $current_title_num,
										'title_real_num' => $current_title_real_num
										);
						//}
						global $tiki_p_edit, $section;
						if ($prefs['wiki_edit_section'] === 'y' && $section === 'wiki page' && $tiki_p_edit === 'y' &&
								( $prefs['wiki_edit_section_level'] == 0 || $hdrlevel <= $prefs['wiki_edit_section_level']) &&
								(empty($options['print']) || !$options['print']) && !$options['suppress_icons'] ) {

							global $smarty;
							include_once('lib/smarty_tiki/function.icon.php');
							$button = '<div class="icon_edit_section"><a href="tiki-editpage.php?';
							if (!empty($options['page'])) {
								$button .= 'page='.urlencode($options['page']).'&amp;';
							}
							$button .= 'hdr='.$nb_hdrs.'">'.smarty_function_icon(array('_id'=>'page_edit_section', 'alt'=>tra('Edit Section')), $smarty).'</a></div>';
						} else {
							$button = '';
						}

						// Use $hdrlevel + 1 because the page title is H1, so none of the other headers should be.
						// Except when page title is off
						// Or when in wysiwyg mode
						$headerInc = 0;
						if( $prefs['feature_page_title'] == 'y' && ! $options['noheaderinc'] )
							++$headerInc;

						if ( $prefs['feature_wiki_show_hide_before'] == 'y' ) {
							$line = $button.'<h'.($hdrlevel+$headerInc).' class="showhide_heading" id="'.$thisid.'">'.$aclose.' '.$title_text.'</h'.($hdrlevel+$headerInc).'>'.$aclose2;
						} else {
							$line = $button.'<h'.($hdrlevel+$headerInc).' class="showhide_heading" id="'.$thisid.'">'.$title_text.'</h'.($hdrlevel+$headerInc).'>'.$aclose.$aclose2;
						}
					} elseif (!strcmp($line, $prefs['wiki_page_separator'])) {
						// Close open paragraph, lists, and div's
						$this->close_blocks($data, $in_paragraph, $listbeg, $divdepth, 1, 1, 1);
						// Leave line unchanged... tiki-index.php will split wiki here
						$line = $prefs['wiki_page_separator'];
						$pageNum += 1;
					} else {
						/** Usual paragraph.
						 *
						 * If the
						 * $prefs['feature_wiki_paragraph_formatting']
						 * is on, then consecutive lines of
						 * text will be gathered into a block
						 * that is surrounded by HTML
						 * paragraph tags. One or more blank
						 * lines, or another special Wiki line
						 * (e.g., heading, titlebar, etc.)
						 * signifies the end of the
						 * paragraph. If the paragraph
						 * formatting feature is off, the
						 * original Tikiwiki behavior is used,
						 * in which each line in the source is
						 * terminated by an explicit line
						 * break (br tag).
						 *
						 * @since Version 1.9
						 */
						if ($inTable == 0 && $inPre == 0 && $inComment == 0 && $inTOC == 0 &&  $inScript == 0
								// Don't put newlines at comments' end!
								&& strpos($line, "-->") !== (strlen($line) - 3)
								&& $options['process_wiki_paragraphs']) {
							 	
							$tline = trim(str_replace('&nbsp;', '', $line));
							
							if ($prefs['feature_wiki_paragraph_formatting'] == 'y') {
								if (count($lines) > 1) {	// don't apply wiki para if only single line so you can have inline includes
									$contains_block = $this->contains_html_block( $tline );
									$contains_br = $this->contains_html_br( $tline );

									if (!$contains_block) {	// check inside plugins etc for block elements
										preg_match_all('/\xc2\xa7[^\xc2\xa7]+\xc2\xa7/', $tline, $m);	// noparse guid for plugins 
										if (count($m) > 0) {
											$m_count = count($m[0]);
											$nop_ix = false;
											for ($i = 0; $i < $m_count; $i++) {
												//$nop_ix = array_search( $m[0][$i], $noparsed['key'] ); 	// array_search doesn't seem to work here - why? no "keys"?
												foreach ($noparsed['key'] as $k => $s) {
													if ($m[0][$i] == $s) {
														$nop_ix = $k;
														break;
													}
												}
												if ($nop_ix !== false) {
													$nop_str = $noparsed['data'][$nop_ix];
													$contains_block = $this->contains_html_block( $nop_str );
													if ($contains_block) {
														break;
													}
												}
											}
										}
									}
									
								 	if ($in_paragraph && ((empty($tline) && $in_empty_paragraph === 0) || $contains_block)) {
										// If still in paragraph, on meeting first blank line or end of div or start of div created by plugins; close a paragraph
										$this->close_blocks($data, $in_paragraph, $listbeg, $divdepth, 1, 0, 0);
									} elseif (!$in_paragraph && !$contains_block && !$contains_br) {
										// If not in paragraph, first non-blank line; start a paragraph; if not start of div created by plugins
										$data .= "<p>";
										if (empty($tline)) {
											$line = '&nbsp;';
											$in_empty_paragraph = 1;
										}
										$in_paragraph = 1;
									} elseif ($in_paragraph && $prefs['feature_wiki_paragraph_formatting_add_br'] == 'y' && !$contains_block) {
										// A normal in-paragraph line if not close of div created by plugins
										if (!empty($tline)) {
											$in_empty_paragraph = 0;
										}
										$line = "<br />" . $line;
									} else {
										// A normal in-paragraph line or a consecutive blank line.
										// Leave it as is.
									}
								}
							} elseif (($prefs['wysiwyg_htmltowiki'] === 'y' && !empty($tline) && strpos($tline, '<br />') === false) || !$options['is_html']) {
								$line .= "<br />";
							}
						}
					}
				}
			}
			$data .= $line . "\n";
		}

		// Close open paragraph, lists, and div's
		$this->close_blocks($data, $in_paragraph, $listbeg, $divdepth, 1, 1, 1);

		/*
		 * Replace special "maketoc" plugins
		 *  Valid arguments :
		 *    - type (look of the maketoc),
		 *    - maxdepth (max level displayed),
		 *    - title (replace the default title),
		 *    - showhide (if set to y, add the Show/Hide link)
		 *    - nolinks (if set to y, don't add links on toc entries)
		 *    - nums : 
		 *       * 'n' means 'no title autonumbering' in TOC,
		 *       * 'force' means :
		 *	    ~ same as 'y' if autonumbering is used in the page,
		 *	    ~ 'number each toc entry as if they were all autonumbered'
		 *       * any other value means 'same as page's headings autonumbering',
		 *
		 *  (Note that title will be translated if a translation is available)
		 *
		 *  Examples: {maketoc}, {maketoc type=box maxdepth=1 showhide=y}, {maketoc title="Page Content" maxdepth=3}, ...
		 *  Obsolete syntax: {maketoc:box}
		 */
		$new_data = '';
		$search_start = 0;
		if ( !$options['ck_editor']) {
			while ( ($maketoc_start = strpos($data, "{maketoc", $search_start)) !== false ) {
				$maketoc_length = strpos($data, "}", $maketoc_start) + 1 - $maketoc_start;
				$maketoc_string = substr($data, $maketoc_start, $maketoc_length);

				// Handle old type definition for type "box" (and preserve environment for the title also)
				if ( $maketoc_length > 12 && strtolower(substr($maketoc_string, 8, 4)) == ':box' ) {
					$maketoc_string = "{maketoc type=box showhide=y title='".tra('index', $options['language'], true).'"'.substr($maketoc_string, 12);
				}

				$maketoc_string = str_replace('&quot;', '"', $maketoc_string);
				$maketoc_regs = array();

				if ( $maketoc_length == 9 || preg_match_all("/([^\s=\(]+)=([^\"\s=\)\}]+|\"[^\"]*\")/", $maketoc_string, $maketoc_regs) ) {

					if ( $maketoc_start > 0 ) {
						$new_data .= substr($data, 0, $maketoc_start);
					}

					// Set maketoc default values
					$maketoc_args = array(
							'type' => '',
							'maxdepth' => 0, // No limit
							'title' => tra('Table of contents', $options['language'], true),
							'showhide' => '',
							'nolinks' => '',
							'nums' => '',
							'levels' => ''
							);

					// Build maketoc arguments list (and remove " chars if they are around the value)
					if ( isset($maketoc_regs[1]) ) {
						$nb_args = count($maketoc_regs[1]);
						for ( $a = 0; $a < $nb_args ; $a++ ) {
							$maketoc_args[strtolower($maketoc_regs[1][$a])] = trim($maketoc_regs[2][$a], '"');
						}
					}

					if ( $maketoc_args['title'] != '' ) {
						// Translate maketoc title
						$maketoc_summary = ' summary="'.tra($maketoc_args['title'], $options['language'], true).'"';
						$maketoc_title = "<div id='toctitle'><h3>".tra($maketoc_args['title'], $options['language']).'</h3></div>';
					} else {
						$maketoc_summary = '';
						$maketoc_title = '';
					}
					if (!empty($maketoc_args['levels'])) {
						$maketoc_args['levels'] = preg_split('/\s*,\s*/', $maketoc_args['levels']);
					}

					// Build maketoc
					switch ( $maketoc_args['type'] ) {
						case 'box': 
							$maketoc_header = '';
							$maketoc = "<table id='toc' class='toc'$maketoc_summary>\n<tr><td>$maketoc_title<ul>";
							$maketoc_footer = "</ul></td></tr></table>\n";
							$link_class = 'toclink';
							break;
						default: 
							$maketoc = '';
							$maketoc_header = "<div id='toc'>".$maketoc_title;
							$maketoc_footer = '</div>';
							$link_class = 'link';
					}
					if ( count($anch) and $need_maketoc !== false) {
						foreach ( $anch as $tocentry ) {
							if ( $maketoc_args['maxdepth'] > 0 && $tocentry['hdrlevel'] > $maketoc_args['maxdepth'] ) {
								continue;
							}
							if (!empty($maketoc_args['levels']) && !in_array($tocentry['hdrlevel'], $maketoc_args['levels'])) {
								continue;
							}
							// Generate the toc entry title (with nums)
							if ( $maketoc_args['nums'] == 'n' ) {
								$tocentry_title = '';
							} elseif ( $maketoc_args['nums'] == 'force' && ! $need_autonumbering ) {
								$tocentry_title = $tocentry['title_real_num'];
							} else {
								$tocentry_title = $tocentry['title_displayed_num'];
							}
							$tocentry_title .= $tocentry['title'];

							// Generate the toc entry link
							$tocentry_link = '#'.$tocentry['id'];
							if ( $tocentry['pagenum'] > 1 ) {
								$tocentry_link = $_SERVER['PHP_SELF'].'?page='.$options['page'].'&pagenum='.$tocentry['pagenum'].$tocentry_link;
							}
							if ( $maketoc_args['nolinks'] != 'y' ) {
								$tocentry_title = "<a href='$tocentry_link' class='link'>".$tocentry_title.'</a>';
							}

							if ( $maketoc != '' ) $maketoc.= "\n";
							$shift = $tocentry['hdrlevel'];
							if (!empty($maketoc_args['levels'])) {
								for ($i = 1; $i <= $tocentry['hdrlevel']; ++$i) {
									if (!in_array($i, $maketoc_args['levels']))
										--$shift;
								}
							}
							switch ( $maketoc_args['type'] ) {
								case 'box':
									$maketoc .= "<li class='toclevel-".$shift."'>".$tocentry_title."</li>";
									break;
								default:
									$maketoc .= str_repeat('*', $shift).$tocentry_title;
							}
						}
						$maketoc = $this->parse_data($maketoc);
						if (preg_match("/^<ul>/", $maketoc)) {
							$maketoc = preg_replace("/^<ul>/", '<ul class="toc">', $maketoc);
							$maketoc .= '<!--toc-->';
						}

						if ( $link_class != 'link' ) {
							$maketoc = preg_replace("/'link'/", "'$link_class'", $maketoc);
						}
					}
					$maketoc = $maketoc_header.$maketoc.$maketoc_footer;

					// Add a Show/Hide link
					if ( isset($maketoc_args['showhide']) && $maketoc_args['showhide'] == 'y' ) {
						$maketoc .= "<script type='text/javascript'>\n"
							. "//<![CDATA[\n"
							. " if (window.showTocToggle) { var tocShowText = '".tra('Show','',true)."'; var tocHideText = '".tra('Hide','',true)."'; showTocToggle(); }\n"
							. "//]]>;\n"
							. "</script>\n";
					}

					$new_data .= $maketoc;
					$data = substr($data, $maketoc_start + $maketoc_length);
					$search_start = 0; // Reinitialize search start cursor, since data now begins after the last replaced maketoc
				} else {
					$search_start = $maketoc_start + $maketoc_length;
				}
			}
		}
		$data = $new_data.$data;
		// Add icon to edit the text before the first section (if there is some)
		if ($prefs['wiki_edit_section'] === 'y' && isset($section) && $section === 'wiki page' && $tiki_p_edit === 'y' && (empty($options['print']) ||
				!$options['print'])  && strpos($data, '<div class="icon_edit_section">') != 0 && !$options['suppress_icons']) {
					
			global $smarty;
			include_once('lib/smarty_tiki/function.icon.php');
			$button = '<div class="icon_edit_section"><a href="tiki-editpage.php?';
			if (!empty($options['page'])) {
				$button .= 'page='.urlencode($options['page']).'&amp;';
			}
			$button .= 'hdr=0">'.smarty_function_icon(array('_id'=>'page_edit_section', 'alt'=>tra('Edit Section')), $smarty).'</a></div>';
			$data = $button.$data;
		}
	}
	
	function contains_html_block($inHtml) {
		// detect all block elements as defined on http://www.w3.org/2007/07/xhtml-basic-ref.html
		$block_detect_regexp = '/<[\/]?(?:address|blockquote|div|dl|fieldset|h\d|hr|li|noscript|ol|p|pre|table|ul)/i';
		return  (preg_match( $block_detect_regexp, $inHtml) > 0);
	}

	function contains_html_br($inHtml) {
		$block_detect_regexp = '/<(?:br)/i';
		return  (preg_match( $block_detect_regexp, $inHtml) > 0);
	}

	function get_wiki_link_replacement( $pageLink, $extra = array() ) {
		global $prefs, $wikilib, $semanticlib;

		// Fetch all externals once
		static $externals = false;
		if( false === $externals ) {
			$externals = $this->fetchMap( 'SELECT LOWER(`name`), `extwiki` FROM `tiki_extwiki`' );
		}
		
		$displayLink = $pageLink;

		// HTML entities encoding breaks page lookup
		$pageLink = html_entity_decode( $pageLink, ENT_COMPAT, 'UTF-8' );

		$description = null;
		$reltype = null;
		$processPlural = false;
		
		if( array_key_exists( 'description', $extra ) )
			$description = $extra['description'];
		if( array_key_exists( 'reltype', $extra ) )
			$reltype = $extra['reltype'];
		if( array_key_exists( 'plural', $extra ) )
			$processPlural = (boolean) $extra['plural'];

		require_once 'WikiParser/OutputLink.php';
		$link = new WikiParser_OutputLink;
		$link->setIdentifier( $pageLink );
		$link->setQualifier( $reltype );
		$link->setDescription( $description );
		$link->setWikiLookup( array( $this, 'parser_helper_wiki_info_getter' ) );
		$link->setWikiLinkBuilder( array( $this, 'parser_helper_wiki_link_builder' ) );
		$link->setExternals( $externals );
		$link->setHandlePlurals( $processPlural );

		if( $prefs['feature_multilingual'] == 'y' && isset( $GLOBALS['pageLang'] ) ) {
			$link->setLanguage( $GLOBALS['pageLang'] );
		}

		return $link->getHtml();
	}

	function parser_helper_wiki_link_builder( $pageLink ) {
		global $wikilib;
		return $wikilib->sefurl($pageLink);
	}

	function parser_helper_wiki_info_getter( $pageName ) {
		global $prefs;
		$page_info = $this->get_page_info($pageName, false);
		
		if ( $page_info !== false ) {
			return $page_info;
		}

		// If page does not exist directly, attempt to find an alias
		if ( $prefs['feature_wiki_pagealias'] == 'y' ) {
			global $semanticlib; require_once 'lib/wiki/semanticlib.php';

			$toPage = $pageName;
			$tokens = explode( ',', $prefs['wiki_pagealias_tokens'] ); 
			
			$prefixes = explode( ',', $prefs["wiki_prefixalias_tokens"]);
			foreach ($prefixes as $p) {
				$p = trim($p);
				if (strlen($p) > 0 && strtolower(substr($pageName, 0, strlen($p))) == strtolower($p)) {
					$toPage = $p;
					$tokens = 'prefixalias';
				}
			}
			 
			$links = $semanticlib->getLinksUsing(
				$tokens,
				array( 'toPage' => $toPage ) );

			if ( count($links) > 1 ) {
				// There are multiple aliases for this page. Need to disambiguate.
				//
				// When feature_likePages is set, trying to display the alias itself will
				// display an error page with the list of aliased pages in the "like pages" section.
				// This allows the user to pick the appropriate alias.
				// So, leave the $pageName to the alias.
				// 
				// If feature_likePages is not set, then the user will only see that the page does not
				// exist. So it's better to just pick the first one.
				//													
				if ($prefs['feature_likePages'] == 'y' || $tokens == 'prefixalias') {
					// Even if there is more then one match, if prefix is being redirected then better
					// to fail than to show possibly wrong page
					return true;
				} else {
					// If feature_likePages is NOT set, then trying to display the first one is fine
					// $pageName is by ref so it does get replaced 
					$pageName = $links[0]['fromPage'];
					return $this->get_page_info( $pageName );
				}
			} elseif (count($links)) {
				// there is exactly one match
				if ($prefs['feature_wiki_1like_redirection'] == 'y') {
					return true;
				} else {
					$pageName = $links[0]['fromPage'];
					return $this->get_page_info( $pageName );
				} 
			}
		}
	}

	function parse_smileys($data) {
		global $prefs;

		if ($prefs['feature_smileys'] == 'y') {
			// Example of all Tiki Smileys (the old syntax)
			// (:biggrin:) (:confused:) (:cool:) (:cry:) (:eek:) (:evil:) (:exclaim:) (:frown:)
			// (:idea:) (:lol:) (:mad:) (:mrgreen:) (:neutral:) (:question:) (:razz:) (:redface:)
			// (:rolleyes:) (:sad:) (:smile:) (:surprised:) (:twisted:) (:wink:) (:arrow:) (:santa:)
			
			$data = preg_replace("/\(:([^:]+):\)/", "<img alt=\"$1\" src=\"img/smiles/icon_$1.gif\" />", $data);

			// Example of Tiki Smileys in the new (common chat) syntax
			// :-D :-S B-) :'( 8-o }:( !-) >:( i-) LOL >X( |-D :-| ?-) :-p |-] :-/ :-( :-) :-o }:) ;-) ->) *<:)
			
			// support for common emoticons format
			// TODO: test if it doesn't conflict, move it to a separate lib ? use pngs (not animated only) ? support emoticon themes ?
			// replace any match starting with a space (or start of data) and the following:
			// :) :-)
			$data = preg_replace('/(\s|^):-?\)/', "$1<img alt=\":-)\" title=\"".tra('smiling')."\" src=\"img/smiles/icon_smile.gif\" />", $data);
			// :( :-(
			$data = preg_replace('/(\s|^):-?\(/', "$1<img alt=\":-(\" title=\"".tra('sad')."\" src=\"img/smiles/icon_sad.gif\" />", $data);
			// :D :-D
			$data = preg_replace('/(\s|^):-?D/', "$1<img alt=\":-D\" title=\"".tra('grinning')."\" src=\"img/smiles/icon_biggrin.gif\" />", $data);
			// :S :-S :s :-s
			$data = preg_replace('/(\s|^):-?S/i', "$1<img alt=\":-S\" title=\"".tra('confused')."\" src=\"img/smiles/icon_confused.gif\" />", $data);
			// B) B-) 8-)
			$data = preg_replace('/(\s|^)(B-?|8-)\)/', "$1<img alt=\"B-)\" title=\"".tra('cool')."\" src=\"img/smiles/icon_cool.gif\" />", $data);
			// :'( :_(
			$data = preg_replace('/(\s|^):[\'|_]\(/', "$1<img alt=\":_(\" title=\"".tra('crying')."\" src=\"img/smiles/icon_cry.gif\" />", $data);
			// 8-o 8-O =-o =-O
			$data = preg_replace('/(\s|^)[8=]-O/i', "$1<img alt=\"8-O\" title=\"".tra('frightened')."\" src=\"img/smiles/icon_eek.gif\" />", $data);
			// }:( }:-(
			$data = preg_replace('/(\s|^)\}:-?\(/', "$1<img alt=\"}:(\" title=\"".tra('evil stuff')."\" src=\"img/smiles/icon_evil.gif\" />", $data);
			// !-) !)
			$data = preg_replace('/(\s|^)\!-?\)/', "$1<img alt=\"(!)\" title=\"".tra('exclamation mark !')."\" src=\"img/smiles/icon_exclaim.gif\" />", $data);
			// >:( >:-(
			$data = preg_replace('/(\s|^)\>:-?\(/', "$1<img alt=\"}:(\" title=\"".tra('frowning')."\" src=\"img/smiles/icon_frown.gif\" />", $data);
			// i-)
			$data = preg_replace('/(\s|^)i-\)/', "$1<img alt=\"(".tra('light bulb').")\" title=\"".tra('idea !')."\" src=\"img/smiles/icon_idea.gif\" />", $data);
			// LOL
			$data = preg_replace('/(\s|^)LOL(\s|$)/', "$1<img alt=\"(".tra('LOL').")\" title=\"".tra('laughing out loud !')."\" src=\"img/smiles/icon_lol.gif\" />$2", $data);
			// >X( >X[ >:[ >X-( >X-[ >:-[
			$data = preg_replace('/(\s|^)\>[:X]-?\(/', "$1<img alt=\">:[\" title=\"".tra('mad')."\" src=\"img/smiles/icon_mad.gif\" />", $data);
			// =D =-D
			$data = preg_replace('/(\s|^)[=]-?D/', "$1<img alt=\"=D\" title=\"".tra('mr. green laughing')."\" src=\"img/smiles/icon_mrgreen.gif\" />", $data);
			
			// to be continued...
		}
		return $data;
	}

	function get_pages($data,$withReltype = false) {
		global $page_regex, $prefs;

		require_once 'WikiParser/PluginMatcher.php';
		$matches = WikiParser_PluginMatcher::match( $data );
		foreach( $matches as $match ) {
			if( $match->getName() == 'code' ) {
				$match->replaceWith( '' );
			}
		}

		$data = $matches->getText();

		preg_match_all("/\(([a-z0-9-]+)?\( *($page_regex) *\)\)/", $data, $normal);
		preg_match_all("/\(([a-z0-9-]+)?\( *($page_regex) *\|(.+?)\)\)/", $data, $withDesc);
		preg_match_all('/<a class="wiki" href="tiki-index\.php\?page=([^\?&"]+)[^"]*"/', $data, $htmlLinks);
		preg_match_all('/<a class="wiki wikinew" href="tiki-editpage\.php\?page=([^\?&"]+)"/', $data, $htmlWantedLinks);
		foreach($htmlLinks[1] as &$h) {
			$h = urldecode($h);
		}
		foreach($htmlWantedLinks[1] as &$h) {
			$h = urldecode($h);
		}

		if ($prefs['feature_wikiwords'] == 'y') {
			preg_match_all("/([ \n\t\r\,\;]|^)?([A-Z][a-z0-9_\-]+[A-Z][a-z0-9_\-]+[A-Za-z0-9\-_]*)($|[ \n\t\r\,\;\.])/", $data, $wikiLinks);

			$pageList = array_merge( $normal[2], $withDesc[2], $wikiLinks[2], $htmlLinks[1], $htmlWantedLinks[1] );
			if( $withReltype ) {
				$relList = array_merge(
					$normal[1], 
					$withDesc[1], 
					count($wikiLinks[2]) ? array_fill( 0, count($wikiLinks[2]), null ) : array(),
					count($htmlLinks[1]) ? array_fill( 0, count($htmlLinks[1]), null ) : array(),
					count($htmlWantedLinks[1]) ? array_fill( 0, count($htmlWantedLinks[1]), null ) : array()
				);
			}
		} else {
			$pageList = array_merge( $normal[2], $withDesc[2], $htmlLinks[1], $htmlWantedLinks[1] );
			if( $withReltype ) {
				$relList = array_merge(
					$normal[1], 
					$withDesc[1],
					count($htmlLinks[1]) ? array_fill( 0, count($htmlLinks[1]), null ) : array(),
					count($htmlWantedLinks[1]) ? array_fill( 0, count($htmlWantedLinks[1]), null ) : array()
				);
			}
		}
	
		if( $withReltype ) {
			$complete = array();
			foreach( $pageList as $idx => $name ) {
				if( ! array_key_exists( $name, $complete ) )
					$complete[$name] = array();
				if( ! empty( $relList[$idx] ) && ! in_array( $relList[$idx], $complete[$name] ) )
					$complete[$name][] = $relList[$idx];
			}

			return $complete;
		} else {
			return array_unique( $pageList );
		}
	}

	function clear_links($page) {
		$this->table('tiki_links')->deleteMultiple(array(
			'fromPage' => $page,
		));

		$query = "delete from `tiki_object_relations` where `source_type` = 'wiki page' AND `source_itemId`=? AND `target_type` = 'wiki page' AND `relation` LIKE 'tiki.link.%'";
		$result = $this->query($query, array($page));
	}

	function replace_link($pageFrom, $pageTo, $types = array()) {
		$links = $this->table('tiki_links');
		$links->insert(array(
			'fromPage' => $pageFrom,
			'toPage' => $pageTo,
		));

		global $relationlib; require_once 'lib/attributes/relationlib.php';
		foreach( $types as $type ) {
			$relationlib->add_relation( "tiki.link.$type", 'wiki page', $pageFrom, 'wiki page', $pageTo );
		}
	}

	function invalidate_cache($page) {
		unset( $this->cache_page_info[urlencode($page)] );
		$this->table('tiki_pages')->update(array(
			'cache_timestamp' => 0,
		), array(
			'pageName' => $page,
		));

		require_once 'lib/cache/pagecache.php';
		$pageCache = Tiki_PageCache::create()
			->checkMeta( 'wiki-page-output-meta-timestamp', array(
				'page' => $page ) )
			->invalidate();
	}

	/** Update a wiki page
		@param array $hash- lock_it,contributions, contributors
	 **/
	function update_page($pageName, $edit_data, $edit_comment, $edit_user, $edit_ip, $edit_description = '', $edit_minor = 0, $lang='', $is_html=null, $hash=null, $saveLastModif=null, $wysiwyg='', $wiki_authors_style='') {
		global $smarty, $prefs, $dbTiki, $histlib, $quantifylib;
		include_once ("lib/wiki/histlib.php");
		include_once ("lib/comments/commentslib.php");

		$commentslib = new Comments($dbTiki);

		if (!$edit_user) $edit_user = 'anonymous';

		$this->invalidate_cache($pageName);
		// Collect pages before modifying edit_data (see update of links below)
		$pages = $this->get_pages($edit_data, true);

		if (!$this->page_exists($pageName))
			return false;

		// Get this page information
		$info = $this->get_page_info($pageName);

		// Use largest version +1 in history table rather than tiki_page because versions used to be bugged
		// tiki_history is also bugged as not all changes get stored in the history, like minor changes
		// and changes that do not modify the body of the page. Both numbers are wrong, but the largest of
		// them both is right.
		$old_version = max(
				$info["version"],
				$histlib->get_page_latest_version($pageName)
			);

		$lastModif = $info["lastModif"];
		$user = $info["user"];
		if (!$user) $user = 'anonymous';
		$ip = $info["ip"];
		$comment = $info["comment"];
		$minor=$info["version_minor"];
		$description = $info['description'];
		$data = $info["data"];
		$willDoHistory = ($prefs['feature_wiki_history_full'] == 'y' || $data != $edit_data || $description != $edit_description || $comment != $edit_comment );
		$version = $old_version + ($willDoHistory?1:0);

		if( $prefs['quantify_changes'] == 'y' && $prefs['feature_multilingual'] == 'y' ) {
			include_once 'lib/wiki/quantifylib.php';
			$quantifylib->recordChangeSize( $info['page_id'], $version, $info['data'], $edit_data );
		}

		if ($is_html === null) {
			$html = $info['is_html'];
		} else {
			$html = $is_html ? 1 : 0;
		}
		if ($wysiwyg == '') {
			$wysiwyg = $info['wysiwyg'];
		}
		
		if( $wysiwyg == 'y' && $html != 1 ) {	// correct for html only wysiwyg
			$html = 1;
		}

		if( $html == 0 ) {
			$edit_data = str_replace( '&lt;x&gt;', '', $edit_data );
		}

		if ($html == 1 && $prefs['feature_purifier'] != 'n') {
			require_once('lib/htmlpurifier_tiki/HTMLPurifier.tiki.php');
			$edit_data = HTMLPurifier($edit_data);
		}

		if( is_null( $saveLastModif ) ) {
			$saveLastModif = $this->now;
		}

		$queryData = array(
			'description' => $edit_description,
			'data' => $edit_data,
			'comment' => $edit_comment,
			'lastModif' => (int) $saveLastModif,
			'version' => $version,
			'version_minor' => $edit_minor,
			'user' => $edit_user,
			'ip' => $edit_ip,
			'page_size' => strlen($data),
			'is_html' => $html,
			'wysiwyg' => $wysiwyg,
			'wiki_authors_style' => $wiki_authors_style,
		);
		if ($lang) {
			$queryData['lang'] = $lang;
		}
		if ($hash !== null) {
			if (!empty($hash['lock_it']) && ($hash['lock_it'] == 'y' || $hash['lock_it'] == 'on')) {
				$queryData['flag'] = 'L';
				$queryData['lockedby'] = $user;
			} else if (empty($hash['lock_it']) || $hash['lock_it'] == 'n') {
				$queryData['flag'] = '';
				$queryData['lockedby'] = '';
			}
		}
		if ($prefs['wiki_comments_allow_per_page'] != 'n') {
			if (!empty($hash['comments_enabled']) && $hash['comments_enabled'] == 'y') {
				$queryData['comments_enabled'] = 'y';
			} else if (empty($hash['comments_enabled']) || $hash['comments_enabled'] == 'n') {
				$queryData['comments_enabled'] = 'n';
			}
		}
		if (empty($hash['contributions'])) {
			$hash['contributions'] = '';
		}
		if (empty($hash['contributors'])) {
			$hash2 = '';
		} else {
			foreach ($hash['contributors'] as $c) {
				$hash3['contributor'] = $c;
				$hash2[] = $hash3;
			}
		}

		$this->table('tiki_pages')->update($queryData, array(
			'pageName' => $pageName,
		));
		$this->replicate_page_to_history($pageName);

		// Parse edit_data updating the list of links from this page
		$this->clear_links($pageName);

		// Pages collected above
		foreach ($pages as $page => $types) {
			$this->replace_link($pageName, $page, $types);
		}

		if (strtolower($pageName) != 'sandbox' && !$edit_minor) {
			$maxversions = $prefs['maxVersions'];

			if ($maxversions && ($nb = $histlib->get_nb_history($pageName)) > $maxversions) {
				// Select only versions older than keep_versions days
				$keep = $prefs['keep_versions'];

				$oktodel = $saveLastModif - ($keep * 24 * 3600);
				$query = "select `pageName` ,`version`, `historyId` from `tiki_history` where `pageName`=? and `lastModif`<=? order by `lastModif` asc";
				$result = $this->fetchAll($query,array($pageName,$oktodel),$nb - $maxversions);
				foreach ( $result as $res ) {
					$page = $res["pageName"];
					$version = $res["version"];
					$histlib->remove_version($res['pageName'], $res['version']);
				}
			}
		}

		// This if no longer checks for minor-ness of the change; sendWikiEmailNotification does that.
		if( $willDoHistory ) {
			if (strtolower($pageName) != 'sandbox') {
				if ($prefs['feature_contribution'] == 'y') {// transfer page contributions to the history
					global $contributionlib; include_once('lib/contribution/contributionlib.php');
					$query = 'select max(`historyId`) from `tiki_history`where `pageName`=? and `version`=?';
					$historyId = $this->getOne($query, array($pageName,(int) $old_version));
					$contributionlib->change_assigned_contributions($pageName, 'wiki page', $historyId, 'history', '', $pageName.'/'.$old_version, "tiki-pagehistory.php?page=$pageName&preview=$old_version");
				}
			}
			if (strtolower($pageName) != 'sandbox') {
				global $logslib; include_once('lib/logs/logslib.php');
				include_once('lib/diff/difflib.php');
				$bytes = diff2($data , $edit_data, 'bytes');
				$logslib->add_action('Updated', $pageName, 'wiki page', $bytes, $edit_user, $edit_ip, '', $this->now, $hash['contributions'], $hash2);
				if ($prefs['feature_contribution'] == 'y') {
					global $contributionlib; include_once('lib/contribution/contributionlib.php');
					$contributionlib->assign_contributions($hash['contributions'], $pageName, 'wiki page', $description, $pageName, "tiki-index.php?page=".urlencode($pageName));
				}
			}

			if ($prefs['feature_multilingual'] == 'y' && $lang ) {
				// Need to update the translated objects table when an object's language changes.
				$this->table('tiki_translated_objects')->update(array(
					'lang' => $lang,
				), array(
					'type' => 'wiki page',
					'objId' => $info['page_id'],
				));
			}

			if ($prefs['wiki_watch_minor'] != 'n' || !$edit_minor) {
				//  Deal with mail notifications.
				include_once('lib/notifications/notificationemaillib.php');
				global $histlib; include_once ("lib/wiki/histlib.php");
				$old = $histlib->get_version($pageName, $old_version);
				$foo = parse_url($_SERVER["REQUEST_URI"]);
				$machine = $this->httpPrefix( true ). dirname( $foo["path"] );
				require_once('lib/diff/difflib.php');
				$diff = diff2($old["data"] , $edit_data, "unidiff");
				sendWikiEmailNotification('wiki_page_changed', $pageName, $edit_user, $edit_comment, $old_version, $edit_data, $machine, $diff, $edit_minor, $hash['contributions'], 0, 0, $lang);
			}

			if ($prefs['feature_score'] == 'y') {
				$this->score_event($user, 'wiki_edit');
			}

		}

		require_once('lib/search/refresh-functions.php');
		refresh_index('pages', $pageName);

		$this->object_post_save( array( 'type' => 'wiki page', 'object' => $pageName ), array(
			'content' => $edit_data,
		) );
	}

	function object_post_save( $context, $data ) {
		global $prefs;

		if ( isset( $data['content'] ) && $prefs['feature_file_galleries'] == 'y') {
			global $filegallib; require_once 'lib/filegals/filegallib.php';
			$filegallib->syncFileBacklinks( $data['content'], $context );
		}

		if( isset( $data['content'] ) ) {
			$this->plugin_post_save_actions( $context, $data );
		}
	}

	/**
	 * Foreach plugin used in a object content call its save handler,
	 * if one exist, and send email notifications when it has pending
	 * status, if preference is enabled.
	 *  
	 * A plugin save handler is a function defined on the plugin file
	 * with the following format: wikiplugin_$pluginName_save()
	 * 
	 * @param array $context object type and id
	 * @param array $data
	 * @return void
	 */
	private function plugin_post_save_actions( $context, $data ) {
		require_once 'WikiParser/PluginMatcher.php';
		require_once 'WikiParser/PluginArgumentParser.php';
		global $prefs;

		$argumentParser = new WikiParser_PluginArgumentParser;

		$matches = WikiParser_PluginMatcher::match( $data['content'] );

		foreach( $matches as $match ) {
			$plugin_name = $match->getName();
			$body = $match->getBody();
			$arguments = $argumentParser->parse( $match->getArguments() );

			$dummy_output = '';
			if( $this->plugin_enabled( $plugin_name, $dummy_output ) ) {
				$status = $this->plugin_can_execute($plugin_name, $body, $arguments, true);

				// when plugin status is pending, $status equals plugin fingerprint
				if ($prefs['wikipluginprefs_pending_notification'] == 'y' && $status !== true && $status != 'rejected') {
					//TODO: create preference to enable and disable notifications
					$this->plugin_pending_notification($plugin_name, $context);
				}
				
				$this->plugin_find_implementation( $plugin_name, $body, $arguments );

				$func_name = 'wikiplugin_' . $plugin_name . '_save';

				if( function_exists( $func_name ) ) {
					$func_name( $context, $body, $arguments );
				}
			}
		}
	}

	/**
	 * Send notification by email that a plugin is waiting to be
	 * approved to everyone with permission to approve it.
	 * 
	 * @param string $plugin_name
	 * @param array $context object type and id
	 * @return void
	 */
	private function plugin_pending_notification($plugin_name, $context) {
		require_once('lib/webmail/tikimaillib.php');
		global $userlib, $prefs, $objectlib, $base_url;
		
		$object = $objectlib->get_object($context['type'], $context['object']);
		
		$mail = new TikiMail(null, $prefs['sender_email']);
		$mail->setSubject(tra("Plugin $plugin_name pending approval"));
		$mail->setHtml(tra("Plugin $plugin_name is pending approval on <a href='$base_url{$object['href']}'>{$object['name']}</a>"));
		
		$allGroups = $userlib->get_groups();
		$accessor = Perms::get($context);
		
		// list of groups with permission to approve plugin on this object
		$groups = array();
		
		foreach ($allGroups['data'] as $group) {
			$accessor->setGroups(array($group['groupName']));
			if ($accessor->plugin_approve) {
				$groups[] = $group['groupName'];
			}
		}

		$recipients = array();
		
		foreach ($groups as $group) {
			$recipients = array_merge($recipients, $userlib->get_group_users($group, 0, -1, 'email'));
		}
		
		$recipients = array_filter($recipients);
		$recipients = array_unique($recipients);

		$mail->setBcc(join(', ', $recipients));
		
		$mail->send(array($prefs['sender_email']));
	}
	
	private function plugin_find_implementation( & $implementation, & $data, & $args ) {
		if( $info = $this->plugin_alias_info( $implementation ) ) {
			$implementation = $info['implementation'];

			// Do the body conversion
			if( isset($info['body']) ) {
				if( ( isset($info['body']['input']) && $info['body']['input'] == 'ignore' )
					|| empty( $data ) )
					$data = isset($info['body']['default']) ? $info['body']['default'] : '';

				if( isset($info['body']['params']) )
					$data = $this->plugin_replace_args( $data, $info['body']['params'], $args );
			} else {
				$data = '';
			}

			// Do parameter conversion
			$params = array();
			if( isset($info['params']) ) {
				foreach( $info['params'] as $key => $value ) {
					if( is_array( $value ) && isset($value['pattern']) && isset($value['params']) ) {
						$params[$key] = $this->plugin_replace_args( $value['pattern'], $value['params'], $args );
					} else {
						// Handle simple values
						if( isset($args[$key]) )
							$params[$key] = $args[$key];
						else
							$params[$key] = $value;
					}
				}
			}

			$args = $params;

			// Attempt to find recursively
			$this->plugin_find_implementation( $implementation, $data, $args );

			return true;
		}

		return false;
	}

	function update_page_version($pageName, $version, $edit_data, $edit_comment, $edit_user, $edit_ip, $lastModif, $description = '', $lang='') {
		global $smarty;

		if (strtolower($pageName) == 'sandbox')
			return;

		// Collect pages before modifying edit_data
		$pages = $this->get_pages($edit_data, true);

		if (!$this->page_exists($pageName))
			return false;

		$history = $this->table('tiki_history');
		$history->delete(array(
			'pageName' => $pageName,
			'version' => (int) $version,
		));
		$history->insert(array(
			'pageName' => $pageName,
			'version' => (int) $version,
			'lastModif' => (int) $lastModif,
			'user' => $edit_user,
			'ip' => $edit_ip,
			'comment' => $edit_comment,
			'data' => $edit_data,
			'description' => $description,
		));

		//print("version: $version<br />");
		// Get this page information
		$info = $this->get_page_info($pageName);

		if ($version >= $info["version"]) {
			$modifications = array(
				'data' => $edit_data,
				'comment' => $edit_comment,
				'lastModif' => $this->now,
				'version' => (int) $version,
				'user' => $edit_user,
				'ip' => $edit_ip,
				'description' => $description,
				'page_size' => strlen($edit_data),
				'lang' => $lang,
			);
			$this->table('tiki_pages')->update($modifications, array(
				'pageName' => $pageName,
			));

			// Parse edit_data updating the list of links from this page
			$this->clear_links($pageName);

			// Pages are collected at the top of the function before adding slashes
			foreach ($pages as $page => $types) {
				$this->replace_link($pageName, $page, $types);
			}
		}
	}

	function get_display_timezone($_user = false) {
		global $prefs, $user;

		if ( $_user === false || $_user == $user ) {
			// If the requested timezone is the current user timezone
			$tz = $prefs['display_timezone'];
		} elseif ( $_user ) {
			// ... else, get the user timezone preferences from DB
			$tz = $this->get_user_preference($_user, 'display_timezone');
		}
		if ( ! TikiDate::TimezoneIsValidId($tz) ) {
			$tz = $prefs['server_timezone'];
		}
		if ( ! TikiDate::TimezoneIsValidId($tz) ) {
			$tz = 'UTC';
		}
		return $tz;
	}

	function get_long_date_format() {
		global $prefs;
		return $prefs['long_date_format'];
	}

	function get_short_date_format() {
		global $prefs;
		return $prefs['short_date_format'];
	}

	function get_long_time_format() {
		global $prefs;
		return $prefs['long_time_format'];
	}

	function get_short_time_format() {
		global $prefs;
		return $prefs['short_time_format'];
	}

	function get_long_datetime_format() {
		static $long_datetime_format = false;

		if (!$long_datetime_format) {
			$t = trim($this->get_long_time_format());
			if (!empty($t)) {
				$t = ' ['.$t.']';
			}
			$long_datetime_format = $this->get_long_date_format().$t;
		}

		return $long_datetime_format;
	}

	function get_short_datetime_format() {
		static $short_datetime_format = false;

		if (!$short_datetime_format) {
			$t = trim($this->get_short_time_format());
			if (!empty($t)) {
				$t = ' ['.$t.']';
			}
			$short_datetime_format = $this->get_short_date_format().$t;
		}

		return $short_datetime_format;
	}

	static function date_format2($format, $timestamp = false, $_user = false, $input_format = 5/*DATE_FORMAT_UNIXTIME*/) {
		return TikiLib::date_format($format, $timestamp, $_user, $input_format, false);
	}

	static function date_format($format, $timestamp = false, $_user = false, $input_format = 5/*DATE_FORMAT_UNIXTIME*/, $is_strftime_format = true) {
		global $tikidate, $tikilib;
		if (!is_object($tikidate)) {
			require_once('lib/tikidate.php');
			$tikidate = new TikiDate();
		}

		if ( ! $timestamp ) {
			$timestamp = time();
		}

		$tikidate->setTZbyID('UTC');
		try {
			$tikidate->setDate($timestamp);
		} catch (Exception $e) {
			return $e->getMessage();
		}

		$tz = $tikilib->get_display_timezone($_user);

		// If user timezone is not also in UTC, convert the date
		if ( $tz != 'UTC' ) {
			$tikidate->setTZbyID($tz);
		}

		return $tikidate->format($format, $is_strftime_format);
	}

	function make_time($hour,$minute,$second,$month,$day,$year) {
		global $tikidate, $tikilib, $prefs;
		if (!is_object($tikidate)) {
			require_once('lib/tikidate.php');
			$tikidate = new TikiDate();
		}
		$display_tz = $tikilib->get_display_timezone();
		if ( $display_tz == '' ) $display_tz = 'UTC';
		$tikidate->setTZbyID($display_tz);
		$tikidate->setLocalTime($day,$month,$year,$hour,$minute,$second,0);
		return $tikidate->getTime();
	}

	function get_long_date($timestamp, $user = false) {
		return $this->date_format($this->get_long_date_format(), $timestamp, $user);
	}

	function get_short_date($timestamp, $user = false) {
		return $this->date_format($this->get_short_date_format(), $timestamp, $user);
	}

	function get_long_time($timestamp, $user = false) {
		return $this->date_format($this->get_long_time_format(), $timestamp, $user);
	}

	function get_short_time($timestamp, $user = false) {
		return $this->date_format($this->get_short_time_format(), $timestamp, $user);
	}

	function get_long_datetime($timestamp, $user = false) {
		return $this->date_format($this->get_long_datetime_format(), $timestamp, $user);
	}

	function get_short_datetime($timestamp, $user = false) {
		return $this->date_format($this->get_short_datetime_format(), $timestamp, $user);
	}
	
	function format_sql_date($sqlstamp) {
		global $user, $tikilib;
		$tz = $tikilib->get_display_timezone($user);
		$unixstamp = strtotime($sqlstamp . $tz);
		$format = $tikilib->get_short_date_format();
		$date = strftime($format, $unixstamp);
		return $date;
	}

	/**
		Per http://www.w3.org/TR/NOTE-datetime
	 */
	function get_iso8601_datetime($timestamp, $user = false) {
		return $this->date_format('%Y-%m-%dT%H:%M:%S%O', $timestamp, $user);
	}

	function get_compact_iso8601_datetime($timestamp, $user = false) {
		// no dashes and no tz info - latter should be fixed
		return $this->date_format('%Y%m%dT%H%M%S', $timestamp, $user);
	}

	static function list_languages($path = false, $short=null, $all=false) {
		$languages = array();

		if (!$path)
			$path = "lang";

		if (!is_dir($path))
			return array();

		$h = opendir($path);

		while ($file = readdir($h)) {
			if (strpos($file,'.') === false && $file != 'CVS' && $file != 'index.php' && is_dir("$path/$file") && file_exists("$path/$file/language.php")) {
				$languages[] = $file;
			}
		}

		closedir ($h);

		// Format and return the list
		return TikiLib::format_language_list($languages, $short, $all);
	}

	function is_valid_language( $language ) {
		return preg_match("/^[a-zA-Z-_]*$/", $language)
			&& file_exists('lang/' . $language . '/language.php');
	}

	/**
	 * @return  array of css files in the style dir
	 */
	function list_styles() {
		global $tikidomain;
		global $csslib; include_once("lib/csslib.php");

		$sty = array();
		$style_base_path = $this->get_style_path();	// knows about $tikidomain
		
		if ($style_base_path) {
			$sty = $csslib->list_css($style_base_path);
		}
		
		if ($tikidomain) {
			$sty = array_unique(array_merge($sty, $csslib->list_css('styles')));
		}
		foreach($sty as &$s) {	// add the .css back onto the end of the style names
			$s .= '.css';		// i started to change this but it hits too many places
		}						// Another TODO for 4.0 (sorry)
		sort($sty);
		return $sty;
		
		/* What is this $tikidomain section?
		 * Some files that call this method used to list styles without considering
		 * $tikidomain, now they do. They're listed below:
		 *
		 *  tiki-theme_control.php
		 *  tiki-theme_control_objects.php
		 *  tiki-theme_control_sections.php
		 *  tiki-my_tiki.php
		 *  modules/mod-switch_theme.php
		 *
		 *  lfagundes
		 *  
		 *  Tiki 3.0 - now handled by get_style_path()
		 *  jonnybradley
		 */

	}

	/**
	 * @param $a_style - main style (e.g. "thenews.css")
	 * @return array of css files in the style options dir
	 */
	function list_style_options($a_style='') {
		global $prefs;
		global $csslib; include_once ("lib/csslib.php");

		if (empty($a_style)) {
			$a_style = $prefs['style'];
		}

		$sty = array();
		$option_base_path = $this->get_style_path($a_style).'options/';
		
		if (is_dir($option_base_path)) {
			$sty = $csslib->list_css($option_base_path);
		}

		if (count($sty)) {
			foreach($sty as &$s) {	// add .css back as above
				$s .= '.css';
			}
			sort($sty);
			return $sty;
		} else {
			return false;
		}
	}
	
	/**
	 * @param $stl - main style (e.g. "thenews.css")
	 * @return string - style passed in up to - | or . char (e.g. "thenews")
	 */
	function get_style_base($stl) {
		$parts = preg_split('/[\-\.]/', $stl);
		if (count($parts) > 0) {
			return $parts[0];
		} else {
			return '';
		}
	}
	
	/**
	 * @param $stl - main style (e.g. "thenews.css" - can be empty to return main styles dir)
	 * @param $opt - optional option file name (e.g. "purple.css")
	 * @param $filename - optional filename to look for (e.g. "purple.png")
	 * @return path to dir or file if found or empty if not - e.g. "styles/mydomain.tld/thenews/options/purple/"
	 */
	function get_style_path($stl = '', $opt = '', $filename = '') {
		global $tikidomain;

		$path = '';
		$dbase = '';
		if ($tikidomain && is_dir("styles/$tikidomain")) {
			$dbase = $tikidomain.'/';
		}
		
		$sbase = '';
		if (!empty($stl)) {
			$sbase = $this->get_style_base($stl).'/';
		}
		if (!is_dir('styles/'.$dbase.$sbase)) {	// if the style dir doesn't exist in tikidomain, use root/styles
			$dbase = '';
		}
		
		$obase = '';
		if (!empty($opt)) {
			$obase = 'options/';
			if ($opt != $filename) {	// exception for getting option.css as it doesn't live in it's own dir
				$obase .= substr($opt, 0, strlen($opt) - 4).'/';
			}
		}
		
		if (is_dir('styles/'.$dbase.$sbase)) {
			if (empty($filename)) {
				if (is_dir('styles/'.$dbase.$sbase.$obase)) {
					$path = 'styles/'.$dbase.$sbase.$obase;
				} else {
					$path = 'styles/'.$dbase.$sbase;	// fall back to "parent" style dir if no option one
				}
			} else {
				if (is_file('styles/'.$dbase.$sbase.$obase.$filename)) {
					$path = 'styles/'.$dbase.$sbase.$obase.$filename;
				} else if (is_file('styles/'.$dbase.$sbase.$filename)) {	// try "parent" style dir if no option one
					$path = 'styles/'.$dbase.$sbase.$filename;
				} else if (is_file('styles/'.$sbase.$obase.$filename)) {	// try non-tikidomain dirs if not found
					$path = 'styles/'.$sbase.$obase.$filename;
				} else if (is_file('styles/'.$sbase.$filename)) {
					$path = 'styles/'.$sbase.$filename;	// fall back to "parent" style dir if no option one
				}
			}
		}
		return $path;
	}

	/**
	 * list_slide_styles 
	 * 
	 * @access public
	 * @return array. List of the slide styles located in styles/slideshow
	 */
	function list_slide_styles()
	{
	  $slide_styles = array();
	  $h = opendir("styles/slideshows");
	  while ($file = readdir($h)) {
		if (strstr($file, "css")) {
		  $slide_styles[] = $file;
		}
	  }
	  closedir($h);
	  return $slide_styles;
	}


	// Comparison function used to sort languages by their name in the
	// current locale.
	static function formatted_language_compare($a, $b) {
		return strcasecmp($a['name'], $b['name']);
	}
	// Returns a list of languages formatted as a twodimensionel array
	// with 'value' being the language code and 'name' being the name of
	// the language.
	// if $short is 'y' returns only the localized language names array
	static function format_language_list($languages, $short=null, $all=false) {
		// The list of available languages so far with both English and
		// translated names.
		global $langmapping, $prefs;
		include("lang/langmapping.php");
		$formatted = array();

		// run through all the language codes:
		if (isset($short) && $short == "y") {
			foreach ($languages as $lc) {
				if ( empty($prefs['available_languages'] ) || (!$all and in_array($lc,$prefs['available_languages']))) {
					if (isset($langmapping[$lc]))
						$formatted[] = array('value' => $lc, 'name' => $langmapping[$lc][0]);
					else
						$formatted[] = array('value' => $lc, 'name' => $lc);
				}
				usort($formatted, array('TikiLib', 'formatted_language_compare'));
			}
			return $formatted;
		}
		foreach ($languages as $lc) {
			if (empty($prefs['available_languages']) || (!$all and in_array($lc,$prefs['available_languages'])) or $all) {
				if (isset($langmapping[$lc])) {
					// known language
					if ($langmapping[$lc][0] == $langmapping[$lc][1]) {
						// Skip repeated text, 'English (English, en)' looks silly.
						$formatted[] = array(
								'value' => $lc,
								'name' => $langmapping[$lc][0] . " ($lc)"
								);
					} else {
						$formatted[] = array(
								'value' => $lc,
								'name' => $langmapping[$lc][1] . " (" . $langmapping[$lc][0] . ', ' . $lc . ")"
								);
					}
				} else {
					// unknown language
					$formatted[] = array(
							'value' => $lc,
							'name' => tra("Unknown language"). " ($lc)"
							);
				}
			}
		}

		// Sort the languages by their name in the current locale
		usort($formatted, array('TikiLib', 'formatted_language_compare'));
		return $formatted;
	}

	function get_language($user = false) {
		global $prefs;
		static $language = false;

		if (!$language) {
			if ($user) {
				$language = $this->get_user_preference($user, 'language', 'default');
				if (!$language || $language == 'default') {
					$language = $prefs['language'];
				}
			} else {
				$language = $prefs['language'];
			}
		}
		return $language;
	}

	function read_raw($text) {
		$file = explode("\n",$text);
		$back = '';
		foreach ($file as $line) {
			$r = $s = '';
			if (substr($line,0,1) != "#") {
				if( preg_match("/^\[([A-Z0-9]+)\]/",$line,$r) ) {
					$var = strtolower($r[1]);
				}
				if (isset($var) and (preg_match("/^([-_\/ a-zA-Z0-9]+)[ \t]+[:=][ \t]+(.*)/",$line,$s))) {
					$back[$var][trim($s[1])] = trim($s[2]);
				}
			}
		}
		return $back;
	}


	function httpScheme() {
		global $url_scheme;     
		return $url_scheme;
	}

	function httpPrefix( $isUserSpecific = false ) {
		global $url_scheme, $url_host, $url_port, $prefs;

		if( $isUserSpecific && $prefs['https_external_links_for_users'] == 'y' ) {
			$scheme = 'https';
		} else {
			$scheme = $url_scheme;
		}

		return $scheme.'://'.$url_host.(($url_port!='')?":$url_port":'');    
	}

	function tikiUrl( $relative, $args = array() ) {
		global $tikiroot;

		$base = $this->httpPrefix() . $tikiroot . $relative;

		if( count( $args ) ) {
			$base .= '?';
			$base .= http_build_query( $args, '', '&' );
		}

		return $base;
	}

	function distance($lat1,$lon1,$lat2,$lon2) {
		// This function uses a pure spherical model
		// it could be improved to use the WGS84 Datum
		// Franck Martin
		$lat1rad=deg2rad($lat1);
		$lon1rad=deg2rad($lon1);
		$lat2rad=deg2rad($lat2);
		$lon2rad=deg2rad($lon2);
		$distance=6367*acos(sin($lat1rad)*sin($lat2rad)+cos($lat1rad)*cos($lat2rad)*cos($lon1rad-$lon2rad));
		return($distance);
	}

	/**
	 * returns a list of usergroups where the user is a member and the group has the right perm
	 * sir-b
	 **/
	function get_groups_to_user_with_permissions($user,$perm) {
		$userid = $this->get_user_id($user);
		$query = "SELECT DISTINCT `users_usergroups`.`groupName` AS `groupName`";
		$query.= "FROM  `users_grouppermissions`, `users_usergroups` ";
		$query.= "WHERE `users_usergroups`.`userId` = ? AND ";
		$query.= "`users_grouppermissions`.`groupName` = `users_usergroups`.`groupName` AND ";
		$query.= "`users_grouppermissions`.`permName` = ? ";
		$query.= "ORDER BY `groupName`";
		return $this->fetchAll($query, array((int)$userid, $perm));
	}

	function other_value_in_tab_line($tab, $valField1, $field1, $field2) {
		foreach ($tab as $line) {
			if ($line[$field1] == $valField1)
				return $line[$field2];
		}
	}

	function get_attach_hash_file_name($file_name) {
		global $prefs;
		do {
			$fhash = md5($file_name.date('U').rand());
		} while (file_exists($prefs['w_use_dir'].$fhash));
		return $fhash;
		}
	function attach_file($file_name, $file_tmp_name, $store_type) {
		global $prefs;
		$tmp_dest = $prefs['tmpDir'] . "/" . $file_name.".tmp";
		if (!move_uploaded_file($file_tmp_name, $tmp_dest))
			return array("ok"=>false, "error"=>tra('Errors detected'));
		$fp = fopen($tmp_dest, "rb");
		$data = '';
		$fhash = '';
		$chunk = '';
		if ($store_type == 'dir') {
			$fhash = $this->get_attach_hash_file_name($file_name);
			$fw = fopen($prefs['w_use_dir'].$fhash, "wb");
			if (!$fw)
				return array("ok"=>false, "error"=>tra('Cannot write to this file:').$prefs['w_use_dir'].$fhash);
		}
		while(!feof($fp)) {
			$chunk = fread($fp, 8192*16);

			if ($store_type == 'dir') {
				fwrite($fw, $chunk);
			}
			$data .= $chunk;
		}
		fclose($fp);
		unlink($tmp_dest);
		if ($store_type == 'dir') {
			fclose($fw);
			$data = "";
		}
		return array("ok"=>true, "data"=>$data, "fhash"=>$fhash);
	}

	/* to get the length of a data without the quoted part (very
		 approximative)  */
	function strlen_quoted($data) {
		global $prefs;
		if ($prefs['feature_use_quoteplugin'] != 'y') {
			$data = preg_replace('/^>.*\\n?/m', '', $data);
		} else {
			$data = preg_replace('/{QUOTE\([^\)]*\)}.*{QUOTE}/Ui', '', $data);
		}
		return strlen($data);
	}

	function list_votes($id, $offset=0, $maxRecords=-1, $sort_mode='user_asc', $find='', $table='', $column='', $from='', $to='') {
		$mid = 'where  `id`=?';
		$bindvars[] = $id;
		$select = '';
		$join = '';
		if (!empty($find)) {
			$mid .= ' and (`user` like ? or `title` like ? or `ip` like ?)';
			$bindvars[] = '%'.$find.'%';
			$bindvars[] = '%'.$find.'%';
			$bindvars[] = '%'.$find.'%';
		}
		if (!empty($from) && !empty($to)) {
			$mid .= ' and ((time >= ? and time <= ?) or time = ?)';
			$bindvars[] = $from;
			$bindvars[] = $to;
			$bindvars[] = 0;
		}
		if (!empty($table) && !empty($column)) {
			$select = ", `$table`.`$column` as title";
			$join = "left join `$table` on (`tiki_user_votings`.`optionId` = `$table`.`optionId`)";
		}
		$query = "select * $select from `tiki_user_votings` $join $mid order by ".$this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_user_votings` $join $mid";
		$ret = $this->fetchAll($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	/**
	  *  Returns explicit message on upload problem
	  *
	  *	@params: $iError: php status of the file uploading (documented in http://uk2.php.net/manual/en/features.file-upload.errors.php )
	  *
	  */
	function uploaded_file_error($iError) {
		switch($iError) {
			case UPLOAD_ERR_OK: return tra('The file was uploaded with success.');
			case UPLOAD_ERR_INI_SIZE : return tra('The uploaded file exceeds the upload_max_filesize directive in php.ini.');
			case UPLOAD_ERR_FORM_SIZE: return tra('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.');
			case UPLOAD_ERR_PARTIAL: return tra('The file you are trying upload was only partially uploaded.');
			case UPLOAD_ERR_NO_FILE: return tra('No file was uploaded. Was a file selected ?');
			case UPLOAD_ERR_NO_TMP_DIR: return tra('A temporary folder is missing.');
			case UPLOAD_ERR_CANT_WRITE: return tra('Failed to write file to disk.');
			case UPLOAD_ERR_EXTENSION: return tra('File upload stopped by extension.');

			default: return tra('Unknown error.');
		}
	}
	
	// from PHP manual (ini-get function example)
	/**
	 * @param string $val		php.ini key returning memory string i.e. 32M
	 * @return int				size in bytes
	 */
	function return_bytes( $val ) {
		$val = trim($val);
		$last = strtolower($val{strlen($val)-1});
		switch ( $last ) {
			// The 'G' modifier is available since PHP 5.1.0
			case 'g': $val *= 1024;
			case 'm': $val *= 1024;
			case 'k': $val *= 1024;
		}
		return $val;
	}

	/**
	 * @return int	bytes of memory available for PHP
	 */
	function get_memory_avail() {
		return $this->get_memory_limit() - memory_get_usage(true);
	}
	
	function get_memory_limit() {
		return $this->return_bytes(ini_get('memory_limit'));
	}

	function get_flags($with_names = false, $translate = false, $sort_names = false) {
		$flags = array();
		$h = opendir("img/flags/");
		while ($file = readdir($h)) {
			if (strstr($file, ".gif")) {
				$parts = explode('.', $file);
				$flags[] = $parts[0];
			}
		}
		closedir ($h);
		sort($flags);

		if ( $with_names ) {
			$ret = array();
			$names = array();
			foreach ( $flags as $f ) {
				$ret[$f] = strtr($f, '_', ' ');
				if ( $translate ) {
					$ret[$f] = tra($ret[$f]);
				}
				if ($sort_names) {
					$names[$f] = strtolower($this->take_away_accent($ret[$f]));
				}
			}
			if ( $sort_names ) {
				array_multisort($names, $ret);
			}
			return $ret;
		}
		return $flags;
	}

	
	function get_snippet($data, $is_html='n', $highlight='', $length=240, $start='', $end='') {
		global $prefs;
		if ($prefs['search_parsed_snippet'] == 'y') {
			$_REQUEST['redirectpage'] = 'y'; //do not interpret redirect
			$data = $this->parse_data($data, array('is_html' => $is_html, 'stripplugins' => true, 'parsetoc' => true));
			$data = strip_tags($data, '<b><i><em><strong><pre><code>');
		}
		if ($length > 0) {
			if (function_exists('mb_substr')) 
				return mb_substr($data, 0, $length);
			else
				return substr($data, 0, $length);
		}
		if (!empty($start) && ($i = strpos($data, $start))) {
			$data = substr($data, $i+strlen($start));
		}
		if (!empty($end) && ($i = strpos($data, $end))) {
			$data = substr($data, 0, $i);
		}
		return $data;
	}

	static function htmldecode($string, $quote_style = ENT_COMPAT, $translation_table = HTML_ENTITIES) {
		if ( $translation_table == HTML_ENTITIES && version_compare(phpversion(), '5', '>=') ) {
			// Use html_entity_decode with UTF-8 only with PHP 5.0 or later, since
			//   this function was available in PHP4 but _without_ multi-byte charater sets support
			$string = html_entity_decode($string, $quote_style, 'utf-8');

		} elseif ( $translation_table == HTML_SPECIALCHARS && version_compare(phpversion(), '5.1.0', '>=') ) {
			// Only available in PHP 5.1.0 or later
			$string = htmlspecialchars_decode($string, $quote_style);

		} else {
			// For compatibility purposes with php < 5
			$trans_tbl = array_flip(get_html_translation_table($translation_table));

			// Not translating double quotes
			if ($quote_style & ENT_NOQUOTES) {
				// Remove double quote from translation table
				unset($trans_tbl['&quot;']);
			}

			$string = strtr($string, $trans_tbl);
			if (function_exists('recode_string')) {
				$string = recode_string('iso-8859-15..utf-8', $string);
			}
		}

		$string = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string);
		$string = preg_replace('~&#([0-9]+);~e', 'chr(\\1)', $string);

		return $string;
	}

	function take_away_accent($str) {
		$accents = explode(' ', 'À Á Â Ã Ä Å Ç È É Ê Ë Ì Í Î Ï Ð Ñ Ò Ó Ô Õ Ö Ù Ú Û Ü Ý ß à á â ã ä å ç è é ê ë ì í î ï ñ ò ó ô õ ö ù ú û ü ý Æ æ');
		$convs =   explode(' ', 'A A A A A A C E E E E I I I I D N O O O O O U U U U Y s a a a a a a c e e e e i i i i n o o o o o u u u u y AE ae');
		return str_replace($accents, $convs, $str);
	}

	/* return the positions in data where the hdr-nth header is find
	 */
function get_wiki_section($data, $hdr) {
	$start = 0;
	$end = strlen($data);
	$lines = explode("\n", $data);
	$header = 0;
	$pp_level = 0;
	$np_level = 0;
	for ($i = 0, $count_lines = count($lines); $i < $count_lines; ++$i) {
		$pp_level += preg_match ('/~pp~/',$lines[$i]);
		$pp_level -= preg_match ('/~\/pp~/',$lines[$i]);
		$np_level += preg_match ('/~np~/',$lines[$i]);
		$np_level -= preg_match ('/~\/np~/',$lines[$i]);
		// We test if we are inside nonparsed or pre section to ignore !*
		if ($pp_level%2 == 0 and $np_level%2 == 0) {
			if (substr($lines[$i], 0, 1) == '!') {
				++$header;
				if ($header == $hdr) { // we are on it - now find the next header at same or lower level
					$level = $this->how_many_at_start($lines[$i], '!');
					$end = strlen($lines[$i]) + 1;
					for (++$i; $i < $count_lines; ++$i) {
						if (substr($lines[$i], 0, 1) == '!' && $level >= $this->how_many_at_start($lines[$i], '!')) {
							return (array($start, $end));
						}
						$end += strlen($lines[$i]) + 1;
					}
					break;
				}
			}
		}
		$start += strlen($lines[$i]) + 1;
	}
	return (array($start, $end));
}

/**
 * \brief Function to embed a flash object (using JS method by default when JS in user's browser is detected)
 *
 * So far it's being called from wikiplugin_flash.php and tiki-edit_banner.php
 *
 * @param javascript = y or n to force to generate a version with javascript or not, ='' user prefs
 */
	function embed_flash($params, $javascript='', $flashvars = false) {
		global $prefs;
		global $headerlib; include_once('lib/headerlib.php');
		if (! isset($params['movie']) ) {
			return false;
		}
		$defaults = array(
						  'width' => 425,
						  'height' => 350,
						  'quality' => 'high',
						  'version' => '9.0.0',
						  'wmode' => 'transparent',
						  );
		$params = array_merge( $defaults, $params );
		if (preg_match('/^(\/|https?:)/', $params['movie'])) {
			$params['allowscriptaccess'] = 'always';
		}
		
		if ( ((empty($javascript) && $prefs['javascript_enabled'] == 'y') || $javascript == 'y')) {
			$myId = (!empty($params['id'])) ? ($params['id']) : 'wp-flash-' . md5($params['movie']);
			$movie = '"'.$params['movie'].'"';
			$div = json_encode( $myId );
			$width = (int) $params['width'];
			$height = (int) $params['height'];
			$version = json_encode( $params['version'] );
			unset( $params['movie'], $params['width'], $params['height'], $params['version'] );
			$params = json_encode($params);
			
			if (!$flashvars) {
				$flashvars = '{}';
			} else {
				$flashvars = json_encode($flashvars);
				$flashvars = str_replace('\\/', '/', $flashvars);
			}
			$js = <<<JS
swfobject.embedSWF( $movie, $div, $width, $height, $version, 'lib/swfobject/expressInstall.swf', $flashvars, $params, {} );
JS;
			$headerlib->add_js( $js );
			return "<div id=\"$myId\">" . tra('Flash player not available.') . "</div>";
		} else { // link on the movie will not work with IE6
			extract ($params,EXTR_SKIP);
			$asetup = "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0\" width=\"$width\" height=\"$height\">";
			$asetup .= "<param name=\"movie\" value=\"$movie\" />";
			$asetup .= "<param name=\"quality\" value=\"$quality\" />";
			$asetup .= "<param name=\"wmode\" value=\"transparent\" />";
			if (!empty($params['allowscriptaccess'])) {
				$asetup .= "<param name=\"allowscriptaccess\" value=\"always\" />";
			}
			if (!empty($params['allowFullScreen'])) {
				$asetup .= '<param name="allowFullScreen" value="' . $params['allowFullScreen'] . '"></param>';
			}
			$asetup .= "<embed src=\"$movie\" quality=\"$quality\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" type=\"application/x-shockwave-flash\" width=\"$width\" height=\"$height\" wmode=\"transparent\"></embed></object>";
			return $asetup;
		}
	}

	// TikiWiki version of parse_str, that:
	//  - uses a workaround for a bug in PHP 5.2.0
	//  - Handle the value of magic_quotes_gpc to stripslashes when needed (as already done for GET/POST/... in tiki-setup_base.php)
	static function parse_str($str, &$arr) {
		parse_str($str, $arr);

		/* From PHP Manual comments (quoting Vladimir Kornea):
		 *   parse_str() contained a bug (#39763) in PHP 5.2.0 that caused it to apply magic quotes twice.
		 * This bug was marked as fixed in the release notes of PHP 5.2.1, but there were apparently some
		 * issues with getting the fix through CVS on time, as our install of PHP 5.2.1 was still affected by it.
		 */
		if ( version_compare(PHP_VERSION, '5.2.0', '>=') && version_compare(PHP_VERSION, '5.2.1', '<') ) {
			$arr = array_map('stripslashes', $arr);
		}

		// parse_str's behavior also depends on magic_quotes_gpc...
		global $magic_quotes_gpc;
		if ( $magic_quotes_gpc ) remove_gpc($arr);
	}

	function bindvars_to_sql_in(&$bindvars, $remove_duplicates = false, $cast_to_int = false) {
		if ( ! is_array($bindvars) ) return false;
		$query = ' IN (';
		$bindvars2 = array();
		foreach ( $bindvars as $id ) {
			if ( $cast_to_int ) $id = (int)$id;
			if ( $remove_duplicates && in_array($id, $bindvars2) ) continue;
			$bindvars2[] = $id;
			if ( $query == '' ) $query .= ',';
			$query .= '?';
		}
		if ( $remove_duplicates ) $bindvars = $bindvars2;
		return ' IN (' . $query . ')';
	}

	function get_jail() {
		global $prefs;
		if( $prefs['feature_categories'] == 'y' && ! empty( $prefs['category_jail'] ) && $prefs['category_jail'] != array(0 => 0) ) {
			// if jail is zero, we should allow non-categorized objects to be seen as well, i.e. consider as no jail
			global $categlib; require_once ('lib/categories/categlib.php');
			$key = $prefs['category_jail'];
			$categories = $prefs['category_jail'];
			if( $prefs['expanded_category_jail_key'] != $key ) {
				$additional = array();

				if (!empty($categories)) {
					foreach( $categories as $categId ) {
						$desc = $categlib->get_category_descendants( $categId );
						$additional = array_merge( $additional, $desc );
					}
				}

				$prefs['expanded_category_jail'] =
					$_SESSION['s_prefs']['expanded_category_jail'] = implode( ',', $additional );
				$_SESSION['s_prefs']['expanded_category_jail_key'] = $key;

				return $additional;
			}

			return explode( ',', $prefs['expanded_category_jail'] );
		} else {
			return array();
		}
	}

	// Determine if the provided IP address is valid or not.
	// Currently only supports IPV4.
	function isValidIP($ip, $ver = 4) {
		$result = false;
	
		$octets = explode('.', $ip);
		if (count($octets) == 4) {
			for ($c = 0; $c < 4; $c++) {
				if ($octets[$c] < 0 || $octets[$c] > 255) {
					$result = false;
					break;
				} else {
					$result = true;
				}
			}
		}

		return $result;
	}

	/**
	 * Returns the approved page name or null if not a staging page or staging is disabled.
	 */
	function get_approved_page( $page ) {
		global $prefs;
		$prefixLen = strlen( $prefs['wikiapproval_prefix'] );
		$prefix = substr( $page, 0, $prefixLen );

		if( $prefs['feature_wikiapproval'] == 'y' && $prefix == $prefs['wikiapproval_prefix'] ) {
			return substr($page, $prefixLen );
		}
	}

	function get_staging_page( $page ) {
		global $prefs;
		$prefixLen = strlen( $prefs['wikiapproval_prefix'] );
		$prefix = substr( $page, 0, $prefixLen );

		if( $prefs['feature_wikiapproval'] == 'y' && $prefix != $prefs['wikiapproval_prefix'] ) {
			return $prefs['wikiapproval_prefix'] . $page;
		}
	}

	function get_approved_page_or_self( $page ) {
		if( $app = $this->get_approved_page( $page ) ) {
			return $app;
		} else {
			return $page;
		}
	}

	protected function rename_object( $type, $old, $new ) {
		global $prefs;

		// comments
		$this->table('tiki_comments')->updateMultiple(array('object' => $new), array(
			'object' => $old,
			'objectType' => $type,
		));

		// Move email notifications
		$oldId = str_replace( $type, ' ', '' ) . $old;
		$newId = str_replace( $type, ' ', '' ) . $new;
		$this->table('tiki_user_watches')->updateMultiple(array('object' => $newId), array(
			'object' => $oldId,
		));
		$this->table('tiki_group_watches')->updateMultiple(array('object' => $newId), array(
			'object' => $oldId,
		));

		// theme_control_objects(objId,name)
		$oldId = md5($type . $old);
		$newId = md5($type . $new);
		$this->table('tiki_theme_control_objects')->updateMultiple(array('objId' => $newId, 'name' => $new), array(
			'objId' => $oldId,
		));

		// polls
		if ($prefs['feature_polls'] == 'y') {
			$query = "update `tiki_polls` tp inner join `tiki_poll_objects` tpo on tp.`pollId` = tpo.`pollId` inner join `tiki_objects` tob on tpo.`catObjectId` = tob.`objectId` set tp.`title`=? where tp.`title`=? and tob.`type` = ?";
			$this->query($query, array( $new, $old, $type ) );
		}

		// Move custom permissions
		$oldId = md5($type . strtolower($old));
		$newId = md5($type . strtolower($new));
		$this->table('users_objectpermissions')->updateMultiple(array('objectId' => $newId), array(
			'objectId' => $oldId,
			'objectType' => $type,
		));

		// Logs
		if ($prefs['feature_actionlog'] == 'y') {
			global $logslib; include_once('lib/logs/logslib.php');
			$logslib->add_action('Renamed', $new, 'wiki page', 'old='.$old.'&new='.$new, '', '', '', '', '', array(array('rename'=>$old)));
			$logslib->rename($type, $old, $new);
		}

		// Attributes
		$this->table('tiki_object_attributes')->updateMultiple(array('itemId' => $new), array(
			'itemId' => $old,
			'type' => $type,
		));
		$this->table('tiki_object_relations')->updateMultiple(array('source_itemId' => $new), array(
			'source_itemId' => $old,
			'source_type' => $type,
		));
		$this->table('tiki_object_relations')->updateMultiple(array('target_itemId' => $new), array(
			'target_itemId' => $old,
			'target_type' => $type,
		));

		global $menulib; include_once('lib/menubuilder/menulib.php');
		$menulib->rename_wiki_page($old, $new);
	}
	
	// This function gets the prefix alias page name e.g. Org:230 for the pretty tracker
	// wiki page corresponding to a tracker item (230 in the example) using prefix aliases
	// Returns false if no such page is found.
	function get_trackeritem_pagealias($itemId) {
		$query = "select `trackerId` from `tiki_tracker_items` where `itemId` = ?";
		$trackerId = $this->getOne($query, array($itemId));

		global $semanticlib; require_once('lib/wiki/semanticlib.php');
		$t_links = $semanticlib->getLinksUsing('trackerid', array( 'toPage' => $trackerId ) );

		if (count($t_links)) {
			$p_links = $semanticlib->getLinksUsing('prefixalias', array( 'fromPage' => $t_links[0]['fromPage'] ) );
			if (count($p_links)) {
				$ret = $p_links[0]['toPage'] . $itemId;
				return $ret;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
        
}
// end of class ------------------------------------------------------

// function to check if a file or directory is in the path
// returns FALSE if incorrect
// returns the canonicalized absolute pathname otherwise
function inpath($file,$dir) {
	$realfile=realpath($file);
	$realdir=realpath($dir);
	if (!$realfile) return (FALSE);
	if (!$realdir) return (FALSE);
	if (substr($realfile,0,strlen($realdir))!= $realdir) {
		return(FALSE);
	} else {
		return($realfile);
	}
}

function compare_links($ar1, $ar2) {
	return $ar1["links"] - $ar2["links"];
}

function compare_backlinks($ar1, $ar2) {
	return $ar1["backlinks"] - $ar2["backlinks"];
}

function r_compare_links($ar1, $ar2) {
	return $ar2["links"] - $ar1["links"];
}

function r_compare_backlinks($ar1, $ar2) {
	return $ar2["backlinks"] - $ar1["backlinks"];
}

function compare_images($ar1, $ar2) {
	return $ar1["images"] - $ar2["images"];
}

function r_compare_images($ar1, $ar2) {
	return $ar2["images"] - $ar1["images"];
}

function compare_files($ar1, $ar2) {
	return $ar1["files"] - $ar2["files"];
}

function r_compare_files($ar1, $ar2) {
	return $ar2["files"] - $ar1["files"];
}

function compare_versions($ar1, $ar2) {
	return $ar1["versions"] - $ar2["versions"];
}

function r_compare_versions($ar1, $ar2) {
	return $ar2["versions"] - $ar1["versions"];
}

function compare_changed($ar1, $ar2) {
	return $ar1["lastChanged"] - $ar2["lastChanged"];
}

function r_compare_changed($ar1, $ar2) {
	return $ar2["lastChanged"] - $ar1["lastChanged"];
}

function chkgd2() {
	if (!isset($_SESSION['havegd2'])) {
#   TODO test this logic in PHP 4.3
#   if (version_compare(phpversion(), "4.3.0") >= 0) {
#  $_SESSION['havegd2'] = true;
#   } else {
	ob_start();
	phpinfo (INFO_MODULES);
	$_SESSION['havegd2'] = preg_match('/GD Version.*2.0/', ob_get_contents());
	ob_end_clean();
# }
	}

	return $_SESSION['havegd2'];
}


function detect_browser_language() {
	global $prefs;
	// Get supported languages
	if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
		$supported = preg_split('/\s*,\s*/', preg_replace('/;q=[0-9.]+/','',$_SERVER['HTTP_ACCEPT_LANGUAGE']));
	else
		return '';

	// Get available languages
	$available = array();
	$available_aprox = array();

	if (is_dir("lang")) {
		$dh = opendir("lang");
		while ($lang = readdir($dh)) {
			if (!strpos($lang,'.') and is_dir("lang/$lang") and file_exists("lang/$lang/language.php") and (empty($prefs['available_languages']) || in_array($lang, $prefs['available_languages']))) {
				$available[strtolower($lang)] = $lang;
				$available_aprox[substr(strtolower($lang), 0, 2)] = $lang;
			}
		}
	}

	// Check better language
	// Priority has been changed in 2.0 to that defined in RFC 4647
	$aproximate_lang = '';
	foreach ($supported as $supported_lang) {
		$lang = strtolower($supported_lang);
		if (in_array($lang, array_keys($available))) {
			// exact match is always good 
			return $available[$lang];
		} elseif (in_array($lang, array_keys($available_aprox))) {
			// otherwise if supported language matches any available dialect, ok also
			return $available_aprox[$lang];
		} elseif ($aproximate_lang == '') {
			// otherwise if supported dialect matches language, store as possible fallback 
			$lang = substr($lang, 0, 2);
			if (in_array($lang, array_keys($available_aprox))) {
				$aproximate_lang = $available_aprox[$lang];
			}
		}
	}

	return $aproximate_lang;
}

function validate_email($email) {
	global $prefs;
	require_once 'lib/core/Zend/Validate/EmailAddress.php';
	$validate = new Zend_Validate_EmailAddress( Zend_Validate_Hostname::ALLOW_ALL );
	
	return $validate->isValid( $email );
}

/* Editor configuration
	 Local Variables:
	 tab-width: 4
	 c-basic-offset: 4
End:
 * vim: fdm=marker tabstop=4 shiftwidth=4 noet:
 */
