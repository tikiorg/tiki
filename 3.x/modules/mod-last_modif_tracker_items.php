<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

// in wikipages, add params like this:    ,trackerId=...,name=...
// in module list, add params like this in params fiels:  trackerId=...&name=...
// name is the name of the tracker field to be displayed (should be descriptive)
$module_params['sort_mode'] = 'lastModif_desc';
include ('modules/mod-last_tracker_items.php');

