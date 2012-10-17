<?php

// list of usecases and assumed permissions
// name of usecase equals name of subdirectory of permissioncheck/
// $uc_perms_file refers always to check.php in the corresponding subdir

$default_file_name="check.php";

$usecase="paranoia";
$uc_perms_subdir[$usecase]=700;
$uc_perms_file[$usecase]=600;

$usecase="paranoia-suphp";
$uc_perms_subdir[$usecase]=701;
$uc_perms_file[$usecase]=600;

$usecase="mixed";
$uc_perms_subdir[$usecase]=770;
$uc_perms_file[$usecase]=660;

$usecase="risky";
$uc_perms_subdir[$usecase]=775;
$uc_perms_file[$usecase]=664;

?>
