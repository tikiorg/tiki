<html>
<head>
<link rel="StyleSheet" href="styles/{$style}" type="text/css" />
</head>

<body style = "margin:0px;" onload = "window.setInterval('location.reload()','10000');">
<table width = "100%" height = "100%">

{foreach from=$chatusers item=chatuser}
<tr><td valign = 'top' class = 'chatchannels'>{$chatuser.nickname|userlink}</td></tr>
{/foreach}

</table>
</body>
</html>
