<a class="pagetitle" href="tiki-edit_templates.php?mode=listing">{tr}Edit templates{/tr}</a><br /><br />
{if $template}<h2>{tr}Template{/tr}: {$template}</h2>{/if}
{if $mode eq 'listing'}
<h3>{tr}Available templates{/tr}:</h3>
<table border="1" cellpadding="0" cellspacing="0" width="97%">
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
<input type="submit" name="save" value="{tr}save{/tr}" />
</div>
</form>
{/if}
