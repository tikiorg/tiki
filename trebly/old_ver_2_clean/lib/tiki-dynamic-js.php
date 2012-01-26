<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

$list = array();
if(!empty($_SESSION['tiki_cookie_jar']) && is_array( $_SESSION['tiki_cookie_jar'] ) )
	foreach( $_SESSION['tiki_cookie_jar'] as $name=>$value )
		$list[] = $name . ": '" . addslashes($value) . "'";
?>
<script type="text/javascript">
var tiki_cookie_jar = new Array();
tiki_cookie_jar = {
	<?php echo implode( ",\n\t", $list ) ?>
};
</script>
