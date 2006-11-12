<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_jscalendar($params, &$smarty) {
	global $headerlib;

	$headerlib->add_cssfile('lib/jscalendar/calendar-system.css');
	$headerlib->add_jsfile('lib/jscalendar/calendar.js');
	global $language;
	if (is_file('lib/jscalendar/calendar-'.$language.'.js')) {
		$headerlib->add_jsfile('lib/jscalendar/calendar-'.$language.'.js');
	} else {
		$headerlib->add_jsfile('lib/jscalendar/calendar-en.js');
	}
	$headerlib->add_jsfile('lib/jscalendar/calendar-setup.js');


	if (isset($params['format'])) {
		$format = preg_replace('/"/','\"',$params['format']);
	} else {
		$format = "%A %e %B %Y, %H:%M";
	}

	if (isset($params['date'])) {
		$date = preg_replace('/[^0-9]/','',$params['date']);
	} else {
		$date = date('U');
	}
	$formatted_date = strftime($format,(int)$date);
	
	if (isset($params['id'])) {
		$id =  preg_replace('/"/','\"',$params['id']);
	} else {
		$id = $_GLOBALS['now'];
	}

	if (isset($params['fieldname'])) {
		$fieldname = preg_replace('/[^-_a-zA-Z0-9\[\]]/','',$params['fieldname']);
	} else {
		$fieldname = "ins_$id";
	}

	$back =<<<__END__
	<input type="hidden" name="$fieldname" value="$date" id="id_$id" />
	<span id="disp_$id" class="daterow">$formatted_date</span>
	<script type="text/javascript">
	Calendar.setup( {
		date        : "$formatted_date",
		inputField  : "id_$id",
		ifFormat    : "%s",
		displayArea : "disp_$id",
		daFormat    : "$format",
		showsTime   : true,
		singleClick : true,
		align       : "bR"
	} );
	</script>
__END__;

	echo $back;

}

?>
