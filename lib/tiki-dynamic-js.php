<?php
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));
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
