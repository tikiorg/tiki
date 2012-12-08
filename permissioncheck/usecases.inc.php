<?php

// list of usecases and assumed permissions
// name of usecase equals name of subdirectory of permissioncheck/
// $uc_perms_file refers always to check.php in the corresponding subdir

$default_file_name = 'check.php';

$uc_txt_file = 'usecases.txt'; // legacy
$uc_file = 'usecases.bin';
$file = fopen($uc_file, 'r') or exit('Unable to open file ' . $uc_file . '!');

while (!feof($file)) {
	$line_of_file_orig = fgets($file);
	if ($line_of_file_orig == '') {
		$dummy = true;
	} else {
	  $line_of_file_mod=str_replace(':', ' ', $line_of_file_orig);
	  list($usecase, $def_subdir_perm, $def_file_perm, $def_subdir_write_perm, $def_file_write_perm) = sscanf($line_of_file_mod, '%s %d %d %d %d');
	  $uc_perms_subdir[$usecase] = $def_subdir_perm;
	  $uc_perms_file[$usecase] = $def_file_perm;
	  $uc_perms_write_subdir[$usecase] = $def_subdir_write_perm; //redundant (2012-11-10), for later usage
	  $uc_perms_write_file[$usecase] = $def_file_write_perm; //redundant (2012-11-10), for later usage
	}
}

fclose($file);

//$usecase="paranoia";
//$uc_perms_subdir[$usecase]=700;
//$uc_perms_file[$usecase]=600;

//$usecase="paranoia-suphp";
//$uc_perms_subdir[$usecase]=701;
//$uc_perms_file[$usecase]=600;

//$usecase="mixed";
//$uc_perms_subdir[$usecase]=770;
//$uc_perms_file[$usecase]=660;

//$usecase="risky";
//$uc_perms_subdir[$usecase]=775;
//$uc_perms_file[$usecase]=664;
