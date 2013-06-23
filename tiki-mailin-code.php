<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
$access->check_script($_SERVER["SCRIPT_NAME"], basename(__FILE__));
include_once ('lib/mailin/mailinlib.php');
require_once ("lib/webmail/net_pop3.php");
include_once ("lib/mail/mimelib.php");
include_once ("lib/webmail/tikimaillib.php");
include_once ('lib/wiki/wikilib.php');
require_once ('lib/mailin/usermailinlib.php');

global $prefs;

$is_html = false;
$show_inlineImages = 'n';
$can_addAttachment = 'n';
$respond_email = 'y';
$save_html = 'y';

/**
 * @param $output
 * @param $out
 * @param $page
 * @param $user
 * @param $body
 */
function mailin_check_attachments(&$output, &$out, $page, $user)
{
	global $wikilib, $show_inlineImages, $can_addAttachment, $respond_email;
	$cnt = 0;
	
	if ($can_addAttachment !== 'y') {
		return;
	}
	
	if (!isset($output["parts"])) {
		return;
	}
	
	for ($it = 0, $count_outputparts = count($output['parts']); $it < $count_outputparts; $it++) {
		if (isset($output["parts"][$it]["d_parameters"]["filename"])) {
			$attachmentPart = $output["parts"][$it];
			$fileName = $attachmentPart["d_parameters"]["filename"];
			if (isset($attachmentPart["ctype_primary"])) $fileType = $attachmentPart["ctype_primary"] . "/" . $attachmentPart["ctype_secondary"];
			else $fileType = "";
			$fileData = $attachmentPart["body"];
			$fileSize = strlen($fileData);
			$wikilib->wiki_attach_file($page, $fileName, $fileType, $fileSize, $fileData, "attached by mail", $user, "");
			$cnt++;
			
			if($show_inlineImages === 'y') {
				$contentId = $attachmentPart['header']['content-id'];
				if (!empty($contentId)) {
					$contentId = str_replace("<", "", $contentId);
					$contentId = str_replace(">", "", $contentId);

					mailin_insert_inline_image($body, $contentId, $attId, $page);
				}
			}
		}
	}
	$out.= $cnt;
	$out.= " attachment(s) added<br />";
}

/**
 * @param $output
 * @return string
 */
function mailin_get_body($output)
{
	if (isset($output['text'][0])) $body = $output["text"][0];
	elseif (isset($output['parts'][0]) && isset($output['parts'][0]["text"][0])) $body = $output['parts'][0]["text"][0];
	elseif (isset($output['parts'][0]) && isset($output['parts'][0]['parts'][0]) && isset($output['parts'][0]['parts'][0]["text"][0])) $body = $output['parts'][0]['parts'][0]["text"][0];
	else $body = '';

	return $body;
}
/**
 * @param $output
 * @return HTML string
 */
function mailin_get_html($output)
{
	if (isset($output['html'][0])) {
		$html = $output["html"][0];
	} elseif (isset($output['parts'][1]) && isset($output['parts'][1]["html"][0])) {
		$html = $output['parts'][1]["html"][0];
	} elseif (isset($output['parts'][0]) && isset($output['parts'][0]['parts'][1]) && isset($output['parts'][0]['parts'][1]["html"][0])) {
		$html = $output['parts'][0]['parts'][1]["html"][0];
	} elseif (isset($output['parts'][0]) && isset($output['parts'][0]['parts'][0]) && isset($output['parts'][0]['parts'][0]['parts'][1]) && isset($output['parts'][0]['parts'][0]['parts'][1]["html"][0])) {
		$html = $output['parts'][0]['parts'][0]['parts'][1]["html"][0];
	} else {
		$html = '';
	}
	return $html;
}
/**
 * @param $body
 * @return parsed content
 */
function mailin_parse_body($body, $acc)
{
	global $prefs;

	$is_html = false;
	$wysiwyg = NULL;
	if (mailin_containsStringHTML($body)) {
		$is_html = true;
		$wysiwyg = 'y';
	}

	if ($is_html && $acc['save_html'] === 'y') {
		// Keep HTML setting. Always save as HTML
		return array(
			'body'=>$body,
			'is_html'=>$is_html,
			'wysiwyg'=>$wysiwyg,
			);
	}
	if ($prefs['feature_wysiwyg'] === 'y' && $prefs['wysiwyg_default'] === 'y' && $prefs['wysiwyg_htmltowiki'] !== 'y' ) {
		// WYSIWYG HTML editor is active
		$is_html = true;
		$wysiwyg = 'y';
		return array(
			'body'=>$body,
			'is_html'=>$is_html,
			'wysiwyg'=>$wysiwyg,
			);
	}
	
	if ($is_html) {
		include_once "lib/wiki/editlib.php";
		$editlib = new EditLib;
		$body = $editlib->parseToWiki($body);
		$is_html = false;
		$wysiwyg = NULL;
	}
	return array(
		'body'=>$body,
		'is_html'=>$is_html,
		'wysiwyg'=>$wysiwyg,
		);
}

function mailin_containsStringHTML($str) 
{
	return preg_match ('/<[^>]*>/', $str) == 1;	
}

/**
 * This is function mailin_extract_inline_images
 * NOTE: Will force the use the HTML source as the page body
 * HTML is required in order to be able to identify the image in the email.
 *
 * @param string $pageName The name of the wiki page
 * @param mixed $output Array of email values
 * @param string $body The email body. Will be used as the wiki page body
 * @param string $out Log output
 * @param string $user The user; for logging purposes
 * @return nothing
 *
 */
function mailin_extract_inline_images($pageName, $output, &$body, &$out, $user)
{
	global $wikilib, $is_html, $show_inlineImages, $can_addAttachment;
	
	if($show_inlineImages !== 'y') {
		return;
	}
	if ($can_addAttachment !== 'y') {
		return;
	}
	
	$cnt = 0;
	$errCnt = 0;
	if (!isset($output["parts"])) {
		return;
	}

	// Only for HTML email
	$html = mailin_get_html($output);
	if(empty($html)) {
		$out.= "inline attachments are only supported for email in html format<br />";
		return;
	}
	
	// Replace the text version, and use use the HTML as the page body
	
	// Locate the HTML
	$matches = array();
	preg_match("/<body[^>]*>(.*?)<\/body>/is", $html, $matches);
	$htmlBody = $matches[1];
	if (empty($htmlBody)) {
		// Assume the html is the body
		$htmlBody = $html;
	}
	
	// Get rid of "id" attributes, as they may cause a failure to load the image
	$htmlBody = str_ireplace('id=', 'xid=', $htmlBody);
	
	// Assign the HTML as the new body
	$body = $htmlBody;
	$is_html = true;

	// Locate the page with inline attachments	
	// Check deep level first, to avoid detecting extra, non-inlined attachments
	$activeParts = array();
	if (isset($output["parts"][0]["parts"][0]['parts'][1]["type"]) && isset($output["parts"][0]["parts"][0]["parts"][1]['ctype_parameters']['name'])) {
		$activeParts = $output["parts"][0]["parts"];
	} elseif (isset($output["parts"][0]['parts'][1]["type"]) && isset($output["parts"][0]["parts"][1]['ctype_parameters']['name'])) {
		$activeParts = $output["parts"][0]['parts'][1];
	} elseif (isset($output["parts"][1]) && isset($output["parts"][1]['ctype_parameters']['name'])) {
		$activeParts = $output["parts"];
	} 
	
	// Scroll the page attachments
	for ($it = 0, $count_outputparts = count($activeParts); $it < $count_outputparts; $it++) {
		if (isset($activeParts[$it]["ctype_parameters"]["name"])) {
			$attachmentPart = $activeParts[$it];
			$fileName = $attachmentPart["ctype_parameters"]["name"];
			if (isset($attachmentPart["type"])) {
				$fileType = $attachmentPart["type"];
			} else {
				$fileType = "";
			}
			
			// Only process images
			if(strpos($fileType,'image/',0) === false) {
				$errCnt++;
				continue;
			}
			
			// Process inline image
			$fileData = $attachmentPart['body'];
			$fileSize = function_exists('mb_strlen') ? mb_strlen($fileData, '8bit') : strlen($fileData);
			$attId = $wikilib->wiki_attach_file($pageName, $fileName, $fileType, $fileSize, $fileData, "inline image by mail", $user, "");
			$cnt++;
		
			$contentId = $attachmentPart['header']['content-id'];
			if (empty($contentId)) {
				$errCnt++;
				continue;
			}
			$contentId = str_replace("<", "", $contentId);
			$contentId = str_replace(">", "", $contentId);

			mailin_insert_inline_image($body, $contentId, $attId, $pageName);
		}
	}
	$out.= $cnt;
	$out.= " inline attachment(s) added. ".$errCnt." failed<br />";
}

function mailin_insert_inline_image(&$body, $contentId, $attId, $pageName)
{
	$search = array();
	$replace = array();
	$search[] = 'cid:'.$contentId;		// This string may differ depending on the senders email client, I guess. Tested using Outlook 2010 as the sender
	$replace[] = 'tiki-download_wiki_attachment.php?attId='.$attId.'&page='.urlencode($pageName);
	
	$newBody = str_replace($search, $replace, $body);
	if($newBody == $body) {
		$errCnt++;
		continue;
	}
	$body = $newBody;
}

/**
 * The tiki-mailin.php script is used to get / set wiki pages or articles
 * using a POP email account.
 */
global $debugger;
if (defined($debugger)) $debugger->msg("tiki-mailin-code.php");
// Get a list of ACTIVE emails accounts configured for mailin procedures
$accs = $mailinlib->list_active_mailin_accounts(0, -1, 'account_desc', '');

if (empty($accs['data'])) {
	$content = '';
	return;
}
$content = '<br /><br />';

$userlib = TikiLib::lib('user');

// foreach account
foreach ($accs['data'] as $acc) {
	
	$show_inlineImages = $acc['show_inlineImages'];
	$can_addAttachment = $prefs['feature_wiki_attachments'];
	if($can_addAttachment === 'y') {
		$can_addAttachment = $acc['attachments'];
	}
	
	if (empty($acc['account'])) {
		continue;
	}
	
	$content.= "<b>Processing account</b><br />";
	$content.= "Account :" . $acc['account'] . "<br />";
	$content.= "Type    :" . $acc['type'] . "<br />";
	$content.= "--------------------------<br />";

	$pop3 = new Net_Pop3();
	$content.= "Connecting...";
	if ($pop3->connect($acc["pop"], $acc["port"])) {
		$content.= "OK.<br />";
		$content.= "Logging in...";
		
		// Login
		if ($status = ($pop3->login($acc["username"], $acc["pass"], "USER")) !== FALSE) {
			$content.= "OK (" . $status . ").<br />";
			if (defined($debugger)) $debugger->msg("Logged in, status " . $status);
		} else {
			$content.= "FAILED.<br />";
			if (defined($debugger)) {
				$debugger->msg("Login failed, status " . $status);
				$debugger->msg("dump of pop3:");
				$debugger->var_dump('$pop3');
			}
		}
		$mailsum = $pop3->numMsg();
		if ($mailsum === FALSE) {
			$content.= "No messages.<br />";
			if (defined($debugger)) {
				$debugger->msg("No messages.");
				$debugger->msg("dump of pop3:");
				$debugger->var_dump('$pop3');
			}
		} else {
			$content.= "Messages:" . $mailsum . "<br />";
			for ($i = 1; $i <= $mailsum; $i++) {
				$aux = $pop3->getParsedHeaders($i);
				if ($aux === FALSE) {
					$content.= "Headers not parsed.<br />";
				} else { // else $aux not FALSE
					if (!isset($aux["From"])) {
						$aux['From'] = $aux['Return-path'];
					}
					preg_match('/<?([-!#$%&\'*+\.\/0-9=?A-Z^_`a-z{|}~]+@[-!#$%&\'*+\/0-9=?A-Z^_`a-z{|}~]+\.[-!#$%&\'*+\.\/0-9=?A-Z^_`a-z{|}~]+)>?/', $aux["From"], $mail);
					$email_from = $mail[1];
					$aux["msgid"] = $i;
					$aux["realmsgid"] = preg_replace('/[<>]/', '', $aux["Message-ID"]);
					$message = $pop3->getMsg($i);
					$mimelib = new mime();
					$output = $mimelib->decode($message);

					$content.= "<br />Reading a request.<br />From: " . $aux["From"] . "<br />Subject: " . $output['header']['subject'] . "<br />";
					$content.= "sender email: " . $email_from . "<br />";
					$aux["sender"]["user"] = $userlib->get_user_by_email($email_from);
					$content.= "sender user: " . $aux["sender"]["user"] . "<br />";
					$cantUseMailIn = $acc["anonymous"] == 'n' && empty($aux["sender"]["user"]);
					if ($cantUseMailIn) {
						$content.= "Anonymous user access denied, sending auto-reply to email address:&nbsp;" . $aux["From"] . "<br />";
						$mail = new TikiMail();
						$mail->setFrom($acc["account"]);
						$l = $prefs['language'];
						$mail->setSubject(tra('Tiki mail-in auto-reply', $l));
						$mail->setText(tra("Sorry, you can't use this feature.", $l));
						if ($acc['respond_email'] === 'y') {
							$res = $mail->send(array($email_from), 'mail');
							$content.= "Response sent<br />";
						} else {
							$content.= "Response by email is disabled<br />";
						}
					} else {
						
						$processEmail = true;
						
						// Validate user's send permission
						$chkUser = $aux["sender"]["user"];
						if (($acc["anonymous"] == 'n') && (!$userlib->user_has_permission($chkUser, 'tiki_p_admin'))) {

							if (!$userlib->user_has_permission($chkUser, 'tiki_p_send_mailin')) {
								$content.= "Access denied, sending auto-reply to email address:&nbsp;" . $aux["From"] . "<br />";
								$mail = new TikiMail();
								$mail->setFrom($acc["account"]);
								$l = $prefs['language'];
								$mail->setSubject(tra('Tiki mail-in auto-reply', $l));
								$mail->setText(tra("Sorry, you can't use this feature.", $l));
								if ($acc['respond_email'] === 'y') {
									$res = $mail->send(array($email_from), 'mail');
									$content.= "Response sent<br />";
								} else {
									$content.= "Response by email is disabled<br />";
								}
								$processEmail = false;
							}
						} 
						if (($acc["admin"] === 'n') && ($userlib->user_has_permission($chkUser, 'tiki_p_admin'))) {
								$content.= "Admin access is blocked, sending auto-reply to email address:&nbsp;" . $aux["From"] . "<br />";
								$mail = new TikiMail();
								$mail->setFrom($acc["account"]);
								$l = $prefs['language'];
								$mail->setSubject(tra('Tiki mail-in auto-reply', $l));
								$mail->setText(tra("Sorry, you can't use this feature.", $l));
								if ($acc['respond_email'] === 'y') {
									$res = $mail->send(array($email_from), 'mail');
									$content.= "Response sent<br />";
								} else {
									$content.= "Response by email is disabled<br />";
								}
								$processEmail = false;
						}
						
						if ($processEmail) {
							
							if (empty($aux["sender"]["user"])) {
								$aux["sender"]["user"] = $email_from;
							}
							if (empty($aux["sender"]["name"])) {
								$aux["sender"]["name"] = $email_from;
							}
							
							if ($prefs['feature_articles'] && $acc['type'] == 'article-put') {
								//////////////
								//	article-put
								//////////////////////////////////////////////////////////////////////////////////

								$title = trim($output['header']['subject']);
								$topicId = isset($acc['article_topicId']) ? $acc['article_topicId'] : 0;
								$chkUser = $aux["sender"]["user"];
								if (($acc["anonymous"] == 'n') && (!$userlib->user_has_permission($chkUser, 'tiki_p_admin'))) {
									if (!$wikilib->user_has_perm_on_object($chkUser, $topicId, 'topic', 'tiki_p_submit_article', 'tiki_p_edit_submission')) {
										$content.= $chkUser." cannot submit the article: ".$title."<br />";
										$processEmail = false;
									} if ($tiki_p_autoapprove_submission == 'y') {
										if (!$wikilib->user_has_perm_on_object($chkUser, $topicId, 'topic', 'tiki_p_autoapprove_submission)')) {
											$content.= $chkUser." cannot auto-approve the article: ".$title."<br />";
											$processEmail = false;
										}
									}
								}
								if ($processEmail) {
									
									// This is used to CREATE articles
									$title = trim($output['header']['subject']);
									$msgbody = mailin_get_body($output);
									if ($msgbody && !empty($acc['discard_after'])) {
										$msgbody = preg_replace("/" . $acc['discard_after'] . ".*$/s", "", $msgbody);
									}
									$heading = $msgbody;
									$topicId = isset($acc['article_topicId']) ? $acc['article_topicId'] : 0;
									$userm = $aux['sender']['user'];
									$authorName = $userm;
									$body = '';
									$publishDate = $tikilib->now;
									$cur_time = explode(',', $tikilib->date_format('%Y,%m,%d,%H,%M,%S', $publishDate));
									$expireDate = $tikilib->make_time($cur_time[3], $cur_time[4], $cur_time[5], $cur_time[1], $cur_time[2], $cur_time[0] + 1);
									$subId = 0;
									$type = $acc['article_type'];
									$useImage = 'n';
									$image_x = '';
									$image_y = '';
									$imgname = '';
									$imgsize = '';
									$imgtype = '';
									$imgdata = '';
									$topline = '';
									$subtitle = '';
									$linkto = '';
									$image_caption = '';
									$lang = '';
									$rating = 7;
									$isfloat = 'n';
									global $artlib;
									if (!is_object($artlib)) {
										include_once ('lib/articles/artlib.php');
									}
									$subid = $artlib->replace_submission($title, $authorName, $topicId, $useImage, $imgname, $imgsize, $imgtype, $imgdata, $heading, $body, $publishDate, $expireDate, $userm, $subId, $image_x, $image_y, $type, $topline, $subtitle, $linkto, $image_caption, $lang, $rating, $isfloat);
									global $tiki_p_autoapprove_submission;
									if ($tiki_p_autoapprove_submission == 'y') {
										$artlib->approve_submission($subid);
										$content.= "Article: $title has been submitted<br />";
									} else {
										$content.= "Article: $title has been created<br />";
									}
								}
							} else { //else if ($acc['type'] == 'article-put')
								if ($acc['type'] == 'wiki') {
									$p_page = trim($aux['Subject']);
									$parts = explode(':', $p_page);
									if (!isset($parts[1])) {
										$parts[1] = $parts[0];
										$parts[0] = 'GET';
									}
									$method = $parts[0];
									$page = $parts[1];
								} else {
									$page = trim($aux['Subject']);
								}
								
								// Strip invalid characters from the page name 
								$wikilib = TikiLib::lib('wiki');
								if($wikilib->contains_badchars($page)) {
									$badChars = $wikilib->get_badchars();
									
									// Replace bad characters with a '_'
									for ($j = 0; $j < strlen($badChars); $j++) { 
										$char = $badChars[$j];
										$page = str_replace($char, "_", $page);
									}
								}
								
								if ($acc['type'] == 'wiki-get' || ($acc['type'] == 'wiki' && $method == "GET")) {
									//////////////
									//	wiki-get, wiki GET: Get a new wiki page. System emails page to user
									//////////////////////////////////////////////////////////////////////////////////
									// A wiki-get account sends a copy of the page to the sender
									// and also sends the source of the page
									$mail = new TikiMail();
									$mail->setFrom($acc["account"]);
									if ($tikilib->page_exists($page)) {
										
										// Check permissions
										$chkUser = $aux["sender"]["user"];
										if (($acc["anonymous"] == 'n') && (!$userlib->user_has_permission($chkUser, 'tiki_p_admin'))) {
											if(!$wikilib->user_has_perm_on_object($chkUser, $page, 'wiki page', 'tiki_p_view')) {
												$content.= $chkUser." cannot view the page: ".$page."<br />";
												$processEmail = false;
											}
										}
										if ($processEmail) {
											$mail->setSubject($page);
											$info = $tikilib->get_page_info($page);
											$data = $tikilib->parse_data($info["data"]);
											$mail->addAttachment($info['data'], 'source.txt', 'plain/txt');
											$mail->setHTML($data, strip_tags($data));
										}
									} else {
										$l = $prefs['language'];
										$mail_data = $smarty->fetchLang($l, "mail/mailin_reply_subject.tpl");
										$mail->setSubject($mail_data . $page);
									}
									$res = $mail->send(array($email_from), 'mail');
									$content.= "Response sent<br />";
									//end if ($acc['type'] == 'wiki-get' || ($acc['type'] == 'wiki' && $method == "GET"))

								} elseif ($acc['type'] == 'wiki-put' || ($acc['type'] == 'wiki' && $method == "PUT")) {
									//////////////
									//	wiki-put, wiki PUT: Send a wiki page. User emails page to System
									//////////////////////////////////////////////////////////////////////////////////
									
									// This is used to Create/Update wiki pages
									$chkUser = $aux["sender"]["user"];

									// Attempt to use HTML, if it exists
									$body = mailin_get_html($output);
									if (empty($body)) {
										$body = mailin_get_body($output);
									}
									
									// Load user routing
									$route = null;
									$routes = $usermailinlib->locate_struct($chkUser, $aux['Subject'], $body);
									if (!empty($routes['data'])) {
										$content.= "User route from pattern: '".$routes['data'][0]['subj_pattern']."' / '".$routes['data'][0]['body_pattern']."'<br />";
										$route = $routes['data'][0];	// Only use the first route
									}


									// Check permissions
									if (($acc["anonymous"] == 'n') && (!$userlib->user_has_permission($chkUser, 'tiki_p_admin'))) {
										if ($tikilib->page_exists($page)) {
											// Check permissions for page
											if (!$wikilib->user_has_perm_on_object($chkUser, $page, 'wiki page', 'tiki_p_edit')) {
												$content.= $chkUser." cannot edit the page: ".$page."<br />";
												$processEmail = false;
											}
											if (!$wikilib->user_has_perm_on_object($chkUser, $page, 'wiki page', 'tiki_p_wiki_attach_files')) {
												$can_addAttachment = 'n';
												$show_inlineImages = 'n';
											}
										} else {
											// Check category permission, if auto-assigning a category.
											// Otherwise checkglobal permissions
											$userlib = TikiLib::lib('user');
											if ($prefs['feature_categories'] && isset($acc['categoryId'])) {
												if (!$userlib->object_has_permission($chkUser, $acc['categoryId'], 'category', 'tiki_p_edit')) {
													$content.= $chkUser." cannot create the page: ".$page."<br />";
													$processEmail = false;
												}
												if (!$userlib->object_has_permission($chkUser, $acc['categoryId'], 'category', 'tiki_p_wiki_attach_files')) {
													$can_addAttachment = 'n';
													$show_inlineImages = 'n';
												}
												if (!empty($routes)) {
													if (!$userlib->object_has_permission($chkUser, $acc['categoryId'], 'category', 'tiki_p_edit_structures')) {
														$content.= $chkUser." cannot edit structures: ".$page."<br />";
														$processEmail = false;
													}
												}
											} else {
												if (!$userlib->user_has_permission($chkUser, 'tiki_p_edit')) {
													$content.= $chkUser." cannot create the page: ".$page."<br />";
													$processEmail = false;
												}
												if (!$userlib->user_has_permission($chkUser, 'tiki_p_wiki_attach_files')) {
													$can_addAttachment = 'n';
													$show_inlineImages = 'n';
												}
												if (!empty($routes)) {
													if (!$userlib->user_has_permission($chkUser, 'tiki_p_edit_structures')) {
														$content.= $chkUser." cannot edit structures: ".$page."<br />";
														$processEmail = false;
													}
												}
											}
										}
									}
									if ($processEmail) {

										// Add namespace, if specified.
										// If no explicit namespace is specified, pages routed to structured may inherit, the structure namespace
										if ($prefs['namespace_enabled'] === 'y') {
											$nsName = trim($acc['namespace']);
											$ns = $prefs['namespace_separator'];
											if (!empty($nsName)) {
												// Use mail-in specified namespace
												if (!empty($ns)) {
													$page = $nsName.$ns.$page;
												}
											} elseif (!empty($route)) {
												// Inherit structure namespace
												$wikilib = TikiLib::lib('wiki');
												$nsName = $wikilib->get_namespace($route['structName']);
												if (!empty($nsName)) {
													if (!empty($ns)) {
														$page = $nsName.$ns.$page;
													}
												}												
											}
										}

										if (!empty($acc['discard_after']) && $body) {
											$body = preg_replace("/" . $acc['discard_after'] . ".*$/s", "", $body);
										}
										if (!empty($body)) {
											if ($prefs['feature_wiki_attachments'] === 'y') {
												mailin_extract_inline_images($page, $output, $body, $content, $aux["sender"]["user"]);
												mailin_check_attachments($output, $content, $page, $aux["sender"]["user"], $body);
											}

											$parsed_data = mailin_parse_body($body, $acc);
											$body = $parsed_data['body'];

											if (!$tikilib->page_exists($page)) {

												// Check User structure routing
												if (!empty($route)) {
													// Structure routing is active. Create a structure node/page
													$parent_id = $route['page_ref_id'];
													$structure_id = $route['structure_id'];
													$begin = true;
														
													$after_ref_id = null;
													$alias='';
													$options = array();
														
													$options['hide_toc'] = 'y';
													$options['creator'] = tra('mail-in');
													$options['creator_msg'] = tra('created from mail-in');
													$options['ip_source'] = '0.0.0.0';
														
													$structlib = TikiLib::lib('struct');
													$structlib->s_create_page($parent_id, $after_ref_id, $page, $alias, $structure_id, $options);
													$content.= "Page: $page has been added to structureId: ".$structure_id."<br />";

													$tikilib->update_page($page, $body, "Updated from " . $acc["account"],
																			$aux["sender"]["user"],
																			$options['ip_source'],
																			'',	//desc
																			0, 	//edit_minor
																			'',	//lang
																			$parsed_data['is_html'],	//is_html
																			'',	//hash
																			null,	//saveLastModif
																			$parsed_data['wysiwyg']	//wysiwyg
													);
													$content.= "Page: $page has been updated<br />";
												} else {
													
													// Create a regular page
													$tikilib->create_page($page, 0, $body, $tikilib->now, "Created from " . $acc["account"], $aux["sender"]["user"],
														'0.0.0.0',
														'',						//description
														'',						//lang
														$parsed_data['is_html'],	//is_html
														'',						//hash
														$parsed_data['wysiwyg']	//wysiwyg
														);
													$content.= "Page: $page has been created<br />";

													// Assign category, if specified
													if ($prefs['feature_categories'] && isset($acc['categoryId'])) {
														try {
															$categoryId = intval($acc['categoryId']);
															if ($categoryId > 0) {
																// Validate the category before adding it
																$categlib = TikiLib::lib('categ');
																$categories = $categlib->get_category($categoryId);
																if ($categories !== false && !empty($categories)) {
																	$categlib->categorizePage($page, $categoryId, $aux["sender"]["user"]);
																	$content.= "Page: $page categorized. Id: ".$categoryId."<br />";
																} else {
																	$content.= "Page: $page not categorized. Invalid categoryId: ".$categoryId."<br />";
																}
															}
														} catch (Exception $e) {
															$content.= "Failed to categorize page: $page  categoryId: ".$categoryId.". Error: ".$e->getMessage()."<br />";
														}
													}
												}
												
											} else {
												$tikilib->update_page($page, $body, "Updated from " . $acc["account"],
																		$aux["sender"]["user"],
																		'0.0.0.0',
																		'',	//desc
																		0, 	//edit_minor
																		'',	//lang
																		$parsed_data['is_html'],	//is_html
																		'',	//hash
																		null,	//saveLastModif
																		$parsed_data['wysiwyg']	//wysiwyg
												);
												$content.= "Page: $page has been updated<br />";
											}
										}
									}
								} elseif ($acc['type'] == 'wiki-append' || $acc['type'] == 'wiki-prepend' || ($acc['type'] == 'wiki' && $method == "APPEND") || ($acc['type'] == 'wiki' && $method == "PREPEND")) {
									//////////////
									//	wiki-append, wiki-prepend, wiki APPEND, wiki PREPEND
									//////////////////////////////////////////////////////////////////////////////////

									// Check permissions
									$chkUser = $aux["sender"]["user"];
									if (($acc["anonymous"] == 'n') && (!$userlib->user_has_permission($chkUser, 'tiki_p_admin'))) {
										if(!$wikilib->user_has_perm_on_object($chkUser, $page, 'wiki page', 'tiki_p_edit')) {
											$content.= $chkUser." cannot edit the page: ".$page."<br />";
											$processEmail = false;
										}
										if(!$wikilib->user_has_perm_on_object($chkUser, $page, 'wiki page', 'tiki_p_wiki_attach_files')) {
											$can_addAttachment = 'n';
											$show_inlineImages = 'n';
										}
									}
									if ($processEmail) {

										// Attempt to use HTML, if it exists
										$body = mailin_get_html($output);
										if (empty($body)) {
											$body = mailin_get_body($output);
										}

										if ($body && !empty($acc['discard_after'])) {
											$body = preg_replace("/" . $acc['discard_after'] . ".*$/s", "", $body);
										}
										$parsed_data = mailin_parse_body($body, $acc);
										$body = $parsed_data['body'];
										if (isset($body)) {
											if ($prefs['feature_wiki_attachments'] === 'y') {
												mailin_extract_inline_images($page, $output, $body, $content, $aux["sender"]["user"]);
												mailin_check_attachments($output, $content, $page, $aux["sender"]["user"], $body);
											}
											if (!$tikilib->page_exists($page)) {
												$tikilib->create_page($page, 0, $body, $tikilib->now, "Created from " . $acc["account"], $aux["sender"]["user"],
																	 '0.0.0.0',
																	 '',						//description
																	 '',						//lang
																	 $parsed_data['is_html'],	//is_html
																	 '',						//hash
																	 $parsed_data['wysiwyg']	//wysiwyg
												);
												$content.= "Page: $page has been created<br />";
											} else {
												$info = $tikilib->get_page_info($page);
												if ($acc['type'] == 'wiki-append' || $acc['type'] == 'wiki' && $method == "APPEND") {
													$body = $info['data'] . $body;
												} else {
													$body = $body . $info['data'];
												}
												$tikilib->update_page($page, $body, "Created from " . $acc["account"],
																	$aux["sender"]["user"],
																	'0.0.0.0',
																	'',	//desc
																	0, 	//edit_minor
																	'',	//lang
																	$parsed_data['is_html'],	//is_html
																	'',	//hash
																	null,	//saveLastModif
																	$parsed_data['wysiwyg']	//wysiwyg
												);
												$content.= "Page: $page has been updated<br />";
											}
										}
									}
								} else {
									//////////////
									//	Invalid mail-in type
									//////////////////////////////////////////////////////////////////////////////////
									$mail = new TikiMail();
									$mail->setFrom($acc["account"]);
									$l = $prefs['language'];
									$mail_data = $smarty->fetchLang($l, "mail/mailin_help_subject.tpl");
									$mail->setSubject($mail_data);
									$smarty->assign('subject', $output['header']['subject']);
									$mail_data = $smarty->fetchLang($l, "mail/mailin_help.tpl");
									$mail->setText($mail_data);
									if ($acc['respond_email'] === 'y') {
										$res = $mail->send(array($email_from), 'mail');
										$content.= "Help response sent<br />";
									} else {
										$content.= "Response by email is disabled<br />";
									}
								}
							}
						}						
					}
					// Remove the email from the pop3 server
					$pop3->deleteMsg($i);
					$content.= "Deleted message<br />";
				}
				
			}
			
		}
		
	} else {
		$content.= "FAILED.<br />";
	}
	$pop3->disconnect();
	
}
