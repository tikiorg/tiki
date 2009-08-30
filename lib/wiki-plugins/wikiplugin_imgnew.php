<?php
/**
 * Show image in configured format.
 * Developed by Scot E. Wilcoxon for Tiki CMS
 * Based on img, SHARETHIS, and THUMBDIV plugins.
 * Usage:
 * {IMAGE(<options=>"values">)}{IMAGE}
 *
 * @package IMAGE plugin.
 * @author Scot E. Wilcoxon <scot@wilcoxon.org>
 * @version 1.7
 *
 * 2008-12-08 SEWilco
 *	 Initial version.
 * 2009-02-10 SEWilco
 * Add default border because default styles don't know this object.
 * 2009-02-13 SEWilco
 * Add descoptions - description option control
 * 2009-02-24 SEWilco
 * Dark border.  Higher priority rules at end of list.
 * Add fileId support.
 * 2009-02-25 SEWilco
 * Add comma-separated list of images.
 * 2009-02-26 SEWilco
 * Only allow one of fileId, id, src options.
 * 2009-08-15 lindon
 * Make plain inline image the default
 * Add attachment ID support
 * Add shadowbox view
 * Add mouseover
 * Add ability to show existing database name or description 
 * Add ability to show existing description or title from image IPTC data
 *
 * Copyright (c) 2002-2009, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 */

/**
 *
 * @return string Summary with plugin's description and parameters.
 */
function wikiplugin_imgnew_help() {
		return tra("Display an image using configured format. Allows presentation of all images to be changed without having to alter existing content.").":<br />~np~" . '{IMAGE(
	[ id="Numeric ID of an image in an Image Gallery (or comma-separated list). "fileId", "attId", "id" or "src" required." ]
	[ fileId="Numeric ID of an image in a File Gallery (or comma-separated list). "fileId", "attId", "id" or "src" required." ]
	[ attId="Numeric ID of an image attachment to a wiki PAGE (or comma-separated list). "fileId", "attId", "id" or "src" required." ]
	[ src="Full URL to the image to display. "fileId", "attId", "id" or "src" required." ]
	[ scalesize="Maximum height or width in pixels (largest dimension is scaled). If no scalesize is given one will be attempted from default or given height or width. If scale does not match a defined scale for the gallery the full sized image is downloaded." ]
	[ height="Height in pixels." ]
	[ width="Width in pixels." ]
	[ link="Alias: lnk. Location the image should point to." ]
	[ rel="\"rel\" attribute to add to the link." ]
	[ title="Title text." ]
	[ alt="Alternate text to display if impossible to load the image." ]
	[ align="Alias:imalign. Alignment of image element in the page. (left, right)" ]
	[ block="Whether to block items from flowing next to image from the top or bottom. (top,bottom,both,none)" ]
	[ desc="Image description to display on the page." ]
	[ usemap="Name of the image map to use for the image." ]
	[ class="CSS class to apply to the image'."'".'s img tag." ]
	[ style="CSS styling to apply to the image." ]
	[ border="Border configuration for image element.  Values "on" and "off" control visibility, or else specify CSS styling options." ]
	[ descoptions="Description configuration.  Values "on" and "off" control visibility, or else specify CSS styling options." ]
	)}{IMAGE}~/np~';
}

/**
 * Return plugin information, with description and parameter list.
 *
 * @return array
 */
function wikiplugin_imgnew_info() {
	return array(
		'name' => tra('Image New'),
		'description' => tra('Display images (experimental - possible successor to img in 4.0)'),
		'prefs' => array( 'wikiplugin_imgnew'),
		'params' => array(
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
			'src' => array(
				'required' => false,
				'name' => tra('Image Source'),
				'description' => tra('Full URL to the image to display. "id", "fileId", "attId" or "src" required.'),
			),
			'thumb' => array(
				'required' => false,
				'name' => tra('Thumbnail'),
				'description' => tra('Makes the image a thumbnail. Will link to the full size image unless "link" is set. Parameter options indicate how the full image will be displayed: "shadowbox", "mouseover", "mousesticky", "popup", "browse" and "browsepopup" (only works with image gallery) and "plain". '),
			),
			'button' => array(
				'required' => false,
				'name' => tra('Enlarge button'),
				'description' => tra('Button for enlarging image. Set to "y" for it to appear. If thumb is set, then same method as thumb will be used to enlarge, except if mouseover or mousesticky is used. If thumb is not set or set to mouseover or mousesticky, then choice of "shadowbox", "popup", "browse" and "browsepopup" (for image gallery), and "plain".'),
			),
			'link' => array(
				'required' => false,
				'name' => tra('Link'),
				'description' => tra('For making the image a hyperlink. Enter a url to the page the image should link to. Not needed if thumb parameter is set. If set and thumb parameter is also set, then the link parameter will be used.'),
			),
			'rel' => array(
				'required' => false,
				'name' => tra('Link Relation'),
				'description' => tra('"rel" attribute to add to the link.'),
			),
			'usemap' => array(
				'required' => false,
				'name' => tra('Image Map'),
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
				'description' => tra('Maximum height or width in pixels (largest dimension is scaled).'),
			),
			'styleimage' => array(
				'required' => false,
				'name' => tra('Image style'),
				'description' => tra('Enter "right", "left" or "center" for alignment, and/or "border" separated by a "|". Otherwise enter CSS styling syntax for other style effects. Leave blank for full size inline image'),
			),
			'stylebox' => array(
				'required' => false,
				'name' => tra('Image block style'),
				'description' => tra('Enter "y" for default settings with a border. Enter "right", "left" or "center" for alignment with default settings. Otherwise enter CSS styling syntax for other style effects.'),
			),
			'styledesc' => array(
				'required' => false,
				'name' => tra('Description style'),
				'description' => tra('Enter "right" or "left" to align text accordingly. Otherwise enter CSS styling syntax for other style effects.'),
			),
			'block' => array(
				'required' => false,
				'name' => tra('Alignment'),
				'description' => tra('Whether to block items from flowing next to image from the top or bottom. (top,bottom,both,none)'),
			),
			'class' => array(
				'required' => false,
				'name' => tra('CSS Class'),
				'description' => tra('CSS class to apply to the image'."'".'s img tag. (Usually used in configuration rather than on individual images.)'),
			),
			'desc' => array(
				'required' => false,
				'name' => tra('Description'),
				'description' => tra('Image description to display on the page. "desc" or "name" for tiki images, "idesc" or "ititle" for iptc data, otherwise enter one.'),
			),
			'title' => array(
				'required' => false,
				'name' => tra('Link title'),
				'description' => tra('Title text.'),
			),
			'alt' => array(
				'required' => false,
				'name' => tra('Image alt text'),
				'description' => tra('Alternate text to display if impossible to load the image.'),
			),
			'default' => array(
				'required' => false,
				'name' => tra('Default configuration'),
				'description' => tra('Default configuration definitions. (Usually used in configuration rather than on individual images.)'),
			),
			'mandatory' => array(
				'required' => false,
				'name' => tra('Mandatory configuration'),
				'description' => tra('Mandatory configuration definitions. (Usually used in configuration rather than on individual images.)'),
			),
			'align' => array(
				'required' => false,
				'name' => tra('align'),
				'description' => tra('(Legacy parameter - do not use. Stylebox should be used instead.)'),
			),
			'imalign' => array(
				'required' => false,
				'name' => tra('imalign'),
				'description' => tra('(Legacy parameter - do not use. Styleimage should be used instead.)'),
			),
			'style' => array(
				'required' => false,
				'name' => tra('style'),
				'description' => tra('(Legacy parameter - do not use. Styleimage should be used instead.)'),
			),
			'border' => array(
				'required' => false,
				'name' => tra('border'),
				'description' => tra('(Legacy parameter - do not use. Stylebox should be used instead.)'),
			),
			'descoptions' => array(
				'required' => false,
				'name' => tra('descoptions'),
				'description' => tra('(Legacy parameter - do not use. Styledesc should be used instead.)'),
			),
		),
	);
}


/**
 * Display specified image.
 * @param string $data Data provided between start and end of plugin.
 * @param array $params Plugin parameters.
 * @param integer $offset Offset.
 * @param array $parseOptions Parsing options such as is_html, language, page, print.
 * @return string Content to add to the current page.
 *
 * @global $tikidomain	Domain can be used to determine formatting context.
 * @global $prefs				Some behavior is controlled by configured preferences.
 * @global $section			Section can be used to determine formatting context.
 * @global $smarty			Some smarty variables are read to determine formatting context.
 *

 * Configuration with 'default' and 'mandatory' options:
 *
 * The 'default' option allow definition of default values,
 * although those can be overridden when using the plugin.
 * The 'mandatory' settings are specified using the same syntax
 * as 'default', but are applied after the plugin-specified
 * parameters.
 * 
 * The default values are specified as:
 *	 condition ? parameter = value [, parameter = value ...][; condition ? parameter = value ...]
 *
 * The conditions are some recognized contexts, so the plugin can behave 
 * differently in various contexts.  For example, the image can be shown
 * in a small size within a module while a different size within an
 * article.  This means that an image in the heading of an article can
 * be made to fit in a module which automatically shows the heading of
 * recent articles.
 * 
 * The defined conditions are:
 *	 - default - default values
 *	 - mode_mobile - When in mobile mode
 *	 - module_*  - When in any module
 *	 - section_* - When in any section (The 'section_*' applies to any section)
 *	 - section_cms_article	 - When in a CMS (Article) section of type Article
 *	 - section_cms_review		 - When in a CMS (Article) section of type Review
 *	 - section_cms_event	 - When in a CMS (Article) section of type Event
 *	 - section_cms_classified		 - When in a CMS (Article) section of type Classified
 *	 - A specific section name can be specified instead of '*'.
 *		 - Current list of section names:
 *			- blogs, calendar, categories, cms, directory, faqs,
 *				featured_links, file_galleries, forums, freetags, friends,
 *				galleries, games, html_pages, livesupport, maps, mytiki,
 *				newsletters, newsreader, poll, quizzes, sheet, surveys,
 *				trackers, user_messages, users, webmail, wiki page
 * 
 * The parameter is the name of any IMAGE option (id, scalesize, width, ...).
 * 
 * sample:
 *	 $imgdata["default"] = 'default ? scalesize = 200, align = right, style = text-align:center; mode_mobile ? scalesize = 150; module_* ? scalesize = 150; section_cms_article ? scalesize = 400';
 *
 *	@internal
 *	Presently some options insert editor-supplied values into CSS fields.
 *	This is a stability and security risk, as an editor might break out
 *	of the current HTML context and emit something awkward to the
 *	reader's browser.  One solution: scan a copy of parameters which are to be
 *	inserted, and remove recognized terms ("border:", "#292929"), and if
 *	anything remains then error and quit.
 */

 ///////////////////////////////////////////////////Function for getting image data from raw file (no filename)////////////////////////////////
 ///Creates a temporary file name and path for a raw image stored in a tiki database since getimagesize needs one to work
	
	function getimagesize_raw($data){
        $cwd = getcwd(); #get current working directory
        $tempfile = tempnam("$cwd/tmp", "temp_image_");#create tempfile and return the path/name (make sure you have created tmp directory under $cwd
        $temphandle = fopen($tempfile, "w");#open for writing
        fwrite($temphandle, $data); #write image to tempfile
        fclose($temphandle);
		global $imagesize;
        $imagesize = getimagesize($tempfile, $otherinfo); #get image params from the tempfile
		global $iptc;
		$iptc = iptcparse($otherinfo['APP13']);
        unlink($tempfile); // this removes the tempfile
	}
 
 ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 
 function wikiplugin_imgnew( $data, $params, $offset, $parseOptions='' ) {
	global $tikidomain, $prefs, $section, $smarty;

	$imgdata = array();
	
	$imgdata['id'] = '';
	$imgdata['fileId'] = '';
	$imgdata['attId'] = '';
	$imgdata['src'] = '';
	$imgdata['thumb'] = '';
	$imgdata['button'] = '';
	$imgdata['link'] = '';
	$imgdata['lnk'] = '';	//legacy syntax from img plugin
	$imgdata['rel'] = '';
	$imgdata['usemap'] = '';
	$imgdata['height'] = '';
	$imgdata['width'] = '';
	$imgdata['max'] = '';
	$imgdata['styleimage'] = '';
	$imgdata['stylebox'] = '';
	$imgdata['styledesc'] = '';
	$imgdata['block'] = '';
	$imgdata['class'] = '';
	$imgdata['desc'] = '';
	$imgdata['title'] = '';
	$imgdata['alt'] = '';
	$imgdata['default'] = '';
	$imgdata['mandatory'] = '';
	$imgdata['align'] = '';	//legacy syntax from img amd image plugins
	$imgdata['imalign'] = '';	//legacy syntax from img plugin
	$imgdata['style'] = '';	//legacy syntax from image plugin
	$imgdata['border'] = '';	//legacy syntax from image plugin
	$imgdata['descoptions'] = ''; //legacy syntax from image plugin

//////////////////////This code (from here to about line 714) is from the image plugin and has not been tested recently////////////////////

	// The following are local defaults copied and modified from above.  Later items have priority.
	$imgdata['default'] = 'default ? section_cms_article ? max = 400, width= , height=';
	// Force certain max and ignore any specified width or height.	Later items have priority.
	$imgdata['mandatory'] = 'section_cms_article ? scalesize = 400; module_* ? max = 150, width= , height=; mode_mobile ? max = 150, width= , height=;';
	/*
	** Start processing... first defaults, then given parameters, then mandatory settings.
	*/

	// Get parameters once in case there is a 'default' parameter.
	// This will be done again later so parameters can override defaults.
	$imgdata = array_merge( $imgdata, $params );

	if( !empty($imgdata['default']) ) { // If defaults have been specified
		$imgdata['default'] = trim($imgdata['default']) . ';'; // trim whitespace and ensure at least one semicolon
		$img_conditions_array = explode( ';', $imgdata['default'] ); // conditions separated by semicolons
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
													if( $_REQUEST['mode'] == 'mobile' ) $img_condition_status = true;
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
															$img_condition_part = substr($img_condition,8); // get part after 'section_'
															$img_condition_part = strtolower($img_condition_part);
															$img_condition_part = trim(strtr($img_condition_part, '_', ' ')); // replace underscore with spaces
															if( $section == $img_condition_part ) $img_condition_status = true;
														} // if( length($img_condition_name) > 8 )
													} // if( substr($img_condition_name,0,8) == 'section_' )
												} // if( !empty($section) )
											}

											if( $img_condition_status == true ) {
												// set the parameters to their values
												switch (strtolower(trim($img_parameter_array[0]))) {
													case 'id':
														$imgdata['id'] = trim($img_parameter_array[1]);
														break;
													case 'fileid':
													case 'fileId':
														$imgdata['fileId'] = trim($img_parameter_array[1]);
														break;
													case 'attId':
														$imgdata['attId'] = trim($img_parameter_array[1]);
														break;
													case 'src':
														$imgdata['src'] = trim($img_parameter_array[1]);
														break;
													case 'thumb':
														$imgdata['thumb'] = trim($img_parameter_array[1]);
														break;
													case 'button':
														$imgdata['button'] = trim($img_parameter_array[1]);
														break;
													case 'lnk':
													case 'link':
														$imgdata['lnk'] = trim($img_parameter_array[1]);
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
													case 'styleimage':
														$imgdata['styleimage'] = trim($img_parameter_array[1]);
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
													case 'align':
														$imgdata['align'] = trim($img_parameter_array[1]);
														break;
													case 'imalign':
														$imgdata['imalign'] = trim($img_parameter_array[1]);
														break;
													case 'style':
														$imgdata['style'] = trim($img_parameter_array[1]);
														break;
													case 'border':
														$imgdata['border'] = trim($img_parameter_array[1]);
														break;
													case 'descoptions':
														$imgdata['descoptions'] = trim($img_parameter_array[1]);
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
															$img_condition_part = substr($img_condition,8); // get part after 'section_'
															$img_condition_part = strtolower($img_condition_part);
															$img_condition_part = trim(strtr($img_condition_part, '_', ' ')); // replace underscore with spaces
															if( $section == $img_condition_part ) $img_condition_status = true;
														} // if( length($img_condition_name) > 8 )
													} // if( substr($img_condition_name,0,8) == 'section_' )
												} // if( !empty($section) )
											}

											if( $img_condition_status == true ) {
												// set the parameters to their values
												switch (strtolower(trim($img_parameter_array[0]))) {
													case 'id':
														$imgdata['id'] = trim($img_parameter_array[1]);
														break;
													case 'fileid':
													case 'fileId':
														$imgdata['fileId'] = trim($img_parameter_array[1]);
														break;
													case 'attId':
														$imgdata['attId'] = trim($img_parameter_array[1]);
														break;
													case 'src':
														$imgdata['src'] = trim($img_parameter_array[1]);
														break;
													case 'thumb':
														$imgdata['thumb'] = trim($img_parameter_array[1]);
														break;
													case 'button':
														$imgdata['button'] = trim($img_parameter_array[1]);
														break;
													case 'lnk':
													case 'link':
														$imgdata['lnk'] = trim($img_parameter_array[1]);
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
													case 'styleimage':
														$imgdata['styleimage'] = trim($img_parameter_array[1]);
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
													case 'align':
														$imgdata['align'] = trim($img_parameter_array[1]);
														break;
													case 'imalign':
														$imgdata['imalign'] = trim($img_parameter_array[1]);
														break;
													case 'style':
														$imgdata['style'] = trim($img_parameter_array[1]);
														break;
													case 'border':
														$imgdata['border'] = trim($img_parameter_array[1]);
														break;
													case 'descoptions':
														$imgdata['descoptions'] = trim($img_parameter_array[1]);
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

//////////////////////////////////////////////////// Error messages //////////////////////////////////////////////////////////////
	// Must set at least one image identifier
	if ( empty($imgdata['fileId']) and empty($imgdata['fileid']) and empty($imgdata['id']) and empty($imgdata['src']) and empty($imgdata['attId']) ) {
		return tra("''No image specified. Either the fileId, attId, id, or src parameter must be specified.''");
	}
	// Can't set more than one image identifier
	if ( ! ( !empty($imgdata['fileId']) Xor !empty($imgdata['fileid']) Xor !empty($imgdata['id']) Xor !empty($imgdata['src']) Xor !empty($imgdata['attId']) ) ) {
		return tra("''Use one and only one of the following parameters: fileId, attId, id, or src.''");
	}
	
	//note:
	//maxes overrules and heights and widths
	//rel cancels out shadowbox
	//dimensions must be pixels and not percentages
	
//////////////////////////////////////////////////// Default parameter and variable settings.//////////////////////////////////////////////////////
	
	// Set styling defaults
	$thumbdef = 100;                          //Thumbnail height max when none is set
	$descdef = 'font-size:12px; line-height:1.5em;';		//default text style for description
	$descheightdef = 'height:15px';           //To set room for enlarge button under image if there is no description
	$borderdef = 'border:1px solid darkgray;';   //default border when styleimage set to border
	$borderboxdef = 'border:1px solid darkgray; padding:5px; background-color: #f9f9f9;';	 //default border when stylebox set to border or y
	$center = 'display:block; margin-left:auto; margin-right:auto;';	//used to center image and box
	$enlargedef = 'float:right; padding-top:.1cm;';	//styling for the enlarge button div
	$captiondef = 'padding-top:2px';									//styling for the caption div
	
	// Set shadowbox view as default for enlarge
	if ( $imgdata['thumb'] == 'y' ) {
		$imgdata['thumb'] = 'shadowbox';
	}		
	if ( $imgdata['button'] == 'y' ) {
		$imgdata['button'] = 'shadowbox';
	}	
	
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
	
////////////////////////////////////////////////////// Syntax support /////////////////////////////////////////////////////////////////////
	
	// Support both 'fileId' and 'fileid' syntax
	if ( (!empty($imgdata['fileid'])) && empty($imgdata['fileId']) ) {
		$imgdata['fileId'] = $imgdata['fileid'];
	}
	
	// Support both 'link' and 'lnk' syntax
	if ( (!empty($imgdata['link'])) && empty($imgdata['lnk']) ) {
		$imgdata['lnk'] = $imgdata['link'];
	}
		
	// Support 'imalign' legacy syntax from previous img plugin
	if ( (!empty($imgdata['imalign'])) && empty($imgdata['styleimage']) ) {
		$imgdata['styleimage'] = $imgdata['imalign'];
	}
	
	// Support 'style' legacy syntax from image plugin
	if ( (!empty($imgdata['style'])) && empty($imgdata['styleimage']) ) {
		$imgdata['styleimage'] = $imgdata['style'];
	}
	
	// Support 'align' legacy syntax from previous img  and image plugin
	if (!empty($imgdata['align'])) {
		if ($imgdata['stylebox'] == 'border') {
		$imgdata['stylebox'] .= '|' . $imgdata['align'];
		} elseif (empty($imgdata['stylebox'])) {
		$imgdata['stylebox'] = $imgdata['align'];
		}
	}
	
	// Support 'descoptions' legacy syntax from image plugin
	if (!empty($imgdata['descoptions'])) {
		if (($imgdata['descoptions'] == 'on') && empty($imgdata['button'])) {
			$imgdata['button'] = 'y';
		} elseif (!empty($imgdata['descoptions']) && empty($imgdata['styledesc'])) {
			$imgdata['styledesc'] = $imgdata['descoptions'];
		}
	}
	
	// Support 'border' legacy syntax from image plugin
	if ($imgdata['border'] == 'on') {
		if (($imgdata['stylebox'] == 'left') || ($imgdata['stylebox'] == 'center') || ($imgdata['stylebox'] == 'right')) {
			$imgdata['stylebox'] .= '|' . $imgdata['border'];
		} elseif (empty($imgdata['stylebox'])) {
			$imgdata['stylebox'] = $imgdata['border'];
		}
	}
	

////////////////////////////////////////////////////////Label images and set id variable based on location////////////////////////////////////
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
	}	
	
//////////////////////////////////////// Process lists of images ////////////////////////////////////////////////////////
	//Process "|" separator
	if( ( !empty($imgdata[$id]) ) && ( strpos($imgdata[$id], '|') !== FALSE ) ) {
		$repl = '';
		$id_list = array();
		$id_list = explode('|',$imgdata[$id]);
		$params[$id] = '';
		foreach ($id_list as $i => $value) {
			$params[$id] = trim($value);
			$repl .= wikiplugin_imgnew( $data, $params, $offset, $parseOptions );
		}
		$repl = "\n\r" . '<br style="clear:both" />' . "\r" . $repl . "\n\r" . '<br style="clear:both" />' . "\r";
		return $repl; // return the result of those images
	}
	
	//Process legacy comma separator
	if( ( !empty($imgdata[$id]) ) && ( strpos($imgdata[$id], ',') !== FALSE ) ) {
		$repl = '';
		$id_list = array();
		$id_list = explode(',',$imgdata[$id]);
		$params[$id] = '';
		foreach ($id_list as $i => $value) {
			$params[$id] = trim($value);
			$repl .= wikiplugin_imgnew( $data, $params, $offset, $parseOptions );
		}
		$repl = "\n\r" . '<br style="clear:both" />' . "\r" . $repl . "\n\r" . '<br style="clear:both" />' . "\r";
		return $repl; // return the result of those images
	}


//////////////////////////////////////////////////// Set image src ///////////////////////////////////////////////////////////
	// Clean up src URLs to exclude javascript
	if (stristr(str_replace(' ', '', $imgdata["src"]),'javascript:')) {
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
	if (empty($imgdata['src'])) {
		switch ($sourcetype) {
			case 'imagegal':
				$imgdata['src'] = $imagegalpath . $imgdata['id'];
				break;
			case 'filegal':
				$imgdata['src'] = $filegalpath . $imgdata['fileId']; // took out " . "&preview=y"
				break;
			case 'attach':
				$imgdata['src'] = $attachpath . $imgdata['attId']; // took out " . "&preview=y"
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
	if (!empty($dbinfo['data'])) {
		global $imagesize, $iptc;
		getimagesize_raw($dbinfo['data']);  //images in databases, calls function in this program
	} else {
		if (!empty($dbinfo['path'])) {
			$imagesize = getimagesize(($basepath . $dbinfo['path']), $otherinfo);  //images in tiki directories
		} else {
			$imagesize = getimagesize($imgdata['src'], $otherinfo);  //wiki_up and external images
		}
		$iptc = iptcparse($otherinfo['APP13']);
	}
			
		//Set variables for height, width and iptc data from image data
		$fwidth = $imagesize[0];
		$fheight = $imagesize[1];
		$idesc = trim($iptc['2#120'][0]);		//description from image iptc
		$ititle = trim($iptc['2#005'][0]);		//title from image iptc
		

	// URL of original image
	$browse_full_image = $imgdata['src']; 
	
///////////////////////////////////////////Add image dimensions to src string///////////////////////////////////////////////////////////////////
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
	//Otherwie html dimensions are the same as original
	} elseif  (empty($height) && empty($width)) {
		$height = $fheight;
		$width = $fwidth;
	}
	
	//Set final height and width dimension string
	$imgdata_dim = ' height="' . $height . '"';
	$imgdata_dim .= ' width="' . $width . '"';

	
////////////////////////////////////////// Create the HTML img tag ///////////////////////////////////////////////////////////////////
	//Start tag with src and dimensions
	$replimg = "\r\t" . '<img src="' . $imgdata['src']. '"';
	$replimg .= $imgdata_dim;
	//Create style attribute allowing for shortcut inputs of right or left and border, separated by a | 
	//Allows align and border in any order and allows for a space. All these work: border|right, right | border, left |border, etc.
	if( !empty($imgdata['styleimage']) ) {
		$styleimgstr = ' style="';
		if (preg_match("/^[borde ]*[| ]*right[| ]*[borde ]*$/", $imgdata['styleimage'])) {
			$styleimg = 'float:right;';
		} elseif (preg_match("/^[borde ]*[| ]*left[| ]*[borde ]*$/", $imgdata['styleimage'])) {
			$styleimg = 'float:left;';
		} elseif (preg_match("/^[borde ]*[| ]*center[| ]*[borde ]*$/", $imgdata['styleimage'])) {
			$styleimg = $center;
		} 
		if (preg_match("/^[righleftcn ]*[| ]*border[| ]*[righleftcn ]*$/", $imgdata['styleimage'])) {
			$styleimg .= $borderdef;
		}
		//If user input something other than a shortcut, use the input
		if (empty($styleimg)) {
			$styleimg = $imgdata['styleimage'] . ';';
		}
		$styleimgstr .= $styleimg . '"';
		$replimg .= $styleimgstr;
	}
	//alt
	if( !empty($imgdata['alt']) ) {
		$replimg .= " alt=\"" . $imgdata['alt'] . "\"";
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
		if ($imgdata['desc'] == 'desc') {		//first check desc shortcuts
			$desconly = $desc;
		} elseif ($imgdata['desc'] == 'idesc') {
			$desconly = $idesc;
		} elseif ($imgdata['desc'] == 'name') {
			$desconly = $imgname;
		} elseif ($imgdata['desc'] == 'ititle') {
			$desconly = $ititle;
		} else {
			$desconly = $imgdata['desc'];        //otherwise use what the user typed in
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
	// Set link to user setting or to image itself if thumb is set and link isn't
	if (!empty($imgdata['lnk'])) {
		$link = $imgdata['lnk'];
	} elseif (!empty($imgdata['thumb'])) {
		if ((($imgdata['thumb'] == 'browse') || ($imgdata['thumb'] == 'browsepopup')) && !empty($imgdata['id'])) {
			$link = 'tiki-browse_image.php?imageId=' . $imgdata['id'];
		} else {
			$link = $browse_full_image;
		}
	}	

	// If mouseover settings are set, create java script and get original image dimensions
	if (!empty($javaset)) {
		$link = 'javascript:void()';
		$script = "\r\t" . '<script type="text/javascript" src="lib/overlib.js"></script>';
		$mouseover = " onmouseover=\"return overlib('$data',BACKGROUND,'$browse_full_image',WIDTH,'$fwidth',HEIGHT,'$fheight'";
		if ($imgdata['thumb'] == 'mousesticky') {
		$mouseover .= ',STICKY';
		}
		$mouseover .= ");\" onmouseout=\"nd();\"";
	}
	
	// Set other link-related attributes
	if ($link) {
		
		// target
		$imgtarget= '';
		if (($prefs['popupLinks'] == 'y' && (preg_match('#^([a-z0-9]+?)://#i', $link) || preg_match('#^www\.([a-z0-9\-]+)\.#i',$link))) || ($imgdata['thumb'] == 'popup') || ($imgdata['thumb'] == 'browsepopup')) {
			if (!empty($javaset) || ($imgdata['thumb'] == 'shadowbox')) {
				$imgtarget= '';
			} else {
				$imgtarget = ' target="_blank"';
			}
		}
		
		// rel
		if (!empty($imgdata['rel'])) {
			$linkrel = ' rel="'.$imgdata['rel'].'"';
		} elseif ($imgdata['thumb'] == 'shadowbox') {
			$linkrel = ' rel="shadowbox; type=img"';
		}
		
		// title
		if ( !empty($imgtitle) ) {
			$linktitle = $imgtitle;
		} else {
			$linktitle = '';
		}
		
		//Final link string
		$replimg = $script . "\r\t" . '<a href="' . $link . '" class="internal"' . $linkrel . $imgtarget . $linktitle . $mouseover . $sticky . '>' . $replimg . '</a>';
	}
	
	//Add link string to rest of string
	$repl .= $replimg;

/////////////////////////////////  Create enlarge button, description and their divs//////////////////////////////////////////////////////////////////////////
	//Set enlarge button link
	if (!empty($imgdata['button'])) {
		if (empty($link) || (!empty($link) && !empty($javaset))) {
			if ((($imgdata['button'] == 'browse') || ($imgdata['button'] == 'browsepopup')) && !empty($imgdata['id']))  {
				$link_button = 'tiki-browse_image.php?imageId=' . $imgdata['id'];
			} else {
				$link_button = $browse_full_image;
			}
		} else {
			$link_button = $link;
		}
	}

	//Set button rel
	if (!empty($imgdata['button'])) {
		if (empty($linkrel) && (empty($imgdata['thumb']) || !empty($javaset))) {	
			if ($imgdata['button'] == 'shadowbox') {
				$linkrel_button = ' rel="shadowbox; type=img"';
			} else {
				$linkrel_button = '';
			}
		} else {
			$linkrel_button = $linkrel;
		}
	}
	//Set button target
	if (!empty($imgdata['button'])) {
		if (empty($imgtarget) && (empty($imgdata['thumb']) || !empty($javaset))) {
			if (($imgdata['button'] == 'popup') || ($imgdata['button'] == 'browsepopup')) {
				$imgtarget_button = ' target="_blank"';
			} else {
				$imgtarget_button = '';
			}
		} else {
			$imgtarget_button = $imgtarget;
		}
	}
	
	//Start div that goes around button and description if these are set
	if ((!empty($imgdata['button'])) || (!empty($imgdata['desc'])) || (!empty($imgdata['styledesc']))) {
		$repl .= "\r\t" . '<div class="mini" style="width:' . $width . 'px;';
		if( !empty($imgdata['styledesc']) ) {
			if (($imgdata['styledesc'] == 'left') || ($imgdata['styledesc'] == 'right')) {
				$repl .= 'text-align:' . $imgdata['styledesc'] . '">';
			} else {
			$repl .= $imgdata['styledesc'] . '">';
			}
		} elseif ((!empty($imgdata['button'])) && (empty($imgdata['desc']))) {
			$repl .= $descheightdef . '">';
		} else {
			$repl .= '">';
		}
		
		//Start description div that also includes enlarge button div
		$repl .= "\r\t\t" . '<div class="thumbcaption" style="padding-top:2px" >';
		
		//Enlarge button div and link string (innermost div)
		if (!empty($imgdata['button'])) {
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

//////////////////////////////////////////Wrap in overall div that includes image if stylebox or button is set/////////////////////////////////////
	//Make the div surrounding the image 2 pixels bigger than the image
	$boxwidth = $width + 2;
	$boxheight = $height + 2;
	//Need a box if either button, desc or stylebox is set
	if (!empty($imgdata['button']) || !empty($imgdata['desc']) || !empty($imgdata['stylebox'])) {
		//first set stylebox string if style box is set
		if (!empty($imgdata['stylebox'])) {				//create strings from shortcuts first
			if ($imgdata['stylebox'] == 'y') {
				$stylebox = $borderboxdef;
			} elseif (preg_match("/^[borde ]*[| ]*right[| ]*[borde ]*$/", $imgdata['stylebox'])) {
				$stylebox = 'float:right;';
			} elseif (preg_match("/^[borde ]*[| ]*left[| ]*[borde ]*$/", $imgdata['stylebox'])) {
				$stylebox = 'float:left;';
			} elseif (preg_match("/^[borde ]*[| ]*center[| ]*[borde ]*$/", $imgdata['stylebox'])) {
				$stylebox = $center;
			} 
			if (preg_match("/^[righleftcn ]*[| ]*border[| ]*[righleftcn ]*$/", $imgdata['stylebox'])) {
				$stylebox .= $borderboxdef;
			}
			//If user input something other than a shortcut, use the input
			if (empty($stylebox)) {
				$styleboxinput = $imgdata['stylebox'] . ';';
			}
			if (empty($imgdata['button']) && empty($imgdata['desc']) && empty($styleboxinput)) {
				$styleboxplus = $stylebox . '; width:' . $boxwidth . 'px; height:' . $boxheight . 'px';
			} elseif (!empty($styleboxinput)) {
				$styleboxplus = $styleboxinput;
			} else {
				$styleboxplus = $stylebox . $descdef . ' width:' . $boxwidth . 'px';
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
	if($_REQUEST['mode'] == 'mobile') {
		$repl = '{img src=' . $imgdata['src'] . "\"}\n<p>" . $imgdata['desc'] . '</p>'; 
	}
	return $repl;
}

?>
