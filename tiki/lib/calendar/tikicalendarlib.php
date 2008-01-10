<?php

class TikiCalendarLib extends TikiLib {

	function TikiCalendarLib($db) {
		$this->TikiLib($db);
	}

	function list_tiki_items($tikiobj, $user, $tstart, $tstop, $offset, $maxRecords, $sort_mode, $find) {
		global $user;
		$ret = array();
		if ( ! is_array($tikiobj) ) return $ret;
		$tikiobj =& array_unique($tikiobj);
		if ( in_array('wiki', $tikiobj) ) {
			$tikiobj[] = 'wiki page';
			$tikiobj[] = 'wiki comment';
		}

		foreach ( $tikiobj as $type ) {
			if ( $type != '' && $type != 'wiki' ) {
				$objectType = ( $type == 'wiki comment' ) ? 'wiki page' : $type;
				$result = $this->get_object_cal_infos($type, array($tstart, $tstop, $objectType));

				while ($res = $result->fetchRow()) {
					if ( $res['start'] > 0 ) {
						$res['show_description'] = 'y';
						$res['visible'] = 'y';
						$res['type'] = $type;
						$dstart = TikiLib::make_time(
							0,0,0,
							TikiLib::date_format('%m', $res['start']),
							TikiLib::date_format('%d', $res['start']),
							TikiLib::date_format('%Y', $res['start'])
						);
						$res['time'] = TikiLib::date_format('%H%M', $res['start']);
						$res['when'] = TikiLib::date_format('%H:%M', $res['start']);
						$when = '<b>'.$res['when'].'</b>';
						$url_vars = array($res['id'], $res['id2']);
						switch ( $res['type'] ) {
							case 'art': $res['description'] = $this->parse_data($res['description']); break;
							case 'blog': $res['name'] = $res['parent'].' :: '.$res['name']; break;
							case 'dir': $res['description'] = addslashes($res['dir_url']).'<br />'.$res['description']; break;
							case 'forum': if ( $res['fid'] > 0 ) $url_vars = array($res['fid'], $res['id2'], 'threadId'.$res['id']); break;
							case 'gal': $res['description'] = tra('New Image Uploaded by').' %s'; break;
							case 'nl':
								$res['description'] = tra('New Subscriptions');
								$res['head'] = ' ... '.$res['head'];
								break;
							case 'track': $res['description'] = tra('New Item in Tracker'); break;
							case 'wiki page': $res['parent'] = 'wiki'; break;
						} 
						$res['url'] = $this->get_object_url($res['type'], $url_vars);
						if ( $res['user'] != '' ) {
							include_once('lib/smarty_tiki/modifier.username.php');
							$res['user'] = smarty_modifier_username($res['user']);
							if ( ! strpos($res['description'], '%s') ) {
								$br = ( $res['description'] == '' ) ? '' : '<br />';
								$res['description'] = '<i>'.tra('by').' %s</i>'.$br.$res['description'];
							}
							$res['description'] = sprintf($res['description'], $res['user']);
						}
						$res['description'] = str_replace(array('"',"\n|\r"), array("'",''), $res['description']);
						if ( $res['name'] == '' ) $res['name'] = $res['id'];
						
						$res['where'] = str_replace("\n|\r", '', addslashes($res['parent']));
						if ( $where == '' && $res['parent'] != '' ) $where = ' '.tra('in').' <b>'.$res['where'].'</b>';
						if ( $res['head'] == '' ) $res['head'] = $when.$where;
						
						$ret[$dstart][] = $res;
						
						unset($where);
						unset($when);
					}
				}
			}
		}
		return $ret;
	}

	function get_object_cal_infos($type, $bindvars = null) {
		switch ( $type ) {
			case 'art': $query = 'select `articleId` as `id`, `title` as `name`, `heading` as `description`, `authorName` as `user`, `topicName` as `parent`, `publishDate` as `start` from `tiki_articles` where (`publishDate`>? and `publishDate`<?)'; break;
			case 'blog': $query = 'select p.`created` as `start`, p.`user` as `user`, p.`title` as `name`, b.`title` as `parent`, b.`blogId` as `id` from `tiki_blog_posts` as p left join `tiki_blogs` as b on p.`blogId`=b.`blogId` where (p.`created`>? and p.`created`<?)'; break;
			case 'chart': $query = 'select `chartId` as `id`, `created` as `start`, `title` as `name`, `description` from `tiki_charts` where (`created`>? and `created`<?)'; break;
			case 'dir': $query = 'select `siteId` as `id`, `created` as `start`, `name`, `description`, `url` as `dir_url` from `tiki_directory_sites` where (`created`>? and `created`<?)'; break;
			case 'faq': $query = 'select `faqId` as `id`, `created` as `start`, `title` as `name`, `description` from `tiki_faqs` where (`created`>? and `created`<?)'; break;
			case 'fgal': $query = 'select f.`created` as `start`, f.`user` as `user`, f.`name` as `name`, f.`description` as `description`, g.`galleryId` as `id`, g.`name` as `parent` from `tiki_files` as f left join `tiki_file_galleries` as g on f.`galleryId`=g.`galleryId` where (f.`created`>? and f.`created`<?)'; break;
			case 'forum': $query = 'select c.`commentDate` as `start`, c.`threadId` as `id`, c.`userName` as `user`, c.`title` as `name`, f.`name` as `parent`, f.`forumId` as `fid`, c.`parentId` as `id2` from `tiki_comments` as c, `tiki_forums` as f where c.`object`=f.`forumId` and (c.`commentDate`>? and c.`commentDate`<?) and c.`objectType` = ?'; break;
			case 'gal': $query = 'select i.`imageId` as `id`, i.`created` as `start`, i.`user` as `user`, i.`name` as `name`, g.`name` as `parent`, g.`galleryId` as `id2` from `tiki_images` as i left join `tiki_galleries` as g on g.`galleryId`=i.`galleryId` where (i.`created`>? and i.`created`<?)'; break;
			case 'nl': $query = "select count(s.`email`) as `head`, max(s.`subscribed`) as `start`, s.`nlId` as `id`, n.`name` as `name` from `tiki_newsletter_subscriptions` as s left join `tiki_newsletters` as n on n.`nlId`=s.`nlId`  where (`subscribed`>? and `subscribed`<?) group by s.`nlId`, FROM_UNIXTIME(s.`subscribed`,'%d')"; break;
			case 'quiz': $query = 'select `quizId` as `id`, `created` as `start`, `name`, `description` from `tiki_quizzes` where (`created`>? and `created`<?)'; break;
			case 'surv': $query = 'select `surveyId` as `id`, `created` as `start`, `name`, `description` from `tiki_surveys` where (`created`>? and `created`<?)'; break;
			case 'track': $query = 'select i.`itemId` as `id`, i.`created` as `start`, t.`name` as `name`, t.`trackerId` as `id2` from `tiki_tracker_items` as i left join `tiki_trackers` as t on t.`trackerId`=i.`trackerId` where (i.`created`>? and i.`created`<?)'; break;
			case 'wiki comment': $query = 'select c.`commentDate` as `start`, c.`userName` as `user`, c.`title` as `name`, c.`object` as `parent`, c.`object` as `id` from `tiki_comments` as c where (c.`commentDate`>? and c.`commentDate`<?) and c.`objectType` = ?'; break;
			case 'wiki page': $query = 'select `lastModif` as `start`, `user`, `object` as `id`, `action` from `tiki_actionlog` where (`lastModif`>? and `lastModif`<?) and `objectType`=?'; break;

		}
		if ( $query != '' ) {
			if ( is_array($bindvars) && ($nb_vars = substr_count($query,'?')) > 0 ) {
				return $this->query($query, array_slice($bindvars,0,$nb_vars));
			} else return $this->query($query);
		}
	}

	function get_object_url($type, $bindvars = null) {
		switch ( $type ) {
			case 'art': $url = 'tiki-read_article.php?articleId=%s'; break;
			case 'blog': $url = 'tiki-view_blog.php?blogId=%s'; break;
			case 'chart': $url = 'tiki-view_chart.php?chartId=%s'; break;
			case 'dir': $url = 'tiki-directory_redirect.php?siteId=%s'; break;
			case 'faq': $url = 'tiki-view_faq.php?faqId=%s'; break;
			case 'fgal': $url = 'tiki-list_file_gallery.php?galleryId=%s'; break;
			case 'forum': $url = 'tiki-view_forum_thread.php?forumId=%s&amp;comments_parentId=%s#%s'; break;
			case 'gal': $url = 'tiki-browse_image.php?imageId=%s&amp;galleryId=%s'; break;
			case 'nl': $url = 'tiki-newsletters.php?nlId=%s'; break;
			case 'quiz': $url = 'tiki-take_quiz.php?quizId=%s'; break;
			case 'surv': $url = 'tiki-take_survey.php?surveyId=%s'; break;
			case 'track': $url = 'tiki-view_tracker_item.php?itemId=%s&amp;trackerId=%s&amp;offset=0&amp;sort_mode=created_desc'; break;
			case 'wiki comment': $url = 'tiki-index.php?page=%s&amp;comzone=show#comments'; break;
			case 'wiki page': $url = 'tiki-index.php?page=%s'; break;
		}
		if ( $url != '' ) {
			if ( is_array($bindvars) && ($nb_vars = substr_count($url,'%s')) > 0 ) {
				return vsprintf($url, array_map('urlencode',array_slice($bindvars,0,$nb_vars)));
			} else return $url;
		}
	}


}
global $dbTiki;
$tikicalendarlib = new TikiCalendarLib($dbTiki);

?>
