<?php
/* $Id: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_thumb.php,v 1.9.2.1 2007-12-09 16:04:07 frank_p Exp $ */
function wikiplugin_thumb_help() {
	return tra("Displays the thumbnail for an image").":<br />~np~{THUMB(image=>url,id=url,max=>,float=>,url=>,original=y, sticky=n)}".tra("description")."{THUMB}~/np~";
}

function wikiplugin_thumb_info() {
	return array(
		'name' => tra('Thumbnail'),
		'documentation' => 'PluginThumb',
		'description' => tra('Displays the thumbnail for an image'),
		'prefs' => array( 'wikiplugin_thumb' ),
		'body' => tra('description'),
		'params' => array(
			'file' => array(
				'required' => false,
				'name' => tra('File ID'),
				'description' => tra('File ID from the file gallery.'),
			),
			'id' => array(
				'required' => false,
				'name' => tra('Image ID'),
				'description' => tra('Image ID from the image gallery.'),
			),
			'image' => array(
				'required' => false,
				'name' => tra('Image'),
				'description' => tra('URL to the image.'),
			),
			'max' => array(
				'required' => false,
				'name' => tra('Maximum Size'),
				'description' => tra('Maximum width or height for the image.'),
			),
			'float' => array(
				'required' => false,
				'name' => tra('Floating'),
				'description' => 'left|right|none',
			),
			'url' => array(
				'required' => false,
				'name' => tra('URL'),
				'description' => tra('Link target of the image.'),
			),
			'original' => array(
				'required' => false,
				'name' => tra('Original'),
				'description' => 'y|n',
			),
			'sticky' => array(
				'required' => false,
				'name' => tra('Sticky'),
				'description' => 'y|n',
			),
		),
	);
}

function wikiplugin_thumb($data, $params) {
	global $smarty, $tikidomain;
	extract ($params,EXTR_SKIP);
	if (!isset($data) or !$data) {
		$data = '&nbsp;';
	}
	if (!isset($max)) {
		$max = 84;
	}
	$style = '';
	if (!isset($float)) {
		$float = "none";
	} elseif ($float == 'right') {
		$style = "margin-left: 2ex;";
	} elseif ($float == 'left') { 
		$style = "margin-right: 2ex;";
	} else {
		$float = "none";
	}
	if (!isset($url)) {
		$url = "javascript:void()";
	}

	if( isset($file) && !empty($file) ) {
		// From file galleries
		global $filegallib; include_once('lib/filegals/filegallib.php');
		$info = $filegallib->get_file($file);

		if( ! $info ) {
			return '^' . tra('File not found.') . '^';
		}

		if( substr($info['filetype'], 0, 5) != 'image' ) {
			return '^' . tra('File is not an image.') . '^';
		}

		require_once('lib/images/images.php');
		if (!class_exists('Image')) return '^' . tra('Server does not support image manipulation.') . '^';

		$imageObj = new Image($info['data']);

		$width = $imageObj->get_width();
		$height = $imageObj->get_height();

		$image = 'tiki-download_file.php?fileId='. urlencode($file) .'&thumbnail&max=' . urlencode($max);
		$imageOver = 'tiki-download_file.php?fileId=' . urlencode($file);
		$type = $info['filetype'];

	} elseif (isset($id) && !empty($id)) {
		// From image galleries
		$image = "show_image.php?id=$id&thumb=1";
		global $imagegallib; include_once('lib/imagegals/imagegallib.php');
		if (isset($original) && $original == 'y') {
			$info = $imagegallib->get_image_info($id, 'o');
			$scalesize = 0;
		} else {
			$info = $imagegallib->get_image_info($id, 's');
			if (empty($info)) {
				$info = $imagegallib->get_image_info($id, 'o');
				$scalesize = 0;
				$original = 'y';
			} else {
				$scalesize = $imagegallib->get_gallery_default_scale($info['galleryId']);			
			}
		}
		$width = $info['xsize'];
		$height = $info['ysize'];
		$imageOver = "show_image.php?id=$id&scalesize=$scalesize";
		$type = $info['type'];
	} elseif (isset($image) && !empty($image)) {
		// From generic image
		if ($tikidomain) {
			$image = preg_replace('~wiki_up/~',"wiki_up/$tikidomain/",$image);
		}
		if (!is_file($image)) {
			return "''image not found'' $image";
		}
		list($width, $height, $type, $attr) = getimagesize($image);
		$imageOver = $image;
	} else {
		return "^" . tra('No image specified.') . "^";
	}

	if ($width > $max or $height > $max) {
		if ($width > $height) {
			$factor = $width / $max;
		} else {
			$factor = $height / $max;
		}
		$twidth = floor($width / $factor);
		$theight = floor($height / $factor);
	} else {
		$twidth = $width;
		$theight = $height;
	}

	$html = '';
	if (!$smarty->get_template_vars('overlib_loaded')) {
		$html = '<div id=\"overDiv\" style=\"position:absolute; visibility:hidden; z-index:1000;\"></div>';
		$html.= '<script type=\"text/javascript\" src=\"lib/overlib.js\"></script>';
		$smarty->assign('overlib_loaded',1);
	}
	$html.= "<a href='$url' style='float:$float;$style' ";
	$html.= " onmouseover=\"return overlib('$data',BACKGROUND,'$imageOver',WIDTH,'$width',HEIGHT,$height";
	if (isset($sticky) && $sticky == 'y') {
		$html .= ',STICKY';
	}
	$html .= ");\" onmouseout='nd();' >";
	$html.= "<img src='$image' width='$twidth' height='$theight' /></a>";
	return $html;
}
?>
