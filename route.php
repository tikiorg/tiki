<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// Adding support for an other web server? Check the end of the file

/**
 * Routing method, receives the path portion of the URL relative to tiki root.
 * http://example.com/tiki/hello-world?foo-bar
 * $path is expectedto be hello-world
 */
function tiki_route($path)
{
	/*
	// If you are converting to Tiki and want to preserve some URLs, map the urls and remove the comment block
	$urlMapping = array(
		'wiki/old-page-name' => 'PageName',
		'corporate/Privacy+Policy.pdf' => 'dl123',
	);

	if (isset($urlMapping[$path])) {
		$path = $urlMapping[$path];
	}
	*/


	$simple = array(
		'articles' => 'tiki-view_articles.php',
		'blogs' => 'tiki-list_blogs.php',
		'calendar' => 'tiki-calendar.php',
		'categories' => 'tiki-browse_categories.php',
		'chat' => 'tiki-chat.php',
		'contact' => 'tiki-contact.php',
		'directories' => 'tiki-directory_browse.php',
		'faqs' => 'tiki-list_faqs.php',
		'filelist' => 'tiki-list_file_gallery.php',
		'forums' => 'tiki-forums.php',
		'galleries' => 'tiki-galleries.php',
		'login' => 'tiki-login_scr.php',
		'logout' => 'tiki-logout.php',
		'my' => 'tiki-my_tiki.php',
		'newsletters' => 'tiki-newsletters.php',
		'quizzes' => 'tiki-list_quizzes.php',
		'register' => 'tiki-register.php',
		'sheets' => 'tiki-sheets.php',
		'stats' => 'tiki-stats.php',
		'surveys' => 'tiki-list_surveys.php',
		'trackers' => 'tiki-list_trackers.php',
		'users' => 'tiki-list_users.php',
	);

	foreach ($simple as $key => $file) {
		tiki_route_attempt("|^$key$|", $file);
	}

	/*
		Valid:

		art123
		article123
		art123-XYZ
		article123-XYZ
	*/
	tiki_route_attempt('/^(art|article)(\d+)(\-.*)?$/', 'tiki-read_article.php', tiki_route_single(2, 'articleId'));

	tiki_route_attempt('|^blog(\d+)(\-.*)?$|', 'tiki-view_blog.php', tiki_route_single(1, 'blogId'));
	tiki_route_attempt('|^blogpost(\d+)(\-.*)?$|', 'tiki-view_blog_post.php', tiki_route_single(1, 'postId'));
	tiki_route_attempt('|^cat(\d+)(\-.*)?$|', 'tiki-browse_categories.php', tiki_route_single(1, 'parentId'));
	tiki_route_attempt_prefix('browseimage', 'tiki-browse_image.php', 'imageId');
	tiki_route_attempt('/^event(\d+)(\-.*)?$/', 'tiki-calendar_edit_item.php', tiki_route_single(1, 'viewcalitemId'));

	tiki_route_attempt(
		'|^cal(\d[\d,]*)$|',
		'tiki-calendar.php',
		function ($parts) {
			$ids = explode(',', $parts[1]);
			$ids = array_filter($ids);
			return array('calIds' => $ids);
		}
	);

	tiki_route_attempt_prefix('directory', 'tiki-directory_browse.php', 'parent');
	tiki_route_attempt_prefix('dirlink', 'tiki-directory_redirect.php', 'siteId');

	tiki_route_attempt_prefix('faq', 'tiki-view_faq.php', 'faqId');
	tiki_route_attempt_prefix('file', 'tiki-list_file_gallery.php', 'galleryId');
	tiki_route_attempt_prefix('forum', 'tiki-view_forum.php', 'forumId');
	tiki_route_attempt_prefix('forumthread', 'tiki-view_forum_thread.php', 'comments_parentId');
	tiki_route_attempt_prefix('gallery', 'tiki-browse_gallery.php', 'galleryId');
	tiki_route_attempt_prefix('img', 'show_image.php', 'id');
	tiki_route_attempt_prefix('image', 'show_image.php', 'id');
	tiki_route_attempt(
		'|^imagescale(\d+)/(\d+)$|',
		'show_image.php',
		function ($parts) {
			return array(
				'id' => $parts[1],
				'scalesize' => $parts[2],
			);
		}
	);
	tiki_route_attempt_prefix('int', 'tiki-integrator.php', 'repID');
	tiki_route_attempt_prefix('item', 'tiki-view_tracker_item.php', 'itemId');
	tiki_route_attempt_prefix('newsletter', 'tiki-newsletters.php', 'nlId', array('info' => '1'));
	tiki_route_attempt_prefix('nl', 'tiki-newsletters.php', 'nlId', array('info' => '1'));
	tiki_route_attempt_prefix('poll', 'tiki-poll_form.php', 'pollId');
	tiki_route_attempt_prefix('quiz', 'tiki-take_quiz.php', 'quizId');
	tiki_route_attempt_prefix('survey', 'tiki-take_survey.php', 'surveyId');
	tiki_route_attempt_prefix('tracker', 'tiki-view_tracker.php', 'trackerId');
	tiki_route_attempt_prefix('sheet', 'tiki-view_sheets.php', 'sheetId');
	tiki_route_attempt_prefix('user', 'tiki-user_information.php', 'userId');
	tiki_route_attempt('|^userinfo$|', 'tiki-view_tracker_item.php', function () { return array('view' => ' user'); });

	tiki_route_attempt_prefix('dl', 'tiki-download_file.php', 'fileId');
	tiki_route_attempt_prefix('thumbnail', 'tiki-download_file.php', 'fileId', array('thumbnail' => ''));
	tiki_route_attempt_prefix('display', 'tiki-download_file.php', 'fileId', array('display' => ''));
	tiki_route_attempt_prefix('preview', 'tiki-download_file.php', 'fileId', array('preview' => ''));

	tiki_route_attempt(
		'/^(wiki|page)\-(.+)$/',
		'tiki-index.php',
		function ($parts) {
			return array('page' => $parts[2]);
		}
	);
	tiki_route_attempt(
		'/^show:(.+)$/',
		'tiki-slideshow.php',
		function ($parts) {
			return array('page' => urldecode($parts[1]));
		}
	);

	tiki_route_attempt(
		'|^tiki\-(\w+)\-(\w+)$|',
		'tiki-ajax_services.php',
		function ($parts) {
			if ($parts[2] == 'x') {
				return array(
					'controller' => $parts[1],
				);
			} else {
				return array(
					'controller' => $parts[1],
					'action' => $parts[2],
				);
			}
		}
	);

	if (false !== $dot = strrpos($path, '.')) {
		// Prevent things that look like filenames from being considered for wiki page names
		$extension = substr($path, $dot + 1);
		if (in_array($extension, array('css', 'gif', 'jpg', 'png', 'php', 'html', 'js', 'htm', 'shtml', 'cgi', 'sql', 'phtml', 'txt', 'ihtml'))) {
			return;
		}
	}

	tiki_route_attempt(
		'|.*|',
		'tiki-index.php',
		function ($parts) {
			return array('page' => urldecode($parts[0]));
		}
	);
}

function tiki_route_attempt($pattern, $file, $callback = null, $extra = array())
{
	global $path, $inclusion, $base, $full;

	if ($inclusion) {
		return;
	}

	if (preg_match($pattern, $path, $parts)) {
		$inclusion = $file;

		$full = $base . $file;

		if ($callback && is_callable($callback)) {
			$_GET = array_merge($_GET, $callback($parts), $extra);
		}
	}
}

function tiki_route_attempt_prefix($prefix, $file, $key, $extra = array())
{
	tiki_route_attempt("|^$prefix(\d+)$|", $file, tiki_route_single(1, $key), $extra);
}

function tiki_route_single($index, $name)
{
	return function ($parts) use ($index, $name) {
		return array($name => $parts[$index]);
	};
}

$sapi = php_sapi_name();
$base = null;
$path = null;
$inclusion = null;

// This portion may need to vary depending on the webserver/configuration

switch ($sapi) {
case 'apache2handler':
default:

	// Fix $_SERVER['REQUEST_URI', which is ASCII encoded on IIS
	//	Convert the SERVER variable itself, to fix $_SERVER['REQUEST_URI'] access everywhere
	//	route.php comes first in the processing.  Avoid dependencies.
	if (strpos($_SERVER['SERVER_SOFTWARE'],'IIS') !== false) {
		if (mb_detect_encoding($_SERVER['REQUEST_URI'], 'UTF-8', true) == false) {
			$_SERVER['REQUEST_URI'] = utf8_encode($_SERVER['REQUEST_URI']);
		}
	}

	if (isset($_SERVER['SCRIPT_URL'])) {
		$full = $_SERVER['SCRIPT_URL'];
	} elseif (isset($_SERVER['REQUEST_URI'])) {
		$full = $_SERVER['REQUEST_URI'];
		if (strpos($full, '?') !== false) {
			$full = substr($full, 0, strpos($full, '?'));
		}
	} elseif (isset($_SERVER['REDIRECT_URL'])) {
		$full = $_SERVER['REDIRECT_URL'];
	} elseif (isset($_SERVER['UNENCODED_URL'])) {	// For IIS
		$full = $_SERVER['UNENCODED_URL'];
	} else {
		break;
	}

	$file = basename(__FILE__);
	$base = substr($_SERVER['PHP_SELF'], 0, -strlen($file));
	$path = substr($full, strlen($base));
	break;
}

// Global check

if (is_null($base) || is_null($path)) {
	header('HTTP/1.0 500 Internal Server Error');
	header('Content-Type: text/plain; charset=utf-8');

	echo "Request could not be understood. Verify routing file.";
	exit;
}

tiki_route($path);

if ($inclusion) {
	$_SERVER['PHP_SELF'] = $base . $inclusion;
	$_SERVER['SCRIPT_NAME'] = $base . basename($inclusion);
	include __DIR__ . '/' . $inclusion;
} else {
	error_log("No route found - full:$full query:{$_SERVER['QUERY_STRING']}");

	// Route to the "no-route" URL, if found
	require_once('lib/init/initlib.php');
	$local_php = TikiInit::getCredentialsFile();
	if ( file_exists($local_php) ) {
		include($local_php);
	}
	if (empty($noroute_url)) {
		// Fail
		header('HTTP/1.0 404 Not Found');
		header('Content-Type: text/plain; charset=utf-8');

		echo "No route found. Please see http://dev.tiki.org/URL+Rewriting+Revamp";
	} else {
		header('Location: '.$noroute_url);
	}
	exit;
}

