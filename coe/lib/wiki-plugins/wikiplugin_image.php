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
 * 
 * Copyright (c) 2002-2009, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 */

/**
 * Return older-style help message, with summary and parameter options.
 *
 * @return string Summary with plugin's description and parameters.
 */
function wikiplugin_image_help() {
		return tra("Display an image using configured format. Allows presentation of all images to be changed without having to alter existing content.").":<br />~np~" . '{IMAGE(
	[ fileId="Numeric ID of an image in a File Gallery (or comma-separated list). "fileId", "id" or "src" required." ]
	[ id="Numeric ID of an image in an Image Gallery (or comma-separated list). "fileId", "id" or "src" required." ]
	[ src="Full URL to the image to display. "fileId", "id" or "src" required." ]
	[ scalesize="Maximum height or width in pixels (largest dimension is scaled). If no scalesize is given one will be attempted from default or given height or width. If scale does not match a defined scale for the gallery the full sized image is downloaded." ]
	[ height="Height in pixels." ]
	[ width="Width in pixels." ]
	[ link="Alias: lnk. Location the image should point to." ]
	[ rel="\"rel\" attribute to add to the link." ]
	[ title="Title text." ]
	[ alt="Alternate text to display if impossible to load the image." ]
	[ align="Alias:imalign. Image alignment in the page. (left, right)" ]
	[ block="Whether to block items from flowing next to image from the top or bottom. (top,bottom,both,none)" ]
	[ desc="Image description to display on the page." ]
	[ usemap="Name of the image map to use for the image." ]
	[ class="CSS class to apply to the image'."'".'s img tag." ]
	[ style="CSS styling to apply to the plugin. (Usually used in configuration rather than on individual images.)" ]
	[ border="Border configuration.  Values "on" and "off" control visibility, or else specify CSS styling options." ]
	[ descoptions="Description configuration.  Values "on" and "off" control visibility, or else specify CSS styling options. (Usually used in configuration rather than on individual images.)" ]
	)}{IMAGE}~/np~';
}

/**
 * Return plugin information, with description and parameter list.
 *
 * @return array
 */
function wikiplugin_image_info() {
	return array(
		'name' => tra('Image'),
		'description' => tra("Display an image.").tra(' (experimental - possible successor to img in 4.0)'),
		'prefs' => array( 'wikiplugin_image'),
		'params' => array(
			'fileId' => array(
				'required' => false,
				'name' => tra('File ID'),
				'description' => tra('Numeric ID of an image in a File Gallery (or comma-separated list). "fileId", "id" or "src" required.'),
			),
			'id' => array(
				'required' => false,
				'name' => tra('Image ID'),
				'description' => tra('Numeric ID of an image in an Image Gallery (or comma-separated list). "fileId", "id" or "src" required.'),
			),
			'src' => array(
				'required' => false,
				'name' => tra('Image Source'),
				'description' => tra('Full URL to the image to display. "fileId", "id" or "src" required.'),
			),
			'scalesize' => array(
				'required' => false,
				'name' => tra('Image size'),
				'description' => tra('Maximum height or width in pixels (largest dimension is scaled).	If no scalesize is given one will be attempted from default or given height or width.  If scale does not match a defined scale for the gallery the full sized image is downloaded.'),
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
			'link' => array(
				'required' => false,
				'name' => tra('Link'),
				'description' => tra('Alias: lnk. Location the image should point to.'),
			),
			'rel' => array(
				'required' => false,
				'name' => tra('Link Relation'),
				'description' => tra('"rel" attribute to add to the link.'),
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
			'align' => array(
				'required' => false,
				'name' => tra('Alignment'),
				'description' => tra('Alias:imalign. Image alignment in the page. (left, right)'),
			),
			'block' => array(
				'required' => false,
				'name' => tra('Alignment'),
				'description' => tra('Whether to block items from flowing next to image from the top or bottom. (top,bottom,both,none)'),
			),
			'desc' => array(
				'required' => false,
				'name' => tra('Description'),
				'description' => tra('Image description to display on the page.'),
			),
			'usemap' => array(
				'required' => false,
				'name' => tra('Image Map'),
				'description' => tra('Name of the image map to use for the image.'),
			),
			'class' => array(
				'required' => false,
				'name' => tra('CSS Class'),
				'description' => tra('CSS class to apply to the image'."'".'s img tag. (Usually used in configuration rather than on individual images.)'),
			),
			'style' => array(
				'required' => false,
				'name' => tra('CSS Style'),
				'description' => tra('CSS styling to apply to the plugin. (Usually used in configuration rather than on individual images.)'),
			),
			'border' => array(
				'required' => false,
				'name' => tra('Border options'),
				'description' => tra('Border configuration.  Values "on" and "off" control visibility, or else specify CSS styling options.'),
			),
			'descoptions' => array(
				'required' => false,
				'name' => tra('Description options'),
				'description' => tra('Description configuration.	Values "on" and "off" control visibility, or else specify CSS styling options. (Usually used in configuration rather than on individual images.)'),
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
function wikiplugin_image( $data, $params, $offset, $parseOptions='' ) {
	global $tikidomain, $prefs, $section, $smarty;

	$imgdata = array();
	
	$imgdata["fileId"] = '';
	$imgdata["id"] = '';
	$imgdata["src"] = '';
	$imgdata["scalesize"] = '';
	$imgdata["height"] = '';
	$imgdata["width"] = '';
	$imgdata["lnk"] = '';
	$imgdata["rel"] = '';
	$imgdata["title"] = '';
	$imgdata["align"] = '';
	$imgdata["imalign"] = '';
	$imgdata["block"] = '';
	$imgdata["desc"] = '';
	$imgdata["alt"] = '';
	$imgdata["usemap"] = '';
	$imgdata["class"] = '';
	$imgdata["style"] = '';
	$imgdata["border"] = '';
	$imgdata["descoptions"] = '';
	$imgdata["default"] = '';
	$imgdata["mandatory"] = '';

	/*
	** Define default parameters here.
	*/
	
	// The following causes the image to be centered.
	$imgdata["style"] = 'text-align:center';
	// The following is the default border.  "border" is name of parameter, which might modify "borderstyle".
	$imgdata["border"] = 'on';
	$imgdata["borderstyle"] = 'border:3px double #292929; padding:.1cm; font-size:12px; line-height:1.5em; margin-left:4px';
	// The following is the default caption options.	"descoptions" is name of parameter, which might modify "captionstyle".
	$imgdata["captionstyle"] = 'text-align:center width:100% font-size:0.9em';

	// The following are local defaults copied and modified from above.  Later items have priority.
	$imgdata["default"] = 'default ? scalesize = 200, align = right, style = text-align:center; section_cms_article ? scalesize = 400, width= , height=';
	// Force certain scalesize and ignore any specified width or height.	Later items have priority.
	$imgdata["mandatory"] = 'section_cms_article ? scalesize = 400; module_* ? scalesize = 150, width= , height=; mode_mobile ? scalesize = 150, width= , height=;';

	/*
	** Start processing... first defaults, then given parameters, then mandatory settings.
	*/

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
													case "fileid":
													case "fileId":
														$imgdata["fileId"] = trim($img_parameter_array[1]);
														break;
													case "id":
														$imgdata["id"] = trim($img_parameter_array[1]);
														break;
													case "src":
														$imgdata["src"] = trim($img_parameter_array[1]);
														break;
													case "scalesize":
														$imgdata["scalesize"] = trim($img_parameter_array[1]);
														break;
													case "height":
														$imgdata["height"] = trim($img_parameter_array[1]);
														break;
													case "width":
														$imgdata["width"] = trim($img_parameter_array[1]);
														break;
													case "lnk":
													case "link":
														$imgdata["lnk"] = trim($img_parameter_array[1]);
														break;
													case "rel":
														$imgdata["rel"] = trim($img_parameter_array[1]);
														break;
													case "title":
														$imgdata["title"] = trim($img_parameter_array[1]);
														break;
													case "align":
														$imgdata["align"] = trim($img_parameter_array[1]);
														break;
													case "imalign":
														$imgdata["imalign"] = trim($img_parameter_array[1]);
														break;
													case "block":
														$imgdata["block"] = trim($img_parameter_array[1]);
														break;
													case "desc":
														$imgdata["desc"] = trim($img_parameter_array[1]);
														break;
													case "alt":
														$imgdata["alt"] = trim($img_parameter_array[1]);
														break;
													case "usemap":
														$imgdata["usemap"] = trim($img_parameter_array[1]);
														break;
													case "class":
														$imgdata["class"] = trim($img_parameter_array[1]);
														break;
													case "style":
														$imgdata["style"] = trim($img_parameter_array[1]);
														break;
													case "border":
														$imgdata["border"] = trim($img_parameter_array[1]);
														break;
													case "descoptions":
														$imgdata["descoptions"] = trim($img_parameter_array[1]);
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
													case "fileid":
													case "fileId":
														$imgdata["fileId"] = trim($img_parameter_array[1]);
														break;
													case "id":
														$imgdata["id"] = trim($img_parameter_array[1]);
														break;
													case "src":
														$imgdata["src"] = trim($img_parameter_array[1]);
														break;
													case "scalesize":
														$imgdata["scalesize"] = trim($img_parameter_array[1]);
														break;
													case "height":
														$imgdata["height"] = trim($img_parameter_array[1]);
														break;
													case "width":
														$imgdata["width"] = trim($img_parameter_array[1]);
														break;
													case "lnk":
													case "link":
														$imgdata["lnk"] = trim($img_parameter_array[1]);
														break;
													case "rel":
														$imgdata["rel"] = trim($img_parameter_array[1]);
														break;
													case "title":
														$imgdata["title"] = trim($img_parameter_array[1]);
														break;
													case "align":
														$imgdata["align"] = trim($img_parameter_array[1]);
														break;
													case "imalign":
														$imgdata["imalign"] = trim($img_parameter_array[1]);
														break;
													case "block":
														$imgdata["block"] = trim($img_parameter_array[1]);
														break;
													case "desc":
														$imgdata["desc"] = trim($img_parameter_array[1]);
														break;
													case "alt":
														$imgdata["alt"] = trim($img_parameter_array[1]);
														break;
													case "usemap":
														$imgdata["usemap"] = trim($img_parameter_array[1]);
														break;
													case "class":
														$imgdata["class"] = trim($img_parameter_array[1]);
														break;
													case "style":
														$imgdata["style"] = trim($img_parameter_array[1]);
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

	if ( empty($imgdata["fileId"]) and empty($imgdata["fileid"]) and empty($imgdata["id"]) and empty($imgdata["src"]) ) {
		return "''no image''";
	}

	if ( ! ( empty($imgdata["fileId"]) Xor empty($imgdata["fileid"]) Xor empty($imgdata["id"]) Xor empty($imgdata["src"]) ) ) {
		return "''IMAGE: Only use one of fileId, id, or src.''";
	}
	
	// Support both 'fileId' and 'fileid' syntax
	if ( (!empty($imgdata['fileid'])) && empty($imgdata['fileId']) )
		$imgdata['fileId'] = $imgdata['fileid'];

	// If a comma-separated list of filegal images was given, show them.
	if( ( !empty($imgdata["fileId"]) ) && ( strpos($imgdata["fileId"], ',') !== FALSE ) ) {
		$repl = "";
		$id_list = array();
		$id_list = explode(',',$imgdata["fileId"]);
		if( empty($params["align"]) ) $params["align"] = "left";
		$params["fileid"] = "";
		foreach ($id_list as $i => $value) {
			$params["fileId"] = trim($value);
			$repl .= wikiplugin_image( $data, $params, $offset, $parseOptions );
		}
		$repl = "\n\r<br style=\"clear:both\" />\r" . $repl . "\n\r<br style=\"clear:both\" />\r";
		return $repl; // return the result of those images
	}

	// If a comma-separated list of imagegal images was given, show them.
	if( ( !empty($imgdata["id"]) ) && ( strpos($imgdata["id"], ',') !== FALSE ) ) {
		$repl = "";
		$id_list = array();
		$id_list = explode(',',$imgdata["id"]);
		if( empty($params["align"]) ) $params["align"] = "left";
		foreach ($id_list as $i => $value) {
			$params["id"] = trim($value);
			$repl .= wikiplugin_image( $data, $params, $offset, $parseOptions );
		}
		$repl = "\n\r<br style=\"clear:both\" />\r" . $repl . "\n\r<br style=\"clear:both\" />\r";
		return $repl; // return the result of those images
	}

	// If a file ID was given expand it into a URL
	if ( !empty($imgdata['fileId']) ) {
		$imgdata['src'] = "tiki-download_file.php?fileId=" . $imgdata['fileId'] . "&preview=y";
	}

	// If an image ID was given expand it into a URL
	if ( !empty($imgdata['id']) ) {
		$imgdata['src'] = "show_image.php?id=" . $imgdata['id'];
	}
	
	// Support both 'imalign' and 'align' syntax
	if ( (!empty($imgdata['imalign'])) && empty($imgdata['align']) )
		$imgdata['align'] = $imgdata['imalign'];
	
	// Support both 'link' and 'lnk' syntax
	if ( (!empty($imgdata['link'])) && empty($imgdata['lnk']) )
		$imgdata['lnk'] = $imgdata['link'];

	// Enable, disable, or configure the border style
	if ( (!empty($imgdata['border'])) ) {
		if ( $imgdata['border'] != 'on' ) {
			if ( $imgdata['border'] == 'off' ) {
				$imgdata['borderstyle'] = ''; // Set to off so emit no style.  CSS file might do something.
			} else {
				$imgdata['borderstyle'] .= ';' . $imgdata['border']; // Append specified style alterations
			}
		} // if border is not 'on'
	} // if border specified

	// Enable, disable, or configure the caption style
	if ( (!empty($imgdata['descoptions'])) ) {
		if ( $imgdata['descoptions'] != 'on' ) {
			if ( $imgdata['descoptions'] == 'off' ) {
				$imgdata['captionstyle'] = ''; // Set to off so emit no style.	CSS file might do something.
			} else {
				$imgdata['captionstyle'] .= ';' . $imgdata['descoptions']; // Append specified style alterations
			}
		} // if caption is not 'on'
	} else {
		$imgdata['descoptions'] = 'on';
	} // if no description options, turn it on

	// If clear was specified, add it to the CSS style
	if ( !empty($imgdata['clear']) )
		$imgdata['style'] = 'clear:' . $imgdata['clear'] . ';' . $imgdata['style'];

	// Clean up src URLs
	if (stristr(str_replace(' ', '', $imgdata["src"]),'javascript:')) {
		$imgdata["src"]  = '';
	}
	if ($tikidomain && !preg_match('|^https?:|', $imgdata['src'])) {
		$imgdata["src"] = preg_replace("~img/wiki_up/~","img/wiki_up/$tikidomain/",$imgdata["src"]);
	}
	if (strstr($imgdata["src"],'javascript:')) {
		$imgdata["src"]  = '';
	}

	// Handle absolute links (e.g. to send a newsletter with images that remain on the tiki site)

	$absolute_links = (!empty($parseOptions['absolute_links'])) ? $parseOptions['absolute_links'] : false;
	if ( (!empty($imgdata['src'])) && $absolute_links && ! preg_match('|^[a-zA-Z]+:\/\/|', $imgdata['src']) ) {
		global $base_host, $url_path;
		$imgdata["src"] = $base_host.( $imgdata["src"][0] == '/' ? '' : $url_path ).$imgdata["src"];
	}
	$browse_full_image = $imgdata["src"]; // URL of original image without sizing additions which come later.


	$scalesize = 0; // Less than one means no scaling
	if( !empty($imgdata["scalesize"]) ) {
		if(ctype_digit($imgdata["scalesize"])) {
			$scalesize = (int)$imgdata["scalesize"];
		}
	}

	// Several sections dealing with image dimension.

	// If a width or height were specified choose the largest as the scalesize
	if( (!empty($imgdata["width"])) and (ctype_digit($imgdata["width"])) ) $scalesize = $imgdata["width"];
	if( (!empty($imgdata["height"])) and (ctype_digit($imgdata["height"])) ) {
		if( $imgdata["width"] < $imgdata["height"]) $scalesize = $imgdata["height"];
	}

	// Is filegals enabled?  Need to know for following section.
	$imgdata_dim = '';
	if ( $prefs['feature_filegals_manager'] == 'y' ) {
		global $detected_lib;
		include_once('lib/images/images.php');
	} else {
		$detected_lib = '';
	}

	// If image is from a file gallery then arbitrary width and height are supported.
	if ( $detected_lib != '' && ereg('^.*tiki-download_file.php\?', $imgdata["src"]) ) {
		// If an image lib has been detected and if we are using an image from a file gallery,
		//	 then also resize the image server-side, because it will generally imply less data to download from the user
		//	 (i.e. speed up the page download) and a better image quality (browser resize algorithms are quick but bad)
		//
		//	 Note: ctype_digit is used to ensure there is only digits in width and height strings (e.g. to avoid '50%', ...)
		//
		if ( (int)$imgdata["width"] > 0 && ctype_digit($imgdata["width"]) ) $imgdata["src"] .= '&amp;x='.$imgdata["width"];
		if ( (int)$imgdata["height"] > 0 && ctype_digit($imgdata["height"]) ) $imgdata["src"] .= '&amp;y='.$imgdata["height"];
		if( $scalesize > 0 ) {
			if( empty($imgdata["width"]) && empty($imgdata["height"]) ) {
				$imgdata["src"] .= "&max=" . $scalesize;
			}
		}
		// If we don't have a description or data for a caption, try to use image's description.
		if( $imgdata["descoptions"] != "off" ) {
			if( empty($imgdata["desc"]) and empty($data) ) {
				global $tikilib;

				$image_file_info = $tikilib->get_file($imgdata["fileId"]);
				if( !empty($image_file_info["description"]) ) $imgdata["desc"] = $image_file_info["description"];
			}
		}
	}
	if ( !empty($imgdata["width"]) ) $imgdata_dim .= ' width="' . $imgdata["width"] . '"';
	if ( !empty($imgdata["height"]) ) $imgdata_dim .= ' height="' . $imgdata["height"] . '"';

	// Things to do for images in image galleries
	if ( ereg('^.*show_image.php\?', $imgdata["src"]) ) {
		// If this is an image gallery URL but we don't know its image ID, try to get the image ID.
		if( empty($imgdata["id"]) ) {
			$imgdata["id"] = preg_replace('~^.*show_image.php\?id=~',"",$imgdata["src"]);
			$imgdata["id"] = preg_replace('~[&].*~',"",$imgdata["id"]);
		}
		// For image galleries, try to determine width and height of image if both have not been provided
		if( empty($imgdata["width"]) or empty($imgdata["height"]) ) {
			global $imagegallib;
			include_once('lib/imagegals/imagegallib.php');

			if (isset($original) && $original == 'y') {
				$imagedata = $imagegallib->get_image($imgdata["id"], 'o');
				$info = $imagegallib->get_image_info($imgdata["id"], 'o');
				$scalesize = 0;
			} else {
				if( (!empty($imgdata["width"])) or (!empty($imgdata["height"])) ) {
					$imagedata = $imagegallib->get_image($imgdata["id"], 's', $imgdata["width"], $imgdata["height"]);
					$info = $imagegallib->get_image_info($imgdata["id"], 's', $imgdata["width"], $imgdata["height"]);
				} else {
					if( !empty($scalesize) ) {
						$imagedata = $imagegallib->get_image($imgdata["id"], 's', $scalesize);	// if only scalesize, pass that as width.
						$info = $imagegallib->get_image_info($imgdata["id"], 's', $scalesize);	// if only scalesize, pass that as width.
					} else {
						$imagedata = $imagegallib->get_image($imgdata["id"], 's');
						$info = $imagegallib->get_image_info($imgdata["id"], 's');
					}
				}
				if (empty($info)) {
					$imagedata = $imagegallib->get_image($imgdata["id"], 'o');
					$info = $imagegallib->get_image_info($imgdata["id"], 'o');
					// $scalesize = 0;	// In this tool don't override scalesize
					$original = 'y';
				} else {
					// else no scaled image yet so if no scale size defined get the default
					if( empty($scalesize) ) $scalesize = $imagegallib->get_gallery_default_scale($info['galleryId']);												
				}
			}
		} // if( empty($imgdata["width"]) or empty($imgdata["height"])
		// As this is in an image gallery, if there is a scalesize then use it.
		if( $scalesize > 0 ) {
			$imgdata["src"] .= "&scalesize=" . $scalesize;
		}
		// If we don't have a description or data for a caption, try to use image's description.
		if( $imgdata["descoptions"] != "off" ) {
			if( empty($imgdata["desc"]) and empty($data) ) {
				if( !empty($info["description"]) ) $imgdata["desc"] = $info["description"];
			}
		}
	} // if ( ereg('^.*show_image.php\?', $imgdata["src"]) ) 

	// Start the image and decorations.
	
	// Create the HTML img tag.

	$replimg = '<img src="' . $imgdata["src"]. '"';
	$replimg .= ' border="0" ' . $imgdata_dim;

	if( !empty($imgdata["alt"]) ) {
		$replimg .= " alt=\"" . $imgdata["alt"] . "\"";
	}
	if( !empty($imgdata["height"]) ) {
		$replimg .= " height=\"" . $imgdata["height"] . "\"";
	}
	if( !empty($imgdata["width"]) ) {
		$replimg .= " width=\"" . $imgdata["width"] . "\"";
	}
	if ( !empty($imgdata["usemap"]) ) {
		$replimg .= ' usemap="#'.$imgdata["usemap"].'"';
	}
	if ( !empty($imgdata["class"]) ) {
		$replimg .= ' class="'.$imgdata["class"].'"';
	}
	$replimg .= " />";

	// Image tag has been assembled, now see if it should be wrapped in an anchor tag.

	if ($imgdata["lnk"]) {
		$imgtarget= '';

		if ($prefs['popupLinks'] == 'y' && (preg_match('#^([a-z0-9]+?)://#i', $imgdata["lnk"]) || preg_match('#^www\.([a-z0-9\-]+)\.#i',$imgdata["lnk"]))) {
			$imgtarget = ' target="_blank"';
		}

		if ( !empty($imgdata["rel"]) )
			$linkrel = ' rel="'.$imgdata["rel"].'"';
		else
			$linkrel = '';

		if ( !empty($imgdata["title"]) )
			$linktitle = ' title="'.$imgdata["title"].'"';
		else
			$linktitle = '';
		$replimg = '<a href="'.$imgdata["lnk"].'" class="internal"'.$linkrel.$imgtarget.$linktitle.'>' . $replimg . '</a>';
	}
	$repl .= $replimg;

	// Add decorations and caption under the image.
	if( $imgdata["descoptions"] != "off" ) {
		if( !empty($imgdata["captionstyle"]) ) {
			$repl .= '<div class="mini" style="' . $imgdata["captionstyle"] . '">';
		} else {
			$repl .= '<div class="mini">';
		}
		$repl .= "\r\t\t<div class=\"thumbcaption\">";
		$repl .= "\r\t\t\t<div class=\"magnify\" style=\"float:right\">";
		// If an image ID was given, point magnify at the Image browse page
		if( !empty($imgdata["id"]) ) {
			$repl .= "<a href=\"tiki-browse_image.php?imageId=" . $imgdata["id"];
		} else {
			$repl .= "<a href=\"" . $browse_full_image; // Unknown image detail page so just point at the original image
		}
		$repl .= "\" class=\"internal\" title=\"Enlarge\"><img src=\"./img/icons2/icn_view.gif\" width=\"12\" height=\"12\" alt=\"Enlarge\" /></a></div>";
		if ( !empty($imgdata["desc"]) ) {
			$repl .= $imgdata["desc"];
		}
		if ( (!empty($imgdata["desc"])) and (!empty($data)) ) {
			$repl .= ' ';
		}
		if( !empty($data) ) {
			$repl .= $data;
		}
		$repl .= "\r\t</div>";
		$repl .= "</div>";
	} // if no desc options or they're not turned off, show desc

	if( (!empty($imgdata["align"]) ) or (!empty($imgdata["borderstyle"])) or (!empty($scalesize)) ) {
		$repl = "\r" . '<div class="img" style="' . ( (empty($imgdata["borderstyle"])) ? '' : ( $imgdata["borderstyle"] . ';' ) ) . ( (empty($imgdata["align"])) ? '' : ( 'float:' . $imgdata["align"] . ';' ) ) . ( (empty($scalesize)) ? '' : ( 'width:' . $scalesize . 'px;' ) ) . $imgdata["style"] . '">' . $repl . '</div>';
	} else {
		$repl = "\r" . '<div class="img">' . $repl . '</div>';
	}

	if( !empty($imgdata["block"]) ) {
		switch ($imgdata["block"]) {
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
		} // switch
	} // if( !empty($imgdata["block"]) )

	// Wrap the whole thing
	if($_REQUEST['mode']!="mobile") {
		$repl = "\r~hc~ IMAGE BEGIN ~/hc~\r" . "~np~" . $repl . "~/np~" . "\r~hc~ IMAGE END ~/hc~\r";
	} else {
		$repl = "{img src=".$imgdata["src"] . "\"}\n<p>" . $imgdata["desc"] . "</p>"; 
	}
	return $repl;
}

?>
