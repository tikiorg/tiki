<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_img_info() {
	return array(
		'name' => tra('Image'),
		'description' => tra('Display images'),
		'documentation' => 'PluginImg',
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
			),
			'id' => array(
				'required' => false,
				'name' => tra('Image ID'),
				'description' => tra('Numeric ID of an image in an Image Gallery (or list separated by commas or |).'),
				'filter' => 'striptags',
			),
			'fileId' => array(
				'required' => false,
				'name' => tra('File ID'),
				'type' => 'image',
				'area' => 'fgal_picker_id',
				'description' => tra('Numeric ID of an image in a File Gallery (or list separated by commas or |).'),
				'filter' => 'striptags',
			),
			'randomGalleryId' => array(
				'required' => false,
				'name' => tra('Gallery ID'),
				'description' => tra('Numeric ID of a File Gallery. Displays a random image from that gallery.'),
				'filter' => 'int',
				'advanced' => true,
			),
			'attId' => array(
				'required' => false,
				'name' => tra('Attachment ID'),
				'description' => tra('Numeric ID of an image attached to a wiki page (or list separated by commas or |).'),
				'filter' => 'striptags',
			),
			'thumb' => array(
				'required' => false,
				'name' => tra('Thumbnail'),
				'description' => tra('Makes the image a thumbnail that enlarges to full size when clicked or moused over (unless "link" is set to another target). "browse" and "browsepopup" only work with image gallery and "download" only works with file gallery or attachments.'),
				'filter' => 'alpha',
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
			),
			'rel' => array(
				'required' => false,
				'name' => tra('Link relation'),
				'filter' => 'striptags',
				'description' => tra('Enter "box" for colorbox effect (like shadowbox and lightbox) or appropriate syntax for link relation.'),
				'advanced' => true,
			),
			'usemap' => array(
				'required' => false,
				'name' => tra('Image map'),
				'filter' => 'striptags',
				'description' => tra('Name of the image map to use for the image.'),
				'advanced' => true,
			),
			'height' => array(
				'required' => false,
				'name' => tra('Image height'),
				'description' => tra('Height in pixels or percent. Syntax: "100" or "100px" means 100 pixels; "50%" means 50 percent.'),
				'filter' => 'striptags',
			),
			'width' => array(
				'required' => false,
				'name' => tra('Image width'),
				'description' => tra('Width in pixels or percent. Syntax: "100" or "100px" means 100 pixels; "50%" means 50 percent.'),
				'filter' => 'striptags',
			),
			'max' => array(
				'required' => false,
				'name' => tra('Maximum image size'),
				'description' => tra('Maximum height or width in pixels (largest dimension is scaled). Overrides height and width settings.'),
				'filter' => 'int',
			),
			'imalign' => array(
				'required' => false,
				'name' => tra('Align image'),
				'description' => tra('Aligns the image itself. If the image is inside a box (because of other settings), use the align parameter to align the box.'),
				'filter' => 'alpha',
				'advanced' => true,
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
			),
			'align' => array(
				'required' => false,
				'name' => tra('Align image block'),
				'description' => tra('Aligns the box containing the image.'),
				'filter' => 'alpha',
				'advanced' => true,
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
			),
			'styledesc' => array(
				'required' => false,
				'name' => tra('Description style'),
				'filter' => 'striptags',
				'description' => tra('Enter "right" or "left" to align text accordingly. Otherwise enter CSS styling syntax for other style effects.'),
				'advanced' => true,
			),
			'block' => array(
				'required' => false,
				'name' => tra('Wrapping control'),
				'description' => tra('Control how other items wrap around the image.'),
				'filter' => 'alpha',
				'advanced' => true,
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
			),
			'desc' => array(
				'required' => false,
				'name' => tra('Caption'),
				'filter' => 'text',
				'description' => tra('Image caption. "desc" or "name" or "namedesc" for tiki images, "idesc" or "ititle" for iptc data, otherwise enter your own description.'),
			),
			'title' => array(
				'required' => false,
				'name' => tra('Link title'),
				'filter' => 'text',
				'description' => tra('Title text. "desc" or "name" or "namedesc", otherwise enter your own title.'),
				'advanced' => true,
			),
			'alt' => array(
				'required' => false,
				'name' => tra('Alternate text'),
				'filter' => 'text',
				'description' => tra('Alternate text that displays when image does not load. Set to "Image" by default.'),
			),
			'default' => array(
				'required' => false,
				'name' => tra('Default config settings'),
				'description' => tra('Default configuration settings (usually set by admin in the source code or through Plugin Alias).'),
				'advanced' => true,
			),
			'mandatory' => array(
				'required' => false,
				'name' => tra('Mandatory admin setting'),
				'description' => tra('Mandatory configuration settings (usually set by admin in the source code or through Plugin Alias).'),
				'advanced' => true,
			),
		),
	);
}

 function wikiplugin_img( $data, $params, $offset, $parseOptions='' ) {
	 global $tikidomain, $prefs, $section, $smarty, $tikiroot;

	$imgdata = array();
	
	$imgdata['src'] = '';
	$imgdata['id'] = '';
	$imgdata['fileId'] = '';
	$imgdata['randomGalleryId'] = '';
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
			$imgdata = apply_default_and_mandatory($imgdata, 'default');   //first process defaults
			$imgdata = array_merge( $imgdata, $params );    //apply user settings, overriding defaults
		}
		//apply mandatory settings, overriding user settings
		if(!empty($imgdata['mandatory'])) $imgdata = apply_default_and_mandatory($imgdata, 'mandatory');   
	}		

//////////////////////////////////////////////////// Error messages and clean javascript //////////////////////////////
	// Must set at least one image identifier
	if ( empty($imgdata['fileId']) and empty($imgdata['id']) and empty($imgdata['src']) and empty($imgdata['attId']) and empty($imgdata['randomGalleryId']) ) {
		return tra("''No image specified. Either the fileId, randomGalleryId, attId, id, or src parameter must be specified.''");
	}
	// Can't set more than one image identifier
	if ( ! ( !empty($imgdata['fileId']) Xor !empty($imgdata['id']) Xor !empty($imgdata['src']) Xor !empty($imgdata['attId']) Xor !empty($imgdata['randomGalleryId'])) ) {
		return tra("''Use one and only one of the following parameters: fileId, randomGalleryId, attId, id, or src.''");
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
	//Process "|" or "," separated images
	$srcmash = $imgdata['fileId'] . $imgdata['id'] . $imgdata['attId'] . $imgdata['src'];
	if (( strpos($srcmash, '|') !== false ) || (strpos($srcmash, ',') !== false ))  {
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
		$separator = '';
		if ( strpos($imgdata[$id], '|') !== false ) {
			$separator = '|';
		} elseif ( strpos($imgdata[$id], ',') !== false )  {
			$separator = ',';
		}
		$repl = '';
		$id_list = array();
		$id_list = explode($separator,$imgdata[$id]);
		$params[$id] = '';
		foreach ($id_list as $i => $value) {
			$params[$id] = trim($value);
			$repl .= wikiplugin_img( $data, $params, $offset, $parseOptions );
		}
		$repl = "\n\r" . '<br style="clear:both" />' . "\r" . $repl . "\n\r" . '<br style="clear:both" />' . "\r";
		return $repl; // return the multiple images
	}
	
	//////////////////////Set src for html///////////////////////////////
	//Set variables for the base path for images in file galleries, image galleries and attachments
	$imagegalpath = 'show_image.php?id=';
	$filegalpath = 'tiki-download_file.php?fileId=';
	$attachpath = 'tiki-download_wiki_attachment.php?attId=';
	
	$repl = '';
	$absolute_links = (!empty($parseOptions['absolute_links'])) ? $parseOptions['absolute_links'] : false;
	//get random image and treat as file gallery image afterwards
	if (!empty($imgdata['randomGalleryId'])) {
		include_once('lib/tikilib.php');
		$tikilib = new TikiLib();
		$dbinfo = $tikilib->get_file(0, $imgdata['randomGalleryId']);
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

	///////////////////////////Get DB info for image size and iptc data/////////////////////////////
	if (!empty($imgdata['height']) || !empty($imgdata['width']) || !empty($imgdata['max']) 
		|| !empty($imgdata['desc']) || strpos($imgdata['rel'], 'box') !== false 
		|| !empty($imgdata['stylebox']) || !empty($imgdata['styledesc']) || !empty($imgdata['button']) 
		|| !empty($imgdata['thumb'])  || !empty($imgdata['align'])
	) {
		//Get ID numbers for images in galleries and attachments included in src parameter as url
		//So we can get db info for these too
		if (strlen(strstr($imgdata['src'], $imagegalpath)) > 0) {                                     
			$imgdata['id'] = substr(strstr($imgdata['src'], $imagegalpath), strlen($imagegalpath));   
		} elseif (strlen(strstr($imgdata['src'], $filegalpath)) > 0) {                                
			$imgdata['fileId'] = substr(strstr($imgdata['src'], $filegalpath), strlen($filegalpath)); 	
		} elseif (strlen(strstr($imgdata['src'], $attachpath)) > 0) {                                 
			$imgdata['attId'] = substr(strstr($imgdata['src'], $attachpath), strlen($attachpath));   
		}
		
		//Deal with images in tiki databases (file and image galleries and attachments)
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
				global $filegallib; 
				include_once('lib/filegals/filegallib.php');
				$dbinfo = $filegallib->get_file($imgdata['fileId']);
				$basepath = $prefs['fgal_use_dir'];
			} else {					//only attachments left
				global $atts;
				global $wikilib;
				include_once('lib/wiki/wikilib.php');
				$dbinfo = $wikilib->get_item_attachment($imgdata['attId']);
				$basepath = $prefs['w_use_dir'];
			}		
			//Give error messages if it doesn't exist or isn't an image
			if (!empty($imgdata['id']) || !empty($imgdata['fileId']) || !empty($imgdata['attId']) || !empty($imgdata['randomGalleryId'])) {
				if( ! $dbinfo ) {
					return '^' . tra('File not found.') . '^';
				} elseif( substr($dbinfo['filetype'], 0, 5) != 'image' AND !preg_match('/thumbnail/i', $imgdata['fileId'])) {
					return '^' . tra('File is not an image.') . '^';
				} else {
				require_once('lib/images/images.php');
					if (!class_exists('Image')) {
					return '^' . tra('Server does not support image manipulation.') . '^';
					}
				}	
			}
		} //finished getting info from db for images in image or file galleries or attachments
		
		//get image to get height and width and iptc data
		$imageObj = '';
		require_once('lib/images/images.php');
		global $imagesize, $iptc, $otherinfo, $imagesizet;

		//if we need iptc data
		if ($imgdata['desc'] == 'idesc' || $imgdata['desc'] == 'ititle') {
			$imagesize = '';
			$iptc = '';
			$ititle = '';
			$idesc = '';
			$otherinfo = array();
			if (!empty($dbinfo['data'])) {
				getimagesize_raw($dbinfo['data'], false);  //images in databases, calls function in this program
			} else {
				if (!empty($dbinfo['path'])) {
					$imagesize = getimagesize(($basepath . $dbinfo['path']), $otherinfo);  //images in tiki directories
				} else {
					$imagesize = getimagesize($src, $otherinfo);  //wiki_up and external images
				}
				if (isset($otherinfo['APP13'])) { 
					$iptc = iptcparse($otherinfo['APP13']); 
				}
			}
		//if we only need height and width
		} else {
			if (!empty($dbinfo['data'])) {
				$imageObj = new Image($dbinfo['data'], false);
			} elseif (!empty($dbinfo['path'])) {
				$imageObj = new Image($basepath . $dbinfo['path'], true);	
			} elseif (strpos($src,'http://') !== false) {
				//Image class doesn't seem to work well for external images - no height or width
				$imagesize = getimagesize($src);
			} else {
				$imageObj = new Image($src, true);
			}
		}
		//Set the variables for height, width and iptc data
		$fwidth = '';
		$fheight = '';
		if (is_object($imageObj)) {
			//set to null first because Image class will place exif_thumbnail dimensions here if thumbnail exists
			$imageObj->height = NULL;
			$imageObj->width = NULL;
			$fwidth = $imageObj->get_width();
			$fheight = $imageObj->get_height();
		} else {  
			$fwidth = $imagesize[0];
			$fheight = $imagesize[1];
			//description from image iptc
			$idesc = isset($iptc['2#120'][0]) ? trim($iptc['2#120'][0]) : '';	
			//title from image iptc	
			$ititle = isset($iptc['2#005'][0]) ? trim($iptc['2#005'][0]) : '';
		}		
		
		//get image gal thumbnail image for height and width
		if (!empty($dbinfot['data']) || !empty($dbinfot['path'])) {
			$fwidtht = '';
			$fheightt = '';
			$imagesizet = '';
			if (!empty($dbinfot['data'])) {
				$imageObjt = new Image($dbinfot['data'], false);
			} else {
				$imageObjt = new Image($basepath . $dbinfot['path'] . '.thumb', true);
			}
			//height and width for image gal thumbs
			$fwidtht = $imageObjt->get_width();
			$fheightt = $imageObjt->get_height();
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
				if (empty($imgdata['width'])) {
					$width = floor($height * $fwidth / $fheight);
				} else {
					$width = $imgdata['width'];
				}
			} elseif (!empty($imgdata['width']))  {
				//use image gal thumbs when possible
				if ((!empty($imgdata['id']) && $imgalthumb == false) 
					&& ($imgdata['width'] < $widtht)
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
					$thumbdef = 120;	// filegals thumbnails size is hard-coded in lib/images/abstract.php
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
	$replimg = "\r\t" . '<img src="' . $src . '"';
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
	
	$replimg .= ' />';

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
		$replimg = '<a href="' . $link . '" class="internal"' . $linkrel . $imgtarget . $linktitle 
					. $mouseover . '>' . $replimg . '</a>';
	}
	
	//Add link string to rest of string
	$repl .= $replimg;

//////////////////////  Create enlarge button, description and their divs////////////////////
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
			$repl .= "\r\t\t\t" . '<div class="magnify">';
			$repl .= "\r\t\t\t\t" . '<a href="' . $link_button . '"' . $linkrel_button . $imgtarget_button ;
			$repl .= ' class="internal"';
			if (!empty($titleonly)) {
				$repl .= ' title="' . $titleonly . '"';
			}
			$repl .= ">\r\t\t\t\t" . '<img class="magnify" src="./pics/icons/magnifier.png" alt="'.tra('Enlarge').'" /></a>' . "\r\t\t\t</div>";
		}	
		//Add description based on user setting (use $desconly from above) and close divs
		isset($desconly) ? $repl .= $desconly : '';
		$repl .= "\r\t\t</div>";
		$repl .= "\r\t</div>";
	}
///////////////////////////////Wrap in overall div that includes image if stylebox or button is set////////////////	
	//Need a box if either button, desc or stylebox is set
	if (!empty($imgdata['button']) || !empty($imgdata['desc']) 
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
		$repl = "\r" . '<div ' . $class . 'style="' . $styleboxplus . '">' . $repl . "\r</div>";
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
	return '~np~' . $repl. '~/np~';
}

/////////////////////////////////////////Function for getting image data from raw file (no filename)////////////////////////////////
 ///Creates a temporary file name and path for a raw image stored in a tiki database since getimagesize needs one to work
if (!function_exists('getimagesize_raw')) {
	function getimagesize_raw($data, $thumb)
	{
        $cwd = getcwd(); #get current working directory
        $tempfile = tempnam("$cwd/tmp", "temp_image_");#create tempfile and return the path/name (make sure you have created tmp directory under $cwd
        $temphandle = fopen($tempfile, "w");#open for writing
        fwrite($temphandle, $data); #write image to tempfile
        fclose($temphandle);
		global $imagesize, $otherinfo, $iptc, $imagesizet;
		if ($thumb == false) {
	        $imagesize = getimagesize($tempfile, $otherinfo); #get image params from the tempfile
			if (!empty($otherinfo['APP13'])) {
				$iptc = iptcparse($otherinfo['APP13']);
			} else {
				$iptc = '';
			}
		} else {
			$imagesizet = getimagesize($tempfile);
		}
        unlink($tempfile); // this removes the tempfile
	}
}
 
