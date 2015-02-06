<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/*Admin default and mandatory settings (must be set by changing this fle or using plugin alias). Default will be used if not overridden
by user. Mandatory will override user settings. Examples below set parameters depending on whether the image is in an article, a module, or
whether mobile mode is set, etc.*/
//Uncomment the following line to set the default parameter. Later items have priority. To override align default, put align parameter first
//	$imgdata['default'] = 'default ? max = 200, align = right, styledesc = text-align: center; section_cms_article ? max= 400, width= , height=';
// Uncomment the following line to set the default parameter. Force certain max and ignore any specified width or height. Later items have priority
//	$imgdata['mandatory'] = 'section_cms_article ? max = 400; module_* ? max = 150, width= , height=; mode_mobile ? max = 150, width= , height=;';

//////////////////////////////////////////////////Function for processing default and mandatory parameters//////////////////////////////////////
//function calls are just below function
if (!function_exists('apply_default_and_mandatory')) {
	function apply_default_and_mandatory($imgdata, $default)
	{
		$smarty = TikiLib::lib('smarty');
		global $section;
		$imgdata[$default] = trim($imgdata[$default]) . ';'; // trim whitespace and ensure at least one semicolon
		$img_conditions_array = explode(';', $imgdata[$default]); // conditions separated by semicolons
		if ( !empty($img_conditions_array) ) {
			foreach ($img_conditions_array as $key => $var) { // for each condition
				if ( !empty($var) ) {
					$img_condition = explode('?', $var); // condition separated from parameters by question mark
					if ( !empty($img_condition) ) {
						$img_condition_name = trim($img_condition[0]);
						if ( !empty($img_condition[1]) ) { // if there is at least one parameter
							$img_condition[1] = trim($img_condition[1]) . ',';	// at least one comma
							$img_parameters_array = explode(',', $img_condition[1]); // separate multiple parameters
							if ( !empty($img_parameters_array) ) {  // if a parameter has been extracted
								foreach ($img_parameters_array as $param_key => $param_var) {	// for each parameter
									if ( !empty($param_var) ) {	// if a parameter exists
										$img_parameter_array = explode('=', trim($param_var)); // separate parameters and values
										if ( !empty($img_parameter_array[0]) ) {  // if a parameter with a value has been extracted

											$img_condition_status = false;	// initialise condition as not being true

											$img_condition_name = strtolower(trim($img_condition_name));
											switch ($img_condition_name) {
												case 'default':
													$img_condition_status = true; // default is always true
   													break;
												case 'mode_mobile':
													if ( isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'mobile' ) $img_condition_status = true;
    												break;
												case 'module_*':
													if ( !empty($smarty) ) {
														$image_module_params = $smarty->getTemplateVars('module_params');
														if ( !empty($image_module_params) ) $img_condition_status = true;
													}
   													break;
												case 'section_*':
													if ( !empty($section) ) $img_condition_status = true;
    												break;
												case 'section_cms_article':
													if ( !empty($section) ) {
														if ( $section == 'cms' ) {
															if ( !empty($smarty) ) {
																$image_article_type = $smarty->getTemplateVars('type');
																if ( !empty($image_article_type) ) {
																	if ( strtolower(trim($image_article_type)) == 'article' ) $img_condition_status = true;
																} // if (!empty($image_article_type))
															} // if (!empty($smarty))
														}
													}
	    											break;
												case 'section_cms_review':
													if ( !empty($section) ) {
														if ( $section == 'cms' ) {
															if ( !empty($smarty) ) {
																$image_article_type = $smarty->getTemplateVars('type');
																if ( !empty($image_article_type) ) {
																	if ( strtolower(trim($image_article_type)) == 'review' ) $img_condition_status = true;
																} // if (!empty($image_article_type))
															} // if (!empty($smarty))
														}
													}
		    										break;
												case 'section_cms_event':
													if ( !empty($section) ) {
														if ( $section == 'cms' ) {
															if ( !empty($smarty) ) {
																$image_article_type = $smarty->getTemplateVars('type');
																if ( !empty($image_article_type) ) {
																	if ( strtolower(trim($image_article_type)) == 'event' ) $img_condition_status = true;
																} // if (!empty($image_article_type))
															} // if (!empty($smarty))
														}
													}
   													break;
												case 'section_cms_classified':
													if ( !empty($section) ) {
														if ( $section == 'cms' ) {
															if ( !empty($smarty) ) {
																$image_article_type = $smarty->getTemplateVars('type');
																if ( !empty($image_article_type) ) {
																	if ( strtolower(trim($image_article_type)) == 'classified' ) $img_condition_status = true;
																} // if (!empty($image_article_type))
															} // if (!empty($smarty))
														}
													}
    												break;
											} // switch ($img_condition_name)

											if ( $img_condition_status != true ) {
												// if match not found yet, examine more specific conditions
												if ( !empty($section) ) {	// if we have a section name
													if ( substr($img_condition_name, 0, 8) == 'section_' ) {
														if ( strlen($img_condition_name) > 8 ) {
															$img_condition_part = substr($img_condition, 8); // get part after "section_"
															$img_condition_part = strtolower($img_condition_part);
															$img_condition_part = trim(strtr($img_condition_part, '_', ' ')); // replace underscore with spaces
															if ( $section == $img_condition_part ) $img_condition_status = true;
														} // if ( length($img_condition_name) > 8 )
													} // if ( substr($img_condition_name,0,8) == "section_" )
												} // if ( !empty($section) )
											}

											if ( $img_condition_status == true ) {
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
											} // if ( $img_condition_status == true )

										} // if ( !empty($img_parameter_array[0] )
									} // if a parameter exists
								} // for each parameter
							} // if ( !empty($img_parameters_array) )
						} // if ( !empty($img_condition[1]) )
					}  // if ( !empty($img_condition) )
				} // if ( !empty($var) )
			} // for each condition
		} // if ( !empty($img_conditions_array) )
	return $imgdata;
	}
}
////////////////////////////////////End of function for processing default and mandatory parameters////////////////////
