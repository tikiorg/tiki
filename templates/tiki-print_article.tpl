{* $Id$ *}<!DOCTYPE html>
<html id="print" lang="{if !empty($pageLang)}{$pageLang}{else}{$prefs.language}{/if}">
	<head>
{include file='header.tpl'}
	</head>
	<body{html_body_attributes}>

		<div id="tiki-clean">
{include file='article.tpl'}
		</div>

{include file='footer.tpl'}
	</body>
</html>
