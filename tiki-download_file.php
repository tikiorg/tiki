<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$force_no_compression = true;
$skip = false;
$thumbnail_format = 'jpeg';

if ( isset($_GET['fileId']) && isset($_GET['thumbnail']) && isset($_COOKIE[ session_name() ]) && count($_GET) == 2 ) {

	$tikiroot = dirname($_SERVER['PHP_SELF']);
	$session_params = session_get_cookie_params();
	session_set_cookie_params($session_params['lifetime'], $tikiroot);
	unset($session_params);
	session_start();

	if ( isset($_SESSION['allowed'][$_GET['fileId']]) ) {
		require_once 'tiki-setup_base.php';

		$query = "select * from `tiki_files` where `fileId`=?";
		$result = $tikilib->query($query, array((int)$_GET['fileId']));
		if ( $result ) {
			$info = $result->fetchRow();
			$skip = true;
		} else {
			$info = array();
		}
	} else {
		session_write_close();
	}
}

if (!$skip) {
	require_once('tiki-setup.php');
	$filegallib = TikiLib::lib('filegal');
	$access->check_feature('feature_file_galleries');
}

if ($prefs["user_store_file_gallery_picture"] == 'y' && isset($_REQUEST["avatar"])) {
	$userprefslib = TikiLib::lib('userprefs');
	if ($user_picture_id = $userprefslib->get_user_picture_id($_REQUEST["avatar"])) {
		$_REQUEST['fileId'] = $user_picture_id;
	} elseif (!empty($prefs['user_default_picture_id'])) {
		$_REQUEST['fileId'] = $prefs['user_default_picture_id'];
	}
}

if ( ! ini_get('safe_mode') ) {
	@set_time_limit(0);
}

$zip = false;
$error = '';

if (!$skip) {
	if ( isset($_REQUEST['fileId']) && !is_array($_REQUEST['fileId'])) {
		if (isset($_GET['draft'])) {
			$info = $filegallib->get_file_draft($_REQUEST['fileId']);
		} else {
			$info = $filegallib->get_file($_REQUEST['fileId']);
		}
	} elseif ( isset($_REQUEST['galleryId']) && isset($_REQUEST['name']) ) {
		$info = $filegallib->get_file_by_name($_REQUEST['galleryId'], $_REQUEST['name']);
	} elseif ( isset($_REQUEST['fileId']) && is_array($_REQUEST['fileId'])) {
		$info = $filegallib->zip($_REQUEST['fileId'], $error);
		$zip = true;
	} elseif ( !empty($_REQUEST['randomGalleryId'])) {
		$info =  $filegallib->get_file(0, $_REQUEST['randomGalleryId']);
	} else {
		$access->display_error('', tra('Incorrect param'), 400);
	}
	if ( ! is_array($info) ) {
		$access->display_error(NULL, tra('File has been deleted'), 404);
	}

	if ( $prefs['auth_token_access'] != 'y' || !$is_token_access ) {
		// Check permissions except if the user comes with a valid Token

		if ( !$zip && $tiki_p_admin_file_galleries != 'y' && !$userlib->user_has_perm_on_object($user, $info['galleryId'], 'file gallery', 'tiki_p_download_files') && !($info['backlinkPerms'] == 'y' && !$filegallib->hasOnlyPrivateBacklinks($info['fileId']))) {
			if (!$user) $_SESSION['loginfrom'] = $_SERVER['REQUEST_URI'];
			$access->display_error('', tra('Permission denied'), 401);
		}
		if ( isset($_GET['thumbnail']) && is_numeric($_GET['thumbnail'])) { //check also perms on thumb 
			$info_thumb = $filegallib->get_file($_GET['thumbnail']);
			if ( !$zip && $tiki_p_admin_file_galleries != 'y' && !$userlib->user_has_perm_on_object($user, $info_thumb['galleryId'], 'file gallery', 'tiki_p_download_files') && !($info['backlinkPerms'] == 'y' && !$filegallib->hasOnlyPrivateBacklinks($info_thumb['fileId']))) {
				if (!$user) $_SESSION['loginfrom'] = $_SERVER['REQUEST_URI'];
				$access->display_error('', tra('Permission denied'), 401);
			}
		}
		if ($prefs['feature_use_fgal_for_user_files'] === 'y' && $tiki_p_admin_file_galleries !== 'y' && $prefs['userfiles_private'] === 'y') {
			$gal_info = $filegallib->get_file_gallery_info($info['galleryId']);
			if ($gal_info['type'] === 'user' && $gal_info['visible'] !== 'y' && $gal_info['user'] !== $user ) {
				$access->display_error('', tra('Permission denied'), 401);
			}
		}
	}
}

//if the file is remote, display, and don't cache
$attributelib = TikiLib::lib('attribute');
$attributes = $attributelib->get_attributes('file', $info['fileId']);

if (isset($attributes['tiki.content.url'])) {
	$smarty->loadPlugin('smarty_modifier_sefurl');
	$src = smarty_modifier_sefurl($info['fileId'], 'file');
	session_write_close();

	$client = $tikilib->get_http_client($src);
	$response = $client->send();
	header('Content-Type: ' . $response->getHeaders()->get('Content-Type'));
	echo $response->getBody();
	exit();
}

// Add hits ( if download or display only ) + lock if set
if ( ! isset($_GET['thumbnail']) && ! isset($_GET['icon']) ) {

	$statslib = TikiLib::lib('stats');
	$filegallib = TikiLib::lib('filegal');
	if ( ! $filegallib->add_file_hit($info['fileId']) ) {
		$access->display_error('', tra('You cannot download this file right now. Your score is low or file limit was reached.'), 401);
	}
	$statslib->stats_hit($info['filename'], 'file', $info['fileId']);

	if ( $prefs['feature_actionlog'] == 'y' ) {
		$logslib = TikiLib::lib('logs');
		$logslib->add_action('Downloaded', $info['galleryId'], 'file gallery', 'fileId='.$info['fileId']);
	}

	if ( ! empty($_REQUEST['lock']) ) {
		if (!empty($info['lockedby']) && $info['lockedby'] != $user) {
			$access->display_error('', tra(sprintf('The file has been locked by %s', $info['lockedby'])), 401);
		}
		$filegallib->lock_file($info['fileId'], $user);
	}
}

session_write_close(); // close the session in case of large downloads to enable further browsing
error_reporting(E_ALL);
while (ob_get_level()>1) {
	ob_end_clean();
}// Be sure output buffering is turned off

$content_changed = false;
$content = &$info['data'];

$md5 = '';
$filepath = '';
if ( ! empty($info['path']) ) {
	if (!$skip and $filegallib->isPodCastGallery($info['galleryId'])) {
		$filepath = $prefs['fgal_podcast_dir'].$info['path'];
	} else {
		$filepath = $prefs['fgal_use_dir'].$info['path'];
	}
	if ( is_readable($filepath) ) {
		$file_stats = stat($filepath);
		$last_modified = $file_stats['mtime'];
		$md5 = empty($info['hash']) ?
			md5($file_stats['mtime'].'='.$file_stats['ino'].'='.$file_stats['size'])
			: md5($info['hash'] . $last_modified);
	} else {
		// File missing or not readable
		header("HTTP/1.0 404 Not Found");
		header('Content-Type: text/plain');		
		echo "Unable to access file: " . ($tiki_p_admin == 'y' ? $filepath : $info['path']);
		die;
	}
} elseif ( ! empty($content) ) {
	$last_modified = $info['lastModif'];
	$md5 = empty($info['hash']) ? md5($content) : md5($info['hash'] . $last_modified);
} else {
	// Empty content
	die;
}

// ETag: Entity Tag used for strong cache validation.
if ( ! isset($_GET['display']) || isset($_GET['x']) || isset($_GET['y']) || isset($_GET['scale']) || isset($_GET['max']) || isset($_GET['format']) ) {
  // if image will be modified, emit a different ETag for modifications.
	$str = isset($_GET['x']) ? $_GET['x'] . 'x' : '';
	$str .= isset($_GET['y']) ? $_GET['y'] . 'y' : '';
	$str .= isset($_GET['scale']) ? $_GET['scale'] . 's' : '';
	$str .= isset($_GET['max']) ? $_GET['max'] . 'm' : '';
	$str .= isset($_GET['format']) ? $_GET['format'] . 'f' : '';
	$etag = '"' . $md5 . '-' . crc32($md5) . '-' . crc32($str) . '"';
} else {
  $etag = '"' . $md5 . '-' . crc32($md5) . '"';
}
header('ETag: '.$etag);

$use_client_cache = false;
if ( isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && $last_modified == strtotime(current($a = explode(';', $_SERVER['HTTP_IF_MODIFIED_SINCE']))) ) {
	$use_client_cache = true;
} elseif ( isset($_SERVER['HTTP_IF_NONE_MATCH']) ) {
	$tmp = array_map('trim', explode(',', $_SERVER['HTTP_IF_NONE_MATCH']));
	foreach ( $tmp as $v ) {
		if ( $v == '*' || $v == $etag ) {
			$use_client_cache = true;
			break;
		}
	}
	unset($tmp);
}

header("Pragma: ");
header('Expires: ');
header('Cache-Control: '.( !empty($user) ? 'private' : 'public' ).',must-revalidate,post-check=0,pre-check=0');

if ( $use_client_cache) {
	header('Status: 304 Not Modified', true, 304);
	exit;
} else {
	if ( !empty($last_modified) ) header('Last-Modified: '.gmdate('D, d M Y H:i:s', $last_modified). ' GMT');
}

// Handle images display, files thumbnails and icons
if ( isset($_GET['preview']) || isset($_GET['thumbnail']) || isset($_GET['display']) || isset($_GET['icon']) ) {
	$use_cache = false;

	// Cache only thumbnails to avoid DOS attacks
	$cacheName = '';
	$cacheType = '';
	if ( ( isset($_GET['thumbnail']) || isset($_GET['preview']) ) && ! isset($_GET['display']) && ! isset($_GET['icon']) && ! isset($_GET['scale']) && ! isset($_GET['x']) && ! isset($_GET['y']) && ! isset($_GET['format']) && ! isset($_GET['max']) ) {
		$cachelib = TikiLib::lib('cache');
		$cacheName = $md5;
		$cacheType = ( isset($_GET['thumbnail']) ? 'thumbnail_' : 'preview_' ) . ((int)$_REQUEST['fileId']).'_';
		$use_cache = true;
	}

	$build_content = true;
	$content_temp = $cachelib->getCached($cacheName, $cacheType);
	if ( $use_cache && $content_temp ) {
		if ($content_temp !== serialize(false) and $content_temp != "") {
			$build_content = false;
			$content = $content_temp;
		}
		$content_changed = true;
	}
	unset($content_temp);

	if ($build_content) {

		// Modify the original image if needed
		if ( ! isset($_GET['display']) || isset($_GET['x']) || isset($_GET['y']) || isset($_GET['scale']) || isset($_GET['max']) || isset($_GET['format']) || isset($_GET['thumbnail']) ) {
	
			require_once('lib/images/images.php');
			if (!class_exists('Image')) die();
	
			$content_changed = true;
			$format = substr($info['filename'], strrpos($info['filename'], '.') + 1);
	
			// Fallback to an icon if the format is not supported
			$tmp = new Image('img/icons/pixel_trans.gif', true, 'gif');	// needed to call non-static Image functions non-statically
			if ( ! $tmp->is_supported($format) ) {
				// Is the filename correct? Maybe it doesn't have an extenstion?
				// Try to determine the format from the filetype too
				$format = substr($info['filetype'], strrpos($info['filetype'], '/') + 1);
				if ( ! $tmp->is_supported($format) ) {
					$_GET['icon'] = 'y';
					$_GET['max'] = 32;
				}
			}

			do {
				$tryIconFallback = false;

				if ( isset($_GET['icon']) ) {
					unset($info['path']);
					$content = null; // Explicitely free memory before generating icon
	
					if ( isset($_GET['max']) ) {
						$icon_x = $_GET['max'];
						$icon_y = $_GET['max'];
					} else {
						$icon_x = isset($_GET['x']) ? $_GET['x'] : 0;
						$icon_y = isset($_GET['y']) ? $_GET['y'] : 0;	
					}
		
					$ext = pathinfo($info['filename']);	// TODO replace with mimelib functions
					$format = isset($ext['extension']) ? $ext['extension'] : $format;
					$content = $tmp->icon($format, $icon_x, $icon_y);
					$format = $tmp->get_icon_default_format();
					$info['filetype'] = 'image/'.$format;
					$info['lastModif'] = 0;
				}
		
				if ( ! isset($_GET['icon']) || ( isset($_GET['format']) && $_GET['format'] != $format ) ) {
					if ( ! empty($info['path']) ) {
						$image = new Image($prefs['fgal_use_dir'].$info['path'], true);
					} else {
						$image = new Image($content, false, $format);
						$content = null; // Explicitely free memory before getting cache
					}
					if ( $image->is_empty() ) die;
		
					$resize = false;
					// We resize if needed
					if ( isset($_GET['x']) || isset($_GET['y']) ) {
						$image->resize(isset($_GET['x']) ? (int) $_GET['x'] : 0, isset($_GET['y']) ? (int) $_GET['y'] : 0);
						$resize = true;
					} elseif ( isset($_GET['scale']) ) {
				 		// We scale if needed
						$image->scale($_GET['scale']+0);
						$resize = true;
					} elseif ( isset($_GET['max']) ) {
					// We reduce size if length or width is greater that $_GET['max'] if needed
						$image->resizemax($_GET['max']+0);
						$resize = true;
					} elseif ( isset($_GET['thumbnail']) ) {
					// We resize to a thumbnail size if needed
						if (is_numeric($_GET['thumbnail'])) {
							if (empty($info_thumb)) {
								$info_thumb = $filegallib->get_file($_GET['thumbnail']);
							}
							if ( ! empty($info_thumb['path']) ) {
								$image = new Image($prefs['fgal_use_dir'].$info_thumb['path'], true);
							} else {
								$image = new Image($info_thumb['data']);
								$content = null; // Explicitely free memory before getting cache
							}
							if ( $image->is_empty() ) die;
						}
						$image->resizethumb();
					} elseif ( isset($_GET['preview']) ) {
					// We resize to a preview size if needed
						$image->resizemax('800');
						$resize = true;
					}
		
					// We change the image format if needed
					if ( isset($_GET['format']) && $image->is_supported($_GET['format']) ) {
						$image->convert($_GET['format']);
					} elseif ( isset($_GET['thumbnail']) && $image->format != 'svg') {
						// Or, if no format is explicitely specified and a thumbnail has to be created, we convert the image to the $thumbnail_format
						if ($image->format == 'png') {
							$thumbnail_format = 'png';	// preserves transparency
						}
						$image->convert($thumbnail_format);
					}
		
					$content = $image->display();
	
					// If the new image creating has failed, fallback to an icon
					if ( ! isset($_GET['icon']) && ( $content === null || $content === false ) ) {
						$tryIconFallback = true;
						$_GET['icon'] = 'y';
						$_GET['max'] = 32;
					} else {
						$info['filetype'] = $image->get_mimetype();
					}
				}
			} while ( $tryIconFallback );
		}
		if (strpos($info['filetype'], 'image/svg') !== false) {
			$info['filetype'] = 'image/svg+xml';
			$content = '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">' . "\n" . $content;
		}
		if ( $use_cache && !empty($content) ) {
			// Remove all existing thumbnails for this file, to avoid taking too much disk space
			// (only one thumbnail size is handled at the same time)
			$cachelib->empty_type_cache($cacheType);

			// Cache Thumbnail
			$cachelib->cacheItem($cacheName, $content, $cacheType);
		}
	}
}

$mimelib = TikiLib::lib('mime');
if ( empty($info['filetype']) || $info['filetype'] == 'application/x-octetstream'
			|| $info['filetype'] == 'application/octet-stream' || $info['filetype'] == 'unknown') {

	$info['filetype'] = $mimelib->from_path($info['filename'], $filepath);

} else if (isset($_GET['thumbnail']) && (strpos($info['filetype'], 'image') === false || ($content_changed && strpos($info['filetype'], 'image/svg') === false))) {	// use thumb format
	$info['filetype'] = $mimelib->from_content($info['filename'], $content);
}
header('Content-type: '.$info['filetype']);

// IE6 can not download file with / in the name (the / can be there from a previous bug)
$file = basename($info['filename']);

// If the content has not changed, ask the browser to download it (instead of displaying it)
if ( ! $content_changed and !isset($_GET['display']) ) {
	header("Content-Disposition: attachment; filename=\"$file\"");
} else {
	header("Content-Disposition: filename=\"$file\"");
}

if ( !empty($filepath) and !$content_changed ) {
	header('Content-Length: '.filesize($filepath));
	readfile($filepath);
} else {
	if ( function_exists('mb_strlen') ) {
		header('Content-Length: '.mb_strlen($content, '8bit'));
	} else {
		header('Content-Length: '.strlen($content));
	}
	echo "$content";
}
