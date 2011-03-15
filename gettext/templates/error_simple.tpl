<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{if !empty($pageLang)}{$pageLang}{else}{$prefs.language}{/if}" lang="{if !empty($pageLang)}{$pageLang}{else}{$prefs.language}{/if}">
	<head>
		{include file='header.tpl'}
	</head>
	<body{html_body_attributes}>
		{if $prefs.feature_bidi eq 'y'}
			<table dir="rtl" ><tr><td>
		{/if}
		<div id="tiki-mid">
			<div class="cbox">
				<div class="cbox-title">{icon _id=exclamation alt="{tr}Error{/tr}" style="vertical-align:middle"}{tr}Error{/tr}</div>
				<div class="cbox-data">
					{$msg}<br /><br />
					<a href="javascript:window.close()" class="linkmenu">{tr}Close Window{/tr}</a><br /><br />
				</div>
			</div>
		</div>
		{if $prefs.feature_bidi eq 'y'}
			</td></tr></table>
		{/if}
		{include file='footer.tpl'}
	</body>
</html>
