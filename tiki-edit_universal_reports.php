<?php
require_once('tiki-setup.php');
global $headerlib, $smarty;

$headerlib->add_jsfile( 'lib/core/UniversalReports/Builder.js' );

$headerlib->add_jq_onready("
	$('<div />')
		.appendTo('body')
		.reportBuilder({
			action: 'build',
			definition: ".json_encode(UniversalReports_Builder::load('Tracker')->definition)."
		});
");


$smarty->display("tiki.tpl");