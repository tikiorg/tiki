<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

check_ticket('admin-inc-metrics');
$pref_toggles = array(
	'metrics_pastresults'
	);

$pref_values = array(
	'metrics_trend_prefix',
	'metrics_trend_suffix',
	'metrics_trend_novalue'
	);

$pref_numeric = array(
	'metrics_pastresults_count',
	'metrics_metric_name_length',
	'metrics_tab_name_length'
	);
if (isset($_REQUEST['metricsprefs'])) {
	foreach ( $pref_toggles as $toggle) simple_set_toggle($toggle);
	foreach ( $pref_values as $value ) simple_set_value($value);
	foreach ( $pref_numeric as $value ) {
		if (!is_numeric($_REQUEST[$value]) || $_REQUEST[$value] < 1) {
			$smarty->assign('msg', tra("You must provide a positive numeric value for $value"));
			$smarty->display("error.tpl");
			die;
		}
		simple_set_value($value);
	}
}
ask_ticket('admin-inc-metrics');
?>
