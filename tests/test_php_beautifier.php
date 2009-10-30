<?php
// This is the test for a php beautifier
//space
require_once('tiki-setup.php');
require_once ('tiki-setup.php');

//array
list($a,$b,$c) = array('','','');
list($a, $b, $c) = array('', '' ,'');
$a = array($k=>'1', $l=>'2');

//block
if ($a)
	$b = 1;
if ($a)
{
	$b = 1;
}
if ($a){$b = 1;}
//vim mark
function a() // {{{
{
	$b = 1;
	return ( $b );
}

//space and operator
$a=$b+$c-($b-$c);
$a=$b.$c;
$a=fct($b,$c);
$a = fct ($a, $b );
// empty line

//space/tab identation
if ($a) {
   $b = 1;
	return $b;
}

// Comment
function fct($a) { //comment
//comment
	$a = $b;
	/* coment */
}
// long line
	function insert_image($galleryId, $name, $description, $filename, $filetype, &$data, $size, $xsize, $ysize, $user, $t_data, $t_type ,$lat=NULL, $lon=NULL, $gal_info=NULL) {
	}

//string
$a = "constant";
$sql = "SELECT `id`, `name` from `people` "
     . "WHERE `name`='Fred' OR `name`='Susan'";

// class
class a
{
	//content
}

// global - for ease grep
global $statslib; include_once ('lib/stats/statslib.php');
