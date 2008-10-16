<?php
// CVS: $Id: tiki-download_file.php,v 1.33.2.4 2008-03-13 20:12:44 nyloth Exp $
// Initialization

$force_no_compression = true;
require_once('tiki-setup.php');
include_once('lib/filegals/filegallib.php');

if ( $prefs['feature_file_galleries'] != 'y' ) {
	$smarty->assign('msg', tra('This feature is disabled'));
	$smarty->display('error.tpl');
	die;
}
@set_time_limit(0);

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

function get_readfile_chunked($filename) {
   $chunksize = 1*(1024*1024); // how many bytes per chunk
   $buffer = '';
   $cnt =0;
   $handle = fopen($filename, 'rb');
   if ($handle === false) {
       return false;
   }
   while (!feof($handle)) {
       $buffer = fread($handle, $chunksize);
   }
   fclose($handle);
   return $buffer;
}

if ( isset($_REQUEST['fileId']) && !is_array($_REQUEST['fileId'])) {
	$info = $tikilib->get_file($_REQUEST['fileId']);
} elseif ( isset($_REQUEST['galleryId']) && isset($_REQUEST['name']) ) {
	$info = $tikilib->get_file_by_name($_REQUEST['galleryId'], $_REQUEST['name']);
} elseif ( isset($_REQUEST['fileId']) && is_array($_REQUEST['fileId'])) {
	$info = $filegallib->zip($_REQUEST['fileId'], $error);
	$zip = true;
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

if ( !$zip && $tiki_p_admin_file_galleries != 'y' && !$userlib->user_has_perm_on_object($user, $info['galleryId'], 'file gallery', 'tiki_p_download_files')) {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra('You can not download files'));
	$smarty->display('error.tpl');
	die;
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
		require_once('lib/logs/logslib.php');
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

// close the session in case of large downloads to enable further browsing
session_write_close();
error_reporting(E_ALL);

$content_changed = false;
$content = &$info['data'];

// Handle images display, files thumbnails and icons
if ( isset($_GET['thumbnail']) || isset($_GET['display']) || isset($_GET['icon']) ) {
	
	if ( $info['path'] && (!$content || sizeof($content) == 0)) {
		$content = &get_readfile_chunked($prefs['fgal_use_dir'].$info['path']);
	}

	// Modify the original image if needed
	if ( ! isset($_GET['display']) || isset($_GET['x']) || isset($_GET['y']) || isset($_GET['scale']) || isset($_GET['max']) || isset($_GET['format']) ) {

		$content_changed = true;
		if ( $info['path'] ) {
			$content=file_get_contents(($prefs['fgal_use_dir'].$info['path']));
		}

		require_once('lib/images/images.php');
		if (!class_exists('Image')) die();

		$format = substr($info['filename'], strrpos($info['filename'], '.') + 1);

		// Fallback to an icon if the format is not supported
		if ( ! Image::is_supported($format) ) {
			$_GET['icon'] = 'y';
			$_GET['max'] = 32;
		}

		if ( isset($_GET['icon']) ) {
			unset($content);
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
		}

		if ( ! isset($_GET['icon']) || ( isset($_GET['format']) && $_GET['format'] != $format ) ) {
			$image = new Image($content);

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
}


// IE6 can not download file with / in the name (the / can be there from a previous bug)
$file = preg_replace('/.*([^\/]*)$/U', '$1', $info['filename']);

// Added by Jenolan  31/8/2003 /////////////////////////////////////////////
// File galleries should always be attachments (files) not inline (textual)

header('Content-type: '.$info['filetype']);
if (!$content_change and !isset($_GET['display'])) {
	header("Content-Disposition: attachment; filename=\"$file\"");
}

header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');

if ( $info['path'] and !$content_changed ) {
	header('Content-Length: '.filesize($prefs['fgal_use_dir'].$info['path']) );
	readfile_chunked($prefs['fgal_use_dir'].$info['path']);
} else {
	if ( function_exists('mb_strlen') ) {
		header('Content-Length: '.mb_strlen($content, '8bit'));
	} else {
		header('Content-Length: '.strlen($content));
	}
	echo "$content";
}
?>
