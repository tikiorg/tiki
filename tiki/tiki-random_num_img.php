<?php
session_start();
//random_number.php
$img_number = imagecreate(70,20);
$white = imagecolorallocate($img_number,255,255,255);
$black = imagecolorallocate($img_number,0,0,0);
$grey_shade = imagecolorallocate($img_number,204,204,204);

imagefill($img_number,0,0,$grey_shade);

$number = get_random();
$_SESSION['random_number']=$number;
Imagestring($img_number,5,10,1,$number,$black);

header("Content-type: image/jpeg");
imagejpeg($img_number);

function get_random()
{
    srand(time());
    $max = getrandmax();
    return rand(1,$max) + rand(1,$max) ;
}
?>