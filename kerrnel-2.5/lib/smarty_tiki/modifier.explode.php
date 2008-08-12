<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_modifier_explode($delimiter, $string, $limit = null) {
	if ( $limit == null ) {
		return explode($delimiter, $string);
	} else {
		return explode($delimiter, $string, $limit);
	}
}

?>
