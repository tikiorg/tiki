<?php
session_start();
//random_number.php
$img_number = imagecreate(95,30);
$white = imagecolorallocate($img_number,255,255,255);
$black = imagecolorallocate($img_number,0,0,0);
$grey_shade = imagecolorallocate($img_number,204,204,204);

imagefill($img_number,0,0,$grey_shade);

srand(time());
$number = get_random();

$_SESSION['random_number']=$number;

for ($i=0;$i<5;$i++)
{
  Imagestring($img_number,5,10+17*$i+rand(0,2),1+rand(0,10),substr($number,$i,1),$black);
}

header("Content-type: image/jpeg");
imagejpeg($img_number);

function get_random()
{
    # return a 5 digit value
    return rand(10000,99999);
}
?>