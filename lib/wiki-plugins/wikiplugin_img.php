<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_img_info()
{
	global $prefs;
	$info = array(
		'name' => tra('Image'),
		'documentation' => 'PluginImg',
		'description' => tra('Display one or more custom formatted images'),
		'prefs' => array( 'wikiplugin_img'),
		'iconname' => 'image',
		'tags' => array( 'basic' ),
		'introduced' => 3,
		'params' => array(
			'type' => array(
				'required' => true,
				'name' => tra('Image Source'),
				'description' => tra('Choose where to get your image from'),
				'since' => '11.0',
				'doctype' => 'id',
				'default' => '',
				'filter' => 'word',
				'options' => array(
					array('text' => tra('Select an option'), 'value' => ''),
					array('text' => tra('An image in the File Galleries'), 'value' => 'fileId'),
					array('text' => tra('An image attached to a wiki page'), 'value' => 'attId'),
					array('text' => tra('An image anywhere on the internet'), 'value' => 'src'),
					array('text' => tra('All the images in a File Gallery'), 'value' => 'fgalId'),
					array('text' => tra('One random image from a File Gallery'), 'value' => 'randomGalleryId'),
				),
			),
			'fileId' => array(
				'required' => true,
				'name' => tra('File ID'),
				'type' => 'image',
				'area' => 'fgal_picker_id',
				'description' => tr('Numeric ID of an image in a File Gallery (or list separated by commas or %0).',
					'<code>|</code>'),
				'since' => '4.0',
				'doctype' => 'id',
				'filter' => 'text',
				'default' => '',
				'accepted' => tra('Valid file IDs separated by commas or |'),
				'parent' => array('name' => 'type', 'value' => 'fileId'),
				'profile_reference' => 'file',
			),
			'id' => array(
				'required' => true,
				'name' => tra('Image ID'),
				'description' => tr('Numeric ID of an image in an Image Gallery (or list separated by commas or %0).',
					'<code>|</code>'),
				'since' => '4.0',
				'doctype' => 'id',
				'filter' => 'text',
				'advanced' => $prefs['feature_galleries'] !== 'y',
				'accepted' => tra('Valid image IDs separated by commas or |'),
				'default' => '',
				'parent' => array('name' => 'type', 'value' => 'id'),
			),
			'src' => array(
				'required' => true,
				'name' => tra('Image Source'),
				'description' => tra('Full URL to the image to display.'),
				'since' => '3.0',
				'doctype' => 'id',
				'filter' => 'url',
				'default' => '',
				'parent' => array('name' => 'type', 'value' => 'src'),
			),
			'randomGalleryId' => array(
				'required' => true,
				'name' => tra('Gallery ID'),
				'description' => tra('Numeric ID of a file gallery. Displays a random image from that gallery.'),
				'since' => '5.0',
				'doctype' => 'id',
				'filter' => 'digits',
				'advanced' => true,
				'default' => '',
				'parent' => array('name' => 'type', 'value' => 'randomGalleryId'),
			),
			'fgalId' => array(
				'required' => true,
				'name' => tra('File Gallery ID'),
				'description' => tra('Numeric ID of a file gallery. Displays all images from that gallery.'),
				'since' => '8.0',
				'doctype' => 'id',
				'filter' => 'digits',
				'advanced' => true,
				'default' => '',
				'parent' => array('name' => 'type', 'value' => 'fgalId'),
				'profile_reference' => 'file_gallery',
			),
			'attId' => array(
				'required' => true,
				'name' => tra('Attachment ID'),
				'description' => tr('Numeric ID of an image attached to a wiki page (or list separated by commas or %0).',
					'<code>|</code>'),
				'since' => '4.0',
				'doctype' => 'id',
				'filter' => 'text',
				'accepted' => tra('Valid attachment IDs separated by commas or |'),
				'default' => '',
				'parent' => array('name' => 'type', 'value' => 'attId'),
			),
			'thumb' => array(
				'required' => false,
				'name' => tra('Thumbnail'),
				'description' => tr('Makes the image a thumbnail with various options.'),
				'since' => '4.0',
				'doctype' => 'link',
				'filter' => 'alpha',
				'default' => '',
				'options' => array(
					array('text' => tra('None'), 'value' => ''),
					array('text' => tra('Simple - links to full size image on a new page'), 'value' => 'y'),
					array('text' => tra('"Lightbox" - enlarges in an overlay box when clicked'), 'value' => 'box'),
					array('text' => tra('Mouseover - enlarges in a popup when moused over'), 'value' => 'mouseover'),
					array('text' => tra('Mouseover (sticky)'), 'value' => 'mousesticky'),
					array('text' => tra('Popup - enlarges in a separate window'), 'value' => 'popup'),
					array('text' => tra('Download'), 'value' => 'download'),
					array('text' => tra('Zoombox - enlarges in a popup with a zoom option'), 'value' => 'zoombox'),
				),
			),
			'link' => array(
				'required' => false,
				'name' => tra('Link'),
				'description' => tr('Causes the image to be a link to this address. Overrides %0thumb%1 unless %0thumb%1 is
					set to %0mouseover%1 or %0mousesticky%1', '<code>', '</code>'),
				'since' => '3.0',
				'doctype' => 'link',
				'filter' => 'url',
				'default' => '',
			),
			'height' => array(
				'required' => false,
				'name' => tra('Image Height'),
				'description' => tr('Height in pixels or percent. Syntax: %0100%1 or %0100px%1 means 100 pixels;
					%050%%1 means 50 percent.', '<code>', '</code>'),
				'since' => '3.0',
				'doctype' => 'size',
				'filter' => 'text',
				'default' => '',
			),
			'width' => array(
				'required' => false,
				'name' => tra('Image Width'),
				'description' => tr('Width in pixels or percent. Syntax: %0100%1 or %0100px%1 means 100 pixels;
					%050%%1 means 50 percent.', '<code>', '</code>'),
				'since' => '3.0',
				'doctype' => 'size',
				'filter' => 'text',
				'default' => '',
			),
			'max' => array(
				'required' => false,
				'name' => tra('Maximum Size'),
				'description' => tra('Maximum height or width in pixels (largest dimension is scaled). Overrides height
					and width settings.'),
				'since' => '4.0',
				'doctype' => 'size',
				'filter' => 'digits',
				'default' => '',
			),
			'desc' => array(
				'required' => false,
				'name' => tra('Caption'),
				'since' => '3.0',
				'doctype' => 'text',
				'filter' => 'text',
				'description' => tr('Image caption. Use %0name%1 or %0desc%1 or %0namedesc%1 for Tiki name and
					description properties, %0idesc%1 or %0ititle%1 for metadata from the image itself, otherwise
					enter your own description.', '<code>', '</code>'),
				'default' => '',
			),
			'alt' => array(
				'required' => false,
				'name' => tra('Alternate Text'),
				'filter' => 'text',
				'description' => tra('Alternate text that displays when image does not load. Set to "Image" by default.'),
				'since' => '3.0',
				'doctype' => 'text',
				'default' => 'Image',
			),
			'responsive' => array(
				'required' => false,
				'name' => tra('Responsive Image'),
				'filter' => 'alpha',
				'description' => tr('Determines whether the image will resize itself to the screen width using the %0img-responsive%1 class.', '<code>', '</code>'),
				'since' => '14.0',
				'doctype' => 'style',
				'advanced' => false,
				'default' => 'y',
				'options' => array(
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n'),
				),
			),

			///// advanced parameters ///////

			'sort_mode' => array(
				'required' => false,
				'name' => tra('Sort Mode'),
				'description' => tr('Sort by database table field name, ascending or descending. Examples:
					%0 or %1.', '<code>fileId_asc</code>', '<code>name_desc</code>'),
				'filter' => 'word',
				'accepted' => tr('%0 or %1 with actual database field name in place of
					%2.', '<code>fieldname_asc</code>', '<code>fieldname_desc</code>', '<code>fieldname</code>'),
				'default' => 'created_desc',
				'since' => '8.0',
				'doctype' => 'id',
				'advanced' => true,
				'options' => array (
					array('text' => tra(''), 'value' => ''),
					array('text' => tra('Random'), 'value' => 'random'),
					array('text' => tra('Created Ascending'), 'value' => 'created_asc'),
					array('text' => tra('Created Descending'), 'value' => 'created_desc'),
					array('text' => tra('Name Ascending'), 'value' => 'name_asc'),
					array('text' => tra('Name Descending'), 'value' => 'name_desc'),
					array('text' => tra('File Name Ascending'), 'value' => 'filename_asc'),
					array('text' => tra('File Name Descending'), 'value' => 'filename_desc'),
					array('text' => tra('Description Ascending'), 'value' => 'description_asc'),
					array('text' => tra('Description Descending'), 'value' => 'description_desc'),
					array('text' => tra('Comment Ascending'), 'value' => 'comment_asc'),
					array('text' => tra('Comment Descending'), 'value' => 'comment_desc'),
					array('text' => tra('Hits Ascending'), 'value' => 'hits_asc'),
					array('text' => tra('Hits Descending'), 'value' => 'hits_desc'),
					array('text' => tra('Max Hits Ascending'), 'value' => 'maxhits_asc'),
					array('text' => tra('Max Hits Descending'), 'value' => 'maxhits_desc'),
					array('text' => tra('File Size Ascending'), 'value' => 'filesize_asc'),
					array('text' => tra('File Size Descending'), 'value' => 'filesize_desc'),
					array('text' => tra('File Type Ascending'), 'value' => 'filetype_asc'),
					array('text' => tra('File Type Descending'), 'value' => 'filetype_desc'),
					array('text' => tra('User Ascending'), 'value' => 'user_asc'),
					array('text' => tra('User Descending'), 'value' => 'user_desc'),
					array('text' => tra('Author Ascending'), 'value' => 'author_asc'),
					array('text' => tra('Author Descending'), 'value' => 'author_desc'),
					array('text' => tra('Locked By Ascending'), 'value' => 'lockedby_asc'),
					array('text' => tra('Locked By Descending'), 'value' => 'lockedby_desc'),
					array('text' => tra('Last Modified User Ascending'), 'value' => 'lastModifUser_asc'),
					array('text' => tra('Last Modified User Descending'), 'value' => 'lastModifUser_desc'),
					array('text' => tra('Last Modified Date Ascending'), 'value' => 'lastModif_asc'),
					array('text' => tra('Last Modified Date Descending'), 'value' => 'lastModif_desc'),
					array('text' => tra('Last Download Ascending'), 'value' => 'lastDownload_asc'),
					array('text' => tra('Last Download Descending'), 'value' => 'lastDownload_desc'),
					array('text' => tra('Delete After Ascending'), 'value' => 'deleteAfter_asc'),
					array('text' => tra('Delete After Descending'), 'value' => 'deleteAfter_desc'),
					array('text' => tra('Votes Ascending'), 'value' => 'votes_asc'),
					array('text' => tra('Votes Descending'), 'value' => 'votes_desc'),
					array('text' => tra('Points Ascending'), 'value' => 'points_asc'),
					array('text' => tra('Points Descending'), 'value' => 'points_desc'),
					array('text' => tra('Archive ID Ascending'), 'value' => 'archiveId_asc'),
					array('text' => tra('Archive ID Descending'), 'value' => 'archiveId_desc'),
				),
			),
			'button' => array(
				'required' => false,
				'name' => tra('Enlarge Button'),
				'description' => tr('Adds an enlarge button (magnifying glass icon) below the image for use together
					with %0thumb%1. Follows %0thumb%1 settings unless %0thumb%1 is set to %0mouseover%1 or %0mousesticky%1
					(or overridden by %0link%1), otherwise button settings are followed, operating as described above
					for %0thumb%1', '<code>', '</code>'),
				'since' => '4.0',
				'doctype' => 'link',
				'filter' => 'alpha',
				'default' => '',
				'advanced' => true,
				'options' => array(
					array('text' => tra('None'), 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('Popup'), 'value' => 'popup'),
					array('text' => tra('Browse'), 'value' => 'browse'),
					array('text' => tra('Browse Popup'), 'value' => 'browsepopup'),
					array('text' => tra('Download'), 'value' => 'download'),
				),
			),
			'rel' => array(
				'required' => false,
				'name' => tra('Link Relation'),
				'since' => '3.0',
				'doctype' => 'link',
				'filter' => 'text',
				'description' => tr('Specifies the relationship between the link image and the target. Enter %0 to
					cause the image to enlarge in a popup when clicked.', '<code>box</code>'),
				'advanced' => true,
				'default' => '',
			),
			'usemap' => array(
				'required' => false,
				'name' => tra('Image Map'),
				'filter' => 'text',
				'description' => tra('Name of the image map to use for the image.'),
				'since' => '3.0',
				'doctype' => 'link',
				'advanced' => true,
				'default' => '',
			),
			'hspace' => array(
				'required' => false,
				'name' => tra('Horizontal spacing'),
				'description' => tra('Horizontal spacing, in pixels, applied to both sides of the image. It may be necessary to use this legacy type of styling if the legacyalign parameter needs to be used for cases where float does not work eg newsletters viewed as an email.'),
				'since' => '15.0',
				'doctype' => 'size',
				'filter' => 'digits',
				'advanced' => true,
				'default' => '',
			),
			'vspace' => array(
				'required' => false,
				'name' => tra('Vertical spacing'),
				'description' => tra('Vertical spacing, in pixels, applied to top and bottom of the image. It may be necessary to use this legacy type of styling if the legacyalign parameter needs to be used for cases where float does not work eg newsletters viewed as an email.'),
				'since' => '15.0',
				'doctype' => 'size',
				'filter' => 'digits',
				'advanced' => true,
				'default' => '',
			),
			'legacyalign' => array(
				'required' => false,
				'name' => tra('Align image using legacy align tag'),
				'description' => tra('Aligns the image itself using the legacy align tag for cases where float does not work eg newsletters viewed as an email. Can be used in addition to the imalign parameter for cases where web pages are viewed by modern browsers and are used by the Newsletter function to send a web page as an email'),
				'since' => '15.0',
				'filter' => 'alpha',
				'advanced' => true,
				'default' => '',
				'options' => array(
					array('text' => tra('None'), 'value' => ''),
					array('text' => tra('Right'), 'value' => 'right'),
					array('text' => tra('Left'), 'value' => 'left'),
					array('text' => tra('Center'), 'value' => 'center'),
				),
			),
			'imalign' => array(
				'required' => false,
				'name' => tra('Align Image'),
				'description' => tr('Aligns the image itself. Overridden by any alignment settings in %0styleimage%1.
					If %0stylebox%1 or %0desc%1 are also set, then image only aligns inside the box - use %0stylebox%1
					in this case to align the box on the page.', '<code>', '</code>'),
				'since' => '3.0',
				'doctype' => 'style',
				'filter' => 'alpha',
				'advanced' => true,
				'default' => '',
				'options' => array(
					array('text' => tra('None'), 'value' => ''),
					array('text' => tra('Right'), 'value' => 'right'),
					array('text' => tra('Left'), 'value' => 'left'),
					array('text' => tra('Center'), 'value' => 'center'),
				),
			),
			'styleimage' => array(
				'required' => false,
				'name' => tra('Image Style'),
				'description' => tr('Enter %0border%1 to place a dark gray border around the image. Otherwise enter
					CSS styling syntax for other style effects.', '<code>', '</code>'),
				'since' => '4.0',
				'doctype' => 'style',
				'filter' => 'text',
				'advanced' => true,
				'default' => '',
			),
			'align' => array(
				'required' => false,
				'name' => tra('Align Image Block'),
				'description' => tr('Aligns a block around the image (including the image). Image is no longer inline
					when this setting is used. Can be overridden by any alignment settings in %0stylebox%1.', '<code>', '</code>'),
				'since' => '3.0',
				'doctype' => 'style',
				'filter' => 'alpha',
				'advanced' => true,
				'default' => '',
				'options' => array(
					array('text' => tra('None'), 'value' => ''),
					array('text' => tra('Right'), 'value' => 'right'),
					array('text' => tra('Left'), 'value' => 'left'),
					array('text' => tra('Center'), 'value' => 'center'),
				),
			),
			'stylebox' => array(
				'required' => false,
				'name' => tra('Image Block Style'),
				'filter' => 'text',
				'description' => tr('Enter %0border%1 to place a dark gray border around the image. Otherwise enter
					CSS styling syntax for other style effects.', '<code>', '</code>'),
				'since' => '4.0',
				'doctype' => 'style',
				'advanced' => true,
				'default' => '',
			),
			'styledesc' => array(
				'required' => false,
				'name' => tra('Description Style'),
				'since' => '4.0',
				'doctype' => 'text',
				'filter' => 'text',
				'description' => tr('Enter %0right%1 or %0left%1 to align text accordingly. Otherwise enter CSS styling
					syntax for other style effects.', '<code>', '</code>'),
				'advanced' => true,
				'default' => '',
			),
			'block' => array(
				'required' => false,
				'name' => tra('Wrapping'),
				'description' => tra('Control how other items wrap around the image.'),
				'since' => '4.0',
				'doctype' => 'style',
				'filter' => 'alpha',
				'advanced' => true,
				'default' => '',
				'options' => array(
					array('text' => tra('None'), 'value' => ''),
					array('text' => tra('Top'), 'value' => 'top'),
					array('text' => tra('Bottom'), 'value' => 'bottom'),
					array('text' => tra('Both'), 'value' => 'both'),
				),
			),
			'class' => array(
				'required' => false,
				'name' => tra('CSS Class'),
				'filter' => 'style',
				'description' => tr('CSS class to apply to the image. %0class="fixedSize"%1 prevents the image from being
					automatically resized and relocated in Tiki SlideShows', '<code>', '</code>'),
				'since' => '3.0',
				'doctype' => 'style',
				'advanced' => true,
				'default' => '',
			),
			'title' => array(
				'required' => false,
				'name' => tra('Link Title'),
				'filter' => 'text',
				'description' => tr('This text will appear in a tool tip when the image is moused over. If this is
					not set, the %0desc%1 setting will be used. Use %0name%1 or %0desc%1 or %0namedesc%1 for Tiki name
					and description properties', '<code>', '</code>'),
				'since' => '3.0',
				'doctype' => 'text',
				'advanced' => true,
				'default' => '',
			),
			'metadata' => array(
				'required' => false,
				'name' => tra('Metadata'),
				'filter' => 'text',
				'description' => tra('Display the image metadata (IPTC, EXIF and XMP information).'),
				'since' => '8.0',
				'doctype' => 'text',
				'default' => '',
				'advanced' => true,
				'options' => array(
					array('text' => tra('None'), 'value' => ''),
					array('text' => tra('View'), 'value' => 'view'),
				),
			),
			'default' => array(
				'required' => false,
				'name' => tra('Default Settings'),
				'description' => tra('Default configuration settings (usually set by admin in the source code or
					through Plugin Alias).'),
				'since' => '4.1',
				'doctype' => 'deprecated',
				'advanced' => true,
				'default' => '',
			),
			'mandatory' => array(
				'required' => false,
				'name' => tra('Mandatory Setting'),
				'description' => tra('Mandatory configuration settings (usually set by admin in the source code or
					through Plugin Alias).'),
				'since' => '4.1',
				'doctype' => 'deprecated',
				'advanced' => true,
				'default' => '',
			),
		),
	);
	if ($prefs['feature_galleries'] === 'y') {
		$info['params']['type']['options'][] = array('text' => tra('Image Gallery Image'), 'value' => 'id');
		$info['params']['thumb']['options'][] = array('text' => tra('Browse'), 'value' => 'browse', 'description' => tra('Image gallery browse window for the image will open when the thumbnail is clicked if the image is in a Tiki image gallery'));
		$info['params']['thumb']['options'][] = array('text' => tra('Browse Popup'), 'value' => 'browsepopup', 'description' => tra('Same as "browse" except that the page opens in a new window or tab.'));
		$info['params']['thumb']['description'] = tra('Makes the image a thumbnail that enlarges to full size when clicked or moused over (unless "link" is set to another target). "browse" and "browsepopup" only work with image gallery and "download" only works with file gallery or attachments.');
	}
	if ($prefs['feature_draw'] === 'y') {
		$info['params']['noDrawIcon'] = array(
			'required' => false,
			'name' => tra('Hide Draw Icon'),
			'description' => tra('Do not show draw/edit icon button under image.'),
			'since' => '11.0',
			'doctype' => 'style',
			'advanced' => true,
			'filter' => 'alpha',
			'options' => array(
				array('text' => tra('None'), 'value' => ''),			
				array('text' => tra('No'), 'value' => 'n'),
				array('text' => tra('Yes'), 'value' => 'y'),
			),
			'default' => '',
		);
	}

	if ($prefs['feature_jquery_zoom'] === 'y') {
		$info['params']['thumb']['options'][] = array('text' => tra('Overlay with zoom'), 'value' => 'zoombox', 'description' => tra('Full size image appears with zoom option in a "Colorbox" overlay when thumbnail is clicked.'));
		$info['params']['thumb']['options'][] = array('text' => tra('Zoom'), 'value' => 'zoom', 'description' => tra('Adds a magnifying glass icon and zooms the image when hovered over.'));
	}

	return $info;
}

function wikiplugin_img( $data, $params )
{
	global $tikidomain, $prefs, $user;
	$userlib = TikiLib::lib('user');
	$smarty = TikiLib::lib('smarty');
	$smarty->loadPlugin('smarty_function_icon');

	$imgdata = array();

	$imgdata['src'] = '';
	$imgdata['id'] = '';
	$imgdata['fileId'] = '';
	$imgdata['randomGalleryId'] = '';
	$imgdata['galleryId'] = '';
	$imgdata['fgalId'] = '';
	$imgdata['sort_mode'] = 'created_desc';
	$imgdata['attId'] = '';
	$imgdata['thumb'] = '';
	$imgdata['button'] = '';
	$imgdata['link'] = '';
	$imgdata['rel'] = '';
	$imgdata['usemap'] = '';
	$imgdata['height'] = '';
	$imgdata['width'] = '';
	$imgdata['max'] = '';
	$imgdata['legacyalign'] = '';
	$imgdata['hspace'] = '';
	$imgdata['vspace'] = '';
	$imgdata['imalign'] = '';
	$imgdata['styleimage'] = '';
	$imgdata['align'] = '';
	$imgdata['stylebox'] = '';
	$imgdata['styledesc'] = '';
	$imgdata['block'] = '';
	$imgdata['class'] = '';
	$imgdata['desc'] = '';
	$imgdata['title'] = '';
	$imgdata['metadata'] = '';
	$imgdata['alt'] = '';
	$imgdata['responsive'] = 'y';
	$imgdata['default'] = '';
	$imgdata['mandatory'] = '';
	$imgdata['fromFieldId'] = 0;		// "private" params set by Tracker_Field_Files
	$imgdata['fromItemId']  = 0;		// ditto
	$imgdata['checkItemPerms']  = 'y';	// ditto
	$imgdata['noDrawIcon']  = 'y';

	$imgdata = array_merge($imgdata, $params);

	//function calls
	if ( !empty($imgdata['default']) || !empty($imgdata['mandatory'])) {
		require_once('lib/images/img_plugin_default_and_mandatory.php');
		if (!empty($imgdata['default'])) {
			$imgdata = apply_default_and_mandatory($imgdata, 'default');	//first process defaults
			$imgdata = array_merge($imgdata, $params);					//then apply user settings, overriding defaults
		}
		//apply mandatory settings, overriding user settings
		if (!empty($imgdata['mandatory'])) $imgdata = apply_default_and_mandatory($imgdata, 'mandatory');
	}

	// Before it was possible to specify many image types at once and Tiki will guess which one to use.
	// Now there is "type" field that clearly identifies image type.
	// Code below leaves image param that is related to "type", removing all others,  this way code is not confused if
	// several parameters are passed
	$info = wikiplugin_img_info();
	foreach($info['params']['type']['options'] as $type) {
		if (!empty($type['value']) && $type['value'] != $imgdata['type'] && !empty($imgdata[$type['value']])) {
			$imgdata[$type['value']] = null;
		}
	}

	//////////////////////////////////////////////////// Error messages and clean javascript //////////////////////////////
	// Must set at least one image identifier
	$set = !empty($imgdata['fileId']) + !empty($imgdata['id']) + !empty($imgdata['src']) + !empty($imgdata['attId'])
		+ !empty($imgdata['randomGalleryId']) + !empty($imgdata['fgalId']);
	if ($set == 0) {
		return tra("''No image specified. One of the following parameters must be set: fileId, randomGalleryId, fgalId, attId, id, or src.''");
	} elseif ($set >1) {
		return tra("''Use one and only one of the following parameters: fileId, randomGalleryId, fgalId, attId, id, or src.''");
	}
	// Clean up src URLs to exclude javascript
	if (stristr(str_replace(' ', '', $imgdata['src']), 'javascript:')) {
		$imgdata['src']  = '';
	}
	if (strstr($imgdata['src'], 'javascript:')) {
		$imgdata['src']  = '';
	}

	if (!isset($data) or !$data) {
		$data = '&nbsp;';
	}

	include_once('tiki-sefurl.php');
	//////////////////////Process multiple images //////////////////////////////////////
	//Process "|" or "," separated images
	$notice = '<!--' . tra('PluginImg: User lacks permission to view image') . '-->';
	$srcmash = $imgdata['fileId'] . $imgdata['id'] . $imgdata['attId'] . $imgdata['src'];
	if (( strpos($srcmash, '|') !== false ) || (strpos($srcmash, ',') !== false ) || !empty($imgdata['fgalId'])) {
		$separator = '';
		if (!empty($imgdata['id'])) {
			$id = 'id';
		} elseif (!empty($imgdata['fileId'])) {
			$id = 'fileId';
		} elseif (!empty($imgdata['attId'])) {
			$id = 'attId';
		} else {
			$id = 'src';
		}
		if ( strpos($imgdata[$id], '|') !== false ) {
			$separator = '|';
		} elseif ( strpos($imgdata[$id], ',') !== false ) {
			$separator = ',';
		}
		$repl = '';
		$id_list = array();
		if (!empty($separator)) {
			$id_list = explode($separator, $imgdata[$id]);
		} else { //fgalId parameter - show all images in a file gallery
			$filegallib = TikiLib::lib('filegal');
			$galdata = $filegallib->get_files(0, -1, $imgdata['sort_mode'], '', $imgdata['fgalId'], false, false, false, true, false, false, false, false, '', true, false, false);
			foreach ($galdata['data'] as $filedata) {
				$id_list[] = $filedata['id'];
			}
			$id = 'fileId';
		}
		$params[$id] = '';
		foreach ($id_list as $i => $value) {
			$params[$id] = trim($value);
			$params['fgalId'] = '';
			$repl .= wikiplugin_img($data, $params);
		}
		if (strpos($repl, $notice) !== false) {
			return $repl;
		} else {
			$repl = "\n\r" . '<br style="clear:both" />' . "\r" . $repl . "\n\r" . '<br style="clear:both" />' . "\r";
			return $repl; // return the multiple images
		}
	}

	$repl = '';

	//////////////////////Set src for html///////////////////////////////
	//Set variables for the base path for images in file galleries, image galleries and attachments
	global $base_url;
	$absolute_links = (!empty(TikiLib::lib('parser')->option['absolute_links'])) ? TikiLib::lib('parser')->option['absolute_links'] : false;
	$imagegalpath = ($absolute_links ? $base_url : '') . 'show_image.php?id=';
	$filegalpath = ($absolute_links ? $base_url : '') . 'tiki-download_file.php?fileId=';
	$attachpath = ($absolute_links ? $base_url : '') . 'tiki-download_wiki_attachment.php?attId=';

	//get random image and treat as file gallery image afterwards
	if (!empty($imgdata['randomGalleryId'])) {
		$filegallib = TikiLib::lib('filegal');
		$dbinfo = $filegallib->get_file(0, $imgdata['randomGalleryId']);
		$imgdata['fileId'] = $dbinfo['fileId'];
		$basepath = $prefs['fgal_use_dir'];
	}

	if (empty($imgdata['src'])) {
		if (!empty($imgdata['id'])) {
			$src = $imagegalpath . $imgdata['id'];
		} elseif (!empty($imgdata['fileId'])) {
			$smarty->loadPlugin('smarty_modifier_sefurl');
			$src = smarty_modifier_sefurl($imgdata['fileId'], 'file');

			if ($absolute_links) {
				$src = TikiLib::tikiUrl($src);
			}
		} else {					//only attachments left
			$src = $attachpath . $imgdata['attId'];
		}
	} elseif ( (!empty($imgdata['src'])) && $absolute_links && ! preg_match('|^[a-zA-Z]+:\/\/|', $imgdata['src']) ) {
		global $base_host, $url_path;
		$src = $base_host.( $imgdata['src'][0] == '/' ? '' : $url_path ) . $imgdata['src'];
	} elseif (!empty($imgdata['src']) && $tikidomain && !preg_match('|^https?:|', $imgdata['src'])) {
		$src = preg_replace("~img/wiki_up/~", "img/wiki_up/$tikidomain/", $imgdata['src']);
	} elseif (!empty($imgdata['src'])) {
		$src = $imgdata['src'];
	}

	$browse_full_image = $src;
	$srcIsEditable = false;
	///////////////////////////Get DB info for image size and metadata/////////////////////////////
	if (!empty($imgdata['height']) || !empty($imgdata['width']) || !empty($imgdata['max'])
		|| !empty($imgdata['desc']) || strpos($imgdata['rel'], 'box') !== false
		|| !empty($imgdata['stylebox']) || !empty($imgdata['styledesc']) || !empty($imgdata['button'])
		|| !empty($imgdata['thumb'])  || !empty($imgdata['align']) || !empty($imgdata['metadata'])  || !empty($imgdata['fileId'])
	) {
		//Get ID numbers for images in galleries and attachments included in src as url parameter
		//So we can get db info for these too
		$parsed = parse_url($imgdata['src']);
		if (empty($parsed['host']) || (!empty($parsed['host']) && strstr($base_url, $parsed['host']))) {
			if (strlen(strstr($imgdata['src'], $imagegalpath)) > 0) {
				$imgdata['id'] = substr(strstr($imgdata['src'], $imagegalpath), strlen($imagegalpath));
			} elseif (strlen(strstr($imgdata['src'], $filegalpath)) > 0) {
				$imgdata['fileId'] = substr(strstr($imgdata['src'], $filegalpath), strlen($filegalpath));
			} elseif (strlen(strstr($imgdata['src'], $attachpath)) > 0) {
				$imgdata['attId'] = substr(strstr($imgdata['src'], $attachpath), strlen($attachpath));
			}
		}
		$imageObj = '';
		require_once('lib/images/images.php');
		//Deal with images with info in tiki databases (file and image galleries and attachments)
		if (empty($imgdata['randomGalleryId']) && (!empty($imgdata['id']) || !empty($imgdata['fileId'])
			|| !empty($imgdata['attId']))
		) {
			//Try to get image from database
			if (!empty($imgdata['id'])) {
				$imagegallib = TikiLib::lib('imagegal');
				$dbinfo = $imagegallib->get_image_info($imgdata['id'], 'o');
				$dbinfo2 = $imagegallib->get_image($imgdata['id'], 'o');
				$dbinfo = isset($dbinfo) && isset($dbinfo2) ? array_merge($dbinfo, $dbinfo2) : array();
				$dbinfot = $imagegallib->get_image_info($imgdata['id'], 't');
				$dbinfot2 = $imagegallib->get_image($imgdata['id'], 't');
				$dbinfot = isset($dbinfot) && isset($dbinfot2) ? array_merge($dbinfot, $dbinfot2) : array();
				$basepath = $prefs['gal_use_dir'];
			} elseif (!isset($dbinfo) && !empty($imgdata['fileId'])) {
				$filegallib = TikiLib::lib('filegal');
				$dbinfo = $filegallib->get_file($imgdata['fileId']);
				$basepath = $prefs['fgal_use_dir'];
			} else {					//only attachments left
				global $atts;
				$wikilib = TikiLib::lib('wiki');
				$dbinfo = $wikilib->get_item_attachment($imgdata['attId']);
				$basepath = $prefs['w_use_dir'];
			}
			//Give error messages if file doesn't exist, isn't an image. Display nothing if user lacks permission
			if (!empty($imgdata['fileId']) || !empty($imgdata['id']) || !empty($imgdata['attId'])) {
				if ( ! $dbinfo ) {
					return '^' . tra('File not found.') . '^';
				} elseif ( substr($dbinfo['filetype'], 0, 5) != 'image' AND !preg_match('/thumbnail/i', $imgdata['fileId'])) {
					return '^' . tra('File is not an image.') . '^';
				} elseif (!class_exists('Image')) {
					return '^' . tra('Server does not support image manipulation.') . '^';
				} elseif (!empty($imgdata['fileId'])) {
					if (!$userlib->user_has_perm_on_object($user, $dbinfo['galleryId'], 'file gallery', 'tiki_p_download_files')) {
						return $notice;
					}
				} elseif (!empty($imgdata['id'])) {
					if (!$userlib->user_has_perm_on_object($user, $dbinfo['galleryId'], 'image gallery', 'tiki_p_view_image_gallery')) {
						return $notice;
					}
				} elseif (!empty($imgdata['attId'])) {
					if (!$userlib->user_has_perm_on_object($user, $dbinfo['page'], 'wiki page', 'tiki_p_wiki_view_attachments')) {
						return $notice;
					}
				}
			}
		} //finished getting info from db for images in image or file galleries or attachments

		//get image to get height and width and iptc data
		if (!empty($dbinfo['data'])) {
			$imageObj = new Image($dbinfo['data'], false);
			$filename = $dbinfo['filename'];
		} elseif (!empty($dbinfo['path'])) {
			$imageObj = new Image($basepath . $dbinfo['path'], true);
			$filename = $dbinfo['filename'];
		} elseif (strpos($src, '//') === false) {
			$imageObj = new Image($src, true);
			$filename = $src;
		}
		// NOTE image sizing should only happen with local images, otherwise will break if remote server can't be reached

		//if we need metadata
		$xmpview = !empty($imgdata['metadata']) ? true : false;
		if (is_object($imageObj) && ($imgdata['desc'] == 'idesc' || $imgdata['desc'] == 'ititle' || $xmpview)) {
			$dbinfoparam = isset($dbinfo) ? $dbinfo : false;
			$metadata = getMetadataArray($imageObj, $dbinfoparam);
			if ($imgdata['desc'] == 'idesc') {
				$idesc = getMetaField($metadata, array('User Data' => 'Description'));
			}
			if ($imgdata['desc'] == 'ititle') {
				$ititle = getMetaField($metadata, array('User Data' => 'Title'));
			}
		}

		$fwidth = '';
		$fheight = '';
		if (!is_object($imageObj) || isset(TikiLib::lib('parser')->option['indexing']) && TikiLib::lib('parser')->option['indexing']) {
			$fwidth = 1;
			$fheight = 1;
		} else {
			$fwidth = $imageObj->get_width();
			$fheight = $imageObj->get_height();
		}
		//get image gal thumbnail image for height and width
		if (!empty($dbinfot['data']) || !empty($dbinfot['path'])) {
			if (!empty($dbinfot['data'])) {
				$imageObjt = new Image($dbinfot['data'], false);
			} elseif (!empty($dbinfot['path'])) {
				$imageObjt = new Image($basepath . $dbinfot['path'] . '.thumb', true);
			}
			$fwidtht = $imageObjt->get_width();
			$fheightt = $imageObjt->get_height();
		}
	/////////////////////////////////////Add image dimensions to src string////////////////////////////////////////////
		//Use url resizing parameters for file gallery images to set $height and $width
		//since they can affect other elements; overrides plugin parameters
		if (!empty($imgdata['fileId']) && strpos($src, '&') !== false) {
			$urlthumb = strpos($src, '&thumbnail');
			$urlprev = strpos($src, '&preview');
			$urldisp = strpos($src, '&display');
			preg_match('/(?<=\&max=)[0-9]+(?=.*)/', $src, $urlmax);
			preg_match('/(?<=\&x=)[0-9]+(?=.*)/', $src, $urlx);
			preg_match('/(?<=\&y=)[0-9]+(?=.*)/', $src, $urly);
			preg_match('/(?<=\&scale=)[0]*\.[0-9]+(?=.*)/', $src, $urlscale);
			if (!empty($urlmax[0]) && $urlmax[0] > 0) $imgdata['max'] = $urlmax[0];
			if (!empty($urlx[0]) && $urlx[0] > 0) $imgdata['width'] = $urlx[0];
			if (!empty($urly[0]) && $urly[0] > 0) $imgdata['height'] = $urly[0];
			if (!empty($urlscale[0]) && $urlscale[0] > 0) {
				$height = floor($urlscale[0] * $fheight);
				$width = floor($urlscale[0] * $fwidth);
				$imgdata['width'] = '';
				$imgdata['height'] = '';
			}
			if ($urlthumb != false && empty($imgdata['height']) && empty($imgdata['width']) && empty($imgdata['max'])) $imgdata['max'] = 120;
			if ($urlprev != false && empty($urlscale[0]) && empty($imgdata['height']) && empty($imgdata['width']) && empty($imgdata['max']) ) $imgdata['max'] = 800;
		}
		//Note if image gal url thumb parameter is used
		$imgalthumb = false;
		if (!empty($imgdata['id'])) {
			preg_match('/(?<=\&thumb=1)[0-9]+(?=.*)/', $src, $urlimthumb);
			if (!empty($urlimthumb[0]) && $urlimthumb[0] > 0) $imgalthumb = true;
		}

		include_once ('lib/mime/mimetypes.php');
		global $mimetypes;

		//Now set dimensions based on plugin parameter settings
		if (!empty($imgdata['max']) || !empty($imgdata['height']) || !empty($imgdata['width'])
			|| !empty($imgdata['thumb'])
		) {
			// find svg image size
			if (!empty($dbinfo['filetype'])  && !empty($mimetypes['svg']) && $dbinfo['filetype'] == $mimetypes['svg']) {
				if (preg_match('/width="(\d+)" height="(\d+)"/', $dbinfo['data'], $svgdim)) {
					$fwidth = $svgdim[1];
					$fheight = $svgdim[2];
				}
			}
			//Convert % and px in height and width
			$scale = '';
			if (strpos($imgdata['height'], '%') !== false || strpos($imgdata['width'], '%') !== false) {
				if ((strpos($imgdata['height'], '%') !== false && strpos($imgdata['width'], '%') !== false)
					&& (empty($imgdata['fileId']) || (empty($urlx[0]) && empty($urly[0])))) {
					$imgdata['height'] = floor(rtrim($imgdata['height'], '%') / 100 * $fheight);
					$imgdata['width'] = floor(rtrim($imgdata['width'], '%') / 100 * $fwidth);
				} elseif (strpos($imgdata['height'], '%') !== false) {
					if ($imgdata['fileId']) {
						$scale = rtrim($imgdata['height'], '%') / 100;
						$height = floor($scale * $fheight);
					} else {
						$imgdata['height'] = floor(rtrim($imgdata['height'], '%') / 100 * $fheight);
					}
				} else {
					if ($imgdata['fileId']) {
						$scale = rtrim($imgdata['width'], '%') / 100;
						$width = floor($scale * $fwidth);
					} else {
						$imgdata['width'] = floor(rtrim($imgdata['width'], '%') / 100 * $fwidth);
					}
				}
			} elseif (strpos($imgdata['height'], 'px') !== false || strpos($imgdata['width'], 'px') !== false) {
				if (strpos($imgdata['height'], 'px') !== false) {
					$imgdata['height'] = rtrim($imgdata['height'], 'px');
				} else {
					$imgdata['width'] = rtrim($imgdata['width'], 'px');
				}
			}
			// Adjust for max setting, keeping aspect ratio
			if (!empty($imgdata['max'])) {
				if (($fwidth > $imgdata['max']) || ($fheight > $imgdata['max'])) {
					//use image gal thumbs when possible
					if ((!empty($imgdata['id']) && $imgalthumb == false)
						&& ($imgdata['max'] < $fwidtht || $imgdata['max'] < $fheightt)
					) {
						$src .= '&thumb=1';
						$imgalthumb == true;
					}
					if ($fwidth > $fheight) {
						$width = $imgdata['max'];
						$height = floor($width * $fheight / $fwidth);
					} else {
						$height = $imgdata['max'];
						$width = floor($height * $fwidth / $fheight);
					}
				//cases where max is set but image is smaller than max
				} else {
					$height = $fheight;
					$width = $fwidth;
				}
			// Adjust for user settings for height and width if max isn't set.
			} elseif (!empty($imgdata['height']) ) {
				//use image gal thumbs when possible
				if ((!empty($imgdata['id']) && $imgalthumb == false)
					&& ($imgdata['height'] < $fheightt)
				) {
					$src .= '&thumb=1';
					$imgalthumb == true;
				}
				$height = $imgdata['height'];
				if (empty($imgdata['width']) && $fheight > 1) {
					$width = floor($height * $fwidth / $fheight);
				} else {
					$width = $imgdata['width'];
				}
			} elseif (!empty($imgdata['width'])) {
				//use image gal thumbs when possible
				if ((!empty($imgdata['id']) && $imgalthumb == false)
					&& ($imgdata['width'] < $fwidtht)
				) {
					$src .= '&thumb=1';
					$imgalthumb == true;
				}
				$width =  $imgdata['width'];
				if (empty($imgdata['height']) && $fwidth > 1) {
					$height = floor($width * $fheight / $fwidth);
				} else {
					$height = $imgdata['height'];
				}
			// If not otherwise set, use default setting for thumbnail height if thumb is set
			} elseif ((!empty($imgdata['thumb']) || !empty($urlthumb))  && empty($scale)) {
				if (!empty($imgdata['fileId'])) {
					$thumbdef = $prefs['fgal_thumb_max_size'];
				} else {
					$thumbdef = 84;
				}
				//handle image gal thumbs
				if (!empty($imgdata['id']) && !empty($fwidtht)  && !empty($fheightt)) {
					$width = $fwidtht;
					$height = $fheightt;
					if ($imgalthumb == false) {
						$src .= '&thumb=1';
						$imgalthumb == true;
					}
				} else {
					if (($fwidth > $thumbdef) || ($fheight > $thumbdef)) {
						if ($fwidth > $fheight) {
							$width = $thumbdef;
							$height = floor($width * $fheight / $fwidth);
						} else {
							$height = $thumbdef;
							$width = floor($height * $fwidth / $fheight);
						}
					}
				}
			}
		}

		//Set final height and width dimension string
		//handle file gallery images separately to use server-side resizing capabilities
		$imgdata_dim = '';
		if (!empty($imgdata['fileId'])) {
			if (empty($urldisp) && empty($urlthumb)) {
				$srcIsEditable = true;
				$src .= '&display';
			}
			if (!empty($scale) && empty($urlscale[0])) {
				$src .= '&scale=' . $scale;
			} elseif ((!empty($imgdata['max']) && $imgdata['thumb'] != 'download')
					&& (empty($urlthumb) && empty($urlmax[0]) && empty($urlprev))
			) {
				$src .= '&max=' . $imgdata['max'];
				$imgdata_dim .= ' width="' . $width . '"';
				$imgdata_dim .= ' height="' . $height . '"';
			} elseif (!empty($width) || !empty($height)) {
				if ((!empty($width) && !empty($height)) && (empty($urlx[0]) && empty($urly[0]) && empty($urlscale[0]))) {
					$src .= '&x=' . $width . '&y=' . $height;
					$imgdata_dim .= ' width="' . $width . '"';
					$imgdata_dim .= ' height="' . $height . '"';
				} elseif (!empty($width) && (empty($urlx[0]) && empty($urlthumb) && empty($urlscale[0]))) {
					$src .= '&x=' . $width;
					$height = $fheight;
					$imgdata_dim .= ' width="' . $width . '"';
					$imgdata_dim .= ' height="' . $height . '"';
				} elseif (!empty($height) && (empty($urly[0]) && empty($urlthumb) && empty($urlscale[0]))) {
					$src .= '&y=' . $height;
					$width = $fwidth;
					$imgdata_dim .= ' width="' . $width . '"';
					$imgdata_dim .= ' height="' . $height . '"';
				}
			} else {
				$imgdata_dim = '';
				$height = $fheight;
				$width = $fwidth;
				if (!empty($width) && !empty($height)) {
					$imgdata_dim .= ' width="' . $width . '"';
					$imgdata_dim .= ' height="' . $height . '"';
				}
			}
		} else {
			if (!empty($height)) {
				$imgdata_dim = ' height="' . $height . '"';
			} else {
				$imgdata_dim = '';
				$height = $fheight;
			}
			if (!empty($width)) {
				$imgdata_dim .= ' width="' . $width . '"';
			} elseif (empty($height)) {
				$imgdata_dim = '';
				$width = $fwidth;
			}
		}
	}

	////////////////////////////////////////// Create the HTML img tag //////////////////////////////////////////////
	//Start tag with src and dimensions
	$src = filter_out_sefurl($src);

	$tagName = '';
	if (!empty($dbinfo['filetype'])  && !empty($mimetypes['svg']) && $dbinfo['filetype'] == $mimetypes['svg']) {
		$tagName = 'div';
		$repldata = $dbinfo['data'];
		if (!empty($fwidth) && !empty($fheight) && !empty($imgdata_dim)) {		// change svg attributes to show at the correct size
			$svgAttributes = $imgdata_dim . ' viewBox="0 0 ' . $fwidth . ' ' . $fheight . '" preserveAspectRatio="xMinYMin meet"';
			$repldata = preg_replace('/width="'.$fwidth.'" height="'.$fheight.'"/', $svgAttributes, $repldata);
		}
		$replimg = '<div type="image/svg+xml" ';
		$imgdata['class'] .= ' table-responsive svgImage pluginImg' . $imgdata['fileId'];
		$imgdata['class'] = trim($imgdata['class']);
	} else {
		$tagName = 'img';
		$replimg = '<img src="' . $src . '" ';
		if ($imgdata['responsive'] == 'y') {
			$imgdata['class'] .= ' regImage img-responsive pluginImg' . $imgdata['fileId'];
		} else {
			$imgdata['class'] .= ' regImage pluginImg' . $imgdata['fileId'];
		}
		$imgdata['class'] = trim($imgdata['class']);
	}

	if (!empty($imgdata_dim)) $replimg .= $imgdata_dim;
	
	//Configure alignment if legacy align has been set
	//legacyalign
	if ( !empty($imgdata['legacyalign']) ) {
		$replimg .= ' align="' . $imgdata['legacyalign'] . '"';
	}
	
	//Configure horizontal spacing if legacy hspace has been set
	//hspace
	if ( !empty($imgdata['hspace']) ) {
		$replimg .= ' hspace="' . $imgdata['hspace'] . '"';
	}
	
	//Configure vertical spacing if legacy vspace has been set
	//vspace
	if ( !empty($imgdata['vspace']) ) {
		$replimg .= ' vspace="' . $imgdata['vspace'] . '"';
	}

	//Create style attribute allowing for shortcut inputs
	//First set alignment string
	$center = 'display:block; margin-left:auto; margin-right:auto;';	//used to center image and box
	$imalign = '';
	if (!empty($imgdata['imalign'])) {
		if ($imgdata['imalign'] == 'center') {
			$imalign = $center;
		} else {
			$imalign = 'float:' . $imgdata['imalign'] . ';';
		}
	} elseif ($imgdata['stylebox'] == 'border') {
		$imalign = $center;
	}
	//set entire style string
	$style = '';
	if ( !empty($imgdata['styleimage']) || !empty($imalign) ) {
		$border = '';
		$borderdef = 'border:1px solid darkgray;';   //default border when styleimage set to border
		if ( !empty($imgdata['styleimage'])) {
			if (!empty($imalign)) {
				if ((strpos(trim($imgdata['styleimage'], ' '), 'float:') !== false)
					|| (strpos(trim($imgdata['styleimage'], ' '), 'display:') !== false)
				) {
					$imalign = '';			//override imalign setting if style image contains alignment syntax
				}
			}
			if ($imgdata['styleimage'] == 'border') {
				$border = $borderdef;
			} else if (strpos($imgdata['styleimage'], 'hidden') === false
				&& strpos($imgdata['styleimage'], 'position') === false
			) {	// quick filter for dangerous styles
				$style = $imgdata['styleimage'];
			}
		}
		$replimg .= ' style="' . $imalign . $border . $style . '"';
	}
	//alt
	if ( !empty($imgdata['alt']) ) {
		$replimg .= ' alt="' . $imgdata['alt'] . '"';
	} elseif ( !empty($imgdata['desc']) ) {
		$replimg .= ' alt="' . $imgdata['desc'] . '"';
	} elseif (!empty($dbinfo['description'])) {
		$replimg .= ' alt="' . $dbinfo['description'] . '"';
	} elseif (!empty($dbinfo['name'])) {
		$replimg .= ' alt="' . $dbinfo['name'] . '"';
	} else {
		$replimg .= ' alt="Image"';
	}
	//usemap
	if ( !empty($imgdata['usemap']) ) {
		$replimg .= ' usemap="#' . $imgdata['usemap'] . '"';
	}
	//class
	if ( !empty($imgdata['class']) ) {
		$replimg .= ' class="' . $imgdata['class'] . '"';
	}

	//title (also used for description and link title below)
	//first set description, which is used for title if no title is set
	if (!empty($imgdata['desc']) || !empty($imgdata['title'])) {
		$desconly = '';
		//attachment database uses comment instead of description or name
		if (!empty($dbinfo['comment'])) {
			$desc = $dbinfo['comment'];
			$imgname = $dbinfo['comment'];
		} else {
			$desc = !empty($dbinfo['description']) ? $dbinfo['description'] : '';
			$imgname = !empty($dbinfo['name']) ? $dbinfo['name'] : '';
		}
		if ( !empty($imgdata['desc']) ) {
			switch ($imgdata['desc']) {
				case 'desc':
					$desconly = $desc;
    				break;
				case 'idesc':
					$desconly = $idesc;
    				break;
				case 'name':
					$desconly = $imgname;
    				break;
				case 'ititle':
					$desconly = $ititle;
    				break;
				case 'namedesc':
					$desconly = $imgname.((!empty($imgname) && !empty($desc))?' - ':'').$desc;
    				break;
				default:
					$desconly = $imgdata['desc'];
			}
		}
		//now set title
		$imgtitle = '';
		$titleonly = '';
		if ( !empty($imgdata['title']) || !empty($desconly)) {
			$imgtitle = ' title="';
			if ( !empty($imgdata['title']) ) {
				switch ($imgdata['title']) {
				case 'desc':
					$titleonly = $desc;
    				break;
				case 'name':
					$titleonly = $imgname;
    				break;
				case 'namedesc':
					$titleonly = $imgname.((!empty($imgname) && !empty($desc))?' - ':'').$desc;
    				break;
				default:
					$titleonly = $imgdata['title'];
				}
			//use desc setting for title if title is empty
			} else {
				$titleonly = $desconly;
			}
			$imgtitle .= $titleonly . '"';
			$replimg .= $imgtitle;
		}
	}

	if (empty($repldata)) {
		$replimg .= ' />' . "\r";
	} else {
		$replimg .= '>' . $repldata . '</' . $tagName . '>';
	}

	////////////////////////////////////////// Create the HTML link ///////////////////////////////////////////
	//Variable for identifying if javascript mouseover is set
	if (($imgdata['thumb'] == 'mouseover') || ($imgdata['thumb'] == 'mousesticky')) {
		$javaset = 'true';
	} else {
		$javaset = '';
	}
	// Set link to user setting or to image itself if thumb is set
	if (!empty($imgdata['link']) || (!empty($imgdata['thumb']) && !(isset($params['link']) && empty($params['link'])))) {
		$mouseover = '';
		if (!empty($imgdata['link'])) {
			$link = $imgdata['link'];
		} elseif ((($imgdata['thumb'] == 'browse') || ($imgdata['thumb'] == 'browsepopup')) && !empty($imgdata['id'])) {
			$link = 'tiki-browse_image.php?imageId=' . $imgdata['id'];
		} elseif ($javaset == 'true') {
			$link = 'javascript:void(0)';
			$popup_params = array( 'text'=>$data, 'width'=>$fwidth, 'height'=>$fheight, 'background'=>$browse_full_image);
			if ($imgdata['thumb'] == 'mousesticky') {
				$popup_params['sticky'] = true;
			}
			
			if ($imgdata['thumb'] == 'mouseover') {
				$popup_params['trigger'] = 'hover';
			} 
			// avoid big images will not be closeable on hover. Fallback to require a click to open and a second click somewhere to close.
			if ($fwidth > 400 || $fheight > 400) {
				$popup_params['trigger'] = 'focus';
			}
			
			$smarty->loadPlugin('smarty_function_popup');
			
			$mouseover = ' ' . smarty_function_popup($popup_params, $smarty);
		} else {
			if (!empty($imgdata['fileId']) && $imgdata['thumb'] != 'download' && empty($urldisp)) {
				$link = $browse_full_image . '&display';
			} else {
				$link = $browse_full_image;
			}
		}
		if (($imgdata['thumb'] == 'box' || $imgdata['thumb'] == 'zoombox') && empty($imgdata['rel'])) {
			$imgdata['rel'] = 'box';
		} else if ($imgdata['thumb'] == 'zoom') {
			$imgdata['rel'] = 'zoom';
		}

		if($imgdata['thumb'] == 'zoombox') {
			$zoomscript = "$(document).bind('cbox_complete', function(){
								$('.cboxPhoto').wrap('<span class=\"zoom_container\" style=\"display:inline-block\"></span>')
								.css('display', 'block')
								.parent()
								.zoom({
									on: 'click'
								});
								$('.zoom_container').append('<div class=\"zoomIcon\"></div>');
								$('.zoomIcon').css('position','relative').css('height','20px').css('width','90px').css('top','-20px')
									.css('background','white').css('padding','3px').css('font-size','14px')
									.html('Click to zoom');
								$('#cboxLoadedContent').css('height', 'auto');
							});";
			TikiLib::lib('header')->add_jq_onready($zoomscript);
		}
		// Set other link-related attributes
		// target
		$imgtarget= '';
		if (($prefs['popupLinks'] == 'y' && (preg_match('#^([a-z0-9]+?)://#i', $link)
			|| preg_match('#^www\.([a-z0-9\-]+)\.#i', $link))) || ($imgdata['thumb'] == 'popup')
			|| ($imgdata['thumb'] == 'browsepopup')
		) {
			if (!empty($javaset) || ($imgdata['rel'] == 'box')) {
				$imgtarget= '';
			} else {
				$imgtarget = ' target="_blank"';
			}
		}
		// rel or data-box
		if (!empty($imgdata['rel'])) {
			$box = ['box', 'type=', 'slideshow', 'zoom'];
			foreach ($box as $btype) {
				if (strpos($imgdata['rel'], $btype) !== false) {
					$attr = 'data-box';
					break;
				}
			}
			if (!isset($attr)) {
				$attr = 'rel';
			}
			$linkrel = ' ' . $attr . '="'.$imgdata['rel'].'"';
		} else {
			$linkrel = '';
		}
		// title
		!empty($imgtitle) ? $linktitle = $imgtitle : $linktitle = '';

		$link = filter_out_sefurl($link);

		// For ImgPlugin alignment 
		$position = '';
		$style = '';
		if($imgdata['imalign'] == "right"){
			$style ='style="float: right;"';
		}elseif($imgdata['imalign'] == "center"){
			$position = "center";
		}

		//Final link string
		$replimg = "\r\t" . '<a href="' . $link . '"' . $style . ' class="internal" position="' . $position . '"' . $linkrel . $imgtarget . $linktitle
					. $mouseover . '>' ."\r\t\t" . $replimg . "\r\t" . '</a>';
		if ($imgdata['thumb'] == 'mouseover') {
			$mouseevent = "$('.internal').popover({ 
						  html : true,
						  placement :wheretoplace
						  });
							function wheretoplace(pop, dom_el) {
						      var width = window.innerWidth;
						      if (width<500) return 'bottom';
						      var left_pos = $(dom_el).offset().left;
						      if (width - left_pos > 400) return 'right';
						      return 'left';
						    }
							";
			TikiLib::lib('header')->add_jq_onready($mouseevent);
		}
		
	}

	//Add link string to rest of string
	$repl .= $replimg;

//////////////////////////Generate metadata dialog box and jquery (dialog icon added in next section)////////////////////////////////////
	if ($imgdata['metadata'] == 'view') {
		//create unique id's in case of multiple pictures
		static $lastval = 0;
		$id_meta = 'imgdialog-' . ++$lastval;
		$id_link = $id_meta . '-link';
		//use metadata stored in file gallery db if available
		include_once 'lib/metadata/metadatalib.php';
		$meta = new FileMetadata;
		$dialog = $meta->dialogTabs($metadata, $id_meta, $id_link, $filename);
		$repl .= $dialog;
	}
	//////////////////////  Create enlarge button, metadata icon, description and their divs////////////////////
	//Start div that goes around button and description if these are set
	if (!empty($imgdata['button']) || !empty($imgdata['desc']) || !empty($imgdata['styledesc']) || !empty($imgdata['metadata'])) {
		//To set room for enlarge button under image if there is no description
		$descheightdef = 'height:17px;clear:left;';
		$repl .= "\r\t" . '<div class="mini" style="width:' . $width . 'px;';
		if ( !empty($imgdata['styledesc']) ) {
			if (($imgdata['styledesc'] == 'left') || ($imgdata['styledesc'] == 'right')) {
				$repl .= 'text-align:' . $imgdata['styledesc'] . '">';
			} else {
			$repl .= $imgdata['styledesc'] . '">';
			}
		} elseif ((!empty($imgdata['button'])) && (empty($desconly))) {
			$repl .= $descheightdef . '">';
		} else {
			$repl .= '">';
		}

		//Start description div that also includes enlarge button div
		$repl .= "\r\t\t" . '<div class="thumbcaption">';

		//Enlarge button div and link string (innermost div)
		if (!empty($imgdata['button'])) {
			if (empty($link) || (!empty($link) && !empty($javaset))) {
				if ((($imgdata['button'] == 'browse') || ($imgdata['button'] == 'browsepopup')) && !empty($imgdata['id'])) {
					$link_button = 'tiki-browse_image.php?imageId=' . $imgdata['id'];
				} else {
					if (!empty($imgdata['fileId']) && $imgdata['button'] != 'download') {
						$link_button = $browse_full_image . '&display';
					} elseif (!empty($imgdata['attId']) && $imgdata['thumb'] == 'download') {
						$link_button = $browse_full_image . '&download=y';
					} else {
						$link_button = $browse_full_image;
					}
				}
				$link_button = filter_out_sefurl($link_button);
			} else {
				$link_button = $link;
			}
			//Set button rel
			if (!empty($imgdata['rel'])) {
				$box = ['box', 'type=', 'slideshow', 'zoom'];
				foreach ($box as $btype) {
					if (strpos($imgdata['rel'], $btype) !== false) {
						$attr = 'data-box';
						break;
					}
				}
				if (!isset($attr)) {
					$attr = 'rel';
				}
				$linkrel_button = ' ' . $attr . '="'.$imgdata['rel'].'"';
			} else {
				$linkrel_button = '';
			}
			//Set button target
			if (empty($imgtarget) && (empty($imgdata['thumb']) || !empty($javaset))) {
				if (($imgdata['button'] == 'popup') || ($imgdata['button'] == 'browsepopup')) {
					$imgtarget_button = ' target="_blank"';
				} else {
					$imgtarget_button = '';
				}
			} else {
				$imgtarget_button = $imgtarget;
			}
			$repl .= "\r\t\t\t" . '<div class="magnify" style="float:right">';
			$repl .= "\r\t\t\t\t" . '<a href="' . $link_button . '"' . $linkrel_button . $imgtarget_button ;
			$repl .= ' class="internal"';
			if (!empty($titleonly)) {
				$repl .= ' title="' . $titleonly . '"';
			}
			$repl .=  ">\r\t\t\t\t"  . smarty_function_icon(['name' => 'view', 'iclass' => 'tips',
					'ititle' => ':' . tra('Enlarge')], $smarty) . '</a>' . "\r\t\t\t</div>";
		}
		//Add metadata icon
		if ($imgdata['metadata'] == 'view') {
			$repl .= '<div style="float:right; margin-right:2px"><a href="#" id="' . $id_link
				. '" class="tips" title=":' . tra('Metadata') . '">' . smarty_function_icon(['name' => 'tag'], $smarty)
				. '</a></div>';
		}
		//Add description based on user setting (use $desconly from above) and close divs
		isset($desconly) ? $repl .= $desconly : '';
		$repl .= "\r\t\t</div>";
		$repl .= "\r\t</div>";
	}
	///////////////////////////////Wrap in overall div that includes image if needed////////////////
	//Need a box if any of these are set
	if (!empty($imgdata['button']) || !empty($imgdata['desc']) || !empty($imgdata['metadata'])
		|| !empty($imgdata['stylebox']) || !empty($imgdata['align'])
	) {
		//Make the div surrounding the image 2 pixels bigger than the image
		if (empty($height)) $height = '';
		if (empty($width)) $width = '';
		$boxwidth = $width + 2;
		$boxheight = $height + 2;
		$alignbox = '';
		$class = '';
		if (!empty($imgdata['align'])) {
			if ($imgdata['align'] == 'center') {
				$alignbox = $center;
			} else {
				$alignbox = 'float:' . $imgdata['align'] . '; margin-' . ($imgdata['align'] == 'left'? 'right': 'left') .':5px;';
			}
		}
		//first set stylebox string if style box is set
		if (!empty($imgdata['stylebox']) || !empty($imgdata['align'])) {		//create strings from shortcuts first
			if ( !empty($imgdata['stylebox'])) {
				if ($imgdata['stylebox'] == 'border') {
					$class = 'class="imgbox" ';
					if (!empty($alignbox)) {
						if ((strpos(trim($imgdata['stylebox'], ' '), 'float:') !== false)
							|| (strpos(trim($imgdata['stylebox'], ' '), 'display:') !== false)
						) {
							$alignbox = '';			//override align setting if stylebox contains alignment syntax
						}
					}
				} else {
					$styleboxinit = $imgdata['stylebox'] . ';';
				}
			}
			if (empty($imgdata['button']) && empty($imgdata['desc']) && empty($styleboxinit)) {
				$styleboxplus = $alignbox . ' width:' . $boxwidth . 'px; height:' . $boxheight . 'px';
			} elseif (!empty($styleboxinit)) {
				if ((strpos(trim($imgdata['stylebox'], ' '), 'height:') === false)
					&& (strpos(trim($imgdata['stylebox'], ' '), 'width:') === false)
				) {
					$styleboxplus = $styleboxinit . ' width:' . $boxwidth . 'px;';
				} else {
					$styleboxplus = $styleboxinit;
				}
			} elseif ($boxwidth === 2) {
				$styleboxplus = $alignbox . ' width: auto;';
			} else {
				$styleboxplus = $alignbox . ' width:' . $boxwidth . 'px;';
			}
		} elseif (!empty($imgdata['button']) || !empty($imgdata['desc']) || !empty($imgdata['metadata'])) {
			$styleboxplus = ' width:' . $boxwidth . 'px;';
		} elseif ($boxwidth === 2) {
			$styleboxplus = ' width: auto;';
		}
	}
	if ( !empty($styleboxplus)) {
		$repl = "\r" . '<div ' . $class . 'style="' . $styleboxplus . '">' . $repl . "\r" . '</div>';
	}
//////////////////////////////////////Place 'clear' block///////////////////////////////////////////////////////////
	if ( !empty($imgdata['block']) ) {
		switch ($imgdata['block']) {
		case 'top':
			$repl = "\n\r<br style=\"clear:both\" />\r" . $repl;
    		break;
		case 'bottom':
			$repl = $repl . "\n\r<br style=\"clear:both\" />\r";
    		break;
		case 'both':
			$repl = "\n\r<br style=\"clear:both\" />\r" . $repl . "\n\r<br style=\"clear:both\" />\r";
    		break;
		}
	}
	// Mobile
	if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'mobile') {
		$repl = '{img src=' . $src . "\"}\n<p>" . $imgdata['desc'] . '</p>';
	}

	if ( ! TikiLib::lib('parser')->option['suppress_icons'] &&
			$prefs['feature_draw'] == 'y' && !empty($dbinfo['galleryId']) && $imgdata['noDrawIcon'] !== 'y') {

		global $tiki_p_edit;
		$perms = TikiLib::lib('tiki')->get_perm_object( $imgdata['fileId'], 'file', $dbinfo );
		if ($imgdata['fromItemId']) {
			if ($imgdata['checkItemPerms'] !== 'n') {
				$perms_Accessor = Perms::get(array('type' => 'tracker item', 'object' => $imgdata['fromItemId']));
				$trackerItemPerms = $perms_Accessor->modify_tracker_items;
			} else {
				$trackerItemPerms = true;
			}
		} else {
			$trackerItemPerms = false;
		}

		if ($perms['tiki_p_upload_files'] === 'y' &&
			(empty($src) == true || $srcIsEditable == true) &&
			($tiki_p_edit == 'y' || $trackerItemPerms)) {

			if ($prefs['wiki_edit_icons_toggle'] == 'y' && !isset($_COOKIE['wiki_plugin_edit_view']) && !$imgdata['fromItemId']) {
				$iconDisplayStyle = " style=\"display:none;\"";
			} else {
				$iconDisplayStyle = '';
			}
			$jsonParams = json_encode(array_filter($imgdata));
			$repl .= "<a href=\"tiki-edit_draw.php?fileId={$imgdata['fileId']}\" onclick=\"return $(this).ajaxEditDraw();\" title=\""
				. tr("Draw on the Image") . "\"" .
				" class=\"editplugin pluginImgEdit{$imgdata['fileId']}\" data-fileid=\"{$imgdata['fileId']}\" " .
				"data-galleryid=\"{$dbinfo['galleryId']}\"{$iconDisplayStyle} data-imgparams='$jsonParams'>" .
				smarty_function_icon(['name' => 'edit', 'iclass' => 'tips', 'ititle' => ':' . tra('Edit')], $smarty)
				. '</a>';
		}
	}
	$repl = str_replace('&', '&amp;', $repl);
	return '~np~' . $repl . "\r" . '~/np~';
}

function getMetadataArray($imageObj, $dbinfo = false)
{
	if ($dbinfo !== false) {
		if (!empty($dbinfo['metadata'])) {
			$metarray = json_decode($dbinfo['metadata'], true);
		} elseif (isset($dbinfo['fileId'])) {
			$filegallib = TikiLib::lib('filegal');
			$metarray = $filegallib->metadataAction($dbinfo['fileId']);
		} else {
			$metarray = $imageObj->getMetadata()->typemeta['best'];
		}
	} else {
		$metarray = $imageObj->getMetadata()->typemeta['best'];
	}
	return $metarray;
}

function getMetaField($metarray, $labelarray)
{
	include_once 'lib/metadata/reconcile.php';
	$rec = new ReconcileExifIptcXmp;
	$labelmap = $rec->basicSummary[key($labelarray)][$labelarray[key($labelarray)]];
	foreach ($labelmap as $type => $fieldname) {
		foreach ($metarray as $subtype => $group) {
			if ($type == $subtype) {
				foreach ($group as $groupname => $fields) {
					if (array_key_exists($fieldname, $fields)) {
						$ret = $fields[$fieldname]['newval'];
						return $ret;
					}
				}
				break;
			}
		}
	}
}
