<?php 
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class reportsLib extends TikiLib
{

	//Sends the Email
	public function sendEmail($user_data, $report_preferences, $report_cache, $tikiUrl) {
		global $prefs, $smarty, $tikilib;

		include_once('lib/webmail/tikimaillib.php');
		$mail = new TikiMail();
		
		
		$smarty->assign('report_preferences', $report_preferences);
		$smarty->assign('report_user', ucfirst($user_data['login']));
		$smarty->assign('report_interval', ucfirst($report_preferences['interval']));
		$smarty->assign('report_date', date("l d.m.Y"));
		$smarty->assign('report_last_report_date', date("l d.m.Y", strtotime($report_preferences['last_report'])));
		$smarty->assign('report_total_changes', count($report_cache));
		if ($prefs['feature_contribution'] == 'y' && !empty($contributions)) {
			global $contributionlib; include_once('lib/contribution/contributionlib.php');
			$smarty->assign('mail_contributions', $contributionlib->print_contributions($contributions));
		}

		$smarty->assign('report_body', $this->makeHtmlEmailBody($report_cache, $report_preferences, $tikiUrl));

		$mail->setUser($user_data['login']);
		if(is_array($report_cache)) {
			if (count($report_cache)==1) {
				$changes = "1 ".tra("change");
			} else {
				$changes = count($report_cache)." ".tra("changes");
			}
		} else {
			$changes = tra("No changes");
		}

		$subject = tra(ucfirst($report_preferences['interval'])." report from")." ".date("d.m.Y", time())." (".$changes.")";
		$mail->setSubject($subject);

		$userlang = $tikilib->get_user_preference($user_data['login'], "language", $prefs['site_language']);

		$mail_data = $smarty->fetchLang($userlang, "mail/report.tpl");

		if($report_preferences['type']=='plain')
			$mail->setText($mail_data);
		else
			$mail->setHtml($mail_data);
		
		$mail->buildMessage();
		$mail->send(array($user_data['email']));

		return true;
	}
	
	//Makes time short
	private function makeTime($time) {
		if (date("d.m.Y", $time)==date("d.m.Y", time()-86400)) {
			return tra("Yesterday")." ".date("H:i", $time);
		} elseif(date("d.m.Y", $time)==date("d.m.Y", time())) {
			return tra("Today")." ".date("H:i", $time);
		} else {
			return date("d.m.", $time)." ".date("H:i", $time);
		}
	}
	
	private function makeChangeArray($report_cache) {
		foreach ($report_cache as $change) {
			$indexIdentifier = $change['event'].$change['data']['action'].$change['data']['galleryId'].$change['data']['pageName'].$change['data']['categoryId'];

			$change_array[$indexIdentifier][] = $change;
		}
		return $change_array;
	}
	
	public function makeHtmlEmailBody($report_cache, $report_preferences, $tikiUrl) {
		global $tikilib, $userlib;		
		$change_array = $this->makeChangeArray($report_cache);
		$somethingHasHappened = false;

		$morechanges = 0;
		foreach ($change_array as $somethingHasHappened=>$array) {
			
			foreach ($array as $key=>$change) {
				
				if ($report_preferences['view']=="short" AND $key>0) {

					$morechanges++;
								
				} elseif ($report_preferences['view']=="detailed" OR $key==0) {

				if ($morechanges>0) {
					$body .= "   ".tra("and")." ".$morechanges." ".tra("more changes of the same type...")."<br>";
					$morechanges = 0;
					if($report_preferences['type']=='plain')
						$body .= "\r\n";
				}

				if($key>0) {
					if($report_preferences['type']=='plain') {
						$body .= "   ";
					} else {
						$body .= "&nbsp;&nbsp;&nbsp;";
					}
				} else {
					$body .= "<b>";
				}

				$body .= $this->makeTime(strtotime($change['time'])).": ";
				$change['data']['user'] = $userlib->clean_user($change['data']['user']);
				
				if ($change['event']=='image_gallery_changed' && empty($change['data']['action'])) {
					$body .= $change['data']['user']." ".tra("changed the picture gallery")." <a href=\"$tikiUrl/tiki-browse_gallery.php?galleryId=".$change['data']['galleryId']."&offset=0&sort_mode=created_desc\">".$change['data']['galleryName']."</a>.";
				} elseif ($change['event']=='image_gallery_changed' && $change['data']['action']=="upload image") {
					$body .= "<u>".$change['data']['user']."</u> ".tra("uploaded the picture")." <a href=\"$tikiUrl/tiki-browse_image.php?imageId=".$change['data']['imageId']."\">".$change['data']['imageName']."</a> ".tra("onto")." <a href=\"$tikiUrl/tiki-browse_gallery.php?galleryId=".$change['data']['galleryId']."&offset=0&sort_mode=created_desc\">".$change['data']['galleryName']."</a>.";
				} elseif ($change['event']=='image_gallery_changed' && $change['data']['action']=="remove image") {
					$body .= "<u>".$change['data']['user']."</u> ".tra("removed the picture")." <a href=\"$tikiUrl/tiki-browse_image.php?imageId=".$change['data']['imageId']."\">".$change['data']['imageName']."</a> ".tra("from")." <a href=\"$tikiUrl/tiki-browse_gallery.php?galleryId=".$change['data']['galleryId']."&offset=0&sort_mode=created_desc\">".$change['data']['galleryName']."</a>.";
				} elseif ($change['event']=="wiki_page_changed") {
					$body .= "<u>".$change['data']['editUser']."</u> ".tra("edited the wikipage")." <a href=\"$tikiUrl/tiki-index.php?page=".$change['data']['pageName']."\">".$change['data']['pageName']."</a> (<a href=\"$tikiUrl/tiki-pagehistory.php?page=".$change['data']['pageName']."&diff_style=sidediff&compare=Compare&newver=".($change['data']['oldVer']+1)."&oldver=".$change['data']['oldVer']."\">".tra("this history")."</a>, <a href=\"$tikiUrl/tiki-pagehistory.php?page=".$change['data']['pageName']."&diff_style=sidediff&compare=Compare&newver=0&oldver=".$change['data']['oldVer']."\">".tra("all history")."</a>)";

				} elseif ($change['event']=="file_gallery_changed" && empty($change['data']['action'])) {
					$body .= "<u>".$change['data']['user']."</u> ".tra("edited the file gallery")." <a href=\"$tikiUrl/tiki-list_file_gallery.php?galleryId=".$change['data']['galleryId']."\">".$change['data']['galleryName']."</a>";
				} elseif ($change['event']=="file_gallery_changed" && $change['data']['action']=="upload file") {
					$body .= "<u>".$change['data']['user']."</u> ".tra("uploaded the file")." <a href=\"$tikiUrl/tiki-download_file.php?fileId=".$change['data']['fileId']."\">".$change['data']['fileName']."</a> ".tra("onto")." <a href=\"$tikiUrl/tiki-list_file_gallery.php?galleryId=".$change['data']['galleryId']."\">".$change['data']['galleryName']."</a>.";
				} elseif ($change['event']=="file_gallery_changed" && $change['data']['action']=="remove file") {
					$body .= "<u>".$change['data']['user']."</u> ".tra("removed the file")." <a href=\"$tikiUrl/tiki-download_file.php?fileId=".$change['data']['fileId']."\">".$change['data']['fileName']."</a> ".tra("from")." <a href=\"$tikiUrl/tiki-list_file_gallery.php?galleryId=".$change['data']['galleryId']."\">".$change['data']['galleryName']."</a>.";					

				} elseif ($change['event']=="category_changed") {
					if ($change['data']['action']=="object entered category") {
						$body .= "<u>".$change['data']['user']."</u> ".tra("added the ".$change['data']['objectType'])." <a href=\"$tikiUrl/".$change['data']['objectUrl']."\">".$change['data']['objectName']."</a> ".tra("to the category")." <a href=\"$tikiUrl/tiki-browse_categories.php?parentId=".$change['data']['categoryId']."&deep=off\">".$change['data']['categoryName']."</a>.";
					} elseif ($change['data']['action']=="object leaved category") {
						$body .= "<u>".$change['data']['user']."</u> ".tra("removed the ".$change['data']['objectType'])." <a href=\"$tikiUrl/".$change['data']['objectUrl']."\">".$change['data']['objectName']."</a> ".tra("from the category")." <a href=\"$tikiUrl/tiki-browse_categories.php?parentId=".$change['data']['categoryId']."&deep=off\">".$change['data']['categoryName']."</a>.";
					} elseif ($change['data']['action']=="category created") {
						$body .= "<u>".$change['data']['user']."</u> ".tra("created the subcategory")." <a href=\"$tikiUrl/tiki-browse_categories.php?parentId=".$change['data']['categoryId']."&deep=off\">".$change['data']['categoryName']."</a> ".tra("in")." <a href=\"$tikiUrl/tiki-browse_categories.php?parentId=".$change['data']['parentId']."&deep=off\">".$change['data']['parentName']."</a>.";
					} elseif ($change['data']['action']=="category removed") {
						$body .= "<u>".$change['data']['user']."</u> ".tra("removed the subcategory")." <a href=\"$tikiUrl/tiki-browse_categories.php?parentId=".$change['data']['categoryId']."&deep=off\">".$change['data']['categoryName']."</a> ".tra("from")." <a href=\"$tikiUrl/tiki-browse_categories.php?parentId=".$change['data']['parentId']."&deep=off\">".$change['data']['parentName']."</a>.";
					} elseif ($change['data']['action']=="category updated") {
						$body .= "<u>".$change['data']['user']."</u> ".tra("edited the category")." <a href=\"$tikiUrl/tiki-browse_categories.php?parentId=".$change['data']['categoryId']."&deep=off\">".$change['data']['categoryName']."</a>";
					}
				} elseif ($change['event']=="article_deleted") {
					$body .= "<u>".$change['data']['user']."</u> ".tra("removed the article")." <a href=\"$tikiUrl/tiki-read_article.php?articleId=".$change['data']['articleId']."\">".$change['data']['articleTitle']."</a>.";
				} elseif ($change['event']=="article_submitted") {
					$body .= "<u>".$change['data']['user']."</u> ".tra("created the article")." <a href=\"$tikiUrl/tiki-read_article.php?articleId=".$change['data']['articleId']."\">".$change['data']['articleTitle']."</a>.";
				} elseif ($change['event']=="article_edited") {
					$body .= "<u>".$change['data']['user']."</u> ".tra("edited the article")." <a href=\"$tikiUrl/tiki-read_article.php?articleId=".$change['data']['articleId']."\">".$change['data']['articleTitle']."</a>.";

				} elseif ($change['event']=="blog_post") {
					$body .= "<u>".$change['data']['user']."</u> ".tra("replied to the blog")." <a href=\"$tikiUrl/tiki-view_blog.php?blogId=".$change['data']['blogId']."\">".$change['data']['blogTitle']."</a> <a href=\"$tikiUrl/tiki-view_blog_post.php?postId=\"".$change['data']['postId']."></a>.";

				} elseif ($change['event']=="forum_post_topic") {
					$body .= "<u>".$change['data']['user']."</u> ".tra("created the topic")." <a href=\"$tikiUrl/tiki-view_forum_thread.php?comments_parentId=".$change['data']['topicId']."&forumId=".$change['data']['forumId']."\">".$change['data']['threadName']."</a> ".tra("at forum")." <a href=\"$tikiUrl/tiki-view_forum.php?forumId=".$change['data']['forumId']."\">".$change['data']['forumName']."</a>.";
				} elseif ($change['event']=="forum_post_thread") {
					global $dbTiki;
					include_once ("lib/comments/commentslib.php");
					$commentslib = new Comments($dbTiki);
					$parent_topic = $commentslib->get_comment($change['data']['topicId']);
					
					$body .= "<u>".$change['data']['user']."</u> <a href=\"$tikiUrl/tiki-view_forum_thread.php?forumId=".$change['data']['forumId']."&comments_parentId=".$change['data']['topicId']."#threadId".$change['data']['threadId']."\">".tra("replied")."</a> ".tra("to the topic")." <a href=\"$tikiUrl/tiki-view_forum_thread.php?comments_parentId=".$change['data']['topicId']."&forumId=".$change['data']['forumId']."\">".$parent_topic['title']."</a>.";
				} elseif ($change['event'] == 'wiki_file_attached') {
					$body .= "<u>".$change['data']['user']."</u> ".tra('uploaded the file') . " <a href=\"$tikiUrl/tiki-download_wiki_attachment.php?attId=".$change['data']['attId']."\">".$change['data']['filename']."</a> ".tra("onto")." <a href=\"$tikiUrl/tiki-index.php?page=".$change['data']['pageName']."\">".$change['data']['pageName']."</a>.";
				}
				if ($key==0)
					$body .= "</b>";
					
				$body .= "<br>";
				if($report_preferences['type']=='plain')
					$body .= "\r\n";
			}
			}
		}
		
		if($report_preferences['type']=='plain')
			$body = strip_tags($body);
		
		if(!$somethingHasHappened) {
			return tra("Nothing has happened.");
		} else {
			return $body;
		}
	}
	
	function add_user_report($user, $interval, $view, $type, $always_email) {
		if(!isset($always_email))
			$always_email = 0;
		
		if (!$this->get_report_preferences_by_user($user)) {
			//Add new report entry	
			$query = "insert into `tiki_user_reports`(`user`, `interval`, `view`, `type`, `always_email`, `last_report`) ";
			$query.= "values(?,?,?,?,?,NOW())";
			$this->query($query,array($user,$interval,$view,$type,$always_email));
		} else {
			//Update report entry
			$query = "update `tiki_user_reports` set `interval`=?, `view`=?, `type`=?, `always_email`=? where `user`=?";
			$this->query($query,array($interval,$view,$type,$always_email,$user));
		}
		return true;
	}
	
	function delete_user_report($user) {
		$query = "delete from `tiki_user_reports` where `user`=?";
		$this->query($query,array($user));

		$this->deleteUsersReportCache($user);
		return true;
	}
	
	function get_report_preferences_by_user($user) {
		$query = "select `id`, `interval`, `view`, `type`, `always_email`, `last_report` from `tiki_user_reports` where `user` = ?";
		$result = $this->query($query, array($user));
		if (!$result->numRows()) {
			return false;
		}
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret = $res;
		}
		
		return $ret;
	}
	
	function getUsersForSendingReport() {
		$query = "select `user`, `interval`, UNIX_TIMESTAMP(`last_report`) as last_report from tiki_user_reports";
		$result = $this->query($query);
		if (!$result->numRows()) {
			return false;
		}
		$ret = array();
		while ($res = $result->fetchRow()) {
			if ($res['interval']=="daily" AND ($res['last_report']+86400)<=time()) {
				$ret[] = $res['user'];
			}
			if ($res['interval']=="weekly" AND ($res['last_report']+604800)<=time()) {
				$ret[] = $res['user'];
			}
			if ($res['interval']=="monthly" AND ($res['last_report']+2419200)<=time()) {
				$ret[] = $res['user'];
			}
		}
		return $ret;
	}
	
	function makeReportCache(&$nots, $cache_data) {
		//Get all users that have enabled reports
		$query = "select `user` from tiki_user_reports";
		$result = $this->query($query);
		$report_users = array();
		while ($res = $result->fetchRow()) {
			$report_users[] = $res['user'];
		}
		
		foreach ($nots as $key=>$not) {
			//If user in $nots has enabled reports
			if (in_array($not['user'], $report_users)) {
				//dump the report-data to the report cache
				$query = "insert into `tiki_user_reports_cache`(`user`, `event`, `data`,`time`) ";
				$query.= "values(?,?,?,NOW())";
				$this->query($query,array($not['user'], $cache_data['event'], serialize($cache_data)));

				//and reove the user from $nots so that he doesn´t get a notification for the event
				unset($nots[$key]);
			}
		}
	}
	
	
	
	function get_report_cache_entries_by_user($user, $order_by) {
		$query = "select `user`, `event`, `data`, `time` from `tiki_user_reports_cache` where `user` = ? ORDER BY $order_by";
		$result = $this->query($query, array($user));
		if (!$result->numRows()) {
			return false;
		}
		$ret = array();
		while ($res = $result->fetchRow()) {
			$res['data'] = unserialize($res['data']);
			$ret[] = $res;
		}
		
		return $ret;
	}
	
	function deleteUsersReportCache($user) {
		$query = "delete from `tiki_user_reports_cache` where `user`=?";
		$this->query($query,array($user));
		return true;
	}
	
	function updateLastSent($user) {
		$query = "update `tiki_user_reports` set last_report = NOW() where `user`=?";
		$this->query($query,array($user));
		return true;
	}
}

global $reportslib;
$reportslib = new reportsLib;
