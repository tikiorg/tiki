<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

// Google menu item for e107
// This menu item will display a search box for Google
/*
//why a redirect ? ? ?

if(isset($_POST['sgoogle'])){

	echo "<script language='Javascript' type='text/javascript'>\n"
	. "<!--\n"
	. "  window.open ('http://www.google.com/search?hl=en&amp;ie=UTF-8&amp;oe=UTF-8&amp;q=".$_POST['sgoogle']."&amp;btnG=Google+Search','Google');\n"
	. "-->\n"
	. "</SCRIPT>\n";
	$smarty->assign('ownurl',$_SERVER["REQUEST_URI"]);
}
*/

?>
