<?php
/* Select the right library for images manipulation */
/* we choose from :
/*  o gd
/*  o imagemagick
*/
require_once('tiki-setup.php');

$extensions = get_loaded_extensions();
$prefered_lib = $prefs['gal_use_lib'];

switch ( $prefered_lib ) {
  case 'imagick':
    if ( in_array('imagick', $extensions ) ) {
     $funcs = get_extension_funcs('imagick');
       if ( in_array('imagick_blob2image', $funcs) ) {
         require_once('lib/images/imagick_old.php');
         break;
       } else {
         require_once('lib/images/imagick_new.php');
         break;
       }
    };
  case 'gd':
    if ( in_array('gd', $extensions ) ) {
       require_once('lib/images/gd.php');
       break;
    };
  default:
    $smarty->assign('msg', tra('No graphic library found.'));
    $smarty->display('error.tpl');
    die();
}
?>
