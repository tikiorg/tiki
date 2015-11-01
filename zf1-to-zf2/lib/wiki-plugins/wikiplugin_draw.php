<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_draw_info()
{
	return array(
		'name' => tra('Draw'),
		'documentation' => 'PluginDraw',
		'description' => tra('Embed a drawing in a page'),
		'prefs' => array( 'feature_draw' , 'wikiplugin_draw'),
		'iconname' => 'edit',
		'tags' => array( 'basic' ),
		'introduced' => 7.1,
		'params' => array(
			'id' => array(
				'required' => false,
				'name' => tra('Drawing ID'),
				'description' => tra('Internal ID of the file id'),
				'filter' => 'digits',
				'accepted' => ' ID number',
				'default' => '',
				'since' => '7.1',
				'profile_reference' => 'file',
			),
			'width' => array(
				'required' => false,
				'name' => tra('Width'),
				'description' => tr('Width in pixels or percentage. Default value is page width. e.g. %0 or %1',
					'<code>200px</code>', '<code>100%</code>'),
				'filter' => 'text',
				'accepted' => 'Number of pixels followed by \'px\' or percent followed by % (e.g. "200px" or "100%").',
				'default' => 'Image width',
				'since' => '7.1'
			),
			'height' => array(
				'required' => false,
				'name' => tra('Height'),
				'description' => tra('Height in pixels or percentage. Default value is complete drawing height.'),
				'filter' => 'text',
				'accepted' => 'Number of pixels followed by \'px\' or percent followed by % (e.g. "200px" or "100%").',
				'default' => 'Image height',
				'since' => '7.1'
			),
			'archive' => array(
				'required' => false,
				'name' => tra('Force Display Archive'),
				'description' => tr('The latest revision of file is automatically shown, by setting archive to Yes (%0),
				it bypasses this check and shows the archive rather than the latest revision', '<code>y</code>'),
				'filter' => 'alpha',
				'default' => 'n',
				'since' => '8.0',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				)
			),
		),
	);
}

function wikiplugin_draw($data, $params)
{
	global $tiki_p_edit, $tiki_p_admin, $tiki_p_upload_files, $prefs, $user, $page;
	$headerlib = TikiLib::lib('header');
	$tikilib = TikiLib::lib('tiki');
	$smarty = TikiLib::lib('smarty');
	$filegallib = TikiLib::lib('filegal');
	$globalperms = Perms::get();

	extract(array_merge($params, array()), EXTR_SKIP);

	static $drawIndex = 0;
	++$drawIndex;

	if (!isset($id)) {
		//check permissions
		if ($tiki_p_upload_files != 'y') {
			return;
		}

		$label = tra('Draw New SVG Image');
		$page = htmlentities($page);
		$content = htmlentities($data);
		$formId = "form$drawIndex";
		$gals=$filegallib->list_file_galleries(0, -1, 'name_desc', $user);

		$galHtml = "";
		if (!function_exists('wp_draw_cmp')) {
			function wp_draw_cmp($a, $b) {
				return strcmp(strtolower($a["name"]), strtolower($b["name"]));
			}
		}
		usort($gals['data'], 'wp_draw_cmp');
		foreach ($gals['data'] as $gal) {
			if ($gal['name'] != "Wiki Attachments" && $gal['name'] != "Users File Galleries")
				$galHtml .= "<option value='".$gal['id']."'>".$gal['name']."</option>";
		}

		$in = tr(" in ");

		$headerlib->add_jq_onready(
<<<JQ
			$('#newDraw$drawIndex').submit(function() {
				var form = $(this);
				var fields = form.serializeArray();
				$.wikiTrackingDraw = {
					fileId: 0,
					page: '$page',
					index: '$drawIndex',
					label: '$label',
					type: 'draw',
					content: '',
					params: {
						width: '',
						height: '',
						id: 0 //this will be updated
					}
				};
				$.each(fields, function(i, field){
					form.data(field.name.toLowerCase(), field.value);
				});

				return form.ajaxEditDraw();
			});
JQ
		);
		return <<<EOF
		~np~
		<form id="newDraw$drawIndex" method="get" action="tiki-edit_draw.php">
			<p>
				<input type="submit" class="btn btn-default btn-sm" name="label" value="$label" class="newSvgButton" />$in
				<select name="galleryId">
					$galHtml
				</select>
				<input type="hidden" name="index" value="$drawIndex"/>
				<input type="hidden" name="page" value="$page"/>
				<input type="hidden" name="archive" value="$archive"/>
			</p>
		</form>
		~/np~
EOF;
	}

	$fileInfo = $filegallib->get_file_info($id);

	//this sets the image to latest in a group of archives
	if (!isset($archive) || $archive != 'y') {
		if (!empty($fileInfo['archiveId']) && $fileInfo['archiveId'] > 0) {
			$id = $fileInfo['archiveId'];
			$fileInfo = $filegallib->get_file_info($id);
		}
	}

	if (!isset($fileInfo['created'])) {
		return tra("File not found.");
	} else {
		$globalperms = Perms::get(array( 'type' => 'file gallery', 'object' => $fileInfo['galleryId'] ));

		if ($globalperms->view_file_gallery != 'y') return "";

		$label = tra('Edit SVG Image');
		$ret = '<div type="image/svg+xml" class="svgImage pluginImg table-responsive' . $fileInfo['fileId'] . '" style="' .
			(isset($height) ? "height: $height;" : "" ).
			(isset($width) ? "width: $width;" : "" )
		. '">' . $fileInfo['data'] . '</div>';

		if ($globalperms->upload_files == 'y') {
			$smarty->loadPlugin('smarty_function_icon');
			$editicon = smarty_function_icon(['name' => 'edit'], $smarty);
			$ret .= "<a href='tiki-edit_draw.php?fileId=$id&page=$page&index=$drawIndex&label=$label" .
				(isset($width) ? "&width=$width" : "") . (isset($height) ? "&height=$height" : "") .
				"' onclick='return $(this).ajaxEditDraw();'  title='Edit: ".$fileInfo['filename'].
				"' data-fileid='".$fileInfo['fileId']."' data-galleryid='".$fileInfo['galleryId']."'>" .
				$editicon . "</a>";
		}


		return '~np~' . $ret . '~/np~';
	}
}
