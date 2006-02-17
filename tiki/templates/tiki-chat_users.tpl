<html>
<head>
<link rel="StyleSheet" href="styles/{$style}" type="text/css" />
</head>

<body style = "margin:0px;" onload = "window.setInterval('location.reload()','10000');">
<table width = "100%" height = "100%">
{foreach from=$chatusers item=chatuser}
{assign var=displayName value=$chatuser.displayName}
{if $chatuser.nickname eq $user}
    {if $tiki_p_admin_chat eq 'y'}
      {assign var=displayName value="@"|cat:$displayName}
    {/if}
{/if}
<tr><td valign = 'top' class = 'chatchannels'>{$chatuser.nickname|userlink:"link":"not_set":$displayName}</td></tr>
{/foreach}

</table>
</body>
</html>
