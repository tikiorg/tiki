<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Build an periodic report e-mail with the changes
 * in Tiki for the objects a user is watching. 
 * 
 * @package Tiki
 * @subpackage Reports
 */
class Reports_Send_BuildEmail
{
	/**
	 * @param TikiLib $tikilib
	 * @return null
	 */
	public function __construct(TikiLib $tikilib)
	{
		$this->tikilib = $tikilib;
	}
	
	protected function buildEmailBody($user_data, $report_preferences, $report_cache)
	{
		global $base_url;
		
		$smarty = TikiLib::lib('smarty');
		$tikilib = TikiLib::lib('tiki');
		
		$smarty->assign('report_preferences', $report_preferences);
		$smarty->assign('report_user', ucfirst($user_data['login']));
		$smarty->assign('report_interval', ucfirst($report_preferences['interval']));
		$smarty->assign('report_date', date("l d.m.Y"));
		$smarty->assign('report_last_report_date', TikiLib::date_format($this->tikilib->get_preference('long_date_format'), strtotime($report_preferences['last_report'])));
		$smarty->assign('report_total_changes', count($report_cache));
		
		$smarty->assign('report_body', $this->makeHtmlEmailBody($report_cache, $report_preferences));

		$userWatchesUrl = $base_url . 'tiki-user_watches.php';
		
		if ($report_preferences['type'] == 'html') {
			$userWatchesUrl = "<a href=$userWatchesUrl>$userWatchesUrl</a>"; 
		}
		
		$smarty->assign('userWatchesUrl', $userWatchesUrl);
		
		$userlang = $tikilib->get_user_preference($user_data['login'], "language", $this->tikilib->get_preference('site_language'));

		$mail_data = $smarty->fetchLang($userlang, "mail/report.tpl");
		
		return $mail_data;	
	}
	
	/**
	 * Organize $report_cache array by event type
	 * 
	 * @param array $report_cache
	 * @return array new array with events organized by type
	 */
	private function makeChangeArray(array $report_cache)
	{
		$change_array = array();
		
		foreach ($report_cache as $change) {
			$indexIdentifier = $change['event'];
			
			if (isset($change['data']['action'])) {
				$indexIdentifier .= $change['data']['action'];
			}
			
			if (isset($change['data']['galleryId'])) {
				$indexIdentifier .= $change['data']['galleryId'];
			} else if (isset($change['data']['pageName'])) {
				$indexIdentifier .= $change['data']['pageName'];
			} else if (isset($change['data']['categoryId'])) {
				$indexIdentifier .= $change['data']['categoryId']; 
			}

			$change_array[$indexIdentifier][] = $change;
		}
		
		return $change_array;
	}
	
	public function makeHtmlEmailBody(array $report_cache, array $report_preferences)
	{
		global $userlib, $base_url;
		
		$tikiUrl = rtrim($base_url, '/');
		
		$change_array = $this->makeChangeArray($report_cache);
		$somethingHasHappened = false;
		$body = '';

		$morechanges = 0;
		foreach ($change_array as $somethingHasHappened=>$array) {

			foreach ($array as $key=>$change) {
				
				if ($report_preferences['view']=="short" AND $key>0) {

					$morechanges++;
								
				} elseif ($report_preferences['view']=="detailed" OR $key==0) {

					if ($morechanges > 0) {
						$body .= "   ".tra("and")." ".$morechanges." ".tra("more changes of the same type...")."<br>";
						$morechanges = 0;
						if ($report_preferences['type']=='plain')
							$body .= "\r\n";
					}
	
					if ($key > 0) {
						if ($report_preferences['type'] == 'plain') {
							$body .= "   ";
						} else {
							$body .= "&nbsp;&nbsp;&nbsp;";
						}
					} else {
						$body .= "<b>";
					}
	
					$body .= $this->tikilib->get_short_datetime(strtotime($change['time'])) . ": ";
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
							$body .= "<u>".$change['data']['user']."</u> ".
								tr(
												"added the %0 %1 to the category %2",
												$change['data']['objectType'],
												"<a href=\"$tikiUrl/{$change['data']['objectUrl']}\">{$change['data']['objectName']}</a>",
												"<a href=\"$tikiUrl/tiki-browse_categories.php?parentId={$change['data']['categoryId']}&deep=off\">{$change['data']['categoryName']}</a>"
								);
						} elseif ($change['data']['action']=="object leaved category") {
							$body .= "<u>".$change['data']['user']."</u> ".
								tr(
												"removed the %0 %1 from the category %2",
												$change['data']['objectType'],
												"<a href=\"$tikiUrl/{$change['data']['objectUrl']}\">{$change['data']['objectName']}</a>",
												"<a href=\"$tikiUrl/tiki-browse_categories.php?parentId={$change['data']['categoryId']}&deep=off\">{$change['data']['categoryName']}</a>."
								);
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
					} elseif ($change['event'] == 'calendar_changed') {
						$calendarlib = TikiLib::lib('calendar');
						$item = $calendarlib->get_item($change['data']['calitemId']);
						$body .= tr('%0 added or updated event %1', "<u>{$change['data']['user']}</u>", "<a href='$tikiUrl/tiki-calendar_edit_item.php?viewcalitemId={$change['data']['calitemId']}'>{$item['name']}</a>");
					} elseif ($change['event'] == 'tracker_item_modified' || $change['event'] == 'tracker_item_comment') {
						$trackerId = $change['data']['trackerId'];
						$itemId = $change['data']['itemId'];
						
						$trklib = TikiLib::lib('trk');
						$tracker = $trklib->get_tracker($trackerId);
						$mainFieldValue = $trklib->get_isMain_value($trackerId, $itemId);
						
						if ($change['event'] == 'tracker_item_modified') {
							if ($mainFieldValue) {
								$body .= tr(
												'%0 added or updated tracker item %1 on tracker %2',
												"<u>{$change['data']['user']}</u>",
												"<a href='$tikiUrl/tiki-view_tracker_item.php?itemId=$itemId'>$mainFieldValue</a>",
												"<a href='$tikiUrl/tiki-view_tracker.php?trackerId=$trackerId'>{$tracker['name']}</a>"
								);
							} else {
								$body .= tr(
												'%0 added or updated tracker item id %1 on tracker %2',
												"<u>{$change['data']['user']}</u>",
												"<a href='$tikiUrl/tiki-view_tracker_item.php?itemId=$itemId'>$itemId</a>",
												"<a href='$tikiUrl/tiki-view_tracker.php?trackerId=$trackerId'>{$tracker['name']}</a>"
								);
							}
						} else {
							// tracker_item_comment event
							if ($mainFieldValue) {
								$body .= tr(
												'%0 added a new comment to %1 on tracker %2',
												"<u>{$change['data']['user']}</u>",
												"<a href='$tikiUrl/tiki-view_tracker_item.php?itemId=$itemId&cookietab=2'>$mainFieldValue</a>",
												"<a href='$tikiUrl/tiki-view_tracker.php?trackerId=$trackerId'>{$tracker['name']}</a>"
								);
							} else {
								$body .= tr(
												'%0 added a new comment to item id %1 on tracker %2',
												"<u>{$change['data']['user']}</u>",
												"<a href='$tikiUrl/tiki-view_tracker_item.php?itemId=$itemId&cookietab=2'>$itemId</a>",
												"<a href='$tikiUrl/tiki-view_tracker.php?trackerId=$trackerId'>{$tracker['name']}</a>"
								);
							}
						}
					}
					
					if ($key==0) {
						$body .= "</b>";
					}
						
					$body .= "<br>";
					if ($report_preferences['type']=='plain') {
						$body .= "\r\n";
					}
				}
			}
		}
		
		if ($report_preferences['type'] == 'plain') {
			$body = strip_tags($body);
		}
		
		if (!$somethingHasHappened) {
			return tra("Nothing has happened.");
		} else {
			return $body;
		}
	}
}
