<a class="pagetitle" href="tiki-edit_templates.php?mode=listing">{tr}Edit Templates{/tr}</a>

<!-- the help link info -->
  
      {if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=EditTemplates" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}EditTemplates{/tr}">
<img border='0' src='img/icons/help.gif' alt='help' /></a>{/if}

<!-- link to tpl -->

      {if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-edit_templates.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}EditTemplates tpl{/tr}">
<img border='0' src='img/icons/info.gif' alt='edit tpl' /></a>{/if}

<!-- begin -->



<a href="tiki-edit_custom_templates.php">{tr}Templates for Custom Style{/tr}</a>







<br /><br />
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
<input type="submit" name="save" value="{tr}Save{/tr}" />
</div>
</form>
{/if}
