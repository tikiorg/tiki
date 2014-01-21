{* $Id$ *}<!DOCTYPE html>
<html id="installer" lang="{if !empty($pageLang)}{$pageLang}{else}{$prefs.language}{/if}">
	<head>
{include file='header.tpl'}
	</head>
	<body class="tiki fixed_width">


{$mid_data}


{include file='footer.tpl'}
{if $headerlib}
	{$headerlib->output_js_files()}
	{$headerlib->output_js()}
{/if}
	</body>
</html>
