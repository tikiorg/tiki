<h1><a class="pagetitle" href="tiki-edit_templates.php?mode=listing">{tr}Edit templates{/tr}</a>

      {if $feature_help eq 'y'}
<a href="{$helpurl}EditTemplates" target="tikihelp" class="tikihelp" title="{tr}EditTemplates{/tr}">
<img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}help{/tr}'></a>{/if}

      {if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-edit_templates.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}EditTemplates tpl{/tr}">
<img src="img/icons/info.gif" border="0" height="16" width="16" alt='{tr}edit tpl{/tr}'></a>{/if}</h1>

{if $template}<h2>{tr}Template{/tr}: {$template}</h2>{/if}
{if $mode eq 'listing'}
<h3>{tr}Available templates{/tr}:</h3>
<table border="1" cellpadding="0" cellspacing="0" >
<tr>
<td class="heading">{tr}Template{/tr}</a></td>
</tr>
{section name=user loop=$files}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd"><a class="link" href="tiki-edit_templates.php?template={$files[user]}">{$files[user]}</a></td>
</tr>
{else}
<tr>
<td class="even"><a class="link" href="tiki-edit_templates.php?template={$files[user]}">{$files[user]}</a></td>
</tr>
{/if}
{sectionelse}
<tr><td colspan="2" class="odd">{tr}No records found{/tr}</td></tr>
{/section}
</table>
{/if}
{if $mode eq 'editing'}
<a class="link" href="tiki-edit_templates.php">{tr}Template listing{/tr}</a><br />
<form action="tiki-edit_templates.php" method="post">
<textarea name="data" rows="20" cols="80">{$data|escape}</textarea>
<div align="center">
<input type="hidden" name="template" value="{$template|escape}" />
{if $feature_edit_templates eq 'y'}
<input type="submit" name="save" value="{tr}save{/tr}" />
{/if}
</div>
</form>
{/if}
