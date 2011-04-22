<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_img_info() {
	return array(
		'name' => tra('Image'),
		'documentation' => 'PluginImg',
		'description' => tra('Display custom formatted images'),
		'prefs' => array( 'wikiplugin_img'),
		'icon' => 'pics/icons/picture.png',
		'params' => array(
			'src' => array(
				'required' => false,
				'name' => tra('Image source'),
				'type' => 'image',
				'area' => 'fgal_picker',
				'description' => tra('Full URL to the image to display. "src", id", "fileId", "attId" or "randomGalleryId" required.'),
				'filter' => 'url',
				'default' => '',
			),
			'id' => array(
				'required' => false,
				'name' => tra('Image ID'),
				'description' => tra('Numeric ID of an image in an Image Gallery (or list separated by commas or |).'),
				'filter' => 'striptags',
				'default' => '',
			),
			'fileId' => array(
				'required' => false,
				'name' => tra('File ID'),
				'type' => 'image',
				'area' => 'fgal_picker_id',
				'description' => tra('Numeric ID of an image in a File Gallery (or list separated by commas or |).'),
				'filter' => 'striptags',
				'default' => '',
			),
			'randomGalleryId' => array(
				'required' => false,
				'name' => tra('Gallery ID'),
				'description' => tra('Numeric ID of a file gallery. Displays a random image from that gallery.'),
				'filter' => 'int',
				'advanced' => true,
				'default' => '',
			),
			'fgalId' => array(
				'required' => false,
				'name' => tra('File Gallery ID'),
				'description' => tra('Numeric ID of a file gallery. Displays all images from that gallery.'),
				'filter' => 'int',
				'advanced' => true,
				'default' => '',
			),
			'sort_mode' => array(
				'required' => false,
				'name' => tra('Sort Mode'),
				'description' => tra('Sort by database table field name, ascending or descending. Examples: fileId_asc or name_desc.'),
				'filter' => 'word',
				'accepted' => 'fieldname_asc or fieldname_desc with actual table field name in place of \'fieldname\'.',
				'default' => 'created_desc',
				'since' => '8.0',
				'options' => array (
					array('text' => tra(''), 'value' => ''),
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
			'attId' => array(
				'required' => false,
				'name' => tra('Attachment ID'),
				'description' => tra('Numeric ID of an image attached to a wiki page (or list separated by commas or |).'),
				'filter' => 'striptags',
				'default' => '',
			),
			'thumb' => array(
				'required' => false,
				'name' => tra('Thumbnail'),
				'description' => tra('Makes the image a thumbnail that enlarges to full size when clicked or moused over (unless "link" is set to another target). "browse" and "browsepopup" only work with image gallery and "download" only works with file gallery or attachments.'),
				'filter' => 'alpha',
				'default' => '',
				'options' => array(
					array('text' => tra('None'), 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y', 'description' => tra('Full size image appears when thumbnail is clicked.')),
					array('text' => tra('Mouseover'), 'value' => 'mouseover', 'description' => tra('Full size image will pop up while cursor is over the thumbnail (and disappear when not).')), 
					array('text' => tra('Mouseover (Sticky)'), 'value' => 'mousesticky', 'description' => tra('Full size image will pop up once cursor passes over thumbnail and will remain up unless cursor passes over full size popup.')), 
					array('text' => tra('Popup'), 'value' => 'popup', 'description' => tra('Full size image will open in a separate winow or tab (depending on browser settings) when thumbnail is clicked.')), 
					array('text' => tra('Browse'), 'value' => 'browse', 'description' => tra('Image gallery browse window for the image will open when the thumbnail is clicked if the image is in a Tiki image gallery')), 
					array('text' => tra('Browse Popup'), 'value' => 'browsepopup', 'description' => tra('Same as "browse" except that the page opens in a new window or tab.')), 
					array('text' => tra('Download'), 'value' => 'download', 'description' => tra('Download dialog box will appear for file gallery and attachment images when thumbnail is clicked.')),
				),
			),
			'button' => array(
				'required' => false,
				'name' => tra('Enlarge button'),
				'description' => tra('Button for enlarging image.'),
				'filter' => 'alpha',
				'default' => '',
				'options' => array(
					array('text' => tra('None'), 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('Popup'), 'value' => 'popup', 'description' => tra('Full size image will open in a separate winow or tab (depending on browser settings) when thumbnail is clicked.')), 
					array('text' => tra('Browse'), 'value' => 'browse', 'description' => tra('Image gallery browse window for the image will open when the thumbnail is clicked if the image is in a Tiki image gallery')), 
					array('text' => tra('Browse Popup'), 'value' => 'browsepopup', 'description' => tra('Same as "browse" except that the page opens in a new window or tab.')), 
					array('text' => tra('Download'), 'value' => 'download', 'description' => tra('Download dialog box will appear for file gallery and attachment images when thumbnail is clicked.')),
				),
			),
			'link' => array(
				'required' => false,
				'name' => tra('Link'),
				'description' => tra('Enter a url to the address the image should link to. Not needed if thumb parameter is set; overrides thumb setting.'),
				'filter' => 'url',
				'default' => '',
			),
			'rel' => array(
				'required' => false,
				'name' => tra('Link relation'),
				'filter' => 'striptags',
				'description' => tra('Enter "box" for colorbox effect (like shadowbox and lightbox) or appropriate syntax for link relation.'),
				'advanced' => true,
				'default' => '',
			),
			'usemap' => array(
				'required' => false,
				'name' => tra('Image map'),
				'filter' => 'striptags',
				'description' => tra('Name of the image map to use for the image.'),
				'advanced' => true,
				'default' => '',
			),
			'height' => array(
				'required' => false,
				'name' => tra('Image height'),
				'description' => tra('Height in pixels or percent. Syntax: "100" or "100px" means 100 pixels; "50%" means 50 percent.'),
				'filter' => 'striptags',
				'default' => '',
			),
			'width' => array(
				'required' => false,
				'name' => tra('Image width'),
				'description' => tra('Width in pixels or percent. Syntax: "100" or "100px" means 100 pixels; "50%" means 50 percent.'),
				'filter' => 'striptags',
				'default' => '',
			),
			'max' => array(
				'required' => false,
				'name' => tra('Maximum image size'),
				'description' => tra('Maximum height or width in pixels (largest dimension is scaled). Overrides height and width settings.'),
				'filter' => 'int',
				'default' => '',
			),
			'imalign' => array(
				'required' => false,
				'name' => tra('Align image'),
				'description' => tra('Aligns the image itself. If the image is inside a box (because of other settings), use the align parameter to align the box.'),
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
				'name' => tra('Image style'),
				'description' => tra('Enter "border" to place a dark gray border around the image. Otherwise enter CSS styling syntax for other style effects.'),
				'filter' => 'striptags',
				'advanced' => true,
				'default' => '',
			),
			'align' => array(
				'required' => false,
				'name' => tra('Align image block'),
				'description' => tra('Aligns the box containing the image.'),
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
				'name' => tra('Image block style'),
				'filter' => 'striptags',
				'description' => tra('Enter "border" to place a dark gray border frame around the image. Otherwise enter CSS styling syntax for other style effects.'),
				'advanced' => true,
				'default' => '',
			),
			'styledesc' => array(
				'required' => false,
				'name' => tra('Description style'),
				'filter' => 'striptags',
				'description' => tra('Enter "right" or "left" to align text accordingly. Otherwise enter CSS styling syntax for other style effects.'),
				'advanced' => true,
				'default' => '',
			),
			'block' => array(
				'required' => false,
				'name' => tra('Wrapping control'),
				'description' => tra('Control how other items wrap around the image.'),
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
				'filter' => 'striptags',
				'description' => tra('CSS class to apply to the image.'),
				'advanced' => true,
				'default' => '',
			),
			'desc' => array(
				'required' => false,
				'name' => tra('Caption'),
				'filter' => 'text',
				'description' => tra('Image caption. "desc" or "name" or "namedesc" for tiki images, "idesc" or "ititle" for iptc data, otherwise enter your own description.'),
				'default' => '',
			),
			'title' => array(
				'required' => false,
				'name' => tra('Link title'),
				'filter' => 'text',
				'description' => tra('Title text. "desc" or "name" or "namedesc", otherwise enter your own title.'),
				'advanced' => true,
				'default' => '',
			),
			'metadata' => array(
				'required' => false,
				'name' => tra('Metadata'),
				'filter' => 'text',
				'description' => tra('Display the image metadata (IPTC and EXIF information).'),
				'default' => '',
				'advanced' => true,
				'options' => array(
					array('text' => tra('None'), 'value' => ''), 
					array('text' => tra('View'), 'value' => 'view'), 
				),
			),
			'alt' => array(
				'required' => false,
				'name' => tra('Alternate text'),
				'filter' => 'text',
				'description' => tra('Alternate text that displays when image does not load. Set to "Image" by default.'),
				'default' => 'Image',
			),
			'default' => array(
				'required' => false,
				'name' => tra('Default config settings'),
				'description' => tra('Default configuration settings (usually set by admin in the source code or through Plugin Alias).'),
				'advanced' => true,
				'default' => '',
			),
			'mandatory' => array(
				'required' => false,
				'name' => tra('Mandatory admin setting'),
				'description' => tra('Mandatory configuration settings (usually set by admin in the source code or through Plugin Alias).'),
				'advanced' => true,
				'default' => '',
			),
		),
	);
}

 function wikiplugin_img( $data, $params, $offset, $parseOptions='' ) {
	 global $tikidomain, $prefs, $section, $smarty, $tikiroot, $tikilib, $userlib, $user;

	$imgdata = array();
	
	$imgdata['src'] = '';
	$imgdata['id'] = '';
	$imgdata['fileId'] = '';
	$imgdata['randomGalleryId'] = '';
	$imgdata['fgalId'] = '';
	$imgdata['sort_mode'] = '';
	$imgdata['attId'] = '';
	$imgdata['thumb'] = '';
	$imgdata['button'] = '';
	$imgdata['link'] = '';
	$imgdata['rel'] = '';
	$imgdata['usemap'] = '';
	$imgdata['height'] = '';
	$imgdata['width'] = '';
	$imgdata['max'] = '';
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
	$imgdata['default'] = '';
	$imgdata['mandatory'] = '';
	
	/*Admin default and mandatory settings (must be set by changing this fle or using plugin alias). Default will be used if not overridden
	by user. Mandatory will override user settings. Examples below set parameters depending on whether the image is in an article, a module, or 
	whether mobile mode is set, etc.*/
	//Uncomment the following line to set the default parameter. Later items have priority. To override align default, put align parameter first
//	$imgdata['default'] = 'default ? max = 200, align = right, styledesc = text-align: center; section_cms_article ? max= 400, width= , height=';
	// Uncomment the following line to set the default parameter. Force certain max and ignore any specified width or height. Later items have priority
//	$imgdata['mandatory'] = 'section_cms_article ? max = 400; module_* ? max = 150, width= , height=; mode_mobile ? max = 150, width= , height=;';

	$imgdata = array_merge( $imgdata, $params );
//////////////////////////////////////////////////Function for processing default and mandatory parameters//////////////////////////////////////
	//function calls are just below function
	if (!function_exists('apply_default_and_mandatory')) {	
	function apply_default_and_mandatory($imgdata, $default) 
	{
			global $section, $smarty;
			$imgdata[$default] = trim($imgdata[$default]) . ';'; // trim whitespace and ensure at least one semicolon
			$img_conditions_array = explode( ';', $imgdata[$default] ); // conditions separated by semicolons
			if( !empty($img_conditions_array) ) {
				foreach($img_conditions_array as $key => $var) { // for each condition
					if( !empty($var) ) {
						$img_condition = explode( '?', $var ); // condition separated from parameters by question mark
						if( !empty($img_condition) ) {
							$img_condition_name = trim($img_condition[0]);
							if( !empty($img_condition[1]) ) { // if there is at least one parameter
								$img_condition[1] = trim($img_condition[1]) . ',';	// at least one comma
								$img_parameters_array = explode( ',', $img_condition[1] ); // separate multiple parameters
								if( !empty($img_parameters_array) ) {  // if a parameter has been extracted
									foreach($img_parameters_array as $param_key => $param_var) {	// for each parameter
										if( !empty($param_var) ) {	// if a parameter exists
											$img_parameter_array = explode( '=', trim($param_var) ); // separate parameters and values
											if( !empty($img_parameter_array[0]) ) {  // if a parameter with a value has been extracted
	
												$img_condition_status = false;	// initialise condition as not being true
	
												$img_condition_name = strtolower(trim($img_condition_name));
												switch ($img_condition_name) {
													case 'default':
														$img_condition_status = true; // default is always true
														break;
													case 'mode_mobile':
														if( isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'mobile' ) $img_condition_status = true;
														break;
													case 'module_*':
														if( !empty($smarty) ) {
															$image_module_params = $smarty->get_template_vars('module_params');
															if( !empty($image_module_params) ) $img_condition_status = true;
														}
														break;
													case 'section_*':
														if( !empty($section) ) $img_condition_status = true;
														break;
													case 'section_cms_article':
														if( !empty($section) ) {
															if( $section == 'cms' ) {
																if( !empty($smarty) ) {
																	$image_article_type = $smarty->get_template_vars('type');
																	if( !empty($image_article_type) ) {
																		if( strtolower(trim($image_article_type)) == 'article' ) $img_condition_status = true;
																	} // if(!empty($image_article_type))
																} // if(!empty($smarty))
															}
														}
														break;
													case 'section_cms_review':
														if( !empty($section) ) {
															if( $section == 'cms' ) {
																if( !empty($smarty) ) {
																	$image_article_type = $smarty->get_template_vars('type');
																	if( !empty($image_article_type) ) {
																		if( strtolower(trim($image_article_type)) == 'review' ) $img_condition_status = true;
																	} // if(!empty($image_article_type))
																} // if(!empty($smarty))
															}
														}
														break;
													case 'section_cms_event':
														if( !empty($section) ) {
															if( $section == 'cms' ) {
																if( !empty($smarty) ) {
																	$image_article_type = $smarty->get_template_vars('type');
																	if( !empty($image_article_type) ) {
																		if( strtolower(trim($image_article_type)) == 'event' ) $img_condition_status = true;
																	} // if(!empty($image_article_type))
																} // if(!empty($smarty))
															}
														}
														break;
													case 'section_cms_classified':
														if( !empty($section) ) {
															if( $section == 'cms' ) {
																if( !empty($smarty) ) {
																	$image_article_type = $smarty->get_template_vars('type');
																	if( !empty($image_article_type) ) {
																		if( strtolower(trim($image_article_type)) == 'classified' ) $img_condition_status = true;
																	} // if(!empty($image_article_type))
																} // if(!empty($smarty))
															}
														}
														break;
												} // switch ($img_condition_name)
	
												if( $img_condition_status != true ) {
													// if match not found yet, examine more specific conditions
													if( !empty($section) ) {	// if we have a section name
														if( substr($img_condition_name,0,8) == 'section_' ) {
															if( strlen($img_condition_name) > 8 ) {
																$img_condition_part = substr($img_condition,8); // get part after "section_"
																$img_condition_part = strtolower($img_condition_part);
																$img_condition_part = trim(strtr($img_condition_part, '_', ' ')); // replace underscore with spaces
																if( $section == $img_condition_part ) $img_condition_status = true;
															} // if( length($img_condition_name) > 8 )
														} // if( substr($img_condition_name,0,8) == "section_" )
													} // if( !empty($section) )
												}
	
												if( $img_condition_status == true ) {
													// set the parameters to their values
													switch (strtolower(trim($img_parameter_array[0]))) {
														case 'src':
															$imgdata['src'] = trim($img_parameter_array[1]);
														break;
														case 'id':
															$imgdata['id'] = trim($img_parameter_array[1]);
														break;
														case 'fileId':
															$imgdata['fileId'] = trim($img_parameter_array[1]);
														break;
														case 'randomGalleryId':
															$imgdata['randomGalleryId'] = trim($img_parameter_array[1]);
														break;
														case 'fgalId':
															$imgdata['fgalId'] = trim($img_parameter_array[1]);
														break;
														case 'sort_mode':
															$imgdata['sort_mode'] = trim($img_parameter_array[1]);
														break;
														case 'attId':
															$imgdata['attId'] = trim($img_parameter_array[1]);
														break;
														case 'thumb':
															$imgdata['thumb'] = trim($img_parameter_array[1]);
														break;
														case 'button':
															$imgdata['button'] = trim($img_parameter_array[1]);
														break;
														case 'link':
															$imgdata['link'] = trim($img_parameter_array[1]);
														break;
														case 'rel':
															$imgdata['rel'] = trim($img_parameter_array[1]);
														break;
														case 'usemap':
															$imgdata['usemap'] = trim($img_parameter_array[1]);
														break;
														case 'height':
															$imgdata['height'] = trim($img_parameter_array[1]);
														break;
														case 'width':
															$imgdata['width'] = trim($img_parameter_array[1]);
														break;
														case 'max':
															$imgdata['max'] = trim($img_parameter_array[1]);
														break;
														case 'imalign':
															$imgdata['imalign'] = trim($img_parameter_array[1]);
														break;
														case 'styleimage':
															$imgdata['styleimage'] = trim($img_parameter_array[1]);
														break;
														case 'align':
															$imgdata['align'] = trim($img_parameter_array[1]);
														break;
														case 'stylebox':
															$imgdata['stylebox'] = trim($img_parameter_array[1]);
														break;
														case 'styledesc':
															$imgdata['styledesc'] = trim($img_parameter_array[1]);
														break;
														case 'block':
															$imgdata['block'] = trim($img_parameter_array[1]);
														break;
														case 'class':
															$imgdata['class'] = trim($img_parameter_array[1]);
														break;
														case 'desc':
															$imgdata['desc'] = trim($img_parameter_array[1]);
														break;
														case 'title':
															$imgdata['title'] = trim($img_parameter_array[1]);
														break;
														case 'metadata':
															$imgdata['metadata'] = trim($img_parameter_array[1]);
														break;
														case 'alt':
															$imgdata['alt'] = trim($img_parameter_array[1]);
														break;
													} // switch ($img_parameter_array[0])
												} // if( $img_condition_status == true )
	
											} // if( !empty($img_parameter_array[0] )
										} // if a parameter exists
									} // for each parameter
								} // if( !empty($img_parameters_array) )
							} // if( !empty($img_condition[1]) )
						}  // if( !empty($img_condition) )
					} // if( !empty($var) )
				} // for each condition
			} // if( !empty($img_conditions_array) )
	return $imgdata;
	}
}
////////////////////////////////////End of function for processing default and mandatory parameters////////////////////
	//function calls
	if( !empty($imgdata['default']) || !empty($imgdata['mandatory'])) {
		if(!empty($imgdata['default'])) { 
			$imgdata = apply_default_and_mandatory($imgdata, 'default');	//first process defaults
			$imgdata = array_merge( $imgdata, $params );					//then apply user settings, overriding defaults
		}
		//apply mandatory settings, overriding user settings
		if(!empty($imgdata['mandatory'])) $imgdata = apply_default_and_mandatory($imgdata, 'mandatory');
	}

//////////////////////////////////////////////////// Error messages and clean javascript //////////////////////////////
	// Must set at least one image identifier
	$set = !empty($imgdata['fileId']) + !empty($imgdata['id']) + !empty($imgdata['src']) + !empty($imgdata['attId']) 
		+ !empty($imgdata['randomGalleryId']) + !empty($imgdata['fgalId']);
	if ($set == 0) {
		return tra("''No image specified. One of the following parameters must be set: fileId, randomGalleryId, fgalId, attId, id.''");
	} elseif ($set >1) {
		return tra("''Use one and only one of the following parameters: fileId, randomGalleryId, fgalId, attId, id, or src.''");
	}
	// Clean up src URLs to exclude javascript
	if (stristr(str_replace(' ', '', $imgdata['src']),'javascript:')) {
		$imgdata['src']  = '';
	}
	if (strstr($imgdata['src'],'javascript:')) {
		$imgdata['src']  = '';
	}
	
 	if (!isset($data) or !$data) {
		$data = '&nbsp;';
	}

	include_once('tiki-sefurl.php');
	//////////////////////Process multiple images //////////////////////////////////////
	//Determine source first
	$id = array();
	if (!empty($imgdata['fileId'])) {
		$id['type'] = 'fileId';
		$id['id'] = $imgdata['fileId'];
	} elseif (!empty($imgdata['id'])) {
		$id['type'] = 'id';
		$id['id'] = $imgdata['id'];
	} elseif (!empty($imgdata['attId'])) {
		$id['type'] = 'attId';
		$id['id'] = $imgdata['attId'];
	} else {
		$id['type'] = 'src';
		$id['id'] = '';
	}		
	//Process "|" or "," separated images
	$notice = '<!--' . tra('PluginImg: User lacks permission to view image') . '-->';
	$srcmash = $imgdata['fileId'] . $imgdata['id'] . $imgdata['attId'] . $imgdata['src'];
	if (( strpos($srcmash, '|') !== false ) || (strpos($srcmash, ',') !== false ) || !empty($imgdata['fgalId']))  {
		$separator = '';
		$id = '';
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
		} elseif ( strpos($imgdata[$id], ',') !== false )  {
			$separator = ',';
		}
		$repl = '';
		$id_list = array();
		if (!empty($separator)) {
			$id_list = explode($separator,$imgdata[$id]);
		} else {
			$filegallib = TikiLib::lib('filegal');
			$galdata = $filegallib->get_files(0, -1, 'created_desc', '', $imgdata['fgalId'], false, false, false, true, false, false, false, false, '', true, false, false);
			foreach($galdata as $filedata) {
				foreach($filedata as $dbinfo) {
					$id_list[] = $dbinfo['id'];
				}
			}
			$id = 'fileId';
		}
		$params[$id] = '';
		foreach ($id_list as $i => $value) {
			$params[$id] = trim($value);
			$params['fgalId'] = '';
			$repl .= wikiplugin_img( $data, $params, $offset, $parseOptions );
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
	$absolute_links = (!empty($parseOptions['absolute_links'])) ? $parseOptions['absolute_links'] : false;
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
			$src = $filegalpath . $imgdata['fileId']; 
		} else {					//only attachments left
			$src = $attachpath . $imgdata['attId']; 
		}
	} elseif ( (!empty($imgdata['src'])) && $absolute_links && ! preg_match('|^[a-zA-Z]+:\/\/|', $imgdata['src']) ) {
		global $base_host, $url_path;
		$src = $base_host.( $imgdata['src'][0] == '/' ? '' : $url_path ) . $imgdata['src'];
	} elseif (!empty($imgdata['src']) && $tikidomain && !preg_match('|^https?:|', $imgdata['src'])) {
		$src = preg_replace("~img/wiki_up/~","img/wiki_up/$tikidomain/",$imgdata['src']);
	} elseif (!empty($imgdata['src'])) {
		$src = $imgdata['src'];
	}
	
	$browse_full_image = $src; 

	///////////////////////////Get DB info for image size and metadata/////////////////////////////
	if (!empty($imgdata['height']) || !empty($imgdata['width']) || !empty($imgdata['max']) 
		|| !empty($imgdata['desc']) || strpos($imgdata['rel'], 'box') !== false 
		|| !empty($imgdata['stylebox']) || !empty($imgdata['styledesc']) || !empty($imgdata['button']) 
		|| !empty($imgdata['thumb'])  || !empty($imgdata['align']) || !empty($imgdata['metadata'])  || !empty($imgdata['fileId'])
	) {
		//Get ID numbers for images in galleries and attachments included in src as url parameter
		//So we can get db info for these too
		global $base_host;
		//don't pick up links to other tiki sites
		if (strpos($imgdata['src'], 'http') === false || (strpos($imgdata['src'], 'http') === 0 && strpos($imgdata['src'], $base_host) !== false)) {
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
				global $imagegallib; 
				include_once('lib/imagegals/imagegallib.php');
				$dbinfo = $imagegallib->get_image_info($imgdata['id'], 'o');
				$dbinfo2 = $imagegallib->get_image($imgdata['id'], 'o');
				$dbinfo = array_merge($dbinfo, $dbinfo2);
				$dbinfot = $imagegallib->get_image_info($imgdata['id'], 't');
				$dbinfot2 = $imagegallib->get_image($imgdata['id'], 't');
				$dbinfot = array_merge($dbinfot, $dbinfot2);
				$basepath = $prefs['gal_use_dir'];
			} elseif (!isset($dbinfo) && !empty($imgdata['fileId'])) {
				$filegallib = TikiLib::lib('filegal');
				$dbinfo = $filegallib->get_file($imgdata['fileId']);
				$basepath = $prefs['fgal_use_dir'];
			} else {					//only attachments left
				global $atts;
				global $wikilib;
				include_once('lib/wiki/wikilib.php');
				$dbinfo = $wikilib->get_item_attachment($imgdata['attId']);
				$basepath = $prefs['w_use_dir'];
			}		
			//Give error messages if file doesn't exist, isn't an image. Display nothing if user lacks permission
			if (!empty($imgdata['fileId']) || !empty($imgdata['id']) || !empty($imgdata['attId'])) {
				if( ! $dbinfo ) {
					return '^' . tra('File not found.') . '^';
				} elseif( substr($dbinfo['filetype'], 0, 5) != 'image' AND !preg_match('/thumbnail/i', $imgdata['fileId'])) {
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
			$imageObj->set_img_info($imageObj->data, false);
			if (isset($imageObj->exif['FILE']['FileName'])) {
				$imageObj->exif['FILE']['FileName'] = $dbinfo['filename'];
			}
		} elseif (!empty($dbinfo['path'])) {
			$imageObj = new Image($basepath . $dbinfo['path'], true);	
			$imageObj->set_img_info($basepath . $dbinfo['path'], true);
			if (isset($imageObj->exif['FILE']['FileName'])) {
				$imageObj->exif['FILE']['FileName'] = $dbinfo['filename'];
			}
		} else {
			$imageObj = new Image($src, true);
			$imageObj->set_img_info($src, true);
		}
		if (isset($imageObj->exif['FILE']['FileDateTime'])) {
			$imageObj->exif['FILE']['FileDateTime'] = $tikilib->get_long_datetime($imageObj->exif['FILE']['FileDateTime'], $user) .
				' (Unixtime: ' . $imageObj->exif['FILE']['FileDateTime'] . ')';
		}
		//if we need iptc data
		if ($imgdata['desc'] == 'idesc' || $imgdata['desc'] == 'ititle' || isset($imgdata['metadata'])) {
			$iptc = $imageObj->get_iptc($imageObj->otherinfo);
			//description from image iptc
			$idesc = isset($iptc['2#120'][0]) ? $iptc['2#120'][0] : '';	
			//title from image iptc	
			$ititle = isset($iptc['2#005'][0]) ? $iptc['2#005'][0] : '';
		}
		$fwidth = '';
		$fheight = '';
		if (isset($parseOptions['indexing']) && $parseOptions['indexing']) {
			$fwidth = 1;
			$fheight = 1;
		} else {
			$fwidth = $imageObj->width;
			$fheight = $imageObj->height;
		}
		$width = $fwidth;
		$height = $fheight;
		//get image gal thumbnail image for height and width
		if (!empty($dbinfot['data']) || !empty($dbinfot['path'])) {
			if (!empty($dbinfot['data'])) {
				$imageObjt = new Image($dbinfot['data'], false);
				$imageObjt->set_img_info($imageObjt->data, false);
			} elseif (!empty($dbinfot['path'])) {
				$imageObjt = new Image($basepath . $dbinfot['path'] . '.thumb', true);	
				$imageObjt->set_img_info($basepath . $dbinfot['path'] . '.thumb', true);
			}
			$fwidtht = $imageObjt->width;
			$fheightt = $imageObjt->height;
		}
	/////////////////////////////////////Add image dimensions to src string////////////////////////////////////////////
		//Use url resizing parameters for file gallery images to set $height and $width
		//since they can affect other elements; overrides plugin parameters
		if (!empty($imgdata['fileId']) && strpos($src, '&') !== false)  {
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
		if (!empty($imgdata['id']))  {
			preg_match('/(?<=\&thumb=1)[0-9]+(?=.*)/', $src, $urlimthumb);
			if (!empty($urlimthumb[0]) && $urlimthumb[0] > 0) $imgalthumb = true;
		}
			
		//Now set dimensions based on plugin parameter settings
		if (!empty($imgdata['max']) || !empty($imgdata['height']) || !empty($imgdata['width']) 
			|| !empty($imgdata['thumb'])
		) {
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
			} elseif (!empty($imgdata['height']) )  {
				//use image gal thumbs when possible
				if ((!empty($imgdata['id']) && $imgalthumb == false) 
					&& ($imgdata['height'] < $fheightt)
				) {
					$src .= '&thumb=1';
					$imgalthumb == true;
				}
				$height = $imgdata['height'];
				if (empty($imgdata['width']) && $fheight > 0) {
					$width = floor($height * $fwidth / $fheight);
				} else {
					$width = $imgdata['width'];
				}
			} elseif (!empty($imgdata['width']))  {
				//use image gal thumbs when possible
				if ((!empty($imgdata['id']) && $imgalthumb == false) 
					&& ($imgdata['width'] < $fwidtht)
				) {
					$src .= '&thumb=1';
					$imgalthumb == true;
				}
				$width =  $imgdata['width'];
				if (empty($imgdata['height']) && $fwidth > 0) {
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
				if (!empty($imgdata['id'])) {
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
				$src .= '&display';
			}
			if (!empty($scale) && empty($urlscale[0])) {
				$src .= '&scale=' . $scale;
			} elseif ((!empty($imgdata['max']) && $imgdata['thumb'] != 'download') 
					&& (empty($urlthumb) && empty($urlmax[0]) && empty($urlprev))
			) {
				$src .= '&max=' . $imgdata['max'];
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
				} elseif (!empty($heigth) && (empty($urly[0]) && empty($urlthumb) && empty($urlscale[0]))) {
					$src .= '&y=' . $height;
					$imgdata_dim = '';
					$width = $fwidth;
				}			
			} else {
				$imgdata_dim = '';
				$height = $fheight;
				$width = $fwidth;
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
			} else {
				$imgdata_dim = '';
				$width = $fwidth;
			}
		}
	}
		
	////////////////////////////////////////// Create the HTML img tag //////////////////////////////////////////////
	//Start tag with src and dimensions
	$src = filter_out_sefurl(htmlentities($src), $smarty);
	$replimg = '<img src="' . $src . '"';
	if (!empty($imgdata_dim)) $replimg .= $imgdata_dim;
	
	//Create style attribute allowing for shortcut inputs 
	//First set alignment string
	$center = 'display:block; margin-left:auto; margin-right:auto;';	//used to center image and box
	if (!empty($imgdata['imalign'])) {
		$imalign = '';
		if ($imgdata['imalign'] == 'center') {
			$imalign = $center;
		} else {
			$imalign = 'float:' . $imgdata['imalign'] . ';';
		}
	} elseif ($imgdata['stylebox'] == 'border') {
		$imalign = $center;
	}
	//set entire style string
	if( !empty($imgdata['styleimage']) || !empty($imalign) ) {
		$border = '';
		$style = '';
		$borderdef = 'border:1px solid darkgray;';   //default border when styleimage set to border
		if ( !empty($imgdata['styleimage'])) {
			if (!empty($imalign)) {
				if ((strpos(trim($imgdata['styleimage'],' '),'float:') !== false) 
					|| (strpos(trim($imgdata['styleimage'],' '),'display:') !== false)
				) {
					$imalign = '';			//override imalign setting if style image contains alignment syntax
				}
			}
			if ($imgdata['styleimage'] == 'border') {
				$border = $borderdef;
			} else if (strpos($imgdata['styleimage'],'hidden') === false 
				&& strpos($imgdata['styleimage'],'position') === false
			) {	// quick filter for dangerous styles
				$style = $imgdata['styleimage'];
			}
		}
		$replimg .= ' style="' . $imalign . $border . $style . '"';
	}
	//alt
	if( !empty($imgdata['alt']) ) {
		$replimg .= ' alt="' . $imgdata['alt'] . '"';
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
		$desc = '';
		$imgname = '';
		$desconly = '';
		if ( !empty($imgdata['desc']) ) {
			//attachment database uses comment instead of description or name
			if (!empty($dbinfo['comment'])) {
				$desc = $dbinfo['comment'];
				$imgname = $dbinfo['comment'];
			} elseif (isset($dbinfo)) {
				$desc = $dbinfo['description'];
				$imgname = $dbinfo['name'];
			}
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
	
	$replimg .= ' />' . "\r";

	////////////////////////////////////////// Create the HTML link ///////////////////////////////////////////
	//Variable for identifying if javascript mouseover is set
	if (($imgdata['thumb'] == 'mouseover') || ($imgdata['thumb'] == 'mousesticky')) {
		$javaset = 'true';
	} else {
		$javaset = '';
	}
	// Set link to user setting or to image itself if thumb is set
	if (!empty($imgdata['link']) || !empty($imgdata['thumb'])) {
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
			require_once $smarty->_get_plugin_filepath('function', 'popup');
			$mouseover = ' ' . smarty_function_popup($popup_params, $smarty);
		} else {
			if (!empty($imgdata['fileId']) && $imgdata['thumb'] != 'download' && empty($urldisp)) {
				$link = $browse_full_image . '&display';
			} else {
				$link = $browse_full_image;
			}
		}
		// Set other link-related attributes				
		// target
		$imgtarget= '';
		if (($prefs['popupLinks'] == 'y' && (preg_match('#^([a-z0-9]+?)://#i', $link) 
			|| preg_match('#^www\.([a-z0-9\-]+)\.#i',$link))) || ($imgdata['thumb'] == 'popup') 
			|| ($imgdata['thumb'] == 'browsepopup')
		) {
			if (!empty($javaset) || ($imgdata['rel'] == 'box')) {
				$imgtarget= '';
			} else {
				$imgtarget = ' target="_blank"';
			}
		}
		// rel
		!empty($imgdata['rel']) ? $linkrel = ' rel="'.$imgdata['rel'].'"' : $linkrel = '';
		// title
		!empty($imgtitle) ? $linktitle = $imgtitle : $linktitle = '';
		
		$link = filter_out_sefurl(htmlentities($link), $smarty);

		//Final link string
		$replimg = "\r\t" . '<a href="' . $link . '" class="internal"' . $linkrel . $imgtarget . $linktitle 
					. $mouseover . '>' ."\r\t\t" . $replimg . "\r\t" . '</a>';
	}
	
	//Add link string to rest of string
	$repl .= $replimg;

//////////////////////////Generate metadata dialog box and jquery (dialog icon added in next section)////////////////////////////////////
	if ($imgdata['metadata'] == 'view') {
		//create unique id's in case of multiple pictures
		static $lastval = 0;
		$id = 'imgdialog-' . ++$lastval;
		$id_link = $id . '-link';
		//start the dialog box
		$dialog = "\r" . '<div id="' . $id . '" title="Image Metadata for ' . htmlspecialchars($dbinfo['filename']) . '" style="display:none">';
		//iptc section
		$dialog .= "\r\t" . '<h3><a href="#">Photographer Data (IPTC)</a></h3>';
		if ($iptc == null) {
			$dialog .= "\r\t" . '<div>' . tra('No IPTC data') . '</div>';
		} else {
			$dialog .= "\r\t" . '<table>';
			foreach (array_keys($iptc) as $key => $s) {
				$dialog .= "\r\t\t" . '<tr>' . "\r\t\t\t" . '<td>' . '<div style="text-align:right; font-weight:bold; width:175px; margin-right:5px">'
					. $iptc[$s][1] . '</div>' . '</td>' . "\r\t\t\t" . '<td>' . '<div style="width:425px">' . htmlspecialchars($iptc[$s][0]) . '</div>' . '</td>' . "\r\t\t" . '</tr>';
			}
			$dialog .= "\r\t" . '</table>'; 
		}
		//exif section
		$dialog .= "\r\t" . '<h3><a href="#">File Data (EXIF)</a></h3>';
		if ($imageObj->exif === false) {
			$dialog .= "\r\t" . '<div>' . tra('No EXIF data') . '</div>';
		} else {
			$dialog .= "\r\t" . '<table>';
			foreach ($imageObj->exif as $cat => $fields) {
				foreach ($fields as $name => $val) {
					$dialog .= "\r\t\t" . '<tr>' . "\r\t\t\t" . '<td>' . '<div style="text-align:right; font-weight:bold; width:175px; margin-right:5px">'
					. $name . '</div>' . '</td>' . "\r\t\t\t" . '<td>' . '<div style="width:425px">' . htmlspecialchars($val) . '</div>' . '</td>' . "\r\t\t" . '</tr>';
				}
			}
			$dialog .= "\r\t" . '</table>'; 
		}
		$dialog .= "\r" . '</div>';
		$repl .= $dialog;
		$jq = '$(document).ready(function() {
					$("#' . $id . '").dialog({
							autoOpen: false,
							width: 700
					});				
						
					$("#' . $id_link . '").click(function() {
							$("#' . $id . '").accordion({
								autoHeight: false,
								collapsible: true
							}).dialog(\'open\');
							return false;
					});
				});';
		global $headerlib;
		$headerlib->add_jq_onready($jq);
	}
	//////////////////////  Create enlarge button, metadata icon, description and their divs////////////////////
	//Start div that goes around button and description if these are set
	if ((!empty($imgdata['button'])) || (!empty($imgdata['desc'])) || (!empty($imgdata['styledesc']))) {
		//To set room for enlarge button under image if there is no description
		$descheightdef = 'height:17px;clear:left;';						
		$repl .= "\r\t" . '<div class="mini" style="width:' . $width . 'px;';
		if( !empty($imgdata['styledesc']) ) {
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
				if ((($imgdata['button'] == 'browse') || ($imgdata['button'] == 'browsepopup')) && !empty($imgdata['id']))  {
					$link_button = 'tiki-browse_image.php?imageId=' . $imgdata['id'];
				} else {
					if (!empty($imgdata['fileId']) && $imgdata['button'] != 'download') {
						$link_button = $browse_full_image . '&display';
					} elseif (!empty($imgdata['attId']) && $imgdata['thumb'] == 'download'){
						$link = $browse_full_image . '&download=y';
					} else {
						$link_button = $browse_full_image;
					}
				}
			} else {
				$link_button = $link;
			}
			//Set button rel
			!empty($imgdata['rel']) ? $linkrel_button = ' rel="'.$imgdata['rel'].'"' : $linkrel_button = '';
/*			if (empty($linkrel) || !empty($javaset)) {
					$linkrel_button = '';
			} else {
				$linkrel_button = $linkrel;
			}*/
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
			$repl .= ">\r\t\t\t\t" . '<img class="magnify" src="./pics/icons/magnifier.png" alt="'.tra('Enlarge').'" /></a>' . "\r\t\t\t</div>";
		}
		//Add metadata icon
		if ($imgdata['metadata'] == 'view') {
			$repl .= '<div style="float:right; margin-right:2px"><a href="#" id="' . $id_link . '"><img src="./pics/icons/tag_blue.png" alt="' . tra('Metadata') . '" title="' . tra('Metadata') . '"/></a></div>';
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
						if ((strpos(trim($imgdata['stylebox'],' '),'float:') !== false) 
							|| (strpos(trim($imgdata['stylebox'],' '),'display:') !== false)
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
				if ((strpos(trim($imgdata['stylebox'],' '),'height:') === false) 
					&& (strpos(trim($imgdata['stylebox'],' '),'width:') === false)
				) {
					$styleboxplus = $styleboxinit . ' width:' . $boxwidth . 'px;';
				} else {
					$styleboxplus = $styleboxinit;
				}
			} else {
				$styleboxplus = $alignbox . ' width:' . $boxwidth . 'px;';
			}
		} elseif (!empty($imgdata['button']) || !empty($imgdata['desc'])) {
		$styleboxplus = ' width:' . $boxwidth . 'px;';
		}
	}
	if ( !empty($styleboxplus)) {
		$repl = "\r" . '<div ' . $class . 'style="' . $styleboxplus . '">' . $repl . "\r" . '</div>';
	}
//////////////////////////////////////Place 'clear' block///////////////////////////////////////////////////////////
	if( !empty($imgdata['block']) ) {
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
		case 'top': 
			break;
		} 
	} 
	// Mobile
	if(isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'mobile') {
		$repl = '{img src=' . $src . "\"}\n<p>" . $imgdata['desc'] . '</p>'; 
	}
	return '~np~' . $repl. "\r" . '~/np~';
 }