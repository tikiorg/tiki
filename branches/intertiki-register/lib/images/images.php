<?php
/* Select the right library for images manipulation
 * We handle gd and imagemagick 1.x and 2.x
 */
require_once('tiki-setup.php');

// Auto-detect php extension to use as image lib
//   This assumes imagick is better than gd (which does not handle transparency in gray PNG), so try to find it first
//
if ( class_exists('Imagick') ) {
	$detected_lib = 'imagick_new'; // Imagick 2.x
} elseif ( function_exists('imagick_rotate') ) {
	$detected_lib = 'imagick_old'; // Imagick 1.x
} elseif ( function_exists('gd_info') ) {
	$detected_lib = 'gd'; // GD
} else {
	$smarty->assign('msg', tra('No graphic library found.'));
	$smarty->display('error.tpl');
	die();
}

// Load the detected lib
require_once('lib/images/'.$detected_lib.'.php');

?>
