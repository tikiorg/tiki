<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$force_no_compression = true;
$skip = false;

if ( isset($_GET['fileId']) && isset($_GET['thumbnail']) && isset($_COOKIE[ session_name() ]) && count($_GET) == 2 ) {

	$tikiroot = dirname($_SERVER['PHP_SELF']);
	$session_params = session_get_cookie_params();
	session_set_cookie_params($session_params['lifetime'],$tikiroot);
	unset($session_params);
	session_start();

	if ( isset($_SESSION['allowed'][$_GET['fileId']]) ) {
		require_once 'tiki-filter-base.php';
		include('db/tiki-db.php');
		$db = TikiDb::get();

		$query = "select * from `tiki_files` where `fileId`=?";
		$result = $db->query($query, array((int)$_GET['fileId']));
		if ( $result ) {
			$info = $result->fetchRow();

			if ( isset($_SESSION['s_prefs']) ) {
				$prefs = $_SESSION['s_prefs'];
			} else {
				$query = "select `value` from `tiki_preferences` where `name` = 'fgal_use_dir';";
				$result = $db->query($query);
				if ( $result ) {
					$tmp = $result->fetchRow();
					$prefs['fgal_use_dir'] = $tmp['value'];
				}
			}
			if ( !isset($prefs['fgal_use_dir']) ) {
				$prefs['fgal_use_dir'] = '';
			}

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
	include_once('lib/filegals/filegallib.php');
	$access->check_feature('feature_file_galleries');
}

if ( ! ini_get('safe_mode') ) {
	@set_time_limit(0);
}

/*
	 Borrowed from http://php.net/manual/en/function.readfile.php#54295 to come
	 over the 2MB readfile() limitation
 */
function readfile_chunked($filename,$retbytes=true) {
	$chunksize = 1*(1024*1024); // how many bytes per chunk
	$buffer = '';
	$cnt =0;
	$handle = fopen($filename, 'rb');
	if ($handle === false) {
		return false;
	}
	while (!feof($handle)) {
		$buffer = fread($handle, $chunksize);
		echo $buffer;
		@ob_flush();
		flush();
		if ($retbytes) {
			$cnt += strlen($buffer);
		}
	}
	$status = fclose($handle);
	if ($retbytes && $status) {
		return $cnt; // return num. bytes delivered like readfile() does.
	}
	return $status;
}
$zip = false;
$error = '';

if (!$skip) {
	if ( isset($_REQUEST['fileId']) && !is_array($_REQUEST['fileId'])) {
		$info = $tikilib->get_file($_REQUEST['fileId']);
	} elseif ( isset($_REQUEST['galleryId']) && isset($_REQUEST['name']) ) {
		$info = $tikilib->get_file_by_name($_REQUEST['galleryId'], $_REQUEST['name']);
	} elseif ( isset($_REQUEST['fileId']) && is_array($_REQUEST['fileId'])) {
		$info = $filegallib->zip($_REQUEST['fileId'], $error);
		$zip = true;
	} elseif ( !empty($_REQUEST['randomGalleryId'])) {
		$info =  $tikilib->get_file(0, $_REQUEST['randomGalleryId']);
	} else {
		$smarty->assign('msg', tra('Incorrect param'));
		$smarty->display('error.tpl');
		die;
	}
	if ( ! is_array($info) ) {
		$smarty->assign('msg', tra('Incorrect param').' '.tra($error));
		$smarty->display('error.tpl');
		die;
	}
	if ( !$zip && $tiki_p_admin_file_galleries != 'y' && !$userlib->user_has_perm_on_object($user, $info['galleryId'], 'file gallery', 'tiki_p_download_files') && !($info['backlinkPerms'] == 'y' && !$filegallib->hasOnlyPrivateBacklinks($info['fileId']))) {
		$smarty->assign('errortype', 401);
		$smarty->assign('msg', tra('Permission denied'));
		$smarty->display('error.tpl');
		die;
	}
}

// Add hits ( if download or display only ) + lock if set
if ( ! isset($_GET['thumbnail']) && ! isset($_GET['icon']) ) {

	require_once('lib/stats/statslib.php');
	if( ! $tikilib->add_file_hit($info['fileId']) )	{
		$smarty->assign('msg', tra('You cannot download this file right now. Your score is low or file limit was reached.'));
		$smarty->display('error.tpl');
		die;
	}
	$statslib->stats_hit($info['filename'], 'file', $info['fileId']);

	if ( $prefs['feature_actionlog'] == 'y' ) {
		global $logslib; require_once('lib/logs/logslib.php');
		$logslib->add_action('Downloaded', $info['galleryId'], 'file gallery', 'fileId='.$info['fileId']);
	}

	if ( ! empty($_REQUEST['lock']) ) {
		if (!empty($info['lockedby']) && $info['lockedby'] != $user) {
			$smarty->assign('msg', tra(sprintf('The file is locked by %s', $info['lockedby'])));
			$smarty->assign('close_window', 'y');
			$smarty->display('error.tpl');
			die;
		}
		$filegallib->lock_file($info['fileId'], $user);
	}
}

session_write_close(); // close the session in case of large downloads to enable further browsing
error_reporting(E_ALL);
if ( ob_get_level() ) while (@ob_end_clean()); // Be sure output buffering is turned off

$content_changed = false;
$content = &$info['data'];

$md5 = '';
if ( ! empty($info['path']) )  {
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
			: $info['hash'];
	} else {
		// File missing or not readable
		die;
	}
} elseif ( ! empty($content) ) {
	$last_modified = $info['lastModif'];
	$md5 = empty($info['hash']) ? md5($content) : $info['hash'];
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
	$etag = '"' . $md5 . '-' . crc32($md5) . '-' . crc32( $str ) . '"';
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

if ( $use_client_cache ) {
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
		global $cachelib; include_once('lib/cache/cachelib.php');
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
		if ( ! isset($_GET['display']) || isset($_GET['x']) || isset($_GET['y']) || isset($_GET['scale']) || isset($_GET['max']) || isset($_GET['format']) ) {
	
			require_once('lib/images/images.php');
			if (!class_exists('Image')) die();
	
			$content_changed = true;
			$format = substr($info['filename'], strrpos($info['filename'], '.') + 1);
	
			// Fallback to an icon if the format is not supported
			if ( ! Image::is_supported($format) ) {
				$_GET['icon'] = 'y';
				$_GET['max'] = 32;
			}
	
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
	
				$content = Image::icon($format, $icon_x, $icon_y);
				$format = Image::get_icon_default_format();
				$info['filetype'] = 'image/'.$format;
				$info['lastModif'] = 0;
			}
	
			if ( ! isset($_GET['icon']) || ( isset($_GET['format']) && $_GET['format'] != $format ) ) {
  				if ( ! empty($info['path']) ) {
					$image = new Image($prefs['fgal_use_dir'].$info['path'], true);
				} else {
					$image = new Image($content);
					$content = null; // Explicitely free memory before getting cache
				}
				if ( $image->is_empty() ) die;
	
				$resize = false;
				// We resize if needed
				if ( isset($_GET['x']) || isset($_GET['y']) ) {
					$image->resize($_GET['x']+0, $_GET['y']+0);
					$resize = true;
				}
				// We scale if needed
				elseif ( isset($_GET['scale']) ) {
					$image->scale($_GET['scale']+0);
					$resize = true;
				}
				// We reduce size if length or width is greater that $_GET['max'] if needed
				elseif ( isset($_GET['max']) ) {
					$image->resizemax($_GET['max']+0);
					$resize = true;
				}
				// We resize to a thumbnail size if needed
				elseif ( isset($_GET['thumbnail']) ) {
					$image->resizethumb();
				}
				// We resize to a preview size if needed
				elseif ( isset($_GET['preview']) ) {
					$image->resizemax('800');
					$resize = true;
				}
	
				// We change the image format if needed
				if ( isset($_GET['format']) && Image::is_supported($_GET['format']) ) {
					$image->convert($_GET['format']);
				}
				// By default, we change the image format to the usual most common format (jpeg) for thumbnails
				elseif ( isset($_GET['thumbnail']) ) {
					$image->convert('jpeg');
				}
	
				$content =& $image->display();
				$info['filetype'] = $image->get_mimetype();
			}
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

if ( empty($info['filetype']) || $info['filetype'] == 'application/x-octetstream' || $info['filetype'] == 'application/octet-stream' ) {
	include_once('lib/mime/mimelib.php');
	$info['filetype'] = tiki_get_mime($info['filename'], 'application/octet-stream');
}
header('Content-type: '.$info['filetype']);

// IE6 can not download file with / in the name (the / can be there from a previous bug)
$file = basename($info['filename']);

// If the content has not changed, ask the browser to download it (instead of displaying it)
if ( ! $content_changed and !isset($_GET['display']) ) {
	header("Content-Disposition: attachment; filename=\"$file\"");
}

if ( $info['path'] and !$content_changed ) {
	header('Content-Length: '.filesize($filepath));
	readfile_chunked($filepath);
} else {
	if ( function_exists('mb_strlen') ) {
		header('Content-Length: '.mb_strlen($content, '8bit'));
	} else {
		header('Content-Length: '.strlen($content));
	}
	echo "$content";
}
