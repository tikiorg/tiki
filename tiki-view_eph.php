<?php
// Initialization
require_once('tiki-setup.php');
include_once('lib/ephemerides/ephlib.php');


if(!isset($_REQUEST["ephId"])) {
  die;
}

$info = $ephlib->get_eph($_REQUEST["ephId"]);
$type=&$info["filetype"];
$file=&$info["filename"];
$content=&$info["data"];

header("Content-type: $type");
header( "Content-Disposition: inline; filename=$file" );
echo "$content";    
?>