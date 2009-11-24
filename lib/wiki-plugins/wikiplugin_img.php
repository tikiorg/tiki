<?php

function wikiplugin_img_info() {
	return array(
		'name' => tra('Image'),
		'description' => tra('Display images'),
		'prefs' => array( 'wikiplugin_img'),
		'icon' => 'pics/icons/picture.png',
		'params' => array(
			'src' => array(
				'required' => false,
				'name' => tra('Image source'),
				'description' => tra('Full URL to the image to display. "id", "fileId", "attId" or "src" required.'),
			),
			'id' => array(
				'required' => false,
				'name' => tra('Image ID'),
				'description' => tra('Numeric ID of an image in an Image Gallery (or comma-separated list). "id", "fileId", "attId" or "src" required.'),
			),
			'fileId' => array(
				'required' => false,
				'name' => tra('File ID'),
				'description' => tra('Numeric ID of an image in a File Gallery (or comma-separated list). "id", "fileId", "attId" or "src" required.'),
			),
			'attId' => array(
				'required' => false,
				'name' => tra('Attachment ID'),
				'description' => tra('Numeric ID of an image attached to a wiki page (or comma-separated list). "id", "fileId", "attId" or "src" required.'),
			),
			'thumb' => array(
				'required' => false,
				'name' => tra('Thumbnail'),
				'description' => tra('Makes the image a thumbnail that enlarges to full size when clicked or moused over (unless "link" is set to another target). "browse" and "browsepopup" only work with image gallery and "download" only works with file gallery.'),
				'options' => array(
					array('text' => tra('None'), 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y', 'description' => tra('Full size image appears when thumbnail is clicked.')),
					array('text' => tra('Mouseover'), 'value' => 'mouseover', 'description' => tra('Full size image will pop up while cursor is over the thumbnail (and disappear when not).')), 
					array('text' => tra('Mouseover (Sticky)'), 'value' => 'mousesticky', 'description' => tra('Full size image will pop up once cursor passes over thumbnail and will remain up unless cursor passes over full size popup.')), 
					array('text' => tra('Popup'), 'value' => 'popup', 'description' => tra('Full size image will open in a separate winow or tab (depending on browser settings) when thumbnail is clicked.')), 
					array('text' => tra('Browse'), 'value' => 'browse', 'description' => tra('Image gallery browse window for the image will open when the thumbnail is clicked if the image is in a Tiki image gallery')), 
					array('text' => tra('Browse Popup'), 'value' => 'browsepopup', 'description' => tra('Same as "browse" except that the page opens in a new window or tab.')), 
					array('text' => tra('Download'), 'value' => 'download', 'description' => tra('Download dialog box will appear for file gallery images when thumbnail is clicked.')),
				),
			),
			'button' => array(
				'required' => false,
				'name' => tra('Enlarge button'),
				'description' => tra('Button for enlarging image.'),
				'options' => array(
					array('text' => tra('None'), 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
				),
			),
			'link' => array(
				'required' => false,
				'name' => tra('Link'),
				'description' => tra('Enter a url to the address the image should link to. Not needed if thumb parameter is set; overrides thumb setting.'),
			),
			'rel' => array(
				'required' => false,
				'name' => tra('Link relation'),
				'description' => tra('Enter "box" for colorbox effect (like shadowbox and lightbox) or appropriate syntax for link relation.'),
			),
			'usemap' => array(
				'required' => false,
				'name' => tra('Image map'),
				'description' => tra('Name of the image map to use for the image.'),
			),
			'height' => array(
				'required' => false,
				'name' => tra('Image height'),
				'description' => tra('Height in pixels.'),
			),
			'width' => array(
				'required' => false,
				'name' => tra('Image width'),
				'description' => tra('Width in pixels.'),
			),
			'max' => array(
				'required' => false,
				'name' => tra('Maximum image size'),
				'description' => tra('Maximum height or width in pixels (largest dimension is scaled). Overrides height and width settings.'),
			),
			'imalign' => array(
				'required' => false,
				'name' => tra('Align image'),
				'description' => tra('Aligns the image itself. If the image is inside a box (because of other settings), use the align parameter to align the box.'),
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
			),
			'align' => array(
				'required' => false,
				'name' => tra('Align image block'),
				'description' => tra('Aligns the box containing the image.'),
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
				'description' => tra('Enter "border" to place a dark gray border frame around the image. Otherwise enter CSS styling syntax for other style effects.'),
			),
			'styledesc' => array(
				'required' => false,
				'name' => tra('Description style'),
				'description' => tra('Enter "right" or "left" to align text accordingly. Otherwise enter CSS styling syntax for other style effects.'),
			),
			'block' => array(
				'required' => false,
				'name' => tra('Wrapping control'),
				'description' => tra('Control how other items wrap around the image.'),
				'options' => array(
					array('text' => tra('None'), 'value' => ''), 
					array('text' => tra('Top'), 'value' => 'top'), 
					array('text' => tra('Bottom'), 'value' => 'bottom'), 
					array('text' => tra('Both'), 'value' => 'both'), 
				),
			),
			'class' => array(
				'required' => false,
				'name' => tra('CSS class'),
				'description' => tra('CSS class to apply to the image.'),
			),
			'desc' => array(
				'required' => false,
				'name' => tra('Caption'),
				'description' => tra('Image caption. "desc" or "name" for tiki images, "idesc" or "ititle" for iptc data, otherwise enter your own description.'),
			),
			'title' => array(
				'required' => false,
				'name' => tra('Link title'),
				'description' => tra('Title text.'),
			),
			'alt' => array(
				'required' => false,
				'name' => tra('Alternate text'),
				'description' => tra('Alternate text that displays when image does not load.'),
			),
			'default' => array(
				'required' => false,
				'name' => tra('Default config settings'),
				'description' => tra('Default configuration settings (usually set by admin).'),
			),
			'mandatory' => array(
				'required' => false,
				'name' => tra('Mandatory admin setting'),
				'description' => tra('Mandatory configuration settings (usually set by admin).'),
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
	by user. Mandatory will override user settings.*/
	//Uncomment the following line to set the default parameter. Later items have priority. To override align default, put align parameter first
//	$imgdata['default'] = 'default ? max = 200, align = right, styledesc = text-align: center; section_cms_article ? max= 400, width= , height=';
	// Uncomment the following line to set the default parameter. Force certain max and ignore any specified width or height.Later items have priority.
//	$imgdata['mandatory'] = 'section_cms_article ? max = 400; module_* ? max = 150, width= , height=; mode_mobile ? max = 150, width= , height=;';

//////////////////////////////////////////////////Old IMAGE code for default and mandatory parameters////////////////////////////////////////////////////
	// Start processing... first defaults, then given parameters, then mandatory settings.
	// Get parameters once in case there is a 'default' parameter.
	// This will be done again later so parameters can override defaults.
	$imgdata = array_merge( $imgdata, $params );

	if( !empty($imgdata['default']) ) { // If defaults have been specified
		$imgdata['default'] = trim($imgdata['default']) . ';'; // trim whitespace and ensure at least one semicolon
		$img_conditions_array = explode( ";", $imgdata['default'] ); // conditions separated by semicolons
		if( !empty($img_conditions_array) ) {
			foreach($img_conditions_array as $key => $var) { // for each condition
				if( !empty($var) ) {
					$img_condition = explode( "?", $var ); // condition separated from parameters by question mark
					if( !empty($img_condition) ) {
						$img_condition_name = trim($img_condition[0]);
						if( !empty($img_condition[1]) ) { // if there is at least one parameter
							$img_condition[1] = trim($img_condition[1]) . ',';	// at least one comma
							$img_parameters_array = explode( ",", $img_condition[1] ); // separate multiple parameters
							if( !empty($img_parameters_array) ) {  // if a parameter has been extracted
								foreach($img_parameters_array as $param_key => $param_var) {	// for each parameter
									if( !empty($param_var) ) {	// if a parameter exists
										$img_parameter_array = explode( "=", trim($param_var) ); // separate parameters and values
										if( !empty($img_parameter_array[0]) ) {  // if a parameter with a value has been extracted

											$img_condition_status = false;	// initialise condition as not being true

											$img_condition_name = strtolower(trim($img_condition_name));
											switch ($img_condition_name) {
												case "default":
													$img_condition_status = true; // default is always true
													break;
												case "mode_mobile":
													if( $_REQUEST['mode'] == "mobile" ) $img_condition_status = true;
													break;
												case "module_*":
													if( !empty($smarty) ) {
														$image_module_params = $smarty->get_template_vars('module_params');
														if( !empty($image_module_params) ) $img_condition_status = true;
													}
													break;
												case "section_*":
													if( !empty($section) ) $img_condition_status = true;
													break;
												case "section_cms_article":
													if( !empty($section) ) {
														if( $section == "cms" ) {
															if( !empty($smarty) ) {
																$image_article_type = $smarty->get_template_vars('type');
																if( !empty($image_article_type) ) {
																	if( strtolower(trim($image_article_type)) == "article" ) $img_condition_status = true;
																} // if(!empty($image_article_type))
															} // if(!empty($smarty))
														}
													}
													break;
												case "section_cms_review":
													if( !empty($section) ) {
														if( $section == "cms" ) {
															if( !empty($smarty) ) {
																$image_article_type = $smarty->get_template_vars('type');
																if( !empty($image_article_type) ) {
																	if( strtolower(trim($image_article_type)) == "review" ) $img_condition_status = true;
																} // if(!empty($image_article_type))
															} // if(!empty($smarty))
														}
													}
													break;
												case "section_cms_event":
													if( !empty($section) ) {
														if( $section == "cms" ) {
															if( !empty($smarty) ) {
																$image_article_type = $smarty->get_template_vars('type');
																if( !empty($image_article_type) ) {
																	if( strtolower(trim($image_article_type)) == "event" ) $img_condition_status = true;
																} // if(!empty($image_article_type))
															} // if(!empty($smarty))
														}
													}
													break;
												case "section_cms_classified":
													if( !empty($section) ) {
														if( $section == "cms" ) {
															if( !empty($smarty) ) {
																$image_article_type = $smarty->get_template_vars('type');
																if( !empty($image_article_type) ) {
																	if( strtolower(trim($image_article_type)) == "classified" ) $img_condition_status = true;
																} // if(!empty($image_article_type))
															} // if(!empty($smarty))
														}
													}
													break;
											} // switch ($img_condition_name)

											if( $img_condition_status != true ) {
												// if match not found yet, examine more specific conditions
												if( !empty($section) ) {	// if we have a section name
													if( substr($img_condition_name,0,8) == "section_" ) {
														if( strlen($img_condition_name) > 8 ) {
															$img_condition_part = substr($img_condition,8); // get part after "section_"
															$img_condition_part = strtolower($img_condition_part);
															$img_condition_part = trim(strtr($img_condition_part, "_", " ")); // replace underscore with spaces
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
	} // if( !empty($imgdata['default']) )

	// merge specified parameters over default values
	$imgdata = array_merge( $imgdata, $params );

	if( !empty($imgdata['mandatory']) ) { // If defaults have been specified
		$imgdata['mandatory'] = trim($imgdata['mandatory']) . ';'; // trim whitespace and ensure at least one semicolon
		$img_conditions_array = explode( ";", $imgdata['mandatory'] ); // conditions separated by semicolons
		if( !empty($img_conditions_array) ) {
			foreach($img_conditions_array as $key => $var) { // for each condition
				if( !empty($var) ) {
					$img_condition = explode( "?", $var ); // condition separated from parameters by question mark
					if( !empty($img_condition) ) {
						$img_condition_name = trim($img_condition[0]);
						if( !empty($img_condition[1]) ) { // if there is at least one parameter
							$img_condition[1] = trim($img_condition[1]) . ',';	// at least one comma
							$img_parameters_array = explode( ",", $img_condition[1] ); // separate multiple parameters
							if( !empty($img_parameters_array) ) {  // if a parameter has been extracted
								foreach($img_parameters_array as $param_key => $param_var) {	// for each parameter
									if( !empty($param_var) ) {	// if a parameter exists
										$img_parameter_array = explode( "=", trim($param_var) ); // separate parameters and values
										if( !empty($img_parameter_array[0]) ) {  // if a parameter with a value has been extracted

											$img_condition_status = false;	// initialise condition as not being true

											$img_condition_name = strtolower(trim($img_condition_name));
											switch ($img_condition_name) {
												case "default":
													$img_condition_status = true; // default is always true
													break;
												case "mode_mobile":
													if( $_REQUEST['mode'] == "mobile" ) $img_condition_status = true;
													break;
												case "module_*":
													if( !empty($smarty) ) {
														$image_module_params = $smarty->get_template_vars('module_params');
														if( !empty($image_module_params) ) $img_condition_status = true;
													}
													break;
												case "section_*":
													if( !empty($section) ) $img_condition_status = true;
													break;
												case "section_cms_article":
													if( !empty($section) ) {
														if( $section == "cms" ) {
															if( !empty($smarty) ) {
																$image_article_type = $smarty->get_template_vars('type');
																if( !empty($image_article_type) ) {
																	if( strtolower(trim($image_article_type)) == "article" ) $img_condition_status = true;
																} // if(!empty($image_article_type))
															} // if(!empty($smarty))
														}
													}
													break;
												case "section_cms_review":
													if( !empty($section) ) {
														if( $section == "cms" ) {
															if( !empty($smarty) ) {
																$image_article_type = $smarty->get_template_vars('type');
																if( !empty($image_article_type) ) {
																	if( strtolower(trim($image_article_type)) == "review" ) $img_condition_status = true;
																} // if(!empty($image_article_type))
															} // if(!empty($smarty))
														}
													}
													break;
												case "section_cms_event":
													if( !empty($section) ) {
														if( $section == "cms" ) {
															if( !empty($smarty) ) {
																$image_article_type = $smarty->get_template_vars('type');
																if( !empty($image_article_type) ) {
																	if( strtolower(trim($image_article_type)) == "event" ) $img_condition_status = true;
																} // if(!empty($image_article_type))
															} // if(!empty($smarty))
														}
													}
													break;
												case "section_cms_classified":
													if( !empty($section) ) {
														if( $section == "cms" ) {
															if( !empty($smarty) ) {
																$image_article_type = $smarty->get_template_vars('type');
																if( !empty($image_article_type) ) {
																	if( strtolower(trim($image_article_type)) == "classified" ) $img_condition_status = true;
																} // if(!empty($image_article_type))
															} // if(!empty($smarty))
														}
													}
													break;
											} // switch ($img_condition_name)

											if( $img_condition_status != true ) {
												// if match not found yet, examine more specific conditions
												if( !empty($section) ) {	// if we have a section name
													if( substr($img_condition_name,0,8) == "section_" ) {
														if( strlen($img_condition_name) > 8 ) {
															$img_condition_part = substr($img_condition,8); // get part after "section_"
															$img_condition_part = strtolower($img_condition_part);
															$img_condition_part = trim(strtr($img_condition_part, "_", " ")); // replace underscore with spaces
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
	} // if( !empty($imgdata['default']) )
//////////////////////////////////////////////////////////End of old IMAGE code for default and mandatory parameters////////////////////////////////////


//////////////////////////////////////////////////// Error messages and clean javascript /////////////////////////////////////////////////
	// Must set at least one image identifier
	if ( empty($imgdata['fileId']) and empty($imgdata['id']) and empty($imgdata['src']) and empty($imgdata['attId']) ) {
		return tra("''No image specified. Either the fileId, attId, id, or src parameter must be specified.''");
	}
	// Can't set more than one image identifier
	if ( ! ( !empty($imgdata['fileId']) Xor !empty($imgdata['id']) Xor !empty($imgdata['src']) Xor !empty($imgdata['attId']) ) ) {
		return tra("''Use one and only one of the following parameters: fileId, attId, id, or src.''");
	}	
	// Clean up src URLs to exclude javascript
	if (stristr(str_replace(' ', '', $imgdata['src']),'javascript:')) {
		$imgdata['src']  = '';
	}
	if (strstr($imgdata['src'],'javascript:')) {
		$imgdata['src']  = '';
	}

	include_once('tiki-sefurl.php');
	
///////////////////////////////////// If only old img parameters used, use old code and get out of program quickly ///////////////////
	if (!empty($imgdata['src']) && (strpos($imgdata['src'], '|') == FALSE  ) && (strpos($imgdata['src'], ',') == FALSE  ) && empty($imgdata['thumb']) 
		&& empty($imgdata['button']) && empty($imgdata['max']) && empty($imgdata['styleimage']) && empty($imgdata['stylebox']) && empty($imgdata['styledesc']) 
		&& empty($imgdata['block']) && ($imgdata['desc'] != 'desc') && ($imgdata['desc'] != 'idesc') && ($imgdata['desc'] != 'name') && ($imgdata['desc'] != 'ititle') && ($imgdata['rel'] != 'box')) {	
		if ($tikidomain && !preg_match('|^https?:|', $imgdata['src'])) {
			$imgdata['src'] = preg_replace("~img/wiki_up/~","img/wiki_up/$tikidomain/",$imgdata['src']);
		}
		// Handle absolute links (e.g. to send a newsletter with images that remains on the tiki site)
		$absolute_links = isset($parseOptions['absolute_links']) ? $parseOptions['absolute_links'] : false;
		if ( $imgdata['src'] != '' && $absolute_links && ! preg_match('|^[a-zA-Z]+:\/\/|', $imgdata['src']) ) {
			global $base_host, $url_path;
			$imgdata['src'] = $base_host.( $imgdata['src'][0] == '/' ? '' : $url_path ).$imgdata['src'];
		}

		$imgdata_dim = '';
		if ( $prefs['feature_filegals_manager'] == 'y' ) {
			global $detected_lib;
			include_once('lib/images/images.php');
		} else {
			$detected_lib = '';
		}

		if ( $detected_lib != '' && ereg('^'.$tikiroot.'tiki-download_file.php\?', $imgdata['src']) ) {
			// If an image lib has been detected and if we are using an image from a file gallery,
			//   then also resize the image server-side, because it will generally imply less data to download from the user
			//   (i.e. speed up the page download) and a better image quality (browser resize algorithms are quick but bad)
			//
			//   Note: ctype_digit is used to ensure there is only digits in width and height strings (e.g. to avoid '50%', ...)
			//
			if ( (int)$imgdata['width'] > 0 && ctype_digit($imgdata['width']) ) $imgdata['src'] .= '&amp;x='.$imgdata['width'];
			if ( (int)$imgdata['height'] > 0 && ctype_digit($imgdata['height']) ) $imgdata['src'] .= '&amp;y='.$imgdata['height'];
		}
		
		$imgdata["src"] = filter_out_sefurl(htmlentities($imgdata["src"]), $smarty);
		
		if ( $imgdata['width'] ) $imgdata_dim .= ' width="' . $imgdata['width'] . '"';
		if ( $imgdata['height'] ) $imgdata_dim .= ' height="' . $imgdata['height'] . '"';

		$repl = '<img alt="' . $imgdata["alt"] . '" src="'.$imgdata["src"].'" border="0" '.$imgdata_dim;

		if ($imgdata['imalign']) {
			$repl .= ' style="float: ' . $imgdata['imalign'] . '"';
		}
		if ($imgdata['usemap']) {
			$repl .= ' usemap="#'.$imgdata['usemap'].'"';
		}
		if ($imgdata['class']) {
			$repl .= ' class="'.$imgdata['class'].'"';
		}

		$repl .= ' />';

		if ($imgdata['link']) {
			$imgtarget= '';

			if ($prefs['popupLinks'] == 'y' && (preg_match('#^([a-z0-9]+?)://#i', $imgdata['link']) || preg_match('#^www\.([a-z0-9\-]+)\.#i',$imgdata['link']))) {
				$imgtarget = ' target="_blank"';
			}

			if ($imgdata['rel'])
				$linkrel = ' rel="'.$imgdata['rel'].'"';
			else
				$linkrel = '';

			if ($imgdata['title'])
				$linktitle = ' title="'.$imgdata['title'].'"';
			else
				$linktitle = '';
			$repl = '<a href="'.$imgdata['link'].'"'.$linkrel.$imgtarget.$linktitle.'>' . $repl . '</a>';
		}

		if ($imgdata['desc']) {
			$repl = '<table cellpadding="0" cellspacing="0"><tr><td>' . $repl . '</td></tr><tr><td class="mini">' . $imgdata['desc'] . '</td></tr></table>';
		}

		if ($imgdata['align']) {
			$repl = '<div class="img" align="' . $imgdata["align"] . '">' . $repl . "</div>";
		} elseif (!$imgdata['desc']) {
			$repl = '<span class="img">' . $repl . "</span>";
		}
		return $repl;
	///////////end of old IMG code////////////////////
	} else {
	////////////////////////////////////////////// Default parameter and variable settings.//////////////////////////////////////////////	
		// Set styling defaults
		$thumbdef = 84;                         //Thumbnail height max when none is set
		if (!empty($imgdata['fileId'])) {
			$thumbdef = 120;	// filegals thumbnails size is hard-coded in lib/images/abstract.php
		}

		$descdef = 'font-size:12px; line-height:1.5em;';		//default text style for description
		$descheightdef = 'height:15px;';           //To set room for enlarge button under image if there is no description
		$borderdef = 'border:1px solid darkgray;';   //default border when styleimage set to border
		$borderboxdef = 'border:1px solid darkgray; padding:5px; background-color: #f9f9f9;';	 //default border when stylebox set to border or y
		$center = 'display:block; margin-left:auto; margin-right:auto;';	//used to center image and box
		$enlargedef = 'float:right; padding-top:.1cm;';	//styling for the enlarge button div
		$captiondef = 'padding-top:2px;';									//styling for the caption div
		
		//Variable for identifying if javascript mouseover is set
		if (($imgdata['thumb'] == 'mouseover') || ($imgdata['thumb'] == 'mousesticky')) {
			$javaset = 'true';
		} else {
			$javaset = '';
		}
		
		if (!isset($data) or !$data) {
			$data = '&nbsp;';
		}
		
		//Set variables for the base path for images in file galleries, image galleries and attachments
		$imagegalpath = 'show_image.php?id=';
		$filegalpath = 'tiki-download_file.php?fileId=';
		$attachpath = 'tiki-download_wiki_attachment.php?attId=';
		$repl = '';

	/////////////////////////////////////////////// Label images and set id variable based on location////////////////////////////
		// Set id's if user set path in src instead of id for images in file galleries, image galleries and attachments 
		//This is so we can get db info
		if (strlen(strstr($imgdata['src'], $imagegalpath)) > 0) {                                     //if the src parameter contains an image gallery path
			$imgdata['id'] = substr(strstr($imgdata['src'], $imagegalpath), strlen($imagegalpath));   //then isolate id number and put it into $imgdata['id']
		} elseif (strlen(strstr($imgdata['src'], $filegalpath)) > 0) {                                //if file gallery path
			$imgdata['fileId'] = substr(strstr($imgdata['src'], $filegalpath), strlen($filegalpath)); //then put fileId into $imgdata['fileId']	
		} elseif (strlen(strstr($imgdata['src'], $attachpath)) > 0) {                                 //if attachment path
			$imgdata['attId'] = substr(strstr($imgdata['src'], $attachpath), strlen($attachpath));    //then put attId into $imgdata['attId']
		}
		//Identify location of source image and id for use later
		$sourcetype = '';
		$id = '';
		if (!empty($imgdata['id'])) {
			$sourcetype = 'imagegal';
			$id = 'id';
		} elseif (!empty($imgdata['fileId'])) {
			$sourcetype = 'filegal';
			$id = 'fileId';
		} elseif (!empty($imgdata['attId'])) {
			$sourcetype = 'attach';
			$id = 'attId';
		} else {
			$sourcetype = 'url';
			$id = 'src';
		}			
		
	//////////////////////////////////////// Process lists of images ////////////////////////////////////////////////////////
		//Process "|" or "," separated images
		$separator = '';
		if ( !empty($imgdata[$id])  && (( strpos($imgdata[$id], '|') !== FALSE ) || ( strpos($imgdata[$id], ',') !== FALSE )))  {
			if ( strpos($imgdata[$id], '|') !== FALSE ) {
				$separator = '|';
			} elseif ( strpos($imgdata[$id], ',') !== FALSE )  {
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

	//////////////////////////////////////////////////// Set image src ///////////////////////////////////////////////////////////
		// Clean up src URLs to exclude javascript
		if (stristr(str_replace(' ', '', $imgdata['src']),'javascript:')) {
			$imgdata['src']  = '';
		}
		if (strstr($imgdata['src'],'javascript:')) {
			$imgdata['src']  = '';
		}
		
		//Deal with images in tiki databases (file and image galleries and attachments)
		if ( !empty($sourcetype)) {
			//Try to get image from database
			switch ($sourcetype) {
				case 'imagegal':
					global $imagegallib; 
					include_once('lib/imagegals/imagegallib.php');
					$dbinfo = $imagegallib->get_image_info($imgdata['id'], 'o');
					$basepath = $prefs['gal_use_dir'];
					break;
				case 'filegal':
					global $filegallib; 
					include_once('lib/filegals/filegallib.php');
					$dbinfo = $filegallib->get_file($imgdata['fileId']);
					$basepath = $prefs['fgal_use_dir'];
					break;
				case 'attach':
					global $atts;
					global $wikilib;
					include_once('lib/wiki/wikilib.php');
					$dbinfo = $wikilib->get_item_attachment($imgdata['attId']);
					$basepath = $prefs['w_use_dir'];
					break;
			}		
			//Give error messages if it doesn't exist or isn't an image
			if (empty($imgdata['src'])) {
				if( ! $dbinfo ) {
					return '^' . tra('File not found.') . '^';
				} elseif( substr($dbinfo['filetype'], 0, 5) != 'image' ) {
					return '^' . tra('File is not an image.') . '^';
				} else {
				require_once('lib/images/images.php');
					if (!class_exists('Image')) {
					return '^' . tra('Server does not support image manipulation.') . '^';
					}
				}	
			}
			//Now that we know it exists, finish getting info for image gallery files since the path and blob are in two different tables
			if ($sourcetype == 'imagegal') {
				global $imagegallib; 
				include_once('lib/imagegals/imagegallib.php');
				$dbinfo2 = $imagegallib->get_image($imgdata['id'], 'o');
				$dbinfo = array_merge($dbinfo, $dbinfo2);
			}	
			//Set other variables from db info
			if (!empty($dbinfo['comment'])) {		//attachment database uses comment instead of description or name
				$desc = $dbinfo['comment'];
				$imgname = $dbinfo['comment'];
			} else {
				$desc = $dbinfo['description'];
				$imgname = $dbinfo['name'];
			}
		} //finished getting info from db for images in image or file galleries or attachments
		
		//Set src (for html) and base path (for getimagesize)
		$absolute_links = (!empty($parseOptions['absolute_links'])) ? $parseOptions['absolute_links'] : false;
		$thumbstring = '';
		if (empty($imgdata['src'])) {
			switch ($sourcetype) {
				case 'imagegal':
					$imgdata['src'] = $imagegalpath . $imgdata['id'];
					if (!empty($imgdata['thumb'])) {
						$thumbstring = '&thumb=1';
					}
					break;
				case 'filegal':				
					$imgdata['src'] = $filegalpath . $imgdata['fileId']; 
					if (!empty($imgdata['thumb'])) {
						$thumbstring = '&thumbnail';
					}
					break;
				case 'attach':
					$imgdata['src'] = $attachpath . $imgdata['attId']; 
					if (!empty($imgdata['thumb'])) {
					}
					break;
				}
		} elseif ( (!empty($imgdata['src'])) && $absolute_links && ! preg_match('|^[a-zA-Z]+:\/\/|', $imgdata['src']) ) {
			global $base_host, $url_path;
			$imgdata['src'] = $base_host.( $imgdata['src'][0] == '/' ? '' : $url_path ) . $imgdata['src'];
		} elseif (!empty($imgdata['src']) && $tikidomain && !preg_match('|^https?:|', $imgdata['src'])) {
			$imgdata['src'] = preg_replace("~img/wiki_up/~","img/wiki_up/$tikidomain/",$imgdata['src']);
		} elseif (!empty($imgdata['src'])) {
			$imgdata['src'] = $imgdata['src'];
		}
		
		//Now get height, width, iptc data from actual image
		//First get the data. Images in db handled differently than those in directories or path
		global $imagesize, $iptc, $otherinfo;
		$otherinfo = array();
		
		if (!empty($dbinfo['data'])) {
			getimagesize_raw($dbinfo['data']);  //images in databases, calls function in this program
		} else {
			if (!empty($dbinfo['path'])) {
				$imagesize = getimagesize(($basepath . $dbinfo['path']), $otherinfo);  //images in tiki directories
			} else {
				$imagesize = getimagesize($imgdata['src'], $otherinfo);  //wiki_up and external images
			}
			if (isset($otherinfo['APP13'])) { $iptc = iptcparse($otherinfo['APP13']); }
		}
				
			//Set variables for height, width and iptc data from image data
			$fwidth = $imagesize[0];
			$fheight = $imagesize[1];
			$idesc = isset($iptc['2#120'][0]) ? trim($iptc['2#120'][0]) : '';		//description from image iptc
			$ititle = isset($iptc['2#005'][0]) ? trim($iptc['2#005'][0]) : '';		//title from image iptc
			

		// URL of original full size image
		$pos = strpos($imgdata['src'], '&thumb');
		if ($pos > 0) {
			//Strip off any thumbnail parameter
			$len = strlen($imgdata['src']);
			$browse_full_image = substr_replace($imgdata['src'], '', $pos, $len-($len-$pos));
		} else {
			$browse_full_image = $imgdata['src']; 
		}
		
	/////////////////////////////////////Add image dimensions to src string////////////////////////////////////////////////////////////////
		// Adjust for max setting, keeping aspect ratio
		if ((!empty($imgdata['max'])) && (ctype_digit($imgdata['max']))) {
			if (($fwidth > $imgdata['max']) || ($fheight > $imgdata['max'])) {
				if ($fwidth > $fheight) {
					$width = $imgdata['max'];
					$height = floor($width * $fheight / $fwidth);
				} else {
					$height = $imgdata['max'];
					$width = floor($height * $fwidth / $fheight);	
				}
			} else {                             //cases where max is set but image is smaller than max 
				$height = $fheight;
				$width = $fwidth;
			}
		// Adjust for user settings for height and width if max isn't set.	
		} elseif (!empty($imgdata['height']) && ctype_digit($imgdata['height']))  {
			$height = $imgdata['height'];
			if (empty($imgdata['width'])) {
				$width = floor($height * $fwidth / $fheight);
			} else {
				$width = $imgdata['width'];
			}
		} elseif (!empty($imgdata['width']) && ctype_digit($imgdata['width']))  {
			$width =  $imgdata['width'];
			if (empty($imgdata['height'])) {
				$height = floor($width * $fheight / $fwidth);
			} else {
				$height = $imgdata['height'];
			}
		// If not otherwise set, use default setting for thumbnail height if thumb is set
		} elseif (!empty($imgdata['thumb'])) {
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
		
		//Set final height and width dimension string
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
		
	////////////////////////////////////////// Create the HTML img tag ///////////////////////////////////////////////////////////////////
		//Start tag with src and dimensions
		$imgdata["src"] = filter_out_sefurl(htmlentities($imgdata["src"]. $thumbstring), $smarty);
		$replimg = "\r\t" . '<img src="' . $imgdata['src'] . '"';
		$replimg .= $imgdata_dim;
		
		//Create style attribute allowing for shortcut inputs 
		//First set alignment string
		$imalign = '';
		$border = '';
		$style = '';
		if (!empty($imgdata['imalign'])) {
			if ($imgdata['imalign'] == 'center') {
				$imalign = $center;
			} else {
				$imalign = 'float:' . $imgdata['imalign'] . ';';
			}
		}
		//set entire style string
		if( !empty($imgdata['styleimage']) || !empty($imalign)) {
			if ( !empty($imgdata['styleimage'])) {
				if (!empty($imalign)) {
					if ((strpos(trim($imgdata['styleimage'],' '),'float:') > 0) || (strpos(trim($imgdata['styleimage'],' '),'display:') > 0)) {
						$imalign = '';			//override imalign setting is style image contains alignment syntax
					}
				}
				if ($imgdata['styleimage'] == 'border') {
					$border = $borderdef;
				} else if (strpos($imgdata['styleimage'],'hidden') === false && strpos($imgdata['styleimage'],'position') === false) {	// quick filter for dangerous styles
					$style = $imgdata['styleimage'];
				}
			}
			$replimg .= ' style="' . $imalign . $border . $style . '"';
		}
		//alt
		if( !empty($imgdata['alt']) ) {
			$replimg .= ' alt="' . $imgdata['alt'] . '"';
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
				default:
					$desconly = $imgdata['desc'];
			}
		}
		//now set title
		if ( !empty($imgdata['title']) || !empty($desconly)) {
			$imgtitle = ' title="';
			if ( !empty($imgdata['title']) ) {
				$titleonly = $imgdata['title'];
			} else {										//use desc setting for title if title is empty
				$titleonly = $desconly;
			}
			$imgtitle .= $titleonly . '"';
			$replimg .= $imgtitle;
		}	
		
		$replimg .= ' />';

	////////////////////////////////////////// Create the HTML link ////////////////////////////////////////////////////////////////////////
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
				if ($sourcetype == 'filegal' && $imgdata['thumb'] != 'download') {
					$link = $browse_full_image . '&display';
				} else {
					$link = $browse_full_image;
				}
			}
			// Set other link-related attributes				
			// target
			$imgtarget= '';
			if (($prefs['popupLinks'] == 'y' && (preg_match('#^([a-z0-9]+?)://#i', $link) || preg_match('#^www\.([a-z0-9\-]+)\.#i',$link))) || ($imgdata['thumb'] == 'popup') || ($imgdata['thumb'] == 'browsepopup')) {
				if (!empty($javaset) || ($imgdata['rel'] == 'box')) {
					$imgtarget= '';
				} else {
					$imgtarget = ' target="_blank"';
				}
			}
			// rel
			if (!empty($imgdata['rel'])) {
				if ($imgdata['rel'] == 'box') {
					$linkrel = ' rel="box';
					if (!empty($fwidth) && !empty($fheight)) {
						$linkrel .= ";width=$fwidth;height=$fheight";
					}
					/*if (!empty($desconly)) {
						$linkrel .= ";title=$desconly";
					}*/
					$linkrel .= '"';
					
				} else {
					$linkrel = ' rel="'.$imgdata['rel'].'"';
				}
			} else {
				$linkrel = '';
			}
			// title
			if ( !empty($imgtitle) ) {
				$linktitle = $imgtitle;
			} else {
				$linktitle = '';
			}
			
			$link = filter_out_sefurl(htmlentities($link), $smarty);

			//Final link string
			$replimg = '<a href="' . $link . '" class="internal"' . $linkrel . $imgtarget . $linktitle . $mouseover . '>' . $replimg . '</a>';
		}
		
		//Add link string to rest of string
		$repl .= $replimg;

	/////////////////////////////////  Create enlarge button, description and their divs////////////////////////////////////////////////////
		//Start div that goes around button and description if these are set
		if ((!empty($imgdata['button'])) || (!empty($imgdata['desc'])) || (!empty($imgdata['styledesc']))) {
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
			$repl .= "\r\t\t" . '<div class="thumbcaption" style="' . $captiondef . '" >';
			
			//Enlarge button div and link string (innermost div)
			if (!empty($imgdata['button'])) {
				if (empty($link) || (!empty($link) && !empty($javaset))) {
					if ((($imgdata['button'] == 'browse') || ($imgdata['button'] == 'browsepopup')) && !empty($imgdata['id']))  {
						$link_button = 'tiki-browse_image.php?imageId=' . $imgdata['id'];
					} else {
						if ($sourcetype == 'filegal' && $imgdata['button'] != 'download') {
							$link_button = $browse_full_image . '&display';
						} else {
							$link_button = $browse_full_image;
						}
					}
				} else {
					$link_button = $link;
				}
				//Set button rel
				if (empty($linkrel) || !empty($javaset)) {
						$linkrel_button = '';
				} else {
					$linkrel_button = $linkrel;
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
				$repl .= "\r\t\t\t" . '<div class="magnify" style="' . $enlargedef . '">';
				$repl .= "\r\t\t\t\t" . '<a href="' . $link_button . '"' . $linkrel_button . $imgtarget_button ;
				$repl .= ' class="internal"';
				if (!empty($titleonly)) {
					$repl .= ' title="' . $titleonly . '"';
				}
				$repl .= ">\r\t\t\t\t" . '<img src="./img/magnifying-glass-micro-icon.png" width="10" height="10" alt="Enlarge" /></a>' . "\r\t\t\t</div>";
			}	
			//Add description based on user setting (use $desconly from above) and close divs
			$repl .= $desconly;
			$repl .= "\r\t\t</div>";
			$repl .= "\r\t</div>";
		}
	///////////////////////////////Wrap in overall div that includes image if stylebox or button is set/////////////////////////////////////	
		//Need a box if either button, desc or stylebox is set
		if (!empty($imgdata['button']) || !empty($imgdata['desc']) || !empty($imgdata['stylebox']) || !empty($imgdata['align'])) {
			//Make the div surrounding the image 2 pixels bigger than the image
			$boxwidth = $width + 2;
			$boxheight = $height + 2;
			$alignbox = '';
			if (!empty($imgdata['align'])) {
				if ($imgdata['align'] == 'center') {
					$alignbox = $center;
				} else {
					$alignbox = 'float:' . $imgdata['align'] . ';';
				}
			}
			//first set stylebox string if style box is set
			if (!empty($imgdata['stylebox']) || !empty($imgdata['align'])) {				//create strings from shortcuts first
				if ( !empty($imgdata['stylebox'])) {
					if ($imgdata['stylebox'] == 'border') {
						$borderbox = $borderboxdef;
						if (!empty($alignbox)) {
							if ((strpos(trim($imgdata['stylebox'],' '),'float:') > 0) || (strpos(trim($imgdata['stylebox'],' '),'display:') > 0)) {
								$alignbox = '';			//override imalign setting is style image contains alignment syntax
							}
						}
					} else {
						$styleboxinit = $imgdata['stylebox'];
					}
				}
				if (empty($imgdata['button']) && empty($imgdata['desc']) && empty($styleboxinit)) {
					$styleboxplus = $alignbox . $borderbox . ' width:' . $boxwidth . 'px; height:' . $boxheight . 'px';
				} elseif (!empty($styleboxinit)) {
					$styleboxplus = $styleboxinit;
				} else {
					$styleboxplus = $alignbox . $borderbox . $descdef . ' width:' . $boxwidth . 'px';
				}
			} elseif (!empty($imgdata['button']) || !empty($imgdata['desc'])) {
			$styleboxplus = $descdef . ' width:' . $boxwidth . 'px;';
			}
		}
		if ( !empty($styleboxplus)) {
			$repl = "\r" . '<div class="img" style="' . $styleboxplus . '">' . $repl . "\r</div>";
		}	
	//////////////////////////////////////Place 'clear' block///////////////////////////////////////////////////////////////////////////////////
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
			$repl = '{img src=' . $imgdata['src'] . "\"}\n<p>" . $imgdata['desc'] . '</p>'; 
		}
		return '~np~'.$repl.'~/np~';
	}
}

///////////////////////////////////////////////////Function for getting image data from raw file (no filename)////////////////////////////////
 ///Creates a temporary file name and path for a raw image stored in a tiki database since getimagesize needs one to work
if (!function_exists('getimagesize_raw')) {
	function getimagesize_raw($data){
        $cwd = getcwd(); #get current working directory
        $tempfile = tempnam("$cwd/tmp", "temp_image_");#create tempfile and return the path/name (make sure you have created tmp directory under $cwd
        $temphandle = fopen($tempfile, "w");#open for writing
        fwrite($temphandle, $data); #write image to tempfile
        fclose($temphandle);
		global $imagesize, $otherinfo, $iptc;
        $imagesize = getimagesize($tempfile, $otherinfo); #get image params from the tempfile
		if (!empty($otherinfo['APP13'])) {
			$iptc = iptcparse($otherinfo['APP13']);
		} else {
			$iptc = '';
		}
        unlink($tempfile); // this removes the tempfile
	}
}
 ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 