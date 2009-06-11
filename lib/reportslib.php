<?php 

include_once ('tikilib.php');
include_once ('wiki/histlib.php');
include_once ('imagegals/imagegallib.php');

class reportsLib extends TikiLib{
	function reportsLib($db) {
		$this->TikiLib($db);
	}
	
	/*shared*/
	function add_user_report($user, $interval, $view, $type, $always_email) {
		if(!isset($always_email))
			$always_email = 0;
			
		if ($this->get_report_preferences_by_user($user))
			$this->delete_user_report($user);

		$query = "insert into `tiki_user_reports`(`user`, `interval`, `view`, `type`, `always_email`, `last_report`) ";
		$query.= "values(?,?,?,?,?,NOW())";
		$this->query($query,array($user,$interval,$view,$type,$always_email));

		return true;
	}
	
	function delete_user_report($user) {
		$query = "delete from `tiki_user_reports` where `user`=?";
		$this->query($query,array($user));
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
	
	function getUsersForReport() {
		$query = "select `user`, `interval`, `last_report` from tiki_user_reports";
		$result = $this->query($query);
		if (!$result->numRows()) {
			return false;
		}
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[] = $res['user'];
		}

		return $ret;
	}
	
	function add_report_chache_entries($users, $event, $data) {
		foreach ($users as $user) {
			$query = "insert into `tiki_user_reports_cache`(`user`, `event`, `data`,`time`) ";
			$query.= "values(?,?,?,NOW())";
			$this->query($query,array($user, $event, serialize($data)));
		}
		
		return true;
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
	
	function deleteUsersReportCache($user_data) {
		$query = "delete from `tiki_user_reports_cache` where `user`=?";
		$this->query($query,array($user_data['login']));
		return true;
	}
	
	function updateLastSent($user_data) {
		$query = "update `tiki_user_reports` set last_report = NOW() where `user`=?";
		$this->query($query,array($user_data['login']));
		return true;
	}
	
	//-----------------------------------------------------
	
	//Fügt die verscheidenen Emailteile zusammen
	public function sendEmail($user_data, $report_preferences, $report_cache) {
		global $prefs, $smarty;

		include_once('lib/webmail/tikimaillib.php');
		$mail = new TikiMail();
		
		$smarty->assign('report_user', ucfirst($user_data['login']));
		$smarty->assign('report_interval', ucfirst($report_preferences['interval']));
		$smarty->assign('report_date', date("l d.m.Y"));
		$smarty->assign('report_last_report_date', date("l d.m.Y", strtotime($report_preferences['last_report'])));
		$smarty->assign('report_total_changes', count($report_cache));
		if ($prefs['feature_contribution'] == 'y' && !empty($contributions)) {
			global $contributionlib; include_once('lib/contribution/contributionlib.php');
			$smarty->assign('mail_contributions', $contributionlib->print_contributions($contributions));
		}

		$smarty->assign('report_body', $this->makeDetailedHtmlEmailBody($report_cache, $report_preferences));

		$mail->setUser($user_data['login']);
		if(is_array($report_cache)) {
			if (count($report_cache)==1) {
				$changes = "1 change";
			} else {
				$changes = count($report_cache)." changes";
			}
		} else {
			$changes = "No changes";
		}
		
		$subject = ucfirst($report_preferences['interval'])."-Report from ".date("d.m.Y", time())." (".$changes.")";
		$mail->setSubject($subject);
		$mail_data = $smarty->fetchLang('de', "mail/report.tpl");
		$mail->setText($mail_data);
		
		echo "Going to ".$user_data['email']."<br>";
		echo "Subject: ".$subject."<br>";
		echo "Message template:<br>".$mail_data;
				
		$mail->buildMessage();
		$mail->send(array($user_data['email']));

		$email_test_headers .= 'From: noreply@tikiwiki.org' . "\n";	// needs a valid sender
		$email_test_headers .= 'Reply-to: '. $email_test_to . "\n";
		$email_test_headers .= "Content-type: text/html; charset=utf-8\n";
		$email_test_headers .= 'X-Mailer: Tiki/'.$TWV->version.' - PHP/' . phpversion() . "\n";
			
		$sentmail = mail($user_data['email'], $subject, $mail_data, $email_test_headers);
		
		return true;
	}
	
	//Erstellt eine schöne Zeitangabe
	private function makeTime($time) {
		if (date("d.m.Y", $time)==date("d.m.Y", time()-86400)) {
			return "Yesterday ".date("H:i", $time);
		} elseif(date("d.m.Y", $time)==date("d.m.Y", time())) {
			return "Today ".date("H:i", $time);
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
	
	public function makeDetailedHtmlEmailBody($report_cache) {
		$change_array = $this->makeChangeArray($report_cache);
		$somethingHasHappened = false;

		//URL Prefix
		global $tikilib;
		$foo = parse_url($_SERVER["REQUEST_URI"]);
		$machine = $tikilib->httpPrefix() . dirname( $foo["path"] );
	
		foreach ($change_array as $somethingHasHappened=>$array) {
			foreach ($array as $key=>$change) {
				if ($key>0)
					$body .= "&nbsp; ";
				else
					$body .= "<b>";
			
				$body .= $this->makeTime(strtotime($change['time'])).": ";
				if ($change['event']=='image_gallery_changed' && empty($change['data']['action'])) {
					$body .= $change['data']['user']." changed the picture gallery <a href=\"$machine/tiki-browse_gallery.php?galleryId=".$change['data']['galleryId']."&offset=0&sort_mode=created_desc\">".$change['data']['galleryName']."</a> changed.";
				} elseif ($change['event']=='image_gallery_changed' && $change['data']['action']=="upload image") {
					$body .= "<u>".$change['data']['user']."</u> added the picture <a href=\"$machine/tiki-browse_image.php?imageId=".$change['data']['imageId']."\">".$change['data']['imageName']."</a> to <a href=\"$machine/tiki-browse_gallery.php?galleryId=".$change['data']['galleryId']."&offset=0&sort_mode=created_desc\">".$change['data']['galleryName']."</a>.";
				} elseif ($change['event']=='image_gallery_changed' && $change['data']['action']=="remove image") {
					$body .= "<u>".$change['data']['user']."</u> removed the picture <a href=\"$machine/tiki-browse_image.php?imageId=".$change['data']['imageId']."\">".$change['data']['imageName']."</a> from <a href=\"$machine/tiki-browse_gallery.php?galleryId=".$change['data']['galleryId']."&offset=0&sort_mode=created_desc\">".$change['data']['galleryName']."</a>.";
				} elseif ($change['event']=="wiki_page_changed") {
					$body .= "<u>".$change['data']['editUser']."</u> edited the wikipage <a href=\"$machine/tiki-index.php?page=".$change['data']['pageName']."\">".$change['data']['pageName']."</a> (<a href=\"$machine/tiki-pagehistory.php?page=".$change['data']['pageName']."&diff_style=sidediff&compare=Compare&newver=".($change['data']['oldVer']+1)."&oldver=".$change['data']['oldVer']."\">this history</a>, <a href=\"$machine/tiki-pagehistory.php?page=".$change['data']['pageName']."&diff_style=sidediff&compare=Compare&newver=0&oldver=".$change['data']['oldVer']."\">all history</a>)";

				} elseif ($change['event']=="file_gallery_changed" && empty($change['data']['action'])) {
					$body .= "<u>".$change['data']['user']."</u> edited the file gallery <a href=\"$machine/tiki-list_file_gallery.php?galleryId=".$change['data']['galleryId']."\">".$change['data']['galleryName']."</a>";
				} elseif ($change['event']=="file_gallery_changed" && $change['data']['action']=="upload file") {
					$body .= "<u>".$change['data']['user']."</u> uploaded the file <a href=\"$machine/tiki-download_file.php?fileId=".$change['data']['fileId']."\">".$change['data']['fileName']."</a> to <a href=\"$machine/tiki-list_file_gallery.php?galleryId=".$change['data']['galleryId']."\">".$change['data']['galleryName']."</a>.";
				} elseif ($change['event']=="file_gallery_changed" && $change['data']['action']=="remove file") {
					$body .= "<u>".$change['data']['user']."</u> removed the file <a href=\"$machine/tiki-download_file.php?fileId=".$change['data']['fileId']."\">".$change['data']['fileName']."</a> from <a href=\"$machine/tiki-list_file_gallery.php?galleryId=".$change['data']['galleryId']."\">".$change['data']['galleryName']."</a>.";					

				} elseif ($change['event']=="category_changed") {
					if ($change['data']['action']=="object entered category") {
						$body .= "<u>".$change['data']['user']."</u> added the ".$change['data']['objectType']." <a href=\"$machine/".$change['data']['objectUrl']."\">".$change['data']['objectName']."</a> to the category <a href=\"$machine/tiki-browse_categories.php?parentId=".$change['data']['categoryId']."&deep=off\">".$change['data']['categoryName']."</a>.";
					} elseif ($change['data']['action']=="object leaved category") {
						$body .= "<u>".$change['data']['user']."</u> removed the ".$change['data']['objectType']." <a href=\"$machine/".$change['data']['objectUrl']."\">".$change['data']['objectName']."</a> from the category <a href=\"$machine/tiki-browse_categories.php?parentId=".$change['data']['categoryId']."&deep=off\">".$change['data']['categoryName']."</a>.";
					} elseif ($change['data']['action']=="category created") {
						$body .= "<u>".$change['data']['user']."</u> created the subcategory <a href=\"$machine/tiki-browse_categories.php?parentId=".$change['data']['categoryId']."&deep=off\">".$change['data']['categoryName']."</a> in <a href=\"$machine/tiki-browse_categories.php?parentId=".$change['data']['parentId']."&deep=off\">".$change['data']['parentName']."</a>.";
					} elseif ($change['data']['action']=="category removed") {
						$body .= "<u>".$change['data']['user']."</u> removed the subcategory <a href=\"$machine/tiki-browse_categories.php?parentId=".$change['data']['categoryId']."&deep=off\">".$change['data']['categoryName']."</a> from <a href=\"$machine/tiki-browse_categories.php?parentId=".$change['data']['parentId']."&deep=off\">".$change['data']['parentName']."</a>.";
					} elseif ($change['data']['action']=="category updated") {
						$body .= "<u>".$change['data']['user']."</u> edited the category <a href=\"$machine/tiki-browse_categories.php?parentId=".$change['data']['categoryId']."&deep=off\">".$change['data']['categoryName']."</a>";
					}
				} elseif ($change['event']=="article_deleted") {
					$body .= "<u>".$change['data']['user']."</u> removed the article <a href=\"$machine/tiki-read_article.php?articleId=".$change['data']['articleId']."\">".$change['data']['articleTitle']."</a>.";
				} elseif ($change['event']=="article_submitted") {
					$body .= "<u>".$change['data']['user']."</u> created the article <a href=\"$machine/tiki-read_article.php?articleId=".$change['data']['articleId']."\">".$change['data']['articleTitle']."</a>.";
				} elseif ($change['event']=="article_edited") {
					$body .= "<u>".$change['data']['user']."</u> edited the article <a href=\"$machine/tiki-read_article.php?articleId=".$change['data']['articleId']."\">".$change['data']['articleTitle']."</a>.";

				} elseif ($change['event']=="blog_post") {
					$body .= "<u>".$change['data']['user']."</u> replied to the blog <a href=\"$machine/tiki-view_blog.php?blogId=".$change['data']['blogId']."\">".$change['data']['blogTitle']."</a> <a href=\"$machine/tiki-view_blog_post.php?postId=\"".$change['data']['postId']."></a>.";

				} elseif ($change['event']=="forum_post_topic") {
					$body .= "<u>".$change['data']['user']."</u> created the topic <a href=\"$machine/tiki-view_forum_thread.php?comments_parentId=".$change['data']['topicId']."&forumId=".$change['data']['forumId']."\">".$change['data']['threadName']."</a> at forum <a href=\"$machine/tiki-view_forum.php?forumId=".$change['data']['forumId']."\">".$change['data']['forumName']."</a>.";
				} elseif ($change['event']=="forum_post_thread") {
					global $dbTiki;
					include_once ("lib/commentslib.php");
					$commentslib = new Comments($dbTiki);
					$parent_topic = $commentslib->get_comment($change['data']['topicId']);
					
					$body .= "<u>".$change['data']['user']."</u> <a href=\"$machine/tiki-view_forum_thread.php?forumId=".$change['data']['forumId']."&comments_parentId=".$change['data']['topicId']."#threadId".$change['data']['threadId']."\">replied</a> to the topic <a href=\"$machine/tiki-view_forum_thread.php?comments_parentId=".$change['data']['topicId']."&forumId=".$change['data']['forumId']."\">".$parent_topic['title']."</a>.";
				}
				if ($key==0)
					$body .= "</b>";
					
				$body .= "<br>";
			}
		}
		
		if(!$somethingHasHappened) {
			return "Nothing has happened.";
		} else {
			return $body;
		}
	}
}

global $dbTiki;
$reportslib = new reportsLib($dbTiki);
?>