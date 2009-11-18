<?php
  /* $Id:$
   */
function wikiplugin_colorbox_info() {
	return array(
		'name' => tra('colorbox'),
		'documentation' => 'PluginClorBox',
		'description' => tra("Display all the images of a file gallery in a colorbox popup"),
		'prefs' => array( 'feature_file_galleries', 'feature_shadowbox', 'wikiplugin_colorbox' ),
		'params' => array(
			'fgalId' => array(
				'required' => false,
				'name' => tra('File gallery ID'),
				'description' => tra('File gallery ID'),
				'filter' => 'digits'
			),
			'galId' => array(
				'required' => false,
				'name' => tra('Image gallery ID'),
				'description' => tra('Image gallery ID'),
				'filter' => 'digits'
			),
		),
	);
}
function wikiplugin_colorbox($data, $params) {
	global $tikilib, $smarty;
	static $iColorbox;
	if (!empty($params['fgalId'])) {
		if (empty($params['sort_mode'])) $params['sort_mode'] = 'created_desc';
		$files = $tikilib->get_files(0, -1, $params['sort_mode'], '', $params['fgalId'], false, false, false, true, false, false, false);
		$smarty->assign('colorboxUrl', 'tiki-download_file.php?fileId=');
		$smarty->assign('colorboxColumn', 'id');
		$smarty->assign('colorboxThumb', 'thumbnail');
	} elseif (!empty($params['galId'])) {
		global $imagegallib; include_once ('lib/imagegals/imagegallib.php');
		if (empty($params['sort_mode'])) $params['sort_mode'] = 'created_desc';
		$files = $imagegallib->get_images(0, -1, $params['sort_mode'], '', $params['galId']);
		$smarty->assign('colorboxUrl', 'show_image.php?id=');
		$smarty->assign('colorboxColumn', 'imageId');
		$smarty->assign('colorboxThumb', 'thumb');
	} else {
		return tra('Incorrect param');
	}
	$smarty->assign('iColorbox', $iColorbox++);
	$smarty->assign_by_ref('colorboxFiles', $files);
	return $smarty->fetch('wiki-plugins/wikiplugin_colobox.tpl');
}
/* 
{img src=tiki-download_file.php?fileId=1&amp;thumbnail link=tiki-download_file.php?fileId=1&amp;display rel="shadowbox[gallery];type=img"}
<a href="tiki-download_file.php?fileId=4&amp;display" rel="shadowbox[gallery];type=img"></a>
<a href="tiki-download_file.php?fileId=7&amp;display" rel="shadowbox[gallery];type=img"></a>
*/