<h1><a class="pagetitle" href="tiki-replicate.php">{tr}Replicate{/tr}</a>

{if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=Replicate" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}system admin{/tr}">
<img border='0' src='img/icons/help.gif' alt="{tr}help{/tr}" /></a>{/if}

{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-replicate.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}system admin tpl{/tr}">
<img src="img/icons/info.gif" border="0" height="16" width="16" alt='{tr}edit tpl{/tr}' /></a>{/if}
</h1>

{if $tikifeedback}
<br />{section name=n loop=$tikifeedback}<div class="simplebox {if $tikifeedback[n].num > 0} highlight{/if}">{$tikifeedback[n].mes}</div>{/section}
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
<tr><td class="heading" colspan="2">{tr}Operations{/tr}</td></tr>
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
<tr><td class="heading" colspan="3">{tr}Operations Log{/tr}</td></tr>
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
