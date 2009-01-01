{title help="Replicate"}{tr}Replicate{/tr}{/title}

{if $tikifeedback}
  <br />
  {section name=n loop=$tikifeedback}
    <div class="simplebox {if $tikifeedback[n].num > 0} highlight{/if}">{$tikifeedback[n].mes}</div>
  {/section}
{/if}

<div class="admin">
<table width="100%">
<tr><td width="50%" valign="top"><b>{tr}Slave{/tr}</b> {$smarty.server.SERVER_NAME|default:$smarty.server.HTTP_HOST}</td>
<td width="50%" valign="top">
<form action="tiki-replicate.php" method="post">
<b>{tr}Master{/tr}</b>
<input type="hidden" name="action" value="save" />
<input type="text" name="master" value="{$master}" />
<input type="submit" name="button" value="{tr}Change{/tr}" />
</form>
</td></tr>
<tr><td width="50%" valign="top">

<table class="normal">
<tr><th colspan="2">{tr}Operations{/tr}</th></tr>
{if $master}
<tr class="form"><td><b>check</b></td><td>
<form action="tiki-replicate.php" method="post">
<input type="hidden" name="action" value="check" />
{$master}
</form>
</td></tr>
{/if}
</table>

{cycle values="odd,even" print=false}
<table class="normal">
<tr><th colspan="3">{tr}Operations Log{/tr}</th></tr>
{section name=i loop=$log}
<tr class="{cycle}"><td>{$log[i].logtime|tiki_short_datetime}</td>
<td>{$log[i].loguser}</td>
<td>{$log[i].logmessage}</td></tr>
{/section}
</table>

</div>
</td>
<td width="50%" valign="top">
<iframe src="http://{$master}/tiki-replicate_console.php" name="{$title|escape}" height="100%" width="100%" align="center" frameborder="0" scrolling="auto" 
style="border:0"></iframe> 
</td></tr></table>
