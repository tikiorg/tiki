<!DOCTYPE html 
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "DTD/xhtml1-transitional.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
    <link rel="StyleSheet"  href="styles/{$style}" type="text/css" />
    {include file="bidi.tpl"}
    <title>Live support:Console</title>
    {literal}
	<script type="text/javascript" src="lib/live_support/live-support.js">
	
	</script>
	{/literal}
	{$trl}
  </head>
  {literal}
  <body>
  {/literal}
   	<input type="hidden" id="user" value="{$user}" />
  	<input type="hidden" id="status" value="online" />
	<table class="normal" width="100%">
		<tr>
			<td class="heading">{tr}Operator{/tr}: {$user}</td>
			<td class="heading">{tr}Status{/tr}: <b>{$status}</b></td>
			<td class="heading" style="text-align:right;">    
				{if $status eq 'offline'}
    				<a href="tiki-live_support_console.php?status=online" class="tableheading">{tr}be online{/tr}</a>
    			{else}
    				<a href="tiki-live_support_console.php?status=offline" class="tableheading">{tr}be offline{/tr}</a>
    		{/if}
		</td>
		</tr>
	</table>

    {if count($requests) > 0}
    <h3>{tr}Support requests{/tr}</h3>
    {if $new_requests eq 'y'}
		<script>
			var NSsound = navigator.plugins && navigator.plugins["LiveAudio"];
  			var IEsound = navigator.plugins && document.all;
  			var audioEnabled = NSsound || IEsound;
  			if (audioEnabled) {
				<EMBED SRC="aladdin.mid" HIDDEN="true" AUTOSTART="true"	STARTTIME="00:10" ENDTIME="00:15">
  			}
		</script>
    {/if}
	<table id='reqs' class="normal">
		<tr>
			<td class="heading">{tr}User{/tr}</td>
			<td class="heading">{tr}Reason{/tr}</td>
			<td class="heading">{tr}Requested{/tr}</td>
			<td class="heading">&nbsp;</td>
		</tr>
		{cycle values="odd,even" print=false}
		{section loop=$requests name=ix}
		<tr>
			<td class="{cycle advance=false}">{$requests[ix].user}</td>
			<td class="{cycle advance=false}">{$requests[ix].reason}</td>
			<td class="{cycle advance=false}">{$requests[ix].timestamp|tiki_short_time}</td>
			<td class="{cycle}">
		    {if $status eq 'online'}
				<a class="link" {jspopup href="tiki-live_support_chat_window.php?reqId=$requests[ix].reqId&role=operator" width="300" height="450"}>{tr}Accept{/tr}</a>
			{else}
				&nbsp;
			{/if}
			</td>
		</tr>
		{/section}
	</table>
	{/if}
    <script>
        var last_req={$last};
    	console_poll();
    </script>

  </body>
</html>  