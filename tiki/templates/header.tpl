<!DOCTYPE html 
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "DTD/xhtml1-transitional.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
    <link rel="StyleSheet"  href="styles/{$style}" type="text/css" />
    {include file="bidi.tpl"}
    <title>{$siteTitle}</title>
    {literal}
	<script type="text/javascript" src="lib/tiki-js.js">
	</script>
	{/literal}
	{if $uses_tabs eq 'y'}
		{literal}
			<link rel="stylesheet" href="lib/tabs/mozilla.css" type="text/css" />
			<script src="lib/tabs/utils.js" type="text/javascript"></script>
			<script src="lib/tabs/viewport.js" type="text/javascript"></script>
			<script src="lib/tabs/global.js" type="text/javascript"></script>
			<script src="lib/tabs/cookie.js" type="text/javascript"></script>
			<script src="lib/tabs/tabs.js" type="text/javascript"></script>
			<script type="text/javascript">
				// <![CDATA[
			
			
				TabParams = {
					useClone         : false,
					alwaysShowClone  : false,
					eventType        : "click",
					tabTagName       : "span"
					};
			

				// ]]>
			</script>

		{/literal}
	{/if}

{$trl}

  </head>

  <body {if $uses_tabs eq 'y'}onload="tabInit()"{/if} {if $user_dbl eq 'y' and $dblclickedit eq 'y' and $tiki_p_edit eq 'y'}ondblclick="location.href='tiki-editpage.php?page={$page}';"{/if}>  
{if $minical_reminders>100}
<iframe width='0' height='0' frameborder="0" src="tiki-minical_reminders.php"></iframe>
{/if}  
