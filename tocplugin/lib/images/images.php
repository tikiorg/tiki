<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/* Select the right library for images manipulation
 * We handle gd and imagemagick 1.x and 2.x
 */
require_once('tiki-setup.php');

// Auto-detect php extension to use as image lib
//   This assumes imagick is better than gd (which does not handle transparency in gray PNG), so try to find it first
//
  $detected_lib = '';
if ( class_exists('Imagick') ) {
	$detected_lib = 'imagick_new'; // Imagick 2.x
} elseif ( function_exists('imagick_rotate') ) {
	$detected_lib = 'imagick_old'; // Imagick 1.x
} elseif ( function_exists('gd_info') ) {
	$detected_lib = 'gd'; // GD
} 

if ($detected_lib != '') {
  // Load the detected lib
  require_once('lib/images/'.$detected_lib.'.php');
}
