<a class="pagetitle" href="tiki-user_watches.php">{tr}User Watches{/tr}</a>
{include file=tiki-mytiki_bar.tpl}
<br/><br/>
<h3>{tr}Watches{/tr}</h3>


<form action="tiki-user_watches.php" method="post" id='formi'>
{tr}Event{/tr}:<select name="event" onchange="javascript:document.getElementById('formi').submit();">
<option value"" {if $smarty.request.event eq ''}selected="selected"{/if}>{tr}All{/tr}</option>
{section name=ix loop=$events}
<option value="{$events[ix]|escape}" {if $events[ix] eq $smarty.request.event}selected="selected"{/if} />{$events[ix]}</option>
{/section}
</select>
</form>

<form action="tiki-user_watches.php" method="post">
<table class="normal">
<tr>
<td style="text-align:center;"  class="heading"><input type="submit" name="delete" value="{tr}Delete{/tr} " onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this watch?{/tr}')"/></td>
<td class="heading">{tr}Event{/tr}</td>
<td class="heading">{tr}Object{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$watches}
<tr>
<td style="text-align:center;"class="{cycle advance=false}">
<input type="checkbox" name="watch[{$watches[ix].hash}]" />
</td>
<td class="{cycle advance=false}">{$watches[ix].event}</td>
<td class="{cycle}"><a class="link" href="{$watches[ix].url}">{$watches[ix].type}:{$watches[ix].title}</a></td>
</tr>
{/section}
</table>
</form>
