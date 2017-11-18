<?php
// use file
// permissioncheck/permission_granted.bin
// to enable/disable permission check if shell is not available
// copy from yes.bin if you want to run permission check
// copy from no.bin in production mode
// the recommended method is to run
// prepare_permissioncheck.sh enable
// or
// prepare_permissioncheck.sh disable
// in Tiki's document root
//
//$permission_granted="yes";
//$permission_granted="no";
$permission_granted = "notdefinedyet";
$permission_grant_control_file = 'permission_granted.bin';
$file = fopen($permission_grant_control_file, 'r') or exit('Unable to open file ' . $permission_grant_control_file . '!');
$permission_granted = fgets($file);

fclose($file);

// quick and dirty: override here
//$permission_granted="yes\n";
