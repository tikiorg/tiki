{popup_init src="lib/overlib.js"}
{*Smarty template*}
<h1>{tr}Activity completed{/tr}</h1>
{include file="tiki-g-user_bar.tpl"}
<br /><br />
<table>
<tr class="odd">
	<td>{tr}Process{/tr}</td>
	<td>{$procname} {$procversion}</td>
</tr>
<tr class="even">
	<td>{tr}Activity{/tr}</td>
	<td>{$actname}</td>
</tr>
</table>
