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
  <body>
  	{$user}
	<table id='reqs' class="normal">
		<tr>
			<td class="heading">Id</td>
			<td class="heading">User</td>
			<td class="heading">Reason</td>
			<td class="heading">&nbsp;</td>
		</tr>
		{cycle values="odd,even" print=false}
		{section loop=$requests name=ix}
		<tr>
			<td class="{cycle advance=false}">{$requests[ix].reqId}</td>
			<td class="{cycle advance=false}">{$requests[ix].user}</td>
			<td class="{cycle advance=false}">{$requests[ix].reason}</td>
			<td class="{cycle}">
			<a class="link" {jspopup href="tiki-live_support_operator.php?reqId=$requests[ix].reqId" width="300" height="450"}>Accept</a>
			</td>
		</tr>
		{/section}
	</table>
    <script>
        /* Activate polling of requests */
        var last_req={$last};
    	console_poll();
    </script>

  </body>
</html>  