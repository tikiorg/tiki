<?php

// $Header$

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//session_start();
// hmm. to many session tweaks in setup_smarty ... we need to call this
require_once('tiki-setup.php');

if ($prefs['feature_antibot'] != 'y' && $prefs['rnd_num_reg'] != 'y') {
	die;
}


//random_number.php
$img_number = imagecreate(95, 30);
$white = imagecolorallocate($img_number, 255, 255, 255);
$black = imagecolorallocate($img_number, 0, 0, 0);
$grey_shade = imagecolorallocate($img_number, 204, 204, 204);

imagefill($img_number, 0, 0, $grey_shade);

srand (time());
$number = get_random();

$_SESSION['random_number'] = $number;

for ($i = 0; $i < 5; $i++) {
	Imagestring($img_number, 5, 10 + 17 * $i + rand(0, 2), 1 + rand(0, 10), substr($number, $i, 1), $black);
}

header ("Content-type: image/jpeg");
imagejpeg ($img_number);

function get_random() {
	# return a 5 digit value
	return rand(10000, 99999);
}

?>
