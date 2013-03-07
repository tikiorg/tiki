<!DOCTYPE html>
<html id="nlsendresult" lang="{$prefs.language}">
	<head>
		{$headerlib->output_headers()}
	</head>
	<body class="{$sectionClass}fullscreen">
	<script type="text/javascript">{literal}
	<!--//--><![CDATA[//><!--
		var autoScrollTimer;
		function autoScroll() {
			window.scrollBy(0, 10000);
			autoScrollTimer = setTimeout('autoScroll();', 200);
		}
		autoScroll();
	//--><!]]>
	{/literal}</script>

	<div id="main">
		<div id="tiki-center">
