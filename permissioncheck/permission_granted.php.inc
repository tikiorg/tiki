<?php
// use file
// permissioncheck/permission_granted.txt
// to enable/disable permission check if shell is not available
// set to "yes" if you want to run permission check
// set to "no" in production mode
// the recommended method is to run
// prepare_permissioncheck.sh enable
// or
// prepare_permissioncheck.sh disnable
// in Tiki's document root
//
//$permission_granted="yes";
//$permission_granted="no";
$permission_txt_file = 'permission_granted.txt';
$file = fopen($permission_txt_file, 'r') or exit('Unable to open file ' . $permission_txt_file . '!');
$permission_granted = fgets($file);

fclose($file);

// quick and dirty: override here
//$permission_granted="yes\n";
