{jq}
{{if $mid != 'tiki-socialnetworks_firstlogin.tpl'}}
	$jq("body").colorbox({
	open: true,
	href: "tiki-socialnetworks_firstlogin.php",
	iframe: true,
	scrolling: false,
	width: 650,
	height: 600
	});
{{/if}}
{/jq}
{literal}
<style type="text/css">
#cboxClose{display:none !important;}
#cboxIframe{overflow:hidden;}
</style>
{/literal}