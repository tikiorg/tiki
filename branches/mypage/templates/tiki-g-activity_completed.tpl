{popup_init src="lib/overlib.js"}
{*Smarty template*}
<h1>{tr}Activity completed{/tr}</h1>
{include file=tiki-g-user_bar.tpl}
<br /><br />
<table class="normal">
<tr>
	<td class="odd">{tr}Process{/tr}
	<td class="odd">{$procname} {$procversion}</td>
</tr>
<tr>
	<td class="odd">{tr}Activity{/tr}
	<td class="odd">{$actname}</td>
</tr>
<tr>
	<td></td>
</tr>

<form method="POST" action="tiki-g-run_activity.php">
<tr class="normal">
	<tr>
		<td class="odd" colspan="2">{tr}Comment{/tr}</td>
	</tr>

		<td class="odd" colspan="2">{tr}Subject{/tr}:<input type="text" name="__title" value="{if $post eq 'y'}{$title}{/if}" {if $post eq 'y'}readonly{/if}/></td>
	<tr>
		<td class="odd" colspan="2"><textarea rows="5" cols="60" name="__comment" {if $post eq 'y'}readonly{/if}>{if $post eq 'y'}{$comment}{/if}</textarea></td>
	</tr>
	{if $post eq 'n'}
	<tr>
		<td class="odd" colspan="2"><input type="submit" name="save" value="{tr}Save{/tr}" /></td>
	</tr>
	{/if}
</table>
<input type="hidden" name="iid" value="{$iid}" />
<input type="hidden" name="__post" value="y" />
<input type="hidden" name="activityId" value="{$actid}" />
</form>
