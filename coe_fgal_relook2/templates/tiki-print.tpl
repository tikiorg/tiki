{* $Id$ *}<!DOCTYPE html>
<html id="print" xmlns="http://www.w3.org/1999/xhtml" xml:lang="{if !empty($pageLang)}{$pageLang}{else}{$prefs.language}{/if}" lang="{if !empty($pageLang)}{$pageLang}{else}{$prefs.language}{/if}">
	<head>
{include file='header.tpl'}
	</head>
	<body{html_body_attributes}>

		<div id="tiki-clean">
			<div id="tiki-mid">
{$mid_data}
			</div>
		</div>

{include file='footer.tpl'}
<!-- Put JS at the end -->
{if $headerlib}
	{$headerlib->output_js_files()}
	{$headerlib->output_js()}
{/if}
	</body>
</html>
