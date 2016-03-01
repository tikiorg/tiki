{jq}
{{if not $smarty.server.REQUEST_URI|stristr:'tiki-socialnetworks_firstlogin.php'}}
if ($("form > input[name=origin]:hidden").length === 0) {	// lightweight fix to avoid clash of user_conditions and fb 1st login
	setTimeout(function () {
		$("body").colorbox({
			open: true,
			href: "tiki-socialnetworks_firstlogin.php",
			iframe: true,
			scrolling: false,
			width: 650,
			height: 650,
			escKey: false,
			overlayClose: false
		});
	}, 1000);
}
{{/if}}
{/jq}
{literal}
<style type="text/css">
#cboxClose{display:none !important;}
#cboxIframe{overflow:hidden;}
</style>
{/literal}