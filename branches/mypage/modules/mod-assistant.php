<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

// This is the start of a guideance wizard
// for 1.8 it will help people with the :: menu options
// Damian aka damosoft aka TikiGod

?>
