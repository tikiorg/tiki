<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_draw_info() {
	return array(
		'name' => tra('Draw'),
		'documentation' => 'PluginDraw',
		'description' => tra('Display or create an image from TikiDraw that is stored into the File Gallery'),
		'prefs' => array( 'feature_draw' ),
		'icon' => 'pics/icons/shape_square_edit.png',
		'params' => array(
			'id' => array(
				'required' => false,
				'name' => tra('Drawing ID'),
				'description' => tra('Internal ID of the file id'),
				'filter' => 'digits',
				'accepted' => ' ID number',
				'default' => '',
				'since' => '8.0'
			),
			'width' => array(
				'required' => false,
				'name' => tra('Width'),
				'description' => tra('Width in pixels or percentage. Default value is page width. e.g. "200px" or "100%"'),
				'filter' => 'striptags',
				'accepted' => 'Number of pixels followed by \'px\' or percent followed by % (e.g. "200px" or "100%").',
				'default' => 'Image width',
				'since' => '8.0'
			),
			'height' => array(
				'required' => false,
				'name' => tra('Height'),
				'description' => tra('Height in pixels or percentage. Default value is complete drawing height.'),
				'filter' => 'striptags',
				'accepted' => 'Number of pixels followed by \'px\' or percent followed by % (e.g. "200px" or "100%").',
				'default' => 'Image height',
				'since' => '8.0'
			),
		),
	);
}

function wikiplugin_draw($data, $params) {
	global $dbTiki, $tiki_p_edit, $tiki_p_admin, $prefs, $user, $page, $tikilib, $smarty;
	extract ($params,EXTR_SKIP);
	
	static $index = 0;
	++$index;
	
	if (!isset($id) && !isset($url)) {
		if( $tiki_p_edit != 'y' ) {
			return ("<b>missing id parameter for plugin</b><br />");
		} else {
			$intro = tra('Incomplete call to plugin: No target svg image.');
			$label = tra('Draw New SVG Image');
			$page = htmlentities($page);
			$content = htmlentities($data);
			$formId = "form$index";
			return <<<EOF
			~np~
			<form method="post" action="tiki-edit_draw.php?galleryId=1">
				<p>$intro</p>
				<p>
					<input type="submit" name="label" value="$label"/>
					<input type="hidden" name="index" value="$index"/>
					<input type="hidden" name="page" value="$page"/>
				</p>
			</form>
			~/np~
EOF;
		}
	}
	
	return '~np~' . "<img src='tiki-download_file.php?fileId=$id' />" . '~/np~';
}
